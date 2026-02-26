@extends('layouts.admin')

@section('title', 'Listado de Pedidos - Corpo Agrícola')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col">
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up pb-10">
            
            {{-- Encabezado --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-2xl font-black text-agro-dark flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600 text-[32px]">shopping_bag</span>
                        Gestión de Pedidos
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Supervisa todas las órdenes web, despachos y ventas físicas.</p>
                </div>
                
                {{-- Botón para nueva venta manual (Módulo futuro) 
                <a href="#" class="inline-flex items-center justify-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-500/30 transition-all transform hover:-translate-y-0.5">
                    <span class="material-symbols-outlined text-[20px]">point_of_sale</span>
                    Nueva Venta Manual
                </a>
                --}}
            </div>

            {{-- CONTROLES Y FILTROS --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-2 mb-6">
                
                {{-- Tabs de Estados --}}
                <div class="flex overflow-x-auto custom-scrollbar border-b border-gray-100 px-4 pt-2 gap-6">
                    <button class="status-tab active pb-3 text-sm font-black border-b-2 border-primary text-agro-dark whitespace-nowrap" data-estado="todos">Todos los Pedidos</button>
                    <button class="status-tab pb-3 text-sm font-bold border-b-2 border-transparent text-gray-400 hover:text-gray-700 whitespace-nowrap" data-estado="pendientes">Pendientes (Validar)</button>
                    <button class="status-tab pb-3 text-sm font-bold border-b-2 border-transparent text-gray-400 hover:text-gray-700 whitespace-nowrap" data-estado="en_proceso">Por Despachar</button>
                    <button class="status-tab pb-3 text-sm font-bold border-b-2 border-transparent text-gray-400 hover:text-gray-700 whitespace-nowrap" data-estado="en_ruta">En Ruta</button>
                    <button class="status-tab pb-3 text-sm font-bold border-b-2 border-transparent text-gray-400 hover:text-gray-700 whitespace-nowrap" data-estado="completados">Completados</button>
                    <button class="status-tab pb-3 text-sm font-bold border-b-2 border-transparent text-gray-400 hover:text-gray-700 whitespace-nowrap" data-estado="cancelados">Devueltos / Cancelados</button>
                </div>

                {{-- Buscador --}}
                <div class="p-4 bg-gray-50/50 rounded-b-2xl">
                    <div class="w-full relative">
                        <span class="material-symbols-outlined absolute left-3 top-3 text-gray-400">search</span>
                        <input type="text" id="searchInput" class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none font-medium text-agro-dark" placeholder="Buscar por Número de Pedido, Nombre del Cliente, CI o Email...">
                    </div>
                </div>
            </div>

            {{-- CONTENEDOR DE LA TABLA AJAX --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 relative" id="tableContainer">
                <div id="loadingOverlay" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-20 hidden items-center justify-center rounded-3xl">
                    <span class="material-symbols-outlined animate-spin text-blue-600 text-4xl">autorenew</span>
                </div>
                
                <div id="tableContent">
                    @include('admin.pedidos.partials._table')
                </div>
            </div>

        </div>
    </main>
</div>

{{-- ========================================== --}}
{{-- MODAL CAMBIO RÁPIDO DE ESTADO              --}}
{{-- ========================================== --}}
<div id="statusModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="statusBackdrop" onclick="closeStatusModal()"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-center p-4">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-sm opacity-0 scale-95 flex flex-col" id="statusPanel">
            
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-black text-agro-dark leading-none">Cambiar Estado</h3>
                <button type="button" onclick="closeStatusModal()" class="text-gray-400 hover:text-red-500 bg-white p-1 rounded-lg border border-gray-200 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form id="statusForm" onsubmit="saveStatus(event)">
                <input type="hidden" id="pedido_id">
                
                <div class="p-6">
                    <p class="text-xs text-gray-500 mb-4 bg-amber-50 p-3 rounded-xl border border-amber-100 text-amber-800">
                        <span class="font-bold">Aviso:</span> Al cambiar a "Pagado" o "Devuelto", el inventario se ajustará automáticamente por el sistema.
                    </p>
                    
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nuevo Estado del Pedido</label>
                    <select id="estado_select" name="estado" required class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all font-bold text-agro-dark outline-none cursor-pointer appearance-none">
                        <option value="pendiente">Pendiente</option>
                        <option value="pagado">Pagado (Aprobado)</option>
                        <option value="preparacion">En Preparación / Empaque</option>
                        <option value="en_ruta">En Ruta (Despacho)</option>
                        <option value="entregado">Entregado al Cliente</option>
                        <option value="devuelto">Devuelto (Reintegro Stock)</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                    <button type="button" onclick="closeStatusModal()" class="px-5 py-2.5 rounded-xl font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 text-sm">Cancelar</button>
                    <button type="submit" id="btnSaveStatus" class="px-6 py-2.5 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm text-sm flex items-center gap-2 transition-all">
                        <span class="material-symbols-outlined text-[18px]">save</span> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- CONTENEDOR TOAST --}}
<div id="toast-container" class="fixed bottom-6 right-6 z-[999] flex flex-col gap-2"></div>

@push('scripts')
<script>
    // ESTADO PARA AJAX
    let currentFiltroEstado = 'todos';
    let currentSearch = '';
    let searchTimeout;

    // EVENTOS DE LOS CONTROLES (Tabs)
    document.querySelectorAll('.status-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.status-tab').forEach(t => {
                t.classList.remove('border-primary', 'text-agro-dark', 'active');
                t.classList.add('border-transparent', 'text-gray-400');
            });
            this.classList.remove('border-transparent', 'text-gray-400');
            this.classList.add('border-primary', 'text-agro-dark', 'active');
            
            currentFiltroEstado = this.getAttribute('data-estado');
            fetchData(1);
        });
    });

    // Buscador
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        currentSearch = this.value;
        searchTimeout = setTimeout(() => fetchData(1), 400); // 400ms debounce
    });

    // Paginación AJAX
    document.addEventListener('click', function(e) {
        if (e.target.closest('.ajax-pagination a')) {
            e.preventDefault();
            fetchData(new URL(e.target.closest('a').href).searchParams.get('page'));
        }
    });

    // Petición AJAX principal
    function fetchData(page = 1) {
        const loading = document.getElementById('loadingOverlay');
        loading.classList.remove('hidden');
        loading.classList.add('flex');

        const params = new URLSearchParams({
            filtro_estado: currentFiltroEstado,
            buscar: currentSearch,
            page: page
        });

        fetch(`{{ route('admin.pedidos.index') }}?${params.toString()}`, {
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
    // LÓGICA DEL MODAL DE ESTADO
    // ==========================================
    function openStatusModal(id, estadoActual) {
        document.getElementById('pedido_id').value = id;
        document.getElementById('estado_select').value = estadoActual;

        const modal = document.getElementById('statusModal');
        const backdrop = document.getElementById('statusBackdrop');
        const panel = document.getElementById('statusPanel');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    function closeStatusModal() {
        const modal = document.getElementById('statusModal');
        const backdrop = document.getElementById('statusBackdrop');
        const panel = document.getElementById('statusPanel');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

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
        }, 3000);
    }

    function saveStatus(e) {
        e.preventDefault();
        const id = document.getElementById('pedido_id').value;
        const estado = document.getElementById('estado_select').value;
        
        const btn = document.getElementById('btnSaveStatus');
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[18px]">autorenew</span> Aplicando...';
        btn.disabled = true;

        fetch(`/admin/pedidos/${id}/estado`, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ estado: estado })
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Error al cambiar estado');
            return data;
        })
        .then(data => {
            if(data.success) {
                closeStatusModal();
                showToast(data.message, 'success');
                fetchData(1); // Recargamos la tabla para ver el nuevo badge
            }
        })
        .catch(err => {
            showToast(err.message, 'error');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }
</script>
@endpush
@endsection