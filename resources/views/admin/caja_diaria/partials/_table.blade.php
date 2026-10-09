<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50/75">
                <th class="px-5 py-3.5 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Turno / Estado</th>
                <th class="px-5 py-3.5 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Caja & Cajero</th>
                <th class="px-5 py-3.5 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Horario</th>
                <th class="px-5 py-3.5 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-right">Base Inicial</th>
                <th class="px-5 py-3.5 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-right">Total Ventas</th>
                <th class="px-5 py-3.5 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-center">Arqueo (Cuadre)</th>
                <th class="px-5 py-3.5 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100/80 bg-white">
            @forelse($sesiones as $sesion)
            @php
                $estaAbierta = is_null($sesion->fecha_cierre);
                $diferencia = (float) $sesion->diferencia_usd;
                $apertura = \Carbon\Carbon::parse($sesion->fecha_apertura);
                $cierre = $sesion->fecha_cierre ? \Carbon\Carbon::parse($sesion->fecha_cierre) : null;
            @endphp
            <tr class="hover:bg-blue-50/30 transition-colors group">
                
                {{-- Turno ID & Estado --}}
                <td class="px-5 py-4 align-top">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="font-bold text-xs text-gray-900 bg-gray-100 px-2 py-0.5 rounded-md border border-gray-200">
                            #{{ $sesion->id }}
                        </span>
                        @if($estaAbierta)
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                <span class="size-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                                En curso
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 text-slate-700">
                                <span class="material-symbols-outlined text-[12px]">lock</span>
                                Cerrado
                            </span>
                        @endif
                    </div>
                    <span class="text-[11px] text-gray-400 font-medium">
                        {{ $sesion->pedidos ? $sesion->pedidos->count() : 0 }} ventas registradas
                    </span>
                </td>

                {{-- Caja y Cajero --}}
                <td class="px-5 py-4 align-top">
                    <div class="flex items-center gap-2.5">
                        <div class="size-8 rounded-xl bg-blue-50 text-[#1e549e] flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[18px]">point_of_sale</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-xs sm:text-sm leading-tight">
                                {{ $sesion->caja ? $sesion->caja->nombre : 'Caja Principal' }}
                            </p>
                            <p class="text-xs text-gray-500 font-medium mt-0.5">
                                {{ $sesion->cajero ? trim($sesion->cajero->nombre . ' ' . $sesion->cajero->apellido) : 'Cajero Desconocido' }}
                            </p>
                        </div>
                    </div>
                </td>

                {{-- Horarios --}}
                <td class="px-5 py-4 align-top">
                    <p class="text-xs font-semibold text-gray-800 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px] text-gray-400">login</span>
                        {{ $apertura->format('d/m/Y h:i A') }}
                    </p>
                    @if($cierre)
                        <p class="text-[11px] text-gray-500 mt-0.5 flex items-center gap-1 font-medium">
                            <span class="material-symbols-outlined text-[14px] text-gray-400">logout</span>
                            {{ $cierre->format('d/m/Y h:i A') }}
                        </p>
                    @else
                        <span class="text-[10px] font-semibold text-emerald-600 block mt-0.5">Operando en vivo...</span>
                    @endif
                </td>

                {{-- Monto Inicial Base --}}
                <td class="px-5 py-4 align-top text-right">
                    <p class="font-bold text-gray-800 text-xs sm:text-sm">${{ number_format($sesion->monto_inicial_usd, 2) }}</p>
                    <p class="text-[10px] text-gray-400 font-medium">Fondo gaveta</p>
                </td>

                {{-- Total Ventas del Sistema --}}
                <td class="px-5 py-4 align-top text-right">
                    <p class="font-bold text-[#1e549e] text-xs sm:text-sm leading-tight">${{ number_format($sesion->total_ventas_sistema_usd, 2) }}</p>
                    <p class="text-[11px] text-gray-400 font-medium mt-0.5">Bs. {{ number_format($sesion->total_ventas_sistema_ves, 2) }}</p>
                </td>

                {{-- Arqueo y Diferencia --}}
                <td class="px-5 py-4 align-top text-center">
                    @if($estaAbierta)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-800 border border-amber-200">
                            <span class="material-symbols-outlined text-[13px]">hourglass_top</span> Pendiente
                        </span>
                    @else
                        @if(abs($diferencia) < 0.01)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="material-symbols-outlined text-[14px]">check_circle</span> Cuadrada ($0.00)
                            </span>
                        @elseif($diferencia > 0)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                <span class="material-symbols-outlined text-[14px]">add_circle</span> +${{ number_format($diferencia, 2) }} Sobrante
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                <span class="material-symbols-outlined text-[14px]">warning</span> -${{ number_format(abs($diferencia), 2) }} Faltante
                            </span>
                        @endif
                    @endif
                </td>

                {{-- Acciones --}}
                <td class="px-5 py-4 align-top text-right">
                    <div class="flex items-center justify-end gap-1.5">
                        {{-- Enlace a la nueva página completa de auditoría --}}
                        <a href="{{ route('admin.caja_diaria.show', $sesion->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-[#1e549e] bg-blue-50 hover:bg-blue-100 hover:text-[#153e77] transition-all border border-blue-200/60" title="Ver auditoría completa y detallada del turno">
                            <span class="material-symbols-outlined text-[15px]">visibility</span>
                            <span>Auditoría</span>
                        </a>

                        {{-- Generar PDF de Cierre Z --}}
                        <a href="{{ route('admin.caja_diaria.pdf', $sesion->id) }}" target="_blank" class="inline-flex items-center gap-1 p-1.5 rounded-lg text-rose-600 hover:text-rose-800 hover:bg-rose-50 transition-all border border-rose-200" title="Generar Informe Oficial de Cierre en PDF">
                            <span class="material-symbols-outlined text-[16px]">picture_as_pdf</span>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-16 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center">
                        <span class="material-symbols-outlined text-4xl text-gray-300 mb-2">event_busy</span>
                        <p class="font-bold text-gray-700 text-sm">No se encontraron turnos con los filtros seleccionados.</p>
                        <p class="text-xs text-gray-400 mt-1">Prueba limpiando los filtros o seleccionando otra fecha.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="px-5 py-3.5 border-t border-gray-100 flex justify-center ajax-pagination">
    {{ $sesiones->links('pagination::tailwind') }}
</div>