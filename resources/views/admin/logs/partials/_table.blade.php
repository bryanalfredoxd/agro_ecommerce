<div class="w-full">
    <table class="w-full text-left border-collapse table-auto md:table-fixed">
        <thead>
            <tr class="border-b border-gray-200 bg-gray-50/80 text-[11px] font-black text-gray-500 uppercase tracking-wider">
                <th class="px-4 py-3 w-32 lg:w-36">Fecha / Hora</th>
                <th class="px-4 py-3 w-44 lg:w-48">Usuario / IP</th>
                <th class="px-4 py-3 w-40 lg:w-44">Módulo / Evento</th>
                <th class="px-4 py-3">Descripción del Cambio</th>
                <th class="px-4 py-3 w-24 text-right">Detalle</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse($logs as $log)
            <tr class="hover:bg-blue-50/30 transition-colors group">
                
                {{-- Columna 1: Fecha y Hora --}}
                <td class="px-4 py-3 align-top">
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-gray-900 leading-tight">
                            {{ $log->fecha ? $log->fecha->format('d/m/Y') : 'N/A' }}
                        </span>
                        <span class="text-[11px] font-mono text-gray-600 font-medium">
                            {{ $log->fecha ? $log->fecha->format('h:i:s A') : '' }}
                        </span>
                        <span class="text-[10px] text-gray-400 mt-0.5 font-medium">
                            {{ $log->fecha ? $log->fecha->diffForHumans() : '' }}
                        </span>
                    </div>
                </td>

                {{-- Columna 2: Usuario e IP --}}
                <td class="px-4 py-3 align-top">
                    @if($log->usuario)
                        <div class="flex items-center gap-2">
                            <div class="size-7 rounded-lg bg-gradient-to-tr from-slate-800 to-indigo-900 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                {{ strtoupper(substr($log->usuario->nombre, 0, 1)) }}
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="text-xs font-bold text-gray-900 truncate" title="{{ $log->usuario->nombre }} {{ $log->usuario->apellido }} ({{ $log->usuario->email }})">
                                    {{ $log->usuario->nombre }} {{ $log->usuario->apellido }}
                                </span>
                                <div class="flex items-center gap-1.5 text-[10px] text-gray-400">
                                    <span class="font-bold text-indigo-700 bg-indigo-50 px-1 py-0.2 rounded border border-indigo-100">
                                        {{ $log->usuario->rol->nombre ?? 'Usuario' }}
                                    </span>
                                    <span class="font-mono text-gray-400">{{ $log->direccion_ip }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-2 text-gray-400">
                            <div class="size-7 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[16px]">dns</span>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="text-xs font-bold text-gray-600 truncate">Sistema / CLI</span>
                                <span class="font-mono text-[10px] text-gray-400">{{ $log->direccion_ip }}</span>
                            </div>
                        </div>
                    @endif
                </td>

                {{-- Columna 3: Módulo y Tipo de Evento --}}
                <td class="px-4 py-3 align-top">
                    <div class="flex flex-col items-start gap-1">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider border {{ $log->badge_color }} shadow-2xs max-w-full truncate" title="{{ str_replace('_', ' ', $log->evento) }}">
                            <span class="material-symbols-outlined text-[13px] shrink-0">{{ $log->icono }}</span>
                            <span class="truncate">{{ str_replace('_', ' ', $log->evento) }}</span>
                        </span>
                        <div class="flex items-center gap-1 text-[11px] text-slate-600 font-medium">
                            <span class="truncate font-semibold">{{ $log->modulo_nombre }}</span>
                            @if($log->registro_id)
                                <span class="text-[10px] font-mono text-gray-400">#{{ $log->registro_id }}</span>
                            @endif
                        </div>
                    </div>
                </td>

                {{-- Columna 4: Descripción y Cambios --}}
                <td class="px-4 py-3 align-top">
                    <div class="flex flex-col gap-1">
                        <p class="text-xs font-medium text-gray-800 leading-snug break-words">
                            {{ $log->descripcion ?: 'Acción ejecutada sobre la base de datos.' }}
                        </p>
                        @if($log->valores_anteriores || $log->valores_nuevos)
                            <div class="flex items-center gap-1.5 flex-wrap">
                                @if($log->valores_anteriores)
                                    <span class="inline-flex items-center gap-1 text-[9px] font-bold text-rose-700 bg-rose-50 px-1.5 py-0.2 rounded border border-rose-200">
                                        <span class="size-1 rounded-full bg-rose-500"></span> Valores Anteriores
                                    </span>
                                @endif
                                @if($log->valores_nuevos)
                                    <span class="inline-flex items-center gap-1 text-[9px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200">
                                        <span class="size-1 rounded-full bg-emerald-500"></span> Nuevos Valores
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </td>

                {{-- Columna 5: Botón Detalle Forense --}}
                <td class="px-4 py-3 align-top text-right">
                    <button type="button" 
                            onclick="inspeccionarLog({{ $log->id }})" 
                            class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-900 hover:bg-indigo-600 text-white text-[11px] font-bold transition-all shadow-2xs hover:scale-105 active:scale-95 group/btn" 
                            title="Inspeccionar valores anteriores vs nuevos y metadatos forenses">
                        <span class="material-symbols-outlined text-[15px] text-sky-400 group-hover/btn:text-white transition-colors">visibility</span>
                        <span>Detalle</span>
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
                        <h4 class="font-black text-gray-800 text-sm">No se encontraron eventos de auditoría</h4>
                        <p class="text-xs text-gray-500 mt-1">Prueba ajustando tus filtros de fecha, tipo de evento o buscador.</p>
                        @if(request('buscar') || request('modulo') || request('evento') || request('usuario_id') || request('fecha_desde'))
                        <button type="button" onclick="resetearTodosFiltros()" class="mt-3 px-3.5 py-1.5 rounded-xl text-xs font-bold text-[#1e549e] bg-blue-50 hover:bg-blue-100 border border-blue-200/60 transition-all flex items-center gap-1.5 shadow-2xs hover:scale-105 active:scale-95">
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

{{-- Paginación y Resumen --}}
<div class="px-5 py-3.5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 ajax-pagination bg-gray-50/70">
    <div class="flex items-center gap-2 text-xs text-gray-500 font-medium">
        <span class="material-symbols-outlined text-[17px] text-indigo-600">receipt_long</span>
        <span>
            Mostrando <strong class="text-gray-800 font-bold">{{ $logs->firstItem() ?? ($logs->count() > 0 ? 1 : 0) }}</strong> a <strong class="text-gray-800 font-bold">{{ $logs->lastItem() ?? $logs->count() }}</strong> de <strong class="text-gray-800 font-bold">{{ $logs->total() }}</strong> eventos registrados
        </span>
    </div>
    <div class="flex items-center gap-1">
        @if($logs->hasPages())
            {{ $logs->links('pagination::tailwind') }}
        @else
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-white border border-gray-200 text-xs font-bold text-gray-600 shadow-2xs">
                <span>Página 1 de 1</span>
            </span>
        @endif
    </div>
</div>
