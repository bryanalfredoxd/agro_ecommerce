<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = User::where('rol_id', 3) // Rol de cliente
            ->withCount(['pedidos' => function ($query) {
                // Ajustado a los estados reales de tu BD
                $query->whereIn('estado', ['entregado', 'completado_caja']);
            }])
            ->with(['pedidos' => function ($query) {
                $query->latest()->take(1);
            }])
            // CORRECCIÓN: cambiado created_at por creado_at
            ->orderBy('creado_at', 'desc') 
            ->paginate(15);

        $estadisticas = [
            'total' => User::where('rol_id', 3)->count(),
            'activos' => User::where('rol_id', 3)->where('activo', true)->count(),
            'inactivos' => User::where('rol_id', 3)->where('activo', false)->count(),
            'con_pedidos' => User::where('rol_id', 3)->whereHas('pedidos')->count(),
        ];

        return view('admin.clientes.index', compact('clientes', 'estadisticas'));
    }

    public function show($id)
    {
        $cliente = User::with(['pedidos' => function ($query) {
            $query->with('detalles') // Asegúrate de que la relación se llama 'detalles' en el modelo Pedido
                  // CORRECCIÓN: cambiado created_at por creado_at
                  ->orderBy('creado_at', 'desc')
                  ->take(10);
        }, 'direcciones'])
        ->findOrFail($id);

        // Estadísticas del cliente (Ajustado a los estados reales)
        $estadisticas = [
            'total_pedidos' => $cliente->pedidos->count(),
            'pedidos_completados' => $cliente->pedidos->whereIn('estado', ['entregado', 'completado_caja'])->count(),
            'total_gastado' => $cliente->pedidos->whereIn('estado', ['entregado', 'completado_caja'])->sum('total_usd'),
            'ultimo_pedido' => $cliente->pedidos->first(),
        ];

        return view('admin.clientes.show', compact('cliente', 'estadisticas'));
    }

    public function create()
    {
        return view('admin.clientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email',
            'telefono' => 'nullable|string|max:20',
            'documento_identidad' => 'nullable|string|max:20|unique:usuarios,documento_identidad',
            'tipo_cliente' => 'required|in:natural,juridico,finca_productor',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'rol_id' => 3, // Cliente
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'documento_identidad' => $request->documento_identidad,
            'tipo_cliente' => $request->tipo_cliente,
            'password_hash' => Hash::make($request->password),
            'activo' => true,
        ]);

        return redirect()->route('admin.clientes.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    public function edit($id)
    {
        $cliente = User::findOrFail($id);
        return view('admin.clientes.edit', compact('cliente'));
    }

    public function update(Request $request, $id)
    {
        $cliente = User::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email,' . $cliente->id,
            'telefono' => 'nullable|string|max:20',
            'documento_identidad' => 'nullable|string|max:20|unique:usuarios,documento_identidad,' . $cliente->id,
            'tipo_cliente' => 'required|in:natural,juridico,finca_productor',
            'activo' => 'boolean',
        ]);

        $cliente->update([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'documento_identidad' => $request->documento_identidad,
            'tipo_cliente' => $request->tipo_cliente,
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('admin.clientes.show', $cliente->id)
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $cliente = User::findOrFail($id);
        $cliente->update([
            'password_hash' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.clientes.show', $cliente->id)
            ->with('success', 'Contraseña actualizada correctamente.');
    }

    public function toggleStatus($id)
    {
        $cliente = User::findOrFail($id);
        $cliente->update(['activo' => !$cliente->activo]);

        $mensaje = $cliente->activo ? 'Cliente activado correctamente.' : 'Cliente desactivado correctamente.';

        return redirect()->back()->with('success', $mensaje);
    }

    public function destroy($id)
    {
        $cliente = User::findOrFail($id);

        // Verificar si tiene pedidos
        if ($cliente->pedidos()->count() > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar un cliente que tiene pedidos asociados.');
        }

        $cliente->delete();

        return redirect()->route('admin.clientes.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}