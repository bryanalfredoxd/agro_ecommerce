@extends('layouts.admin')

@section('title', 'Editar Cliente - ' . $cliente->nombre)
@section('page_header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-green-600"><i class="fas fa-user-edit mr-2"></i>Editar Cliente</h1>
            <p class="text-gray-600 mt-1">{{ $cliente->nombre }} {{ $cliente->apellido }} - {{ $cliente->email }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.clientes.show', $cliente->id) }}" class="p-2 bg-gray-100 rounded-md hover:bg-gray-200 transition">
                <i class="fas fa-eye mr-1"></i> Ver
            </a>
            <a href="{{ route('admin.clientes.index') }}" class="p-2 bg-gray-100 rounded-md hover:bg-gray-200 transition">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-md">
        <div class="bg-gradient-to-r from-yellow-500 to-orange-600 text-white p-4 rounded-t-xl">
            <h6 class="text-lg font-bold mb-0"><i class="fas fa-user-edit mr-2"></i>Editar Información del Cliente</h6>
        </div>

        <div class="p-6">
            <form action="{{ route('admin.clientes.update', $cliente->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <h6 class="text-lg font-semibold text-gray-900 mb-4"><i class="fas fa-user mr-2"></i>Datos Personales</h6>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $cliente->nombre) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                            @error('nombre')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="apellido" class="block text-sm font-medium text-gray-700 mb-1">
                                Apellido <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="apellido" name="apellido" value="{{ old('apellido', $cliente->apellido) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                            @error('apellido')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                Correo Electrónico <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email', $cliente->email) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">
                                Teléfono
                            </label>
                            <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $cliente->telefono) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                            @error('telefono')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="documento_identidad" class="block text-sm font-medium text-gray-700 mb-1">
                                Documento de Identidad
                            </label>
                            <input type="text" id="documento_identidad" name="documento_identidad" value="{{ old('documento_identidad', $cliente->documento_identidad) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                   placeholder="V-12345678 o J-123456789">
                            @error('documento_identidad')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tipo_cliente" class="block text-sm font-medium text-gray-700 mb-1">
                                Tipo de Cliente <span class="text-red-500">*</span>
                            </label>
                            <select id="tipo_cliente" name="tipo_cliente" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                                <option value="">Seleccionar tipo...</option>
                                <option value="natural" {{ old('tipo_cliente', $cliente->tipo_cliente) == 'natural' ? 'selected' : '' }}>Persona Natural</option>
                                <option value="juridico" {{ old('tipo_cliente', $cliente->tipo_cliente) == 'juridico' ? 'selected' : '' }}>Persona Jurídica</option>
                                <option value="finca_productor" {{ old('tipo_cliente', $cliente->tipo_cliente) == 'finca_productor' ? 'selected' : '' }}>Finca/Productor</option>
                            </select>
                            @error('tipo_cliente')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <h6 class="text-lg font-semibold text-gray-900 mb-4"><i class="fas fa-toggle-on mr-2"></i>Estado del Cliente</h6>
                    <div class="flex items-center">
                        <input type="checkbox" id="activo" name="activo" value="1"
                               {{ old('activo', $cliente->activo) ? 'checked' : '' }}
                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="activo" class="ml-2 block text-sm text-gray-900">
                            Cliente activo (puede acceder al sistema)
                        </label>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Si desactivas al cliente, no podrá acceder al sistema ni realizar pedidos.
                    </p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <h6 class="text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-info-circle mr-2"></i>Información del Sistema</h6>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-600">Fecha de Registro:</span>
                            <span class="text-gray-900 ml-2">{{ $cliente->creado_at ? $cliente->creado_at->format('d/m/Y H:i') : 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600">Última Actualización:</span>
                            <span class="text-gray-900 ml-2">{{ $cliente->actualizado_at ? $cliente->actualizado_at->format('d/m/Y H:i') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.clientes.show', $cliente->id) }}"
                       class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-4 rounded-md transition duration-200">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                        <i class="fas fa-save mr-2"></i>Actualizar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection