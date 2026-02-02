@php
    // Solo mostrar splash si no se ha mostrado antes
    if (session()->has('splash_shown')) {
        return;
    }
    
    // Marcar que se mostró el splash (con JavaScript para mejor control)
@endphp

<link rel="stylesheet" href="{{ asset('css/splash.css') }}">

<div id="splash-screen">
    <div class="splash-content">
        <!-- Logo con contenedor circular para fondo blanco -->
        <div class="splash-logo-container">
            <div class="splash-logo-glow"></div>
            <div class="splash-logo-inner">
                <img src="{{ asset('img/ico/logo0.webp') }}" 
                     alt="Corpo Agricola" 
                     class="splash-logo-img">
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
    </div>
</div>

<script>
// Ejecutar inmediatamente (sin esperar DOMContentLoaded)
(function() {
    'use strict';
    
    // Verificar si ya se mostró el splash
    const splashShown = sessionStorage.getItem('splash_shown');
    
    if (splashShown === 'true') {
        // Si ya se mostró, no hacer nada
        return;
    }
    
    // Marcar que se va a mostrar el splash
    document.body.classList.add('splash-active');
    
    // Inicializar elementos
    const splashScreen = document.getElementById('splash-screen');
    const progressBar = document.getElementById('splash-progress');
    
    if (!splashScreen) return;
    
    // Ocultar scroll del body
    document.body.style.overflow = 'hidden';
    
    // Función para iniciar el progreso
    function startProgress() {
        if (progressBar) {
            progressBar.style.width = '100%';
        }
    }
    
    // Función para ocultar el splash
    function hideSplash() {
        // Marcar como mostrado en sessionStorage
        sessionStorage.setItem('splash_shown', 'true');
        
        // Ocultar el splash
        splashScreen.classList.add('hidden');
        
        // Remover clase del body después de la animación
        setTimeout(() => {
            document.body.classList.remove('splash-active');
            document.body.style.overflow = '';
            
            // Remover completamente del DOM
            splashScreen.style.display = 'none';
            
            // Opcional: enviar al servidor que se mostró
            fetch('/splash/mark-shown', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ shown: true })
            }).catch(() => {
                // Silenciar errores si falla la petición
            });
        }, 800);
    }
    
    // Función para saltar el splash
    function skipSplash() {
        splashScreen.classList.add('fast-exit');
        hideSplash();
    }
    
    // Permitir saltar con click
    splashScreen.addEventListener('click', skipSplash);
    
    // Permitir saltar con tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.key === ' ') {
            skipSplash();
        }
    });
    
    // Iniciar progreso después de un breve delay
    setTimeout(startProgress, 100);
    
    // Configurar tiempo total del splash (5 segundos)
    setTimeout(hideSplash, 5000);
    
})();
</script>