@php
    $pago = $pedido->pago;
    $tienePagoRevision = $pago && $pago->estado === 'revision';
    $pagoAprobado = $pago && $pago->estado === 'aprobado';
    $pagoRechazado = $pago && $pago->estado === 'rechazado';
@endphp

<div class="space-y-6 p-6">

    {{-- CABECERA RESUMEN DEL PEDIDO --}}
    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xl font-black text-slate-900">Pedido #{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}</span>
                @if($pedido->canal_venta === 'web')
                    <span class="inline-flex items-center gap-1 text-[10px] font-black text-blue-700 bg-blue-100 px-2 py-0.5 rounded-md uppercase">
                        <span class="material-symbols-outlined text-[13px]">language</span> Web
                    </span>
                @elseif($pedido->canal_venta === 'whatsapp')
                    <span class="inline-flex items-center gap-1 text-[10px] font-black text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md uppercase">
                        <span class="material-symbols-outlined text-[13px]">chat</span> WhatsApp
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 text-[10px] font-black text-slate-700 bg-slate-200 px-2 py-0.5 rounded-md uppercase">
                        <span class="material-symbols-outlined text-[13px]">storefront</span> Caja POS
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-1 font-medium">Registrado el {{ \Carbon\Carbon::parse($pedido->creado_at)->format('d \d\e F, Y \a \l\a\s h:i A') }}</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            {{-- Badge Estado Pedido --}}
            @php
                $clasesEstado = match($pedido->estado) {
                    'pendiente' => 'bg-amber-100 text-amber-800 border-amber-300',
                    'pagado' => 'bg-blue-100 text-blue-800 border-blue-300',
                    'preparacion' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
                    'en_ruta' => 'bg-orange-100 text-orange-800 border-orange-300',
                    'entregado', 'completado_caja' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                    'devuelto', 'cancelado' => 'bg-rose-100 text-rose-800 border-rose-300',
                    default => 'bg-gray-100 text-gray-800 border-gray-300',
                };
            @endphp
            <span class="px-3 py-1 rounded-xl text-xs font-black uppercase tracking-wider border {{ $clasesEstado }}">
                {{ str_replace('_', ' ', $pedido->estado) }}
            </span>
        </div>
    </div>

    {{-- GRID DE DOS COLUMNAS: CLIENTE / ENTREGA Y AUDITORÍA DE PAGO --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        
        {{-- BLOQUE 1: DATOS DEL CLIENTE Y DESPACHO --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 p-5 shadow-2xs space-y-4">
            <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider flex items-center gap-2 pb-2 border-b border-gray-100">
                <span class="material-symbols-outlined text-blue-600 text-[18px]">person</span>
                Cliente & Entrega
            </h4>

            @if($pedido->usuario)
                <div class="space-y-2">
                    <div>
                        <span class="text-[11px] text-gray-400 font-bold uppercase block">Nombre Completo</span>
                        <p class="font-black text-gray-900 text-sm capitalize">{{ $pedido->usuario->nombre }} {{ $pedido->usuario->apellido }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-[11px] text-gray-400 font-bold uppercase block">Cédula / RIF</span>
                            <span class="font-bold text-gray-800">{{ $pedido->usuario->documento_identidad ?? 'No registrada' }}</span>
                        </div>
                        <div>
                            <span class="text-[11px] text-gray-400 font-bold uppercase block">Teléfono</span>
                            @if($pedido->usuario->telefono)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pedido->usuario->telefono) }}" target="_blank" class="font-black text-emerald-600 hover:underline inline-flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">chat</span>
                                    {{ $pedido->usuario->telefono }}
                                </a>
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <span class="text-[11px] text-gray-400 font-bold uppercase block">Correo Electrónico</span>
                        <span class="text-xs text-gray-700 font-medium break-all">{{ $pedido->usuario->email }}</span>
                    </div>
                </div>
            @else
                <p class="text-xs text-gray-500 italic">Venta rápida en mostrador / Cliente genérico POS</p>
            @endif

            {{-- Datos de Entrega --}}
            <div class="pt-3 border-t border-gray-100 space-y-2">
                <div>
                    <span class="text-[11px] text-gray-400 font-bold uppercase block">Modalidad de Entrega</span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold {{ $pedido->es_delivery ? 'text-blue-800' : 'text-emerald-800' }}">
                        <span class="material-symbols-outlined text-[16px] {{ $pedido->es_delivery ? 'text-blue-600' : 'text-emerald-600' }}">{{ $pedido->es_delivery ? 'local_shipping' : 'storefront' }}</span>
                        {{ $pedido->es_delivery ? 'Envío a Domicilio (Delivery)' : 'Retiro en Sede Física' }}
                    </span>
                </div>

                @if($pedido->es_delivery)
                    <div>
                        <span class="text-[11px] text-gray-400 font-bold uppercase block">Dirección de Destino</span>
                        <p class="text-xs text-gray-800 bg-gray-50 p-2.5 rounded-xl border border-gray-100 font-medium leading-relaxed">{{ $pedido->direccion_texto }}</p>
                        @if((float)$pedido->costo_delivery_usd > 0)
                            <span class="inline-block mt-1 text-[11px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                                Tarifa de Flete: ${{ number_format($pedido->costo_delivery_usd, 2) }}
                            </span>
                        @endif
                    </div>
                @else
                    <div>
                        <span class="text-[11px] text-gray-400 font-bold uppercase block">Punto de Entrega</span>
                        <div class="bg-emerald-50/60 p-2.5 rounded-xl border border-emerald-200/80 text-xs text-emerald-900 font-medium flex items-start gap-2">
                            <span class="material-symbols-outlined text-[16px] text-emerald-600 shrink-0 mt-0.5">storefront</span>
                            <div>
                                <strong class="block font-black text-emerald-950">Sede Principal - Agrosuministros Rodríguez</strong>
                                <span class="text-[11px] text-emerald-800">El cliente retirará su pedido personalmente en mostrador una vez preparado.</span>
                            </div>
                        </div>
                    </div>
                @endif

                @if($pedido->instrucciones_entrega)
                    <div>
                        <span class="text-[11px] text-amber-600 font-bold uppercase block">Instrucciones Especiales</span>
                        <p class="text-xs text-amber-800 bg-amber-50/80 p-2.5 rounded-xl border border-amber-200/60 font-medium">{{ $pedido->instrucciones_entrega }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- BLOQUE 2: AUDITORÍA Y VERIFICACIÓN DEL PAGO --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 p-5 shadow-2xs space-y-4 flex flex-col justify-between">
            <div>
                <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider flex items-center gap-2 pb-2 border-b border-gray-100">
                    <span class="material-symbols-outlined text-emerald-600 text-[18px]">account_balance_wallet</span>
                    Auditoría del Pago
                </h4>

                @if($pago)
                    <div class="mt-3 space-y-3">
                        
                        {{-- Estado y Método --}}
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <span class="font-black text-xs uppercase px-2.5 py-1 rounded-lg border bg-gray-50 border-gray-200 text-gray-800 inline-flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[15px] text-blue-600">payments</span>
                                {{ $pago->nombre_metodo }}
                            </span>

                            @if($tienePagoRevision)
                                @if($pago->esPresencial())
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-black {{ $pedido->es_delivery ? 'bg-teal-100 text-teal-800 border border-teal-300' : 'bg-indigo-100 text-indigo-800 border border-indigo-300' }} inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[15px]">{{ $pedido->es_delivery ? 'local_shipping' : 'store' }}</span>
                                        {{ $pedido->es_delivery ? 'Cobro Contra Entrega (Efectivo)' : 'Cobro en Mostrador' }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-amber-100 text-amber-800 border border-amber-300 inline-flex items-center gap-1 animate-pulse">
                                        <span class="material-symbols-outlined text-[15px]">pending</span>
                                        Por Verificar (Digital)
                                    </span>
                                @endif
                            @elseif($pagoAprobado)
                                <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300 inline-flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[15px]">verified</span>
                                    Aprobado / Pagado
                                </span>
                            @elseif($pagoRechazado)
                                <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-rose-100 text-rose-800 border border-rose-300 inline-flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[15px]">cancel</span>
                                    Rechazado
                                </span>
                            @endif
                        </div>

                        {{-- Alerta informativa para Pago Presencial --}}
                        @if($pago->esPresencial())
                            <div class="p-3 bg-amber-50/70 rounded-xl border border-amber-200/80">
                                <div class="flex items-start gap-2 text-xs text-amber-900">
                                    <span class="material-symbols-outlined text-amber-600 text-sm mt-0.5">info</span>
                                    <div>
                                        <p class="font-bold">Modalidad: Pago Presencial ({{ $pago->nombre_metodo }})</p>
                                        <p class="text-[11px] text-amber-800 mt-0.5">
                                            @if($pedido->es_delivery)
                                                El cliente cancelará en efectivo al momento de recibir su pedido con el repartidor.
                                            @else
                                                El cliente apartó la mercancía vía web y realizará el pago directamente en caja/mostrador (efectivo o punto de venta/Biopago) al retirar sus insumos.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Referencia y Montos --}}
                        <div class="bg-gray-50/80 p-3 rounded-xl border border-gray-100 space-y-1.5 text-xs">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 font-bold">N° Referencia:</span>
                                <span class="font-mono font-black text-gray-900 bg-white px-2 py-0.5 rounded border border-gray-200 select-all">
                                    {{ $pago->referencia_bancaria ?? ($pago->esPresencial() ? 'PAGO-EN-SITIO' : 'NO REPORTADA') }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 font-bold">Monto del Pedido:</span>
                                <div class="text-right">
                                    <span class="font-black text-gray-900">${{ number_format($pago->monto_usd ?? $pedido->total_usd, 2) }}</span>
                                    <span class="text-gray-400 text-[10px] block">Bs. {{ number_format($pago->monto_ves ?? $pedido->total_ves_calculado, 2) }}</span>
                                </div>
                            </div>

                            <div class="flex justify-between items-center text-[11px] text-gray-400">
                                <span>Fecha del Registro:</span>
                                <span>{{ $pago->fecha_pago ? \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y, h:i A') : 'N/A' }}</span>
                            </div>

                            @if($pago->verificador)
                                <div class="flex justify-between items-center text-[11px] pt-1 border-t border-gray-200/60">
                                    <span class="text-gray-500 font-bold">Auditado por:</span>
                                    <span class="text-emerald-700 font-bold">{{ $pago->verificador->nombre }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Comprobante Imagen --}}
                        @if($pago->captura_pago_url)
                            <div class="pt-2">
                                <span class="text-[11px] text-gray-400 font-bold uppercase block mb-1">Comprobante Adjunto</span>
                                <div class="flex items-center gap-3 bg-gray-50 p-2 rounded-xl border border-gray-200">
                                    <img src="{{ $pago->url_comprobante }}" alt="Comprobante" class="size-14 object-cover rounded-lg border border-gray-200 cursor-pointer hover:opacity-90 shadow-2xs" onerror="this.onerror=null; this.src='data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2256%22%20height%3D%2256%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%2394a3b8%22%20stroke-width%3D%221.5%22%3E%3Crect%20x%3D%223%22%20y%3D%223%22%20width%3D%2218%22%20height%3D%2218%22%20rx%3D%222%22%2F%3E%3Ccircle%20cx%3D%228.5%22%20cy%3D%228.5%22%20r%3D%221.5%22%2F%3E%3Cpolyline%20points%3D%2221%2015%2016%2010%205%2021%22%2F%3E%3C%2Fsvg%3E';" onclick="verComprobante('{{ $pago->url_comprobante }}', '{{ $pago->referencia_bancaria ?? 'SIN-REF' }}', {{ $pago->id }}, '{{ $pago->estado }}', {{ $pedido->id }})">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-gray-800">Captura de Transferencia</p>
                                        <button type="button" onclick="verComprobante('{{ $pago->url_comprobante }}', '{{ $pago->referencia_bancaria ?? 'SIN-REF' }}', {{ $pago->id }}, '{{ $pago->estado }}', {{ $pedido->id }})" class="text-blue-600 hover:text-blue-800 text-[11px] font-bold inline-flex items-center gap-1 mt-0.5">
                                            <span class="material-symbols-outlined text-[14px]">zoom_in</span> Ampliar en Pantalla Completa
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                @else
                    <div class="py-6 text-center text-gray-400">
                        <span class="material-symbols-outlined text-4xl text-gray-300">hourglass_empty</span>
                        <p class="text-xs font-bold mt-1">El cliente aún no ha reportado comprobante de pago para este pedido.</p>
                    </div>
                @endif
            </div>

            {{-- BOTONES DE RESOLUCIÓN DEL PAGO (SI ESTÁ EN REVISIÓN) --}}
            @if($tienePagoRevision)
                <div class="pt-3 border-t border-amber-200 bg-amber-50/50 -mx-5 -mb-5 p-4 rounded-b-2xl">
                    <span class="text-[10px] font-black text-amber-800 uppercase tracking-widest block mb-2 text-center">
                        {{ $pago->esPresencial() ? 'Confirmación de Cobro en Tienda / Entrega' : 'Acción de Tesorería Requerida' }}
                    </span>
                    <div class="flex gap-2">
                        <button type="button" onclick="aprobarPagoDirecto({{ $pago->id }}, '{{ $pago->referencia_bancaria ?? 'EN-SITIO' }}', {{ $pedido->id }}, {{ $pago->esPresencial() ? 'true' : 'false' }})" class="flex-1 py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs flex items-center justify-center gap-1.5 transition-all shadow-sm">
                            <span class="material-symbols-outlined text-[17px]">{{ $pago->esPresencial() ? 'payments' : 'check_circle' }}</span>
                            {{ $pago->esPresencial() ? 'Confirmar Cobro y Entrega' : 'Aprobar Pago' }}
                        </button>
                        <button type="button" onclick="rechazarPagoDirecto({{ $pago->id }}, '{{ $pago->referencia_bancaria ?? 'SIN-REF' }}', {{ $pedido->id }})" class="py-2.5 px-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs flex items-center justify-center gap-1.5 transition-all shadow-sm">
                            <span class="material-symbols-outlined text-[17px]">cancel</span>
                            Rechazar
                        </button>
                    </div>
                </div>
            @endif

        </div>

    </div>

    {{-- LISTA DE ARTÍCULOS A EMPACAR --}}
    <div class="bg-white rounded-2xl border border-gray-200/90 p-5 shadow-2xs space-y-3">
        <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider flex items-center gap-2 pb-2 border-b border-gray-100">
            <span class="material-symbols-outlined text-blue-600 text-[18px]">inventory_2</span>
            Artículos y Productos del Pedido ({{ $pedido->detalles->count() }})
        </h4>

        <div class="space-y-2.5">
            @foreach($pedido->detalles as $detalle)
                <div class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 bg-gray-50/40 hover:bg-gray-50 transition-colors">
                    
                    {{-- Imagen miniatura --}}
                    <div class="size-12 rounded-lg bg-white border border-gray-200 flex items-center justify-center overflow-hidden shrink-0">
                        @if($detalle->producto && $detalle->producto->imagen_url)
                            <img src="{{ asset($detalle->producto->imagen_url) }}" class="size-full object-cover">
                        @else
                            <span class="material-symbols-outlined text-gray-300">image</span>
                        @endif
                    </div>
                    
                    {{-- Información del Producto --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-xs sm:text-sm font-bold text-gray-900 truncate">{{ $detalle->producto->nombre ?? 'Producto Eliminado' }}</p>
                        <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                            @if($detalle->producto && $detalle->producto->sku)
                                <span class="text-[10px] text-gray-500 font-mono">SKU: {{ $detalle->producto->sku }}</span>
                            @endif
                            <span class="text-[10px] bg-gray-200/80 text-gray-600 px-1.5 py-0.2 rounded font-semibold uppercase">
                                {{ $detalle->producto->marca->nombre ?? 'GENÉRICO' }}
                            </span>
                            <span class="text-[11px] text-gray-500">
                                ${{ number_format($detalle->precio_unitario_usd ?? 0, 2) }} / {{ $detalle->producto->unidad_medida ?? 'UND' }}
                            </span>
                        </div>
                    </div>
                    
                    {{-- Cantidad Solicitada --}}
                    <div class="text-right pl-2">
                        <span class="text-[10px] text-gray-400 font-bold uppercase block">Cantidad</span>
                        <p class="text-base sm:text-lg font-black text-blue-700 leading-tight">
                            {{ rtrim(rtrim(number_format($detalle->cantidad_solicitada, 3), '0'), '.') }} 
                            <span class="text-[10px] font-bold text-blue-400 uppercase">{{ $detalle->producto->unidad_medida ?? 'UND' }}</span>
                        </p>
                        <span class="text-xs font-bold text-gray-600 block">
                            ${{ number_format(($detalle->cantidad_solicitada * ($detalle->precio_unitario_usd ?? 0)), 2) }}
                        </span>
                    </div>

                </div>
            @endforeach
        </div>
    </div>

    {{-- DESGLOSE FINANCIERO Y CONTROL LOGÍSTICO --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        
        {{-- CAMBIO RÁPIDO DE ESTADO LOGÍSTICO (SI ESTÁ PAGADO) --}}
        <div class="bg-gray-50 rounded-2xl border border-gray-200/80 p-5 space-y-3 flex flex-col justify-between">
            <div>
                <span class="text-xs font-black text-slate-400 uppercase tracking-wider block mb-1">Control de Despacho Operativo</span>
                <p class="text-xs text-gray-600">Cambia la etapa del pedido según avance la preparación física.</p>
            </div>

            @if($pedido->estado === 'pendiente')
                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 font-semibold flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">lock</span>
                    El despacho está bloqueado hasta que se apruebe el pago.
                </div>
            @elseif(in_array($pedido->estado, ['entregado', 'completado_caja', 'devuelto', 'cancelado']))
                <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    Este pedido se encuentra en estado final: {{ strtoupper($pedido->estado) }}.
                </div>
            @elseif(Auth::user()->tienePermiso('actualizar_estado_pedido'))
                <div class="flex items-center gap-2">
                    <select id="modal_estado_select_{{ $pedido->id }}" class="flex-1 h-10 px-3 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-800 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none">
                        <option value="preparacion" {{ $pedido->estado === 'preparacion' ? 'selected' : '' }}>En Preparación / Empaque</option>
                        <option value="en_ruta" {{ $pedido->estado === 'en_ruta' ? 'selected' : '' }}>En Ruta (Repartidor)</option>
                        <option value="entregado" {{ $pedido->estado === 'entregado' ? 'selected' : '' }}>Entregado al Cliente ✅</option>
                        <option value="cancelado" class="text-red-600">Cancelar Pedido</option>
                    </select>
                    <button type="button" onclick="guardarEstadoDesdeModal({{ $pedido->id }})" class="px-4 h-10 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-black flex items-center gap-1 transition-colors">
                        Actualizar
                    </button>
                </div>
            @else
                <div class="p-3 rounded-xl bg-gray-100 border border-gray-200 text-xs text-gray-700 font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-gray-500">lock</span>
                    Fase actual: {{ strtoupper(str_replace('_', ' ', $pedido->estado)) }} (Solo lectura).
                </div>
            @endif

            <div class="pt-2 grid grid-cols-2 gap-2">
                <a href="{{ route('admin.pedidos.ticket', $pedido->id) }}" target="_blank" class="py-2.5 px-3 bg-white hover:bg-blue-50 text-gray-700 hover:text-[#1e549e] font-bold text-xs rounded-xl border border-gray-200 hover:border-blue-200 flex items-center justify-center gap-1.5 transition-all shadow-2xs" title="Imprimir Ticket Térmico 80mm">
                    <span class="material-symbols-outlined text-[17px] text-[#1e549e]">receipt_long</span>
                    <span>Ticket (80mm)</span>
                </a>
                <a href="{{ route('admin.pedidos.factura', $pedido->id) }}" target="_blank" class="py-2.5 px-3 bg-white hover:bg-emerald-50 text-gray-700 hover:text-emerald-700 font-bold text-xs rounded-xl border border-gray-200 hover:border-emerald-200 flex items-center justify-center gap-1.5 transition-all shadow-2xs" title="Imprimir Factura Fiscal A4 / Guardar PDF">
                    <span class="material-symbols-outlined text-[17px] text-emerald-600">description</span>
                    <span>Factura (A4 / PDF)</span>
                </a>
            </div>
        </div>

        {{-- RESUMEN DE TOTALES --}}
        <div class="bg-slate-900 text-white rounded-2xl p-5 space-y-2.5 flex flex-col justify-between shadow-md">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block pb-1 border-b border-white/10">Resumen Financiero</span>
            
            <div class="space-y-1.5 text-xs text-slate-300">
                <div class="flex justify-between">
                    <span>Subtotal Productos:</span>
                    <span class="font-bold text-white">${{ number_format($pedido->subtotal_usd, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Costo Delivery:</span>
                    <span class="font-bold text-white">${{ number_format($pedido->costo_delivery_usd, 2) }}</span>
                </div>
                @if($pedido->descuento_usd > 0)
                    <div class="flex justify-between text-emerald-400">
                        <span>Descuento:</span>
                        <span class="font-bold">-${{ number_format($pedido->descuento_usd, 2) }}</span>
                    </div>
                @endif
            </div>

            <div class="pt-3 border-t border-white/10 flex items-baseline justify-between">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Orden</span>
                    <span class="text-2xl font-black text-white leading-none">${{ number_format($pedido->total_usd, 2) }}</span>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total en Bolívares</span>
                    <span class="text-sm font-black text-[#aad6f3]">Bs. {{ number_format($pedido->total_ves_calculado, 2) }}</span>
                </div>
            </div>
        </div>

    </div>

</div>