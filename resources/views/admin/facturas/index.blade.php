@extends('layouts.admin')

@section('title', 'Facturación - Agrosuministros Rodríguez')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up pb-10">
            
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('admin.dashboard') }}" class="w-10 h-10 flex-shrink-0 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:text-teal-600 hover:border-teal-200 transition-colors shadow-sm">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div>
                    <h2 class="text-2xl font-black text-agro-dark flex items-center gap-2">
                        <span class="material-symbols-outlined text-teal-600 text-[32px]">receipt</span>
                        Listado de Facturas
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Control fiscal de las ventas generadas en tienda y web.</p>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-2 mb-6">
                <div class="flex overflow-x-auto custom-scrollbar border-b border-gray-100 px-4 pt-2 gap-6">
                    <button class="status-tab active pb-3 text-sm font-black border-b-2 border-teal-600 text-agro-dark whitespace-nowrap" data-estado="todas">Todas</button>
                    <button class="status-tab pb-3 text-sm font-bold border-b-2 border-transparent text-gray-700 hover:text-gray-700 whitespace-nowrap" data-estado="emitida">Emitidas Válidas</button>
                    <button class="status-tab pb-3 text-sm font-bold border-b-2 border-transparent text-gray-700 hover:text-gray-700 whitespace-nowrap" data-estado="anulada">Anuladas</button>
                </div>

                <div class="p-4 bg-gray-50/50 rounded-b-2xl">
                    <div class="w-full relative">
                        <span class="material-symbols-outlined absolute left-3 top-3 text-gray-700">search</span>
                        <input type="text" id="searchInput" class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all outline-none font-medium text-agro-dark" placeholder="Buscar por Nº Factura, Cliente o RIF...">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 relative" id="tableContainer">
                <div id="loadingOverlay" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-20 hidden items-center justify-center rounded-3xl">
                    <span class="material-symbols-outlined animate-spin text-teal-600 text-4xl">autorenew</span>
                </div>
                <div id="tableContent">
                    @include('admin.facturas.partials._table')
                </div>
            </div>
        </div>
    </main>
</div>

{{-- MODAL VISOR DE FACTURA (FORMATO TICKET) --}}
<div id="facturaModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity opacity-0" id="facturaBackdrop" onclick="closeFacturaModal()"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-center p-3 sm:p-4">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-md max-h-[92vh] flex flex-col opacity-0 scale-95" id="facturaPanel">
            
            <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex flex-wrap sm:flex-nowrap justify-between items-center gap-2 bg-slate-50/90 sticky top-0 z-20">
                <div class="flex items-center gap-2 sm:gap-2.5">
                    <span class="material-symbols-outlined text-[#1e549e] text-2xl">receipt_long</span>
                    <div>
                        <h3 class="text-sm font-black text-slate-800 leading-tight">Ticket de Venta</h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Vista Previa 80mm</p>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <button id="btnTicket" onclick="imprimirTicket()" class="text-white bg-[#1e549e] hover:bg-[#164079] px-2.5 sm:px-3 py-1.5 rounded-xl transition-all flex items-center gap-1 font-bold text-xs shadow-xs hover:scale-105 active:scale-95" title="Imprimir Ticket Térmico 80mm">
                        <span class="material-symbols-outlined text-[15px]">receipt</span>
                        <span>Ticket</span>
                    </button>
                    <button id="btnA4" onclick="imprimirA4()" class="text-slate-700 hover:text-[#1e549e] bg-white border border-slate-200 hover:border-[#1e549e] px-2 sm:px-2.5 py-1.5 rounded-xl transition-all flex items-center gap-1 font-bold text-xs shadow-2xs hover:scale-105 active:scale-95" title="Imprimir Factura en Formato A4">
                        <span class="material-symbols-outlined text-[15px]">print</span>
                        <span class="hidden xs:inline">Formato</span> <span>A4</span>
                    </button>
                    <button type="button" onclick="closeFacturaModal()" class="text-slate-400 hover:text-rose-500 p-1.5 rounded-xl hover:bg-slate-100 transition-colors" title="Cerrar">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>
            </div>

            <div id="facturaContent" class="overflow-y-auto custom-scrollbar flex-1 relative bg-slate-100/80 p-4 sm:p-5">
                <div id="facturaLoader" class="absolute inset-0 bg-slate-100/95 z-10 flex flex-col items-center justify-center">
                    <span class="material-symbols-outlined animate-spin text-[#1e549e] text-3xl mb-2">sync</span>
                    <p class="font-bold text-xs text-slate-500">Cargando ticket...</p>
                </div>
                <div id="facturaInyectada" class="print-area"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@push('scripts')
<script>
    let currentFiltroEstado = 'todas';
    let currentSearch = '';
    let searchTimeout;

    document.querySelectorAll('.status-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.status-tab').forEach(t => {
                t.classList.remove('border-teal-600', 'text-agro-dark', 'active');
                t.classList.add('border-transparent', 'text-gray-700');
            });
            this.classList.remove('border-transparent', 'text-gray-700');
            this.classList.add('border-teal-600', 'text-agro-dark', 'active');
            currentFiltroEstado = this.getAttribute('data-estado');
            fetchData(1);
        });
    });

    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        currentSearch = this.value;
        searchTimeout = setTimeout(() => fetchData(1), 400); 
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.ajax-pagination a')) {
            e.preventDefault();
            fetchData(new URL(e.target.closest('a').href).searchParams.get('page'));
        }
    });

    function fetchData(page = 1) {
        const loading = document.getElementById('loadingOverlay');
        loading.classList.remove('hidden'); loading.classList.add('flex');

        const params = new URLSearchParams({ filtro_estado: currentFiltroEstado, buscar: currentSearch, page: page });

        fetch(`{{ route('admin.facturas.index') }}?${params.toString()}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.text())
        .then(html => document.getElementById('tableContent').innerHTML = html)
        .finally(() => { loading.classList.add('hidden'); loading.classList.remove('flex'); });
    }

    let currentFacturaId = null;

    function imprimirTicket() {
        if (currentFacturaId) {
            window.open(`/admin/facturas/${currentFacturaId}/ticket`, '_blank');
        }
    }

    function imprimirA4() {
        if (currentFacturaId) {
            window.open(`/admin/facturas/${currentFacturaId}/imprimir`, '_blank');
        }
    }

    // Modal Visor de Factura
    function verFactura(id) {
        currentFacturaId = id;
        const modal = document.getElementById('facturaModal');
        const backdrop = document.getElementById('facturaBackdrop');
        const panel = document.getElementById('facturaPanel');
        const loader = document.getElementById('facturaLoader');
        const content = document.getElementById('facturaInyectada');
        
        content.innerHTML = '';
        loader.classList.remove('hidden');
        modal.classList.remove('hidden');
        
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);

        fetch(`/admin/facturas/${id}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.text())
        .then(html => {
            content.innerHTML = html;
            loader.classList.add('hidden');
        });
    }

    function closeFacturaModal() {
        const modal = document.getElementById('facturaModal');
        const backdrop = document.getElementById('facturaBackdrop');
        const panel = document.getElementById('facturaPanel');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    // Anular Factura
    function anularFactura(id, numero) {
        Swal.fire({
            title: '¿Anular Factura?',
            text: `Vas a anular fiscalmente la factura ${numero}. Esta acción no se puede revertir.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Sí, Anular',
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/facturas/${id}/anular`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message);
                    return data;
                })
                .then(data => {
                    Swal.fire({ title: 'Anulada', text: data.message, icon: 'success', timer: 2000, showConfirmButton: false });
                    fetchData(1);
                })
                .catch(error => Swal.fire('Error', error.message, 'error'));
            }
        });
    }


    
</script>
<style>
    @media print {
        body * { visibility: hidden; }
        .print-area, .print-area * { visibility: visible; }
        .print-area { position: absolute; left: 0; top: 0; width: 100%; }
        #facturaModal { background: none; }
        #facturaPanel { box-shadow: none; max-width: 100%; margin: 0; padding: 0; }
    }
</style>
@endpush
@endsection