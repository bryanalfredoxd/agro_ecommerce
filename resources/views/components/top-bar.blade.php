@php
    use App\Models\TasaCambio;
    use App\Models\HorarioSemanal;
    
    // Obtener la tasa de cambio
    $tasaDolar = TasaCambio::obtenerValorUSD();
    
    // Obtener horario de hoy
    $horarioHoy = HorarioSemanal::obtenerHorarioHoyFormateado();
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
                
                <div class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-primary">
                        local_shipping
                    </span>
                    <span class="font-medium whitespace-nowrap">
                        <span class="inline sm:hidden">Envíos</span> <span class="hidden sm:inline">Envíos Nacionales</span> </span>
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
                
                <div class="flex items-center gap-1.5 text-gray-300 cursor-default" title="Horario de atención">
                    <span class="material-symbols-outlined text-[16px]">schedule</span>
                    <span class="font-medium">{{ $horarioHoy }}</span>
                </div>
            </div>
            
        </div>
    </div>
</div>