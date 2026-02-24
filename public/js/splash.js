(function() {
    'use strict';
    
    // 1. Verificación Inmediata: Si ya se mostró en esta pestaña, abortar antes de procesar nada
    if (sessionStorage.getItem('splash_shown') === 'true') {
        const el = document.getElementById('splash-screen');
        if (el) el.style.display = 'none';
        document.body.classList.remove('splash-active');
        document.body.style.overflow = '';
        return;
    }
    
    // Inicializar elementos
    const splashScreen = document.getElementById('splash-screen');
    const progressBar = document.getElementById('splash-progress');
    const progressText = document.getElementById('splash-progress-text');
    
    if (!splashScreen) return;
    
    // Variables de estado
    let totalResources = 0;
    let loadedResources = 0;
    let minDisplayTime = 1500; 
    let maxDisplayTime = 8000; // Aumentado a 8s como margen de seguridad
    let startTime = Date.now();
    let isFinishing = false; // Evita que la función de cierre se ejecute múltiples veces

    // Marcar activo y bloquear scroll
    document.body.classList.add('splash-active');
    document.body.style.overflow = 'hidden';

    // --- MEJORA 1: SEGURO DE VIDA (Emergency Timeout) ---
    // Si después de 10 segundos el splash sigue ahí, lo quitamos a la fuerza
    const emergencyTimeout = setTimeout(() => {
        if (!isFinishing) {
            console.warn("Splash: Tiempo límite de seguridad alcanzado.");
            finishSplash();
        }
    }, 10000);

    // --- MEJORA 2: DETECTOR DE BOTÓN "ATRÁS" (bfcache) ---
    // Detecta si la página se carga desde la memoria del navegador al retroceder
    window.addEventListener('pageshow', function(event) {
        if (event.persisted || sessionStorage.getItem('splash_shown') === 'true') {
            hideSplashImmediate();
        }
    });

    const criticalResources = [
        '{{ asset("img/ico/Logo0.webp") }}',
    ];
    
    function updateProgress(resourceType = '') {
        if (isFinishing) return;
        loadedResources++;
        
        let percentage = 0;
        if (totalResources > 0) {
            percentage = Math.min(95, Math.round((loadedResources / totalResources) * 100));
        }
        
        const elapsedTime = Date.now() - startTime;
        const timeBasedProgress = Math.min(80, (elapsedTime / minDisplayTime) * 50);
        const combinedProgress = Math.max(percentage, timeBasedProgress);
        
        if (progressBar) progressBar.style.width = `${combinedProgress}%`;
        if (progressText && resourceType) {
            progressText.textContent = `Cargando ${resourceType}...`;
        }
    }
    
    function startLoadMonitoring() {
        const allImages = document.querySelectorAll('img');
        totalResources = Math.max(criticalResources.length, allImages.length + 1);
        
        const splashLogo = document.querySelector('.splash-logo');
        if (splashLogo) {
            if (splashLogo.complete) updateProgress('logo');
            else {
                splashLogo.addEventListener('load', () => updateProgress('logo'));
                splashLogo.addEventListener('error', () => updateProgress('logo'));
            }
        }
        
        document.querySelectorAll('img').forEach(img => {
            if (img.classList.contains('splash-logo')) return;
            if (img.complete) updateProgress('imágenes');
            else {
                img.addEventListener('load', () => updateProgress('imágenes'));
                img.addEventListener('error', () => updateProgress('imágenes'));
            }
        });
        
        document.querySelectorAll('link[rel="stylesheet"]').forEach(link => {
            link.addEventListener('load', () => updateProgress('estilos'));
            setTimeout(() => updateProgress('estilos'), 100);
        });
        
        document.querySelectorAll('script[src]').forEach(script => {
            script.addEventListener('load', () => updateProgress('scripts'));
            script.addEventListener('error', () => updateProgress('scripts'));
        });
        
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            updateProgress('DOM');
        } else {
            document.addEventListener('DOMContentLoaded', () => updateProgress('DOM'));
        }
        
        window.addEventListener('load', handlePageLoaded);
        setTimeout(finishSplash, maxDisplayTime);
    }
    
    function handlePageLoaded() {
        updateProgress('página completa');
        setTimeout(finishSplash, 300);
    }
    
    function finishSplash() {
        if (isFinishing) return;
        isFinishing = true;
        
        // Limpiar el temporizador de emergencia
        clearTimeout(emergencyTimeout);
        
        if (progressBar) progressBar.style.width = '100%';
        if (progressText) progressText.textContent = '¡Listo!';
        
        const elapsedTime = Date.now() - startTime;
        const remainingTime = Math.max(0, minDisplayTime - elapsedTime);
        
        setTimeout(hideSplash, remainingTime + 500);
    }
    
    function hideSplash() {
        sessionStorage.setItem('splash_shown', 'true');
        splashScreen.classList.add('hidden');
        
        setTimeout(() => {
            document.body.classList.remove('splash-active');
            document.body.style.overflow = '';
            // MEJORA 3: Remover del DOM para que no interfiera más
            splashScreen.remove();
            
            // Notificación al servidor
            fetch('/splash/mark-shown', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ shown: true })
            }).catch(() => {});
        }, 800);
    }

    // Función para matar el splash sin animaciones (usada en el retroceso)
    function hideSplashImmediate() {
        isFinishing = true;
        clearTimeout(emergencyTimeout);
        sessionStorage.setItem('splash_shown', 'true');
        document.body.classList.remove('splash-active');
        document.body.style.overflow = '';
        if (splashScreen) splashScreen.remove();
    }
    
    function skipSplash() {
        if (isFinishing && splashScreen.classList.contains('hidden')) return;
        splashScreen.classList.add('fast-exit');
        hideSplash();
    }
    
    splashScreen.addEventListener('click', skipSplash);
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.key === ' ') skipSplash();
    });
    
    setTimeout(startLoadMonitoring, 100);
    
})();
