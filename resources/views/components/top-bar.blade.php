@php
    use App\Models\TasaCambio;
    use App\Models\HorarioSemanal;
    
    // Obtener la tasa de cambio
    $tasaDolar = TasaCambio::obtenerValorUSD();
    
    // Obtener estado del horario
    $estadoHorario = HorarioSemanal::obtenerEstadoHorarioHoy();
    $textoHorario = $estadoHorario['mostrar']; // Usamos el campo 'mostrar' que ya tiene el texto correcto
    
    // Determinar clase de color según estado
    $claseHorario = 'text-gray-300'; // Por defecto
    $iconoHorario = 'schedule'; // Por defecto
    
    switch($estadoHorario['estado']) {
        case 'abierto':
            $claseHorario = 'text-green-400';
            $iconoHorario = 'schedule';
            break;
        case 'antes_horario':
            $claseHorario = 'text-yellow-400';
            $iconoHorario = 'lock_clock';
            break;
        case 'despues_horario':
            $claseHorario = 'text-red-400';
            $iconoHorario = 'lock';
            break;
        case 'no_laborable':
            $claseHorario = 'text-red-500';
            $iconoHorario = 'event_busy';
            break;
        case 'sin_horario_dia':
        case 'sin_horario':
            $claseHorario = 'text-orange-400';
            $iconoHorario = 'info';
            break;
    }
@endphp

<div class="bg-agro-dark text-white w-full border-b border-white/10 text-[11px] sm:text-xs md:text-sm">
    <div class="layout-container">
        <div class="flex justify-between items-center h-[36px] sm:h-[40px]">
            
            <div class="flex items-center justify-between w-full sm:w-auto sm:justify-start gap-4">
                
                <div class="flex items-center gap-1.5 group cursor-help" title="Tasa BCV actualizada">
                    <span class="material-symbols-outlined text-[16px] text-primary animate-pulse">
                        currency_exchange
                    </span>
                    <span class="font-medium whitespace-nowrap flex items-center gap-1">
                        <span class="text-primary font-bold">USD</span> 
                        <span class="text-gray-400">→</span>
                        <span class="font-bold text-white">{{ $tasaDolar }}</span> 
                        <span class="text-gray-400">Bs</span>
                    </span>
                </div>
                
                <div class="h-3 w-px bg-white/20 sm:hidden"></div>
                
                <!-- Para móvil: muestra el horario -->
                <div class="flex items-center gap-1.5 sm:hidden {{ $claseHorario }}">
                    <span class="material-symbols-outlined text-[16px]">{{ $iconoHorario }}</span>
                    <span class="font-medium whitespace-nowrap">{{ $textoHorario }}</span>
                </div>
            </div>
            
            <div class="hidden sm:flex items-center gap-6">
                <a href="tel:+584241234567" class="flex items-center gap-1.5 hover:text-primary transition-colors group">
                    <span class="material-symbols-outlined text-[16px]">phone</span>
                    <span class="font-medium group-hover:underline">(0424) 123-4567</span>
                </a>
                
                <div class="h-3 w-px bg-white/20"></div>
                
                <a href="#" class="flex items-center gap-1.5 hover:text-primary transition-colors group">
                    <span class="material-symbols-outlined text-[16px]">support_agent</span>
                    <span class="font-medium group-hover:underline">Soporte</span>
                </a>
                
                <div class="h-3 w-px bg-white/20"></div>
                
                <!-- Para desktop: muestra "Envíos Nacionales" -->
                <div class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-primary">
                        local_shipping
                    </span>
                    <span class="font-medium whitespace-nowrap">Envíos Nacionales</span>
                </div>
                
                <div class="h-3 w-px bg-white/20"></div>
                
                <!-- Para desktop: muestra el horario -->
                <div class="flex items-center gap-1.5 {{ $claseHorario }} cursor-default" title="Horario de atención">
                    <span class="material-symbols-outlined text-[16px]">{{ $iconoHorario }}</span>
                    <span class="font-medium">{{ $textoHorario }}</span>
                </div>
            </div>
            
        </div>
    </div>
</div>