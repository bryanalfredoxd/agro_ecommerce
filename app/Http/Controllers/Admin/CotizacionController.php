<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use App\Models\CotizacionDetalle;
use App\Models\Producto;
use App\Models\User;
use App\Models\TasaCambio;
use App\Models\ConfiguracionTienda;
use App\Mail\CotizacionMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CotizacionController extends Controller
{
    /**
     * Listado general de cotizaciones
     */
    public function index(Request $request)
    {
        $query = Cotizacion::with(['vendedor', 'detalles.producto']);

        // 1. Buscador (por número, cliente, cédula o email)
        if ($request->filled('buscar')) {
            $busqueda = trim($request->buscar);
            $query->where(function ($q) use ($busqueda) {
                $q->where('numero_cotizacion', 'LIKE', "%{$busqueda}%")
                  ->orWhere('cliente_nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('cliente_documento', 'LIKE', "%{$busqueda}%")
                  ->orWhere('cliente_email', 'LIKE', "%{$busqueda}%");
            });
        }

        // 2. Filtro por estado
        if ($request->filled('filtro_estado') && $request->filtro_estado !== 'todas') {
            $query->where('estado', $request->filtro_estado);
        }

        // Métricas / KPIs rápidos
        $totalCotizaciones = Cotizacion::count();
        $pendientesCount = Cotizacion::where('estado', 'pendiente')->count();
        $aprobadasCount = Cotizacion::where('estado', 'aprobada')->count();
        $montoTotalUsd = Cotizacion::sum('total_usd');

        $cotizaciones = $query->orderBy('fecha_emision', 'desc')->paginate(12);

        return view('admin.cotizaciones.index', compact(
            'cotizaciones',
            'totalCotizaciones',
            'pendientesCount',
            'aprobadasCount',
            'montoTotalUsd'
        ));
    }

    /**
     * Formulario de creación interactivo
     */
    public function create()
    {
        $tasaRecord = TasaCambio::latest('creado_at')->first();
        $valorTasaBcv = $tasaRecord ? (float)$tasaRecord->valor_tasa : 1.0000;
        $tasaId = $tasaRecord ? $tasaRecord->id : null;

        $config = ConfiguracionTienda::first();
        $ivaPorcentajeDefecto = $config ? (float)$config->iva_porcentaje : 16.00;

        $siguienteNumero = Cotizacion::generarSiguienteNumero();

        $productosIniciales = Producto::with('categoria')
                                      ->whereNull('eliminado_at')
                                      ->limit(40)
                                      ->get();

        return view('admin.cotizaciones.create', compact(
            'valorTasaBcv',
            'tasaId',
            'config',
            'ivaPorcentajeDefecto',
            'siguienteNumero',
            'productosIniciales'
        ));
    }

    /**
     * AJAX: Búsqueda de productos
     */
    public function buscarProducto(Request $request)
    {
        $busqueda = trim($request->input('buscar', ''));

        $query = Producto::with('categoria')->whereNull('eliminado_at');

        if (!empty($busqueda)) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('sku', 'LIKE', "%{$busqueda}%")
                  ->orWhere('codigo_barras', 'LIKE', "%{$busqueda}%");
            });
        }

        $productos = $query->limit(30)->get();

        return response()->json(['productos' => $productos]);
    }

    /**
     * AJAX: Búsqueda y autocompletado de clientes
     */
    public function buscarCliente(Request $request)
    {
        $busqueda = trim($request->input('buscar', ''));
        if (strlen($busqueda) < 2) {
            return response()->json(['clientes' => []]);
        }

        $usuarios = User::where(function ($q) use ($busqueda) {
            $q->where('documento_identidad', 'LIKE', "%{$busqueda}%")
              ->orWhere('nombre', 'LIKE', "%{$busqueda}%")
              ->orWhere('apellido', 'LIKE', "%{$busqueda}%")
              ->orWhere('email', 'LIKE', "%{$busqueda}%")
              ->orWhere('telefono', 'LIKE', "%{$busqueda}%");
        })->limit(6)->get()->map(function ($u) {
            return [
                'id' => $u->id,
                'documento' => $u->documento_identidad ?: '',
                'nombre' => trim($u->nombre . ' ' . ($u->apellido ?? '')),
                'email' => $u->email ?: '',
                'telefono' => $u->telefono ?: '',
            ];
        });

        return response()->json(['clientes' => $usuarios]);
    }

    /**
     * Guardar cotización en base de datos
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente_nombre' => 'required|string|max:200',
            'cliente_documento' => 'nullable|string|max:30',
            'cliente_telefono' => 'nullable|string|max:50',
            'cliente_email' => 'nullable|email|max:150',
            'cliente_direccion' => 'nullable|string|max:500',
            'validez_dias' => 'required|integer|min:1|max:90',
            'observaciones' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.descripcion' => 'required|string|max:255',
            'items.*.cantidad' => 'required|numeric|min:0.01',
            'items.*.precio_unitario_usd' => 'required|numeric|min:0',
        ], [
            'cliente_nombre.required' => 'Debe ingresar el nombre del cliente o empresa.',
            'items.required' => 'Debe agregar al menos un ítem a la cotización.',
            'items.min' => 'Debe agregar al menos un ítem a la cotización.',
        ]);

        $tasaRecord = TasaCambio::latest('creado_at')->first();
        $valorTasaBcv = $tasaRecord ? (float)$tasaRecord->valor_tasa : 1.0000;
        $tasaId = $tasaRecord ? $tasaRecord->id : null;

        $cotizacion = DB::transaction(function () use ($request, $valorTasaBcv, $tasaId) {
            $numeroCotizacion = Cotizacion::generarSiguienteNumero();
            $validezDias = (int)$request->validez_dias;
            $fechaEmision = Carbon::now();
            $fechaVencimiento = Carbon::now()->addDays($validezDias)->endOfDay();

            // Calcular totales
            $subtotalUsd = 0;
            $detallesData = [];

            foreach ($request->items as $item) {
                $cant = (float)$item['cantidad'];
                $precio = (float)$item['precio_unitario_usd'];
                $totalLinea = round($cant * $precio, 2);
                $subtotalUsd += $totalLinea;

                $detallesData[] = [
                    'producto_id' => !empty($item['producto_id']) ? (int)$item['producto_id'] : null,
                    'descripcion' => trim($item['descripcion']),
                    'cantidad' => $cant,
                    'unidad_medida' => $item['unidad_medida'] ?? 'ud',
                    'precio_unitario_usd' => $precio,
                    'total_usd' => $totalLinea,
                    'creado_at' => now(),
                ];
            }

            // Manejo de IVA
            $aplicaIva = $request->boolean('aplica_iva');
            $ivaPorcentaje = $aplicaIva ? (float)($request->iva_porcentaje ?: 16.00) : 0.00;
            $ivaUsd = $aplicaIva ? round($subtotalUsd * ($ivaPorcentaje / 100), 2) : 0.00;

            $totalUsd = round($subtotalUsd + $ivaUsd, 2);
            $totalVes = round($totalUsd * $valorTasaBcv, 2);

            // Crear Cotización
            $cot = Cotizacion::create([
                'numero_cotizacion' => $numeroCotizacion,
                'vendedor_id' => Auth::id(),
                'cliente_id' => !empty($request->cliente_id) ? (int)$request->cliente_id : null,
                'cliente_nombre' => trim($request->cliente_nombre),
                'cliente_documento' => trim($request->cliente_documento),
                'cliente_telefono' => trim($request->cliente_telefono),
                'cliente_email' => trim($request->cliente_email),
                'cliente_direccion' => trim($request->cliente_direccion),
                'tasa_cambio_id' => $tasaId,
                'valor_tasa_bcv' => $valorTasaBcv,
                'subtotal_usd' => $subtotalUsd,
                'iva_porcentaje' => $ivaPorcentaje,
                'iva_usd' => $ivaUsd,
                'total_usd' => $totalUsd,
                'total_ves' => $totalVes,
                'validez_dias' => $validezDias,
                'fecha_emision' => $fechaEmision,
                'fecha_vencimiento' => $fechaVencimiento,
                'estado' => 'pendiente',
                'observaciones' => $request->observaciones,
            ]);

            // Guardar detalles
            foreach ($detallesData as &$d) {
                $d['cotizacion_id'] = $cot->id;
            }
            CotizacionDetalle::insert($detallesData);

            return $cot;
        });

        // Envío opcional inmediato por correo
        if ($request->boolean('enviar_correo') && !empty($cotizacion->cliente_email)) {
            try {
                Mail::to($cotizacion->cliente_email)->send(new CotizacionMail($cotizacion));
                return redirect()->route('admin.cotizaciones.show', $cotizacion->id)
                                 ->with('success', "Cotización {$cotizacion->numero_cotizacion} creada y enviada exitosamente a {$cotizacion->cliente_email}.");
            } catch (\Throwable $e) {
                return redirect()->route('admin.cotizaciones.show', $cotizacion->id)
                                 ->with('warning', "Cotización guardada exitosamente, pero hubo un detalle al enviar el correo: " . $e->getMessage());
            }
        }

        return redirect()->route('admin.cotizaciones.show', $cotizacion->id)
                         ->with('success', "Cotización {$cotizacion->numero_cotizacion} registrada exitosamente.");
    }

    /**
     * Vista de detalle en panel administrativo
     */
    public function show($id)
    {
        $cotizacion = Cotizacion::with(['vendedor', 'cliente', 'detalles.producto.categoria'])->findOrFail($id);
        $config = ConfiguracionTienda::first();

        return view('admin.cotizaciones.show', compact('cotizacion', 'config'));
    }

    /**
     * Impresión en formato A4 (Navegador con window.print)
     */
    public function imprimir($id)
    {
        $cotizacion = Cotizacion::with(['vendedor', 'cliente', 'detalles.producto.categoria'])->findOrFail($id);
        $config = ConfiguracionTienda::first();

        return view('cotizaciones.imprimir', compact('cotizacion', 'config'));
    }

    /**
     * Descarga directa en archivo PDF (DomPDF)
     */
    public function descargarPdf($id)
    {
        $cotizacion = Cotizacion::with(['vendedor', 'cliente', 'detalles.producto.categoria'])->findOrFail($id);
        $config = ConfiguracionTienda::first();

        $pdf = Pdf::loadView('cotizaciones.imprimir_pdf', compact('cotizacion', 'config'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download("Cotizacion-{$cotizacion->numero_cotizacion}.pdf");
    }

    /**
     * Enviar cotización por correo electrónico con PDF adjunto
     */
    public function enviarCorreo(Request $request, $id)
    {
        $cotizacion = Cotizacion::with(['vendedor', 'cliente', 'detalles.producto.categoria'])->findOrFail($id);

        $destinatario = trim($request->input('email', $cotizacion->cliente_email));
        if (empty($destinatario) || !filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
            return back()->with('error', 'El cliente no tiene un correo electrónico válido registrado.');
        }

        try {
            $config = ConfiguracionTienda::first();
            Mail::to($destinatario)->send(new CotizacionMail($cotizacion, $config));

            // Si el correo ingresado era diferente, actualizamos el registro
            if ($cotizacion->cliente_email !== $destinatario) {
                $cotizacion->cliente_email = $destinatario;
                $cotizacion->save();
            }

            return back()->with('success', "¡Cotización #{$cotizacion->numero_cotizacion} enviada exitosamente a {$destinatario} con el archivo PDF adjunto!");
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo enviar el correo: ' . $e->getMessage());
        }
    }

    /**
     * Cambio de estado (Aprobada, Rechazada, Pendiente)
     */
    public function cambiarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,aprobada,rechazada,vencida'
        ]);

        $cotizacion = Cotizacion::findOrFail($id);
        $cotizacion->estado = $request->estado;
        $cotizacion->save();

        return back()->with('success', "Estado de la cotización actualizado a " . ucfirst($request->estado) . ".");
    }

    /**
     * Eliminar cotización
     */
    public function destroy($id)
    {
        $cotizacion = Cotizacion::findOrFail($id);
        $numero = $cotizacion->numero_cotizacion;
        $cotizacion->delete();

        return redirect()->route('admin.cotizaciones.index')
                         ->with('success', "Cotización {$numero} eliminada correctamente.");
    }
}
