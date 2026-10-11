{{-- TABLA DE SOLICITUDES DE PRODUCTOS E INSUMOS --}}
<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full min-w-[1020px] text-left border-collapse">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50/75 text-[11px] font-black uppercase tracking-wider text-gray-500">
                <th class="px-5 py-4 w-36">ID & Fecha</th>
                <th class="px-5 py-4 min-w-[280px]">Insumo / Requerimiento</th>
                <th class="px-5 py-4 min-w-[260px]">Cliente & Contacto</th>
                <th class="px-5 py-4 text-center w-48">Estado</th>
                <th class="px-5 py-4 text-right w-36">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-xs text-gray-700 font-medium bg-white">
            @forelse($solicitudes as $solicitud)
                @php
                    $badge = $solicitud->badge_estado;
                    $telLimpio = preg_replace('/[^0-9]/', '', $solicitud->telefono_cliente ?? '');
                    if (str_starts_with($telLimpio, '0')) {
                        $telWhatsapp = '58' . substr($telLimpio, 1);
                    } elseif (str_starts_with($telLimpio, '58')) {
                        $telWhatsapp = $telLimpio;
                    } else {
                        $telWhatsapp = $telLimpio;
                    }
                    $mensajeWa = rawurlencode("Hola " . ($solicitud->nombre_cliente ?: 'amigo') . ", le contactamos de Agrosuministros Rodríguez C.A. en respuesta a su solicitud del producto: \"" . $solicitud->nombre_producto . "\".");
                @endphp
                <tr class="hover:bg-blue-50/30 transition-colors group" id="row-solicitud-{{ $solicitud->id }}">
                    
                    {{-- 1. ID & FECHA --}}
                    <td class="px-5 py-4.5 align-top w-36">
                        <div class="flex flex-col gap-1">
                            <span class="font-mono font-black text-xs text-[#1e549e] bg-blue-50/80 border border-blue-100/60 px-2.5 py-0.5 rounded-lg w-fit shadow-2xs">
                                #SOL-{{ str_pad($solicitud->id, 4, '0', STR_PAD_LEFT) }}
                            </span>
                            <div class="mt-1">
                                <span class="text-xs font-bold text-gray-800 block">
                                    {{ $solicitud->fecha_peticion ? $solicitud->fecha_peticion->format('d/m/Y') : 'Reciente' }}
                                </span>
                                <span class="text-[11px] text-gray-400 font-medium block">
                                    {{ $solicitud->fecha_peticion ? $solicitud->fecha_peticion->format('h:i A') : '' }}
                                </span>
                            </div>
                            <span class="text-[10px] text-gray-400 font-semibold mt-0.5">
                                {{ $solicitud->fecha_peticion ? $solicitud->fecha_peticion->diffForHumans() : '' }}
                            </span>
                        </div>
                    </td>

                    {{-- 2. PRODUCTO / INSUMO Y ESPECIFICACIONES --}}
                    <td class="px-5 py-4.5 align-top min-w-[280px]">
                        <div class="flex items-start gap-3">
                            <div class="size-9 rounded-xl bg-gradient-to-br from-[#e5f2fb] to-white text-[#1e549e] border border-[#aad6f3]/50 flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
                                <span class="material-symbols-outlined text-[20px]">box_add</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-black text-gray-900 text-sm leading-snug break-words group-hover:text-[#1e549e] transition-colors">
                                    {{ $solicitud->nombre_producto }}
                                </h4>
                                
                                @if($solicitud->descripcion_adicional)
                                    <div class="mt-2 p-3 bg-slate-50/90 rounded-xl border border-slate-200/70 text-slate-700 text-xs leading-relaxed">
                                        <span class="flex items-center gap-1 font-bold text-slate-400 uppercase text-[9px] tracking-wider mb-1">
                                            <span class="material-symbols-outlined text-[13px] text-slate-400">notes</span>
                                            Detalles o Presentación:
                                        </span>
                                        <p class="whitespace-pre-line text-xs font-medium text-slate-600">{{ $solicitud->descripcion_adicional }}</p>
                                    </div>
                                @else
                                    <p class="text-[11px] text-gray-400 italic mt-1 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[13px]">info</span>
                                        Sin especificaciones adicionales
                                    </p>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- 3. CLIENTE & CONTACTO --}}
                    <td class="px-5 py-4.5 align-top min-w-[260px]">
                        <div class="flex flex-col gap-1.5">
                            {{-- Nombre y Badge Tipo --}}
                            <div class="flex items-center flex-wrap gap-1.5">
                                <span class="font-black text-gray-900 text-xs sm:text-sm">
                                    {{ $solicitud->nombre_cliente }}
                                </span>
                                @if($solicitud->usuario)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200/80" title="Usuario registrado en la tienda">
                                        <span class="size-1.5 rounded-full bg-emerald-500"></span> Cliente
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200/80" title="Solicitud como visitante web">
                                        <span class="size-1.5 rounded-full bg-amber-500"></span> Invitado
                                    </span>
                                @endif
                            </div>

                            {{-- Email --}}
                            @if($solicitud->email_cliente)
                                <a href="mailto:{{ $solicitud->email_cliente }}" class="text-[11px] text-gray-500 hover:text-blue-600 truncate flex items-center gap-1.5 transition-colors w-fit">
                                    <span class="material-symbols-outlined text-[14px] text-gray-400 shrink-0">mail</span>
                                    <span class="truncate">{{ $solicitud->email_cliente }}</span>
                                </a>
                            @endif

                            {{-- Teléfono / WhatsApp en fila limpia sin solapamiento --}}
                            @if($solicitud->telefono_cliente)
                                <div class="flex items-center flex-wrap gap-2 mt-1">
                                    {{-- Botón de Llamada --}}
                                    <a href="tel:{{ $solicitud->telefono_cliente }}" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition-all shadow-2xs" 
                                       title="Llamar al cliente">
                                        <span class="material-symbols-outlined text-[14px] text-gray-500">call</span>
                                        <span>{{ $solicitud->telefono_cliente }}</span>
                                    </a>

                                    {{-- Botón directo de WhatsApp --}}
                                    @if(!empty($telWhatsapp))
                                        <a href="https://wa.me/{{ $telWhatsapp }}?text={{ $mensajeWa }}" 
                                           target="_blank" 
                                           rel="noopener noreferrer"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-bold transition-all shadow-2xs hover:scale-105"
                                           title="Escribir al cliente por WhatsApp">
                                            <span class="material-symbols-outlined text-[14px] text-emerald-600">chat</span>
                                            <span>WhatsApp</span>
                                        </a>
                                    @endif
                                </div>
                            @else
                                <span class="text-[11px] text-gray-400 italic flex items-center gap-1 mt-0.5">
                                    <span class="material-symbols-outlined text-[13px]">phone_disabled</span>
                                    Sin teléfono registrado
                                </span>
                            @endif
                        </div>
                    </td>

                    {{-- 4. ESTADO DE LA SOLICITUD --}}
                    <td class="px-5 py-4.5 align-top text-center w-48">
                        <div class="flex flex-col items-center gap-2">
                            {{-- Badge representativo actual --}}
                            <span class="inline-flex items-center justify-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black border uppercase tracking-wider shadow-2xs w-full max-w-[170px] {{ $badge['bg'] }}" id="badge-estado-{{ $solicitud->id }}">
                                <span class="material-symbols-outlined text-[15px]">{{ $badge['icono'] }}</span>
                                <span class="truncate">{{ $badge['texto'] }}</span>
                            </span>

                            {{-- Selector rápido interactivo --}}
                            <div class="relative w-full max-w-[170px]">
                                <select onchange="cambiarEstadoSolicitud({{ $solicitud->id }}, this.value)" 
                                        class="w-full text-xs font-bold py-1.5 px-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 hover:border-[#1e549e] focus:border-[#1e549e] focus:ring-2 focus:ring-[#1e549e]/15 outline-none cursor-pointer transition-all shadow-2xs">
                                    <option value="pendiente" {{ $solicitud->estado == 'pendiente' ? 'selected' : '' }}>🕒 Pendiente</option>
                                    <option value="revisado" {{ $solicitud->estado == 'revisado' ? 'selected' : '' }}>🔍 En Revisión</option>
                                    <option value="agregado" {{ $solicitud->estado == 'agregado' ? 'selected' : '' }}>✅ Agregado</option>
                                    <option value="rechazado" {{ $solicitud->estado == 'rechazado' ? 'selected' : '' }}>❌ Descartado</option>
                                </select>
                            </div>
                        </div>
                    </td>

                    {{-- 5. ACCIONES --}}
                    <td class="px-5 py-4.5 align-top text-right w-36">
                        <div class="flex items-center justify-end gap-2">
                            {{-- Botón Crear Producto en Inventario --}}
                            @if(Auth::user()?->tienePermiso('gestionar_productos'))
                                <a href="{{ route('admin.productos.create', ['nombre' => $solicitud->nombre_producto, 'descripcion' => $solicitud->descripcion_adicional]) }}" 
                                   class="inline-flex items-center justify-center size-9 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 hover:border-emerald-600 transition-all shadow-2xs hover:shadow-md hover:scale-105" 
                                   title="Crear este producto en el catálogo">
                                    <span class="material-symbols-outlined text-[19px]">add_box</span>
                                </a>
                            @endif

                            {{-- Botón Eliminar --}}
                            <button type="button" 
                                    onclick="eliminarSolicitud({{ $solicitud->id }}, '{{ addslashes($solicitud->nombre_producto) }}')"
                                    class="inline-flex items-center justify-center size-9 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white border border-rose-200 hover:border-rose-600 transition-all shadow-2xs hover:shadow-md hover:scale-105 cursor-pointer" 
                                    title="Eliminar solicitud">
                                <span class="material-symbols-outlined text-[19px]">delete</span>
                            </button>
                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-14 px-4 text-center">
                        <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                            <div class="size-16 rounded-3xl bg-blue-50 text-[#1e549e] border border-blue-100 flex items-center justify-center mb-3 shadow-2xs">
                                <span class="material-symbols-outlined text-[32px]">inbox</span>
                            </div>
                            <h3 class="text-base font-black text-gray-800">No se encontraron solicitudes</h3>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                No hay requerimientos que coincidan con los filtros aplicados o aún no se han registrado peticiones de clientes.
                            </p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Paginación --}}
@if($solicitudes->hasPages())
    <div class="p-4 border-t border-gray-100 bg-gray-50/60">
        {{ $solicitudes->links() }}
    </div>
@endif
