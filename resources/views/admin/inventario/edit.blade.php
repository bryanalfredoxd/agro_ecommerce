@extends('layouts.admin')

@section('title', 'Editar Lote - ' . $lote->numero_lote)
@section('page_header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-green-600"><i class="fas fa-edit mr-2"></i>Editar Lote</h1>
            <p class="text-gray-600 mt-1">{{ $lote->producto->nombre ?? 'Producto no encontrado' }} - Lote #{{ $lote->numero_lote }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.inventario.show', $lote->id) }}" class="p-2 bg-gray-100 rounded-md hover:bg-gray-200 transition">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-md">
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-4 rounded-t-xl">
            <h6 class="text-lg font-bold mb-0"><i class="fas fa-edit mr-2"></i>Editar Información del Lote</h6>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.inventario.update', $lote->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Número de Lote -->
                    <div>
                        <label for="numero_lote" class="block text-sm font-medium text-gray-700 mb-2">
                            Número de Lote
                        </label>
                        <input type="text" id="numero_lote" name="numero_lote"
                               value="{{ old('numero_lote', $lote->numero_lote) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                        @error('numero_lote')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fecha de Vencimiento -->
                    <div>
                        <label for="fecha_vencimiento" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Vencimiento
                        </label>
                        <input type="date" id="fecha_vencimiento" name="fecha_vencimiento"
                               value="{{ old('fecha_vencimiento', $lote->fecha_vencimiento->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                        @error('fecha_vencimiento')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ubicación en Almacén -->
                    <div class="md:col-span-2">
                        <label for="ubicacion_almacen" class="block text-sm font-medium text-gray-700 mb-2">
                            Ubicación en Almacén
                        </label>
                        <input type="text" id="ubicacion_almacen" name="ubicacion_almacen"
                               value="{{ old('ubicacion_almacen', $lote->ubicacion_almacen) }}"
                               placeholder="Ej: Estante A, Pasillo 3"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('ubicacion_almacen')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Información del Producto (Solo lectura) -->
                <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                    <h6 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Información del Producto (No editable)</h6>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-500">Producto:</span>
                            <span class="text-gray-900">{{ $lote->producto->nombre ?? 'No encontrado' }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-500">Proveedor:</span>
                            <span class="text-gray-900">{{ $lote->proveedor->nombre ?? 'No encontrado' }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-500">Cantidad Inicial:</span>
                            <span class="text-gray-900">{{ number_format($lote->cantidad_inicial, 0) }} unidades</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-500">Cantidad Restante:</span>
                            <span class="text-gray-900">{{ number_format($lote->cantidad_restante, 0) }} unidades</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-500">Costo Unitario:</span>
                            <span class="text-gray-900">${{ number_format($lote->costo_unitario_usd, 2) }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-500">Estado:</span>
                            <span class="text-gray-900">
                                @if($lote->activo)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Inactivo
                                    </span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('admin.inventario.show', $lote->id) }}"
                       class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-200">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-save mr-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection