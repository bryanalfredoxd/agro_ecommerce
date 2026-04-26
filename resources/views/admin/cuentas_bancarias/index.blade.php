@extends('layouts.admin')

@section('title', 'Cuentas Bancarias - Corpo Agrícola')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up pb-20">
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.dashboard') }}" class="w-10 h-10 flex-shrink-0 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:text-blue-600 hover:border-blue-200 transition-colors shadow-sm">
                        <span class="material-symbols-outlined">arrow_back</span>
                    </a>
                    <div>
                        <h2 class="text-2xl font-black text-agro-dark flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-600 text-[32px]">account_balance</span>
                            Métodos de Pago
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Configura dónde te pagarán los clientes al comprar por la web.</p>
                    </div>
                </div>
                <button onclick="openModal()" class="inline-flex items-center justify-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-500/30 transition-all transform hover:-translate-y-0.5">
                    <span class="material-symbols-outlined text-[20px]">add_circle</span>
                    Añadir Método
                </button>
            </div>

            {{-- Filtros --}}
            <div class="bg-white rounded-t-3xl shadow-sm border border-gray-100 p-4 flex gap-4 relative z-10 flex-col sm:flex-row">
                <div class="flex-1 relative">
                    <span class="material-symbols-outlined absolute left-3 top-3 text-gray-700">search</span>
                    <input type="text" id="searchInput" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none font-medium text-agro-dark" placeholder="Buscar por banco, titular o correo...">
                </div>
                <div class="w-full sm:w-56 relative">
                    <select id="filtroTipo" class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none font-bold text-gray-600 appearance-none cursor-pointer">
                        <option value="todos">Todos los Métodos</option>
                        <option value="pago_movil">Pago Móvil</option>
                        <option value="zelle">Zelle</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="binance">Binance</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-3 text-gray-700 pointer-events-none">filter_list</span>
                </div>
            </div>

            <div class="bg-white rounded-b-3xl shadow-sm border border-t-0 border-gray-100 relative" id="tableContainer">
                <div id="loadingOverlay" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-20 hidden items-center justify-center rounded-b-3xl">
                    <span class="material-symbols-outlined animate-spin text-blue-600 text-4xl">autorenew</span>
                </div>
                <div id="tableContent">
                    @include('admin.cuentas_bancarias.partials._table')
                </div>
            </div>

        </div>
    </main>
</div>

{{-- MODAL CREAR / EDITAR --}}
<div id="cuentaModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="modalBackdrop" onclick="closeModal()"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-center p-4">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-2xl opacity-0 scale-95 flex flex-col max-h-[90vh]" id="modalPanel">
            
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-black text-agro-dark leading-none" id="modalTitle">Nuevo Método de Pago</h3>
                <button type="button" onclick="closeModal()" class="text-gray-700 hover:text-red-500 bg-white p-1 rounded-lg border border-gray-200 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form id="cuentaForm" onsubmit="saveCuenta(event)" class="overflow-y-auto custom-scrollbar flex-1">
                <input type="hidden" id="cuenta_id">
                
                <div class="p-6 space-y-5">
                    
                    {{-- Selector de Tipo (Controla la UI) --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-2 ml-1">Tipo de Operación <span class="text-red-500">*</span></label>
                        <select id="tipo_metodo" name="tipo_metodo" onchange="actualizarCamposVisibles()" required class="w-full h-12 px-4 rounded-xl bg-blue-50/50 border border-blue-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all font-black text-blue-800 outline-none cursor-pointer">
                            <option value="pago_movil">Pago Móvil</option>
                            <option value="zelle">Zelle</option>
                            <option value="transferencia">Transferencia Bancaria (Bs)</option>
                            <option value="binance">Binance Pay</option>
                            <option value="efectivo_usd">Efectivo en Tienda (USD)</option>
                            <option value="efectivo_bs">Efectivo en Tienda (Bs)</option>
                            <option value="punto_venta">Punto de Venta</option>
                        </select>
                    </div>

                    {{-- CAMPOS DINÁMICOS --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        
                        <div class="grupo-titular">
                            <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-2 ml-1">Nombre del Titular</label>
                            <input type="text" id="nombre_titular" name="nombre_titular" class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white outline-none font-medium text-agro-dark">
                        </div>

                        <div class="grupo-banco hidden">
                            <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-2 ml-1">Banco / Entidad</label>
                            <input type="text" id="banco_entidad" name="banco_entidad" placeholder="Ej: Banesco, Mercantil..." class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white outline-none font-medium text-agro-dark">
                        </div>

                        <div class="grupo-cuenta hidden">
                            <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-2 ml-1">Número de Cuenta</label>
                            <input type="text" id="numero_cuenta" name="numero_cuenta" class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white outline-none font-mono font-bold text-agro-dark tracking-widest">
                        </div>

                        <div class="grupo-telefono hidden">
                            <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-2 ml-1">Teléfono</label>
                            <input type="text" id="telefono" name="telefono" placeholder="0414..." class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white outline-none font-mono font-bold text-agro-dark">
                        </div>

                        <div class="grupo-identificacion hidden">
                            <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-2 ml-1">CI / RIF</label>
                            <input type="text" id="identificacion" name="identificacion" placeholder="V-12345678" class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white outline-none font-mono font-bold text-agro-dark uppercase">
                        </div>

                        <div class="grupo-email hidden">
                            <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-2 ml-1">Correo Electrónico (Zelle/Binance)</label>
                            <input type="email" id="email" name="email" class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white outline-none font-medium text-agro-dark">
                        </div>

                    </div>

                    {{-- Instrucciones Generales --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-2 ml-1">Instrucciones Adicionales (Opcional)</label>
                        <textarea id="instrucciones_adicionales" name="instrucciones_adicionales" rows="2" placeholder="Ej: Indicar número de pedido en la nota de Zelle..." class="w-full p-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white outline-none text-sm text-gray-600 font-medium"></textarea>
                    </div>

                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3 sticky bottom-0">
                    <button type="button" onclick="closeModal()" class="px-5 py-2.5 rounded-xl font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 text-sm">Cancelar</button>
                    <button type="submit" id="btnSave" class="px-6 py-2.5 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm text-sm flex items-center gap-2 transition-all">
                        <span class="material-symbols-outlined text-[18px]">save</span> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="toast-container" class="fixed bottom-6 right-6 z-[999] flex flex-col gap-2"></div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@push('scripts')
<script>
    let currentSearch = '';
    let currentTipo = 'todos';
    let searchTimeout;

    // Buscador y Filtro
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        currentSearch = this.value;
        searchTimeout = setTimeout(() => fetchData(1), 400);
    });

    document.getElementById('filtroTipo').addEventListener('change', function() {
        currentTipo = this.value;
        fetchData(1);
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
        
        fetch(`{{ route('admin.cuentas_bancarias.index') }}?buscar=${currentSearch}&tipo=${currentTipo}&page=${page}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => document.getElementById('tableContent').innerHTML = html)
        .finally(() => { loading.classList.add('hidden'); loading.classList.remove('flex'); });
    }

    // LÓGICA DEL FORMULARIO DINÁMICO
    function actualizarCamposVisibles() {
        const metodo = document.getElementById('tipo_metodo').value;
        
        // Ocultar todos primero y quitar el "required" para que no bloquee el guardado
        document.querySelectorAll('.grupo-titular, .grupo-banco, .grupo-cuenta, .grupo-telefono, .grupo-identificacion, .grupo-email').forEach(el => {
            el.classList.add('hidden');
        });

        // Mostrar solo lo necesario
        if (metodo === 'pago_movil') {
            document.querySelectorAll('.grupo-titular, .grupo-banco, .grupo-telefono, .grupo-identificacion').forEach(el => el.classList.remove('hidden'));
        } 
        else if (metodo === 'zelle') {
            document.querySelectorAll('.grupo-titular, .grupo-email').forEach(el => el.classList.remove('hidden'));
        } 
        else if (metodo === 'transferencia') {
            document.querySelectorAll('.grupo-titular, .grupo-banco, .grupo-cuenta, .grupo-identificacion').forEach(el => el.classList.remove('hidden'));
        }
        else if (metodo === 'binance') {
            document.querySelectorAll('.grupo-titular, .grupo-email').forEach(el => el.classList.remove('hidden'));
        }
        // Para efectivo y punto de venta no hace falta pedir datos adicionales, solo el titular/instrucciones.
    }

    function openModal(cuenta = null) {
        const form = document.getElementById('cuentaForm');
        form.reset();
        document.getElementById('cuenta_id').value = '';
        document.getElementById('modalTitle').innerText = 'Nuevo Método de Pago';
        
        if (cuenta) {
            document.getElementById('modalTitle').innerText = 'Editar Método';
            document.getElementById('cuenta_id').value = cuenta.id;
            document.getElementById('tipo_metodo').value = cuenta.tipo_metodo;
            document.getElementById('nombre_titular').value = cuenta.nombre_titular || '';
            document.getElementById('banco_entidad').value = cuenta.banco_entidad || '';
            document.getElementById('numero_cuenta').value = cuenta.numero_cuenta || '';
            document.getElementById('telefono').value = cuenta.telefono || '';
            document.getElementById('identificacion').value = cuenta.identificacion || '';
            document.getElementById('email').value = cuenta.email || '';
            document.getElementById('instrucciones_adicionales').value = cuenta.instrucciones_adicionales || '';
        }

        actualizarCamposVisibles(); // Disparar la lógica visual

        const modal = document.getElementById('cuentaModal');
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
        const modal = document.getElementById('cuentaModal');
        const backdrop = document.getElementById('modalBackdrop');
        const panel = document.getElementById('modalPanel');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    function saveCuenta(e) {
        e.preventDefault();
        const id = document.getElementById('cuenta_id').value;
        const btn = document.getElementById('btnSave');
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin">autorenew</span> Guardando...';
        btn.disabled = true;

        let formData = new FormData(e.target);
        // Si usamos fetch para actualizar por PUT usando FormData, laravel necesita un campo _method extra
        if (id) formData.append('_method', 'PUT');
        
        let url = id ? `/admin/cuentas-bancarias/${id}` : `{{ route('admin.cuentas_bancarias.store') }}`;

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

    function toggleCuenta(id) {
        fetch(`/admin/cuentas-bancarias/${id}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            showToast(data.message, 'success');
            fetchData(document.querySelector('.ajax-pagination .active span')?.innerText || 1);
        })
        .catch(() => showToast('Ocurrió un problema de red.', 'error'));
    }

    function deleteCuenta(id) {
        Swal.fire({
            title: '¿Eliminar método?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Sí, eliminar',
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/cuentas-bancarias/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                .then(res => res.json())
                .then(data => {
                    showToast(data.message, 'success');
                    fetchData(1);
                });
            }
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

    // Forzar la lógica al cargar si hay un modal abierto (para navegadores cacheados)
    document.addEventListener('DOMContentLoaded', actualizarCamposVisibles);
</script>
@endpush
@endsection