<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50/50">
                <th class="px-6 py-4 text-[10px] font-black text-gray-700 uppercase tracking-widest w-16">Estado</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-700 uppercase tracking-widest">Tipo / Entidad</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-700 uppercase tracking-widest">Datos de Recepción</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-700 uppercase tracking-widest text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($cuentas as $cuenta)
            <tr class="hover:bg-gray-50/50 transition-colors group {{ !$cuenta->activo ? 'opacity-60 bg-gray-50' : '' }}">
                
                {{-- Columna 1: Toggle Switch --}}
                <td class="px-6 py-4">
                    <label class="relative inline-flex items-center cursor-pointer" title="{{ $cuenta->activo ? 'Desactivar' : 'Activar' }}">
                        <input type="checkbox" class="sr-only peer" {{ $cuenta->activo ? 'checked' : '' }} onchange="toggleCuenta({{ $cuenta->id }})">
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
                    </label>
                </td>

                {{-- Columna 2: Tipo de Método --}}
                <td class="px-6 py-4">
                    @php
                        $metodoBadge = match($cuenta->tipo_metodo) {
                            'pago_movil' => 'bg-purple-50 text-purple-700 border-purple-200',
                            'zelle' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                            'binance' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                            'transferencia' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'efectivo_usd', 'efectivo_bs' => 'bg-green-50 text-green-700 border-green-200',
                            default => 'bg-gray-50 text-gray-700 border-gray-200',
                        };
                    @endphp
                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider border {{ $metodoBadge }}">
                        {{ str_replace('_', ' ', $cuenta->tipo_metodo) }}
                    </span>
                    @if($cuenta->banco_entidad)
                        <p class="text-sm font-bold text-agro-dark mt-1">{{ $cuenta->banco_entidad }}</p>
                    @endif
                </td>

                {{-- Columna 3: Datos Específicos --}}
                <td class="px-6 py-4">
                    @if($cuenta->nombre_titular)
                        <p class="text-xs font-bold text-gray-800 capitalize">{{ $cuenta->nombre_titular }}</p>
                    @endif
                    
                    <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1 text-[11px] text-gray-500">
                        @if($cuenta->telefono) <span class="font-mono"><span class="font-bold text-gray-700">TEL:</span> {{ $cuenta->telefono }}</span> @endif
                        @if($cuenta->identificacion) <span class="font-mono"><span class="font-bold text-gray-700">CI/RIF:</span> {{ $cuenta->identificacion }}</span> @endif
                        @if($cuenta->email) <span class="font-medium"><span class="font-bold text-gray-700">EMAIL:</span> {{ $cuenta->email }}</span> @endif
                        @if($cuenta->numero_cuenta) <span class="font-mono"><span class="font-bold text-gray-700">CTA:</span> {{ $cuenta->numero_cuenta }}</span> @endif
                    </div>
                </td>

                {{-- Columna 4: Acciones --}}
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <button onclick='openModal(@json($cuenta))' class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 flex items-center justify-center transition-all shadow-sm">
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                        </button>
                        <button onclick="deleteCuenta({{ $cuenta->id }})" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-red-600 hover:border-red-200 hover:bg-red-50 flex items-center justify-center transition-all shadow-sm">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                    <span class="material-symbols-outlined text-4xl mb-2 text-gray-300">account_balance_wallet</span>
                    <p class="font-bold">No hay métodos de pago configurados</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="px-6 py-4 border-t border-gray-100 flex justify-center ajax-pagination">
    {{ $cuentas->links('pagination::tailwind') }}
</div>