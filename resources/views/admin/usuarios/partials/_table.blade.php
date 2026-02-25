<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50/50">
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Usuario</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Documento</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Rol Asignado</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Registro</th>
                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Accion</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($usuarios as $user)
            <tr class="hover:bg-gray-50/50 transition-colors group">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-black shadow-sm">
                            {{ substr($user->nombre, 0, 1) }}{{ substr($user->apellido, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-bold text-agro-dark capitalize">{{ $user->nombre }} {{ $user->apellido }}</p>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="font-mono text-sm text-gray-700">{{ $user->documento_identidad ?? 'N/A' }}</span>
                </td>
                <td class="px-6 py-4">
                    {{-- Solo mostramos el ROL --}}
                    <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-700 text-[11px] font-bold rounded-lg border border-indigo-100 uppercase tracking-wider">
                        {{ $user->rol ? $user->rol->nombre : 'Sin Rol' }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <p class="text-sm font-medium text-gray-700">{{ \Carbon\Carbon::parse($user->creado_at)->format('d M Y') }}</p>
                </td>
                <td class="px-6 py-4 text-center">
                    @if($user->id != 1) 
                        <button onclick="openPermisosModal({{ $user->id }}, '{{ $user->nombre }} {{ $user->apellido }}')" 
                                class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700 text-gray-600 rounded-xl transition-all shadow-sm text-xs font-bold">
                            <span class="material-symbols-outlined text-[16px]">manage_accounts</span>
                            Configurar
                        </button>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                    <span class="material-symbols-outlined text-4xl mb-2 text-gray-300">search_off</span>
                    <p class="font-bold">No se encontraron usuarios</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="px-6 py-4 border-t border-gray-100 flex justify-center ajax-pagination">
    {{ $usuarios->links('pagination::tailwind') }}
</div>