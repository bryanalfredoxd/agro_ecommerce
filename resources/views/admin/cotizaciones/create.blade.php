@extends('layouts.admin')

@section('title', 'Crear Nueva Cotización - Agrosuministros Rodríguez')

@section('content')
<div class="bg-gray-50 flex min-h-screen font-sans">

    @include('admin.partials.sidebar')

    <main class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto">
        @include('admin.partials.topbar')

        <div class="p-4 sm:p-8 animate-fade-in-up pb-12">
            
            {{-- ENCABEZADO --}}
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.cotizaciones.index') }}" class="w-10 h-10 flex-shrink-0 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:text-[#1e549e] hover:border-blue-200 transition-colors shadow-sm">
                        <span class="material-symbols-outlined">arrow_back</span>
                    </a>
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-2xl font-black text-slate-900">Nueva Cotización de Productos</h2>
                            <span class="px-3 py-1 bg-blue-100 text-[#1e549e] rounded-full text-xs font-mono font-black">
                                {{ $siguienteNumero }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mt-0.5">Selecciona o busca los insumos para calcular el presupuesto en Dólares y Bolívares.</p>
                    </div>
                </div>

                {{-- TASA BCV BADGE --}}
                <div class="hidden sm:flex items-center gap-3 bg-white px-4 py-2 rounded-2xl border border-gray-200 shadow-sm">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <div class="text-right">
                        <div class="text-[10px] uppercase font-bold text-gray-400">Tasa Oficial BCV</div>
                        <div class="text-sm font-black text-slate-800 font-mono">Bs. {{ number_format($valorTasaBcv, 4) }} / USD</div>
                    </div>
                </div>
            </div>

            @if(isset($errors) && $errors->any())
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <span class="material-symbols-outlined text-rose-600">error</span>
                    Por favor revisa los siguientes errores:
                </div>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('admin.cotizaciones.store') }}" method="POST" id="formCotizacion">
                @csrf
                <input type="hidden" name="cliente_id" id="cliente_id" value="{{ old('cliente_id') }}">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                    <!-- COLUMNA 1: DATOS DEL CLIENTE Y PARÁMETROS (4 COLS) -->
                    <div class="lg:col-span-4 space-y-6">
                        
                        <!-- TARJETA CLIENTE -->
                        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                            <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
                                <h3 class="font-black text-slate-900 text-sm flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[#1e549e] text-[20px]">person</span>
                                    Datos del Cliente
                                </h3>
                                <span class="text-[11px] font-bold text-gray-400">Requerido</span>
                            </div>

                            <!-- AUTOCOMPLETAR CLIENTE -->
                            <div class="relative mb-4">
                                <label class="block text-xs font-bold text-gray-600 mb-1">Buscar Cliente Registrado</label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-gray-400 text-[18px]">search</span>
                                    <input type="text" id="inputBuscarCliente" 
                                           class="w-full pl-9 pr-8 py-2 bg-slate-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500/20 focus:border-[#1e549e] outline-none" 
                                           placeholder="Buscar por cédula, nombre o correo..." autocomplete="off">
                                    <button type="button" id="btnLimpiarCliente" onclick="limpiarCliente()" class="absolute right-2.5 top-2.5 text-gray-400 hover:text-gray-600 hidden">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                </div>
                                <div id="listaResultadosCliente" class="absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-xl z-30 max-h-48 overflow-y-auto hidden"></div>
                            </div>

                            <div class="space-y-3.5">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Nombre / Razón Social <span class="text-rose-500">*</span></label>
                                    <input type="text" name="cliente_nombre" id="cliente_nombre" value="{{ old('cliente_nombre') }}" required
                                           class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:border-[#1e549e] outline-none" 
                                           placeholder="Ej. Finca La Pradera C.A. / Juan Pérez">
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 mb-1">Cédula / RIF</label>
                                        <input type="text" name="cliente_documento" id="cliente_documento" value="{{ old('cliente_documento') }}"
                                               class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs font-mono text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:border-[#1e549e] outline-none" 
                                               placeholder="V-12345678 / J-00000000-0">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 mb-1">Teléfono</label>
                                        <input type="text" name="cliente_telefono" id="cliente_telefono" value="{{ old('cliente_telefono') }}"
                                               class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:border-[#1e549e] outline-none" 
                                               placeholder="0412-1234567">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Correo Electrónico (Para envío PDF)</label>
                                    <input type="email" name="cliente_email" id="cliente_email" value="{{ old('cliente_email') }}"
                                           class="w-full px-3.5 py-2 bg-white border border-gray-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:border-[#1e549e] outline-none" 
                                           placeholder="cliente@ejemplo.com">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Dirección / Finca (Opcional)</label>
                                    <textarea name="cliente_direccion" id="cliente_direccion" rows="2"
                                              class="w-full px-3.5 py-2 bg-white border border-gray-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:border-[#1e549e] outline-none resize-none" 
                                              placeholder="Ubicación de entrega o sector...">{{ old('cliente_direccion') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- TARJETA CONDICIONES -->
                        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-4">
                            <h3 class="font-black text-slate-900 text-sm flex items-center gap-2 pb-2 border-b border-gray-100">
                                <span class="material-symbols-outlined text-[#1e549e] text-[20px]">tune</span>
                                Parámetros de Oferta
                            </h3>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Días de Validez del Presupuesto</label>
                                <select name="validez_dias" id="validez_dias" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:border-[#1e549e] outline-none">
                                    <option value="3" {{ old('validez_dias') == 3 ? 'selected' : '' }}>3 Días continuos</option>
                                    <option value="5" {{ old('validez_dias') == 5 ? 'selected' : '' }}>5 Días continuos</option>
                                    <option value="7" {{ old('validez_dias', 7) == 7 ? 'selected' : '' }}>7 Días continuos (Estándar)</option>
                                    <option value="15" {{ old('validez_dias') == 15 ? 'selected' : '' }}>15 Días continuos</option>
                                    <option value="30" {{ old('validez_dias') == 30 ? 'selected' : '' }}>30 Días continuos</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Observaciones / Condiciones Especiales</label>
                                <textarea name="observaciones" rows="3"
                                          class="w-full px-3.5 py-2 bg-white border border-gray-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:border-[#1e549e] outline-none resize-none" 
                                          placeholder="Términos de despacho, flete, condiciones de garantía...">{{ old('observaciones') }}</textarea>
                            </div>

                            <!-- ENVÍO AUTOMÁTICO POR CORREO -->
                            <div class="p-3 bg-blue-50/70 border border-blue-100 rounded-2xl flex items-start gap-3">
                                <input type="checkbox" name="enviar_correo" id="enviar_correo" value="1" class="mt-0.5 rounded text-[#1e549e] focus:ring-blue-500">
                                <label for="enviar_correo" class="text-xs text-slate-700 select-none cursor-pointer">
                                    <strong class="font-bold text-slate-900 block">Enviar PDF por correo inmediatamente</strong>
                                    Si marcas esta casilla y llenas el email del cliente, el PDF se enviará automáticamente al guardar.
                                </label>
                            </div>
                        </div>

                    </div>

                    <!-- COLUMNA 2: BUSCADOR DE PRODUCTOS Y TABLA DE ÍTEMS (8 COLS) -->
                    <div class="lg:col-span-8 space-y-6">
                        
                        <!-- BUSCADOR RÁPIDO DE PRODUCTOS -->
                        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                            <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
                                <h3 class="font-black text-slate-900 text-sm flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[#1e549e] text-[20px]">inventory_2</span>
                                    Agregar Productos al Presupuesto
                                </h3>
                                <button type="button" onclick="agregarFilaManual()" class="text-xs font-bold text-[#1e549e] hover:underline flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">add</span>
                                    Agregar Ítem Personalizado
                                </button>
                            </div>

                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-3 text-gray-400">search</span>
                                <input type="text" id="inputBuscarProducto" 
                                       class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-gray-200 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-[#1e549e] outline-none font-medium text-slate-800" 
                                       placeholder="Buscar insumos por nombre, SKU o código de barras..." autocomplete="off">
                                <div id="listaResultadosProducto" class="absolute left-0 right-0 top-full mt-2 bg-white border border-gray-200 rounded-2xl shadow-2xl z-30 max-h-72 overflow-y-auto hidden divide-y divide-gray-100"></div>
                            </div>
                        </div>

                        <!-- TABLA DINÁMICA DE ÍTEMS -->
                        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                            <h3 class="font-black text-slate-900 text-sm mb-4">
                                Ítems Cotizados (<span id="totalItemsCount">0</span>)
                            </h3>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left" id="tablaItems">
                                    <thead>
                                        <tr class="text-[11px] font-black uppercase text-gray-400 border-b border-gray-100 pb-2">
                                            <th class="pb-2 w-[45%]">Descripción del Producto</th>
                                            <th class="pb-2 w-[18%] text-center">Cantidad</th>
                                            <th class="pb-2 w-[18%] text-right">Precio Unit ($)</th>
                                            <th class="pb-2 w-[14%] text-right">Total ($)</th>
                                            <th class="pb-2 w-[5%] text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsBody" class="divide-y divide-gray-100 text-xs">
                                        {{-- Filas insertadas dinámicamente con JS --}}
                                    </tbody>
                                </table>
                            </div>

                            <div id="emptyStateItems" class="py-12 text-center text-gray-400">
                                <span class="material-symbols-outlined text-4xl text-gray-300 mb-1">add_shopping_cart</span>
                                <p class="font-bold text-slate-600 text-xs">Aún no has agregado productos a esta cotización</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">Usa el buscador superior para seleccionar insumos o haz clic en "Agregar Ítem Personalizado".</p>
                            </div>

                            <!-- RESUMEN FINANCIERO Y LIQUIDACIÓN -->
                            <div class="mt-8 pt-6 border-t border-gray-100">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                                    
                                    <!-- OPCIONES DE IMPUESTO -->
                                    <div class="space-y-3 bg-slate-50/80 p-4 rounded-2xl border border-gray-200/60">
                                        <div class="flex items-center justify-between">
                                            <label class="text-xs font-bold text-slate-700 flex items-center gap-2 cursor-pointer select-none">
                                                <input type="checkbox" name="aplica_iva" id="checkAplicaIva" value="1" onchange="recalcularTotales()" class="rounded text-[#1e549e] focus:ring-blue-500">
                                                <span>Aplicar Impuesto IVA ({{ (float)$ivaPorcentajeDefecto }}%)</span>
                                            </label>
                                            <div class="flex items-center gap-1.5" id="contenedorPorcentajeIva" style="display: none;">
                                                <span class="text-xs font-black text-[#1e549e] bg-blue-50 px-2.5 py-1 rounded-md border border-blue-200 shadow-2xs">
                                                    {{ (float)$ivaPorcentajeDefecto }}% IVA Global
                                                </span>
                                            </div>
                                        </div>
                                        <p class="text-[10px] text-gray-500">
                                            Por defecto las cotizaciones operan en monto neto. Activa la casilla si requieres desglosar el {{ (float)$ivaPorcentajeDefecto }}% de IVA fiscal configurado en el sistema.
                                        </p>
                                    </div>

                                    <!-- TOTALES LIQUIDADOS -->
                                    <div class="space-y-2 text-right">
                                        <div class="flex justify-between text-xs text-gray-600 font-medium">
                                            <span>Subtotal Neto:</span>
                                            <span class="font-bold text-slate-800 font-mono" id="subtotalDisplay">$0.00 USD</span>
                                        </div>
                                        <div class="flex justify-between text-xs text-gray-600 font-medium" id="filaIvaDisplay" style="display: none;">
                                            <span>IVA (<span id="ivaPorcLabel">16</span>%):</span>
                                            <span class="font-bold text-slate-800 font-mono" id="ivaDisplay">$0.00 USD</span>
                                        </div>
                                        <div class="flex justify-between text-base font-black text-slate-900 pt-2 border-t border-gray-200">
                                            <span>TOTAL COTIZADO:</span>
                                            <span class="font-mono text-[#0b1d38]" id="totalUsdDisplay">$0.00 USD</span>
                                        </div>
                                        <div class="flex justify-between text-xs font-bold text-[#1e549e] pt-1">
                                            <span>Equivalente BCV:</span>
                                            <span class="font-mono" id="totalVesDisplay">Bs. 0.00</span>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <!-- BOTONES DE ACCIÓN INFERIOR -->
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <a href="{{ route('admin.cotizaciones.index') }}" class="px-5 py-3 rounded-xl border border-gray-300 text-slate-700 font-bold text-xs hover:bg-gray-100 transition-colors">
                                Cancelar
                            </a>
                            <button type="submit" id="btnGuardar" class="px-7 py-3 bg-[#0b1d38] hover:bg-[#162a4a] text-white font-extrabold text-sm rounded-xl shadow-xl shadow-[#0b1d38]/20 transition-all hover:scale-[1.02] active:scale-95 flex items-center gap-2">
                                <span class="material-symbols-outlined text-[20px]">save</span>
                                Generar Cotización
                            </button>
                        </div>

                    </div>

                </div>
            </form>

        </div>
    </main>
</div>

{{-- JAVASCRIPT REACTIVO DE COTIZACIONES --}}
<script>
    const TASA_BCV = {{ $valorTasaBcv }};
    let filaIndex = 0;

    // --- 1. BÚSQUEDA Y AUTOCOMPLETADO DE CLIENTE ---
    const inputBuscarCliente = document.getElementById('inputBuscarCliente');
    const listaResultadosCliente = document.getElementById('listaResultadosCliente');
    let timeoutCliente = null;

    inputBuscarCliente.addEventListener('input', function() {
        clearTimeout(timeoutCliente);
        const query = this.value.trim();
        if (query.length < 2) {
            listaResultadosCliente.classList.add('hidden');
            return;
        }

        timeoutCliente = setTimeout(() => {
            fetch(`{{ route('admin.cotizaciones.buscar-cliente') }}?buscar=${encodeURIComponent(query)}`)
                .then(r => r.json())
                .then(data => {
                    listaResultadosCliente.innerHTML = '';
                    if (data.clientes && data.clientes.length > 0) {
                        data.clientes.forEach(c => {
                            const div = document.createElement('div');
                            div.className = 'p-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0 transition-colors';
                            div.innerHTML = `
                                <div class="font-bold text-xs text-slate-800">${c.nombre}</div>
                                <div class="text-[11px] text-gray-500 flex gap-2 font-mono">
                                    <span>${c.documento || 'S/D'}</span>
                                    <span>•</span>
                                    <span>${c.email || 'Sin correo'}</span>
                                </div>
                            `;
                            div.onclick = () => seleccionarCliente(c);
                            listaResultadosCliente.appendChild(div);
                        });
                        listaResultadosCliente.classList.remove('hidden');
                    } else {
                        listaResultadosCliente.innerHTML = '<div class="p-3 text-xs text-gray-400 text-center">No se encontraron clientes registrados</div>';
                        listaResultadosCliente.classList.remove('hidden');
                    }
                });
        }, 300);
    });

    function seleccionarCliente(c) {
        document.getElementById('cliente_id').value = c.id;
        document.getElementById('cliente_nombre').value = c.nombre;
        document.getElementById('cliente_documento').value = c.documento;
        document.getElementById('cliente_telefono').value = c.telefono;
        document.getElementById('cliente_email').value = c.email;
        inputBuscarCliente.value = c.nombre;
        document.getElementById('btnLimpiarCliente').classList.remove('hidden');
        listaResultadosCliente.classList.add('hidden');
    }

    function limpiarCliente() {
        document.getElementById('cliente_id').value = '';
        document.getElementById('cliente_nombre').value = '';
        document.getElementById('cliente_documento').value = '';
        document.getElementById('cliente_telefono').value = '';
        document.getElementById('cliente_email').value = '';
        inputBuscarCliente.value = '';
        document.getElementById('btnLimpiarCliente').classList.add('hidden');
    }

    // --- 2. BÚSQUEDA Y AGREGADO DE PRODUCTOS ---
    const inputBuscarProducto = document.getElementById('inputBuscarProducto');
    const listaResultadosProducto = document.getElementById('listaResultadosProducto');
    let timeoutProducto = null;

    inputBuscarProducto.addEventListener('input', function() {
        clearTimeout(timeoutProducto);
        const query = this.value.trim();
        if (query.length < 1) {
            listaResultadosProducto.classList.add('hidden');
            return;
        }

        timeoutProducto = setTimeout(() => {
            fetch(`{{ route('admin.cotizaciones.buscar-producto') }}?buscar=${encodeURIComponent(query)}`)
                .then(r => r.json())
                .then(data => {
                    listaResultadosProducto.innerHTML = '';
                    if (data.productos && data.productos.length > 0) {
                        data.productos.forEach(p => {
                            const div = document.createElement('div');
                            div.className = 'p-3 hover:bg-blue-50 cursor-pointer flex items-center justify-between transition-colors';
                            const precio = parseFloat(p.precio_venta_usd || 0).toFixed(2);
                            div.innerHTML = `
                                <div>
                                    <div class="font-extrabold text-xs text-slate-800">${p.nombre}</div>
                                    <div class="text-[11px] text-gray-500 font-mono mt-0.5">
                                        SKU: ${p.sku || 'N/A'} • Stock: ${p.stock_total || 0} ${p.unidad_medida || 'ud'}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-black text-xs text-[#0b1d38] font-mono">$${precio}</div>
                                    <div class="text-[10px] text-gray-400">Bs. ${(precio * TASA_BCV).toFixed(2)}</div>
                                </div>
                            `;
                            div.onclick = () => {
                                agregarFilaProducto(p);
                                inputBuscarProducto.value = '';
                                listaResultadosProducto.classList.add('hidden');
                            };
                            listaResultadosProducto.appendChild(div);
                        });
                        listaResultadosProducto.classList.remove('hidden');
                    } else {
                        listaResultadosProducto.innerHTML = '<div class="p-3 text-xs text-gray-400 text-center">No se encontraron productos coincidentes</div>';
                        listaResultadosProducto.classList.remove('hidden');
                    }
                });
        }, 250);
    });

    // Cerrar sugerencias al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#inputBuscarCliente') && !e.target.closest('#listaResultadosCliente')) {
            listaResultadosCliente.classList.add('hidden');
        }
        if (!e.target.closest('#inputBuscarProducto') && !e.target.closest('#listaResultadosProducto')) {
            listaResultadosProducto.classList.add('hidden');
        }
    });

    // --- 3. GESTIÓN DE FILAS EN LA TABLA ---
    function agregarFilaProducto(p) {
        const id = filaIndex++;
        const precio = parseFloat(p.precio_venta_usd || 0).toFixed(2);
        const unidad = p.unidad_medida || 'ud';

        const tr = document.createElement('tr');
        tr.id = `fila-${id}`;
        tr.className = 'hover:bg-slate-50 transition-colors';
        tr.innerHTML = `
            <td class="py-3 pr-2">
                <input type="hidden" name="items[${id}][producto_id]" value="${p.id}">
                <input type="hidden" name="items[${id}][unidad_medida]" value="${unidad}">
                <div class="font-bold text-slate-800 text-xs">${p.nombre}</div>
                <input type="text" name="items[${id}][descripcion]" value="${p.nombre}" 
                       class="mt-1 w-full px-2 py-1 bg-slate-50 border border-gray-200 rounded text-[11px] text-gray-600 outline-none">
            </td>
            <td class="py-3 px-2 text-center">
                <div class="flex items-center justify-center gap-1">
                    <input type="number" name="items[${id}][cantidad]" value="1" min="0.01" step="any" oninput="actualizarLinea(${id})"
                           class="w-16 px-2 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-bold text-center outline-none font-mono focus:border-[#1e549e]">
                    <span class="text-[10px] text-gray-400 font-bold">${unidad}</span>
                </div>
            </td>
            <td class="py-3 px-2 text-right">
                <input type="number" name="items[${id}][precio_unitario_usd]" value="${precio}" min="0" step="0.01" oninput="actualizarLinea(${id})"
                       class="w-20 px-2 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-bold text-right outline-none font-mono focus:border-[#1e549e]">
            </td>
            <td class="py-3 pl-2 text-right font-black font-mono text-slate-800" id="totalLinea-${id}">
                $${precio}
            </td>
            <td class="py-3 pl-2 text-center">
                <button type="button" onclick="eliminarFila(${id})" class="text-gray-300 hover:text-rose-600 transition-colors p-1">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                </button>
            </td>
        `;

        document.getElementById('itemsBody').appendChild(tr);
        recalcularTotales();
    }

    function agregarFilaManual() {
        const id = filaIndex++;
        const tr = document.createElement('tr');
        tr.id = `fila-${id}`;
        tr.className = 'hover:bg-slate-50 transition-colors';
        tr.innerHTML = `
            <td class="py-3 pr-2">
                <input type="hidden" name="items[${id}][producto_id]" value="">
                <input type="text" name="items[${id}][descripcion]" placeholder="Nombre del producto, servicio o flete..." required
                       class="w-full px-2.5 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-bold text-slate-800 outline-none focus:border-[#1e549e]">
            </td>
            <td class="py-3 px-2 text-center">
                <div class="flex items-center justify-center gap-1">
                    <input type="number" name="items[${id}][cantidad]" value="1" min="0.01" step="any" oninput="actualizarLinea(${id})"
                           class="w-16 px-2 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-bold text-center outline-none font-mono focus:border-[#1e549e]">
                    <select name="items[${id}][unidad_medida]" class="text-[10px] bg-slate-50 border border-gray-200 rounded py-1 font-bold text-gray-500">
                        <option value="ud">ud</option>
                        <option value="saco">saco</option>
                        <option value="kg">kg</option>
                        <option value="litro">litro</option>
                        <option value="flete">flete</option>
                    </select>
                </div>
            </td>
            <td class="py-3 px-2 text-right">
                <input type="number" name="items[${id}][precio_unitario_usd]" value="0.00" min="0" step="0.01" oninput="actualizarLinea(${id})"
                       class="w-20 px-2 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-bold text-right outline-none font-mono focus:border-[#1e549e]">
            </td>
            <td class="py-3 pl-2 text-right font-black font-mono text-slate-800" id="totalLinea-${id}">
                $0.00
            </td>
            <td class="py-3 pl-2 text-center">
                <button type="button" onclick="eliminarFila(${id})" class="text-gray-300 hover:text-rose-600 transition-colors p-1">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                </button>
            </td>
        `;

        document.getElementById('itemsBody').appendChild(tr);
        recalcularTotales();
    }

    function eliminarFila(id) {
        const el = document.getElementById(`fila-${id}`);
        if (el) el.remove();
        recalcularTotales();
    }

    function actualizarLinea(id) {
        const fila = document.getElementById(`fila-${id}`);
        if (!fila) return;

        const cant = parseFloat(fila.querySelector(`input[name="items[${id}][cantidad]"]`).value) || 0;
        const precio = parseFloat(fila.querySelector(`input[name="items[${id}][precio_unitario_usd]"]`).value) || 0;
        const total = (cant * precio).toFixed(2);

        document.getElementById(`totalLinea-${id}`).innerText = `$${total}`;
        recalcularTotales();
    }

    // --- 4. RECALCULAR TOTALES FINANCIEROS ---
    function recalcularTotales() {
        const rows = document.querySelectorAll('#itemsBody tr');
        let subtotal = 0;

        rows.forEach(r => {
            const cantInput = r.querySelector('input[name*="[cantidad]"]');
            const precioInput = r.querySelector('input[name*="[precio_unitario_usd]"]');
            if (cantInput && precioInput) {
                const c = parseFloat(cantInput.value) || 0;
                const p = parseFloat(precioInput.value) || 0;
                subtotal += (c * p);
            }
        });

        const checkAplicaIva = document.getElementById('checkAplicaIva').checked;
        const contIva = document.getElementById('contenedorPorcentajeIva');
        const filaIva = document.getElementById('filaIvaDisplay');
        const ivaPorc = {{ (float)$ivaPorcentajeDefecto }};

        let iva = 0;
        if (checkAplicaIva) {
            contIva.style.display = 'flex';
            filaIva.style.display = 'flex';
            iva = subtotal * (ivaPorc / 100);
            document.getElementById('ivaPorcLabel').innerText = ivaPorc;
        } else {
            contIva.style.display = 'none';
            filaIva.style.display = 'none';
        }

        const totalUsd = subtotal + iva;
        const totalVes = totalUsd * TASA_BCV;

        document.getElementById('subtotalDisplay').innerText = `$${subtotal.toFixed(2)} USD`;
        document.getElementById('ivaDisplay').innerText = `$${iva.toFixed(2)} USD`;
        document.getElementById('totalUsdDisplay').innerText = `$${totalUsd.toFixed(2)} USD`;
        document.getElementById('totalVesDisplay').innerText = `Bs. ${totalVes.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

        document.getElementById('totalItemsCount').innerText = rows.length;
        document.getElementById('emptyStateItems').style.display = rows.length === 0 ? 'block' : 'none';
    }

    // Validación antes de enviar
    document.getElementById('formCotizacion').addEventListener('submit', function(e) {
        const rows = document.querySelectorAll('#itemsBody tr');
        if (rows.length === 0) {
            e.preventDefault();
            alert('Debes agregar al menos un producto o ítem a la cotización.');
            return false;
        }
    });
</script>
@endsection
