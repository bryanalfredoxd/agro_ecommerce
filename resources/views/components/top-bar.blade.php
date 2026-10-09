@php
    use App\Models\TasaCambio;
    use App\Models\HorarioSemanal;
    
    // Obtener la tasa de cambio
    $tasaDolar = TasaCambio::obtenerValorUSD();
    
    // Obtener estado del horario
    $estadoHorario = HorarioSemanal::obtenerEstadoHorarioHoy();
    $textoHorario = $estadoHorario['mostrar'];
    
    // Determinar clase de color según estado
    $claseHorario = 'text-slate-200';
    $iconoHorario = 'schedule';
    
    switch($estadoHorario['estado']) {
        case 'abierto':
            $claseHorario = 'text-emerald-300';
            $iconoHorario = 'schedule';
            break;
        case 'antes_horario':
            $claseHorario = 'text-amber-300';
            $iconoHorario = 'lock_clock';
            break;
        case 'despues_horario':
            $claseHorario = 'text-rose-300';
            $iconoHorario = 'lock';
            break;
        case 'no_laborable':
            $claseHorario = 'text-rose-400';
            $iconoHorario = 'event_busy';
            break;
        case 'sin_horario_dia':
        case 'sin_horario':
            $claseHorario = 'text-amber-300';
            $iconoHorario = 'info';
            break;
    }
@endphp

<div class="bg-[#1e549e] text-white w-full border-b border-white/10 text-[11px] sm:text-xs">
    <div class="layout-container">
        <div class="flex justify-between items-center h-[36px] sm:h-[38px]">
            
            <div class="flex items-center justify-between w-full sm:w-auto sm:justify-start gap-4">
                
                <div class="flex items-center gap-1.5 group cursor-help" title="Tasa oficial BCV actualizada">
                    <span class="material-symbols-outlined text-[16px] text-[#aad6f3]">
                        currency_exchange
                    </span>
                    <span class="font-medium whitespace-nowrap flex items-center gap-1">
                        <span class="text-[#aad6f3] font-bold">Tasa BCV:</span> 
                        <span class="text-white/80">1 USD =</span>
                        <span class="font-bold text-white bg-white/10 px-1.5 py-0.5 rounded-md">{{ $tasaDolar }}</span> 
                        <span class="text-white/80">VES</span>
                    </span>
                </div>
                
                <div class="h-3 w-px bg-white/20 sm:hidden"></div>
                
                <!-- Para móvil: muestra el horario -->
                <div class="flex items-center gap-1.5 sm:hidden {{ $claseHorario }}">
                    <span class="material-symbols-outlined text-[15px]">{{ $iconoHorario }}</span>
                    <span class="font-medium whitespace-nowrap">{{ $textoHorario }}</span>
                </div>
            </div>
            
            <div class="hidden sm:flex items-center gap-5">
                <a href="tel:+584129614615" class="flex items-center gap-1.5 hover:text-[#aad6f3] transition-colors group">
                    <span class="material-symbols-outlined text-[16px] text-[#aad6f3]">call</span>
                    <span class="font-medium">+58 412-9614615</span>
                </a>
                
                <div class="h-3 w-px bg-white/20"></div>
                
                <div class="flex items-center gap-1.5 text-white/90">
                    <span class="material-symbols-outlined text-[16px] text-[#aad6f3]">
                        local_shipping
                    </span>
                    <span class="font-medium whitespace-nowrap">Despachos a Nivel Nacional</span>
                </div>
                
                <div class="h-3 w-px bg-white/20"></div>
                
                <!-- Para desktop: muestra el horario -->
                <div class="flex items-center gap-1.5 {{ $claseHorario }}" title="Horario de atención">
                    <span class="material-symbols-outlined text-[16px]">{{ $iconoHorario }}</span>
                    <span class="font-medium">{{ $textoHorario }}</span>
                </div>
            </div>
            
        </div>
    </div>
</div>