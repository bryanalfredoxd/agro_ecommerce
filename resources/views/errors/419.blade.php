@extends('errors.layout')

@section('title', 'Sesión o Página Expirada')
@section('code', '419')
@section('badge_style', 'bg-amber-500/10 text-amber-400 border-amber-500/30')
@section('badge_dot', 'bg-amber-400')

@section('illustration')
<div class="relative">
    <div class="size-24 sm:size-28 rounded-3xl bg-gradient-to-br from-amber-500/20 via-slate-800 to-slate-900 border border-amber-500/30 flex items-center justify-center shadow-xl shadow-amber-500/10">
        <span class="material-symbols-outlined text-amber-400 text-5xl sm:text-6xl animate-spin" style="animation-duration: 8s;">history_toggle_off</span>
    </div>
    <div class="absolute -bottom-2 -right-2 size-9 rounded-xl bg-slate-950 border border-slate-700 flex items-center justify-center text-amber-400 shadow-md">
        <span class="material-symbols-outlined text-[20px]">hourglass_empty</span>
    </div>
</div>
@endsection

@section('message')
{{ $exception?->getMessage() ?: 'Tu sesión o el token de seguridad ha caducado por inactividad. Esta medida protege tus datos confidenciales impidiendo envíos de formularios duplicados o antiguos.' }}
@endsection

@section('details')
<div class="flex items-start gap-2">
    <span class="material-symbols-outlined text-amber-400 text-[18px] shrink-0 mt-0.5">security_update_good</span>
    <div>
        <p class="font-bold text-slate-200">¿Cómo continuar?</p>
        <p class="text-slate-400 mt-0.5">Haz clic en <strong>"Recargar y Continuar"</strong> para renovar automáticamente tu clave de seguridad. Si estabas realizando un pago o enviando un formulario, podrás intentarlo de nuevo inmediatamente.</p>
    </div>
</div>
@endsection

@section('actions')
<button type="button" onclick="window.location.reload()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-heading font-black text-sm shadow-lg shadow-amber-500/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
    <span class="material-symbols-outlined text-[20px]">refresh</span>
    <span>Recargar y Continuar</span>
</button>

<a href="{{ url('/') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-heading font-bold text-sm border border-slate-700 transition-all hover:scale-[1.02] active:scale-[0.98]">
    <span class="material-symbols-outlined text-[18px]">home</span>
    <span>Página Principal</span>
</a>

<a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-slate-900/60 hover:bg-slate-800 text-slate-300 hover:text-white font-heading font-semibold text-xs border border-slate-800 transition-all">
    <span class="material-symbols-outlined text-[16px]">login</span>
    <span>Iniciar Sesión</span>
</a>
@endsection
