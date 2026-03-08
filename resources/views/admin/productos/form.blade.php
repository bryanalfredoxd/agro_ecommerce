@extends('layouts.admin')

@section('title', isset($producto) ? 'Editar Producto' : 'Crear Producto' . ' - Corpo Agrícola')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up pb-24">
            
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('admin.productos.index') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:text-green-600 hover:border-green-200 transition-colors shadow-sm">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div>
                    <h2 class="text-2xl font-black text-agro-dark">
                        {{ isset($producto) ? 'Editar Producto: ' . $producto->nombre : 'Crear Nuevo Producto' }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Completa los datos del formulario. Los campos con (*) son obligatorios.</p>
                </div>
            </div>

            {{-- Bloque para mostrar errores que vengan del Backend --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-6 py-4 rounded-xl font-bold text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ isset($producto) ? route('admin.productos.update', $producto->id) : route('admin.productos.store') }}" method="POST" enctype="multipart/form-data" id="mainProductForm">
                @csrf
                @if(isset($producto)) @method('PUT') @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                    
                    <div class="lg:col-span-2 space-y-6">
                        
                        {{-- Panel Información Básica --}}
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
                            <h3 class="text-lg font-black text-agro-dark border-b border-gray-100 pb-4 mb-6 flex items-center gap-2">
                                <span class="material-symbols-outlined text-green-600">info</span> Información General
                            </h3>
                            
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nombre del Producto <span class="text-red-500">*</span></label>
                                    <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre ?? '') }}" required class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white focus:ring-2 focus:ring-green-500/20 transition-all font-bold text-agro-dark outline-none">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Categoría</label>
                                        <select name="categoria_id" class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white focus:ring-2 focus:ring-green-500/20 transition-all font-bold text-agro-dark outline-none cursor-pointer">
                                            <option value="">Seleccione...</option>
                                            @foreach($categorias as $cat)
                                                <option value="{{ $cat->id }}" {{ (old('categoria_id', $producto->categoria_id ?? '') == $cat->id) ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Marca</label>
                                        <select name="marca_id" class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white focus:ring-2 focus:ring-green-500/20 transition-all font-bold text-agro-dark outline-none cursor-pointer">
                                            <option value="">Seleccione...</option>
                                            @foreach($marcas as $marca)
                                                <option value="{{ $marca->id }}" {{ (old('marca_id', $producto->marca_id ?? '') == $marca->id) ? 'selected' : '' }}>{{ $marca->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Descripción / Detalles</label>
                                    <textarea name="descripcion" rows="4" class="w-full p-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white focus:ring-2 focus:ring-green-500/20 transition-all text-sm text-agro-dark outline-none">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Panel Inventario y Logística --}}
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
                            <h3 class="text-lg font-black text-agro-dark border-b border-gray-100 pb-4 mb-6 flex items-center gap-2">
                                <span class="material-symbols-outlined text-green-600">shelves</span> Control de Inventario y Medidas
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">SKU / Código Interno</label>
                                    <input type="text" name="sku" value="{{ old('sku', $producto->sku ?? '') }}" oninput="this.value = this.value.toUpperCase().replace(/\s/g, '')" class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white focus:ring-2 focus:ring-green-500/20 transition-all font-mono font-bold text-agro-dark outline-none uppercase">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Código de Barras</label>
                                    <input type="text" name="codigo_barras" value="{{ old('codigo_barras', $producto->codigo_barras ?? '') }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white transition-all font-mono font-bold text-agro-dark outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1 text-green-600">Stock Físico Total</label>
                                    <input type="number" step="1" name="stock_total" id="stock_total" onblur="validarStock()" value="{{ old('stock_total', $producto->stock_total ?? 0) }}" class="limit-decimals w-full h-12 px-4 rounded-xl bg-green-50/50 border border-green-200 focus:border-green-500 focus:bg-white transition-all font-black text-green-700 outline-none">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
                                <div>
                                    <label class="block text-[9px] font-black text-gray-400 uppercase mb-1 ml-1" title="Cómo vendes el producto">U. de Medida (Venta)</label>
                                    <select name="unidad_medida" id="unidadMedidaSelect" onchange="automatizarUnidadesVenta()" class="w-full h-10 px-3 rounded-xl bg-white border border-gray-200 focus:border-green-500 outline-none font-bold text-gray-700 text-sm">
                                        @foreach(['unidad', 'kg', 'g', 'mg', 'litro', 'ml', 'galon', 'saco', 'bulto', 'paquete', 'caja', 'tambor', 'paila', 'frasco', 'metro', 'rollo', 'dosis', 'blister'] as $unidad)
                                            <option value="{{ $unidad }}" {{ (old('unidad_medida', $producto->unidad_medida ?? '') == $unidad) ? 'selected' : '' }}>{{ ucfirst($unidad) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-gray-400 uppercase mb-1 ml-1">Venta Mínima</label>
                                    <input type="number" step="0.01" name="venta_minima" id="venta_minima" value="{{ old('venta_minima', $producto->venta_minima ?? 1.00) }}" class="limit-decimals auto-int-venta w-full h-10 px-3 rounded-xl bg-white border border-gray-200 focus:border-green-500 outline-none font-bold text-gray-700 text-sm">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-gray-400 uppercase mb-1 ml-1">Incremento (Paso)</label>
                                    <input type="number" step="0.01" name="paso_venta" id="paso_venta" value="{{ old('paso_venta', $producto->paso_venta ?? 1.00) }}" class="limit-decimals auto-int-venta w-full h-10 px-3 rounded-xl bg-white border border-gray-200 focus:border-green-500 outline-none font-bold text-gray-700 text-sm">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-orange-500 uppercase mb-1 ml-1">Alerta Stock Bajo</label>
                                    <input type="number" step="1" name="stock_minimo_alerta" id="stock_minimo_alerta" onblur="validarStock()" value="{{ old('stock_minimo_alerta', $producto->stock_minimo_alerta ?? 5) }}" class="limit-decimals w-full h-10 px-3 rounded-xl bg-white border border-orange-200 text-orange-600 focus:border-orange-500 outline-none font-bold text-sm">
                                </div>
                            </div>
                            <p id="error-stock" class="text-[10px] text-red-500 font-bold hidden mt-2 ml-2">¡Error! La Alerta de Stock no puede ser mayor al Stock Físico Total.</p>

                            <hr class="my-6 border-gray-100">

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Contenido Neto Real</label>
                                    <input type="number" step="0.01" name="contenido_neto" id="contenido_neto" value="{{ old('contenido_neto', $producto->contenido_neto ?? '') }}" class="limit-decimals auto-int-contenido w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white outline-none font-bold text-agro-dark">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Unidad del Contenido</label>
                                    <select name="unidad_contenido" id="unidadContenidoSelect" onchange="automatizarContenido()" class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white outline-none font-bold text-gray-700">
                                        @foreach(['kg', 'g', 'mg', 'l', 'ml', 'cc', 'galon', 'oz', 'm', 'cm', 'dosis', 'unidad'] as $unidadC)
                                            <option value="{{ $unidadC }}" {{ (old('unidad_contenido', $producto->unidad_contenido ?? '') == $unidadC) ? 'selected' : '' }}>{{ ucfirst($unidadC) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Panel Atributos JSON --}}
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
                            <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-4">
                                <h3 class="text-lg font-black text-agro-dark flex items-center gap-2">
                                    <span class="material-symbols-outlined text-green-600">tune</span> Atributos Extra
                                </h3>
                                <button type="button" onclick="agregarAtributo()" class="bg-gray-100 hover:bg-green-100 text-gray-600 hover:text-green-700 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">
                                    + Añadir Fila
                                </button>
                            </div>
                            
                            <div id="atributos-container" class="space-y-3">
                                @php
                                    $atributosArray = [];
                                    if(isset($producto) && $producto->atributos_json) {
                                        $atributosArray = is_array($producto->atributos_json) ? $producto->atributos_json : json_decode($producto->atributos_json, true);
                                    }
                                @endphp

                                @forelse($atributosArray as $key => $val)
                                    <div class="flex items-center gap-3">
                                        <input type="text" name="attr_keys[]" value="{{ $key }}" placeholder="Ej: Material" class="w-1/3 h-10 px-3 rounded-xl bg-gray-50 border border-gray-200 text-sm font-bold outline-none">
                                        <input type="text" name="attr_values[]" value="{{ $val }}" placeholder="Ej: Plástico" class="flex-1 h-10 px-3 rounded-xl bg-gray-50 border border-gray-200 text-sm outline-none">
                                        <button type="button" onclick="this.parentElement.remove()" class="w-10 h-10 flex-shrink-0 flex items-center justify-center text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                                            <span class="material-symbols-outlined text-[18px]">close</span>
                                        </button>
                                    </div>
                                @empty
                                    <div class="flex items-center gap-3">
                                        <input type="text" name="attr_keys[]" placeholder="Ej: Dosificación" class="w-1/3 h-10 px-3 rounded-xl bg-gray-50 border border-gray-200 text-sm font-bold outline-none focus:border-green-500 focus:bg-white">
                                        <input type="text" name="attr_values[]" placeholder="Ej: 5ml por Litro" class="flex-1 h-10 px-3 rounded-xl bg-gray-50 border border-gray-200 text-sm outline-none focus:border-green-500 focus:bg-white">
                                        <button type="button" onclick="this.parentElement.remove()" class="w-10 h-10 flex-shrink-0 flex items-center justify-center text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                                            <span class="material-symbols-outlined text-[18px]">close</span>
                                        </button>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                    </div>

                    {{-- COLUMNA DERECHA: Imagen, Precios y Banderas --}}
                    <div class="space-y-6">
                        
                        {{-- Panel Imagen --}}
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-sm font-black text-agro-dark mb-4">Fotografía Principal</h3>
                            
                            <div class="w-full aspect-square bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 flex flex-col items-center justify-center relative overflow-hidden mb-4 group hover:border-green-400 transition-colors">
                                @if(isset($producto) && $producto->imagen_url)
                                    <img src="{{ asset($producto->imagen_url) }}" class="w-full h-full object-cover" id="previewImg">
                                @else
                                    <span class="material-symbols-outlined text-4xl text-gray-300 mb-2 group-hover:text-green-400" id="previewIcon">add_photo_alternate</span>
                                    <img src="" class="w-full h-full object-cover hidden" id="previewImg">
                                @endif
                                <input type="file" name="imagen" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="previewImage(event)">
                            </div>
                            <p class="text-[10px] text-center text-gray-400">Clic para cambiar imagen (Max 2MB)</p>
                        </div>

                        {{-- Panel Precios --}}
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-black text-agro-dark mb-4 flex items-center gap-2">
                                <span class="material-symbols-outlined text-green-600">payments</span> Precios (USD)
                            </h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Precio de Venta <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-3.5 font-bold text-gray-400">$</span>
                                        <input type="number" step="0.01" name="precio_venta_usd" id="precio_venta" onblur="validarPrecios()" value="{{ old('precio_venta_usd', $producto->precio_venta_usd ?? '') }}" required class="limit-decimals w-full h-12 pl-8 pr-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white text-xl font-black text-agro-dark outline-none">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-red-400 uppercase tracking-widest mb-1.5 ml-1">Precio Oferta (Opcional)</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-3.5 font-bold text-gray-400">$</span>
                                        <input type="number" step="0.01" name="precio_oferta_usd" id="precio_oferta" onblur="validarPrecios()" value="{{ old('precio_oferta_usd', $producto->precio_oferta_usd ?? '') }}" class="limit-decimals w-full h-12 pl-8 pr-4 rounded-xl bg-red-50/50 border border-red-100 focus:border-red-400 focus:bg-white text-lg font-bold text-red-600 outline-none">
                                    </div>
                                    <p id="error-oferta" class="text-[10px] text-red-500 font-bold hidden mt-1 ml-1">Error: La oferta debe ser menor al precio de venta.</p>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Costo Promedio (Interno)</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-3.5 font-bold text-gray-400">$</span>
                                        <input type="number" step="0.01" name="costo_promedio_usd" id="costo_promedio" onblur="validarPrecios()" value="{{ old('costo_promedio_usd', $producto->costo_promedio_usd ?? '0.00') }}" class="w-full h-12 pl-8 pr-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-gray-400 focus:bg-white text-sm font-bold text-gray-600 outline-none">
                                    </div>
                                    <p id="error-costo" class="text-[10px] text-red-500 font-bold hidden mt-1 ml-1">Error: Costo mayor al precio (Pérdida).</p>
                                </div>
                            </div>
                        </div>

                        {{-- Panel Banderas / Configuración Extra --}}
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 space-y-4">
                            <label class="relative flex items-center p-3 bg-purple-50 rounded-xl border border-purple-100 cursor-pointer">
                                <input type="checkbox" name="es_controlado" class="sr-only peer" {{ old('es_controlado', $producto->es_controlado ?? false) ? 'checked' : '' }}>
                                <div class="relative w-11 h-6 bg-purple-200 rounded-full peer peer-checked:after:translate-x-full after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                <div class="ml-3">
                                    <span class="text-sm font-black text-purple-900 block">Venta Controlada</span>
                                    <span class="text-[9px] font-bold text-purple-600 uppercase">Requiere Receta Médica</span>
                                </div>
                            </label>
                        </div>

                    </div>
                </div>

                <div class="h-28 w-full"></div>

                {{-- Barra Fija Abajo para Guardar --}}
                <div class="fixed bottom-0 left-0 lg:left-72 right-0 bg-white/80 backdrop-blur-md border-t border-gray-200 p-4 sm:px-8 flex justify-end gap-4 z-40 shadow-[0_-10px_30px_rgba(0,0,0,0.05)]">
                    <a href="{{ route('admin.productos.index') }}" class="px-6 py-3 rounded-xl font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">Cancelar</a>
                    <button type="submit" id="btnSubmitForm" class="px-8 py-3 rounded-xl font-black text-white bg-green-600 hover:bg-green-700 shadow-lg shadow-green-600/30 hover:-translate-y-1 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined">save</span>
                        {{ isset($producto) ? 'Guardar Cambios' : 'Crear Producto' }}
                    </button>
                </div>
            </form>

        </div>
    </main>
</div>

<div id="toast-container" class="fixed bottom-6 right-6 z-[999] flex flex-col gap-2"></div>

@push('scripts')
<script>
    // ==========================================
    // SISTEMA DE NOTIFICACIONES VISUALES (TOAST)
    // ==========================================
    function showToast(message, type = 'error') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-600' : (type === 'warning' ? 'bg-orange-500' : 'bg-red-600');
        const icon = type === 'success' ? 'check_circle' : (type === 'warning' ? 'warning' : 'error');

        toast.className = `flex items-center gap-3 px-5 py-4 rounded-xl shadow-2xl text-white transform translate-y-10 opacity-0 transition-all duration-300 ${bgColor}`;
        toast.innerHTML = `<span class="material-symbols-outlined">${icon}</span><p class="font-bold text-sm">${message}</p>`;

        container.appendChild(toast);
        
        // Animación de entrada
        setTimeout(() => toast.classList.remove('translate-y-10', 'opacity-0'), 10);
        
        // Animación de salida y destrucción
        setTimeout(() => {
            toast.classList.add('translate-y-10', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // ==========================================
    // 1. BLOQUEO DE DECIMALES Y MÁXIMOS EN TIEMPO REAL
    // ==========================================
    document.querySelectorAll('.limit-decimals').forEach(input => {
        input.addEventListener('input', function(e) {
            let val = e.target.value;
            
            let forceInt = this.classList.contains('force-integer');
            let allow3Decimals = this.classList.contains('allow-3-decimals');
            let maxDecimals = allow3Decimals ? 3 : 2;

            // Bloqueo de decimales según configuración
            if (forceInt) {
                if (val.includes('.')) e.target.value = val.split('.')[0]; // Borra todo después del punto
            } else if (val.includes('.')) {
                let parts = val.split('.');
                if (parts[1].length > maxDecimals) {
                    e.target.value = parts[0] + '.' + parts[1].substring(0, maxDecimals);
                }
            }

            // Bloqueo de valor máximo (Ej: no pasar de 0.999 en gramos)
            if (this.hasAttribute('max') && this.max !== "") {
                if (parseFloat(e.target.value) > parseFloat(this.max)) {
                    e.target.value = this.max;
                    showToast(`El valor máximo para esta unidad es ${this.max}`, 'warning');
                }
            }
        });
    });

    // ==========================================
    // 2. AUTOMATIZACIÓN LÓGICA DE UNIDADES
    // ==========================================

    // Empaques físicos o unidades que JAMÁS se pueden picar a la mitad
    const unidadesEnteras = [
        'unidad', 'saco', 'bulto', 'paquete', 'caja', 
        'tambor', 'paila', 'frasco', 'rollo', 'blister'
    ];
    // Sub-unidades de medida que solo operan por debajo de 1.000
    const unidadesFraccionales = ['g', 'mg', 'ml', 'cc', 'cm'];

    function automatizarUnidadesVenta() {
        const unidadVenta = document.getElementById('unidadMedidaSelect').value;
        const inputsVenta = document.querySelectorAll('.auto-int-venta');
        
        inputsVenta.forEach(input => {
            if (unidadesEnteras.includes(unidadVenta)) {
                // Configuración para Enteros (Unidad, Saco...)
                input.step = "1";
                input.max = "";
                input.removeAttribute('max');
                input.classList.add('force-integer');
                input.classList.remove('allow-3-decimals');
                if (input.value && input.value % 1 !== 0) input.value = Math.ceil(input.value);

            } else if (unidadesFraccionales.includes(unidadVenta)) {
                // Configuración para Fracciones (Gramos, Mililitros)
                input.step = "0.001";
                input.max = "0.999"; // LÍMITE MÁXIMO
                input.classList.remove('force-integer');
                input.classList.add('allow-3-decimals');
                if (parseFloat(input.value) > 0.999) input.value = "0.999";

            } else {
                // Configuración para Normales (Kg, Litros, Metros)
                input.step = "0.01";
                input.max = "";
                input.removeAttribute('max');
                input.classList.remove('force-integer');
                input.classList.remove('allow-3-decimals');
            }
        });
    }

    function automatizarContenido() {
        const unidadC = document.getElementById('unidadContenidoSelect').value;
        const inputContenido = document.getElementById('contenido_neto');

        if (unidadesEnteras.includes(unidadC)) {
            inputContenido.step = "1";
            inputContenido.max = "";
            inputContenido.removeAttribute('max');
            inputContenido.classList.add('force-integer');
            inputContenido.classList.remove('allow-3-decimals');
            if (inputContenido.value && inputContenido.value % 1 !== 0) inputContenido.value = Math.ceil(inputContenido.value);

        } else if (unidadesFraccionales.includes(unidadC)) {
            inputContenido.step = "0.001";
            inputContenido.max = "0.999"; // LÍMITE MÁXIMO
            inputContenido.classList.remove('force-integer');
            inputContenido.classList.add('allow-3-decimals');
            if (parseFloat(inputContenido.value) > 0.999) inputContenido.value = "0.999";

        } else {
            inputContenido.step = "0.01";
            inputContenido.max = "";
            inputContenido.removeAttribute('max');
            inputContenido.classList.remove('force-integer');
            inputContenido.classList.remove('allow-3-decimals');
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        automatizarUnidadesVenta();
        automatizarContenido();
    });

    // ==========================================
    // 3. VALIDACIONES AL GUARDAR Y EN TIEMPO REAL
    // ==========================================
    function validarPrecios() {
        const pVenta = parseFloat(document.getElementById('precio_venta').value) || 0;
        const pOferta = parseFloat(document.getElementById('precio_oferta').value) || 0;
        const pCosto = parseFloat(document.getElementById('costo_promedio').value) || 0;
        
        let hasError = false;

        if (pOferta > 0 && pOferta >= pVenta) {
            document.getElementById('error-oferta').classList.remove('hidden');
            hasError = true;
        } else {
            document.getElementById('error-oferta').classList.add('hidden');
        }

        if (pCosto > 0 && pCosto > pVenta) {
            document.getElementById('error-costo').classList.remove('hidden');
            hasError = true;
        } else {
            document.getElementById('error-costo').classList.add('hidden');
        }
        
        return !hasError;
    }

    function validarStock() {
        const stockTotal = parseFloat(document.getElementById('stock_total').value) || 0;
        const stockAlerta = parseFloat(document.getElementById('stock_minimo_alerta').value) || 0;
        
        let hasError = false;

        if (stockAlerta > stockTotal && stockTotal > 0) {
            document.getElementById('error-stock').classList.remove('hidden');
            hasError = true;
        } else {
            document.getElementById('error-stock').classList.add('hidden');
        }
        
        return !hasError;
    }

    // Interceptar el botón de Guardar (Sustituimos el alert por el Toast)
    document.getElementById('mainProductForm').addEventListener('submit', function(e) {
        const preciosOk = validarPrecios();
        const stockOk = validarStock();

        if (!preciosOk || !stockOk) {
            e.preventDefault(); // Detiene el envío
            
            // Usamos nuestro Toast en lugar del molesto alert()
            showToast("No se puede guardar. Corrige los errores en rojo marcados en el formulario.", "error");
            
            // Hacemos scroll hacia arriba para que el usuario vea los errores
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    // Funciones de Atributos JSON e Imagen... (se mantienen igual)
    function agregarAtributo() {
        const container = document.getElementById('atributos-container');
        const html = `
            <div class="flex items-center gap-3 animate-fade-in-up">
                <input type="text" name="attr_keys[]" placeholder="Característica" class="w-1/3 h-10 px-3 rounded-xl bg-gray-50 border border-gray-200 text-sm font-bold outline-none focus:border-green-500 focus:bg-white">
                <input type="text" name="attr_values[]" placeholder="Valor" class="flex-1 h-10 px-3 rounded-xl bg-gray-50 border border-gray-200 text-sm outline-none focus:border-green-500 focus:bg-white">
                <button type="button" onclick="this.parentElement.remove()" class="w-10 h-10 flex-shrink-0 flex items-center justify-center text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function previewImage(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('previewImg');
                const icon = document.getElementById('previewIcon');
                
                img.src = e.target.result;
                img.classList.remove('hidden');
                if(icon) icon.classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection