@extends('layouts.admin')

@section('title', isset($producto) ? 'Editar Producto' : 'Crear Producto' . ' - Corpo Agrícola')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up">
            
            {{-- Encabezado con botón de regreso --}}
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

            <form action="{{ isset($producto) ? route('admin.productos.update', $producto->id) : route('admin.productos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($producto)) @method('PUT') @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                    
                    {{-- COLUMNA IZQUIERDA: Datos Generales --}}
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
                                <span class="material-symbols-outlined text-green-600">shelves</span> Control de Inventario
                            </h3>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">SKU / Código Interno</label>
                                    <input type="text" name="sku" value="{{ old('sku', $producto->sku ?? '') }}" class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white focus:ring-2 focus:ring-green-500/20 transition-all font-mono font-bold text-agro-dark outline-none uppercase">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Código de Barras</label>
                                    <input type="text" name="codigo_barras" value="{{ old('codigo_barras', $producto->codigo_barras ?? '') }}" class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white focus:ring-2 focus:ring-green-500/20 transition-all font-mono font-bold text-agro-dark outline-none">
                                </div>
                                
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Stock Total</label>
                                    <input type="number" step="0.001" name="stock_total" value="{{ old('stock_total', $producto->stock_total ?? 0) }}" class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white focus:ring-2 focus:ring-green-500/20 transition-all font-bold text-agro-dark outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Unidad de Medida de Venta</label>
                                    <select name="unidad_medida" class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white outline-none font-bold text-gray-700">
                                        @foreach(['unidad', 'kg', 'g', 'litro', 'ml', 'saco', 'bulto'] as $unidad)
                                            <option value="{{ $unidad }}" {{ (old('unidad_medida', $producto->unidad_medida ?? '') == $unidad) ? 'selected' : '' }}>{{ ucfirst($unidad) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1 text-orange-500">Alerta Stock Mínimo</label>
                                    <input type="number" step="0.001" name="stock_minimo_alerta" value="{{ old('stock_minimo_alerta', $producto->stock_minimo_alerta ?? 5) }}" class="w-full h-12 px-4 rounded-xl bg-orange-50/50 border border-orange-200 focus:border-orange-500 focus:bg-white outline-none font-bold text-orange-700">
                                </div>
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
                                    <img src="{{ asset('storage/' . $producto->imagen_url) }}" class="w-full h-full object-cover" id="previewImg">
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
                                        <input type="number" step="0.01" name="precio_venta_usd" value="{{ old('precio_venta_usd', $producto->precio_venta_usd ?? '') }}" required class="w-full h-12 pl-8 pr-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-green-500 focus:bg-white text-xl font-black text-agro-dark outline-none">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-red-400 uppercase tracking-widest mb-1.5 ml-1">Precio Oferta (Opcional)</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-3.5 font-bold text-gray-400">$</span>
                                        <input type="number" step="0.01" name="precio_oferta_usd" value="{{ old('precio_oferta_usd', $producto->precio_oferta_usd ?? '') }}" class="w-full h-12 pl-8 pr-4 rounded-xl bg-red-50/50 border border-red-100 focus:border-red-400 focus:bg-white text-lg font-bold text-red-600 outline-none">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Costo Promedio (Interno)</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-3.5 font-bold text-gray-400">$</span>
                                        <input type="number" step="0.0001" name="costo_promedio_usd" value="{{ old('costo_promedio_usd', $producto->costo_promedio_usd ?? '0.00') }}" class="w-full h-12 pl-8 pr-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-gray-400 focus:bg-white text-sm font-bold text-gray-600 outline-none">
                                    </div>
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

                {{-- Espaciador para que la barra fija no tape el contenido final --}}
                <div class="h-28 w-full"></div>

                {{-- Barra Fija Abajo para Guardar --}}
                <div class="fixed bottom-0 left-0 lg:left-72 right-0 bg-white/80 backdrop-blur-md border-t border-gray-200 p-4 sm:px-8 flex justify-end gap-4 z-40 shadow-[0_-10px_30px_rgba(0,0,0,0.05)]">
                    <a href="{{ route('admin.productos.index') }}" class="px-6 py-3 rounded-xl font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">Cancelar</a>
                    <button type="submit" class="px-8 py-3 rounded-xl font-black text-white bg-green-600 hover:bg-green-700 shadow-lg shadow-green-600/30 hover:-translate-y-1 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined">save</span>
                        {{ isset($producto) ? 'Guardar Cambios' : 'Crear Producto' }}
                    </button>
                </div>
            </form>

        </div>
    </main>
</div>

@push('scripts')
<script>
    // JS para la previsualización de la imagen antes de subirla 
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