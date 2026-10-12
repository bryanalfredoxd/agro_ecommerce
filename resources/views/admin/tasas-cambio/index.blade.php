@extends('layouts.admin')

@section('title', 'Control de Tasa de Cambio - Agrosuministros Rodríguez')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up pb-20">
            
            {{-- ENCABEZADO --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-3.5">
                    <a href="{{ route('admin.dashboard') }}" class="size-10 flex-shrink-0 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:text-teal-600 hover:border-teal-200 transition-colors shadow-2xs">
                        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                    </a>
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-black text-agro-dark flex items-center gap-2">
                            <span class="material-symbols-outlined text-teal-600 text-[32px]">currency_exchange</span>
                            Control de Tasa de Cambio
                        </h2>
                        <p class="text-xs sm:text-sm text-gray-500 mt-0.5 font-medium">Supervisa la tasa oficial del BCV y establece excepciones manuales para las ventas y conversiones.</p>
                    </div>
                </div>

                @if(Auth::user()->tienePermiso('gestionar_tasas_cambio'))
                <div class="flex items-center gap-2.5">
                    <button type="button" 
                            onclick="abrirModalSincronizarBCV()" 
                            id="btnSyncBCVHeader"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-black text-white bg-teal-600 hover:bg-teal-700 shadow-sm shadow-teal-600/20 transition-all hover:scale-105 active:scale-95 cursor-pointer">
                        <span class="material-symbols-outlined text-[19px]">sync</span>
                        <span>Sincronizar al BCV</span>
                    </button>
                </div>
                @endif
            </div>

            {{-- PANEL SUPERIOR: Tasa Actual y Formulario --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                
                {{-- Tarjeta 1: TASA ACTUAL ACTIVA --}}
                <div class="lg:col-span-1 bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8 relative overflow-hidden flex flex-col justify-between">
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-teal-50 rounded-full z-0 pointer-events-none"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Tasa Vigente (USD a VES)</h3>
                            <div class="size-8 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">payments</span>
                            </div>
                        </div>
                        
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="text-4xl sm:text-5xl font-black text-agro-dark tracking-tight" id="currentRateText">
                                {{ number_format($tasaActual->valor_tasa ?? 0, 2) }}
                            </span>
                            <span class="text-xl font-bold text-gray-700">Bs</span>
                        </div>

                        <div id="tasaBadgeStatus">
                            @if(isset($tasaActual) && $tasaActual->fuente === 'MANUAL')
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-orange-50 text-orange-700 text-[10px] font-black rounded-lg border border-orange-200 mt-2 uppercase">
                                    <span class="material-symbols-outlined text-[14px]">warning</span> Sobrescritura Manual Activa
                                </div>
                            @else
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-teal-50 text-teal-700 text-[10px] font-black rounded-lg border border-teal-200 mt-2 uppercase">
                                    <span class="material-symbols-outlined text-[14px]">check_circle</span> Sincronizada Automáticamente
                                </div>
                            @endif
                        </div>

                        <p class="text-[11px] text-gray-500 font-semibold mt-6" id="tasaUltimaFecha">
                            Última actualización: {{ $tasaActual ? \Carbon\Carbon::parse($tasaActual->creado_at)->format('d M Y, h:i A') : 'N/A' }}
                        </p>
                    </div>

                    @if(Auth::user()->tienePermiso('gestionar_tasas_cambio'))
                    <div class="relative z-10 pt-4 mt-4 border-t border-gray-100">
                        <button type="button" onclick="abrirModalSincronizarBCV()" class="w-full inline-flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-bold text-teal-700 bg-teal-50/80 hover:bg-teal-100 border border-teal-200/60 transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">sync</span>
                            <span>Consultar BCV en vivo</span>
                        </button>
                    </div>
                    @endif
                </div>

                {{-- Tarjeta 2: INYECTAR TASA MANUAL --}}
                <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-black text-agro-dark">Forzar Tasa Manual</h3>
                            <p class="text-xs text-gray-500 mt-1 max-w-sm">Si el BCV presenta fallas o requieres aplicar una contingencia comercial, puedes fijar la tasa manualmente.</p>
                        </div>
                        <div class="hidden sm:flex size-12 bg-orange-50 text-orange-600 rounded-2xl items-center justify-center">
                            <span class="material-symbols-outlined text-[24px]">edit_note</span>
                        </div>
                    </div>

                    @if(Auth::user()->tienePermiso('gestionar_tasas_cambio'))
                    <form id="formManualRate" onsubmit="validarYAbrirModalManual(event)" class="mt-6 flex flex-col sm:flex-row gap-4 items-end">
                        <div class="w-full sm:w-1/2 group">
                            <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-orange-600">Nuevo Valor (Bs / USD)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="font-black text-gray-700 group-focus-within:text-orange-600 transition-colors">Bs.</span>
                                </div>
                                <input type="number" name="valor_tasa" id="input_valor_tasa" step="0.0001" min="1" required 
                                       class="w-full h-12 rounded-xl bg-gray-50 border border-gray-200 focus:border-orange-500 focus:bg-white focus:ring-2 focus:ring-orange-500/20 transition-all duration-300 pl-12 pr-4 text-lg font-black text-agro-dark outline-none shadow-inner" placeholder="Ej: 36.50">
                            </div>
                        </div>
                        <button type="submit" id="btnSaveRate" class="w-full sm:w-auto h-12 px-6 rounded-xl bg-agro-dark text-white font-black text-sm uppercase tracking-wide hover:bg-orange-600 shadow-md transform hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2 flex-shrink-0 cursor-pointer">
                            <span class="material-symbols-outlined text-[20px]">gavel</span> Imponer Tasa
                        </button>
                    </form>
                    @else
                    <div class="mt-6 p-4 rounded-xl bg-gray-50 border border-gray-200 text-xs text-gray-500 flex items-center gap-2">
                        <span class="material-symbols-outlined text-gray-400">lock</span>
                        <span>No cuentas con autorización para alterar la tasa de cambio manual.</span>
                    </div>
                    @endif
                </div>

            </div>

            {{-- HISTÓRICO DE TASAS (AJAX) --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 relative">
                <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <h3 class="text-lg font-black text-agro-dark flex items-center gap-2">
                        <span class="material-symbols-outlined text-gray-700">history</span>
                        Historial de Movimientos
                    </h3>
                    
                    {{-- Filtros Rápidos --}}
                    <div class="flex items-center bg-gray-50 p-1 rounded-xl border border-gray-200">
                        <button class="filter-btn active px-4 py-1.5 text-xs font-bold rounded-lg transition-colors bg-white shadow-sm text-agro-dark cursor-pointer" data-fuente="all">Todas</button>
                        <button class="filter-btn px-4 py-1.5 text-xs font-bold rounded-lg transition-colors text-gray-500 hover:text-agro-dark cursor-pointer" data-fuente="API">Automáticas (BCV)</button>
                        <button class="filter-btn px-4 py-1.5 text-xs font-bold rounded-lg transition-colors text-gray-500 hover:text-agro-dark cursor-pointer" data-fuente="MANUAL">Manuales</button>
                    </div>
                </div>

                <div id="loadingOverlay" class="absolute inset-0 top-[70px] bg-white/80 backdrop-blur-sm z-20 hidden items-center justify-center rounded-b-3xl">
                    <span class="material-symbols-outlined animate-spin text-teal-600 text-4xl">autorenew</span>
                </div>
                
                <div id="tableContent">
                    @include('admin.tasas-cambio.partials._table')
                </div>
            </div>

        </div>
    </main>
</div>

{{-- ========================================================================= --}}
{{-- MODAL 1: CONFIRMAR TASA MANUAL                                            --}}
{{-- ========================================================================= --}}
<div id="modalConfirmarManual" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity opacity-0" id="modalManualBackdrop" onclick="cerrarModalManual()"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-center p-4">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-md opacity-0 scale-95 flex flex-col border border-gray-100" id="modalManualPanel">
            
            <div class="p-6 pb-0 flex items-start gap-4">
                <div class="size-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[28px]">gavel</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-black text-gray-900 leading-tight">¿Imponer Tasa Manual?</h3>
                    <p class="text-xs text-gray-500 mt-1">Sobrescribirá temporalmente el valor de referencia del BCV.</p>
                </div>
                <button type="button" onclick="cerrarModalManual()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div class="p-4 rounded-2xl bg-orange-50/60 border border-orange-200/80 text-center">
                    <span class="text-[10px] font-black text-orange-800 uppercase tracking-widest block mb-1">Nueva Tasa Propuesta</span>
                    <div class="flex items-baseline justify-center gap-1.5">
                        <span class="text-sm font-bold text-orange-700">Bs.</span>
                        <span class="text-3xl font-black text-orange-950 tracking-tight" id="modalManualTasaValor">0.00</span>
                        <span class="text-xs font-bold text-orange-700">/ 1 USD</span>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-amber-50/60 border border-amber-200/70 flex items-start gap-2.5">
                    <span class="material-symbols-outlined text-amber-600 text-[18px] shrink-0 mt-0.5">warning</span>
                    <p class="text-xs text-amber-900 font-medium leading-relaxed">
                        Todos los nuevos pedidos de la tienda online, el terminal de ventas (POS) y las cotizaciones operarán con este monto de inmediato.
                    </p>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50/80 border-t border-gray-100 flex items-center justify-end gap-3">
                <button type="button" onclick="cerrarModalManual()" class="px-4 py-2.5 rounded-xl font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 text-xs transition-colors cursor-pointer">
                    Cancelar
                </button>
                <button type="button" id="btnConfirmarManual" onclick="confirmarGuardarManual()" class="px-5 py-2.5 rounded-xl font-black text-white bg-orange-600 hover:bg-orange-700 shadow-sm text-xs flex items-center gap-2 transition-all cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">check</span>
                    <span id="btnConfirmarManualText">Sí, Imponer Tasa</span>
                </button>
            </div>

        </div>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL 2: CONFIRMAR SINCRONIZACIÓN BCV                                     --}}
{{-- ========================================================================= --}}
<div id="modalConfirmarBCV" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity opacity-0" id="modalBCVBackdrop" onclick="cerrarModalBCV()"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-center p-4">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-md opacity-0 scale-95 flex flex-col border border-gray-100" id="modalBCVPanel">
            
            <div class="p-6 pb-0 flex items-start gap-4">
                <div class="size-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[28px]">currency_exchange</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-black text-gray-900 leading-tight">Sincronizar con el BCV</h3>
                    <p class="text-xs text-gray-500 mt-1">Obtén la cotización oficial de referencia en vivo.</p>
                </div>
                <button type="button" onclick="cerrarModalBCV()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <p class="text-xs text-gray-600 leading-relaxed">
                    El sistema consultará de forma segura el valor oficial de referencia del <strong>Banco Central de Venezuela (BCV)</strong> y lo aplicará de inmediato al catálogo de productos y caja.
                </p>

                <div class="p-4 rounded-2xl bg-teal-50/60 border border-teal-200/80 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-black text-teal-800 uppercase tracking-widest block mb-0.5">Tasa Actual en Sistema</span>
                        <span class="text-xl font-black text-teal-950">Bs. {{ number_format($tasaActual->valor_tasa ?? 0, 2) }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-0.5">Fuente</span>
                        <span class="text-xs font-bold {{ (isset($tasaActual) && $tasaActual->fuente === 'MANUAL') ? 'text-orange-700' : 'text-teal-700' }}">
                            {{ $tasaActual ? ($tasaActual->fuente === 'MANUAL' ? 'Manual' : 'Oficial BCV') : 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="px-4 sm:px-6 py-3.5 sm:py-4 bg-gray-50/80 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-end gap-2 sm:gap-3">
                <button type="button" onclick="cerrarModalBCV()" class="w-full sm:w-auto px-4 py-2.5 rounded-xl font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 text-xs text-center transition-colors cursor-pointer">
                    Cancelar
                </button>
                <button type="button" id="btnEjecutarSyncBCV" onclick="ejecutarSincronizacionBCV()" class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-black text-white bg-teal-600 hover:bg-teal-700 shadow-sm text-xs flex items-center justify-center gap-2 transition-all cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]" id="iconEjecutarSync">sync</span>
                    <span id="btnEjecutarSyncBCVText">Sincronizar Ahora</span>
                </button>
            </div>

        </div>
    </div>
</div>

{{-- CONTENEDOR TOAST --}}
<div id="toast-container" class="fixed bottom-6 right-6 z-[999] flex flex-col gap-2"></div>

@push('scripts')
<script>
    let currentFuente = 'all';

    // Manejo de Filtros
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => {
                b.classList.remove('bg-white', 'shadow-sm', 'text-agro-dark', 'active');
                b.classList.add('text-gray-500');
            });
            this.classList.add('bg-white', 'shadow-sm', 'text-agro-dark', 'active');
            this.classList.remove('text-gray-500');
            
            currentFuente = this.getAttribute('data-fuente');
            fetchData(1);
        });
    });

    // Paginación AJAX
    document.addEventListener('click', function(e) {
        if (e.target.closest('.ajax-pagination a')) {
            e.preventDefault();
            const url = new URL(e.target.closest('a').href);
            fetchData(url.searchParams.get('page'));
        }
    });

    function fetchData(page = 1) {
        const loading = document.getElementById('loadingOverlay');
        loading.classList.remove('hidden');
        loading.classList.add('flex');

        fetch(`{{ route('admin.tasas-cambio.index') }}?fuente=${currentFuente}&page=${page}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            document.getElementById('tableContent').innerHTML = html;
        })
        .finally(() => {
            loading.classList.add('hidden');
            loading.classList.remove('flex');
        });
    }

    // ==========================================
    // SISTEMA DE NOTIFICACIONES TOAST ELEGANTE
    // ==========================================
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        const isSuccess = type === 'success';
        const bgColor = isSuccess ? 'bg-[#0f2347] border-teal-500/50' : 'bg-[#2b1016] border-rose-500/50';
        const iconColor = isSuccess ? 'text-teal-400' : 'text-rose-400';
        const icon = isSuccess ? 'check_circle' : 'error';

        toast.className = `flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-2xl border text-white transform translate-y-6 opacity-0 transition-all duration-300 ${bgColor}`;
        toast.innerHTML = `
            <span class="material-symbols-outlined ${iconColor} text-[22px] shrink-0">${icon}</span>
            <p class="font-bold text-xs sm:text-sm leading-snug">${message}</p>
        `;

        container.appendChild(toast);
        setTimeout(() => toast.classList.remove('translate-y-6', 'opacity-0'), 10);
        setTimeout(() => {
            toast.classList.add('translate-y-6', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // ==========================================
    // MODAL 1: TASA MANUAL (CONFIRMACIÓN ELEGANTE)
    // ==========================================
    function validarYAbrirModalManual(e) {
        e.preventDefault();
        const inputTasa = document.getElementById('input_valor_tasa');
        const val = parseFloat(inputTasa.value);

        if (isNaN(val) || val <= 0) {
            showToast('Por favor ingrese un valor de tasa válido mayor a 0.', 'error');
            inputTasa.focus();
            return;
        }

        document.getElementById('modalManualTasaValor').innerText = val.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 4 });
        
        abrirModalManual();
    }

    function abrirModalManual() {
        const modal = document.getElementById('modalConfirmarManual');
        const backdrop = document.getElementById('modalManualBackdrop');
        const panel = document.getElementById('modalManualPanel');

        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    function cerrarModalManual() {
        const modal = document.getElementById('modalConfirmarManual');
        const backdrop = document.getElementById('modalManualBackdrop');
        const panel = document.getElementById('modalManualPanel');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 250);
    }

    function confirmarGuardarManual() {
        const inputTasa = document.getElementById('input_valor_tasa');
        const btn = document.getElementById('btnConfirmarManual');
        const btnText = document.getElementById('btnConfirmarManualText');
        const originalText = btnText.innerText;

        btn.disabled = true;
        btnText.innerText = 'Imponiendo...';

        fetch(`{{ route('admin.tasas-cambio.store') }}`, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ valor_tasa: inputTasa.value })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                cerrarModalManual();
                showToast(data.message, 'success');
                
                // Actualizar número gigante arriba
                document.getElementById('currentRateText').innerText = data.nueva_tasa;
                
                // Actualizar badge a Sobrescritura Manual
                const badgeContainer = document.getElementById('tasaBadgeStatus');
                if (badgeContainer) {
                    badgeContainer.innerHTML = `
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-orange-50 text-orange-700 text-[10px] font-black rounded-lg border border-orange-200 mt-2 uppercase">
                            <span class="material-symbols-outlined text-[14px]">warning</span> Sobrescritura Manual Activa
                        </div>
                    `;
                }

                // Actualizar fecha
                if (data.fecha_actualizacion) {
                    document.getElementById('tasaUltimaFecha').innerText = 'Última actualización: ' + data.fecha_actualizacion;
                }

                inputTasa.value = '';
                fetchData(1); // Recargar la tabla histórica
            } else {
                showToast(data.message || 'Error al guardar.', 'error');
            }
        })
        .catch(() => showToast('Error de comunicación con el servidor.', 'error'))
        .finally(() => {
            btn.disabled = false;
            btnText.innerText = originalText;
        });
    }

    // ==========================================
    // MODAL 2: SINCRONIZACIÓN BCV
    // ==========================================
    function abrirModalSincronizarBCV() {
        const modal = document.getElementById('modalConfirmarBCV');
        const backdrop = document.getElementById('modalBCVBackdrop');
        const panel = document.getElementById('modalBCVPanel');

        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    function cerrarModalBCV() {
        const modal = document.getElementById('modalConfirmarBCV');
        const backdrop = document.getElementById('modalBCVBackdrop');
        const panel = document.getElementById('modalBCVPanel');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 250);
    }

    function ejecutarSincronizacionBCV() {
        const btn = document.getElementById('btnEjecutarSyncBCV');
        const btnText = document.getElementById('btnEjecutarSyncBCVText');
        const icon = document.getElementById('iconEjecutarSync');
        const originalText = btnText.innerText;

        btn.disabled = true;
        btnText.innerText = 'Consultando BCV...';
        icon.classList.add('animate-spin');

        fetch(`{{ route('admin.tasas-cambio.sincronizar') }}`, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Error al sincronizar con el BCV');
            return data;
        })
        .then(data => {
            cerrarModalBCV();
            showToast(data.message, 'success');

            // Actualizar número gigante arriba
            if (data.nueva_tasa) {
                document.getElementById('currentRateText').innerText = data.nueva_tasa;
            }

            // Actualizar badge a Automático
            const badgeContainer = document.getElementById('tasaBadgeStatus');
            if (badgeContainer) {
                badgeContainer.innerHTML = `
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-teal-50 text-teal-700 text-[10px] font-black rounded-lg border border-teal-200 mt-2 uppercase">
                        <span class="material-symbols-outlined text-[14px]">check_circle</span> Sincronizada Automáticamente
                    </div>
                `;
            }

            // Actualizar fecha
            if (data.fecha_actualizacion) {
                document.getElementById('tasaUltimaFecha').innerText = 'Última actualización: ' + data.fecha_actualizacion;
            }

            fetchData(1); // Recargar la tabla
        })
        .catch(err => {
            showToast(err.message, 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btnText.innerText = originalText;
            icon.classList.remove('animate-spin');
        });
    }

    // Soporte tecla Escape para cerrar modales
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modalManual = document.getElementById('modalConfirmarManual');
            if (modalManual && !modalManual.classList.contains('hidden')) {
                cerrarModalManual();
                return;
            }

            const modalBCV = document.getElementById('modalConfirmarBCV');
            if (modalBCV && !modalBCV.classList.contains('hidden')) {
                cerrarModalBCV();
            }
        }
    });
</script>
@endpush
@endsection