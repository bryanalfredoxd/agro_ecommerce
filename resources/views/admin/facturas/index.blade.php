@extends('layouts.admin')

@section('title', 'Facturación - Corpo Agrícola')

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
                    <button class="status-tab pb-3 text-sm font-bold border-b-2 border-transparent text-gray-400 hover:text-gray-700 whitespace-nowrap" data-estado="emitida">Emitidas Válidas</button>
                    <button class="status-tab pb-3 text-sm font-bold border-b-2 border-transparent text-gray-400 hover:text-gray-700 whitespace-nowrap" data-estado="anulada">Anuladas</button>
                </div>

                <div class="p-4 bg-gray-50/50 rounded-b-2xl">
                    <div class="w-full relative">
                        <span class="material-symbols-outlined absolute left-3 top-3 text-gray-400">search</span>
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

{{-- MODAL VISOR DE FACTURA --}}
<div id="facturaModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="facturaBackdrop" onclick="closeFacturaModal()"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-center p-4">
        <div class="relative transform overflow-hidden sm:rounded-3xl rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-3xl max-h-[90vh] flex flex-col opacity-0 scale-95" id="facturaPanel">
            
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 sticky top-0 z-20">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-teal-600 text-2xl">receipt_long</span>
                    <h3 class="text-lg font-black text-agro-dark leading-none">Detalle de Factura</h3>
                </div>
                <div class="flex gap-2">
                    {{-- NUEVO BOTÓN GENERAR PDF --}}
                    <button id="btnImprimir" onclick="descargarPDF()" class="text-gray-500 hover:text-teal-600 bg-white border border-gray-200 hover:border-teal-200 p-1.5 rounded-lg transition-colors flex items-center gap-1 px-3 font-bold text-sm" title="Descargar PDF">
                        <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span> PDF
                    </button>
                    <button type="button" onclick="closeFacturaModal()" class="text-gray-400 hover:text-red-500 bg-white border border-gray-200 hover:border-red-200 p-1.5 rounded-lg transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
            </div>

            <div id="facturaContent" class="overflow-y-auto custom-scrollbar flex-1 relative bg-gray-100 p-4 sm:p-8">
                <div id="facturaLoader" class="absolute inset-0 bg-gray-100 z-10 flex flex-col items-center justify-center">
                    <span class="material-symbols-outlined animate-spin text-teal-600 text-4xl mb-4">autorenew</span>
                    <p class="font-bold text-gray-500">Cargando documento...</p>
                </div>
                <div id="facturaInyectada" class="print-area"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- LIBRERÍA PARA GENERAR PDF FIELES AL DISEÑO --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

@push('scripts')

<script>
    let currentFiltroEstado = 'todas';
    let currentSearch = '';
    let searchTimeout;

    document.querySelectorAll('.status-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.status-tab').forEach(t => {
                t.classList.remove('border-teal-600', 'text-agro-dark', 'active');
                t.classList.add('border-transparent', 'text-gray-400');
            });
            this.classList.remove('border-transparent', 'text-gray-400');
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

    // Modal Visor de Factura
    function verFactura(id) {
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

    // ==========================================
    // GENERADOR DE PDF (HTML a PDF)
    // ==========================================
    function descargarPDF() {
        const btn = document.getElementById('btnImprimir');
        const originalHtml = btn.innerHTML;
        
        // Estado de carga en el botón
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[18px]">autorenew</span> Procesando...';
        btn.disabled = true;

        // El elemento HTML que queremos convertir a PDF
        const elemento = document.getElementById('documentoFactura');
        
        // Configuraciones de calidad y formato
        const opciones = {
            margin:       [10, 10, 10, 10], // Márgenes en mm
            filename:     'Copia_Contable_Corpo_Agricola.pdf', // Nombre del archivo descargado
            image:        { type: 'jpeg', quality: 1 }, // Máxima calidad
            html2canvas:  { 
                scale: 3, // Escala x3 para que el texto se vea súper nítido al hacer zoom
                useCORS: true, 
                logging: false 
            },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        // Generar y descargar
        html2pdf().set(opciones).from(elemento).save().then(() => {
            // Restaurar el botón cuando termine
            btn.innerHTML = originalHtml;
            btn.disabled = false;
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