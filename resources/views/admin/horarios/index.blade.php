@extends('layouts.admin')

@section('title', 'Horarios de Atención - Corpo Agrícola')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        @include('admin.partials.topbar')

        {{-- Le quitamos el pb-24 que dejaba espacio abajo --}}
        <div class="p-4 sm:p-8 animate-fade-in-up">
            
            {{-- Encabezado --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-2xl font-black text-agro-dark flex items-center gap-2">
                        <span class="material-symbols-outlined text-gray-500 text-[32px]">schedule</span>
                        Horario de Atención Físico
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Configura los días y horas en los que la tienda física y el delivery operan.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
                
                {{-- Columna Izquierda: Tarjeta Informativa --}}
                <div class="xl:col-span-1 space-y-6">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8 relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-gray-50 rounded-full z-0"></div>
                        <div class="relative z-10">
                            <h3 class="text-lg font-black text-agro-dark mb-4 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">info</span> Importante
                            </h3>
                            <p class="text-sm text-gray-500 leading-relaxed mb-4">
                                Estos horarios se mostrarán a los clientes en la tienda virtual. Si marcas un día como <strong class="text-red-500">Cerrado</strong>, el sistema indicará que no hay atención ni despachos ese día.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Columna Derecha: Formulario de los 7 días --}}
                <div class="xl:col-span-2 bg-white rounded-3xl shadow-sm border border-gray-100 p-0 overflow-hidden">
                    
                    {{-- Encabezados de la tabla visual --}}
                    <div class="hidden sm:grid grid-cols-12 gap-4 px-8 py-4 border-b border-gray-100 bg-gray-50/80">
                        <div class="col-span-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Día</div>
                        <div class="col-span-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Estado</div>
                        <div class="col-span-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Apertura</div>
                        <div class="col-span-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Cierre</div>
                    </div>

                    <form id="horarioForm" onsubmit="saveHorarios(event)">
                        <div class="divide-y divide-gray-50">
                            
                            @php
                                $hoy = date('N'); // Día actual (1-7)
                            @endphp

                            @foreach($horarios as $horario)
                            <div class="p-5 sm:px-8 sm:py-5 flex flex-col sm:grid sm:grid-cols-12 gap-4 sm:items-center hover:bg-gray-50/50 transition-colors {{ $horario->dia_semana == $hoy ? 'bg-primary/5 border-l-4 border-l-primary' : '' }}">
                                
                                {{-- 1. Nombre del Día --}}
                                <div class="col-span-3 flex items-center justify-between sm:justify-start gap-2">
                                    <span class="font-black text-agro-dark text-base sm:text-sm capitalize">{{ $horario->nombre_dia }}</span>
                                    @if($horario->dia_semana == $hoy)
                                        <span class="bg-primary text-white text-[9px] font-black px-2 py-0.5 rounded uppercase tracking-wider">Hoy</span>
                                    @endif
                                </div>

                                {{-- 2. Toggle Switch Laborable --}}
                                <div class="col-span-3 flex items-center sm:justify-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="horarios[{{ $horario->dia_semana }}][es_laborable]" class="sr-only peer dia-toggle" data-dia="{{ $horario->dia_semana }}" {{ $horario->es_laborable ? 'checked' : '' }} value="1">
                                        <div class="w-11 h-6 bg-red-100 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                                        <span class="ml-3 text-xs font-bold uppercase transition-colors toggle-label {{ $horario->es_laborable ? 'text-green-600' : 'text-red-500' }}">
                                            {{ $horario->es_laborable ? 'Abierto' : 'Cerrado' }}
                                        </span>
                                    </label>
                                </div>

                                {{-- 3. Hora de Apertura --}}
                                <div class="col-span-3 flex items-center sm:justify-center gap-2">
                                    <span class="sm:hidden text-xs font-bold text-gray-400 w-16">Apertura:</span>
                                    <input type="time" name="horarios[{{ $horario->dia_semana }}][hora_apertura]" id="apertura_{{ $horario->dia_semana }}" 
                                           value="{{ $horario->hora_apertura ? \Carbon\Carbon::parse($horario->hora_apertura)->format('H:i') : '08:00' }}" 
                                           {{ !$horario->es_laborable ? 'disabled' : '' }}
                                           class="h-10 px-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-primary focus:bg-white text-sm font-bold text-agro-dark outline-none transition-all w-full max-w-[140px] disabled:opacity-50 disabled:cursor-not-allowed">
                                </div>

                                {{-- 4. Hora de Cierre --}}
                                <div class="col-span-3 flex items-center sm:justify-center gap-2">
                                    <span class="sm:hidden text-xs font-bold text-gray-400 w-16">Cierre:</span>
                                    <input type="time" name="horarios[{{ $horario->dia_semana }}][hora_cierre]" id="cierre_{{ $horario->dia_semana }}" 
                                           value="{{ $horario->hora_cierre ? \Carbon\Carbon::parse($horario->hora_cierre)->format('H:i') : '17:00' }}" 
                                           {{ !$horario->es_laborable ? 'disabled' : '' }}
                                           class="h-10 px-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-primary focus:bg-white text-sm font-bold text-agro-dark outline-none transition-all w-full max-w-[140px] disabled:opacity-50 disabled:cursor-not-allowed">
                                </div>
                            </div>
                            @endforeach

                        </div>

                        {{-- Footer del Formulario (Posición Natural abajo de los días, sin solapar) --}}
                        <div class="p-6 sm:px-8 sm:py-6 border-t border-gray-100 bg-gray-50/80 flex flex-col sm:flex-row justify-end gap-3 rounded-b-3xl">
                            <button type="button" onclick="window.location.reload()" class="w-full sm:w-auto px-6 py-3 rounded-xl font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 transition-colors text-center">Descartar Cambios</button>
                            <button type="submit" id="btnSave" class="w-full sm:w-auto px-8 py-3 rounded-xl font-black text-white bg-agro-dark hover:bg-black shadow-md hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">save</span> Guardar Calendario
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </main>
</div>

{{-- CONTENEDOR TOAST --}}
<div id="toast-container" class="fixed bottom-6 right-6 z-[999] flex flex-col gap-2"></div>

@push('scripts')
<script>
    // Lógica visual para activar/desactivar inputs
    document.querySelectorAll('.dia-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const dia = this.getAttribute('data-dia');
            const apertura = document.getElementById('apertura_' + dia);
            const cierre = document.getElementById('cierre_' + dia);
            const label = this.parentElement.querySelector('.toggle-label');

            if (this.checked) {
                apertura.disabled = false;
                cierre.disabled = false;
                label.innerText = 'Abierto';
                label.classList.replace('text-red-500', 'text-green-600');
            } else {
                apertura.disabled = true;
                cierre.disabled = true;
                label.innerText = 'Cerrado';
                label.classList.replace('text-green-600', 'text-red-500');
            }
        });
    });

    // Función global Toast
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-600' : 'bg-red-600';
        const icon = type === 'success' ? 'check_circle' : 'error';

        toast.className = `flex items-center gap-3 px-5 py-4 rounded-xl shadow-2xl text-white transform translate-y-10 opacity-0 transition-all duration-300 ${bgColor}`;
        toast.innerHTML = `<span class="material-symbols-outlined">${icon}</span><p class="font-bold text-sm">${message}</p>`;

        container.appendChild(toast);
        setTimeout(() => toast.classList.remove('translate-y-10', 'opacity-0'), 10);
        setTimeout(() => {
            toast.classList.add('translate-y-10', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Petición AJAX Guardar
    function saveHorarios(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSave');
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[18px]">autorenew</span> Guardando...';
        btn.disabled = true;

        const formData = new FormData(e.target);
        
        fetch(`{{ route('admin.horarios.updateAll') }}`, {
            method: 'POST',
            body: formData,
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Error en el servidor');
            return data;
        })
        .then(data => {
            if(data.success) {
                showToast(data.message, 'success');
            }
        })
        .catch(err => {
            showToast(err.message, 'error');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }
</script>
@endpush
@endsection