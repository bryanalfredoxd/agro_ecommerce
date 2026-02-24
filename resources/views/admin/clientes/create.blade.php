@extends('layouts.admin')

@section('title', 'Crear Nuevo Cliente')
@section('page_header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-green-600"><i class="fas fa-user-plus mr-2"></i>Crear Cliente</h1>
            <p class="text-gray-600 mt-1">Registrar un nuevo cliente en el sistema</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.clientes.index') }}" class="p-2 bg-gray-100 rounded-md hover:bg-gray-200 transition">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-md">
        <div class="bg-gradient-to-r from-green-500 to-blue-600 text-white p-4 rounded-t-xl">
            <h6 class="text-lg font-bold mb-0"><i class="fas fa-user-plus mr-2"></i>Información del Nuevo Cliente</h6>
        </div>

        <div class="p-6">
            <form action="{{ route('admin.clientes.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <h6 class="text-lg font-semibold text-gray-900 mb-4"><i class="fas fa-user mr-2"></i>Datos Personales</h6>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            @error('nombre')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="apellido" class="block text-sm font-medium text-gray-700 mb-1">
                                Apellido <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="apellido" name="apellido" value="{{ old('apellido') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            @error('apellido')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                Correo Electrónico <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">
                                Teléfono
                            </label>
                            <input type="text" id="telefono" name="telefono" value="{{ old('telefono') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            @error('telefono')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="documento_identidad" class="block text-sm font-medium text-gray-700 mb-1">
                                Documento de Identidad
                            </label>
                            <input type="text" id="documento_identidad" name="documento_identidad" value="{{ old('documento_identidad') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
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
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">Seleccionar tipo...</option>
                                <option value="natural" {{ old('tipo_cliente') == 'natural' ? 'selected' : '' }}>Persona Natural</option>
                                <option value="juridico" {{ old('tipo_cliente') == 'juridico' ? 'selected' : '' }}>Persona Jurídica</option>
                                <option value="finca_productor" {{ old('tipo_cliente') == 'finca_productor' ? 'selected' : '' }}>Finca/Productor</option>
                            </select>
                            @error('tipo_cliente')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <h6 class="text-lg font-semibold text-gray-900 mb-4"><i class="fas fa-shield-alt mr-2"></i>Seguridad</h6>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                Contraseña <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password" name="password" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                Confirmar Contraseña <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        La contraseña debe tener al menos 8 caracteres.
                    </p>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.clientes.index') }}"
                       class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-4 rounded-md transition duration-200">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                        <i class="fas fa-save mr-2"></i>Crear Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection