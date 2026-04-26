@extends('layouts.admin')

@section('title', 'Tesorería y Pagos - Corpo Agrícola')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up pb-10">
            
            {{-- Encabezado --}}
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('admin.dashboard') }}" class="w-10 h-10 flex-shrink-0 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:text-blue-600 hover:border-blue-200 transition-colors shadow-sm">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div>
                    <h2 class="text-2xl font-black text-agro-dark flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600 text-[32px]">account_balance</span>
                        Auditoría de Pagos
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Verifica referencias y capturas de transferencias antes de liberar despachos.</p>
                </div>
            </div>

            {{-- CONTROLES Y FILTROS --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-2 mb-6">
                
                {{-- Tabs de Estados --}}
                <div class="flex overflow-x-auto custom-scrollbar border-b border-gray-100 px-4 pt-2 gap-6">
                    <button class="status-tab active pb-3 text-sm font-black border-b-2 border-primary text-agro-dark whitespace-nowrap" data-estado="revision">En Revisión (Pendientes)</button>
                    <button class="status-tab pb-3 text-sm font-bold border-b-2 border-transparent text-gray-700 hover:text-gray-700 whitespace-nowrap" data-estado="aprobado">Aprobados</button>
                    <button class="status-tab pb-3 text-sm font-bold border-b-2 border-transparent text-gray-700 hover:text-gray-700 whitespace-nowrap" data-estado="rechazado">Rechazados</button>
                    <button class="status-tab pb-3 text-sm font-bold border-b-2 border-transparent text-gray-700 hover:text-gray-700 whitespace-nowrap" data-estado="todos">Historial Completo</button>
                </div>

                {{-- Buscador y Filtro por Método --}}
                <div class="p-4 bg-gray-50/50 rounded-b-2xl flex flex-col sm:flex-row gap-4">
                    <div class="flex-1 relative">
                        <span class="material-symbols-outlined absolute left-3 top-3 text-gray-700">search</span>
                        <input type="text" id="searchInput" class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none font-medium text-agro-dark" placeholder="Buscar por Número de Referencia o ID de Pedido...">
                    </div>
                    
                    {{-- NUEVO: Filtro por Método de Pago --}}
                    <div class="w-full sm:w-56 relative">
                        <select id="metodoSelect" class="w-full pl-4 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none font-bold text-gray-600 appearance-none cursor-pointer">
                            <option value="todos">Todos los Métodos</option>
                            <option value="pago_movil">Pago Móvil</option>
                            <option value="zelle">Zelle</option>
                            <option value="binance">Binance</option>
                            <option value="transferencia">Transferencia Bancaria</option>
                            <option value="efectivo_usd">Efectivo USD</option>
                            <option value="efectivo_bs">Efectivo Bs</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-3 text-gray-700 pointer-events-none">account_balance_wallet</span>
                    </div>
                </div>
            </div>

            {{-- CONTENEDOR DE LA TABLA AJAX --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 relative" id="tableContainer">
                <div id="loadingOverlay" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-20 hidden items-center justify-center rounded-3xl">
                    <span class="material-symbols-outlined animate-spin text-blue-600 text-4xl">autorenew</span>
                </div>
                
                <div id="tableContent">
                    @include('admin.pagos.partials._table')
                </div>
            </div>

        </div>
    </main>
</div>

{{-- ========================================== --}}
{{-- MODAL VISOR DE COMPROBANTES (IMAGEN)       --}}
{{-- ========================================== --}}
<div id="comprobanteModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity opacity-0" id="comprobanteBackdrop" onclick="closeComprobante()"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-center p-4">
        <div class="relative transform overflow-hidden rounded-3xl bg-black text-left shadow-2xl transition-all w-full max-w-lg opacity-0 scale-95 flex flex-col" id="comprobantePanel">
            
            <div class="px-6 py-4 border-b border-gray-800 flex justify-between items-center bg-black">
                <h3 class="text-sm font-bold text-white tracking-widest uppercase">Referencia: <span id="visor_referencia" class="text-blue-400"></span></h3>
                <button type="button" onclick="closeComprobante()" class="text-gray-700 hover:text-white bg-gray-900 p-1.5 rounded-lg border border-gray-800 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="p-2 flex justify-center items-center bg-gray-900 max-h-[80vh] overflow-y-auto custom-scrollbar">
                <img id="visor_imagen" src="" alt="Comprobante de Pago" class="max-w-full h-auto rounded-xl">
            </div>
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{-- MODAL VER DETALLE DEL PEDIDO (NUEVO)       --}}
{{-- ========================================== --}}
<div id="detalleModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="detalleBackdrop" onclick="closeDetalleModal()"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-center p-4 sm:p-0">
        <div class="relative transform overflow-hidden sm:rounded-3xl rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-2xl max-h-[90vh] flex flex-col opacity-0 scale-95" id="detallePanel">
            
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-white sticky top-0 z-20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-sm">
                        <span class="material-symbols-outlined">shopping_bag</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-agro-dark leading-none">Detalles de la Compra</h3>
                        <p class="text-xs text-gray-700 font-bold mt-1" id="detalle_pedido_titulo">Pedido #000000</p>
                    </div>
                </div>
                <button type="button" onclick="closeDetalleModal()" class="text-gray-700 hover:text-red-500 bg-gray-50 hover:bg-red-50 p-2 rounded-xl transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div id="detalleContent" class="overflow-y-auto custom-scrollbar flex-1 relative">
                <div id="detalleLoader" class="absolute inset-0 bg-white z-10 flex flex-col items-center justify-center p-12">
                    <span class="material-symbols-outlined animate-spin text-blue-600 text-4xl mb-4">autorenew</span>
                    <p class="font-bold text-gray-500">Cargando pedido...</p>
                </div>
                <div id="detalleInyectado"></div>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end">
                <button type="button" onclick="closeDetalleModal()" class="px-6 py-2.5 rounded-xl font-black text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 shadow-sm text-sm transition-all">
                    Cerrar Vista
                </button>
            </div>
        </div>
    </div>
</div>

<div id="toast-container" class="fixed bottom-6 right-6 z-[999] flex flex-col gap-2"></div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@push('scripts')
<script>
    // VARIABLES AJAX
    let currentFiltroEstado = 'revision'; 
    let currentSearch = '';
    let currentFiltroMetodo = 'todos'; // Nueva variable
    let searchTimeout;

    // EVENTOS TABS
    document.querySelectorAll('.status-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.status-tab').forEach(t => {
                t.classList.remove('border-primary', 'text-agro-dark', 'active');
                t.classList.add('border-transparent', 'text-gray-700');
            });
            this.classList.remove('border-transparent', 'text-gray-700');
            this.classList.add('border-primary', 'text-agro-dark', 'active');
            currentFiltroEstado = this.getAttribute('data-estado');
            fetchData(1);
        });
    });

    // EVENTOS BUSCADOR Y SELECTOR DE MÉTODO
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        currentSearch = this.value;
        searchTimeout = setTimeout(() => fetchData(1), 400); 
    });

    document.getElementById('metodoSelect').addEventListener('change', function() {
        currentFiltroMetodo = this.value;
        fetchData(1);
    });

    // PAGINACIÓN AJAX
    document.addEventListener('click', function(e) {
        if (e.target.closest('.ajax-pagination a')) {
            e.preventDefault();
            fetchData(new URL(e.target.closest('a').href).searchParams.get('page'));
        }
    });

    function fetchData(page = 1) {
        const loading = document.getElementById('loadingOverlay');
        loading.classList.remove('hidden');
        loading.classList.add('flex');

        const params = new URLSearchParams({ 
            filtro_estado: currentFiltroEstado, 
            buscar: currentSearch, 
            filtro_metodo: currentFiltroMetodo,
            page: page 
        });

        fetch(`{{ route('admin.pagos.index') }}?${params.toString()}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.text())
        .then(html => { document.getElementById('tableContent').innerHTML = html; })
        .finally(() => {
            loading.classList.add('hidden');
            loading.classList.remove('flex');
        });
    }

    // ==========================================
    // VISOR DE COMPROBANTES
    // ==========================================
    function verComprobante(imgUrl, referencia) {
        document.getElementById('visor_imagen').src = imgUrl;
        document.getElementById('visor_referencia').innerText = referencia || 'SIN REF';
        
        const modal = document.getElementById('comprobanteModal');
        const backdrop = document.getElementById('comprobanteBackdrop');
        const panel = document.getElementById('comprobantePanel');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    function closeComprobante() {
        const modal = document.getElementById('comprobanteModal');
        const backdrop = document.getElementById('comprobanteBackdrop');
        const panel = document.getElementById('comprobantePanel');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.getElementById('visor_imagen').src = '';
        }, 300);
    }

    // ==========================================
    // VISOR DE DETALLES DEL PEDIDO (AJAX)
    // ==========================================
    function verDetallePedido(id) {
        const modal = document.getElementById('detalleModal');
        const backdrop = document.getElementById('detalleBackdrop');
        const panel = document.getElementById('detallePanel');
        const loader = document.getElementById('detalleLoader');
        const content = document.getElementById('detalleInyectado');
        
        document.getElementById('detalle_pedido_titulo').innerText = `Pedido #${String(id).padStart(6, '0')}`;
        
        content.innerHTML = '';
        loader.classList.remove('hidden');
        modal.classList.remove('hidden');
        
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);

        // Reutilizamos la misma ruta del Controlador de Pedidos que ya existe
        fetch(`/admin/pedidos/${id}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            content.innerHTML = html;
            loader.classList.add('hidden');
        })
        .catch(err => {
            content.innerHTML = `<div class="p-8 text-center text-red-500 font-bold"><span class="material-symbols-outlined text-4xl mb-2">error</span><br>Error al cargar el detalle del pedido.</div>`;
            loader.classList.add('hidden');
        });
    }

    function closeDetalleModal() {
        const modal = document.getElementById('detalleModal');
        const backdrop = document.getElementById('detalleBackdrop');
        const panel = document.getElementById('detallePanel');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    // ==========================================
    // APROBAR / RECHAZAR PAGOS (SWEETALERT2)
    // ==========================================
    function aprobarPago(id, referencia) {
        Swal.fire({
            title: '¿Aprobar Pago?',
            html: `Verificaste que los fondos de la ref <b>${referencia}</b> están en la cuenta bancaria.<br><br><span class="text-sm text-gray-500">Esto autorizará a logística para despachar el pedido.</span>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a', 
            cancelButtonColor: '#9ca3af',
            confirmButtonText: '<span class="material-symbols-outlined align-middle mr-1 text-[18px]">check_circle</span> Aprobar y Despachar',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                enviarResolucionPago(`/admin/pagos/${id}/aprobar`);
            }
        });
    }

    function rechazarPago(id, referencia) {
        Swal.fire({
            title: '¿Rechazar Pago?',
            text: `El pago con referencia ${referencia} será marcado como inválido. El pedido no será despachado.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', 
            cancelButtonColor: '#9ca3af',
            confirmButtonText: '<span class="material-symbols-outlined align-middle mr-1 text-[18px]">cancel</span> Rechazar Pago',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                enviarResolucionPago(`/admin/pagos/${id}/rechazar`);
            }
        });
    }

    function enviarResolucionPago(url) {
        Swal.fire({
            title: 'Procesando...',
            text: 'Actualizando base de datos e inventario.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Error en el servidor');
            return data;
        })
        .then(data => {
            Swal.close();
            showToast(data.message, 'success');
            fetchData(1); 
        })
        .catch(error => {
            Swal.fire('Error', error.message, 'error');
        });
    }

    // TOAST
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
</script>
@endpush
@endsection