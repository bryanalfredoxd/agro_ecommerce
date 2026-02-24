@extends('layouts.admin')

@section('title', 'Recetas Veterinarias')
@section('page_header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-green-600"><i class="fas fa-file-prescription mr-2"></i>Recetas Veterinarias</h1>
            <p class="text-gray-600 mt-1">Gestión de recetas veterinarias para productos controlados</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.recetas.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                <i class="fas fa-plus mr-2"></i>Nueva Receta
            </a>
        </div>
    </div>
@endsection

@section('content')
<style>
.hover-elevate:hover {
    transform: translateY(-2px);
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}
.filter-active {
    background-color: rgba(34, 197, 94, 0.1);
    border-color: rgb(34, 197, 94);
    color: rgb(34, 197, 94);
}
</style>

<!-- Estadísticas -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-md p-4 hover-elevate">
        <div class="flex items-center">
            <div class="bg-blue-100 p-3 rounded-full">
                <i class="fas fa-file-prescription text-blue-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-bold text-gray-900">{{ $estadisticas['total'] }}</h3>
                <p class="text-sm text-gray-600">Total</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-4 hover-elevate">
        <div class="flex items-center">
            <div class="bg-yellow-100 p-3 rounded-full">
                <i class="fas fa-clock text-yellow-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-bold text-gray-900">{{ $estadisticas['pendientes'] }}</h3>
                <p class="text-sm text-gray-600">Pendientes</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-4 hover-elevate">
        <div class="flex items-center">
            <div class="bg-green-100 p-3 rounded-full">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-bold text-gray-900">{{ $estadisticas['aprobadas'] }}</h3>
                <p class="text-sm text-gray-600">Aprobadas</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-4 hover-elevate">
        <div class="flex items-center">
            <div class="bg-red-100 p-3 rounded-full">
                <i class="fas fa-times-circle text-red-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-bold text-gray-900">{{ $estadisticas['rechazadas'] }}</h3>
                <p class="text-sm text-gray-600">Rechazadas</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-4 hover-elevate">
        <div class="flex items-center">
            <div class="bg-orange-100 p-3 rounded-full">
                <i class="fas fa-exclamation-triangle text-orange-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-bold text-gray-900">{{ $estadisticas['por_vencer'] }}</h3>
                <p class="text-sm text-gray-600">Por Vencer</p>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.recetas.index') }}"
           class="px-4 py-2 rounded-md text-sm font-medium transition duration-200 {{ !request('estado') ? 'filter-active bg-green-100 text-green-800 border border-green-300' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Todas
        </a>
        <a href="{{ route('admin.recetas.index', ['estado' => 'pendiente']) }}"
           class="px-4 py-2 rounded-md text-sm font-medium transition duration-200 {{ request('estado') === 'pendiente' ? 'filter-active bg-green-100 text-green-800 border border-green-300' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Pendientes
        </a>
        <a href="{{ route('admin.recetas.index', ['estado' => 'aprobada']) }}"
           class="px-4 py-2 rounded-md text-sm font-medium transition duration-200 {{ request('estado') === 'aprobada' ? 'filter-active bg-green-100 text-green-800 border border-green-300' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Aprobadas
        </a>
        <a href="{{ route('admin.recetas.index', ['estado' => 'rechazada']) }}"
           class="px-4 py-2 rounded-md text-sm font-medium transition duration-200 {{ request('estado') === 'rechazada' ? 'filter-active bg-green-100 text-green-800 border border-green-300' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Rechazadas
        </a>
        <a href="{{ route('admin.recetas.index', ['estado' => 'expirada']) }}"
           class="px-4 py-2 rounded-md text-sm font-medium transition duration-200 {{ request('estado') === 'expirada' ? 'filter-active bg-green-100 text-green-800 border border-green-300' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Expiradas
        </a>
    </div>
</div>

<!-- Tabla de Recetas -->
<div class="bg-white rounded-lg shadow-md">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Lista de Recetas Veterinarias</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Veterinario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Animal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimiento</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($recetas as $receta)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        #{{ str_pad($receta->id, 4, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div>
                            <div class="font-medium">{{ $receta->cliente->name ?? 'N/A' }}</div>
                            <div class="text-gray-500 text-xs">{{ $receta->cliente->email ?? '' }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div>
                            <div class="font-medium">{{ $receta->veterinario_nombre }}</div>
                            @if($receta->veterinario_matricula)
                                <div class="text-gray-500 text-xs">Matr: {{ $receta->veterinario_matricula }}</div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div>
                            <div class="font-medium">{{ $receta->cliente_animal_tipo }}</div>
                            @if($receta->cliente_animal_cantidad)
                                <div class="text-gray-500 text-xs">{{ $receta->cliente_animal_cantidad }} animales</div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @php
                            $estaVencida = $receta->estaVencida();
                            $porVencer = $receta->estaPorVencer();
                        @endphp
                        <span class="{{ $estaVencida ? 'text-red-600 font-semibold' : ($porVencer ? 'text-yellow-600 font-semibold' : 'text-green-600') }}">
                            {{ $receta->fecha_vencimiento_receta ? $receta->fecha_vencimiento_receta->format('d/m/Y') : 'N/A' }}
                            @if($estaVencida)
                                <i class="fas fa-exclamation-triangle ml-1"></i>
                            @elseif($porVencer)
                                <i class="fas fa-clock ml-1"></i>
                            @else
                                <i class="fas fa-check-circle ml-1"></i>
                            @endif
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($receta->estado == 'pendiente')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i>Pendiente
                            </span>
                        @elseif($receta->estado == 'aprobada')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>Aprobada
                            </span>
                        @elseif($receta->estado == 'rechazada')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>Rechazada
                            </span>
                        @elseif($receta->estado == 'expirada')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                <i class="fas fa-calendar-times mr-1"></i>Expirada
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.recetas.show', $receta->id) }}"
                               class="text-blue-600 hover:text-blue-900 transition duration-200">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($receta->estado == 'pendiente')
                            <a href="{{ route('admin.recetas.edit', $receta->id) }}"
                               class="text-yellow-600 hover:text-yellow-900 transition duration-200">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-8 text-gray-500">
                        <i class="fas fa-file-prescription text-4xl mb-4 text-gray-300"></i>
                        <p class="text-lg font-medium">No hay recetas veterinarias registradas</p>
                        <p class="text-sm text-gray-400 mt-1">Las nuevas recetas aparecerán aquí automáticamente</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    @if($recetas->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $recetas->links() }}
    </div>
    @endif
</div>
@endsection