<div class="mb-8">
    <h2 class="text-lg font-black text-agro-dark flex items-center gap-2 mb-6">
        <span class="material-symbols-outlined text-primary">apps</span>
        Módulos Principales
    </h2>

    {{-- AQUÍ ESTÁ LA MAGIA: Agregamos "items-start" para que las tarjetas no se estiren al abrir una --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 lg:gap-6 items-start">
        
        {{-- MÓDULO 2: VENTAS Y PEDIDOS --}}
        <details class="bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 group open:ring-2 open:ring-blue-500/20">
            <summary class="p-6 cursor-pointer list-none [&::-webkit-details-marker]:hidden relative outline-none">
                <div class="absolute top-6 right-6 w-8 h-8 flex items-center justify-center bg-gray-50 text-gray-400 rounded-full group-open:bg-blue-50 group-open:text-blue-600 transition-colors">
                    <span class="material-symbols-outlined transition-transform duration-300 group-open:rotate-180">expand_more</span>
                </div>
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[28px]">shopping_cart</span>
                </div>
                <h3 class="text-lg font-black text-agro-dark mb-1 group-hover:text-blue-600 transition-colors">Ventas y Pedidos</h3>
                <p class="text-sm text-gray-500 pr-8 leading-relaxed mt-2">
                    Gestión integral del flujo de ventas. Supervisa y administra los pedidos web, valida pagos recibidos (Zelle, Pago Móvil, Transferencias), registra ventas manuales desde mostrador (POS) y controla el historial de devoluciones o despachos parciales.
                </p>
            </summary>
            
            <div class="px-6 pb-6 pt-2 border-t border-gray-50 animate-fade-in-up">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-4">
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-blue-50 text-gray-600 hover:text-blue-700 transition-colors border border-transparent hover:border-blue-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">shopping_bag</span> Listado de Pedidos
                    </a>
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-blue-50 text-gray-600 hover:text-blue-700 transition-colors border border-transparent hover:border-blue-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">fact_check</span> Gestión de Pagos
                    </a>
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-blue-50 text-gray-600 hover:text-blue-700 transition-colors border border-transparent hover:border-blue-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">point_of_sale</span> Venta Manual (POS)
                    </a>
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-blue-50 text-gray-600 hover:text-blue-700 transition-colors border border-transparent hover:border-blue-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">assignment_return</span> Devoluciones
                    </a>
                </div>
            </div>
        </details>

        {{-- MÓDULO 3: CATÁLOGO E INVENTARIO --}}
        <details class="bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 group open:ring-2 open:ring-green-500/20">
            <summary class="p-6 cursor-pointer list-none [&::-webkit-details-marker]:hidden relative outline-none">
                <div class="absolute top-6 right-6 w-8 h-8 flex items-center justify-center bg-gray-50 text-gray-400 rounded-full group-open:bg-green-50 group-open:text-green-600 transition-colors">
                    <span class="material-symbols-outlined transition-transform duration-300 group-open:rotate-180">expand_more</span>
                </div>
                <div class="w-12 h-12 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-green-600 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[28px]">category</span>
                </div>
                <h3 class="text-lg font-black text-agro-dark mb-1 group-hover:text-green-600 transition-colors">Catálogo e Inventario</h3>
                <p class="text-sm text-gray-500 pr-8 leading-relaxed mt-2">
                    Administración centralizada de productos y existencias. Crea productos simples o compuestos (combos), organiza tu catálogo por categorías jerárquicas y marcas, controla el stock físico dividiéndolo en lotes de proveedores y monitorea fechas de caducidad.
                </p>
            </summary>
            
            <div class="px-6 pb-6 pt-2 border-t border-gray-50 animate-fade-in-up">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-4">
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-green-50 text-gray-600 hover:text-green-700 transition-colors border border-transparent hover:border-green-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">inventory_2</span> Listado de Productos
                    </a>
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-green-50 text-gray-600 hover:text-green-700 transition-colors border border-transparent hover:border-green-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">shelves</span> Inventario por Lotes
                    </a>
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-green-50 text-gray-600 hover:text-green-700 transition-colors border border-transparent hover:border-green-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">category</span> Categorías
                    </a>
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-green-50 text-gray-600 hover:text-green-700 transition-colors border border-transparent hover:border-green-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">sell</span> Marcas
                    </a>
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-green-50 text-gray-600 hover:text-green-700 transition-colors border border-transparent hover:border-green-100 font-bold text-xs sm:col-span-2">
                        <span class="material-symbols-outlined text-[18px]">history</span> Histórico de Precios
                    </a>
                </div>
            </div>
        </details>

        {{-- MÓDULO 4: USUARIOS Y ROLES --}}
        <details class="bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 group open:ring-2 open:ring-indigo-500/20">
            <summary class="p-6 cursor-pointer list-none [&::-webkit-details-marker]:hidden relative outline-none">
                <div class="absolute top-6 right-6 w-8 h-8 flex items-center justify-center bg-gray-50 text-gray-400 rounded-full group-open:bg-indigo-50 group-open:text-indigo-600 transition-colors">
                    <span class="material-symbols-outlined transition-transform duration-300 group-open:rotate-180">expand_more</span>
                </div>
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[28px]">manage_accounts</span>
                </div>
                <h3 class="text-lg font-black text-agro-dark mb-1 group-hover:text-indigo-600 transition-colors">Usuarios y Roles</h3>
                <p class="text-sm text-gray-500 pr-8 leading-relaxed mt-2">
                    Control de accesos y perfiles. Administra la base de datos de clientes, asigna roles de trabajo al personal operativo interno (como Cajeros, Repartidores o Almacenistas) y define con precisión qué permisos de seguridad y apartados puede ver cada usuario del sistema.
                </p>
            </summary>
            
            <div class="px-6 pb-6 pt-2 border-t border-gray-50 animate-fade-in-up">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-4">
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-indigo-50 text-gray-600 hover:text-indigo-700 transition-colors border border-transparent hover:border-indigo-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">group</span> Listado de Usuarios
                    </a>
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-indigo-50 text-gray-600 hover:text-indigo-700 transition-colors border border-transparent hover:border-indigo-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">admin_panel_settings</span> Roles y Permisos
                    </a>
                </div>
            </div>
        </details>

        {{-- MÓDULO 5: LOGÍSTICA --}}
        <details class="bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 group open:ring-2 open:ring-orange-500/20">
            <summary class="p-6 cursor-pointer list-none [&::-webkit-details-marker]:hidden relative outline-none">
                <div class="absolute top-6 right-6 w-8 h-8 flex items-center justify-center bg-gray-50 text-gray-400 rounded-full group-open:bg-orange-50 group-open:text-orange-600 transition-colors">
                    <span class="material-symbols-outlined transition-transform duration-300 group-open:rotate-180">expand_more</span>
                </div>
                <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[28px]">local_shipping</span>
                </div>
                <h3 class="text-lg font-black text-agro-dark mb-1 group-hover:text-orange-600 transition-colors">Logística y Delivery</h3>
                <p class="text-sm text-gray-500 pr-8 leading-relaxed mt-2">
                    Coordinación de despachos y rutas. Configura y delimita las zonas de cobertura geográfica con sus respectivas tarifas de envío, asigna pedidos a tu flota de repartidores y realiza el seguimiento del estado de los despachos en tránsito.
                </p>
            </summary>
            
            <div class="px-6 pb-6 pt-2 border-t border-gray-50 animate-fade-in-up">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-4">
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-orange-50 text-gray-600 hover:text-orange-700 transition-colors border border-transparent hover:border-orange-100 font-bold text-xs sm:col-span-2">
                        <span class="material-symbols-outlined text-[18px]">route</span> Seguimiento Entregas
                    </a>
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-orange-50 text-gray-600 hover:text-orange-700 transition-colors border border-transparent hover:border-orange-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">map</span> Zonas de Delivery
                    </a>
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-orange-50 text-gray-600 hover:text-orange-700 transition-colors border border-transparent hover:border-orange-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">two_wheeler</span> Repartidores
                    </a>
                </div>
            </div>
        </details>

        {{-- MÓDULO 6: FINANZAS Y CAJA --}}
        <details class="bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 group open:ring-2 open:ring-teal-500/20">
            <summary class="p-6 cursor-pointer list-none [&::-webkit-details-marker]:hidden relative outline-none">
                <div class="absolute top-6 right-6 w-8 h-8 flex items-center justify-center bg-gray-50 text-gray-400 rounded-full group-open:bg-teal-50 group-open:text-teal-600 transition-colors">
                    <span class="material-symbols-outlined transition-transform duration-300 group-open:rotate-180">expand_more</span>
                </div>
                <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-teal-600 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[28px]">point_of_sale</span>
                </div>
                <h3 class="text-lg font-black text-agro-dark mb-1 group-hover:text-teal-600 transition-colors">Finanzas y Caja</h3>
                <p class="text-sm text-gray-500 pr-8 leading-relaxed mt-2">
                    Centro de operaciones financieras. Registra aperturas y cierres de las cajas físicas de tu tienda, emite facturación comercial (con control de correlativos e IVA), gestiona retiros de efectivo y mantén actualizado el histórico de la tasa de cambio vigente.
                </p>
            </summary>
            
            <div class="px-6 pb-6 pt-2 border-t border-gray-50 animate-fade-in-up">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-4">
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-teal-50 text-gray-600 hover:text-teal-700 transition-colors border border-transparent hover:border-teal-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">receipt</span> Listado de Facturas
                    </a>
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-teal-50 text-gray-600 hover:text-teal-700 transition-colors border border-transparent hover:border-teal-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">store</span> Caja Diaria / POS
                    </a>
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-teal-50 text-gray-600 hover:text-teal-700 transition-colors border border-transparent hover:border-teal-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">currency_exchange</span> Tasas de Cambio
                    </a>
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-teal-50 text-gray-600 hover:text-teal-700 transition-colors border border-transparent hover:border-teal-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">settings</span> Config. Facturación
                    </a>
                </div>
            </div>
        </details>

        {{-- MÓDULO 7: RECETAS VETERINARIAS --}}
        <details class="bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 group open:ring-2 open:ring-purple-500/20">
            <summary class="p-6 cursor-pointer list-none [&::-webkit-details-marker]:hidden relative outline-none">
                <div class="absolute top-6 right-6 w-8 h-8 flex items-center justify-center bg-gray-50 text-gray-400 rounded-full group-open:bg-purple-50 group-open:text-purple-600 transition-colors">
                    <span class="material-symbols-outlined transition-transform duration-300 group-open:rotate-180">expand_more</span>
                </div>
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[28px]">stethoscope</span>
                </div>
                <h3 class="text-lg font-black text-agro-dark mb-1 group-hover:text-purple-600 transition-colors">Récipes Veterinarios</h3>
                <p class="text-sm text-gray-500 pr-8 leading-relaxed mt-2">
                    Módulo especializado para el cumplimiento de normativas en productos controlados. Revisa las recetas médicas subidas por los clientes, valida diagnósticos o indicaciones, y aprueba o rechaza récipes antes de autorizar el despacho y venta de medicamentos restringidos.
                </p>
            </summary>
            
            <div class="px-6 pb-6 pt-2 border-t border-gray-50 animate-fade-in-up">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-4">
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-purple-50 text-gray-600 hover:text-purple-700 transition-colors border border-transparent hover:border-purple-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">medical_services</span> Listado de Recetas
                    </a>
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-purple-50 text-gray-600 hover:text-purple-700 transition-colors border border-transparent hover:border-purple-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">fact_check</span> Revisión Pendientes
                    </a>
                </div>
            </div>
        </details>

        {{-- MÓDULO 8: SISTEMA Y AJUSTES --}}
        <details class="bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 group open:ring-2 open:ring-gray-500/20 md:col-span-2 xl:col-span-3">
            <summary class="p-6 cursor-pointer list-none [&::-webkit-details-marker]:hidden relative outline-none">
                <div class="absolute top-6 right-6 w-8 h-8 flex items-center justify-center bg-gray-50 text-gray-400 rounded-full group-open:bg-gray-100 group-open:text-gray-800 transition-colors">
                    <span class="material-symbols-outlined transition-transform duration-300 group-open:rotate-180">expand_more</span>
                </div>
                <div class="w-12 h-12 bg-gray-100 text-gray-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-gray-800 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[28px]">settings_suggest</span>
                </div>
                <h3 class="text-lg font-black text-agro-dark mb-1 group-hover:text-gray-800 transition-colors">Sistema y Ajustes</h3>
                <p class="text-sm text-gray-500 pr-8 leading-relaxed mt-2">
                    Configuraciones globales de la plataforma. Modifica datos operativos de la empresa (abierto/cerrado), gestiona cuentas bancarias donde el cliente transfiere, configura llaves de integración con APIs externas y consulta el registro de auditoría (logs) de las acciones críticas.
                </p>
            </summary>
            
            <div class="px-6 pb-6 pt-2 border-t border-gray-50 animate-fade-in-up">
                <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 mt-4">
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-600 hover:text-gray-900 transition-colors border border-transparent hover:border-gray-200 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">storefront</span> Configuración Tienda
                    </a>
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-600 hover:text-gray-900 transition-colors border border-transparent hover:border-gray-200 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">account_balance</span> Cuentas Bancarias
                    </a>
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-600 hover:text-gray-900 transition-colors border border-transparent hover:border-gray-200 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">api</span> APIs Externas
                    </a>
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-600 hover:text-gray-900 transition-colors border border-transparent hover:border-gray-200 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">schedule</span> Horarios Físicos
                    </a>
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-600 hover:text-gray-900 transition-colors border border-transparent hover:border-gray-200 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">plagiarism</span> Logs de Auditoría
                    </a>
                </div>
            </div>
        </details>

    </div>
</div>