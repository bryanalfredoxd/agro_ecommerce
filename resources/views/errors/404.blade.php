@extends('errors.layout')

@section('title', 'Página o Recurso No Encontrado')
@section('code', '404')
@section('badge_style', 'bg-sky-500/10 text-sky-400 border-sky-500/30')
@section('badge_dot', 'bg-sky-400')

@section('illustration')
<div class="relative">
    <div class="size-24 sm:size-28 rounded-3xl bg-gradient-to-br from-sky-500/20 via-slate-800 to-slate-900 border border-sky-500/30 flex items-center justify-center shadow-xl shadow-sky-500/10">
        <span class="material-symbols-outlined text-sky-400 text-5xl sm:text-6xl animate-bounce">search_off</span>
    </div>
    <div class="absolute -bottom-2 -right-2 size-9 rounded-xl bg-slate-950 border border-slate-700 flex items-center justify-center text-slate-400 shadow-md">
        <span class="material-symbols-outlined text-[20px]">explore_off</span>
    </div>
</div>
@endsection

@section('message')
{{ $exception?->getMessage() ?: 'El recurso, producto o pedido que intentas consultar no existe en nuestro sistema, fue reubicado o la dirección escrita contiene algún error en su identificador.' }}
@endsection

@section('details')
<div class="flex items-start gap-2">
    <span class="material-symbols-outlined text-sky-400 text-[18px] shrink-0 mt-0.5">tips_and_updates</span>
    <div>
        <p class="font-bold text-slate-200">¿Qué puedes hacer?</p>
        <p class="text-slate-400 mt-0.5">• Si escribiste un número de orden o factura directamente en la barra de direcciones, comprueba que el ID sea correcto.</p>
        <p class="text-slate-400">• Si buscabas un insumo agrícola o veterinario, puedes encontrarlo en nuestro catálogo general o solicitarlo con nosotros.</p>
    </div>
</div>
@endsection

@section('actions')
<a href="{{ route('catalogo') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-heading font-bold text-sm shadow-lg shadow-emerald-600/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
    <span class="material-symbols-outlined text-[18px]">storefront</span>
    <span>Explorar Catálogo</span>
</a>

@auth
    @if(Auth::user()->puedeAccederPanelAdmin())
        <a href="{{ route('admin.dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-heading font-bold text-sm border border-slate-700 transition-all hover:scale-[1.02] active:scale-[0.98]">
            <span class="material-symbols-outlined text-[18px]">{{ Auth::user()->icono_panel ?? 'admin_panel_settings' }}</span>
            <span>Panel Administrativo</span>
        </a>
    @else
        <a href="{{ route('perfil.pedidos') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-heading font-bold text-sm border border-slate-700 transition-all hover:scale-[1.02] active:scale-[0.98]">
            <span class="material-symbols-outlined text-[18px]">receipt_long</span>
            <span>Ver Mis Pedidos</span>
        </a>
    @endif
@else
    <a href="{{ url('/') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-heading font-bold text-sm border border-slate-700 transition-all hover:scale-[1.02] active:scale-[0.98]">
        <span class="material-symbols-outlined text-[18px]">home</span>
        <span>Volver al Inicio</span>
    </a>
@endauth

<button type="button" onclick="window.history.back()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-slate-900/60 hover:bg-slate-800 text-slate-400 hover:text-slate-200 font-heading font-semibold text-xs border border-slate-800 transition-all">
    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
    <span>Volver Atrás</span>
</button>
@endsection
