@extends('layouts.admin')

@section('title', 'Panel de Control - Corpo Agrícola')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    {{-- SIDEBAR LATERAL --}}
    @include('admin.partials.sidebar')

    {{-- CONTENIDO PRINCIPAL --}}
    <main class="flex-1 min-w-0 flex flex-col">
        
        {{-- TOPBAR ADMIN --}}
        @include('admin.partials.topbar')

        {{-- Área de Contenido --}}
        <div class="p-4 sm:p-8 animate-fade-in-up pb-20">
            
            {{-- INDICADORES (KPIs) --}}
            @include('admin.partials.kpis')

            {{-- CUADRÍCULA DE MÓDULOS --}}
            @include('admin.partials.modulos')

        </div>
    </main>
</div>

@push('styles')
<style>
    /* Efecto para que los elementos aparezcan suavemente */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.4s ease-out forwards;
    }
    
    /* Scrollbar delgada y elegante para el Sidebar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.2);
    }
</style>
@endpush
@endsection