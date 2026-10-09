{{-- resources/views/perfil/partials/sidebar.blade.php --}}
@php
    $active = $active ?? 'perfil';
    $user = auth()->user();
    $iniciales = strtoupper(substr($user->nombre ?? 'U', 0, 1) . substr($user->apellido ?? '', 0, 1));
    $tipoBadge = match($user->tipo_cliente) {
        'juridico' => ['texto' => 'Empresa Jurídica', 'icono' => 'domain', 'clase' => 'text-[#1e549e] bg-[#e5f2fb] border-[#aad6f3]/60'],
        'finca_productor' => ['texto' => 'Productor / Finca', 'icono' => 'agriculture', 'clase' => 'text-emerald-700 bg-emerald-50 border-emerald-200'],
        default => ['texto' => 'Cliente Particular', 'icono' => 'person', 'clase' => 'text-slate-700 bg-slate-100 border-slate-200']
    };
    $totalPedidos = $user->pedidos()->count();
    $totalDirecciones = $user->direcciones()->count();
    $totalFavoritos = $user->favoritos()->count();
@endphp

<aside class="w-full lg:w-80 xl:w-84 shrink-0">
    <div class="bg-white rounded-3xl shadow-xs border border-slate-200/90 overflow-hidden lg:sticky lg:top-24 transition-all">
        
        {{-- Tarjeta de Identidad del Usuario --}}
        <div class="p-6 text-center relative overflow-hidden bg-gradient-to-b from-[#e5f2fb]/40 via-white to-white border-b border-slate-100">
            <div class="absolute -top-12 -right-12 size-32 bg-[#3492cf]/10 rounded-full blur-2xl pointer-events-none"></div>

            {{-- Avatar con Anillo Gradiente --}}
            <div class="relative size-24 mx-auto mb-3.5">
                <div class="size-full rounded-full bg-gradient-to-tr from-[#1e549e] to-[#3492cf] p-1 shadow-md shadow-[#1e549e]/15">
                    <div class="size-full rounded-full bg-white flex items-center justify-center text-[#1e549e] font-black text-2xl tracking-tight">
                        {{ $iniciales ?: 'AG' }}
                    </div>
                </div>
                <div class="absolute bottom-0.5 right-0.5 size-6 rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center text-white shadow-2xs" title="Cuenta Activa">
                    <span class="material-symbols-outlined text-[13px] font-bold">check</span>
                </div>
            </div>

            {{-- Nombre y Correo --}}
            <h2 class="text-lg font-black text-slate-900 truncate leading-tight">
                {{ $user->nombre }} {{ $user->apellido }}
            </h2>
            <p class="text-xs text-slate-500 font-medium truncate mt-0.5 mb-3">{{ $user->email }}</p>

            {{-- Badge de Tipo de Cliente --}}
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider border {{ $tipoBadge['clase'] }}">
                <span class="material-symbols-outlined text-[15px]">{{ $tipoBadge['icono'] }}</span>
                <span>{{ $tipoBadge['texto'] }}</span>
            </div>

            {{-- Métricas Rápidas (3 Mini-Pills) --}}
            <div class="grid grid-cols-3 gap-2 mt-5 pt-4 border-t border-slate-100/80">
                <a href="{{ route('perfil.pedidos') }}" class="group/stat p-2 rounded-2xl bg-slate-50 hover:bg-[#e5f2fb] transition-colors border border-slate-100 text-center">
                    <span class="block text-base font-black text-slate-900 group-hover/stat:text-[#1e549e] leading-tight">{{ $totalPedidos }}</span>
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Pedidos</span>
                </a>
                <a href="{{ route('perfil') }}#direcciones" class="group/stat p-2 rounded-2xl bg-slate-50 hover:bg-[#e5f2fb] transition-colors border border-slate-100 text-center">
                    <span class="block text-base font-black text-slate-900 group-hover/stat:text-[#1e549e] leading-tight">{{ $totalDirecciones }}</span>
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Ubicaciones</span>
                </a>
                <a href="{{ route('perfil.favoritos') }}" class="group/stat p-2 rounded-2xl bg-slate-50 hover:bg-[#e5f2fb] transition-colors border border-slate-100 text-center">
                    <span class="block text-base font-black text-slate-900 group-hover/stat:text-[#1e549e] leading-tight">{{ $totalFavoritos }}</span>
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Deseos</span>
                </a>
            </div>
        </div>

        {{-- Menú de Navegación Lateral --}}
        <div class="p-3 bg-white">
            <nav class="space-y-1">
                
                {{-- Información Personal --}}
                <a href="{{ route('perfil') }}#datos-personales" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl font-bold text-xs transition-all group {{ $active === 'perfil' ? 'bg-[#e5f2fb] text-[#1e549e] shadow-2xs font-black' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <div class="flex items-center gap-2.5 min-w-0 truncate">
                        <div class="size-8 rounded-xl flex items-center justify-center shrink-0 {{ $active === 'perfil' ? 'bg-white text-[#1e549e] shadow-2xs' : 'bg-slate-100 text-slate-500 group-hover:text-[#1e549e] group-hover:bg-[#e5f2fb]' }}">
                            <span class="material-symbols-outlined text-[18px]">badge</span>
                        </div>
                        <span class="truncate">Información Personal</span>
                    </div>
                    @if($active === 'perfil')
                        <span class="material-symbols-outlined text-[16px] text-[#1e549e]">chevron_right</span>
                    @endif
                </a>

                {{-- Direcciones de Envío --}}
                <a href="{{ route('perfil') }}#direcciones" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl font-bold text-xs transition-all group {{ $active === 'direcciones' ? 'bg-[#e5f2fb] text-[#1e549e] shadow-2xs font-black' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <div class="flex items-center gap-2.5 min-w-0 truncate">
                        <div class="size-8 rounded-xl flex items-center justify-center shrink-0 {{ $active === 'direcciones' ? 'bg-white text-[#1e549e] shadow-2xs' : 'bg-slate-100 text-slate-500 group-hover:text-[#1e549e] group-hover:bg-[#e5f2fb]' }}">
                            <span class="material-symbols-outlined text-[18px]">location_on</span>
                        </div>
                        <span class="truncate">Mis Direcciones</span>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $active === 'direcciones' ? 'bg-[#1e549e] text-white' : 'bg-slate-100 text-slate-500' }}">
                        {{ $totalDirecciones }}
                    </span>
                </a>

                {{-- Seguridad y Contraseña --}}
                <a href="{{ route('perfil') }}#seguridad" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl font-bold text-xs transition-all group {{ $active === 'seguridad' ? 'bg-[#e5f2fb] text-[#1e549e] shadow-2xs font-black' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <div class="flex items-center gap-2.5 min-w-0 truncate">
                        <div class="size-8 rounded-xl flex items-center justify-center shrink-0 {{ $active === 'seguridad' ? 'bg-white text-[#1e549e] shadow-2xs' : 'bg-slate-100 text-slate-500 group-hover:text-[#1e549e] group-hover:bg-[#e5f2fb]' }}">
                            <span class="material-symbols-outlined text-[18px]">shield</span>
                        </div>
                        <span class="truncate">Seguridad y Clave</span>
                    </div>
                    @if($active === 'seguridad')
                        <span class="material-symbols-outlined text-[16px] text-[#1e549e]">chevron_right</span>
                    @endif
                </a>

                <div class="h-px bg-slate-100 my-2 mx-2"></div>

                {{-- Historial de Pedidos --}}
                <a href="{{ route('perfil.pedidos') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl font-bold text-xs transition-all group {{ $active === 'pedidos' ? 'bg-[#e5f2fb] text-[#1e549e] shadow-2xs font-black' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <div class="flex items-center gap-2.5 min-w-0 truncate">
                        <div class="size-8 rounded-xl flex items-center justify-center shrink-0 {{ $active === 'pedidos' ? 'bg-white text-[#1e549e] shadow-2xs' : 'bg-slate-100 text-slate-500 group-hover:text-[#1e549e] group-hover:bg-[#e5f2fb]' }}">
                            <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                        </div>
                        <span class="truncate">Historial de Pedidos</span>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $active === 'pedidos' ? 'bg-[#1e549e] text-white' : 'bg-slate-100 text-slate-500' }}">
                        {{ $totalPedidos }}
                    </span>
                </a>

                {{-- Mis Favoritos --}}
                <a href="{{ route('perfil.favoritos') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl font-bold text-xs transition-all group {{ $active === 'favoritos' ? 'bg-[#e5f2fb] text-[#1e549e] shadow-2xs font-black' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <div class="flex items-center gap-2.5 min-w-0 truncate">
                        <div class="size-8 rounded-xl flex items-center justify-center shrink-0 {{ $active === 'favoritos' ? 'bg-white text-rose-600 shadow-2xs' : 'bg-rose-50 text-rose-500' }}">
                            <span class="material-symbols-outlined text-[18px] fill-1">favorite</span>
                        </div>
                        <span class="truncate">Lista de Deseos</span>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $active === 'favoritos' ? 'bg-[#1e549e] text-white' : 'bg-slate-100 text-slate-500' }}">
                        {{ $totalFavoritos }}
                    </span>
                </a>

                <div class="h-px bg-slate-100 my-2 mx-2"></div>

                {{-- Cerrar Sesión --}}
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2.5 px-3.5 py-2.5 rounded-2xl font-bold text-xs text-rose-600 hover:bg-rose-50 transition-colors group cursor-pointer">
                        <div class="size-8 rounded-xl bg-rose-50 group-hover:bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">logout</span>
                        </div>
                        <span>Cerrar Sesión</span>
                    </button>
                </form>

            </nav>
        </div>

    </div>
</aside>
