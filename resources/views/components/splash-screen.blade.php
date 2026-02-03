@php
    // Solo mostrar splash si no se ha mostrado antes
    if (session()->has('splash_shown')) {
        return;
    }
@endphp

<link rel="stylesheet" href="{{ asset('css/splash.css') }}">

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
    const progressText = document.getElementById('splash-progress-text');
    
    if (!splashScreen) return;
    
    // Ocultar scroll del body
    document.body.style.overflow = 'hidden';
    
    // Variables para el progreso
    let totalResources = 0;
    let loadedResources = 0;
    let minDisplayTime = 1500; // Mínimo 1.5 segundos
    let maxDisplayTime = 8000; // Máximo 8 segundos (como fallback)
    let startTime = Date.now();
    let hasLoaded = false;
    
    // Recursos críticos que deben cargarse
    const criticalResources = [
        '{{ asset("img/ico/Logo0.webp") }}',
        // Agrega aquí otros recursos críticos si es necesario
    ];
    
    // Función para actualizar la barra de progreso
    function updateProgress(resourceType = '') {
        loadedResources++;
        
        // Calcular porcentaje basado en recursos cargados
        let percentage = 0;
        if (totalResources > 0) {
            percentage = Math.min(95, Math.round((loadedResources / totalResources) * 100));
        }
        
        // Asegurar progreso mínimo
        const elapsedTime = Date.now() - startTime;
        const timeBasedProgress = Math.min(80, (elapsedTime / minDisplayTime) * 50);
        
        // Combinar progreso por tiempo y por recursos
        const combinedProgress = Math.max(percentage, timeBasedProgress);
        
        if (progressBar) {
            progressBar.style.width = `${combinedProgress}%`;
        }
        
        if (progressText && resourceType) {
            progressText.textContent = `Cargando ${resourceType}...`;
        }
        
        console.log(`Progreso: ${combinedProgress}% (Recursos: ${loadedResources}/${totalResources})`);
    }
    
    // Función para verificar si todos los recursos críticos están cargados
    function checkAllResourcesLoaded() {
        // Verificar si el logo está cargado
        const logo = document.querySelector('.splash-logo');
        if (logo && !logo.complete) {
            return false;
        }
        
        // Verificar si se han cargado suficientes recursos
        if (totalResources > 0 && loadedResources < totalResources) {
            return false;
        }
        
        return true;
    }
    
    // Función para iniciar el monitoreo de carga
    function startLoadMonitoring() {
        // Contar imágenes en la página principal
        const allImages = document.querySelectorAll('img');
        totalResources = Math.max(criticalResources.length, allImages.length + 1);
        
        // Monitorear carga del logo del splash
        const splashLogo = document.querySelector('.splash-logo');
        if (splashLogo) {
            if (splashLogo.complete) {
                updateProgress('logo');
            } else {
                splashLogo.addEventListener('load', () => updateProgress('logo'));
                splashLogo.addEventListener('error', () => updateProgress('logo'));
            }
        }
        
        // Monitorear carga de imágenes en el body (excluyendo el splash)
        document.querySelectorAll('img').forEach(img => {
            if (img.classList.contains('splash-logo')) return;
            
            if (img.complete) {
                updateProgress('imágenes');
            } else {
                img.addEventListener('load', () => updateProgress('imágenes'));
                img.addEventListener('error', () => updateProgress('imágenes'));
            }
        });
        
        // Monitorear carga de hojas de estilo
        document.querySelectorAll('link[rel="stylesheet"]').forEach(link => {
            link.addEventListener('load', () => updateProgress('estilos'));
            // Para CSS no hay evento error confiable, así que asumimos que carga
            setTimeout(() => updateProgress('estilos'), 100);
        });
        
        // Monitorear carga de scripts
        document.querySelectorAll('script[src]').forEach(script => {
            script.addEventListener('load', () => updateProgress('scripts'));
            script.addEventListener('error', () => updateProgress('scripts'));
        });
        
        // También monitorear cuando el DOM esté listo
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            updateProgress('DOM');
        } else {
            document.addEventListener('DOMContentLoaded', () => updateProgress('DOM'));
        }
        
        // Monitorear carga completa de la página
        window.addEventListener('load', handlePageLoaded);
        
        // Fallback: máximo tiempo de espera
        setTimeout(finishSplash, maxDisplayTime);
    }
    
    // Función que se ejecuta cuando la página carga
    function handlePageLoaded() {
        updateProgress('página completa');
        hasLoaded = true;
        
        // Esperar un poco más para asegurar que todo esté listo
        setTimeout(finishSplash, 300);
    }
    
    // Función para finalizar el splash
    function finishSplash() {
        // Marcar que todos los recursos están cargados
        hasLoaded = true;
        
        // Completar la barra de progreso
        if (progressBar) {
            progressBar.style.width = '100%';
        }
        
        if (progressText) {
            progressText.textContent = '¡Listo!';
        }
        
        // Esperar al menos el tiempo mínimo de visualización
        const elapsedTime = Date.now() - startTime;
        const remainingTime = Math.max(0, minDisplayTime - elapsedTime);
        
        setTimeout(hideSplash, remainingTime + 500); // +500ms para animación suave
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
    
    // Iniciar monitoreo después de un breve delay
    setTimeout(startLoadMonitoring, 100);
    
})();
</script>