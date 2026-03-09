@extends('layouts.admin')

@section('title', 'Configuración de la Tienda - Corpo Agrícola')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up pb-24">
            
            {{-- Encabezado --}}
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('admin.dashboard') }}" class="w-10 h-10 flex-shrink-0 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:text-gray-900 hover:border-gray-400 transition-colors shadow-sm">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div>
                    <h2 class="text-2xl font-black text-agro-dark flex items-center gap-2">
                        <span class="material-symbols-outlined text-gray-600 text-[32px]">storefront</span>
                        Configuración Global
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Ajusta los parámetros operativos principales de Corpo Agrícola.</p>
                </div>
            </div>

            <form action="{{ route('admin.configuracion.update') }}" method="POST" class="max-w-4xl">
                @csrf
                
                <div class="space-y-6">
                    {{-- Tarjeta 1: Datos Generales e Impuestos --}}
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
                        <h3 class="text-lg font-black text-agro-dark border-b border-gray-100 pb-4 mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-600">info</span> Información e Impuestos
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nombre Comercial de la Empresa</label>
                                <input type="text" name="nombre_empresa" value="{{ old('nombre_empresa', $config->nombre_empresa) }}" required class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all font-bold text-agro-dark outline-none">
                            </div>
                            
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Porcentaje de Impuesto (IVA) %</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-3.5 font-bold text-gray-400">%</span>
                                    <input type="number" step="0.01" min="0" max="100" name="iva_porcentaje" value="{{ old('iva_porcentaje', $config->iva_porcentaje) }}" required class="limit-decimals w-full h-12 pl-10 pr-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all font-bold text-blue-600 text-lg outline-none">
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1 ml-1 font-medium">Este porcentaje se calculará automáticamente en los carritos web y el POS.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Tarjeta 2: Estado Operativo de la Tienda Web --}}
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
                        <h3 class="text-lg font-black text-agro-dark border-b border-gray-100 pb-4 mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-orange-500">power_settings_new</span> Operatividad Web
                        </h3>
                        
                        <div class="mb-6">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Modo de Funcionamiento Actual</label>
                            <select name="modo_operativo" id="modo_operativo" onchange="verificarModo()" class="w-full h-14 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-orange-500 focus:bg-white focus:ring-2 focus:ring-orange-500/20 transition-all font-bold text-agro-dark outline-none cursor-pointer">
                                <option value="automatico" {{ $config->modo_operativo == 'automatico' ? 'selected' : '' }}>Automático (Abre y cierra según los horarios configurados)</option>
                                <option value="manual_abierto" {{ $config->modo_operativo == 'manual_abierto' ? 'selected' : '' }}>Forzar Abierto 24/7 (Ignora los horarios)</option>
                                <option value="manual_cerrado" {{ $config->modo_operativo == 'manual_cerrado' ? 'selected' : '' }}>Cierre de Emergencia (Nadie puede comprar online)</option>
                            </select>
                        </div>

                        <div id="caja_mensaje_cierre" class="transition-all duration-300 {{ $config->modo_operativo == 'manual_cerrado' ? 'bg-red-50 border border-red-200 p-4 rounded-2xl' : '' }}">
                            <label class="block text-[10px] font-black uppercase tracking-widest mb-2 ml-1 {{ $config->modo_operativo == 'manual_cerrado' ? 'text-red-500' : 'text-gray-400' }}">Mensaje visible cuando la tienda esté cerrada</label>
                            <textarea name="mensaje_cierre_emergencia" rows="3" class="w-full p-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-orange-500 focus:bg-white outline-none text-sm text-gray-600 font-medium">{{ old('mensaje_cierre_emergencia', $config->mensaje_cierre_emergencia) }}</textarea>
                            <p class="text-[10px] text-gray-400 mt-1.5 ml-1">Este texto lo verán los clientes en la página web en lugar del botón "Añadir al Carrito".</p>
                        </div>
                    </div>
                </div>

                {{-- Auditoría Footer --}}
                <div class="mt-4 px-2 flex justify-between items-center text-xs font-bold text-gray-400">
                    <p>Última actualización: {{ \Carbon\Carbon::parse($config->actualizado_at)->format('d/m/Y h:i A') }}</p>
                    <p>Editado por: {{ $config->ultimoEditor ? $config->ultimoEditor->nombre : 'Sistema' }}</p>
                </div>

                {{-- Botón Guardar Flotante --}}
                <div class="fixed bottom-0 left-0 lg:left-72 right-0 bg-white/80 backdrop-blur-md border-t border-gray-200 p-4 sm:px-8 flex justify-end z-40 shadow-[0_-10px_30px_rgba(0,0,0,0.05)]">
                    <button type="submit" class="px-8 py-3 rounded-xl font-black text-white bg-gray-900 hover:bg-black shadow-lg shadow-gray-900/30 hover:-translate-y-1 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined">save</span> Guardar Configuración
                    </button>
                </div>
            </form>

        </div>
    </main>
</div>

<div id="toast-container" class="fixed bottom-6 right-6 z-[999] flex flex-col gap-2"></div>

@push('scripts')
<script>
    // 1. Mostrar Notificaciones de Sesión (si el controlador envía "success")
    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif
    
    @if($errors->any())
        showToast("Revisa los errores en el formulario.", 'error');
    @endif

    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if(!container) return;
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

    // 2. Control Visual del Modo Operativo
    function verificarModo() {
        const select = document.getElementById('modo_operativo');
        const cajaMensaje = document.getElementById('caja_mensaje_cierre');
        const labelMensaje = cajaMensaje.querySelector('label');

        if (select.value === 'manual_cerrado') {
            cajaMensaje.classList.add('bg-red-50', 'border', 'border-red-200', 'p-4', 'rounded-2xl');
            labelMensaje.classList.replace('text-gray-400', 'text-red-500');
        } else {
            cajaMensaje.classList.remove('bg-red-50', 'border', 'border-red-200', 'p-4', 'rounded-2xl');
            labelMensaje.classList.replace('text-red-500', 'text-gray-400');
        }
    }

    // 3. Limitar decimales a 2 (usamos el mismo código seguro de los productos)
    document.querySelectorAll('.limit-decimals').forEach(input => {
        input.addEventListener('input', function(e) {
            let val = e.target.value;
            if (val.includes('.')) {
                let parts = val.split('.');
                if (parts[1].length > 2) {
                    e.target.value = parts[0] + '.' + parts[1].substring(0, 2);
                }
            }
        });
    });
</script>
@endpush
@endsection