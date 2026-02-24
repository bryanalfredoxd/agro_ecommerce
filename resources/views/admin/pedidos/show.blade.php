@extends('layouts.admin')

@section('title', 'Detalle del Pedido #' . str_pad($pedido->id, 5, '0', STR_PAD_LEFT))
@section('page_header')
    <div class="flex justify-between items-center">
        <div class="flex items-center">
            <a href="{{ route('admin.pedidos.index') }}" class="mr-3 p-2 bg-gray-100 rounded-md hover:bg-gray-200 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Pedido #{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}</h1>
        </div>
        <span class="text-sm text-gray-500">{{ $pedido->creado_at->format('d/m/Y H:i') }}</span>
    </div>
@endsection

@section('content')
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button class="ml-auto text-green-700 hover:text-green-900" onclick="this.parentElement.style.display='none'">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <!-- Productos del Pedido -->
        <div class="bg-white rounded-xl shadow-md">
            <div class="bg-gradient-to-r from-green-500 to-teal-600 text-white p-4 rounded-t-xl">
                <h6 class="text-lg font-bold mb-0"><i class="fas fa-box-open mr-2"></i> Productos del Pedido</h6>
            </div>
            <div class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">SKU</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Cant.</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">Precio Unit.</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($detalles as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->nombre }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->sku }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-bold">{{ number_format($item->cantidad_solicitada, 0) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">${{ number_format($item->precio_historico_usd, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600 text-right">${{ number_format($item->cantidad_solicitada * $item->precio_historico_usd, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-600">Subtotal:</td>
                                <td class="px-6 py-3 text-right text-sm font-bold">${{ number_format($pedido->subtotal_usd, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-600">Delivery:</td>
                                <td class="px-6 py-3 text-right text-sm font-bold">${{ number_format($pedido->costo_delivery_usd, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="px-6 py-3 text-right text-lg font-bold text-gray-900">Total a Pagar:</td>
                                <td class="px-6 py-3 text-right text-lg font-black text-green-600">${{ number_format($pedido->total_usd, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Información de Envío -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-md">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white p-4 rounded-t-xl">
                    <h6 class="text-sm font-bold mb-0"><i class="fas fa-user mr-2"></i> Datos del Cliente</h6>
                </div>
                <div class="p-6">
                    <p class="mb-2"><span class="text-gray-600">Nombre:</span> <span class="font-medium">{{ $pedido->usuario->nombre ?? 'N/A' }} {{ $pedido->usuario->apellido ?? '' }}</span></p>
                    <p class="mb-2"><span class="text-gray-600">Teléfono:</span> <span class="font-medium">{{ $pedido->usuario->telefono ?? 'No registrado' }}</span></p>
                    <p class="mb-0"><span class="text-gray-600">Email:</span> <span class="font-medium">{{ $pedido->usuario->email ?? 'N/A' }}</span></p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md">
                <div class="bg-gradient-to-r from-purple-500 to-pink-600 text-white p-4 rounded-t-xl">
                    <h6 class="text-sm font-bold mb-0"><i class="fas fa-truck mr-2"></i> Información de Envío</h6>
                </div>
                <div class="p-6">
                    <p class="mb-2"><span class="text-gray-600">Zona:</span> <span class="font-medium">{{ $pedido->zonaDelivery->nombre_zona ?? 'Retiro en Tienda' }}</span></p>
                    <p class="mb-2"><span class="text-gray-600">Dirección:</span></p>
                    <p class="text-sm text-gray-700 bg-gray-100 p-3 rounded-lg">{{ $pedido->direccion_texto ?? 'El cliente retirará en el local.' }}</p>
                    @if($pedido->instrucciones_entrega)
                        <p class="mt-3 text-sm text-red-600 bg-red-50 p-2 rounded"><i class="fas fa-exclamation-circle mr-1"></i> {{ $pedido->instrucciones_entrega }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-md sticky top-6">
            <div class="bg-gradient-to-r from-yellow-500 to-orange-600 text-white p-4 rounded-t-xl">
                <h5 class="text-lg font-bold mb-0">Gestión del Pedido</h5>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.pedidos.update', $pedido->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado Actual</label>
                        <select name="estado" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 
                            @if($pedido->estado == 'pendiente') bg-gray-100
                            @elseif($pedido->estado == 'pagado') bg-blue-100
                            @elseif($pedido->estado == 'entregado' || $pedido->estado == 'completado_caja') bg-green-100
                            @else bg-purple-100 @endif">
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

                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold">
                        <i class="fas fa-save mr-2"></i> Actualizar Estado
                    </button>

                    <div class="text-center mt-4 text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-1"></i> Al marcar como "Pagado", el sistema descontará el inventario automáticamente.
                    </div>
                </form>

                <hr class="my-6">

                <h6 class="text-lg font-bold mb-4"><i class="fas fa-money-bill-wave text-green-600 mr-2"></i> Reporte de Pago</h6>

                @if(!$pedido->pago)
                    <div class="text-center py-6 text-gray-500 bg-gray-50 rounded-lg">
                        No hay reportes de pago registrados aún.
                    </div>
                @else
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-800">{{ strtoupper(str_replace('_', ' ', $pedido->pago->metodo)) }}</span>
                            <span class="font-bold text-green-600">${{ number_format($pedido->pago->monto_usd, 2) }}</span>
                        </div>
                        <div class="text-sm text-gray-600 mb-1">Ref: <span class="font-medium text-gray-900">{{ $pedido->pago->referencia_bancaria }}</span></div>
                        <div class="text-sm font-bold 
                            @if($pedido->pago->estado == 'aprobado') text-green-600
                            @else text-yellow-600 @endif">
                            {{ strtoupper($pedido->pago->estado) }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection