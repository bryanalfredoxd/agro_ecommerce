@extends('layouts.admin')

@section('title', 'Editar Receta Veterinaria - #' . $receta->id)
@section('page_header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-green-600"><i class="fas fa-edit mr-2"></i>Editar Receta Veterinaria</h1>
            <p class="text-gray-600 mt-1">Modificar receta #{{ str_pad($receta->id, 4, '0', STR_PAD_LEFT) }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.recetas.show', $receta->id) }}" class="p-2 bg-blue-100 rounded-md hover:bg-blue-200 transition">
                <i class="fas fa-eye mr-1"></i> Ver
            </a>
            <a href="{{ route('admin.recetas.index') }}" class="p-2 bg-gray-100 rounded-md hover:bg-gray-200 transition">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.recetas.update', $receta->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Información del Cliente y Pedido -->
        <div class="bg-white rounded-xl shadow-md">
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-4 rounded-t-xl">
                <h6 class="text-lg font-bold mb-0"><i class="fas fa-user mr-2"></i>Información del Cliente y Pedido</h6>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="cliente_usuario_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Cliente <span class="text-red-500">*</span>
                        </label>
                        <input type="text" readonly
                               class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500"
                               value="{{ $receta->cliente->name ?? 'Cliente no encontrado' }} ({{ $receta->cliente->email ?? '' }})">
                        <input type="hidden" name="cliente_usuario_id" value="{{ $receta->cliente_usuario_id }}">
                    </div>
                    <div>
                        <label for="pedido_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Pedido Relacionado <span class="text-red-500">*</span>
                        </label>
                        <input type="text" readonly
                               class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500"
                               value="Pedido #{{ str_pad($receta->pedido->id ?? 'N/A', 5, '0', STR_PAD_LEFT) }} - ${{ number_format($receta->pedido->total_usd ?? 0, 2) }} - {{ ucfirst($receta->pedido->estado ?? 'N/A') }}">
                        <input type="hidden" name="pedido_id" value="{{ $receta->pedido_id }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Veterinario -->
        <div class="bg-white rounded-xl shadow-md">
            <div class="bg-gradient-to-r from-green-500 to-teal-600 text-white p-4 rounded-t-xl">
                <h6 class="text-lg font-bold mb-0"><i class="fas fa-user-md mr-2"></i>Información del Veterinario</h6>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="veterinario_nombre" class="block text-sm font-medium text-gray-700 mb-1">
                            Nombre del Veterinario <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="veterinario_nombre" name="veterinario_nombre" required
                               value="{{ old('veterinario_nombre', $receta->veterinario_nombre) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                               placeholder="Dr. Juan Pérez">
                    </div>
                    <div>
                        <label for="veterinario_matricula" class="block text-sm font-medium text-gray-700 mb-1">
                            Matrícula Profesional
                        </label>
                        <input type="text" id="veterinario_matricula" name="veterinario_matricula"
                               value="{{ old('veterinario_matricula', $receta->veterinario_matricula) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                               placeholder="MP-12345">
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Animal -->
        <div class="bg-white rounded-xl shadow-md">
            <div class="bg-gradient-to-r from-purple-500 to-pink-600 text-white p-4 rounded-t-xl">
                <h6 class="text-lg font-bold mb-0"><i class="fas fa-paw mr-2"></i>Información del Animal</h6>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="cliente_animal_tipo" class="block text-sm font-medium text-gray-700 mb-1">
                            Tipo de Animal <span class="text-red-500">*</span>
                        </label>
                        <select id="cliente_animal_tipo" name="cliente_animal_tipo" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="">Seleccionar tipo...</option>
                            <option value="Bovino" {{ old('cliente_animal_tipo', $receta->cliente_animal_tipo) == 'Bovino' ? 'selected' : '' }}>Bovino</option>
                            <option value="Porcino" {{ old('cliente_animal_tipo', $receta->cliente_animal_tipo) == 'Porcino' ? 'selected' : '' }}>Porcino</option>
                            <option value="Avícola" {{ old('cliente_animal_tipo', $receta->cliente_animal_tipo) == 'Avícola' ? 'selected' : '' }}>Avícola</option>
                            <option value="Caprino" {{ old('cliente_animal_tipo', $receta->cliente_animal_tipo) == 'Caprino' ? 'selected' : '' }}>Caprino</option>
                            <option value="Ovino" {{ old('cliente_animal_tipo', $receta->cliente_animal_tipo) == 'Ovino' ? 'selected' : '' }}>Ovino</option>
                            <option value="Equino" {{ old('cliente_animal_tipo', $receta->cliente_animal_tipo) == 'Equino' ? 'selected' : '' }}>Equino</option>
                            <option value="Canino" {{ old('cliente_animal_tipo', $receta->cliente_animal_tipo) == 'Canino' ? 'selected' : '' }}>Canino</option>
                            <option value="Felino" {{ old('cliente_animal_tipo', $receta->cliente_animal_tipo) == 'Felino' ? 'selected' : '' }}>Felino</option>
                            <option value="Otro" {{ old('cliente_animal_tipo', $receta->cliente_animal_tipo) == 'Otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>
                    <div>
                        <label for="cliente_animal_cantidad" class="block text-sm font-medium text-gray-700 mb-1">
                            Cantidad de Animales
                        </label>
                        <input type="number" id="cliente_animal_cantidad" name="cliente_animal_cantidad" min="1"
                               value="{{ old('cliente_animal_cantidad', $receta->cliente_animal_cantidad) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="50">
                    </div>
                    <div>
                        <label for="fecha_prescription" class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha de Prescripción <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="fecha_prescription" name="fecha_prescription" required
                               value="{{ old('fecha_prescription', $receta->fecha_prescription->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                </div>
            </div>
        </div>

        <!-- Archivo de la Receta -->
        <div class="bg-white rounded-xl shadow-md">
            <div class="bg-gradient-to-r from-yellow-500 to-orange-600 text-white p-4 rounded-t-xl">
                <h6 class="text-lg font-bold mb-0"><i class="fas fa-file-upload mr-2"></i>Archivo de la Receta</h6>
            </div>
            <div class="p-6">
                @if($receta->archivo_url)
                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-file mr-2"></i>
                        Archivo actual: <a href="{{ Storage::url($receta->archivo_url) }}" target="_blank" class="underline">Ver archivo actual</a>
                    </p>
                </div>
                @endif
                <div>
                    <label for="archivo_url" class="block text-sm font-medium text-gray-700 mb-1">
                        Subir Nuevo Archivo (PDF o Imagen) - Opcional
                    </label>
                    <input type="file" id="archivo_url" name="archivo_url" accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Formatos permitidos: PDF, JPG, JPEG, PNG. Tamaño máximo: 5MB</p>
                </div>
            </div>
        </div>

        <!-- Productos Prescritos -->
        <div class="bg-white rounded-xl shadow-md">
            <div class="bg-gradient-to-r from-red-500 to-pink-600 text-white p-4 rounded-t-xl">
                <h6 class="text-lg font-bold mb-0"><i class="fas fa-pills mr-2"></i>Productos Prescritos</h6>
            </div>
            <div class="p-6">
                <div id="productos-container">
                    @foreach($receta->recetaProductos as $index => $recetaProducto)
                    <div class="producto-item border border-gray-200 rounded-lg p-4 mb-4">
                        <div class="flex justify-between items-center mb-4">
                            <h5 class="text-md font-semibold text-gray-900">Producto {{ $index + 1 }}</h5>
                            @if($loop->first)
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Producto principal</span>
                            @else
                            <button type="button" onclick="removerProducto(this)" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Producto <span class="text-red-500">*</span>
                                </label>
                                <select name="productos[{{ $index }}][producto_id]" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                    <option value="">Seleccionar producto...</option>
                                    @foreach($productos as $producto)
                                    <option value="{{ $producto->id }}" {{ $recetaProducto->producto_id == $producto->id ? 'selected' : '' }}>
                                        {{ $producto->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Cantidad Prescrita <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="productos[{{ $index }}][cantidad_prescrita]" step="0.001" min="0.001" required
                                       value="{{ old('productos.' . $index . '.cantidad_prescrita', $recetaProducto->cantidad_prescrita) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                       placeholder="0.000">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Instrucciones de Dosificación
                                </label>
                                <input type="text" name="productos[{{ $index }}][dosis_instrucciones]"
                                       value="{{ old('productos.' . $index . '.dosis_instrucciones', $recetaProducto->dosis_instrucciones) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                       placeholder="Ej: 1ml por cada 50kg">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <button type="button" onclick="agregarProducto()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                    <i class="fas fa-plus mr-2"></i>Agregar Otro Producto
                </button>
            </div>
        </div>

        <!-- Fechas -->
        <div class="bg-white rounded-xl shadow-md">
            <div class="bg-gradient-to-r from-indigo-500 to-blue-600 text-white p-4 rounded-t-xl">
                <h6 class="text-lg font-bold mb-0"><i class="fas fa-calendar mr-2"></i>Fechas de Vigencia</h6>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="fecha_vencimiento_receta" class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha de Vencimiento <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="fecha_vencimiento_receta" name="fecha_vencimiento_receta" required
                               value="{{ old('fecha_vencimiento_receta', $receta->fecha_vencimiento_receta->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Hasta cuándo es válida esta receta</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Observaciones -->
        <div class="bg-white rounded-xl shadow-md">
            <div class="bg-gradient-to-r from-gray-500 to-gray-700 text-white p-4 rounded-t-xl">
                <h6 class="text-lg font-bold mb-0"><i class="fas fa-sticky-note mr-2"></i>Observaciones</h6>
            </div>
            <div class="p-6">
                <div>
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">
                        Observaciones Adicionales
                    </label>
                    <textarea id="observaciones" name="observaciones" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                              placeholder="Observaciones adicionales sobre la receta...">{{ old('observaciones', $receta->observaciones) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.recetas.show', $receta->id) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-6 rounded-md transition duration-200">
                Cancelar
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition duration-200">
                <i class="fas fa-save mr-2"></i>Actualizar Receta
            </button>
        </div>
    </form>
</div>

<script>
let productoIndex = {{ $receta->recetaProductos->count() }};

function agregarProducto() {
    const container = document.getElementById('productos-container');
    const productoHtml = `
        <div class="producto-item border border-gray-200 rounded-lg p-4 mb-4">
            <div class="flex justify-between items-center mb-4">
                <h5 class="text-md font-semibold text-gray-900">Producto ${productoIndex + 1}</h5>
                <button type="button" onclick="removerProducto(this)" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Producto <span class="text-red-500">*</span>
                    </label>
                    <select name="productos[${productoIndex}][producto_id]" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="">Seleccionar producto...</option>
                        @foreach($productos as $producto)
                        <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Cantidad Prescrita <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="productos[${productoIndex}][cantidad_prescrita]" step="0.001" min="0.001" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                           placeholder="0.000">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Instrucciones de Dosificación
                    </label>
                    <input type="text" name="productos[${productoIndex}][dosis_instrucciones]"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                           placeholder="Ej: 1ml por cada 50kg">
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', productoHtml);
    productoIndex++;
}

function removerProducto(button) {
    button.closest('.producto-item').remove();
}

// Establecer fecha mínima para fecha_prescription (hoy)
document.getElementById('fecha_prescription').min = new Date().toISOString().split('T')[0];

// Establecer fecha mínima para fecha_vencimiento_receta (mañana)
const tomorrow = new Date();
tomorrow.setDate(tomorrow.getDate() + 1);
document.getElementById('fecha_vencimiento_receta').min = tomorrow.toISOString().split('T')[0];
</script>
@endsection