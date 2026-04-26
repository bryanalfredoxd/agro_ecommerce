@extends('layouts.admin')

@section('title', 'Auditoría de Caja - Corpo Agrícola')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up pb-10">
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.dashboard') }}" class="w-10 h-10 flex-shrink-0 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:text-teal-600 hover:border-teal-200 transition-colors shadow-sm">
                        <span class="material-symbols-outlined">arrow_back</span>
                    </a>
                    <div>
                        <h2 class="text-2xl font-black text-agro-dark flex items-center gap-2">
                            <span class="material-symbols-outlined text-teal-600 text-[32px]">store</span>
                            Auditoría de Cajas (POS)
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Supervisa los turnos, ingresos y descuadres de los cajeros físicos.</p>
                    </div>
                </div>
                
                {{-- EL BOTÓN MÁGICO PARA IR A VENDER --}}
                <a href="{{ route('admin.pos.index') }}" class="inline-flex items-center justify-center gap-2 bg-teal-600 text-white px-6 py-3 rounded-xl font-black hover:bg-teal-700 hover:shadow-lg hover:shadow-teal-500/30 transition-all transform hover:-translate-y-0.5">
                    <span class="material-symbols-outlined text-[24px]">point_of_sale</span>
                    Ir al Terminal de Ventas (POS)
                </a>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-2 mb-6">
                <div class="flex overflow-x-auto custom-scrollbar px-4 pt-2 gap-6">
                    <button class="status-tab active pb-3 text-sm font-black border-b-2 border-teal-600 text-agro-dark whitespace-nowrap" data-estado="todas">Todos los Turnos</button>
                    <button class="status-tab pb-3 text-sm font-bold border-b-2 border-transparent text-gray-700 hover:text-gray-700 whitespace-nowrap" data-estado="abierta">Turnos Abiertos</button>
                    <button class="status-tab pb-3 text-sm font-bold border-b-2 border-transparent text-gray-700 hover:text-gray-700 whitespace-nowrap" data-estado="cerrada">Turnos Cerrados</button>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 relative" id="tableContainer">
                <div id="loadingOverlay" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-20 hidden items-center justify-center rounded-3xl">
                    <span class="material-symbols-outlined animate-spin text-teal-600 text-4xl">autorenew</span>
                </div>
                <div id="tableContent">
                    @include('admin.caja_diaria.partials._table')
                </div>
            </div>
        </div>
    </main>
</div>

{{-- MODAL VISOR DE MOVIMIENTOS --}}
<div id="movimientosModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="movBackdrop" onclick="closeMovModal()"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-center p-4">
        <div class="relative transform overflow-hidden sm:rounded-3xl rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-2xl max-h-[90vh] flex flex-col opacity-0 scale-95" id="movPanel">
            
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white sticky top-0 z-20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-teal-50 text-teal-600 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined">receipt_long</span>
                    </div>
                    <h3 class="text-lg font-black text-agro-dark leading-none">Corte de Caja (Turno)</h3>
                </div>
                <button type="button" onclick="closeMovModal()" class="text-gray-700 hover:text-red-500 bg-gray-50 border border-gray-200 hover:border-red-200 p-2 rounded-xl transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div id="movContent" class="overflow-y-auto custom-scrollbar flex-1 relative">
                <div id="movLoader" class="absolute inset-0 bg-white z-10 flex flex-col items-center justify-center p-12">
                    <span class="material-symbols-outlined animate-spin text-teal-600 text-4xl mb-4">autorenew</span>
                    <p class="font-bold text-gray-500">Cargando movimientos...</p>
                </div>
                <div id="movInyectado"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentFiltroEstado = 'todas';

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

    document.addEventListener('click', function(e) {
        if (e.target.closest('.ajax-pagination a')) {
            e.preventDefault();
            fetchData(new URL(e.target.closest('a').href).searchParams.get('page'));
        }
    });

    function fetchData(page = 1) {
        const loading = document.getElementById('loadingOverlay');
        loading.classList.remove('hidden'); loading.classList.add('flex');

        fetch(`{{ route('admin.caja_diaria.index') }}?filtro_estado=${currentFiltroEstado}&page=${page}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.text())
        .then(html => document.getElementById('tableContent').innerHTML = html)
        .finally(() => { loading.classList.add('hidden'); loading.classList.remove('flex'); });
    }

    // Modal de Movimientos
    function verMovimientos(id) {
        const modal = document.getElementById('movimientosModal');
        const backdrop = document.getElementById('movBackdrop');
        const panel = document.getElementById('movPanel');
        const loader = document.getElementById('movLoader');
        const content = document.getElementById('movInyectado');
        
        content.innerHTML = '';
        loader.classList.remove('hidden');
        modal.classList.remove('hidden');
        
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);

        fetch(`/admin/caja-diaria/${id}/movimientos`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.text())
        .then(html => {
            content.innerHTML = html;
            loader.classList.add('hidden');
        });
    }

    function closeMovModal() {
        const modal = document.getElementById('movimientosModal');
        const backdrop = document.getElementById('movBackdrop');
        const panel = document.getElementById('movPanel');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }
</script>
@endpush
@endsection