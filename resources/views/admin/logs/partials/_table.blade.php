{{-- Indicador sutil de desplazamiento en móviles --}}
<div class="sm:hidden px-4 py-2.5 bg-gradient-to-r from-indigo-50/90 via-blue-50/60 to-indigo-50/90 border-b border-indigo-100/70 flex items-center justify-between text-[11px] text-indigo-900 font-semibold select-none">
    <div class="flex items-center gap-1.5">
        <span class="material-symbols-outlined text-[16px] text-indigo-600">swipe</span>
        <span>Desliza la tabla para ver todos los detalles</span>
    </div>
    <span class="material-symbols-outlined text-[15px] text-indigo-500 animate-pulse">arrow_forward</span>
</div>

<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-collapse min-w-[840px]">
        <thead>
            <tr class="border-b border-gray-200 bg-gray-50/80 text-[11px] font-black text-gray-500 uppercase tracking-wider">
                <th class="px-4 py-3 min-w-[130px] w-36">¿Cuándo fue?</th>
                <th class="px-4 py-3 min-w-[190px] w-52">¿Quién lo hizo?</th>
                <th class="px-4 py-3 min-w-[160px] w-48">¿En qué pantalla?</th>
                <th class="px-4 py-3 min-w-[240px]">¿Qué ocurrió?</th>
                <th class="px-4 py-3 min-w-[140px] w-36 text-right">Acción</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse($logs as $log)
            <tr class="hover:bg-blue-50/30 transition-colors group">
                
                {{-- Columna 1: Fecha y Hora en Criollo --}}
                <td class="px-4 py-3 align-top min-w-[130px]">
                    <div class="flex flex-col whitespace-nowrap">
                        <span class="text-xs font-bold text-gray-900 leading-tight">
                            {{ $log->fecha ? $log->fecha->format('d/m/Y') : 'N/A' }}
                        </span>
                        <span class="text-[11px] font-mono text-gray-600 font-medium mt-0.5">
                            {{ $log->fecha ? $log->fecha->format('h:i:s A') : '' }}
                        </span>
                        <span class="text-[10px] text-gray-400 mt-0.5 font-medium">
                            {{ $log->fecha ? $log->fecha->diffForHumans() : '' }}
                        </span>
                    </div>
                </td>

                {{-- Columna 2: ¿Quién lo hizo? --}}
                <td class="px-4 py-3 align-top min-w-[190px]">
                    @if($log->usuario)
                        <div class="flex items-center gap-2.5">
                            <div class="size-8 rounded-xl bg-gradient-to-tr from-slate-800 to-indigo-900 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                {{ strtoupper(substr($log->usuario->nombre, 0, 1)) }}
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="text-xs font-bold text-gray-900 truncate" title="{{ $log->usuario->nombre }} {{ $log->usuario->apellido }} ({{ $log->usuario->email }})">
                                    {{ $log->usuario->nombre }} {{ $log->usuario->apellido }}
                                </span>
                                <div class="flex items-center gap-1.5 text-[10px] text-gray-400 mt-0.5 flex-wrap">
                                    <span class="font-bold text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded border border-indigo-100 whitespace-nowrap">
                                        {{ $log->usuario->rol->nombre ?? 'Usuario' }}
                                    </span>
                                    <span class="font-mono text-gray-400 whitespace-nowrap">{{ $log->direccion_ip }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-2.5 text-gray-400">
                            <div class="size-8 rounded-xl bg-gray-100 text-gray-500 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[17px]">smart_toy</span>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="text-xs font-bold text-gray-600 truncate">El Sistema (Automático)</span>
                                <span class="font-mono text-[10px] text-gray-400 mt-0.5 whitespace-nowrap">{{ $log->direccion_ip }}</span>
                            </div>
                        </div>
                    @endif
                </td>

                {{-- Columna 3: Pantalla y Acción Amigable --}}
                <td class="px-4 py-3 align-top min-w-[160px]">
                    <div class="flex flex-col items-start gap-1">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider border {{ $log->badge_color }} shadow-2xs max-w-full whitespace-nowrap" title="{{ $log->evento_amigable }}">
                            <span class="material-symbols-outlined text-[13px] shrink-0">{{ $log->icono }}</span>
                            <span>{{ $log->evento_amigable }}</span>
                        </span>
                        <div class="flex items-center gap-1 text-[11px] text-slate-600 font-medium mt-0.5">
                            <span class="font-semibold">{{ $log->modulo_nombre }}</span>
                            @if($log->registro_id)
                                <span class="text-[10px] font-mono text-gray-400">#{{ $log->registro_id }}</span>
                            @endif
                        </div>
                    </div>
                </td>

                {{-- Columna 4: Explicación de Qué Pasó --}}
                <td class="px-4 py-3 align-top min-w-[240px]">
                    <div class="flex flex-col gap-1">
                        <p class="text-xs font-medium text-gray-800 leading-snug break-words">
                            {{ $log->descripcion ?: 'Acción realizada en el sistema.' }}
                        </p>
                        @if($log->valores_anteriores || $log->valores_nuevos)
                            <div class="flex items-center gap-1.5 flex-wrap mt-0.5">
                                <span class="inline-flex items-center gap-1 text-[9px] font-bold text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded border border-indigo-200 whitespace-nowrap">
                                    <span class="size-1 rounded-full bg-indigo-500"></span> Datos modificados
                                </span>
                            </div>
                        @endif
                    </div>
                </td>

                {{-- Columna 5: Botón Amigable "Ver qué cambió" --}}
                <td class="px-4 py-3 align-top text-right min-w-[140px]">
                    <button type="button" 
                            onclick="inspeccionarLog({{ $log->id }})" 
                            class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-900 hover:bg-indigo-600 text-white text-[11px] font-bold transition-all shadow-2xs hover:scale-105 active:scale-95 group/btn whitespace-nowrap shrink-0" 
                            title="Ver exactamente qué cambió en este movimiento">
                        <span class="material-symbols-outlined text-[15px] text-sky-400 group-hover/btn:text-white transition-colors">visibility</span>
                        <span>Ver qué cambió</span>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-14 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                        <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-3 text-gray-400">
                            <span class="material-symbols-outlined text-3xl">manage_search</span>
                        </div>
                        <h4 class="font-black text-gray-800 text-sm">No se encontraron movimientos registrados</h4>
                        <p class="text-xs text-gray-500 mt-1">Prueba cambiando las fechas o quitando los filtros de búsqueda.</p>
                        @if(request('buscar') || request('modulo') || request('evento') || request('usuario_id') || request('fecha_desde'))
                        <button type="button" onclick="resetearTodosFiltros()" class="mt-3 px-3.5 py-1.5 rounded-xl text-xs font-bold text-[#1e549e] bg-blue-50 hover:bg-blue-100 border border-blue-200/60 transition-all flex items-center gap-1.5 shadow-2xs hover:scale-105 active:scale-95">
                            <span class="material-symbols-outlined text-[16px]">filter_alt_off</span>
                            Quitar filtros aplicados
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Paginación y Resumen en Criollo --}}
<div class="px-4 sm:px-5 py-3.5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 ajax-pagination bg-gray-50/70">
    <div class="flex items-center gap-2 text-xs text-gray-500 font-medium text-center sm:text-left">
        <span class="material-symbols-outlined text-[17px] text-indigo-600 shrink-0">receipt_long</span>
        <span>
            Mostrando del <strong class="text-gray-800 font-bold">{{ $logs->firstItem() ?? ($logs->count() > 0 ? 1 : 0) }}</strong> al <strong class="text-gray-800 font-bold">{{ $logs->lastItem() ?? $logs->count() }}</strong> (Total: <strong class="text-gray-800 font-bold">{{ $logs->total() }}</strong> movimientos)
        </span>
    </div>
    <div class="flex items-center justify-center gap-1 w-full sm:w-auto">
        @if($logs->hasPages())
            {{ $logs->links('pagination::tailwind') }}
        @else
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-white border border-gray-200 text-xs font-bold text-gray-600 shadow-2xs">
                <span>Página 1 de 1</span>
            </span>
        @endif
    </div>
</div>
