@extends('layouts.admin')

@section('title', 'Dashboard Administrativo')
@section('page_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0 fw-bold text-primary"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h1>
        <small class="text-muted">{{ now()->format('l, d F Y') }}</small>
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

<div class="row g-4 mb-5">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 rounded-4 hover-elevate h-100 card-stats">
            <div class="card-body gradient-success text-white rounded-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-normal mb-2 opacity-75">Ingresos de Hoy</h6>
                        <h3 class="mb-0 fw-bold">${{ number_format($totalIngresosUsd, 2) }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 p-3 rounded-circle">
                        <i class="fas fa-dollar-sign fa-lg"></i>
                    </div>
                </div>
                <div class="mt-3 small">
                    <span class="fw-bold"><i class="fas fa-shopping-bag me-1"></i>{{ $totalPedidosHoy }}</span> pedidos hoy
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 rounded-4 hover-elevate h-100 card-stats">
            <div class="card-body gradient-primary text-white rounded-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-normal mb-2 opacity-75">Pedidos en Curso</h6>
                        <h3 class="mb-0 fw-bold">{{ $pedidosActivos }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 p-3 rounded-circle">
                        <i class="fas fa-truck-fast fa-lg"></i>
                    </div>
                </div>
                <div class="mt-3 small">
                    Pendientes y en preparación
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 rounded-4 hover-elevate h-100 card-stats">
            <div class="card-body gradient-danger text-white rounded-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-normal mb-2 opacity-75">Stock Crítico</h6>
                        <h3 class="mb-0 fw-bold">{{ $stockCritico }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 p-3 rounded-circle">
                        <i class="fas fa-exclamation-triangle fa-lg"></i>
                    </div>
                </div>
                <div class="mt-3 small">
                    <span class="fw-bold"><i class="fas fa-clock me-1"></i>{{ $productosPorVencer }}</span> lotes por vencer
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 rounded-4 hover-elevate h-100 card-stats">
            <div class="card-body gradient-warning text-white rounded-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-normal mb-2 opacity-75">Recetas por Validar</h6>
                        <h3 class="mb-0 fw-bold">{{ $recetasPendientes }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 p-3 rounded-circle">
                        <i class="fas fa-file-medical fa-lg"></i>
                    </div>
                </div>
                <div class="mt-3 small">
                    Requieren revisión veterinaria
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-gradient-primary text-white border-0 rounded-top-4 py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold mb-0"><i class="fas fa-clock me-2"></i>Últimos Pedidos Registrados</h6>
                <a href="{{ route('admin.pedidos.index') }}" class="btn btn-sm btn-light text-primary fw-bold shadow-sm">
                    <i class="fas fa-list me-1"></i>Ver todos
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 fw-bold text-muted">ID</th>
                                <th class="fw-bold text-muted">Cliente</th>
                                <th class="fw-bold text-muted">Fecha</th>
                                <th class="fw-bold text-muted">Total</th>
                                <th class="fw-bold text-muted">Estado</th>
                                <th class="pe-4 fw-bold text-muted text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimosPedidos as $pedido)
                            <tr>
                                <td class="ps-4 text-muted fw-medium">#{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td class="fw-medium">{{ $pedido->nombre }} {{ $pedido->apellido }}</td>
                                <td class="text-muted">{{ \Carbon\Carbon::parse($pedido->creado_at)->format('d/m/Y h:i A') }}</td>
                                <td class="fw-bold text-success">${{ number_format($pedido->total_usd, 2) }}</td>
                                <td>
                                    @if($pedido->estado == 'pendiente')
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">Pendiente</span>
                                    @elseif($pedido->estado == 'pagado')
                                        <span class="badge bg-info rounded-pill px-3 py-2">Pagado</span>
                                    @elseif($pedido->estado == 'entregado')
                                        <span class="badge bg-success rounded-pill px-3 py-2">Entregado</span>
                                    @elseif($pedido->estado == 'completado_caja')
                                        <span class="badge bg-success rounded-pill px-3 py-2">Completado</span>
                                    @else
                                        <span class="badge bg-primary rounded-pill px-3 py-2">{{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-center">
                                    <a href="{{ route('admin.pedidos.show', $pedido->id) }}" class="btn btn-sm btn-outline-primary rounded-circle btn-action" title="Ver detalle del pedido">
                                        <i class="fas fa-eye"></i>
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

    <div class="col-12 col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-gradient-info text-white border-0 rounded-top-4 py-3">
                <h6 class="m-0 fw-bold mb-0"><i class="fas fa-chart-line me-2"></i>Resumen Semanal</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">Pedidos esta semana</span>
                    <span class="fw-bold text-primary">{{ $totalPedidosSemana }}</span>
                </div>
                <div class="progress mb-3" style="height: 8px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $totalPedidosSemana > 0 ? min(100, ($totalPedidosSemana / 50) * 100) : 0 }}%" aria-valuenow="{{ $totalPedidosSemana }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">Ingresos semanales</span>
                    <span class="fw-bold text-success">${{ number_format($totalIngresosSemana, 2) }}</span>
                </div>
                <div class="progress mb-3" style="height: 8px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $totalIngresosSemana > 0 ? min(100, ($totalIngresosSemana / 10000) * 100) : 0 }}%" aria-valuenow="{{ $totalIngresosSemana }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="text-center mt-4">
                    <a href="#" class="btn btn-outline-primary btn-sm fw-bold">Ver Reporte Completo</a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-gradient-secondary text-white border-0 rounded-top-4 py-3">
                <h6 class="m-0 fw-bold mb-0"><i class="fas fa-star me-2"></i>Productos Más Vendidos</h6>
            </div>
            <div class="card-body">
                @forelse($productosMasVendidos as $index => $producto)
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-{{ ['primary', 'success', 'warning'][$index] }} bg-opacity-10 p-2 rounded-circle me-3">
                        <i class="fas fa-{{ ['seedling', 'tractor', 'pills'][$index] }} text-{{ ['primary', 'success', 'warning'][$index] }}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold">{{ $producto->nombre }}</h6>
                        <small class="text-muted">{{ $producto->total_vendido }} unidades vendidas</small>
                    </div>
                    <span class="badge bg-{{ ['success', 'warning', 'info'][$index] }}">#{{ $index + 1 }}</span>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-chart-bar fa-2x mb-2"></i>
                    <p>No hay datos de ventas aún</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection