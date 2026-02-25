<div class="mb-8">
    <h2 class="text-lg font-black text-agro-dark flex items-center gap-2 mb-6">
        <span class="material-symbols-outlined text-primary">apps</span>
        Módulos Principales
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 lg:gap-6 items-start">
        
        {{-- MÓDULO 2: VENTAS Y PEDIDOS --}}
        @if(Auth::user()->tienePermiso('ver_pedidos') || Auth::user()->tienePermiso('gestionar_pagos') || Auth::user()->tienePermiso('crear_venta_manual') || Auth::user()->tienePermiso('procesar_devoluciones'))
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
                    Gestión integral del flujo de ventas. Supervisa y administra los pedidos web, valida pagos recibidos (Zelle, Pago Móvil), registra ventas manuales desde mostrador (POS) y controla devoluciones.
                </p>
            </summary>
            
            <div class="px-6 pb-6 pt-2 border-t border-gray-50 animate-fade-in-up">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-4">
                    @if(Auth::user()->tienePermiso('ver_pedidos'))
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-blue-50 text-gray-600 hover:text-blue-700 transition-colors border border-transparent hover:border-blue-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">shopping_bag</span> Listado de Pedidos
                    </a>
                    @endif
                    @if(Auth::user()->tienePermiso('gestionar_pagos'))
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-blue-50 text-gray-600 hover:text-blue-700 transition-colors border border-transparent hover:border-blue-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">fact_check</span> Gestión de Pagos
                    </a>
                    @endif
                    @if(Auth::user()->tienePermiso('crear_venta_manual'))
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-blue-50 text-gray-600 hover:text-blue-700 transition-colors border border-transparent hover:border-blue-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">point_of_sale</span> Venta Manual (POS)
                    </a>
                    @endif
                    @if(Auth::user()->tienePermiso('procesar_devoluciones'))
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-blue-50 text-gray-600 hover:text-blue-700 transition-colors border border-transparent hover:border-blue-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">assignment_return</span> Devoluciones
                    </a>
                    @endif
                </div>
            </div>
        </details>
        @endif

        {{-- MÓDULO 3: CATÁLOGO E INVENTARIO --}}
        @if(Auth::user()->tienePermiso('ver_productos') || Auth::user()->tienePermiso('gestionar_inventario_lotes') || Auth::user()->tienePermiso('gestionar_categorias_marcas') || Auth::user()->tienePermiso('ver_historico_precios'))
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
                    Administración centralizada de productos y existencias. Crea productos simples o compuestos, organiza categorías, controla lotes de proveedores y monitorea caducidades.
                </p>
            </summary>
            
            <div class="px-6 pb-6 pt-2 border-t border-gray-50 animate-fade-in-up">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-4">
                    @if(Auth::user()->tienePermiso('ver_productos'))
                    <a href="{{ route('admin.productos.index') }}" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-green-50 text-gray-600 hover:text-green-700 transition-colors border border-transparent hover:border-green-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">inventory_2</span> Listado de Productos
                    </a>
                    @endif
                    @if(Auth::user()->tienePermiso('gestionar_inventario_lotes'))
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-green-50 text-gray-600 hover:text-green-700 transition-colors border border-transparent hover:border-green-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">shelves</span> Inventario por Lotes
                    </a>
                    @endif
                    @if(Auth::user()->tienePermiso('gestionar_categorias_marcas'))
                    <a href="{{ route('admin.categorias.index') }}" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-green-50 text-gray-600 hover:text-green-700 transition-colors border border-transparent hover:border-green-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">category</span> Categorías
                    </a>
                    <a href="{{ route('admin.marcas.index') }}" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-green-50 text-gray-600 hover:text-green-700 transition-colors border border-transparent hover:border-green-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">sell</span> Marcas
                    </a>
                    @endif
                    @if(Auth::user()->tienePermiso('ver_historico_precios'))
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-green-50 text-gray-600 hover:text-green-700 transition-colors border border-transparent hover:border-green-100 font-bold text-xs sm:col-span-2">
                        <span class="material-symbols-outlined text-[18px]">history</span> Histórico de Precios
                    </a>
                    @endif
                </div>
            </div>
        </details>
        @endif

        {{-- MÓDULO 4: USUARIOS Y ROLES --}}
        @if(Auth::user()->tienePermiso('ver_usuarios') || Auth::user()->tienePermiso('gestionar_roles_permisos'))
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
                    Control de accesos y perfiles. Administra clientes, asigna roles operativos y define permisos de seguridad granulares.
                </p>
            </summary>
            
            <div class="px-6 pb-6 pt-2 border-t border-gray-50 animate-fade-in-up">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-4">
                    @if(Auth::user()->tienePermiso('ver_usuarios'))
                    <a href="{{ route('admin.usuarios.index') }}" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-indigo-50 text-gray-600 hover:text-indigo-700 transition-colors border border-transparent hover:border-indigo-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">group</span> Listado de Usuarios
                    </a>
                    @endif
                    @if(Auth::user()->tienePermiso('gestionar_roles_permisos'))
                    <a href="{{ route('admin.roles.index') }}" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-indigo-50 text-gray-600 hover:text-indigo-700 transition-colors border border-transparent hover:border-indigo-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">admin_panel_settings</span> Roles y Permisos
                    </a>
                    @endif
                </div>
            </div>
        </details>
        @endif

        {{-- MÓDULO 5: LOGÍSTICA --}}
        @if(Auth::user()->tienePermiso('ver_seguimiento_rutas') || Auth::user()->tienePermiso('gestionar_zonas_delivery') || Auth::user()->tienePermiso('gestionar_repartidores'))
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
                    Coordinación de despachos y rutas. Configura zonas de cobertura, tarifas, flota de repartidores y rastreo en vivo.
                </p>
            </summary>
            
            <div class="px-6 pb-6 pt-2 border-t border-gray-50 animate-fade-in-up">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-4">
                    @if(Auth::user()->tienePermiso('ver_seguimiento_rutas'))
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-orange-50 text-gray-600 hover:text-orange-700 transition-colors border border-transparent hover:border-orange-100 font-bold text-xs sm:col-span-2">
                        <span class="material-symbols-outlined text-[18px]">route</span> Seguimiento Entregas
                    </a>
                    @endif
                    @if(Auth::user()->tienePermiso('gestionar_zonas_delivery'))
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-orange-50 text-gray-600 hover:text-orange-700 transition-colors border border-transparent hover:border-orange-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">map</span> Zonas de Delivery
                    </a>
                    @endif
                    @if(Auth::user()->tienePermiso('gestionar_repartidores'))
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-orange-50 text-gray-600 hover:text-orange-700 transition-colors border border-transparent hover:border-orange-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">two_wheeler</span> Repartidores
                    </a>
                    @endif
                </div>
            </div>
        </details>
        @endif

        {{-- MÓDULO 6: FINANZAS Y CAJA --}}
        @if(Auth::user()->tienePermiso('ver_facturas') || Auth::user()->tienePermiso('gestionar_caja_diaria') || Auth::user()->tienePermiso('gestionar_tasas_cambio') || Auth::user()->tienePermiso('configurar_facturacion'))
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
                    Centro de operaciones financieras. Registra aperturas/cierres, emite facturación fiscal y mantén la tasa BCV.
                </p>
            </summary>
            
            <div class="px-6 pb-6 pt-2 border-t border-gray-50 animate-fade-in-up">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-4">
                    @if(Auth::user()->tienePermiso('ver_facturas'))
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-teal-50 text-gray-600 hover:text-teal-700 transition-colors border border-transparent hover:border-teal-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">receipt</span> Listado de Facturas
                    </a>
                    @endif
                    @if(Auth::user()->tienePermiso('gestionar_caja_diaria'))
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-teal-50 text-gray-600 hover:text-teal-700 transition-colors border border-transparent hover:border-teal-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">store</span> Caja Diaria / POS
                    </a>
                    @endif
                    @if(Auth::user()->tienePermiso('gestionar_tasas_cambio'))
                    <a href="{{ route('admin.tasas-cambio.index') }}" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-teal-50 text-gray-600 hover:text-teal-700 transition-colors border border-transparent hover:border-teal-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">currency_exchange</span> Tasas de Cambio
                    </a>
                    @endif
                    @if(Auth::user()->tienePermiso('configurar_facturacion'))
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-teal-50 text-gray-600 hover:text-teal-700 transition-colors border border-transparent hover:border-teal-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">settings</span> Config. Facturación
                    </a>
                    @endif
                </div>
            </div>
        </details>
        @endif

        {{-- MÓDULO 7: RECETAS VETERINARIAS --}}
        @if(Auth::user()->tienePermiso('ver_recetas') || Auth::user()->tienePermiso('auditar_recetas'))
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
                    Módulo de cumplimiento. Revisa y audita las recetas médicas subidas por clientes para medicamentos restringidos.
                </p>
            </summary>
            
            <div class="px-6 pb-6 pt-2 border-t border-gray-50 animate-fade-in-up">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-4">
                    @if(Auth::user()->tienePermiso('ver_recetas'))
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-purple-50 text-gray-600 hover:text-purple-700 transition-colors border border-transparent hover:border-purple-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">medical_services</span> Listado de Recetas
                    </a>
                    @endif
                    @if(Auth::user()->tienePermiso('auditar_recetas'))
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-purple-50 text-gray-600 hover:text-purple-700 transition-colors border border-transparent hover:border-purple-100 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">fact_check</span> Revisión Pendientes
                    </a>
                    @endif
                </div>
            </div>
        </details>
        @endif

        {{-- MÓDULO 8: SISTEMA Y AJUSTES --}}
        @if(Auth::user()->tienePermiso('configurar_tienda') || Auth::user()->tienePermiso('gestionar_cuentas_banco') || Auth::user()->tienePermiso('configurar_apis') || Auth::user()->tienePermiso('gestionar_horarios') || Auth::user()->tienePermiso('ver_logs_auditoria'))
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
                    Configuraciones globales. Modifica datos de empresa, cuentas bancarias, APIs externas, horarios y logs de auditoría.
                </p>
            </summary>
            
            <div class="px-6 pb-6 pt-2 border-t border-gray-50 animate-fade-in-up">
                <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 mt-4">
                    @if(Auth::user()->tienePermiso('configurar_tienda'))
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-600 hover:text-gray-900 transition-colors border border-transparent hover:border-gray-200 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">storefront</span> Configuración Tienda
                    </a>
                    @endif
                    @if(Auth::user()->tienePermiso('gestionar_cuentas_banco'))
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-600 hover:text-gray-900 transition-colors border border-transparent hover:border-gray-200 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">account_balance</span> Cuentas Bancarias
                    </a>
                    @endif
                    @if(Auth::user()->tienePermiso('configurar_apis'))
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-600 hover:text-gray-900 transition-colors border border-transparent hover:border-gray-200 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">api</span> APIs Externas
                    </a>
                    @endif
                    @if(Auth::user()->tienePermiso('gestionar_horarios'))
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-600 hover:text-gray-900 transition-colors border border-transparent hover:border-gray-200 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">schedule</span> Horarios Físicos
                    </a>
                    @endif
                    @if(Auth::user()->tienePermiso('ver_logs_auditoria'))
                    <a href="#" class="flex items-center gap-2 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-600 hover:text-gray-900 transition-colors border border-transparent hover:border-gray-200 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px]">plagiarism</span> Logs de Auditoría
                    </a>
                    @endif
                </div>
            </div>
        </details>
        @endif

    </div>
</div>