@extends('layouts.admin')

@section('title', 'Gestión de Inventario')
@section('page_header')
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-green-600"><i class="fas fa-box-open mr-2"></i>Gestión de Inventario</h1>
        <small class="text-gray-500">{{ now()->format('l, d F Y') }}</small>
    </div>
@endsection

@section('content')
<style>
.hover-elevate:hover {
    transform: translateY(-5px);
    transition: all 0.3s ease;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}
.gradient-success {
    background: linear-gradient(135deg, #28a745, #20c997);
}
.gradient-warning {
    background: linear-gradient(135deg, #ffc107, #e83e8c);
}
.gradient-danger {
    background: linear-gradient(135deg, #dc3545, #fd7e14);
}
.gradient-primary {
    background: linear-gradient(135deg, #007bff, #6610f2);
}
.card-stats {
    position: relative;
    overflow: hidden;
}
.card-stats::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    transform: translate(30px, -30px);
}
.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
    transform: scale(1.01);
    transition: all 0.2s ease;
}
.btn-action {
    transition: all 0.2s ease;
}
.btn-action:hover {
    transform: scale(1.1);
}
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
</style>

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-md hover-elevate h-32 card-stats">
        <div class="p-6 gradient-primary text-white rounded-xl h-full flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h6 class="text-sm font-medium opacity-80 mb-2">Total Productos</h6>
                    <h3 class="text-2xl font-bold">{{ $totalProductos }}</h3>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full ml-4">
                    <i class="fas fa-cubes text-xl"></i>
                </div>
            </div>
            <div class="text-sm mt-4">
                Productos en catálogo
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md hover-elevate h-32 card-stats">
        <div class="p-6 gradient-success text-white rounded-xl h-full flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h6 class="text-sm font-medium opacity-80 mb-2">Total Lotes</h6>
                    <h3 class="text-2xl font-bold">{{ $totalLotes }}</h3>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full ml-4">
                    <i class="fas fa-boxes-stacked text-xl"></i>
                </div>
            </div>
            <div class="text-sm mt-4">
                Lotes activos en inventario
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md hover-elevate h-32 card-stats">
        <div class="p-6 gradient-danger text-white rounded-xl h-full flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h6 class="text-sm font-medium opacity-80 mb-2">Lotes Vencidos</h6>
                    <h3 class="text-2xl font-bold">{{ $lotesVencidos }}</h3>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full ml-4">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
            </div>
            <div class="text-sm mt-4">
                Requieren atención inmediata
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md hover-elevate h-32 card-stats">
        <div class="p-6 gradient-warning text-white rounded-xl h-full flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h6 class="text-sm font-medium opacity-80 mb-2">Valor Inventario</h6>
                    <h3 class="text-2xl font-bold">${{ number_format($valorTotalInventario, 2) }}</h3>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full ml-4">
                    <i class="fas fa-dollar-sign text-xl"></i>
                </div>
            </div>
            <div class="text-sm mt-4">
                Valor total en USD
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-md">
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-4 rounded-t-xl flex justify-between items-center">
        <h6 class="text-lg font-bold mb-0"><i class="fas fa-boxes-stacked mr-2"></i>Lotes de Inventario</h6>
        <div class="flex space-x-2">
            <span class="text-sm bg-white bg-opacity-20 px-3 py-1 rounded-full">
                <i class="fas fa-clock mr-1"></i> {{ $lotesPorVencer }} por vencer (30 días)
            </span>
        </div>
    </div>
    <div class="p-0">
        <div class="overflow-x-auto">
            <table class="w-full table-hover">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Lote</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Proveedor</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Vencimiento</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ubicación</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Acción</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($lotes as $lote)
                    @php
                        // Cálculos seguros para cada lote
                        $fechaVencimiento = $lote->fecha_vencimiento ? \Carbon\Carbon::parse($lote->fecha_vencimiento) : null;
                        $estaVencido = $fechaVencimiento ? $fechaVencimiento->isPast() : false;
                        $porVencer = $fechaVencimiento ? ($fechaVencimiento->diffInDays(now()) <= 30 && !$estaVencido) : false;
                        $porcentaje = $lote->cantidad_inicial > 0 ? ($lote->cantidad_restante / $lote->cantidad_inicial) * 100 : 0;
                    @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($lote->producto && $lote->producto->imagenes && $lote->producto->imagenes->count() > 0)
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $lote->producto->imagenes->first()->ruta_imagen) }}" alt="{{ $lote->producto->nombre }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $lote->producto->nombre ?? 'Producto no encontrado' }}</div>
                                    <div class="text-sm text-gray-500">${{ number_format($lote->costo_unitario_usd, 2) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="font-medium">{{ $lote->numero_lote }}</div>
                            <div class="text-gray-500 text-xs">ID: {{ $lote->id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $lote->proveedor->razon_social ?? 'Proveedor no encontrado' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="font-medium">{{ number_format($lote->cantidad_restante, 0) }} / {{ number_format($lote->cantidad_inicial, 0) }}</div>
                            <div class="text-gray-500 text-xs">
                                {{ number_format($porcentaje, 1) }}% restante
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($estaVencido)
                                <span class="status-badge bg-red-100 text-red-800">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>Vencido
                                </span>
                                <div class="text-gray-500 text-xs mt-1">{{ $fechaVencimiento ? $fechaVencimiento->format('d/m/Y') : 'N/A' }}</div>
                            @elseif($porVencer)
                                <span class="status-badge bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>Por vencer
                                </span>
                                <div class="text-gray-500 text-xs mt-1">{{ $fechaVencimiento ? $fechaVencimiento->format('d/m/Y') : 'N/A' }}</div>
                            @else
                                <span class="status-badge bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Válido
                                </span>
                                <div class="text-gray-500 text-xs mt-1">{{ $fechaVencimiento ? $fechaVencimiento->format('d/m/Y') : 'N/A' }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $lote->ubicacion_almacen ?? 'No especificada' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($lote->activo)
                                <span class="status-badge bg-green-100 text-green-800">Activo</span>
                            @else
                                <span class="status-badge bg-gray-100 text-gray-800">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <a href="{{ route('admin.inventario.show', $lote->id) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 btn-action">
                                <i class="fas fa-eye mr-1"></i> Ver
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-8 text-gray-500">
                            <i class="fas fa-box-open fa-3x mb-3 text-light"></i><br>
                            <span class="fw-bold">No hay lotes de inventario</span><br>
                            <small>Los lotes aparecerán aquí automáticamente cuando se agreguen productos</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($lotes->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $lotes->links() }}
        </div>
        @endif
    </div>
</div>
@endsection