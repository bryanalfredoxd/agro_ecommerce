<?php

return [
    [
        'id' => 'modulo_dashboard',
        'nombre' => 'Panel Principal & Métricas',
        'icono' => 'dashboard',
        'color' => 'indigo',
        'descripcion' => 'Acceso al tablero de control general y monitoreo de indicadores clave.',
        'permisos' => [
            'ver_dashboard' => [
                'nombre' => 'Ver Dashboard General',
                'tipo' => 'vista',
                'badge' => 'Visualización',
                'badge_color' => 'bg-sky-50 text-sky-700 border-sky-200',
                'descripcion' => 'Permite visualizar la pantalla principal del panel de control y sus gráficas.'
            ],
            'ver_kpis_financieros' => [
                'nombre' => 'Ver KPIs Financieros',
                'tipo' => 'sensible',
                'badge' => 'Financiero',
                'badge_color' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'descripcion' => 'Permite ver las tarjetas de ventas del día, ingresos en USD/Bs y tasas de cambio.'
            ],
            'ver_kpis_operativos' => [
                'nombre' => 'Ver KPIs Operativos',
                'tipo' => 'vista',
                'badge' => 'Operativo',
                'badge_color' => 'bg-blue-50 text-blue-700 border-blue-200',
                'descripcion' => 'Permite ver tarjetas de pedidos pendientes, alertas de stock y recetas por auditar.'
            ],
        ]
    ],
    [
        'id' => 'modulo_ventas',
        'nombre' => 'Ventas, Pedidos & Terminal POS',
        'icono' => 'shopping_cart',
        'color' => 'blue',
        'descripcion' => 'Gestión de órdenes web, mostrador POS, auditoría de pagos y devoluciones.',
        'permisos' => [
            'ver_pedidos' => [
                'nombre' => 'Ver Pedidos',
                'tipo' => 'vista',
                'badge' => 'Visualización',
                'badge_color' => 'bg-sky-50 text-sky-700 border-sky-200',
                'descripcion' => 'Permite visualizar el listado de pedidos web y consultar su detalle 360°.'
            ],
            'crear_venta_manual' => [
                'nombre' => 'Operar Terminal POS (Venta Mostrador)',
                'tipo' => 'operativo',
                'badge' => 'Punto de Venta',
                'badge_color' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                'descripcion' => 'Permite abrir el terminal POS, facturar ventas en mostrador y emitir tickets.'
            ],
            'actualizar_estado_pedido' => [
                'nombre' => 'Actualizar Estado de Pedidos',
                'tipo' => 'sensible',
                'badge' => 'Acción Sensible',
                'badge_color' => 'bg-amber-50 text-amber-700 border-amber-200',
                'descripcion' => 'Permite cambiar pedidos a preparación, en ruta, entregado o cancelar orden.'
            ],
            'gestionar_pagos' => [
                'nombre' => 'Auditar y Aprobar Pagos',
                'tipo' => 'sensible',
                'badge' => 'Acción Sensible',
                'badge_color' => 'bg-amber-50 text-amber-700 border-amber-200',
                'descripcion' => 'Permite ver comprobantes, aprobar o rechazar transferencias y pagos móviles.'
            ],
            'procesar_devoluciones' => [
                'nombre' => 'Procesar Devoluciones',
                'tipo' => 'sensible',
                'badge' => 'Acción Sensible',
                'badge_color' => 'bg-amber-50 text-amber-700 border-amber-200',
                'descripcion' => 'Permite registrar y procesar devoluciones parciales o totales de pedidos.'
            ],
        ]
    ],
    [
        'id' => 'modulo_catalogo',
        'nombre' => 'Catálogo, Productos e Inventario',
        'icono' => 'inventory_2',
        'color' => 'emerald',
        'descripcion' => 'Administración de artículos, lotes, marcas y categorías de la tienda.',
        'permisos' => [
            'ver_productos' => [
                'nombre' => 'Ver Catálogo de Productos',
                'tipo' => 'vista',
                'badge' => 'Visualización',
                'badge_color' => 'bg-sky-50 text-sky-700 border-sky-200',
                'descripcion' => 'Permite visualizar el catálogo y consultar precios e inventario.'
            ],
            'gestionar_productos' => [
                'nombre' => 'Gestionar Productos',
                'tipo' => 'sensible',
                'badge' => 'Acción Sensible',
                'badge_color' => 'bg-amber-50 text-amber-700 border-amber-200',
                'descripcion' => 'Permite crear, editar, eliminar productos, combos y alternar destacados.'
            ],
            'gestionar_inventario_lotes' => [
                'nombre' => 'Inventario y Lotes de Compra',
                'tipo' => 'sensible',
                'badge' => 'Acción Sensible',
                'badge_color' => 'bg-amber-50 text-amber-700 border-amber-200',
                'descripcion' => 'Permite registrar compras a proveedores, editar lotes y controlar vencimientos.'
            ],
            'gestionar_categorias_marcas' => [
                'nombre' => 'Gestionar Categorías y Marcas',
                'tipo' => 'sensible',
                'badge' => 'Acción Sensible',
                'badge_color' => 'bg-amber-50 text-amber-700 border-amber-200',
                'descripcion' => 'Permite crear, editar y organizar las categorías y marcas de la tienda.'
            ],
            'ver_historico_precios' => [
                'nombre' => 'Ver Histórico de Precios',
                'tipo' => 'vista',
                'badge' => 'Visualización',
                'badge_color' => 'bg-sky-50 text-sky-700 border-sky-200',
                'descripcion' => 'Permite visualizar el registro cronológico de fluctuaciones de precios.'
            ],
        ]
    ],
    [
        'id' => 'modulo_finanzas',
        'nombre' => 'Finanzas, Facturación y Caja',
        'icono' => 'receipt_long',
        'color' => 'teal',
        'descripcion' => 'Control de facturas emitidas, arqueos de caja diaria y tasas cambiarias.',
        'permisos' => [
            'ver_facturas' => [
                'nombre' => 'Ver Facturas Emitidas',
                'tipo' => 'vista',
                'badge' => 'Visualización',
                'badge_color' => 'bg-sky-50 text-sky-700 border-sky-200',
                'descripcion' => 'Permite consultar el listado de facturas emitidas e imprimir tickets/PDF.'
            ],
            'gestionar_caja_diaria' => [
                'nombre' => 'Gestionar Caja Diaria / Arqueos',
                'tipo' => 'sensible',
                'badge' => 'Acción Sensible',
                'badge_color' => 'bg-amber-50 text-amber-700 border-amber-200',
                'descripcion' => 'Permite abrir/cerrar turnos de caja, registrar movimientos y arqueos de efectivo.'
            ],
            'gestionar_tasas_cambio' => [
                'nombre' => 'Gestionar Tasas de Cambio',
                'tipo' => 'sensible',
                'badge' => 'Acción Sensible',
                'badge_color' => 'bg-amber-50 text-amber-700 border-amber-200',
                'descripcion' => 'Permite actualizar manualmente la tasa de cambio o sincronizar con BCV.'
            ],
            'configurar_facturacion' => [
                'nombre' => 'Configurar Facturación y Anulaciones',
                'tipo' => 'seguridad',
                'badge' => 'Fiscal / Crítico',
                'badge_color' => 'bg-purple-50 text-purple-700 border-purple-200',
                'descripcion' => 'Permite configurar series fiscales, porcentaje de IVA y anular facturas emitidas.'
            ],
        ]
    ],
    [
        'id' => 'modulo_logistica',
        'nombre' => 'Logística y Zonas de Delivery',
        'icono' => 'local_shipping',
        'color' => 'orange',
        'descripcion' => 'Zonas de cobertura en mapa interactivo, tarifas y personal de despacho.',
        'permisos' => [
            'ver_logistica' => [
                'nombre' => 'Ver Módulo de Logística',
                'tipo' => 'vista',
                'badge' => 'Visualización',
                'badge_color' => 'bg-sky-50 text-sky-700 border-sky-200',
                'descripcion' => 'Permite acceder al mapa y consultar el estado general de despachos.'
            ],
            'gestionar_zonas_delivery' => [
                'nombre' => 'Gestionar Zonas y Tarifas de Delivery',
                'tipo' => 'sensible',
                'badge' => 'Acción Sensible',
                'badge_color' => 'bg-amber-50 text-amber-700 border-amber-200',
                'descripcion' => 'Permite crear/editar polígonos de zonas de envío, tarifas y el switch maestro de delivery.'
            ],
            'gestionar_repartidores' => [
                'nombre' => 'Gestionar Personal de Reparto',
                'tipo' => 'operativo',
                'badge' => 'Operativo',
                'badge_color' => 'bg-blue-50 text-blue-700 border-blue-200',
                'descripcion' => 'Permite asignar repartidores a despachos y consultar su rendimiento.'
            ],
            'ver_seguimiento_rutas' => [
                'nombre' => 'Seguimiento de Rutas en Tiempo Real',
                'tipo' => 'vista',
                'badge' => 'Visualización',
                'badge_color' => 'bg-sky-50 text-sky-700 border-sky-200',
                'descripcion' => 'Permite monitorear la ubicación y avance de los repartidores en ruta.'
            ],
        ]
    ],
    [
        'id' => 'modulo_usuarios',
        'nombre' => 'Usuarios, Roles y Seguridad',
        'icono' => 'manage_accounts',
        'color' => 'violet',
        'descripcion' => 'Directorio de clientes y empleados, control de acceso y auditorías.',
        'permisos' => [
            'ver_usuarios' => [
                'nombre' => 'Ver Directorio de Usuarios',
                'tipo' => 'vista',
                'badge' => 'Visualización',
                'badge_color' => 'bg-sky-50 text-sky-700 border-sky-200',
                'descripcion' => 'Permite ver el listado de clientes registrados y personal del sistema.'
            ],
            'gestionar_usuarios' => [
                'nombre' => 'Gestionar Cuentas de Usuarios',
                'tipo' => 'sensible',
                'badge' => 'Acción Sensible',
                'badge_color' => 'bg-amber-50 text-amber-700 border-amber-200',
                'descripcion' => 'Permite editar datos de usuarios y suspender o reactivar cuentas.'
            ],
            'gestionar_roles_permisos' => [
                'nombre' => 'Gestionar Roles y Permisos RBAC',
                'tipo' => 'seguridad',
                'badge' => 'Seguridad / Crítico',
                'badge_color' => 'bg-purple-50 text-purple-700 border-purple-200',
                'descripcion' => 'Permite crear roles, asignar permisos a roles y configurar excepciones por usuario.'
            ],
            'ver_logs_auditoria' => [
                'nombre' => 'Ver Logs de Auditoría Forense',
                'tipo' => 'vista',
                'badge' => 'Auditoría',
                'badge_color' => 'bg-sky-50 text-sky-700 border-sky-200',
                'descripcion' => 'Permite consultar el registro detallado de acciones realizadas por el personal.'
            ],
        ]
    ],
    [
        'id' => 'modulo_recetas',
        'nombre' => 'Récipes y Control Agronómico',
        'icono' => 'prescriptions',
        'color' => 'cyan',
        'descripcion' => 'Validación de fórmulas y recetas agronómicas para venta controlada.',
        'permisos' => [
            'ver_recetas' => [
                'nombre' => 'Ver Recetas Subidas',
                'tipo' => 'vista',
                'badge' => 'Visualización',
                'badge_color' => 'bg-sky-50 text-sky-700 border-sky-200',
                'descripcion' => 'Permite ver la lista de recetas subidas por los clientes en sus pedidos.'
            ],
            'auditar_recetas' => [
                'nombre' => 'Auditar y Validar Récipes',
                'tipo' => 'sensible',
                'badge' => 'Acción Sensible',
                'badge_color' => 'bg-amber-50 text-amber-700 border-amber-200',
                'descripcion' => 'Permite aprobar o rechazar recetas para autorizar el despacho de insumos regulados.'
            ],
        ]
    ],
    [
        'id' => 'modulo_configuracion',
        'nombre' => 'Configuración del Sistema',
        'icono' => 'settings',
        'color' => 'slate',
        'descripcion' => 'Parámetros institucionales, horarios laborales, bancos e integraciones.',
        'permisos' => [
            'configurar_tienda' => [
                'nombre' => 'Datos de Tienda y Estado Operativo',
                'tipo' => 'sensible',
                'badge' => 'Acción Sensible',
                'badge_color' => 'bg-amber-50 text-amber-700 border-amber-200',
                'descripcion' => 'Permite editar datos fiscales, logos y alternar modo de tienda abierta/cerrada.'
            ],
            'gestionar_cuentas_banco' => [
                'nombre' => 'Gestionar Cuentas Bancarias',
                'tipo' => 'sensible',
                'badge' => 'Acción Sensible',
                'badge_color' => 'bg-amber-50 text-amber-700 border-amber-200',
                'descripcion' => 'Permite administrar cuentas para cobro (Pago Móvil, Zelle, transferencias).'
            ],
            'gestionar_horarios' => [
                'nombre' => 'Gestionar Horarios de Atención',
                'tipo' => 'sensible',
                'badge' => 'Acción Sensible',
                'badge_color' => 'bg-amber-50 text-amber-700 border-amber-200',
                'descripcion' => 'Permite definir el horario laboral semanal y programar días feriados.'
            ],
            'configurar_apis' => [
                'nombre' => 'Configuración de APIs y Conexiones',
                'tipo' => 'seguridad',
                'badge' => 'Seguridad / APIs',
                'badge_color' => 'bg-purple-50 text-purple-700 border-purple-200',
                'descripcion' => 'Permite configurar llaves de API externas, credenciales BCV y pasarelas.'
            ],
        ]
    ],
];
