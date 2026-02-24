@extends('layouts.admin')

@section('title', 'Pedidos')
@section('page_header')
    Pedidos
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="m-0 fw-bold"><i class="fas fa-list me-2 text-primary"></i> Lista de Pedidos</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pedidos as $pedido)
                    <tr>
                        <td class="fw-medium">#{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $pedido->usuario->nombre ?? 'N/A' }} {{ $pedido->usuario->apellido ?? '' }}</td>
                        <td>{{ $pedido->creado_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <span class="badge 
                                @if($pedido->estado == 'pendiente') bg-secondary
                                @elseif($pedido->estado == 'pagado') bg-info
                                @elseif($pedido->estado == 'entregado' || $pedido->estado == 'completado_caja') bg-success
                                @else bg-primary @endif">
                                {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                            </span>
                        </td>
                        <td class="text-end fw-bold">${{ number_format($pedido->total_usd, 2) }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.pedidos.show', $pedido->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $pedidos->links() }}
        </div>
    </div>
</div>
@endsection