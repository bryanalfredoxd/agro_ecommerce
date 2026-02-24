@extends('layouts.admin')

@section('title', 'Detalle del Cliente - ' . $cliente->nombre)
@section('page_header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-green-600"><i class="fas fa-user mr-2"></i>Cliente</h1>
            <p class="text-gray-600 mt-1">{{ $cliente->nombre }} {{ $cliente->apellido }} - {{ $cliente->email }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.clientes.index') }}" class="p-2 bg-gray-100 rounded-md hover:bg-gray-200 transition">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
            <a href="{{ route('admin.clientes.edit', $cliente->id) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                <i class="fas fa-edit mr-2"></i>Editar
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-md">
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-4 rounded-t-xl">
                <h6 class="text-lg font-bold mb-0"><i class="fas fa-user mr-2"></i>Información del Cliente</h6>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h6 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Datos Personales</h6>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nombre Completo</dt>
                                <dd class="text-sm text-gray-900 font-medium">{{ $cliente->nombre }} {{ $cliente->apellido }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Correo Electrónico</dt>
                                <dd class="text-sm text-gray-900">{{ $cliente->email }}</dd>
                            </div>
                            @if($cliente->telefono)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                                <dd class="text-sm text-gray-900">{{ $cliente->telefono }}</dd>
                            </div>
                            @endif
                            @if($cliente->documento_identidad)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Documento de Identidad</dt>
                                <dd class="text-sm text-gray-900 font-mono">{{ $cliente->documento_identidad }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                    <div>
                        <h6 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Información Adicional</h6>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tipo de Cliente</dt>
                                <dd class="text-sm text-gray-900 font-medium">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($cliente->tipo_cliente == 'natural') bg-blue-100 text-blue-800
                                        @elseif($cliente->tipo_cliente == 'juridico') bg-purple-100 text-purple-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $cliente->tipo_cliente)) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Estado</dt>
                                <dd class="text-sm text-gray-900">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $cliente->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        <i class="fas {{ $cliente->activo ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                        {{ $cliente->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Fecha de Registro</dt>
                                <dd class="text-sm text-gray-900">{{ $cliente->creado_at ? $cliente->creado_at->format('d/m/Y H:i') : 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Última Actualización</dt>
                                <dd class="text-sm text-gray-900">{{ $cliente->actualizado_at ? $cliente->actualizado_at->format('d/m/Y H:i') : 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md">
            <div class="bg-gradient-to-r from-green-500 to-teal-600 text-white p-4 rounded-t-xl">
                <h6 class="text-lg font-bold mb-0"><i class="fas fa-shopping-cart mr-2"></i>Pedidos Recientes</h6>
            </div>
            <div class="p-6">
                @if($cliente->pedidos->count() > 0)
                <div class="space-y-4">
                    @foreach($cliente->pedidos->take(5) as $pedido)
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <h5 class="text-lg font-semibold text-gray-900">Pedido #{{ $pedido->id }}</h5>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($pedido->estado == 'completado' || $pedido->estado == 'entregado' || $pedido->estado == 'completado_caja') bg-green-100 text-green-800
                                        @elseif($pedido->estado == 'en_ruta') bg-blue-100 text-blue-800
                                        @elseif($pedido->estado == 'preparacion') bg-yellow-100 text-yellow-800
                                        @elseif($pedido->estado == 'pagado') bg-purple-100 text-purple-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                                    <div>
                                        <span class="font-medium">Fecha:</span> {{ $pedido->creado_at ? $pedido->creado_at->format('d/m/Y H:i') : 'N/A' }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Total:</span> ${{ number_format($pedido->total_usd, 2) }}
                                    </div>
                                </div>

                                @if($pedido->detallesPedido && $pedido->detallesPedido->count() > 0)
                                <div class="mt-3">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Productos:</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($pedido->detallesPedido->take(3) as $detalle)
                                        <span class="inline-flex px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded">
                                            {{ $detalle->producto ? $detalle->producto->nombre : 'Producto eliminado' }}
                                            ({{ $detalle->cantidad }})
                                        </span>
                                        @endforeach
                                        @if($pedido->detallesPedido->count() > 3)
                                        <span class="inline-flex px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded">
                                            +{{ $pedido->detallesPedido->count() - 3 }} más
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end">
                            <a href="{{ route('admin.pedidos.show', $pedido->id) }}"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                <i class="fas fa-eye mr-1"></i>Ver Detalles
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($cliente->pedidos->count() > 5)
                <div class="mt-6 text-center">
                    <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">
                        Ver todos los pedidos ({{ $cliente->pedidos->count() }})
                    </a>
                </div>
                @endif

                @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-shopping-cart text-3xl mb-4"></i>
                    <p class="text-lg">Este cliente aún no ha realizado pedidos</p>
                    <p class="text-sm">Los pedidos aparecerán aquí cuando el cliente realice compras</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-md">
            <div class="bg-gradient-to-r from-purple-500 to-pink-600 text-white p-4 rounded-t-xl">
                <h6 class="text-lg font-bold mb-0"><i class="fas fa-chart-bar mr-2"></i>Estadísticas</h6>
            </div>
            <div class="p-6 space-y-4">
                <div class="text-center">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $estadisticas['total_pedidos'] }}</div>
                            <div class="text-sm text-gray-600">Total Pedidos</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $estadisticas['pedidos_completados'] }}</div>
                            <div class="text-sm text-gray-600">Completados</div>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">${{ number_format($estadisticas['total_gastado'], 2) }}</div>
                            <div class="text-sm text-gray-600">Total Gastado</div>
                        </div>
                    </div>

                    @if($estadisticas['ultimo_pedido'])
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="text-sm text-gray-600 mb-1">Último Pedido</div>
                        <div class="text-sm font-medium text-gray-900">
                            {{ $estadisticas['ultimo_pedido']->creado_at ? $estadisticas['ultimo_pedido']->creado_at->format('d/m/Y') : 'N/A' }}
                        </div>
                        <div class="text-xs text-gray-500">
                            ${{ number_format($estadisticas['ultimo_pedido']->total_usd, 2) }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md">
            <div class="bg-gradient-to-r from-green-500 to-blue-600 text-white p-4 rounded-t-xl">
                <h6 class="text-lg font-bold mb-0"><i class="fas fa-tasks mr-2"></i>Acciones</h6>
            </div>
            <div class="p-6 space-y-3">
                <form action="{{ route('admin.clientes.toggle-status', $cliente->id) }}" method="POST" class="w-full">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full {{ $cliente->activo ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white font-medium py-2 px-4 rounded-md transition duration-200">
                        <i class="fas {{ $cliente->activo ? 'fa-ban' : 'fa-check' }} mr-2"></i>
                        {{ $cliente->activo ? 'Desactivar Cliente' : 'Activar Cliente' }}
                    </button>
                </form>

                <button type="button" onclick="openPasswordModal()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                    <i class="fas fa-key mr-2"></i>Cambiar Contraseña
                </button>

                @if($cliente->pedidos->count() == 0)
                <form action="{{ route('admin.clientes.destroy', $cliente->id) }}" method="POST" class="w-full"
                      onsubmit="return confirm('¿Estás seguro de eliminar este cliente? Esta acción no se puede deshacer.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                        <i class="fas fa-trash mr-2"></i>Eliminar Cliente
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

<div id="passwordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-key mr-2 text-blue-600"></i>Cambiar Contraseña
                </h3>
                <button onclick="closePasswordModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="{{ route('admin.clientes.update-password', $cliente->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Nueva Contraseña <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        Confirmar Contraseña <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="flex space-x-3 pt-4">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                        <i class="fas fa-save mr-2"></i>Cambiar Contraseña
                    </button>
                    <button type="button" onclick="closePasswordModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-4 rounded-md transition duration-200">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openPasswordModal() {
    document.getElementById('passwordModal').classList.remove('hidden');
}

function closePasswordModal() {
    document.getElementById('passwordModal').classList.add('hidden');
}

// Cerrar modal al hacer clic fuera
document.getElementById('passwordModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePasswordModal();
    }
});
</script>
@endsection