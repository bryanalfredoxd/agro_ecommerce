@extends('errors.layout')

@section('title', 'Demasiadas Solicitudes')
@section('code', '429')
@section('badge_style', 'bg-orange-500/10 text-orange-400 border-orange-500/30')
@section('badge_dot', 'bg-orange-400')

@section('illustration')
<div class="relative">
    <div class="size-24 sm:size-28 rounded-3xl bg-gradient-to-br from-orange-500/20 via-slate-800 to-slate-900 border border-orange-500/30 flex items-center justify-center shadow-xl shadow-orange-500/10">
        <span class="material-symbols-outlined text-orange-400 text-5xl sm:text-6xl animate-pulse">speed</span>
    </div>
    <div class="absolute -bottom-2 -right-2 size-9 rounded-xl bg-slate-950 border border-slate-700 flex items-center justify-center text-orange-400 shadow-md">
        <span class="material-symbols-outlined text-[20px]">timer</span>
    </div>
</div>
@endsection

@section('message')
{{ $exception?->getMessage() ?: 'Has realizado demasiadas solicitudes en un corto lapso de tiempo. Para garantizar la estabilidad del servicio, hemos pausado temporalmente tus peticiones.' }}
@endsection

@section('details')
<div class="flex items-start gap-2">
    <span class="material-symbols-outlined text-orange-400 text-[18px] shrink-0 mt-0.5">info</span>
    <div>
        <p class="font-bold text-slate-200">Protección del Servidor</p>
        <p class="text-slate-400 mt-0.5">Por favor espera aproximadamente 30 a 60 segundos antes de recargar o enviar una nueva solicitud.</p>
    </div>
</div>
@endsection

@section('actions')
<button type="button" onclick="window.location.reload()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 hover:to-orange-500 text-slate-950 font-heading font-black text-sm shadow-lg shadow-orange-500/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
    <span class="material-symbols-outlined text-[20px]">refresh</span>
    <span>Intentar Nuevamente</span>
</button>

<a href="{{ url('/') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-heading font-bold text-sm border border-slate-700 transition-all hover:scale-[1.02] active:scale-[0.98]">
    <span class="material-symbols-outlined text-[18px]">home</span>
    <span>Página Principal</span>
</a>
@endsection
