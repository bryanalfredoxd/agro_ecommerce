<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50/75">
                <th class="px-5 py-3.5 text-[11px] font-bold text-gray-500 uppercase tracking-wider w-16">Habilitada</th>
                <th class="px-5 py-3.5 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Caja Registradora</th>
                <th class="px-5 py-3.5 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Cajero Asignado</th>
                <th class="px-5 py-3.5 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-center">Estado del Turno</th>
                <th class="px-5 py-3.5 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100/80 bg-white">
            @forelse($cajasFisicas as $caja)
            @php
                $turnoActivo = $caja->sesionActiva;
            @endphp
            <tr class="hover:bg-blue-50/30 transition-colors group {{ !$caja->activa ? 'opacity-60 bg-gray-50' : '' }}">
                
                {{-- Toggle Activa / Inactiva --}}
                <td class="px-5 py-4 align-middle">
                    @if(Auth::user()->tienePermiso('gestionar_caja_diaria'))
                    <label class="relative inline-flex items-center cursor-pointer" title="{{ $caja->activa ? 'Desactivar Caja' : 'Activar Caja' }}">
                        <input type="checkbox" class="sr-only peer" {{ $caja->activa ? 'checked' : '' }} onchange="toggleCaja({{ $caja->id }})">
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#1e549e]"></div>
                    </label>
                    @else
                    <span class="inline-flex size-2 rounded-full {{ $caja->activa ? 'bg-emerald-500' : 'bg-gray-300' }}" title="{{ $caja->activa ? 'Activa' : 'Inactiva' }}"></span>
                    @endif
                </td>

                {{-- Nombre de la Caja --}}
                <td class="px-5 py-4 align-middle">
                    <div class="flex items-center gap-2.5">
                        <div class="size-8 rounded-xl flex items-center justify-center font-bold text-sm {{ $caja->activa ? 'bg-blue-50 text-[#1e549e]' : 'bg-gray-100 text-gray-500' }}">
                            <span class="material-symbols-outlined text-[19px]">point_of_sale</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm leading-tight">{{ $caja->nombre }}</p>
                            <p class="text-[11px] text-gray-400 font-medium">ID #{{ $caja->id }}</p>
                        </div>
                    </div>
                </td>

                {{-- Cajero Asignado --}}
                <td class="px-5 py-4 align-middle">
                    @if($caja->cajeroAsignado)
                        <div class="flex items-center gap-2">
                            <div class="size-7 rounded-full bg-slate-800 text-white flex items-center justify-center text-xs font-bold uppercase">
                                {{ substr($caja->cajeroAsignado->nombre, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-xs">{{ $caja->cajeroAsignado->nombre }} {{ $caja->cajeroAsignado->apellido }}</p>
                                <p class="text-[11px] text-gray-500 font-medium">{{ $caja->cajeroAsignado->documento_identidad ?: 'Sin DNI' }}</p>
                            </div>
                        </div>
                    @else
                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-gray-400 bg-gray-100 px-2.5 py-1 rounded-lg">
                            <span class="material-symbols-outlined text-[14px]">person_off</span> Sin cajero fijo
                        </span>
                    @endif
                </td>

                {{-- Estado de Turno en Vivo --}}
                <td class="px-5 py-4 align-middle text-center">
                    @if($turnoActivo)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="size-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                            <span>En Turno ({{ $turnoActivo->cajero ? $turnoActivo->cajero->nombre : 'Cajero' }})</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                            <span class="size-1.5 rounded-full bg-slate-400"></span>
                            <span>Cerrada / Disponible</span>
                        </span>
                    @endif
                </td>

                {{-- Acciones --}}
                <td class="px-5 py-4 align-middle text-right">
                    @if(Auth::user()->tienePermiso('gestionar_caja_diaria'))
                    <div class="flex items-center justify-end gap-1.5">
                        <button onclick='editarCajaModal(@json($caja))' class="size-8 rounded-lg bg-white border border-gray-200 text-gray-600 hover:text-[#1e549e] hover:border-blue-200 hover:bg-blue-50 flex items-center justify-center transition-all shadow-2xs" title="Editar Caja">
                            <span class="material-symbols-outlined text-[17px]">edit</span>
                        </button>
                        <button onclick="eliminarCaja({{ $caja->id }}, '{{ addslashes($caja->nombre) }}')" class="size-8 rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-rose-600 hover:border-rose-200 hover:bg-rose-50 flex items-center justify-center transition-all shadow-2xs" title="Eliminar Caja">
                            <span class="material-symbols-outlined text-[17px]">delete</span>
                        </button>
                    </div>
                    @else
                    <span class="text-xs text-gray-400 italic">Solo lectura</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center">
                        <span class="material-symbols-outlined text-4xl text-gray-300 mb-2">point_of_sale</span>
                        <p class="font-bold text-gray-700 text-sm">No hay cajas registradoras registradas.</p>
                        <p class="text-xs text-gray-400 mt-0.5">Haz clic en "Nueva Caja" para dar de alta una caja física.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
