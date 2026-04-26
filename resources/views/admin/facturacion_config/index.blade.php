@extends('layouts.admin')

@section('title', 'Configuración de Facturación - Corpo Agrícola')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up pb-20">
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.dashboard') }}" class="w-10 h-10 flex-shrink-0 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:text-teal-600 hover:border-teal-200 transition-colors shadow-sm">
                        <span class="material-symbols-outlined">arrow_back</span>
                    </a>
                    <div>
                        <h2 class="text-2xl font-black text-agro-dark flex items-center gap-2">
                            <span class="material-symbols-outlined text-teal-600 text-[32px]">settings</span>
                            Series y Correlativos
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Administra los prefijos y números de inicio de tus documentos fiscales.</p>
                    </div>
                </div>
                <button onclick="openModal()" class="inline-flex items-center justify-center gap-2 bg-teal-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-teal-700 hover:shadow-lg hover:shadow-teal-500/30 transition-all transform hover:-translate-y-0.5">
                    <span class="material-symbols-outlined text-[20px]">add_circle</span>
                    Nueva Serie
                </button>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 relative" id="tableContainer">
                <div id="loadingOverlay" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-20 hidden items-center justify-center rounded-3xl">
                    <span class="material-symbols-outlined animate-spin text-teal-600 text-4xl">autorenew</span>
                </div>
                <div id="tableContent">
                    @include('admin.facturacion_config.partials._table')
                </div>
            </div>

        </div>
    </main>
</div>

{{-- MODAL CREAR / EDITAR --}}
<div id="serieModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="modalBackdrop" onclick="closeModal()"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-center p-4">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-lg opacity-0 scale-95 flex flex-col" id="modalPanel">
            
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-black text-agro-dark leading-none" id="modalTitle">Nueva Serie de Facturación</h3>
                <button type="button" onclick="closeModal()" class="text-gray-700 hover:text-red-500 bg-white p-1 rounded-lg border border-gray-200 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form id="serieForm" onsubmit="saveSerie(event)">
                <input type="hidden" id="ajuste_id">
                
                <div class="p-6 space-y-5">
                    
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-2 ml-1">Prefijo / Serie <span class="text-red-500">*</span></label>
                            <input type="text" id="serie" name="serie" placeholder="Ej: F, NE, A" required maxlength="10" class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-teal-500 focus:bg-white focus:ring-2 focus:ring-teal-500/20 transition-all font-black text-agro-dark outline-none uppercase tracking-widest">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-2 ml-1">Impuesto a aplicar (%) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 font-bold text-gray-700">%</span>
                                <input type="number" id="porcentaje_iva" name="porcentaje_iva" step="0.01" min="0" max="100" value="16.00" required class="w-full h-12 pl-10 pr-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-teal-500 focus:bg-white focus:ring-2 focus:ring-teal-500/20 transition-all font-bold text-agro-dark outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="bg-teal-50 p-4 rounded-2xl border border-teal-100">
                        <label class="block text-[10px] font-black text-teal-600 uppercase tracking-widest mb-2 ml-1">Siguiente Número Correlativo <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-3.5 font-bold text-teal-600">#</span>
                            <input type="number" id="proximo_numero" name="proximo_numero" min="1" value="1" required class="w-full h-12 pl-10 pr-4 rounded-xl bg-white border border-teal-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all font-mono font-black text-xl text-teal-800 outline-none">
                        </div>
                        <p class="text-[10px] text-teal-700 font-bold mt-2 ml-1">¡Cuidado! Si modificas este número en una serie que ya está en uso, podrías generar saltos contables.</p>
                    </div>

                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-5 py-2.5 rounded-xl font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 text-sm">Cancelar</button>
                    <button type="submit" id="btnSave" class="px-6 py-2.5 rounded-xl font-bold text-white bg-teal-600 hover:bg-teal-700 shadow-sm text-sm flex items-center gap-2 transition-all">
                        <span class="material-symbols-outlined text-[18px]">save</span> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="toast-container" class="fixed bottom-6 right-6 z-[999] flex flex-col gap-2"></div>

@push('scripts')
<script>
    function fetchData(page = 1) {
        const loading = document.getElementById('loadingOverlay');
        loading.classList.remove('hidden'); loading.classList.add('flex');
        
        fetch(`{{ route('admin.facturacion_config.index') }}?page=${page}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => document.getElementById('tableContent').innerHTML = html)
        .finally(() => { loading.classList.add('hidden'); loading.classList.remove('flex'); });
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('.ajax-pagination a')) {
            e.preventDefault();
            fetchData(new URL(e.target.closest('a').href).searchParams.get('page'));
        }
    });

    function openModal(ajuste = null) {
        const form = document.getElementById('serieForm');
        form.reset();
        document.getElementById('ajuste_id').value = '';
        document.getElementById('modalTitle').innerText = 'Nueva Serie de Facturación';
        
        if (ajuste) {
            document.getElementById('modalTitle').innerText = 'Editar Serie';
            document.getElementById('ajuste_id').value = ajuste.id;
            document.getElementById('serie').value = ajuste.serie;
            document.getElementById('proximo_numero').value = ajuste.proximo_numero;
            document.getElementById('porcentaje_iva').value = ajuste.porcentaje_iva;
        }

        const modal = document.getElementById('serieModal');
        const backdrop = document.getElementById('modalBackdrop');
        const panel = document.getElementById('modalPanel');
        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    function closeModal() {
        const modal = document.getElementById('serieModal');
        const backdrop = document.getElementById('modalBackdrop');
        const panel = document.getElementById('modalPanel');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    function saveSerie(e) {
        e.preventDefault();
        const id = document.getElementById('ajuste_id').value;
        const btn = document.getElementById('btnSave');
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin">autorenew</span> Guardando...';
        btn.disabled = true;

        let formData = new FormData(e.target);
        if (id) formData.append('_method', 'PUT');
        
        let url = id ? `/admin/configuracion-facturacion/${id}` : `{{ route('admin.facturacion_config.store') }}`;

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Error en validación');
            return data;
        })
        .then(data => {
            closeModal();
            showToast(data.message, 'success');
            fetchData(1);
        })
        .catch(err => showToast(err.message, 'error'))
        .finally(() => { btn.innerHTML = originalText; btn.disabled = false; });
    }

    function toggleSerie(id) {
        fetch(`/admin/configuracion-facturacion/${id}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message);
            return data;
        })
        .then(data => {
            showToast(data.message, 'success');
            fetchData(document.querySelector('.ajax-pagination .active span')?.innerText || 1);
        })
        .catch(err => {
            showToast(err.message, 'error');
            // Revertir el toggle visualmente si falló
            fetchData(document.querySelector('.ajax-pagination .active span')?.innerText || 1);
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
        }, 3000);
    }
</script>
@endpush
@endsection