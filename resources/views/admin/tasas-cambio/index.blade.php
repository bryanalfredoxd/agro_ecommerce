@extends('layouts.admin')

@section('title', 'Tasas de Cambio - Corpo Agrícola')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col">
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up pb-20">
            
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-2xl font-black text-agro-dark flex items-center gap-2">
                        <span class="material-symbols-outlined text-teal-600 text-[32px]">currency_exchange</span>
                        Control de Tasa de Cambio
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Supervisa la tasa BCV actual y establece excepciones manuales.</p>
                </div>
            </div>

            {{-- PANEL SUPERIOR: Tasa Actual y Formulario --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                
                {{-- Tarjeta 1: TASA ACTUAL ACTIVA --}}
                <div class="lg:col-span-1 bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8 relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-teal-50 rounded-full z-0"></div>
                    <div class="relative z-10">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Tasa Vigente (USD a VES)</h3>
                        
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="text-4xl sm:text-5xl font-black text-agro-dark tracking-tight" id="currentRateText">
                                {{ number_format($tasaActual->valor_tasa ?? 0, 2) }}
                            </span>
                            <span class="text-xl font-bold text-gray-400">Bs</span>
                        </div>

                        @if(isset($tasaActual) && $tasaActual->fuente === 'MANUAL')
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-orange-50 text-orange-700 text-[10px] font-black rounded-lg border border-orange-200 mt-2 uppercase">
                                <span class="material-symbols-outlined text-[14px]">warning</span> Sobrescritura Manual Activa
                            </div>
                        @else
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-teal-50 text-teal-700 text-[10px] font-black rounded-lg border border-teal-200 mt-2 uppercase">
                                <span class="material-symbols-outlined text-[14px]">check_circle</span> Sincronizada Automáticamente
                            </div>
                        @endif

                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-6">
                            Última actualización: {{ $tasaActual ? \Carbon\Carbon::parse($tasaActual->creado_at)->format('d M Y, h:i A') : 'N/A' }}
                        </p>
                    </div>
                </div>

                {{-- Tarjeta 2: INYECTAR TASA MANUAL --}}
                <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-black text-agro-dark">Forzar Tasa Manual</h3>
                            <p class="text-xs text-gray-500 mt-1 max-w-sm">Si el BCV no actualiza o hay una contingencia, puedes fijar una tasa manualmente. Todos los nuevos pedidos usarán este valor.</p>
                        </div>
                        <div class="hidden sm:flex w-12 h-12 bg-orange-50 text-orange-600 rounded-2xl items-center justify-center">
                            <span class="material-symbols-outlined text-[24px]">edit_note</span>
                        </div>
                    </div>

                    <form id="formManualRate" onsubmit="saveManualRate(event)" class="mt-6 flex flex-col sm:flex-row gap-4 items-end">
                        <div class="w-full sm:w-1/2 group">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1 transition-colors group-focus-within:text-orange-600">Nuevo Valor (Bs)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="font-black text-gray-400 group-focus-within:text-orange-600 transition-colors">Bs.</span>
                                </div>
                                <input type="number" name="valor_tasa" id="input_valor_tasa" step="0.0001" min="1" required 
                                       class="w-full h-12 rounded-xl bg-gray-50 border border-gray-200 focus:border-orange-500 focus:bg-white focus:ring-2 focus:ring-orange-500/20 transition-all duration-300 pl-12 pr-4 text-lg font-black text-agro-dark outline-none shadow-inner" placeholder="Ej: 36.50">
                            </div>
                        </div>
                        <button type="submit" id="btnSaveRate" class="w-full sm:w-auto h-12 px-6 rounded-xl bg-agro-dark text-white font-black text-sm uppercase tracking-wide hover:bg-orange-600 shadow-md transform hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2 flex-shrink-0">
                            <span class="material-symbols-outlined text-[20px]">gavel</span> Imponer Tasa
                        </button>
                    </form>
                </div>

            </div>

            {{-- HISTÓRICO DE TASAS (AJAX) --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 relative">
                <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <h3 class="text-lg font-black text-agro-dark flex items-center gap-2">
                        <span class="material-symbols-outlined text-gray-400">history</span>
                        Historial de Movimientos
                    </h3>
                    
                    {{-- Filtros Rápidos --}}
                    <div class="flex items-center bg-gray-50 p-1 rounded-xl border border-gray-200">
                        <button class="filter-btn active px-4 py-1.5 text-xs font-bold rounded-lg transition-colors bg-white shadow-sm text-agro-dark" data-fuente="all">Todas</button>
                        <button class="filter-btn px-4 py-1.5 text-xs font-bold rounded-lg transition-colors text-gray-500 hover:text-agro-dark" data-fuente="API">Automáticas</button>
                        <button class="filter-btn px-4 py-1.5 text-xs font-bold rounded-lg transition-colors text-gray-500 hover:text-agro-dark" data-fuente="MANUAL">Manuales</button>
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
    // INYECCIÓN DE TASA MANUAL (AJAX)
    // ==========================================
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-600' : 'bg-red-600';
        const icon = type === 'success' ? 'check_circle' : 'error';

        toast.className = `flex items-center gap-3 px-5 py-4 rounded-xl shadow-2xl text-white transform translate-y-10 opacity-0 transition-all duration-300 ${bgColor}`;
        toast.innerHTML = `<span class="material-symbols-outlined">${icon}</span><p class="font-bold text-sm">${message}</p>`;

        container.appendChild(toast);
        setTimeout(() => toast.classList.remove('translate-y-10', 'opacity-0'), 10);
        setTimeout(() => {
            toast.classList.add('translate-y-10', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    function saveManualRate(e) {
        e.preventDefault();
        
        if(!confirm('¿Estás seguro de forzar esta tasa? Todos los nuevos pedidos y ventas en tienda usarán este valor.')) return;

        const btn = document.getElementById('btnSaveRate');
        const originalHtml = btn.innerHTML;
        const inputTasa = document.getElementById('input_valor_tasa');
        
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin">autorenew</span> Aplicando...';
        btn.disabled = true;

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
                showToast(data.message, 'success');
                // Actualizar el número gigante arriba
                document.getElementById('currentRateText').innerText = data.nueva_tasa;
                inputTasa.value = '';
                fetchData(1); // Recargar la tabla
            } else {
                showToast(data.message || 'Error al guardar.', 'error');
            }
        })
        .catch(() => showToast('Error crítico en el servidor.', 'error'))
        .finally(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    }
</script>
@endpush
@endsection