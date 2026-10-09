@php
    // Solo mostrar splash si no se ha mostrado antes en la sesión
    if (session()->has('splash_shown')) {
        return;
    }
@endphp

<link rel="stylesheet" href="{{ asset('css/splash.css') }}">
<script src="{{ asset('js/splash.js') }}" defer></script>

<div id="splash-screen">
    <div class="splash-content">
        <!-- Logo oficial directo con canal alfa (sin cuadro blanco) -->
        <div class="splash-logo-container">
            <div class="splash-logo-glow"></div>
            <img src="{{ asset('img/logo.png') }}" 
                 alt="Agrosuministros Rodríguez" 
                 class="splash-logo-img splash-logo">
        </div>
        
        <!-- Título corporativo en blanco y azul cielo de alto contraste -->
        <h1 class="splash-title">
            Agrosuministros <span>Rodríguez</span>
        </h1>
        
        <!-- Subtítulo -->
        <p class="splash-subtitle">
            Soluciones integrales para el campo venezolano
        </p>
        
        <!-- Barra de progreso estilizada -->
        <div class="splash-progress-container">
            <div class="splash-progress-bar" id="splash-progress"></div>
        </div>
        
        <!-- Puntos de carga animados -->
        <div class="splash-loading-dots">
            <div class="splash-dot"></div>
            <div class="splash-dot"></div>
            <div class="splash-dot"></div>
        </div>
        
        <div class="splash-progress-text" id="splash-progress-text">
            Iniciando plataforma...
        </div>
    </div>
</div>
