@extends('layouts.admin')

@section('title', 'Detalle del Pedido #' . str_pad($pedido->id, 5, '0', STR_PAD_LEFT))
@section('page_header')
    <a href="{{ route('admin.pedidos.index') }}" class="btn btn-sm btn-light border shadow-sm me-2"><i class="fas fa-arrow-left"></i></a>
    Pedido #{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    <div class="col-12 col-lg-8">
        
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="m-0 fw-bold"><i class="fas fa-box-open me-2 text-primary"></i> Productos del Pedido</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Producto</th>
                                <th>SKU</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-end text-muted">Precio Unit.</th>
                                <th class="pe-4 text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detalles as $item)
                            <tr>
                                <td class="ps-4 fw-medium">{{ $item->nombre }}</td>
                                <td class="text-muted text-sm">{{ $item->sku }}</td>
                                <td class="text-center fw-bold">{{ number_format($item->cantidad_solicitada, 0) }}</td>
                                <td class="text-end text-muted">${{ number_format($item->precio_historico_usd, 2) }}</td>
                                <td class="pe-4 text-end fw-bold text-success">${{ number_format($item->cantidad_solicitada * $item->precio_historico_usd, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end text-muted">Subtotal:</td>
                                <td class="pe-4 text-end fw-bold">${{ number_format($pedido->subtotal_usd, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end text-muted border-0">Delivery:</td>
                                <td class="pe-4 text-end fw-bold border-0">${{ number_format($pedido->costo_delivery_usd, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end text-dark fw-bold border-0 fs-5">Total a Pagar:</td>
                                <td class="pe-4 text-end fw-black text-success fs-5 border-0">${{ number_format($pedido->total_usd, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-md-6">
                <div class="card shadow-sm border-0 rounded-3 h-100">
                    <div class="card-body">
                        <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-user me-2 text-secondary"></i> Datos del Cliente</h6>
                        <p class="mb-1"><span class="text-muted">Nombre:</span> {{ $pedido->usuario->nombre ?? 'N/A' }} {{ $pedido->usuario->apellido ?? '' }}</p>
                        <p class="mb-1"><span class="text-muted">Teléfono:</span> {{ $pedido->usuario->telefono ?? 'No registrado' }}</p>
                        <p class="mb-0"><span class="text-muted">Email:</span> {{ $pedido->usuario->email ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card shadow-sm border-0 rounded-3 h-100">
                    <div class="card-body">
                        <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-truck me-2 text-secondary"></i> Información de Envío</h6>
                        <p class="mb-1"><span class="text-muted">Zona:</span> {{ $pedido->zonaDelivery->nombre_zona ?? 'Retiro en Tienda' }}</p>
                        <p class="mb-1"><span class="text-muted">Dirección:</span></p>
                        <p class="small text-dark mb-0 bg-light p-2 rounded">{{ $pedido->direccion_texto ?? 'El cliente retirará en el local.' }}</p>
                        @if($pedido->instrucciones_entrega)
                            <p class="mt-2 mb-0 small text-danger"><i class="fas fa-exclamation-circle"></i> {{ $pedido->instrucciones_entrega }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="col-12 col-lg-4">
        <div class="card shadow-sm border-0 rounded-3 border-top border-primary border-4 sticky-top" style="top: 100px;">
            <div class="card-body">
                <h5 class="fw-bold text-center mb-4">Gestión del Pedido</h5>

                <form action="{{ route('admin.pedidos.update', $pedido->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold text-uppercase">Estado Actual</label>
                        <select name="estado" class="form-select form-select-lg fw-bold 
                            @if($pedido->estado == 'pendiente') text-secondary
                            @elseif($pedido->estado == 'pagado') text-info
                            @elseif($pedido->estado == 'entregado' || $pedido->estado == 'completado_caja') text-success
                            @else text-primary @endif"
                        >
                            <option value="pendiente" {{ $pedido->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="pagado" {{ $pedido->estado == 'pagado' ? 'selected' : '' }}>Pagado</option>
                            <option value="preparacion" {{ $pedido->estado == 'preparacion' ? 'selected' : '' }}>En Preparación</option>
                            <option value="en_ruta" {{ $pedido->estado == 'en_ruta' ? 'selected' : '' }}>En Ruta / Delivery</option>
                            <option value="entregado" {{ $pedido->estado == 'entregado' ? 'selected' : '' }}>Entregado</option>
                            <option value="completado_caja" {{ $pedido->estado == 'completado_caja' ? 'selected' : '' }}>Completado en Caja</option>
                            <option value="cancelado" {{ $pedido->estado == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            <option value="devuelto" {{ $pedido->estado == 'devuelto' ? 'selected' : '' }}>Devuelto</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm"><i class="fas fa-save me-2"></i> Actualizar Estado</button>
                    
                    <div class="text-center mt-3 text-muted small">
                        <i class="fas fa-info-circle"></i> Al marcar como "Pagado", el sistema descontará el inventario automáticamente.
                    </div>
                </form>

                <hr class="my-4">

                <h6 class="fw-bold mb-3"><i class="fas fa-money-bill-wave text-success me-2"></i> Reporte de Pago</h6>
                
                @if(!$pedido->pago)
                    <div class="alert alert-light text-center border mb-0 text-muted small">
                        No hay reportes de pago registrados aún.
                    </div>
                @else
                    <div class="bg-light p-3 rounded mb-2 border">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="badge bg-dark">{{ strtoupper(str_replace('_', ' ', $pedido->pago->metodo)) }}</span>
                            <span class="fw-bold text-success">${{ number_format($pedido->pago->monto_usd, 2) }}</span>
                        </div>
                        <div class="small text-muted mb-1">Ref: <span class="text-dark fw-medium">{{ $pedido->pago->referencia_bancaria }}</span></div>
                        <div class="small fw-bold {{ $pedido->pago->estado == 'aprobado' ? 'text-success' : 'text-warning' }}">{{ strtoupper($pedido->pago->estado) }}</div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection