@extends('errors.layout')

@section('title', 'Inconveniente en el Servidor')
@section('code', '500')
@section('badge_style', 'bg-rose-500/10 text-rose-400 border-rose-500/30')
@section('badge_dot', 'bg-rose-400')

@section('illustration')
<div class="relative">
    <div class="size-24 sm:size-28 rounded-3xl bg-gradient-to-br from-rose-500/20 via-slate-800 to-slate-900 border border-rose-500/30 flex items-center justify-center shadow-xl shadow-rose-500/10">
        <span class="material-symbols-outlined text-rose-400 text-5xl sm:text-6xl animate-pulse">cloud_off</span>
    </div>
    <div class="absolute -bottom-2 -right-2 size-9 rounded-xl bg-slate-950 border border-slate-700 flex items-center justify-center text-amber-400 shadow-md">
        <span class="material-symbols-outlined text-[20px]">engineering</span>
    </div>
</div>
@endsection

@section('message')
{{ config('app.debug') && $exception?->getMessage() ? $exception->getMessage() : 'Ocurrió un inconveniente técnico inesperado al procesar tu solicitud en el servidor. Tus transacciones y datos se encuentran completamente resguardados.' }}
@endsection

@section('details')
<div class="space-y-1.5">
    <div class="flex items-start gap-2">
        <span class="material-symbols-outlined text-emerald-400 text-[18px] shrink-0 mt-0.5">check_circle</span>
        <p class="text-slate-300">Tus pedidos e información financiera previa están seguros en el sistema.</p>
    </div>
    <div class="flex items-start gap-2">
        <span class="material-symbols-outlined text-sky-400 text-[18px] shrink-0 mt-0.5">build_circle</span>
        <p class="text-slate-400">Puedes intentar recargar la página en unos segundos o ponerte en contacto con nuestro equipo si estabas en medio de una orden urgente.</p>
    </div>
</div>
@endsection

@section('actions')
<button type="button" onclick="window.location.reload()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-heading font-bold text-sm shadow-lg shadow-emerald-600/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
    <span class="material-symbols-outlined text-[18px]">refresh</span>
    <span>Reintentar Operación</span>
</button>

<a href="{{ url('/') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-heading font-bold text-sm border border-slate-700 transition-all hover:scale-[1.02] active:scale-[0.98]">
    <span class="material-symbols-outlined text-[18px]">home</span>
    <span>Página Principal</span>
</a>

<a href="https://wa.me/584120000000" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-slate-900/60 hover:bg-slate-800 text-emerald-400 hover:text-emerald-300 font-heading font-semibold text-xs border border-slate-800 transition-all">
    <span class="material-symbols-outlined text-[16px]">support_agent</span>
    <span>Atención por WhatsApp</span>
</a>
@endsection
