@extends('errors.layout')

@section('title', 'Acceso Denegado o No Autorizado')
@section('code', '403')
@section('badge_style', 'bg-rose-500/10 text-rose-400 border-rose-500/30')
@section('badge_dot', 'bg-rose-400')

@section('illustration')
<div class="relative">
    <div class="size-24 sm:size-28 rounded-3xl bg-gradient-to-br from-rose-500/20 via-slate-800 to-slate-900 border border-rose-500/30 flex items-center justify-center shadow-xl shadow-rose-500/10">
        <span class="material-symbols-outlined text-rose-400 text-5xl sm:text-6xl animate-pulse">shield_lock</span>
    </div>
    <div class="absolute -bottom-2 -right-2 size-9 rounded-xl bg-slate-950 border border-slate-700 flex items-center justify-center text-rose-400 shadow-md">
        <span class="material-symbols-outlined text-[20px]">lock</span>
    </div>
</div>
@endsection

@section('message')
{{ $exception?->getMessage() ?: 'No cuentas con los privilegios suficientes para ingresar a esta sección o consultar la información del pedido solicitado.' }}
@endsection

@section('details')
<div class="space-y-2">
    <div class="flex items-start gap-2">
        <span class="material-symbols-outlined text-rose-400 text-[18px] shrink-0 mt-0.5">verified_user</span>
        <div>
            <p class="font-bold text-slate-200">Seguridad & Privacidad de Pedidos</p>
            <p class="text-slate-400 mt-0.5">Cada cliente únicamente puede consultar sus propias órdenes de compra, tickets y facturas. Los pedidos ajenos están protegidos contra accesos no autorizados.</p>
        </div>
    </div>
    
    @auth
        @if(Auth::user()->esPersonal())
        <div class="pt-2 border-t border-slate-800/80 flex items-start gap-2">
            <span class="material-symbols-outlined text-amber-400 text-[18px] shrink-0 mt-0.5">admin_panel_settings</span>
            <div>
                <p class="font-bold text-slate-200">Control de Roles Operativos</p>
                <p class="text-slate-400 mt-0.5">Estás conectado con el rol <strong class="text-white capitalize">{{ Auth::user()->rol->nombre ?? 'Personal' }}</strong>. Para tener acceso a este módulo o estados específicos, el Administrador debe asignarte el permiso correspondiente desde la configuración de roles.</p>
            </div>
        </div>
        @endif
    @endauth
</div>
@endsection

@section('actions')
@auth
    @if(Auth::user()->puedeAccederPanelAdmin())
        <a href="{{ Auth::user()->ruta_panel ?? route('admin.dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-heading font-bold text-sm shadow-lg shadow-emerald-600/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
            <span class="material-symbols-outlined text-[18px]">{{ Auth::user()->icono_panel ?? 'space_dashboard' }}</span>
            <span>Ir a Mi Panel de Trabajo</span>
        </a>
    @else
        <a href="{{ route('perfil.pedidos') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-heading font-bold text-sm shadow-lg shadow-emerald-600/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
            <span class="material-symbols-outlined text-[18px]">receipt_long</span>
            <span>Consultar Mis Pedidos</span>
        </a>
    @endif
@else
    <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-heading font-bold text-sm shadow-lg shadow-emerald-600/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
        <span class="material-symbols-outlined text-[18px]">login</span>
        <span>Iniciar Sesión con Mi Cuenta</span>
    </a>
@endauth

<a href="{{ url('/') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-heading font-bold text-sm border border-slate-700 transition-all hover:scale-[1.02] active:scale-[0.98]">
    <span class="material-symbols-outlined text-[18px]">home</span>
    <span>Página Principal</span>
</a>

<button type="button" onclick="window.history.back()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-slate-900/60 hover:bg-slate-800 text-slate-400 hover:text-slate-200 font-heading font-semibold text-xs border border-slate-800 transition-all">
    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
    <span>Volver Atrás</span>
</button>
@endsection
