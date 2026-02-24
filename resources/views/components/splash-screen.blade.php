@php
    // Solo mostrar splash si no se ha mostrado antes
    if (session()->has('splash_shown')) {
        return;
    }
@endphp

<link rel="stylesheet" href="{{ asset('css/splash.css') }}">

<script src="{{ asset('js/splash.js') }}" defer></script>

<div id="splash-screen">
    <div class="splash-content">
        <!-- Logo con contenedor circular para fondo blanco -->
        <div class="splash-logo-container">
            <div class="splash-logo-glow"></div>
            <div class="splash-logo-inner">
                <img src="{{ asset('img/ico/Logo0.webp') }}" 
                     alt="Corpo Agricola" 
                     class="splash-logo-img splash-logo">
            </div>
        </div>
        
        <!-- Título -->
        <h1 class="splash-title">
            Corpo<span> Agricola</span>
        </h1>
        
        <!-- Subtítulo -->
        <p class="splash-subtitle">
            Cosechando oportunidades digitales
        </p>
        
        <!-- Barra de progreso -->
        <div class="splash-progress-container">
            <div class="splash-progress-bar" id="splash-progress"></div>
        </div>
        
        <!-- Puntos de carga animados -->
        <div class="splash-loading-dots">
            <div class="splash-dot"></div>
            <div class="splash-dot"></div>
            <div class="splash-dot"></div>
        </div>
        
        <!-- Texto de progreso (opcional) -->
        <div class="splash-progress-text" id="splash-progress-text">
            Cargando recursos...
        </div>
    </div>
</div>

