<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50/75">
                <th class="px-5 py-4 text-[11px] font-black text-gray-500 uppercase tracking-wider">Pedido / Fecha</th>
                <th class="px-5 py-4 text-[11px] font-black text-gray-500 uppercase tracking-wider">Cliente & Contacto</th>
                <th class="px-5 py-4 text-[11px] font-black text-gray-500 uppercase tracking-wider">Auditoría del Pago</th>
                <th class="px-5 py-4 text-[11px] font-black text-gray-500 uppercase tracking-wider text-center">Estado del Pedido</th>
                <th class="px-5 py-4 text-[11px] font-black text-gray-500 uppercase tracking-wider text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100/80 bg-white">
            @forelse($pedidos as $pedido)
            @php
                $pago = $pedido->pago;
                $tienePagoRevision = $pago && $pago->estado === 'revision';
                $pagoAprobado = $pago && $pago->estado === 'aprobado';
                $pagoRechazado = $pago && $pago->estado === 'rechazado';
            @endphp
            <tr class="hover:bg-blue-50/30 transition-colors group {{ $tienePagoRevision ? 'bg-amber-50/20' : '' }}">
                
                {{-- Columna 1: Info del Pedido y Canal --}}
                <td class="px-5 py-4 align-top">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-2">
                            <button onclick="verDetallePedido({{ $pedido->id }})" class="font-black text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1 transition-colors hover:underline" title="Ver detalle del pedido">
                                #{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}
                            </button>
                            @if($tienePagoRevision)
                                <span class="size-2 rounded-full bg-amber-500 animate-ping inline-block" title="Pago pendiente de revisión"></span>
                            @endif
                        </div>
                        <span class="text-[11px] text-gray-400 font-medium">{{ \Carbon\Carbon::parse($pedido->creado_at)->format('d/m/Y, h:i A') }}</span>
                        
                        <div class="mt-1">
                            @if($pedido->canal_venta === 'web')
                                <span class="inline-flex items-center gap-1 text-[9px] font-black text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-200/60 uppercase">
                                    <span class="material-symbols-outlined text-[12px]">language</span> Web
                                </span>
                            @elseif($pedido->canal_venta === 'whatsapp')
                                <span class="inline-flex items-center gap-1 text-[9px] font-black text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60 uppercase">
                                    <span class="material-symbols-outlined text-[12px]">chat</span> WhatsApp
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[9px] font-black text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200 uppercase">
                                    <span class="material-symbols-outlined text-[12px]">storefront</span> Tienda Fís.
                                </span>
                            @endif
                        </div>
                    </div>
                </td>

                {{-- Columna 2: Cliente y Contacto --}}
                <td class="px-5 py-4 align-top">
                    @if($pedido->usuario)
                        <div class="flex flex-col">
                            <span class="font-black text-gray-900 text-sm capitalize leading-tight">
                                {{ $pedido->usuario->nombre }} {{ $pedido->usuario->apellido }}
                            </span>
                            <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">
                                @if($pedido->usuario->documento_identidad)
                                    <span class="font-medium bg-gray-100 px-1.5 py-0.5 rounded text-[10px] text-gray-700">
                                        {{ $pedido->usuario->documento_identidad }}
                                    </span>
                                @endif
                                @if($pedido->usuario->telefono)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pedido->usuario->telefono) }}" target="_blank" class="inline-flex items-center gap-0.5 text-[11px] text-emerald-600 hover:text-emerald-700 font-bold hover:underline" title="Escribir por WhatsApp">
                                        <span class="material-symbols-outlined text-[13px]">call</span>
                                        {{ $pedido->usuario->telefono }}
                                    </a>
                                @endif
                            </div>
                            <span class="text-[11px] text-gray-400 truncate max-w-[200px] mt-0.5" title="{{ $pedido->usuario->email }}">
                                {{ $pedido->usuario->email }}
                            </span>
                        </div>
                    @else
                        <span class="text-xs text-gray-400 italic font-bold">Cliente en Caja (POS)</span>
                    @endif
                </td>

                {{-- Columna 3: Auditoría y Verificación del Pago (UNIFICADA) --}}
                <td class="px-5 py-4 align-top">
                    <div class="flex flex-col gap-1.5 max-w-[260px]">
                        {{-- Montos agrupados de forma limpia y consistente --}}
                        <div class="flex items-baseline gap-2">
                            <span class="font-black text-slate-900 text-base leading-none tracking-tight">
                                ${{ number_format($pedido->total_usd, 2) }}
                            </span>
                            <span class="text-xs font-bold text-slate-500 font-mono">
                                Bs. {{ number_format($pedido->total_ves_calculado, 2) }}
                            </span>
                        </div>

                        {{-- Método de Pago y Referencia --}}
                        @if($pago)
                            @php
                                $badgeMetodo = match($pago->metodo) {
                                    'pago_movil' => ['bg' => 'bg-sky-50 text-sky-700 border-sky-200/80', 'nombre' => 'Pago Móvil', 'icon' => 'phone_iphone'],
                                    'zelle' => ['bg' => 'bg-purple-50 text-purple-700 border-purple-200/80', 'nombre' => 'Zelle', 'icon' => 'payments'],
                                    'binance' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200/80', 'nombre' => 'Binance Pay', 'icon' => 'currency_bitcoin'],
                                    'transferencia' => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200/80', 'nombre' => 'Transferencia', 'icon' => 'account_balance'],
                                    'efectivo_usd', 'efectivo_bs' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80', 'nombre' => 'Efectivo', 'icon' => 'attach_money'],
                                    'punto_venta' => ['bg' => 'bg-indigo-50 text-indigo-700 border-indigo-200/80', 'nombre' => 'Punto de Venta', 'icon' => 'point_of_sale'],
                                    'biopago' => ['bg' => 'bg-cyan-50 text-cyan-700 border-cyan-200/80', 'nombre' => 'Biopago', 'icon' => 'fingerprint'],
                                    default => ['bg' => 'bg-gray-50 text-gray-700 border-gray-200/80', 'nombre' => ucfirst(str_replace('_', ' ', $pago->metodo ?? 'Otro')), 'icon' => 'credit_card'],
                                };
                            @endphp

                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black border {{ $badgeMetodo['bg'] }} uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px]">{{ $badgeMetodo['icon'] }}</span>
                                    {{ $badgeMetodo['nombre'] }}
                                </span>

                                @if($pago->referencia_bancaria)
                                    <span class="inline-flex items-center font-mono font-bold text-[10px] text-gray-700 bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200/80 select-all" title="Referencia">
                                        Ref: {{ $pago->referencia_bancaria }}
                                    </span>
                                @endif
                            </div>

                            {{-- Estado del Pago y Botón Comprobante alineados --}}
                            <div class="flex items-center gap-1.5 flex-wrap pt-0.5">
                                @if($tienePagoRevision)
                                    @if($pago->esPresencial())
                                        @if($pedido->es_delivery)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-teal-50 text-teal-800 border border-teal-300" title="Cobro en efectivo al entregar">
                                                <span class="material-symbols-outlined text-[13px] text-teal-600">local_shipping</span>
                                                Contra Entrega
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-indigo-50 text-indigo-800 border border-indigo-300" title="Cobro en caja/mostrador al retirar">
                                                <span class="material-symbols-outlined text-[13px] text-indigo-600">store</span>
                                                Cobro en Sede
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-amber-50 text-amber-800 border border-amber-300">
                                            <span class="material-symbols-outlined text-[13px] text-amber-600 animate-spin">autorenew</span>
                                            Por Verificar
                                        </span>
                                    @endif
                                @elseif($pagoAprobado)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-emerald-50 text-emerald-800 border border-emerald-300" title="{{ $pago->verificador ? 'Verificado por ' . $pago->verificador->nombre : 'Verificado' }}">
                                        <span class="material-symbols-outlined text-[13px] text-emerald-600">verified</span>
                                        Pago Aprobado
                                        @if($pago->verificador)
                                            <span class="text-[9px] font-medium text-emerald-700">({{ $pago->verificador->nombre }})</span>
                                        @endif
                                    </span>
                                @elseif($pagoRechazado)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-rose-50 text-rose-800 border border-rose-300">
                                        <span class="material-symbols-outlined text-[13px] text-rose-600">cancel</span>
                                        Pago Rechazado
                                    </span>
                                @endif

                                @if($pago->captura_pago_url)
                                    <button type="button" 
                                            onclick="verComprobante('{{ $pago->url_comprobante }}', '{{ $pago->referencia_bancaria ?? 'SIN-REF' }}', {{ $pago->id }}, '{{ $pago->estado }}', {{ $pedido->id }})" 
                                            class="inline-flex items-center gap-1 text-[10px] font-black text-blue-700 bg-blue-50/90 hover:bg-blue-100 hover:text-blue-800 px-2 py-0.5 rounded-md border border-blue-200 transition-colors shadow-2xs" 
                                            title="Ver comprobante adjunto">
                                        <span class="material-symbols-outlined text-[13px]">receipt_long</span>
                                        Comprobante
                                    </button>
                                @endif
                            </div>

                        @else
                            {{-- Sin Pago Reportado --}}
                            <div class="inline-flex items-center gap-1 text-xs text-amber-700 font-semibold bg-amber-50/80 px-2 py-1 rounded-md border border-amber-200/60 w-fit">
                                <span class="material-symbols-outlined text-[14px]">hourglass_empty</span>
                                <span class="text-[10px] font-bold uppercase">Esperando Reporte</span>
                            </div>
                        @endif
                    </div>
                </td>

                {{-- Columna 4: Estado del Pedido / Despacho --}}
                <td class="px-5 py-4 align-top text-center">
                    @php
                        $clasesEstado = match($pedido->estado) {
                            'pendiente' => 'bg-amber-50 text-amber-700 border-amber-300/80',
                            'pagado' => 'bg-blue-50 text-blue-700 border-blue-300/80',
                            'preparacion' => 'bg-indigo-50 text-indigo-700 border-indigo-300/80',
                            'en_ruta' => 'bg-orange-50 text-orange-700 border-orange-300/80',
                            'entregado', 'completado_caja' => 'bg-emerald-50 text-emerald-700 border-emerald-300/80',
                            'devuelto', 'cancelado' => 'bg-rose-50 text-rose-700 border-rose-300/80',
                            default => 'bg-gray-50 text-gray-600 border-gray-200',
                        };
                        $iconoEstado = match($pedido->estado) {
                            'pendiente' => 'hourglass_empty',
                            'pagado' => 'task_alt',
                            'preparacion' => 'inventory_2',
                            'en_ruta' => 'local_shipping',
                            'entregado', 'completado_caja' => 'check_circle',
                            'devuelto' => 'assignment_return',
                            'cancelado' => 'cancel',
                            default => 'info',
                        };
                    @endphp
                    <div class="flex flex-col items-center gap-1.5">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg border text-[10px] font-black uppercase tracking-wider {{ $clasesEstado }} shadow-2xs">
                            <span class="material-symbols-outlined text-[14px]">{{ $iconoEstado }}</span>
                            {{ str_replace('_', ' ', $pedido->estado) }}
                        </span>
                        
                        <div class="flex items-center gap-1 text-[10px] font-medium">
                            <span class="material-symbols-outlined text-[13px] {{ $pedido->es_delivery ? 'text-blue-500' : 'text-emerald-600' }}">
                                {{ $pedido->es_delivery ? 'local_shipping' : 'storefront' }}
                            </span>
                            <span class="line-clamp-1 max-w-[140px] font-bold {{ $pedido->es_delivery ? 'text-blue-700' : 'text-emerald-700' }}" title="{{ $pedido->tipo_entrega_texto }}">
                                {{ $pedido->es_delivery ? 'Delivery' : 'Retiro en Sede' }}
                            </span>
                        </div>
                    </div>
                </td>

                {{-- Columna 5: Acciones Unificadas (Aprobación de Pago + Logística + Detalle) --}}
                <td class="px-5 py-4 align-top text-right">
                    <div class="flex items-center justify-end gap-1.5 flex-wrap">
                        
                        {{-- BOTONES DIRECTOS DE AUDITORÍA DE PAGO (Si está en revisión y tiene permiso) --}}
                        @if($tienePagoRevision && Auth::user()->tienePermiso('gestionar_pagos'))
                            <button type="button" onclick="aprobarPagoDirecto({{ $pago->id }}, '{{ $pago->referencia_bancaria ?? 'SIN-REF' }}', {{ $pedido->id }}, {{ $pago->esPresencial() ? 'true' : 'false' }})" class="size-8 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center transition-all shadow-2xs hover:scale-105 active:scale-95" title="{{ $pago->esPresencial() ? ($pedido->es_delivery ? 'Confirmar Cobro Contra Entrega' : 'Confirmar Cobro en Caja') : 'Aprobar Pago y Liberar Pedido' }}">
                                <span class="material-symbols-outlined text-[18px]">{{ $pago->esPresencial() ? 'payments' : 'check' }}</span>
                            </button>
                            <button type="button" onclick="rechazarPagoDirecto({{ $pago->id }}, '{{ $pago->referencia_bancaria ?? 'SIN-REF' }}', {{ $pedido->id }})" class="size-8 rounded-xl bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center transition-all shadow-2xs hover:scale-105 active:scale-95" title="Rechazar Pago">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                            </button>
                        @endif

                        {{-- BOTÓN CAMBIAR ESTADO LOGÍSTICO (Si ya está pagado y tiene permiso) --}}
                        @if(!in_array($pedido->estado, ['pendiente', 'entregado', 'completado_caja', 'devuelto', 'cancelado']) && Auth::user()->tienePermiso('actualizar_estado_pedido'))
                            <button type="button" onclick="openStatusModal({{ $pedido->id }}, '{{ $pedido->estado }}', {{ $pedido->es_delivery ? 'true' : 'false' }})" class="size-8 rounded-xl {{ $pedido->es_delivery ? 'bg-blue-50 border-blue-200 text-blue-600 hover:bg-blue-600' : 'bg-emerald-50 border-emerald-200 text-emerald-600 hover:bg-emerald-600' }} border hover:text-white flex items-center justify-center transition-all shadow-2xs hover:scale-105 active:scale-95" title="{{ $pedido->es_delivery ? 'Despacho Logístico (Delivery)' : 'Entrega en Tienda Física' }}">
                                <span class="material-symbols-outlined text-[17px]">{{ $pedido->es_delivery ? 'local_shipping' : 'storefront' }}</span>
                            </button>
                        @endif

                        {{-- BOTÓN IMPRIMIR (Ticket 80mm o Factura A4) --}}
                        @if(in_array($pedido->estado, ['pagado', 'preparacion', 'en_ruta', 'entregado', 'completado_caja']))
                            <button type="button" 
                                    onclick="abrirOpcionesImpresion({{ $pedido->id }}, '{{ $pedido->factura ? $pedido->factura->numero_factura : '' }}')" 
                                    class="size-8 rounded-xl bg-white hover:bg-gray-100 text-gray-700 border border-gray-200 flex items-center justify-center transition-all shadow-2xs hover:scale-105 active:scale-95" 
                                    title="Imprimir (Ticket 80mm o Factura A4 / PDF)">
                                <span class="material-symbols-outlined text-[17px]">print</span>
                            </button>
                        @endif

                        {{-- BOTÓN VER DETALLE COMPLETO (360°) --}}
                        <button type="button" onclick="verDetallePedido({{ $pedido->id }})" class="size-8 rounded-xl bg-slate-900 hover:bg-black text-white flex items-center justify-center transition-all shadow-2xs hover:scale-105 active:scale-95" title="Ver Detalle Completo 360°">
                            <span class="material-symbols-outlined text-[17px]">visibility</span>
                        </button>

                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-16 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-3 text-gray-400">
                            <span class="material-symbols-outlined text-3xl">receipt_long</span>
                        </div>
                        <h4 class="font-black text-gray-800 text-base">No se encontraron pedidos</h4>
                        <p class="text-xs text-gray-500 mt-1">Prueba seleccionando otra pestaña de estado o ajusta tus filtros de búsqueda.</p>
                        @if(request('buscar') || (request('filtro_canal') && request('filtro_canal') !== 'todos') || (request('filtro_metodo') && request('filtro_metodo') !== 'todos'))
                        <button type="button" onclick="resetearTodosFiltros()" class="mt-4 px-4 py-2 rounded-xl text-xs font-bold text-[#1e549e] bg-blue-50 hover:bg-blue-100 border border-blue-200/60 transition-all flex items-center gap-1.5 shadow-2xs hover:scale-105 active:scale-95">
                            <span class="material-symbols-outlined text-[16px]">filter_alt_off</span>
                            Limpiar filtros aplicados
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Paginación Ajax --}}
<div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 ajax-pagination bg-gray-50/50">
    <div class="text-xs text-gray-500 font-medium">
        Mostrando {{ $pedidos->firstItem() ?? 0 }} a {{ $pedidos->lastItem() ?? 0 }} de {{ $pedidos->total() }} pedidos registrados
    </div>
    <div>
        {{ $pedidos->links('pagination::tailwind') }}
    </div>
</div>