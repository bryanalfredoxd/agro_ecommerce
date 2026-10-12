@extends('layouts.admin')

@section('title', 'Cotización ' . $cotizacion->numero_cotizacion . ' - Agrosuministros Rodríguez')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up pb-12">
            
            {{-- Mensajes Flash --}}
            @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-emerald-600 text-[26px]">check_circle</span>
                    <span class="text-sm font-bold">{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-rose-600 text-[26px]">error</span>
                    <span class="text-sm font-bold">{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            @endif

            <!-- ENCABEZADO Y ACCIONES -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-8">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.cotizaciones.index') }}" class="w-10 h-10 flex-shrink-0 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:text-[#1e549e] hover:border-blue-200 transition-colors shadow-sm">
                        <span class="material-symbols-outlined">arrow_back</span>
                    </a>
                    <div>
                        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                            <h2 class="text-2xl font-black text-slate-900 font-mono">{{ $cotizacion->numero_cotizacion }}</h2>
                            @php $badge = $cotizacion->estado_badge; @endphp
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black border {{ $badge['bg'] }}">
                                <span class="w-2 h-2 rounded-full {{ $badge['dot'] }}"></span>
                                {{ $badge['label'] }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Emitida el {{ \Carbon\Carbon::parse($cotizacion->fecha_emision)->format('d/m/Y h:i A') }} • Asesor: <strong>{{ $cotizacion->vendedor ? $cotizacion->vendedor->nombre : 'Central' }}</strong>
                        </p>
                    </div>
                </div>

                <!-- GRUPO DE BOTONES -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- BOTÓN IMPRIMIR A4 -->
                    <a href="{{ route('admin.cotizaciones.imprimir', $cotizacion->id) }}" target="_blank"
                       class="px-4 py-2.5 bg-[#0b1d38] hover:bg-[#162a4a] text-white text-xs font-black rounded-xl shadow-md transition-all flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px]">print</span>
                        Imprimir A4
                    </a>

                    <!-- BOTÓN DESCARGAR PDF -->
                    <a href="{{ route('admin.cotizaciones.pdf', $cotizacion->id) }}" 
                       class="px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-black rounded-xl shadow-md transition-all flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        Descargar PDF
                    </a>

                    <!-- BOTÓN ENVIAR POR CORREO -->
                    <button type="button" onclick="abrirModalCorreo()" 
                            class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black rounded-xl shadow-md transition-all flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px]">mail</span>
                        Enviar por Correo
                    </button>

                    <!-- CAMBIO DE ESTADO DROPDOWN/FORM -->
                    <form action="{{ route('admin.cotizaciones.estado', $cotizacion->id) }}" method="POST" class="inline-flex">
                        @csrf
                        <select name="estado" onchange="this.form.submit()" class="px-3 py-2 bg-white border border-gray-300 rounded-xl text-xs font-bold text-slate-700 outline-none cursor-pointer hover:border-gray-400">
                            <option value="pendiente" {{ $cotizacion->estado === 'pendiente' ? 'selected' : '' }}>Marcar Pendiente</option>
                            <option value="aprobada" {{ $cotizacion->estado === 'aprobada' ? 'selected' : '' }}>Marcar Aprobada</option>
                            <option value="rechazada" {{ $cotizacion->estado === 'rechazada' ? 'selected' : '' }}>Marcar Rechazada</option>
                        </select>
                    </form>

                    <!-- ELIMINAR -->
                    <form action="{{ route('admin.cotizaciones.destroy', $cotizacion->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta cotización?');" class="inline-flex">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-9 h-9 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors" title="Eliminar">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- CUERPO PRINCIPAL -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- DETALLE Y TABLA DE ÍTEMS (8 COLS) -->
                <div class="lg:col-span-8 space-y-6">
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
                            <h3 class="font-black text-slate-900 text-sm flex items-center gap-2">
                                <span class="material-symbols-outlined text-[#1e549e] text-[20px]">list_alt</span>
                                Productos y Servicios Cotizados ({{ $cotizacion->detalles->count() }})
                            </h3>
                            <span class="text-xs font-mono font-bold text-gray-500">
                                Moneda Base: USD
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="text-[11px] font-black uppercase text-gray-400 border-b border-gray-100 pb-2">
                                        <th class="pb-3 w-[8%] text-center">#</th>
                                        <th class="pb-3 w-[50%]">Descripción</th>
                                        <th class="pb-3 w-[14%] text-center">Cant.</th>
                                        <th class="pb-3 w-[14%] text-right">P. Unit ($)</th>
                                        <th class="pb-3 w-[14%] text-right">Total ($)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-xs">
                                    @foreach($cotizacion->detalles as $idx => $item)
                                    <tr>
                                        <td class="py-3 text-center text-gray-400 font-mono font-bold">{{ $idx + 1 }}</td>
                                        <td class="py-3">
                                            <div class="font-bold text-slate-800 text-xs">{{ $item->descripcion }}</div>
                                            @if($item->producto && $item->producto->categoria)
                                                <div class="text-[10px] text-gray-400 font-medium">{{ $item->producto->categoria->nombre }}</div>
                                            @endif
                                        </td>
                                        <td class="py-3 text-center font-bold font-mono text-slate-700">
                                            {{ (float)$item->cantidad }} <span class="text-[10px] text-gray-400">{{ $item->unidad_medida ?: 'ud' }}</span>
                                        </td>
                                        <td class="py-3 text-right font-mono text-slate-700">
                                            ${{ number_format($item->precio_unitario_usd, 2) }}
                                        </td>
                                        <td class="py-3 text-right font-black font-mono text-slate-900">
                                            ${{ number_format($item->total_usd, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- TOTALES -->
                        <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end">
                            <div class="w-full sm:w-72 space-y-2">
                                <div class="flex justify-between text-xs text-gray-600 font-medium">
                                    <span>Subtotal Neto:</span>
                                    <span class="font-bold text-slate-800 font-mono">${{ number_format($cotizacion->subtotal_usd, 2) }} USD</span>
                                </div>
                                @if($cotizacion->iva_usd > 0)
                                <div class="flex justify-between text-xs text-gray-600 font-medium">
                                    <span>IVA ({{ (float)$cotizacion->iva_porcentaje }}%):</span>
                                    <span class="font-bold text-slate-800 font-mono">${{ number_format($cotizacion->iva_usd, 2) }} USD</span>
                                </div>
                                @endif
                                <div class="flex justify-between text-base font-black text-slate-900 pt-2 border-t border-gray-200">
                                    <span>TOTAL USD:</span>
                                    <span class="font-mono text-[#0b1d38]">${{ number_format($cotizacion->total_usd, 2) }} USD</span>
                                </div>
                                <div class="flex justify-between text-xs font-bold text-[#1e549e] pt-1">
                                    <span>Equivalente BCV:</span>
                                    <span class="font-mono">Bs. {{ number_format($cotizacion->total_ves, 2) }}</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    @if($cotizacion->observaciones)
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                        <h4 class="font-black text-slate-900 text-xs uppercase tracking-wider mb-2 text-gray-400">Observaciones y Condiciones:</h4>
                        <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">{{ $cotizacion->observaciones }}</p>
                    </div>
                    @endif
                </div>

                <!-- PANEL LATERAL DE INFORMACIÓN (4 COLS) -->
                <div class="lg:col-span-4 space-y-6">
                    
                    <!-- DATOS CLIENTE -->
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                        <h3 class="font-black text-slate-900 text-sm flex items-center gap-2 pb-3 mb-4 border-b border-gray-100">
                            <span class="material-symbols-outlined text-[#1e549e] text-[20px]">badge</span>
                            Cliente Receptor
                        </h3>
                        <div class="space-y-3 text-xs">
                            <div>
                                <div class="text-[11px] font-bold text-gray-400 uppercase">Nombre / Razón Social</div>
                                <div class="text-sm font-extrabold text-slate-900 mt-0.5">{{ $cotizacion->cliente_nombre }}</div>
                            </div>
                            @if($cotizacion->cliente_documento)
                            <div>
                                <div class="text-[11px] font-bold text-gray-400 uppercase">Cédula o RIF</div>
                                <div class="font-mono font-bold text-slate-800 mt-0.5">{{ $cotizacion->cliente_documento }}</div>
                            </div>
                            @endif
                            @if($cotizacion->cliente_telefono)
                            <div>
                                <div class="text-[11px] font-bold text-gray-400 uppercase">Teléfono</div>
                                <div class="font-mono font-bold text-slate-800 mt-0.5">{{ $cotizacion->cliente_telefono }}</div>
                            </div>
                            @endif
                            @if($cotizacion->cliente_email)
                            <div>
                                <div class="text-[11px] font-bold text-gray-400 uppercase">Correo Electrónico</div>
                                <div class="text-slate-800 mt-0.5">{{ $cotizacion->cliente_email }}</div>
                            </div>
                            @endif
                            @if($cotizacion->cliente_direccion)
                            <div>
                                <div class="text-[11px] font-bold text-gray-400 uppercase">Dirección / Finca</div>
                                <div class="text-slate-700 mt-0.5 leading-relaxed">{{ $cotizacion->cliente_direccion }}</div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- VIGENCIA Y TASA -->
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                        <h3 class="font-black text-slate-900 text-sm flex items-center gap-2 pb-3 mb-4 border-b border-gray-100">
                            <span class="material-symbols-outlined text-[#1e549e] text-[20px]">calendar_today</span>
                            Vigencia y Tasa
                        </h3>
                        <div class="space-y-3 text-xs">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Fecha de Emisión:</span>
                                <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($cotizacion->fecha_emision)->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Validez Oferta:</span>
                                <span class="font-bold text-slate-800">{{ $cotizacion->validez_dias }} días continuos</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Fecha de Vencimiento:</span>
                                <span class="font-bold {{ $cotizacion->esta_vencida ? 'text-amber-600' : 'text-slate-800' }}">
                                    {{ $cotizacion->fecha_vencimiento ? \Carbon\Carbon::parse($cotizacion->fecha_vencimiento)->format('d/m/Y') : 'N/A' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                                <span class="text-gray-500">Tasa BCV Aplicada:</span>
                                <span class="font-black font-mono text-slate-900">Bs. {{ number_format($cotizacion->valor_tasa_bcv, 4) }}</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </main>
</div>

<!-- MODAL PARA ENVIAR POR CORREO -->
<div id="modalCorreo" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl animate-scale-up">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
            <h3 class="font-black text-slate-900 text-base flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-600">forward_to_inbox</span>
                Enviar Cotización por Correo
            </h3>
            <button onclick="cerrarModalCorreo()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="{{ route('admin.cotizaciones.enviar_correo', $cotizacion->id) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <p class="text-xs text-gray-500">
                    Se enviará un correo con el resumen de la oferta y el documento oficial en formato <strong>PDF adjunto</strong>.
                </p>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Correo Electrónico Destinatario</label>
                    <input type="email" name="email" value="{{ $cotizacion->cliente_email }}" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-gray-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none" 
                           placeholder="cliente@ejemplo.com">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="cerrarModalCorreo()" class="px-4 py-2 text-xs font-bold text-gray-600 hover:bg-gray-100 rounded-xl">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black rounded-xl shadow-md flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">send</span>
                        Enviar Ahora
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModalCorreo() {
        document.getElementById('modalCorreo').classList.remove('hidden');
    }
    function cerrarModalCorreo() {
        document.getElementById('modalCorreo').classList.add('hidden');
    }
</script>
@endsection
