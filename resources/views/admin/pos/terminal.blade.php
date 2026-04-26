@extends('layouts.admin')
@section('title', 'Terminal POS - Corpo Agrícola')

@section('content')
<div class="bg-gray-100 flex h-screen font-sans overflow-hidden">
    
    {{-- BARRA LATERAL --}}
    <div class="w-16 bg-agro-dark hidden md:flex flex-col items-center py-4 border-r border-gray-800 z-50">
        <a href="{{ route('admin.dashboard') }}" class="w-10 h-10 rounded-xl bg-white/10 text-white flex items-center justify-center hover:bg-primary transition-colors mb-6" title="Volver al Dashboard">
            <span class="material-symbols-outlined">dashboard</span>
        </a>
        <button onclick="abrirModalCierre()" class="w-10 h-10 rounded-xl mt-auto bg-red-500/20 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors" title="Cerrar Turno">
            <span class="material-symbols-outlined">lock</span>
        </button>
    </div>

    <main class="flex-1 flex flex-col md:flex-row min-w-0 relative">
        
        {{-- COLUMNA IZQUIERDA: CARRITO --}}
        <section class="w-full md:w-[380px] xl:w-[420px] bg-white flex flex-col border-r border-gray-200 shadow-xl z-10 shrink-0">
            
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div>
                    <h2 class="font-black text-agro-dark flex items-center gap-1.5 text-lg">
                        <span class="material-symbols-outlined text-primary">receipt_long</span> Ticket
                    </h2>
                    <p class="text-[10px] text-gray-700 font-bold uppercase mt-0.5">Tasa BCV: Bs. <span id="tasa_bcv">{{ number_format($valorTasa, 2) }}</span></p>
                </div>
                <button onclick="vaciarCarrito()" class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition-colors" title="Vaciar Ticket">
                    <span class="material-symbols-outlined text-[20px]">delete_sweep</span>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar p-2 bg-gray-50/30" id="cart-container">
                <div id="empty-cart-msg" class="flex flex-col items-center justify-center h-full text-gray-300">
                    <span class="material-symbols-outlined text-5xl mb-2">shopping_cart</span>
                    <p class="text-sm font-bold">El ticket está vacío</p>
                </div>
            </div>

            {{-- TOTALES Y BOTÓN COBRAR (CON IVA) --}}
            <div class="p-5 border-t border-gray-200 bg-white shadow-[0_-10px_30px_rgba(0,0,0,0.03)]">
                <div class="flex justify-between items-center mb-1 text-sm">
                    <span class="text-gray-500 font-bold">Subtotal</span>
                    <span class="font-bold text-gray-600" id="subtotal_usd_ui">$0.00</span>
                </div>
                <div class="flex justify-between items-center mb-3 text-sm">
                    <span class="text-gray-500 font-bold">IVA ({{ number_format($porcentajeIva, 0) }}%)</span>
                    <span class="font-bold text-gray-600" id="iva_usd_ui">$0.00</span>
                </div>
                <div class="flex justify-between items-end border-t border-dashed border-gray-300 pt-3 mb-5">
                    <span class="text-xl font-black text-agro-dark uppercase tracking-widest">Total</span>
                    <div class="text-right">
                        <p class="text-3xl font-black text-primary leading-none" id="total_usd_ui">$0.00</p>
                        <p class="text-xs font-bold text-gray-500 mt-1" id="total_ves_ui">Bs. 0.00</p>
                    </div>
                </div>
                <button onclick="abrirModalCobro()" id="btnCobrar" disabled class="w-full h-14 rounded-2xl font-black text-white bg-primary hover:bg-green-600 disabled:bg-gray-300 disabled:cursor-not-allowed shadow-xl shadow-primary/30 transition-all flex items-center justify-center gap-2 text-lg active:scale-95">
                    <span class="material-symbols-outlined">payments</span> COBRAR (F9)
                </button>
            </div>
        </section>

        {{-- COLUMNA DERECHA: BÚSQUEDA --}}
        <section class="flex-1 flex flex-col min-w-0 bg-gray-50 relative">
            <div class="p-4 bg-white border-b border-gray-200 shadow-sm z-10 flex gap-2">
                <div class="relative flex-1">
                    <span class="material-symbols-outlined absolute left-4 top-3.5 text-gray-700 text-[28px]">barcode_scanner</span>
                    <input type="text" id="pos-search" autofocus autocomplete="off" placeholder="Escanea código o busca por nombre..." class="w-full h-14 pl-14 pr-4 rounded-2xl bg-gray-100 border border-transparent focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/20 transition-all text-lg font-bold text-agro-dark outline-none placeholder-gray-400">
                </div>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar p-6">
                <div id="search-loader" class="hidden text-center mt-10">
                    <span class="material-symbols-outlined animate-spin text-primary text-4xl">autorenew</span>
                </div>
                <div id="products-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    {{-- Las tarjetas hermosas se inyectan aquí --}}
                </div>
            </div>
        </section>

    </main>
</div>

{{-- ========================================== --}}
{{-- MODAL DE COBRO (CHECKOUT)                  --}}
{{-- ========================================== --}}
<div id="checkoutModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity opacity-0" id="checkoutBackdrop"></div>
    
    <div class="fixed inset-0 z-10 flex justify-center items-center p-4">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-lg opacity-0 scale-95 flex flex-col" id="checkoutPanel">
            
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-agro-dark text-white">
                <h3 class="text-xl font-black flex items-center gap-2"><span class="material-symbols-outlined text-primary">point_of_sale</span> Procesar Pago</h3>
                <button type="button" onclick="cerrarModalCobro()" class="text-gray-700 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="p-6">
                <div class="flex justify-between bg-gray-50 p-4 rounded-2xl border border-gray-200 mb-6">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Total a Cobrar</p>
                        <p class="text-3xl font-black text-agro-dark leading-none mt-1" id="checkout_total_usd">$0.00</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Equivalente</p>
                        <p class="text-xl font-black text-gray-700 mt-1" id="checkout_total_ves">Bs. 0.00</p>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-2 ml-1">Método de Pago</label>
                    <select id="metodo_pago" onchange="verificarMetodo()" class="w-full h-12 px-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-primary focus:bg-white outline-none font-bold text-agro-dark text-sm">
                        <option value="efectivo_usd">Efectivo USD</option>
                        <option value="efectivo_bs">Efectivo Bs</option>
                        <option value="pago_movil">Pago Móvil</option>
                        <option value="zelle">Zelle</option>
                        <option value="punto_venta">Punto de Venta</option>
                        <option value="binance">Binance</option>
                        <option value="transferencia">Transferencia Bancaria</option>
                    </select>
                </div>

                {{-- Campo Dinámico de Referencia (Solo para pagos digitales) --}}
                <div id="caja_referencia" class="hidden">
                    <label class="block text-[10px] font-black text-blue-500 uppercase tracking-widest mb-2 ml-1">Referencia Bancaria / Recibo</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3.5 font-bold text-gray-700 text-lg material-symbols-outlined">receipt</span>
                        <input type="text" id="referencia_pago" class="w-full h-14 pl-12 pr-4 rounded-xl bg-blue-50/50 border border-blue-200 focus:border-blue-500 focus:bg-white text-base font-bold text-agro-dark outline-none uppercase" placeholder="Ej: 12345678">
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex gap-3">
                <button type="button" onclick="procesarTransaccion()" id="btnConfirmarPago" class="w-full h-12 rounded-xl font-black text-white bg-agro-dark hover:bg-black shadow-lg transition-all flex items-center justify-center gap-2 text-lg">
                    Confirmar Transacción
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{-- MODAL CERRAR CAJA                          --}}
{{-- ========================================== --}}
<div id="cierreModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity opacity-0" id="cierreBackdrop"></div>
    <div class="fixed inset-0 z-10 flex justify-center items-center p-4">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-sm opacity-0 scale-95 flex flex-col" id="cierrePanel">
            
            <div class="bg-red-600 p-6 text-center">
                <span class="material-symbols-outlined text-white text-5xl mb-2">lock_person</span>
                <h3 class="text-xl font-black text-white">Cerrar Turno</h3>
            </div>

            <div class="p-6">
                <p class="text-sm text-gray-500 mb-4 text-center">Cuenta los billetes físicos en tu gaveta y escribe el monto total para que el sistema calcule el cuadre de caja.</p>
                
                <div>
                    <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-2 ml-1">Efectivo Físico Contado (USD)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3.5 font-bold text-gray-700 text-lg">$</span>
                        <input type="number" step="0.01" id="dinero_fisico_usd" class="w-full h-14 pl-10 pr-4 rounded-xl bg-gray-50 border border-gray-200 focus:border-red-500 focus:bg-white text-xl font-black text-agro-dark outline-none" placeholder="0.00">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-2 ml-1">Observaciones (Opcional)</label>
                    <textarea id="observaciones_cierre" rows="2" class="w-full p-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-red-500 outline-none text-sm"></textarea>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                <button type="button" onclick="cerrarModalCierre()" class="px-5 py-2.5 rounded-xl font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 text-sm">Cancelar</button>
                <button type="button" onclick="ejecutarCierreCaja()" id="btnCerrarCaja" class="px-6 py-2.5 rounded-xl font-black text-white bg-red-600 hover:bg-red-700 shadow-lg text-sm transition-all">
                    Finalizar Turno
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@push('scripts')
<script>
    // VARIABLES GLOBALES
    let carrito = [];
    const tasaBcv = parseFloat({{ $valorTasa }});
    const ivaPorcentaje = parseFloat({{ $porcentajeIva }});
    let searchTimeout;

    const searchInput = document.getElementById('pos-search');
    const grid = document.getElementById('products-grid');
    const cartContainer = document.getElementById('cart-container');
    const emptyMsg = document.getElementById('empty-cart-msg');
    const btnCobrar = document.getElementById('btnCobrar');

    document.addEventListener('click', (e) => {
        if(e.target.tagName !== 'INPUT' && e.target.tagName !== 'BUTTON' && e.target.tagName !== 'SELECT' && e.target.tagName !== 'TEXTAREA') {
            searchInput.focus();
        }
    });

    // 1. BUSCADOR INTELIGENTE
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        let query = this.value.trim();
        if (query.length === 0) { grid.innerHTML = ''; return; }
        searchTimeout = setTimeout(() => realizarBusqueda(query, false), 300);
    });

    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(searchTimeout);
            realizarBusqueda(this.value.trim(), true);
        }
    });

    function realizarBusqueda(query, isExact) {
        document.getElementById('search-loader').classList.remove('hidden');
        grid.innerHTML = '';

        fetch(`{{ url('/admin/pos/buscar-producto') }}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ buscar: query, exact: isExact })
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('search-loader').classList.add('hidden');
            
            if (data.exact && data.producto) {
                agregarAlCarrito(data.producto);
                searchInput.value = '';
                return;
            }

            if (data.productos && data.productos.length > 0) {
                data.productos.forEach(prod => {
                    let precio = prod.precio_oferta_usd ? prod.precio_oferta_usd : prod.precio_venta_usd;
                    
                    // DISEÑO DE TARJETA MEJORADO (Con Imagen y Categoría)
                    let imgUrl = prod.imagen_url ? `/${prod.imagen_url}` : '';
                    let imgHtml = prod.imagen_url 
                        ? `<img src="${imgUrl}" class="w-full h-full object-cover mix-blend-multiply">` 
                        : `<span class="material-symbols-outlined text-4xl text-gray-300">image</span>`;
                    
                    let catNombre = prod.categoria ? prod.categoria.nombre : 'Sin Categoría';

                    grid.innerHTML += `
                        <div onclick='agregarAlCarrito(${JSON.stringify(prod).replace(/'/g, "&apos;")})' class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md hover:border-primary cursor-pointer transition-all active:scale-95 flex flex-col h-full overflow-hidden group">
                            
                            <div class="h-28 bg-gray-50 flex items-center justify-center p-2 border-b border-gray-100 group-hover:bg-primary/5 transition-colors">
                                ${imgHtml}
                            </div>
                            
                            <div class="p-3 flex flex-col flex-1">
                                <p class="text-[9px] font-black text-gray-700 uppercase tracking-widest mb-1 truncate">${catNombre}</p>
                                <h4 class="font-bold text-agro-dark text-xs leading-snug mb-2 line-clamp-2 flex-1">${prod.nombre}</h4>
                                
                                <div class="flex justify-between items-end mt-1 pt-2 border-t border-gray-100">
                                    <span class="text-[10px] font-black text-green-600 bg-green-50 px-1.5 py-0.5 rounded">${parseFloat(prod.stock_total)} Disp.</span>
                                    <span class="font-black text-primary text-sm">$${parseFloat(precio).toFixed(2)}</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                grid.innerHTML = `<div class="col-span-full text-center text-gray-700 py-10 font-bold">No se encontraron productos.</div>`;
            }
        });
    }

    // 2. MOTOR DEL CARRITO (CON VALIDACIÓN DE MÍNIMOS)
    function agregarAlCarrito(producto) {
        let index = carrito.findIndex(p => p.id === producto.id);
        let paso = parseFloat(producto.paso_venta) || 1;
        let precio = parseFloat(producto.precio_oferta_usd ? producto.precio_oferta_usd : producto.precio_venta_usd);
        let min = parseFloat(producto.venta_minima) || 1;

        if (index > -1) {
            if (carrito[index].cantidad + paso > parseFloat(producto.stock_total)) {
                Swal.fire({icon: 'error', title: 'Sin Stock', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false});
                return;
            }
            carrito[index].cantidad += paso;
        } else {
            if (min > parseFloat(producto.stock_total)) {
                Swal.fire({icon: 'error', title: 'Stock insuficiente', text: 'El stock es menor a la venta mínima permitida.', toast: true, position: 'top-end', timer: 3000, showConfirmButton: false});
                return;
            }
            carrito.push({
                id: producto.id, nombre: producto.nombre, precio: precio, cantidad: min,
                unidad: producto.unidad_medida, paso: paso, venta_minima: min, stock_max: parseFloat(producto.stock_total)
            });
        }
        renderizarCarrito();
    }

    function modificarCantidad(index, accion) {
        let item = carrito[index];
        if (accion === 'suma') {
            if (item.cantidad + item.paso > item.stock_max) return;
            item.cantidad += item.paso;
        } else {
            // REGLA DE NEGOCIO: Si al restar el paso, queda por debajo de la venta mínima, se elimina del carrito.
            if (item.cantidad - item.paso < item.venta_minima) {
                carrito.splice(index, 1);
            } else {
                item.cantidad -= item.paso;
            }
        }
        renderizarCarrito();
    }

    function eliminarItem(index) {
        carrito.splice(index, 1);
        renderizarCarrito();
    }

    function vaciarCarrito() {
        carrito = [];
        renderizarCarrito();
        searchInput.focus();
    }

    function renderizarCarrito() {
        cartContainer.innerHTML = '';
        let subtotalUsd = 0;

        if (carrito.length === 0) {
            cartContainer.appendChild(emptyMsg);
            emptyMsg.classList.remove('hidden');
            btnCobrar.disabled = true;
        } else {
            emptyMsg.classList.add('hidden');
            btnCobrar.disabled = false;

            carrito.forEach((item, index) => {
                let subItem = item.precio * item.cantidad;
                subtotalUsd += subItem;

                let html = `
                    <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm mb-2 relative group animate-fade-in-up">
                        <div class="flex justify-between items-start gap-2 mb-2">
                            <h4 class="font-bold text-agro-dark text-xs leading-tight line-clamp-2 pr-6">${item.nombre}</h4>
                            <button onclick="eliminarItem(${index})" class="text-gray-300 hover:text-red-500 absolute top-3 right-3 transition-colors"><span class="material-symbols-outlined text-[16px]">close</span></button>
                        </div>
                        <div class="flex justify-between items-end">
                            <div class="flex items-center gap-1 bg-gray-50 rounded-lg p-1 border border-gray-200">
                                <button onclick="modificarCantidad(${index}, 'resta')" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow-sm text-gray-600 hover:text-primary"><span class="material-symbols-outlined text-[16px]">remove</span></button>
                                <span class="font-black text-sm min-w-[28px] text-center">${item.cantidad}</span>
                                <button onclick="modificarCantidad(${index}, 'suma')" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow-sm text-gray-600 hover:text-primary"><span class="material-symbols-outlined text-[16px]">add</span></button>
                            </div>
                            <div class="text-right">
                                <p class="font-black text-primary text-sm">$${subItem.toFixed(2)}</p>
                            </div>
                        </div>
                    </div>
                `;
                cartContainer.insertAdjacentHTML('beforeend', html);
            });
        }

        // CÁLCULO DE IVA Y TOTALES
        let ivaUsd = subtotalUsd * (ivaPorcentaje / 100);
        let totalUsd = subtotalUsd + ivaUsd;
        let totalVes = totalUsd * tasaBcv;

        // Mostrar en UI
        document.getElementById('subtotal_usd_ui').innerText = `$${subtotalUsd.toFixed(2)}`;
        document.getElementById('iva_usd_ui').innerText = `$${ivaUsd.toFixed(2)}`;
        document.getElementById('total_usd_ui').innerText = `$${totalUsd.toFixed(2)}`;
        document.getElementById('total_ves_ui').innerText = `Bs. ${totalVes.toFixed(2)}`;
        
        // Guardar para el modal de cobro
        document.getElementById('checkout_total_usd').innerText = `$${totalUsd.toFixed(2)}`;
        document.getElementById('checkout_total_ves').innerText = `Bs. ${totalVes.toFixed(2)}`;
    }

    // 3. CHECKOUT Y PAGO (CON REFERENCIAS)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F9') { e.preventDefault(); if (carrito.length > 0) abrirModalCobro(); }
    });

    function verificarMetodo() {
        const metodo = document.getElementById('metodo_pago').value;
        const metodosDigitales = ['pago_movil', 'zelle', 'binance', 'transferencia', 'punto_venta'];
        const cajaRef = document.getElementById('caja_referencia');

        if (metodosDigitales.includes(metodo)) {
            cajaRef.classList.remove('hidden');
            document.getElementById('referencia_pago').focus();
        } else {
            cajaRef.classList.add('hidden');
            document.getElementById('referencia_pago').value = '';
        }
    }

    function abrirModalCobro() {
        document.getElementById('metodo_pago').value = 'efectivo_usd';
        verificarMetodo();

        const modal = document.getElementById('checkoutModal');
        const backdrop = document.getElementById('checkoutBackdrop');
        const panel = document.getElementById('checkoutPanel');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    function cerrarModalCobro() {
        const modal = document.getElementById('checkoutModal');
        const backdrop = document.getElementById('checkoutBackdrop');
        const panel = document.getElementById('checkoutPanel');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
        searchInput.focus();
    }

    function procesarTransaccion() {
        const metodo = document.getElementById('metodo_pago').value;
        const referencia = document.getElementById('referencia_pago').value;
        const metodosDigitales = ['pago_movil', 'zelle', 'binance', 'transferencia', 'punto_venta'];

        // Validar si requiere referencia y no la puso
        if (metodosDigitales.includes(metodo) && referencia.trim() === '') {
            Swal.fire('Falta Referencia', 'Por favor ingresa el número de referencia del pago o recibo.', 'warning');
            return;
        }

        const btn = document.getElementById('btnConfirmarPago');
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin">autorenew</span> Procesando...';

        fetch(`{{ url('/admin/pos/procesar-venta') }}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                metodo_pago: metodo,
                referencia_pago: referencia,
                carrito: carrito
            })
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Error al procesar');
            return data;
        })
        .then(data => {
            cerrarModalCobro();
            vaciarCarrito();
            Swal.fire({ title: '¡Venta Exitosa!', text: 'El inventario fue descontado.', icon: 'success', timer: 2000, showConfirmButton: false });
        })
        .catch(err => {
            Swal.fire('Error', err.message, 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = 'Confirmar Transacción';
        });
    }

    // 4. CIERRE DE CAJA
    function abrirModalCierre() {
        const modal = document.getElementById('cierreModal');
        const backdrop = document.getElementById('cierreBackdrop');
        const panel = document.getElementById('cierrePanel');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    function cerrarModalCierre() {
        const modal = document.getElementById('cierreModal');
        const backdrop = document.getElementById('cierreBackdrop');
        const panel = document.getElementById('cierrePanel');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    function ejecutarCierreCaja() {
        const dineroFisico = document.getElementById('dinero_fisico_usd').value;
        const obs = document.getElementById('observaciones_cierre').value;

        if(dineroFisico.trim() === '') {
            Swal.fire('Error', 'Debes ingresar el monto que contaste en caja.', 'warning');
            return;
        }

        const btn = document.getElementById('btnCerrarCaja');
        btn.disabled = true;
        btn.innerHTML = 'Cerrando...';

        fetch(`{{ url('/admin/pos/cerrar-caja') }}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ dinero_real_usd: dineroFisico, observaciones: obs })
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Error al cerrar caja');
            return data;
        })
        .then(data => {
            Swal.fire({ title: 'Turno Finalizado', icon: 'success', timer: 2000, showConfirmButton: false }).then(() => {
                window.location.reload(); // Recarga y lo saca de la pantalla del POS
            });
        })
        .catch(err => {
            Swal.fire('Error', err.message, 'error');
            btn.disabled = false;
            btn.innerHTML = 'Finalizar Turno';
        });
    }
</script>
@endpush
@endsection