@extends('layouts.admin')

@section('title', 'Detalle del Lote - ' . $lote->numero_lote)
@section('page_header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-green-600"><i class="fas fa-box-open mr-2"></i>Detalle del Lote</h1>
            <p class="text-gray-600 mt-1">Lote #{{ $lote->numero_lote }} - {{ $lote->producto->nombre ?? 'Producto no encontrado' }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.inventario.index') }}" class="p-2 bg-gray-100 rounded-md hover:bg-gray-200 transition">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>
    </div>
@endsection

@section('content')
<!-- Mensajes de éxito y error -->
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span>{{ session('error') }}</span>
        </div>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-md">
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-4 rounded-t-xl">
                <h6 class="text-lg font-bold mb-0"><i class="fas fa-cube mr-2"></i>Información del Producto</h6>
            </div>
            <div class="p-6">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        @if($lote->producto && $lote->producto->imagenes && $lote->producto->imagenes->count() > 0)
                            <img class="h-20 w-20 rounded-lg object-cover" src="{{ asset('storage/' . $lote->producto->imagenes->first()->ruta_imagen) }}" alt="{{ $lote->producto->nombre }}">
                        @else
                            <div class="h-20 w-20 rounded-lg bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-image text-gray-400 text-2xl"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h5 class="text-xl font-bold text-gray-900">{{ $lote->producto->nombre ?? 'Producto no encontrado' }}</h5>
                        <p class="text-gray-600 mt-1">{{ $lote->producto->descripcion ?? 'Sin descripción' }}</p>
                        <div class="mt-3 flex items-center space-x-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $lote->producto->categoria->nombre ?? 'Sin categoría' }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $lote->producto->marca->nombre ?? 'Sin marca' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md">
            <div class="bg-gradient-to-r from-green-500 to-teal-600 text-white p-4 rounded-t-xl">
                <h6 class="text-lg font-bold mb-0"><i class="fas fa-boxes-stacked mr-2"></i>Información del Lote</h6>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h6 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Detalles del Lote</h6>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Número de Lote</dt>
                                <dd class="text-sm text-gray-900 font-mono">{{ $lote->numero_lote }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Fecha de Vencimiento</dt>
                                <dd class="text-sm text-gray-900">
                                    @php
                                        // Aseguramos que sea un objeto Carbon
                                        $fechaVencimiento = \Carbon\Carbon::parse($lote->fecha_vencimiento);
                                        $estaVencido = $fechaVencimiento->isPast();
                                        $porVencer = $fechaVencimiento->diffInDays(now()) <= 30 && !$estaVencido;
                                    @endphp
                                    <span class="{{ $estaVencido ? 'text-red-600 font-semibold' : ($porVencer ? 'text-yellow-600 font-semibold' : 'text-green-600') }}">
                                        {{ $fechaVencimiento->format('d/m/Y') }}
                                        @if($estaVencido)
                                            <i class="fas fa-exclamation-triangle ml-1"></i> (VENCIDO)
                                        @elseif($porVencer)
                                            <i class="fas fa-clock ml-1"></i> (Por vencer)
                                        @else
                                            <i class="fas fa-check-circle ml-1"></i> (Válido)
                                        @endif
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Ubicación en Almacén</dt>
                                <dd class="text-sm text-gray-900">{{ $lote->ubicacion_almacen ?? 'No especificada' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Estado</dt>
                                <dd class="text-sm">
                                    @if($lote->activo)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-times-circle mr-1"></i> Inactivo
                                        </span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <h6 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Inventario y Costos</h6>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Cantidad Inicial</dt>
                                <dd class="text-sm text-gray-900 font-semibold">{{ number_format($lote->cantidad_inicial, 3) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Cantidad Restante</dt>
                                <dd class="text-sm text-gray-900 font-semibold">{{ number_format($lote->cantidad_restante, 3) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Porcentaje Restante</dt>
                                <dd class="text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                            @php $porcentaje = $lote->cantidad_inicial > 0 ? ($lote->cantidad_restante / $lote->cantidad_inicial) * 100 : 0; @endphp
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $porcentaje }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium">{{ number_format($porcentaje, 1) }}%</span>
                                    </div>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Costo Unitario</dt>
                                <dd class="text-sm text-gray-900 font-semibold text-green-600">${{ number_format($lote->costo_unitario_usd, 4) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Valor Total Restante</dt>
                                <dd class="text-sm text-gray-900 font-bold text-green-600">${{ number_format($lote->cantidad_restante * $lote->costo_unitario_usd, 2) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-md">
            <div class="bg-gradient-to-r from-purple-500 to-pink-600 text-white p-4 rounded-t-xl">
                <h6 class="text-lg font-bold mb-0"><i class="fas fa-truck mr-2"></i>Proveedor</h6>
            </div>
            <div class="p-6">
                <div class="text-center">
                    <div class="h-16 w-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-building text-gray-400 text-2xl"></i>
                    </div>
                    
                    @if($lote->proveedor)
                        <h5 class="text-lg font-bold text-gray-900">{{ $lote->proveedor->razon_social ?? 'Sin Razón Social' }}</h5>
                        <p class="text-gray-600 text-sm">{{ $lote->proveedor->persona_contacto ?? 'Sin persona de contacto' }}</p>
                        
                        @if($lote->proveedor->telefono)
                            <p class="text-gray-600 text-sm mt-1">
                                <i class="fas fa-phone mr-1"></i>{{ $lote->proveedor->telefono }}
                            </p>
                        @endif
                    @else
                        <h5 class="text-lg font-bold text-gray-500">Sin Proveedor Asignado</h5>
                        <p class="text-gray-400 text-sm mt-2">Este lote no tiene un proveedor vinculado en el sistema.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md">
            <div class="bg-gradient-to-r from-gray-700 to-gray-900 text-white p-4 rounded-t-xl">
                <h6 class="text-lg font-bold mb-0"><i class="fas fa-bolt mr-2"></i>Acciones</h6>
            </div>
            <div class="p-6 space-y-3">
                <a href="{{ route('admin.inventario.edit', $lote->id) }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200 inline-block text-center">
                    <i class="fas fa-edit mr-2"></i>Editar Lote
                </a>
                <button type="button" onclick="openAddStockModal()" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                    <i class="fas fa-plus mr-2"></i>Agregar Stock
                </button>
                <button type="button" onclick="openArchiveModal()" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                    <i class="fas fa-archive mr-2"></i>Archivar Lote
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Stock -->
<div id="addStockModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-plus mr-2 text-yellow-600"></i>Agregar Stock al Lote
                </h3>
                <button onclick="closeAddStockModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form action="{{ route('admin.inventario.agregar-stock', $lote->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                
                <div>
                    <label for="cantidad_agregar" class="block text-sm font-medium text-gray-700 mb-1">
                        Cantidad a Agregar <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="cantidad_agregar" 
                           name="cantidad_agregar" 
                           step="0.001" 
                           min="0.001" 
                           required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                           placeholder="0.000">
                    <p class="text-xs text-gray-500 mt-1">Unidad: {{ $lote->producto->unidad_medida ?? 'kg' }}</p>
                </div>
                
                <div>
                    <label for="costo_unitario_nuevo" class="block text-sm font-medium text-gray-700 mb-1">
                        Costo Unitario Nuevo (USD) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="costo_unitario_nuevo" 
                           name="costo_unitario_nuevo" 
                           step="0.0001" 
                           min="0.0001" 
                           required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                           placeholder="0.0000">
                    <p class="text-xs text-gray-500 mt-1">Costo actual: ${{ number_format($lote->costo_unitario_usd, 4) }}</p>
                </div>
                
                <div class="bg-blue-50 p-3 rounded-md">
                    <h4 class="text-sm font-semibold text-blue-800 mb-2">Cálculo Automático</h4>
                    <p class="text-xs text-blue-700">
                        El sistema calculará automáticamente el nuevo costo promedio ponderado basado en la cantidad existente y el nuevo stock agregado.
                    </p>
                </div>
                
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-md p-3">
                        <ul class="text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <div class="flex space-x-3 pt-4">
                    <button type="submit" class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                        <i class="fas fa-plus mr-2"></i>Agregar Stock
                    </button>
                    <button type="button" onclick="closeAddStockModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-4 rounded-md transition duration-200">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Archivar Lote -->
<div id="archiveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-archive mr-2 text-red-600"></i>Archivar Lote
                </h3>
                <button onclick="closeArchiveModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="text-center mb-6">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h4 class="text-md font-semibold text-gray-900 mb-2">¿Estás seguro?</h4>
                <p class="text-sm text-gray-600">
                    Esta acción archivará el lote "{{ $lote->numero_lote }}" y lo marcará como inactivo. 
                    @if($lote->cantidad_restante > 0)
                        <strong class="text-red-600">Todavía tiene {{ number_format($lote->cantidad_restante, 3) }} unidades restantes.</strong>
                    @else
                        No tiene stock restante.
                    @endif
                </p>
            </div>
            
            <form action="{{ route('admin.inventario.archivar', $lote->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
                    <div class="flex">
                        <i class="fas fa-info-circle text-yellow-600 mt-0.5 mr-2"></i>
                        <div class="text-sm text-yellow-800">
                            <p class="font-semibold">Nota importante:</p>
                            <p>Los lotes archivados no se mostrarán en el inventario activo pero se mantendrá toda la información histórica.</p>
                        </div>
                    </div>
                </div>
                
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-md p-3">
                        <ul class="text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <div class="flex space-x-3 pt-4">
                    <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                        <i class="fas fa-archive mr-2"></i>Archivar Lote
                    </button>
                    <button type="button" onclick="closeArchiveModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-4 rounded-md transition duration-200">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddStockModal() {
    document.getElementById('addStockModal').classList.remove('hidden');
}

function closeAddStockModal() {
    document.getElementById('addStockModal').classList.add('hidden');
}

function openArchiveModal() {
    document.getElementById('archiveModal').classList.remove('hidden');
}

function closeArchiveModal() {
    document.getElementById('archiveModal').classList.add('hidden');
}

// Cerrar modales al hacer clic fuera
document.getElementById('addStockModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAddStockModal();
    }
});

document.getElementById('archiveModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeArchiveModal();
    }
});
</script>

@endsection