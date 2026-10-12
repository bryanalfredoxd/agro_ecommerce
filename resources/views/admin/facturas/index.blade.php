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
                    <button type="button" onclick="abrirModalDevolucion(currentFacturaId)" class="text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200 px-2.5 sm:px-3 py-1.5 rounded-xl transition-all flex items-center gap-1 font-bold text-xs shadow-2xs hover:scale-105 active:scale-95" title="Procesar Devolución de esta Factura">
                        <span class="material-symbols-outlined text-[15px] text-amber-600">assignment_return</span>
                        <span class="hidden xs:inline">Devolver</span>
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

{{-- MODAL PROCESAR DEVOLUCIÓN Y REEMBOLSO --}}
<div id="modalProcesarDevolucion" class="fixed inset-0 z-[110] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity opacity-0" id="devolucionModalBackdrop" onclick="cerrarModalDevolucion()"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-center p-3 sm:p-4">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-4xl max-h-[92vh] flex flex-col opacity-0 scale-95" id="devolucionModalPanel">
            
            {{-- Header --}}
            <div class="px-5 py-3.5 border-b border-amber-600/30 flex justify-between items-center bg-gradient-to-r from-amber-500 to-amber-600 text-white sticky top-0 z-20 shadow-xs" style="background: linear-gradient(135deg, #d97706, #b45309); color: #ffffff;">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-xs text-white">
                        <span class="material-symbols-outlined text-2xl">assignment_return</span>
                    </div>
                    <div>
                        <h3 class="text-base font-black leading-tight flex items-center gap-2 text-white">
                            <span>Procesar Devolución / Reembolso</span>
                            <span id="devFacturaBadge" class="bg-white/20 text-white px-2 py-0.5 rounded-lg text-xs font-mono font-bold"></span>
                        </h3>
                        <p class="text-xs text-amber-100 font-medium">Reintegro de productos y devolución del dinero de la compra</p>
                    </div>
                </div>
                <button type="button" onclick="cerrarModalDevolucion()" class="text-white hover:bg-white/20 p-1.5 rounded-xl transition-colors">
                    <span class="material-symbols-outlined text-2xl">close</span>
                </button>
            </div>

            {{-- Body --}}
            <div class="overflow-y-auto custom-scrollbar flex-1 p-5 sm:p-6 relative bg-slate-50 text-slate-800">
                <div id="devModalLoader" class="absolute inset-0 bg-white/95 z-20 flex flex-col items-center justify-center">
                    <span class="material-symbols-outlined animate-spin text-amber-600 text-4xl mb-2">sync</span>
                    <p class="font-bold text-xs text-slate-700">Cargando detalles de la factura...</p>
                </div>

                {{-- Banner Resumen Factura --}}
                <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs mb-5">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                        <div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Cliente</span>
                            <span id="devClienteNombre" class="font-black text-slate-900 truncate block"></span>
                            <span id="devClienteDoc" class="text-[10px] font-mono text-slate-600 font-bold"></span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Fecha Emisión</span>
                            <span id="devFechaEmision" class="font-bold text-slate-900"></span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Total Facturado</span>
                            <span id="devTotalFactura" class="font-black text-emerald-700 text-sm"></span>
                            <span id="devTotalFacturaBs" class="text-[10px] font-bold text-slate-600 block"></span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Tasa BCV</span>
                            <span id="devTasaBcv" class="font-bold text-slate-900"></span>
                            <span id="devYaDevueltoAlert" class="text-[10px] font-bold text-amber-700 block mt-0.5"></span>
                        </div>
                    </div>
                </div>

                {{-- Formulario de Devolución --}}
                <form id="formProcesarDevolucion" onsubmit="enviarDevolucion(event)">
                    @csrf
                    <input type="hidden" id="devFacturaId" value="">

                    {{-- Tabla de Productos a Devolver --}}
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden mb-5">
                        <div class="px-4 py-3 bg-slate-100 border-b border-slate-200 flex items-center justify-between">
                            <span class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[18px] text-amber-600">inventory_2</span>
                                Selección de Productos a Devolver
                            </span>
                            <span class="text-[11px] text-slate-600 font-medium">Especifica cantidades y estado de la mercancía</span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200 text-[10px] text-slate-700 uppercase tracking-wider font-bold">
                                        <th class="px-4 py-2.5">Producto</th>
                                        <th class="px-3 py-2.5 text-center">Cant. Comprada</th>
                                        <th class="px-3 py-2.5 text-center">Cant. Devolver</th>
                                        <th class="px-3 py-2.5">Motivo del Ítem</th>
                                        <th class="px-3 py-2.5">Estado Producto</th>
                                        <th class="px-4 py-2.5 text-right">Subtotal Reembolso</th>
                                    </tr>
                                </thead>
                                <tbody id="devItemsTableBody" class="divide-y divide-slate-100 bg-white">
                                    {{-- Inyectado dinámicamente con JS --}}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Opciones del Reembolso --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                        {{-- Motivo General --}}
                        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
                            <label class="block text-xs font-black text-slate-900 mb-1.5 uppercase tracking-wider">
                                Motivo General de la Devolución <span class="text-rose-500">*</span>
                            </label>
                            <select id="devMotivoGeneralSelect" onchange="toggleMotivoOtro(this.value)" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none mb-2">
                                <option value="Cliente arrepentido o insatisfecho">Cliente arrepentido o insatisfecho con el producto</option>
                                <option value="Producto dañado o con defecto de fábrica">Producto dañado o con defecto de fábrica</option>
                                <option value="Error en despacho / producto equivocado">Error en despacho / producto equivocado</option>
                                <option value="Fecha de vencimiento próxima o caducado">Fecha de vencimiento próxima o caducado</option>
                                <option value="Sobrante de siembra o aplicación no requerida">Sobrante de siembra o aplicación no requerida</option>
                                <option value="OTRO">Otro motivo específico...</option>
                            </select>
                            <input type="text" id="devMotivoGeneralCustom" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none hidden" placeholder="Escriba el motivo detallado de la devolución...">
                        </div>

                        {{-- Método de Reembolso --}}
                        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
                            <label class="block text-xs font-black text-slate-900 mb-1.5 uppercase tracking-wider">
                                Método para Reembolsar el Dinero <span class="text-rose-500">*</span>
                            </label>
                            <select id="devMetodoReembolso" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
                                <option value="efectivo_usd">Efectivo Divisas ($)</option>
                                <option value="efectivo_bs">Efectivo Bolívares (Bs)</option>
                                <option value="pago_movil">Pago Móvil (Bs)</option>
                                <option value="transferencia">Transferencia Bancaria</option>
                                <option value="punto_venta">Reverso Punto de Venta / Débito</option>
                                <option value="nota_credito">Nota de Crédito para próxima compra</option>
                                <option value="otro">Otro método acordado</option>
                            </select>
                            <p class="text-[10px] text-slate-500 mt-1.5 font-medium">Si se entrega efectivo y hay caja abierta, se asentará el egreso correspondiente.</p>
                        </div>
                    </div>

                    {{-- Reingreso a Stock y Observaciones --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                        {{-- Switch Reingresar Stock --}}
                        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs flex flex-col justify-between">
                            <div>
                                <label class="flex items-center gap-2.5 cursor-pointer">
                                    <input type="checkbox" id="devReingresarStock" checked class="w-4 h-4 text-amber-600 rounded focus:ring-amber-500 border-slate-300">
                                    <span class="text-xs font-black text-slate-900">Reingresar productos al Stock / Inventario físico</span>
                                </label>
                                <p class="text-[11px] text-slate-600 mt-1.5 pl-6 leading-relaxed">
                                    Al estar marcado, el inventario aumentará automáticamente con las unidades devueltas. Desmárcalo si la mercancía está vencida, deteriorada o se enviará a descarte.
                                </p>
                            </div>
                        </div>

                        {{-- Observaciones y Anotaciones --}}
                        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
                            <label class="block text-xs font-black text-slate-900 mb-1 uppercase tracking-wider">
                                Anotaciones y Observaciones del Reembolso
                            </label>
                            <textarea id="devObservaciones" rows="2" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none resize-none" placeholder="Anotaciones: estado del empaque, sello de seguridad, persona que autoriza..."></textarea>
                        </div>
                    </div>

                    {{-- Card Totales a Reembolsar --}}
                    <div class="bg-slate-900 text-white rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-md" style="background-color: #0f172a; color: #ffffff;">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-500/30 text-amber-400 flex items-center justify-center">
                                <span class="material-symbols-outlined text-2xl">payments</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider block">Total Calculado a Reembolsar</span>
                                <div class="text-xs text-slate-300">
                                    <span id="devTotalItemsCount" class="font-bold text-amber-400">0</span> ítem(s) seleccionados
                                </div>
                            </div>
                        </div>

                        <div class="text-right flex items-center sm:items-end flex-col">
                            <div class="text-2xl sm:text-3xl font-black text-emerald-400 tracking-tight" id="devPreviewUsd">$0.00</div>
                            <div class="text-xs font-bold text-slate-300" id="devPreviewVes">Bs. 0.00</div>
                        </div>
                    </div>

                    {{-- Botones de Acción --}}
                    <div class="mt-5 flex justify-end gap-3">
                        <button type="button" onclick="cerrarModalDevolucion()" class="px-5 py-2.5 rounded-xl border-2 border-slate-300 bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs transition-colors shadow-2xs">
                            Cancelar
                        </button>
                        <button type="submit" id="btnDevSubmit" disabled class="px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md hover:shadow-lg transition-all flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" style="background-color: #d97706; color: #ffffff;">
                            <span class="material-symbols-outlined text-[18px]">assignment_return</span>
                            <span>Confirmar Devolución</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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


    // ==========================================
    // SISTEMA COMPLETO DE DEVOLUCIONES
    // ==========================================
    let devCurrentItems = [];
    let devTasaBcv = 1.0;

    function abrirModalDevolucion(id) {
        if (!id) return;
        currentFacturaId = id;

        // Si el modal del visor de ticket está abierto, lo cerramos
        closeFacturaModal();

        const modal = document.getElementById('modalProcesarDevolucion');
        const backdrop = document.getElementById('devolucionModalBackdrop');
        const panel = document.getElementById('devolucionModalPanel');
        const loader = document.getElementById('devModalLoader');

        document.getElementById('devFacturaId').value = id;
        loader.classList.remove('hidden');
        modal.classList.remove('hidden');

        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);

        fetch(`/admin/facturas/${id}/devolucion/datos`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Error al obtener datos');
            return data;
        })
        .then(data => {
            document.getElementById('devFacturaBadge').innerText = data.factura.numero_factura;
            document.getElementById('devClienteNombre').innerText = data.factura.cliente_nombre;
            document.getElementById('devClienteDoc').innerText = 'CI/RIF: ' + data.factura.cliente_documento;
            document.getElementById('devFechaEmision').innerText = data.factura.fecha_emision;
            document.getElementById('devTotalFactura').innerText = `$${parseFloat(data.factura.total_usd).toFixed(2)}`;
            document.getElementById('devTotalFacturaBs').innerText = `Bs. ${parseFloat(data.factura.total_ves).toFixed(2)}`;
            document.getElementById('devTasaBcv').innerText = `Bs. ${parseFloat(data.tasa_bcv_actual).toFixed(2)}`;
            
            if (data.factura.total_ya_devuelto_usd > 0) {
                document.getElementById('devYaDevueltoAlert').innerText = `Ya devuelto: $${data.factura.total_ya_devuelto_usd.toFixed(2)}`;
            } else {
                document.getElementById('devYaDevueltoAlert').innerText = '';
            }

            devTasaBcv = parseFloat(data.tasa_bcv_actual) || 1.0;
            devCurrentItems = data.items || [];

            renderItemsDevolucion(devCurrentItems);
            loader.classList.add('hidden');
        })
        .catch(err => {
            loader.classList.add('hidden');
            Swal.fire('Atención', err.message, 'warning');
            cerrarModalDevolucion();
        });
    }

    function renderItemsDevolucion(items) {
        const tbody = document.getElementById('devItemsTableBody');
        tbody.innerHTML = '';

        if (!items || items.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="p-6 text-center text-slate-500 font-bold">No hay ítems disponibles para devolver en esta factura.</td></tr>';
            recalcularTotalesDevolucion();
            return;
        }

        items.forEach((it, idx) => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50 transition-colors';
            
            const disp = parseFloat(it.cantidad_disponible);
            const precio = parseFloat(it.precio_unitario_usd);
            const disabled = disp <= 0 ? 'disabled' : '';

            tr.innerHTML = `
                <td class="px-4 py-3">
                    <div class="font-bold text-slate-900 text-xs">${it.nombre_producto}</div>
                    <div class="text-[10px] text-slate-600 flex items-center gap-2 mt-0.5">
                        <span class="font-mono font-semibold">SKU: ${it.sku}</span>
                        ${it.lote_codigo ? `<span class="bg-blue-100 text-blue-900 border border-blue-200 px-1 rounded font-mono font-bold">Lote: ${it.lote_codigo}</span>` : ''}
                        <span>•</span>
                        <span class="font-bold text-slate-700">$${precio.toFixed(2)} c/u</span>
                    </div>
                </td>
                <td class="px-3 py-3 text-center">
                    <div class="font-black text-slate-900 text-xs">${it.cantidad_original} ${it.unidad}</div>
                    ${it.ya_devuelto > 0 ? `<div class="text-[10px] text-rose-600 font-bold">(${it.ya_devuelto} devueltos)</div>` : ''}
                    <div class="text-[10px] text-emerald-700 font-extrabold mt-0.5">Disp: ${disp}</div>
                </td>
                <td class="px-3 py-3 text-center">
                    <div class="flex items-center justify-center gap-1.5">
                        <input type="number" 
                               id="devItemQty_${idx}" 
                               data-index="${idx}"
                               data-detalle-id="${it.pedido_detalle_id}"
                               data-precio="${precio}"
                               min="0" 
                               max="${disp}" 
                               step="any" 
                               value="0" 
                               ${disabled}
                               oninput="recalcularTotalesDevolucion()"
                               class="w-16 px-2 py-1 text-center font-black text-xs text-slate-900 bg-white border-2 border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
                        <button type="button" 
                                onclick="seleccionarTodoItem(${idx}, ${disp})" 
                                ${disabled}
                                class="px-2 py-1 text-[11px] font-black bg-amber-600 hover:bg-amber-700 text-white rounded-lg shadow-2xs transition-colors disabled:opacity-40"
                                style="background-color: #d97706; color: #ffffff;">
                            Todo
                        </button>
                    </div>
                </td>
                <td class="px-3 py-3">
                    <select id="devItemMotivo_${idx}" ${disabled} class="w-full px-2 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 outline-none">
                        <option value="cliente_arrepentido">Cliente Arrepentido</option>
                        <option value="producto_danado">Producto Dañado / Defecto</option>
                        <option value="no_corresponde">No Corresponde / Equivocado</option>
                        <option value="vencido">Vencido / Por Vencer</option>
                        <option value="error_despacho">Error en Despacho</option>
                    </select>
                </td>
                <td class="px-3 py-3">
                    <select id="devItemEstado_${idx}" ${disabled} class="w-full px-2 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 outline-none">
                        <option value="reincorporado">Reincorporado (Buen Estado)</option>
                        <option value="recibido">Recibido en Verificación</option>
                        <option value="descarte">Descarte / Desecho</option>
                        <option value="devuelto_proveedor">Devolver al Proveedor</option>
                    </select>
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="font-black text-slate-900 text-xs" id="devItemSubtotal_${idx}">$0.00</div>
                    <div class="text-[10px] text-slate-600 font-bold" id="devItemSubtotalBs_${idx}">Bs. 0.00</div>
                </td>
            `;

            tbody.appendChild(tr);
        });

        recalcularTotalesDevolucion();
    }

    function seleccionarTodoItem(idx, max) {
        const input = document.getElementById(`devItemQty_${idx}`);
        if (input) {
            input.value = max;
            recalcularTotalesDevolucion();
        }
    }

    function recalcularTotalesDevolucion() {
        let totalUsd = 0;
        let totalItemsCount = 0;

        devCurrentItems.forEach((it, idx) => {
            const input = document.getElementById(`devItemQty_${idx}`);
            const disp = parseFloat(it.cantidad_disponible);
            const precio = parseFloat(it.precio_unitario_usd);

            if (input) {
                let qty = parseFloat(input.value) || 0;
                if (qty < 0) qty = 0;
                if (qty > disp) {
                    qty = disp;
                    input.value = disp;
                }

                const subUsd = qty * precio;
                const subBs = subUsd * devTasaBcv;

                const subUsdElem = document.getElementById(`devItemSubtotal_${idx}`);
                const subBsElem = document.getElementById(`devItemSubtotalBs_${idx}`);

                if (subUsdElem) subUsdElem.innerText = `$${subUsd.toFixed(2)}`;
                if (subBsElem) subBsElem.innerText = `Bs. ${subBs.toFixed(2)}`;

                if (qty > 0) {
                    totalUsd += subUsd;
                    totalItemsCount += qty;
                }
            }
        });

        const totalVes = totalUsd * devTasaBcv;

        document.getElementById('devPreviewUsd').innerText = `$${totalUsd.toFixed(2)}`;
        document.getElementById('devPreviewVes').innerText = `Bs. ${totalVes.toFixed(2)}`;
        document.getElementById('devTotalItemsCount').innerText = totalItemsCount.toFixed(2).replace(/\.00$/, '');

        const submitBtn = document.getElementById('btnDevSubmit');
        if (submitBtn) {
            submitBtn.disabled = (totalUsd <= 0);
        }
    }

    function toggleMotivoOtro(val) {
        const customInput = document.getElementById('devMotivoGeneralCustom');
        if (val === 'OTRO') {
            customInput.classList.remove('hidden');
            customInput.focus();
        } else {
            customInput.classList.add('hidden');
        }
    }

    function cerrarModalDevolucion() {
        const modal = document.getElementById('modalProcesarDevolucion');
        const backdrop = document.getElementById('devolucionModalBackdrop');
        const panel = document.getElementById('devolucionModalPanel');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 250);
    }

    function enviarDevolucion(event) {
        event.preventDefault();

        const facturaId = document.getElementById('devFacturaId').value;
        const motivoSelect = document.getElementById('devMotivoGeneralSelect').value;
        const motivoCustom = document.getElementById('devMotivoGeneralCustom').value.trim();
        const motivoGeneral = (motivoSelect === 'OTRO' && motivoCustom) ? motivoCustom : motivoSelect;
        const metodoReembolso = document.getElementById('devMetodoReembolso').value;
        const reingresarStock = document.getElementById('devReingresarStock').checked;
        const observaciones = document.getElementById('devObservaciones').value.trim();

        const itemsPayload = [];
        devCurrentItems.forEach((it, idx) => {
            const input = document.getElementById(`devItemQty_${idx}`);
            const qty = parseFloat(input ? input.value : 0) || 0;
            if (qty > 0) {
                const motivoItem = document.getElementById(`devItemMotivo_${idx}`).value;
                const estadoItem = document.getElementById(`devItemEstado_${idx}`).value;
                itemsPayload.push({
                    pedido_detalle_id: it.pedido_detalle_id,
                    cantidad: qty,
                    motivo_devolucion: motivoItem,
                    estado_producto: estadoItem
                });
            }
        });

        if (itemsPayload.length === 0) {
            Swal.fire('Atención', 'Debes seleccionar al menos un producto con cantidad mayor a cero.', 'warning');
            return;
        }

        const previewUsd = document.getElementById('devPreviewUsd').innerText;
        const previewVes = document.getElementById('devPreviewVes').innerText;

        Swal.fire({
            title: '¿Confirmar Devolución?',
            html: `
                <div class="text-left text-xs space-y-2 mt-2 text-slate-800">
                    <p>Se procesará la devolución por <strong class="text-slate-900">${previewUsd}</strong> (${previewVes}).</p>
                    <p><strong>Método de Reembolso:</strong> <span class="text-slate-900 font-bold">${metodoReembolso.replace('_', ' ').toUpperCase()}</span></p>
                    <p><strong>Reingreso a Stock:</strong> <span class="font-bold ${reingresarStock ? 'text-emerald-700' : 'text-rose-700'}">${reingresarStock ? 'SÍ, reincorporar unidades' : 'NO (Descarte/Garantía)'}</span></p>
                    <p class="text-slate-500 italic">Esta operación generará un comprobante de auditoría numerado y actualizará el estado de la factura.</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d97706',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, Procesar Devolución',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                const btnSubmit = document.getElementById('btnDevSubmit');
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="material-symbols-outlined animate-spin text-[18px]">sync</span> Procesando...';

                fetch(`/admin/facturas/${facturaId}/devolucion`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        items: itemsPayload,
                        motivo_general: motivoGeneral,
                        metodo_reembolso: metodoReembolso,
                        reingresar_stock: reingresarStock,
                        observaciones: observaciones
                    })
                })
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Error en el servidor');
                    return data;
                })
                .then(data => {
                    cerrarModalDevolucion();
                    fetchData(1);

                    Swal.fire({
                        title: '¡Devolución Procesada!',
                        html: `
                            <p class="text-sm text-slate-800 mb-3">${data.message}</p>
                            <p class="text-xs text-slate-600 font-medium">Código de Devolución: <strong class="font-mono text-amber-900 bg-amber-100 px-2 py-0.5 rounded border border-amber-300">${data.devolucion.codigo}</strong></p>
                        `,
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#1e549e',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Imprimir Comprobante (80mm)',
                        cancelButtonText: 'Cerrar',
                        customClass: { popup: 'rounded-2xl' }
                    }).then((r) => {
                        if (r.isConfirmed && data.devolucion && data.devolucion.id) {
                            window.open(`/admin/devoluciones/${data.devolucion.id}/comprobante`, '_blank');
                        }
                    });
                })
                .catch(error => {
                    Swal.fire('Error', error.message, 'error');
                })
                .finally(() => {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<span class="material-symbols-outlined text-[18px]">assignment_return</span><span>Confirmar Devolución</span>';
                });
            }
        });
    }
</script>
@endpush

@push('styles')
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