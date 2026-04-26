@extends('layouts.admin')

@section('title', 'Histórico de Precios - Corpo Agrícola')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up pb-20">
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.dashboard') }}" class="w-10 h-10 flex-shrink-0 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:text-green-600 hover:border-green-200 transition-colors shadow-sm">
                        <span class="material-symbols-outlined">arrow_back</span>
                    </a>
                    <div>
                        <h2 class="text-2xl font-black text-agro-dark flex items-center gap-2">
                            <span class="material-symbols-outlined text-green-600 text-[32px]">history</span>
                            Auditoría de Precios
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Registro inmutable de todas las variaciones de precio en el catálogo.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-t-3xl shadow-sm border border-gray-100 p-4 relative z-10">
                <div class="relative w-full max-w-md">
                    <span class="material-symbols-outlined absolute left-3 top-3 text-gray-700">search</span>
                    <input type="text" id="searchInput" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all outline-none font-medium text-agro-dark" placeholder="Buscar producto por nombre o SKU...">
                </div>
            </div>

            <div class="bg-white rounded-b-3xl shadow-sm border border-t-0 border-gray-100 relative" id="tableContainer">
                <div id="loadingOverlay" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-20 hidden items-center justify-center rounded-b-3xl">
                    <span class="material-symbols-outlined animate-spin text-green-600 text-4xl">autorenew</span>
                </div>
                <div id="tableContent">
                    @include('admin.historico_precios.partials._table')
                </div>
            </div>

        </div>
    </main>
</div>

@push('scripts')
<script>
    let currentSearch = '';
    let searchTimeout;

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
        
        fetch(`{{ route('admin.historico_precios.index') }}?buscar=${currentSearch}&page=${page}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => document.getElementById('tableContent').innerHTML = html)
        .finally(() => { loading.classList.add('hidden'); loading.classList.remove('flex'); });
    }
</script>
@endpush
@endsection