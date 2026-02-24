@extends('layouts.admin')

@section('title', 'Dashboard Administrativo')
@section('page_header')
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-green-600"><i class="fas fa-tachometer-alt mr-2"></i>Dashboard</h1>
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
.gradient-primary {
    background: linear-gradient(135deg, #007bff, #6610f2);
}
.gradient-danger {
    background: linear-gradient(135deg, #dc3545, #fd7e14);
}
.gradient-warning {
    background: linear-gradient(135deg, #ffc107, #e83e8c);
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
</style>

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-md hover-elevate h-40 card-stats">
        <div class="p-6 gradient-success text-white rounded-xl h-full flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h6 class="text-sm font-medium opacity-80 mb-2">Ingresos de Hoy</h6>
                    <h3 class="text-2xl font-bold">${{ number_format($totalIngresosUsd, 2) }}</h3>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full ml-4">
                    <i class="fas fa-dollar-sign text-xl"></i>
                </div>
            </div>
            <div class="text-sm mt-4">
                <span class="font-semibold"><i class="fas fa-shopping-bag mr-1"></i>{{ $totalPedidosHoy }}</span> pedidos hoy
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md hover-elevate h-40 card-stats">
        <div class="p-6 gradient-primary text-white rounded-xl h-full flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h6 class="text-sm font-medium opacity-80 mb-2">Pedidos en Curso</h6>
                    <h3 class="text-2xl font-bold">{{ $pedidosActivos }}</h3>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full ml-4">
                    <i class="fas fa-truck-fast text-xl"></i>
                </div>
            </div>
            <div class="text-sm mt-4">
                Pendientes y en preparación
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md hover-elevate h-40 card-stats">
        <div class="p-6 gradient-danger text-white rounded-xl h-full flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h6 class="text-sm font-medium opacity-80 mb-2">Stock Crítico</h6>
                    <h3 class="text-2xl font-bold">{{ $stockCritico }}</h3>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full ml-4">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
            </div>
            <div class="text-sm mt-4">
                <span class="font-semibold"><i class="fas fa-clock mr-1"></i>{{ $productosPorVencer }}</span> lotes por vencer
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md hover-elevate h-40 card-stats">
        <div class="p-6 gradient-warning text-white rounded-xl h-full flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h6 class="text-sm font-medium opacity-80 mb-2">Recetas por Validar</h6>
                    <h3 class="text-2xl font-bold">{{ $recetasPendientes }}</h3>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full ml-4">
                    <i class="fas fa-file-medical text-xl"></i>
                </div>
            </div>
            <div class="text-sm mt-4">
                Requieren revisión veterinaria
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-md">
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-4 rounded-t-xl flex justify-between items-center">
                <h6 class="text-lg font-bold mb-0"><i class="fas fa-clock mr-2"></i>Últimos Pedidos Registrados</h6>
                <a href="{{ route('admin.pedidos.index') }}" class="bg-white text-blue-600 px-3 py-1 rounded-md text-sm font-semibold shadow hover:bg-gray-100 transition">
                    <i class="fas fa-list mr-1"></i>Ver todos
                </a>
            </div>
            <div class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full table-hover">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($ultimosPedidos as $pedido)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $pedido->nombre }} {{ $pedido->apellido }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($pedido->creado_at)->format('d/m/Y h:i A') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">${{ number_format($pedido->total_usd, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($pedido->estado == 'pendiente')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Pendiente</span>
                                    @elseif($pedido->estado == 'pagado')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Pagado</span>
                                    @elseif($pedido->estado == 'entregado')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Entregado</span>
                                    @elseif($pedido->estado == 'completado_caja')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Completado</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <a href="{{ route('admin.pedidos.show', $pedido->id) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 btn-action">
                                        <i class="fas fa-eye mr-1"></i> Ver
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-box-open fa-3x mb-3 text-light"></i><br>
                                    <span class="fw-bold">No hay pedidos recientes</span><br>
                                    <small>Los nuevos pedidos aparecerán aquí automáticamente</small>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-md">
            <div class="bg-gradient-to-r from-cyan-500 to-blue-600 text-white p-4 rounded-t-xl">
                <h6 class="text-lg font-bold mb-0"><i class="fas fa-chart-line mr-2"></i>Resumen Semanal</h6>
            </div>
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-sm text-gray-600">Pedidos esta semana</span>
                    <span class="font-bold text-blue-600">{{ $totalPedidosSemana }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $totalPedidosSemana > 0 ? min(100, ($totalPedidosSemana / 50) * 100) : 0 }}%"></div>
                </div>
                <div class="flex justify-between items-center mb-4">
                    <span class="text-sm text-gray-600">Ingresos semanales</span>
                    <span class="font-bold text-green-600">${{ number_format($totalIngresosSemana, 2) }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mb-6">
                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $totalIngresosSemana > 0 ? min(100, ($totalIngresosSemana / 10000) * 100) : 0 }}%"></div>
                </div>
                <div class="text-center">
                    <a href="#" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Ver Reporte Completo
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md">
            <div class="bg-gradient-to-r from-gray-700 to-gray-900 text-white p-4 rounded-t-xl">
                <h6 class="text-lg font-bold mb-0"><i class="fas fa-star mr-2"></i>Productos Más Vendidos</h6>
            </div>
            <div class="p-6 space-y-4">
                @forelse($productosMasVendidos as $index => $producto)
                <div class="flex items-center">
                    <div class="bg-{{ ['green', 'blue', 'yellow'][$index] }}-100 p-3 rounded-full mr-4">
                        <i class="fas fa-{{ ['seedling', 'tractor', 'pills'][$index] }} text-{{ ['green', 'blue', 'yellow'][$index] }}-600"></i>
                    </div>
                    <div class="flex-1">
                        <h6 class="text-sm font-bold text-gray-900 mb-1">{{ $producto->nombre }}</h6>
                        <small class="text-gray-500">{{ $producto->total_vendido }} unidades vendidas</small>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ ['green', 'blue', 'yellow'][$index] }}-100 text-{{ ['green', 'blue', 'yellow'][$index] }}-800">
                        #{{ $index + 1 }}
                    </span>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-chart-bar text-3xl mb-2"></i>
                    <p>No hay datos de ventas aún</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection