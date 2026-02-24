@extends('layouts.admin')

@section('title', 'Detalle de Receta Veterinaria - #' . $receta->id)
@section('page_header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-green-600"><i class="fas fa-file-prescription mr-2"></i>Receta Veterinaria</h1>
            <p class="text-gray-600 mt-1">Receta #{{ str_pad($receta->id, 4, '0', STR_PAD_LEFT) }} - {{ $receta->cliente->name ?? 'Cliente no encontrado' }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.recetas.index') }}" class="p-2 bg-gray-100 rounded-md hover:bg-gray-200 transition">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
            @if($receta->estado == 'pendiente')
            <a href="{{ route('admin.recetas.edit', $receta->id) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                <i class="fas fa-edit mr-2"></i>Editar
            </a>
            @endif
        </div>
    </div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-md">
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-4 rounded-t-xl">
                <h6 class="text-lg font-bold mb-0"><i class="fas fa-file-prescription mr-2"></i>Información de la Receta</h6>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h6 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Datos del Veterinario</h6>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nombre del Veterinario</dt>
                                <dd class="text-sm text-gray-900 font-medium">{{ $receta->veterinario_nombre }}</dd>
                            </div>
                            @if($receta->veterinario_matricula)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Matrícula Profesional</dt>
                                <dd class="text-sm text-gray-900 font-mono">{{ $receta->veterinario_matricula }}</dd>
                            </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Fecha de Prescripción</dt>
                                <dd class="text-sm text-gray-900">{{ $receta->fecha_prescription->format('d/m/Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <h6 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Datos del Animal</h6>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tipo de Animal</dt>
                                <dd class="text-sm text-gray-900 font-medium">{{ $receta->cliente_animal_tipo }}</dd>
                            </div>
                            @if($receta->cliente_animal_cantidad)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Cantidad de Animales</dt>
                                <dd class="text-sm text-gray-900">{{ $receta->cliente_animal_cantidad }}</dd>
                            </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Fecha de Vencimiento</dt>
                                <dd class="text-sm text-gray-900">
                                    @php
                                        $estaVencida = $receta->estaVencida();
                                        $porVencer = $receta->estaPorVencer();
                                    @endphp
                                    <span class="{{ $estaVencida ? 'text-red-600 font-semibold' : ($porVencer ? 'text-yellow-600 font-semibold' : 'text-green-600') }}">
                                        {{ $receta->fecha_vencimiento_receta->format('d/m/Y') }}
                                        @if($estaVencida)
                                            <i class="fas fa-exclamation-triangle ml-1"></i> (VENCIDA)
                                        @elseif($porVencer)
                                            <i class="fas fa-clock ml-1"></i> (Por vencer)
                                        @else
                                            <i class="fas fa-check-circle ml-1"></i> (Válida)
                                        @endif
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                @if($receta->observaciones)
                <div class="mt-6">
                    <h6 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Observaciones</h6>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $receta->observaciones }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        @if($receta->archivo_url)
        <div class="bg-white rounded-xl shadow-md">
            <div class="bg-gradient-to-r from-green-500 to-teal-600 text-white p-4 rounded-t-xl">
                <h6 class="text-lg font-bold mb-0"><i class="fas fa-file-alt mr-2"></i>Archivo Adjunto</h6>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-file-pdf text-red-500 text-2xl mr-3"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Receta Veterinaria</p>
                            <p class="text-xs text-gray-500">Archivo escaneado o PDF de la receta</p>
                        </div>
                    </div>
                    <a href="{{ Storage::url($receta->archivo_url) }}"
                       target="_blank"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                        <i class="fas fa-eye mr-2"></i>Ver Archivo
                    </a>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-xl shadow-md">
            <div class="bg-gradient-to-r from-purple-500 to-pink-600 text-white p-4 rounded-t-xl">
                <h6 class="text-lg font-bold mb-0"><i class="fas fa-pills mr-2"></i>Productos Prescritos</h6>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($receta->recetaProductos as $recetaProducto)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h5 class="text-lg font-semibold text-gray-900">{{ $recetaProducto->producto->nombre ?? 'Producto no encontrado' }}</h5>
                                <p class="text-sm text-gray-600 mt-1">{{ $recetaProducto->producto->descripcion ?? '' }}</p>

                                <div class="grid grid-cols-2 gap-4 mt-3">
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Cantidad Prescrita:</span>
                                        <span class="text-sm text-gray-900 ml-2">{{ number_format($recetaProducto->cantidad_prescrita, 3) }} {{ $recetaProducto->producto->unidad_medida ?? 'kg' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Estado:</span>
                                        <span class="text-sm ml-2">
                                            @if($recetaProducto->autorizado)
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i>Autorizado
                                                </span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    <i class="fas fa-clock mr-1"></i>Pendiente
                                                </span>
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                @if($recetaProducto->dosis_instrucciones)
                                <div class="mt-3">
                                    <span class="text-sm font-medium text-gray-500">Instrucciones de Dosificación:</span>
                                    <p class="text-sm text-gray-700 mt-1 bg-blue-50 p-2 rounded">{{ $recetaProducto->dosis_instrucciones }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-pills text-3xl mb-2"></i>
                        <p>No hay productos prescritos en esta receta</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-md">
            <div class="bg-gradient-to-r from-gray-700 to-gray-900 text-white p-4 rounded-t-xl">
                <h6 class="text-lg font-bold mb-0"><i class="fas fa-info-circle mr-2"></i>Estado</h6>
            </div>
            <div class="p-6">
                <div class="text-center">
                    @if($receta->estado == 'pendiente')
                        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                        </div>
                        <h4 class="text-lg font-bold text-gray-900 mb-2">Pendiente de Revisión</h4>
                        <p class="text-sm text-gray-600">Esta receta está esperando ser revisada por un veterinario autorizado.</p>
                    @elseif($receta->estado == 'aprobada')
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                        </div>
                        <h4 class="text-lg font-bold text-gray-900 mb-2">Receta Aprobada</h4>
                        <p class="text-sm text-gray-600">Esta receta ha sido validada y autorizada.</p>
                    @elseif($receta->estado == 'rechazada')
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                        </div>
                        <h4 class="text-lg font-bold text-gray-900 mb-2">Receta Rechazada</h4>
                        <p class="text-sm text-gray-600">Esta receta fue rechazada durante la revisión.</p>
                    @elseif($receta->estado == 'expirada')
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-calendar-times text-gray-600 text-2xl"></i>
                        </div>
                        <h4 class="text-lg font-bold text-gray-900 mb-2">Receta Expirada</h4>
                        <p class="text-sm text-gray-600">Esta receta ha expirado y ya no es válida.</p>
                    @endif
                </div>

                @if($receta->revisor)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h6 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Revisado Por</h6>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-user-md text-blue-600"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-900">{{ $receta->revisor->name }}</p>
                        <p class="text-xs text-gray-500">{{ $receta->fecha_revision ? $receta->fecha_revision->format('d/m/Y H:i') : '' }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        @if($receta->estado == 'pendiente')
        <div class="bg-white rounded-xl shadow-md">
            <div class="bg-gradient-to-r from-green-500 to-blue-600 text-white p-4 rounded-t-xl">
                <h6 class="text-lg font-bold mb-0"><i class="fas fa-tasks mr-2"></i>Acciones</h6>
            </div>
            <div class="p-6 space-y-3">
                <button type="button" onclick="openApproveModal()" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                    <i class="fas fa-check-circle mr-2"></i>Aprobar Receta
                </button>
                <button type="button" onclick="openRejectModal()" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                    <i class="fas fa-times-circle mr-2"></i>Rechazar Receta
                </button>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-xl shadow-md">
            <div class="bg-gradient-to-r from-cyan-500 to-blue-600 text-white p-4 rounded-t-xl">
                <h6 class="text-lg font-bold mb-0"><i class="fas fa-user mr-2"></i>Cliente</h6>
            </div>
            <div class="p-6">
                <div class="text-center">
                    <div class="h-16 w-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user text-gray-400 text-2xl"></i>
                    </div>

                    <h5 class="text-lg font-bold text-gray-900">{{ $receta->cliente->name ?? 'Cliente no encontrado' }}</h5>
                    <p class="text-gray-600 text-sm">{{ $receta->cliente->email ?? '' }}</p>

                    @if($receta->pedido)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600">Pedido relacionado:</p>
                        <a href="{{ route('admin.pedidos.show', $receta->pedido->id) }}"
                           class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i class="fas fa-shopping-cart mr-1"></i>Ver Pedido #{{ $receta->pedido->id }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-check-circle mr-2 text-green-600"></i>Aprobar Receta
                </h3>
                <button onclick="closeApproveModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="{{ route('admin.recetas.aprobar', $receta->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div class="text-center mb-6">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <h4 class="text-md font-semibold text-gray-900 mb-2">¿Aprobar esta receta?</h4>
                    <p class="text-sm text-gray-600">
                        Al aprobar esta receta, se autorizarán todos los productos prescritos para su venta.
                    </p>
                </div>

                <div>
                    <label for="observaciones_aprobacion" class="block text-sm font-medium text-gray-700 mb-1">
                        Observaciones (opcional)
                    </label>
                    <textarea id="observaciones_aprobacion"
                              name="observaciones_revision"
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                              placeholder="Agregar observaciones sobre la aprobación..."></textarea>
                </div>

                <div class="flex space-x-3 pt-4">
                    <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                        <i class="fas fa-check-circle mr-2"></i>Aprobar Receta
                    </button>
                    <button type="button" onclick="closeApproveModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-4 rounded-md transition duration-200">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-times-circle mr-2 text-red-600"></i>Rechazar Receta
                </h3>
                <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="{{ route('admin.recetas.rechazar', $receta->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div class="text-center mb-6">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <h4 class="text-md font-semibold text-gray-900 mb-2">¿Rechazar esta receta?</h4>
                    <p class="text-sm text-gray-600">
                        Al rechazar esta receta, no se permitirá la venta de los productos prescritos.
                    </p>
                </div>

                <div>
                    <label for="observaciones_rechazo" class="block text-sm font-medium text-gray-700 mb-1">
                        Motivo del Rechazo <span class="text-red-500">*</span>
                    </label>
                    <textarea id="observaciones_rechazo"
                              name="observaciones_revision"
                              rows="4"
                              required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                              placeholder="Explique el motivo del rechazo de la receta..."></textarea>
                </div>

                <div class="flex space-x-3 pt-4">
                    <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                        <i class="fas fa-times-circle mr-2"></i>Rechazar Receta
                    </button>
                    <button type="button" onclick="closeRejectModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-4 rounded-md transition duration-200">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openApproveModal() {
    document.getElementById('approveModal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
}

function openRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

// Cerrar modales al hacer clic fuera
document.getElementById('approveModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeApproveModal();
    }
});

document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});
</script>
@endsection