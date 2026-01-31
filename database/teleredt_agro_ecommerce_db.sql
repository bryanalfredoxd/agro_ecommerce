-- ==========================================================
-- E-COMMERCE AGROPECUARIA VENEZUELA - DB MEJORADA
-- ==========================================================
CREATE DATABASE IF NOT EXISTS agro_ecommerce_db;
USE agro_ecommerce_db;

-- ----------------------------------------------------------
-- 1. SEGURIDAD Y USUARIOS (Mantiene estructura sólida)
-- ----------------------------------------------------------
CREATE TABLE permisos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) UNIQUE NOT NULL, 
    descripcion TEXT,
    creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) UNIQUE NOT NULL -- Admin, Vendedor, Almacenista, Cliente, Repartidor
) ENGINE=InnoDB;

CREATE TABLE rol_permisos (
    rol_id INT,
    permiso_id INT,
    PRIMARY KEY (rol_id, permiso_id),
    FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permiso_id) REFERENCES permisos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rol_id INT,
    nombre VARCHAR(100),
    apellido VARCHAR(100),
    email VARCHAR(150) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    documento_identidad VARCHAR(20), -- Cédula o RIF
    tipo_cliente ENUM('natural', 'juridico', 'finca_productor') DEFAULT 'natural', -- Nuevo para Agro
    activo BOOLEAN DEFAULT TRUE,
    creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id)
) ENGINE=InnoDB;

CREATE TABLE permisos_extra_usuario (
    usuario_id INT,
    permiso_id INT,
    accion ENUM('permitir', 'denegar') DEFAULT 'permitir',
    PRIMARY KEY (usuario_id, permiso_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (permiso_id) REFERENCES permisos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE direcciones_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    alias VARCHAR(50), 
    direccion_texto TEXT NOT NULL,
    referencia_punto TEXT, -- Importante para fincas/zonas rurales
    geo_latitud DECIMAL(10, 8),
    geo_longitud DECIMAL(11, 8),
    es_principal BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE password_resets (
    email VARCHAR(150) NOT NULL,
    token VARCHAR(255) NOT NULL,
    creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (email)
) ENGINE=InnoDB;

-- Tabla para sesiones (requerida por Laravel)
CREATE TABLE sessions (
    id VARCHAR(255) NOT NULL PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    INDEX sessions_user_id_index (user_id),
    INDEX sessions_last_activity_index (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 2. CONFIGURACIÓN Y MONEDAS (Vital para Venezuela)
-- ----------------------------------------------------------
CREATE TABLE configuracion_api (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_api VARCHAR(100) UNIQUE NOT NULL,
    url_base VARCHAR(255),
    api_key VARCHAR(255),
    api_secret VARCHAR(255),
    activo BOOLEAN DEFAULT TRUE,
    actualizado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE configuracion_tienda (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_empresa VARCHAR(100),
    iva_porcentaje DECIMAL(5,2) DEFAULT 16.00,
    modo_operativo ENUM('automatico', 'manual_abierto', 'manual_cerrado') DEFAULT 'automatico',
    mensaje_cierre_emergencia TEXT,
    ultimo_editor_id INT NULL,
    actualizado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE tasas_cambio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_moneda VARCHAR(10) DEFAULT 'USD',
    moneda_base VARCHAR(10) DEFAULT 'VES',
    valor_tasa DECIMAL(15, 4) NOT NULL,
    fuente ENUM('API', 'MANUAL') DEFAULT 'API',
    usuario_editor_id INT, 
    creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_editor_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- Horarios (Útil para saber cuándo se despacha a zonas rurales)
CREATE TABLE horarios_semanales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dia_semana INT NOT NULL UNIQUE, 
    es_laborable BOOLEAN DEFAULT TRUE,
    hora_apertura TIME, 
    hora_cierre TIME
) ENGINE=InnoDB;

CREATE TABLE horarios_especiales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE UNIQUE NOT NULL, 
    motivo VARCHAR(100), 
    es_laborable BOOLEAN DEFAULT FALSE, 
    hora_apertura TIME NULL, 
    hora_cierre TIME NULL
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- 3. LOGÍSTICA (Adaptado para Agro)
-- ----------------------------------------------------------
CREATE TABLE zonas_delivery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_zona VARCHAR(100),
    precio_delivery_usd DECIMAL(10, 2) NOT NULL,
    tiempo_estimado_minutos INT,
    requiere_vehiculo_carga BOOLEAN DEFAULT FALSE, -- Para sacos de alimento o alambre
    activa BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

CREATE TABLE repartidores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    tipo_vehiculo ENUM('moto', 'carro', 'camion_carga', 'pickup') DEFAULT 'moto', -- Ajustado
    placa VARCHAR(20),
    disponible BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- 4. INVENTARIO Y PRODUCTOS (CORAZÓN DEL AGRO)
-- ----------------------------------------------------------
CREATE TABLE proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    razon_social VARCHAR(200),
    rif VARCHAR(20) UNIQUE,
    telefono VARCHAR(20),
    persona_contacto VARCHAR(100),
    activo BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

-- NUEVA TABLA: Marcas / Laboratorios (Crucial en Agro: Bayer, Purina, etc.)
CREATE TABLE marcas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) UNIQUE NOT NULL,
    pais_origen VARCHAR(100),
    activo BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100), -- Ej: Veterinaria, Semillas, Ferretería, Nutrición
    imagen_url VARCHAR(255),
    categoria_padre_id INT NULL,
    FOREIGN KEY (categoria_padre_id) REFERENCES categorias(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT,
    marca_id INT, -- Nuevo enlace a marca
    proveedor_defecto_id INT NULL,
    
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    sku VARCHAR(100) UNIQUE,
    codigo_barras VARCHAR(100) UNIQUE, 
    
    -- Precios y Costos
    costo_promedio_usd DECIMAL(15, 4) DEFAULT 0.0000,
    precio_venta_usd DECIMAL(15, 2) NOT NULL,
    precio_oferta_usd DECIMAL(15, 2) NULL,
    
    -- Unidades AGROPECUARIAS
    -- 'unidad': Herramientas / 'kg': Granel / 'saco': Alimento / 'metro': Mecatillo-Guaya
    -- 'ml': Vacunas peq / 'litro': Venenos / 'dosis': Semen o vacunas
    unidad_medida ENUM('unidad', 'kg', 'g', 'mg', 'litro', 'ml', 'galon', 'saco', 'bulto', 'paquete', 'metro', 'rollo', 'dosis') DEFAULT 'unidad',
    
    -- Datos Técnicos
    contenido_neto DECIMAL(10,3), -- Ej: Si es un saco, aquí va '40.000' (kg)
    unidad_contenido ENUM('kg', 'g', 'l', 'ml', 'unidad') DEFAULT 'kg',
    
    es_controlado BOOLEAN DEFAULT FALSE, -- Requiere récipe/permiso (Venenos fuertes o antibióticos)
    atributos_json JSON NULL, -- { "ingrediente_activo": "Ivermectina", "tiempo_retiro": "28 dias", "toxicidad": "Banda Roja" }
    
    -- Stock
    stock_total DECIMAL(12, 3) DEFAULT 0.000, 
    stock_minimo_alerta DECIMAL(12, 3) DEFAULT 5.000,
    
    -- Reglas de venta
    venta_minima DECIMAL(10, 3) DEFAULT 1.000,
    paso_venta DECIMAL(10, 3) DEFAULT 1.000, -- Ej: Vender guaya de 0.5 en 0.5 metros
    
    es_combo BOOLEAN DEFAULT FALSE,
    destacado BOOLEAN DEFAULT FALSE,
    eliminado_at TIMESTAMP NULL,
    
    FOREIGN KEY (categoria_id) REFERENCES categorias(id),
    FOREIGN KEY (marca_id) REFERENCES marcas(id),
    FOREIGN KEY (proveedor_defecto_id) REFERENCES proveedores(id)
) ENGINE=InnoDB;

-- MEJORA 1: Registro de Precios Históricos
CREATE TABLE historico_precios_productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    precio_anterior_usd DECIMAL(15, 2) NOT NULL,
    precio_nuevo_usd DECIMAL(15, 2) NOT NULL,
    motivo_cambio VARCHAR(255), -- 'ajuste_costo', 'oferta', 'inflacion', 'cambio_proveedor'
    usuario_editor_id INT NULL,
    creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_editor_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

CREATE TABLE producto_imagenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    url_imagen VARCHAR(255),
    es_principal BOOLEAN DEFAULT FALSE,
    orden INT DEFAULT 0,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE productos_compuestos ( -- Combos Agro (Ej: Kit de Vacunación)
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_padre_id INT NOT NULL, 
    producto_hijo_id INT NOT NULL,  
    cantidad_requerida DECIMAL(12, 3) NOT NULL, 
    FOREIGN KEY (producto_padre_id) REFERENCES productos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_hijo_id) REFERENCES productos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE inventario_lotes ( -- Vital para VENCIMIENTOS de medicinas
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    proveedor_id INT,
    numero_lote VARCHAR(50),
    fecha_vencimiento DATE NOT NULL,
    cantidad_inicial DECIMAL(12, 3) NOT NULL,
    cantidad_restante DECIMAL(12, 3) NOT NULL,
    costo_unitario_usd DECIMAL(15, 4) NOT NULL,
    ubicacion_almacen VARCHAR(50), -- Estante A, Pasillo 3 (Nuevo)
    activo BOOLEAN DEFAULT TRUE,
    creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id),
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id)
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- 5. PUNTO DE VENTA Y CAJAS
-- ----------------------------------------------------------
CREATE TABLE cajas_fisicas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    activa BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

CREATE TABLE sesiones_caja (
    id INT AUTO_INCREMENT PRIMARY KEY,
    caja_id INT NOT NULL,
    cajero_usuario_id INT NOT NULL,
    fecha_apertura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_cierre TIMESTAMP NULL,
    monto_inicial_usd DECIMAL(15, 2) NOT NULL,
    total_ventas_sistema_usd DECIMAL(15, 2) DEFAULT 0.00,
    total_ventas_sistema_ves DECIMAL(15, 2) DEFAULT 0.00,
    dinero_real_en_caja_usd DECIMAL(15, 2) NULL,
    dinero_real_en_caja_ves DECIMAL(15, 2) NULL,
    diferencia_usd DECIMAL(15, 2) NULL,
    observaciones_cierre TEXT,
    FOREIGN KEY (caja_id) REFERENCES cajas_fisicas(id),
    FOREIGN KEY (cajero_usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

CREATE TABLE movimientos_caja (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sesion_caja_id INT NOT NULL,
    tipo ENUM('ingreso', 'egreso') NOT NULL,
    motivo VARCHAR(255),
    monto_usd DECIMAL(15, 2) DEFAULT 0,
    monto_ves DECIMAL(15, 2) DEFAULT 0,
    creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sesion_caja_id) REFERENCES sesiones_caja(id)
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- 6. VENTAS Y PEDIDOS
-- ----------------------------------------------------------
CREATE TABLE cupones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    tipo_descuento ENUM('porcentaje', 'monto_fijo') NOT NULL,
    valor_descuento DECIMAL(15, 2) NOT NULL,
    compra_minima_usd DECIMAL(15, 2) DEFAULT 0.00,
    fecha_inicio DATETIME,
    fecha_fin DATETIME,
    limite_uso_total INT DEFAULT 1,
    veces_usado INT DEFAULT 0,
    activo BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    canal_venta ENUM('web', 'tienda_fisica', 'whatsapp') DEFAULT 'web',
    sesion_caja_id INT NULL, 
    
    usuario_id INT NULL, 
    zona_delivery_id INT NULL, 
    tasa_cambio_id INT,
    cupon_id INT NULL,
    
    -- Importes
    subtotal_usd DECIMAL(15, 2),
    costo_delivery_usd DECIMAL(15, 2) DEFAULT 0,
    descuento_usd DECIMAL(15, 2) DEFAULT 0,
    total_usd DECIMAL(15, 2),
    total_ves_calculado DECIMAL(15, 2),
    
    estado ENUM('pendiente', 'pagado', 'preparacion', 'en_ruta', 'entregado', 'devuelto', 'cancelado', 'completado_caja') DEFAULT 'pendiente',
    
    -- Datos Web / Delivery
    direccion_texto TEXT,
    geo_latitud DECIMAL(10, 8),
    geo_longitud DECIMAL(11, 8),
    instrucciones_entrega TEXT,
    
    -- MEJORA 2: Devoluciones
    motivo_devolucion TEXT,
    fecha_devolucion TIMESTAMP NULL,
    usuario_autorizo_devolucion_id INT NULL,
    
    creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (sesion_caja_id) REFERENCES sesiones_caja(id),
    FOREIGN KEY (zona_delivery_id) REFERENCES zonas_delivery(id),
    FOREIGN KEY (tasa_cambio_id) REFERENCES tasas_cambio(id),
    FOREIGN KEY (usuario_autorizo_devolucion_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

CREATE TABLE pedido_detalles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT,
    producto_id INT,
    
    -- MEJORA 4: Trazabilidad por lote
    inventario_lote_id INT NULL, -- Saber exactamente de qué lote se despachó
    
    cantidad_solicitada DECIMAL(12, 3) NOT NULL, 
    -- Útil para productos a granel (pides 1kg, te despachan 1.05kg)
    cantidad_real_despachada DECIMAL(12, 3) NULL, 
    
    precio_historico_usd DECIMAL(15, 2),
    
    -- Cambiado de 'instrucciones_corte' a 'observaciones'
    observaciones VARCHAR(255) NULL, 
    
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id),
    FOREIGN KEY (inventario_lote_id) REFERENCES inventario_lotes(id)
) ENGINE=InnoDB;

-- MEJORA 2: Devoluciones y Ajustes de Inventario (Tabla Detallada)
CREATE TABLE devoluciones_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    pedido_detalle_id INT NOT NULL,
    inventario_lote_id INT NULL, -- Lote específico devuelto (si aplica)
    cantidad_devuelta DECIMAL(12, 3) NOT NULL,
    motivo_devolucion ENUM('producto_danado', 'no_corresponde', 'vencido', 'sobrante', 'error_despacho', 'cliente_arrepentido') NOT NULL,
    estado_producto ENUM('recibido', 'en_verificacion', 'reincorporado', 'descarte', 'devuelto_proveedor') DEFAULT 'recibido',
    costo_reposicion_usd DECIMAL(15, 4) NULL, -- Costo si se debe reponer
    observaciones TEXT,
    usuario_recibe_id INT NULL,
    fecha_recepcion TIMESTAMP NULL,
    usuario_procesa_id INT NULL,
    fecha_procesamiento TIMESTAMP NULL,
    
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY (pedido_detalle_id) REFERENCES pedido_detalles(id),
    FOREIGN KEY (inventario_lote_id) REFERENCES inventario_lotes(id),
    FOREIGN KEY (usuario_recibe_id) REFERENCES usuarios(id),
    FOREIGN KEY (usuario_procesa_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

CREATE TABLE entrega_seguimiento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT,
    repartidor_id INT,
    hora_recogida TIMESTAMP NULL,
    hora_entrega TIMESTAMP NULL,
    estado_rastreo VARCHAR(100), 
    observaciones_entrega TEXT,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY (repartidor_id) REFERENCES repartidores(id)
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- 7. FACTURACIÓN Y PAGOS
-- ----------------------------------------------------------
CREATE TABLE factura_ajustes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    serie VARCHAR(10) NOT NULL,
    proximo_numero INT NOT NULL DEFAULT 1,
    porcentaje_iva DECIMAL(5, 2) DEFAULT 16.00,
    activo BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

CREATE TABLE facturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT UNIQUE,
    serie_usada VARCHAR(10), 
    numero_factura VARCHAR(20) UNIQUE NOT NULL,
    cedula_rif_cliente VARCHAR(20),
    nombre_razon_social VARCHAR(200),
    direccion_fiscal TEXT,
    
    subtotal_usd DECIMAL(15, 2),
    impuesto_usd DECIMAL(15, 2),
    total_usd DECIMAL(15, 2),
    
    valor_tasa_bcv DECIMAL(15, 4),
    total_ves DECIMAL(15, 2),
    
    estado ENUM('emitida', 'anulada', 'reembolsada') DEFAULT 'emitida',
    fecha_emision TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
) ENGINE=InnoDB;

CREATE TABLE pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT,
    metodo ENUM('pago_movil', 'zelle', 'efectivo_usd', 'efectivo_bs', 'transferencia', 'punto_venta', 'binance', 'biopago'),
    referencia_bancaria VARCHAR(100),
    monto_usd DECIMAL(15, 2),
    monto_ves DECIMAL(15, 2),
    captura_pago_url VARCHAR(255),
    estado ENUM('revision', 'aprobado', 'rechazado') DEFAULT 'revision',
    verificado_por_usuario_id INT,
    fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY (verificado_por_usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- 8. CARRITO Y AUDITORÍA
-- ----------------------------------------------------------
CREATE TABLE carrito (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    producto_id INT,
    cantidad DECIMAL(12, 3), 
    observaciones VARCHAR(255), -- Modificado
    actualizado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
) ENGINE=InnoDB;

CREATE TABLE favoritos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    producto_id INT,
    UNIQUE(usuario_id, producto_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
) ENGINE=InnoDB;

-- MEJORA 3: Sistema de Recetas/Formularios para Productos Controlados
CREATE TABLE recetas_veterinarias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL, -- Relación con el pedido
    cliente_usuario_id INT NOT NULL, -- Cliente que presenta la receta
    veterinario_nombre VARCHAR(200) NOT NULL, -- Nombre del veterinario
    veterinario_matricula VARCHAR(50), -- Matrícula profesional
    cliente_animal_tipo VARCHAR(100), -- Tipo de animal (bovino, porcino, avícola, etc.)
    cliente_animal_cantidad INT, -- Cantidad de animales a tratar
    fecha_prescription DATE, -- Fecha de la prescripción
    fecha_vencimiento_receta DATE, -- Hasta cuándo es válida
    archivo_url VARCHAR(255), -- PDF o imagen escaneada
    estado ENUM('pendiente', 'aprobada', 'rechazada', 'expirada') DEFAULT 'pendiente',
    observaciones TEXT,
    usuario_revisa_id INT NULL, -- Usuario que revisa la receta
    fecha_revision TIMESTAMP NULL,
    creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY (cliente_usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (usuario_revisa_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- Tabla intermedia para productos controlados en recetas
CREATE TABLE receta_productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    receta_id INT NOT NULL,
    producto_id INT NOT NULL, -- Producto controlado
    cantidad_prescrita DECIMAL(12, 3) NOT NULL, -- Cantidad prescrita
    dosis_instrucciones TEXT, -- Instrucciones de dosificación
    autorizado BOOLEAN DEFAULT FALSE, -- Si fue autorizado en la revisión
    
    FOREIGN KEY (receta_id) REFERENCES recetas_veterinarias(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
) ENGINE=InnoDB;

CREATE TABLE auditoria_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL, 
    evento VARCHAR(50), 
    tabla_afectada VARCHAR(50),
    registro_id INT,
    valores_anteriores JSON, 
    valores_nuevos JSON,      
    direccion_ip VARCHAR(45), 
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE estadisticas_ventas_diarias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha_reporte DATE UNIQUE NOT NULL,
    total_pedidos INT DEFAULT 0,
    total_ingresos_usd DECIMAL(15, 2) DEFAULT 0.00,
    total_ingresos_ves DECIMAL(15, 2) DEFAULT 0.00,
    unidades_vendidas DECIMAL(15, 3) DEFAULT 0.000, -- Renombrado de kilos a unidades
    creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- 9. TRIGGERS (Actualizados para Agro con Mejoras)
-- ----------------------------------------------------------
DELIMITER //

-- 1. Calcular Costo Promedio al entrar Lote
CREATE TRIGGER tr_entrada_lote_costos
AFTER INSERT ON inventario_lotes
FOR EACH ROW
BEGIN
    DECLARE stock_actual DECIMAL(12, 3);
    DECLARE costo_actual DECIMAL(15, 4);
    DECLARE nuevo_costo DECIMAL(15, 4);
    
    SELECT stock_total, costo_promedio_usd INTO stock_actual, costo_actual 
    FROM productos WHERE id = NEW.producto_id;
    
    IF (stock_actual + NEW.cantidad_inicial) > 0 THEN
        SET nuevo_costo = ((stock_actual * costo_actual) + (NEW.cantidad_inicial * NEW.costo_unitario_usd)) / (stock_actual + NEW.cantidad_inicial);
    ELSE
        SET nuevo_costo = NEW.costo_unitario_usd;
    END IF;

    UPDATE productos 
    SET stock_total = stock_total + NEW.cantidad_inicial,
        costo_promedio_usd = nuevo_costo
    WHERE id = NEW.producto_id;
END //

-- 2. Registrar Cambio de Precios en Histórico
CREATE TRIGGER tr_registrar_cambio_precio
AFTER UPDATE ON productos
FOR EACH ROW
BEGIN
    IF NEW.precio_venta_usd != OLD.precio_venta_usd THEN
        INSERT INTO historico_precios_productos 
        (producto_id, precio_anterior_usd, precio_nuevo_usd, usuario_editor_id)
        VALUES 
        (NEW.id, OLD.precio_venta_usd, NEW.precio_venta_usd, NULL);
    END IF;
END //

-- 3. Descontar Inventario (Actualizado con trazabilidad de lotes)
CREATE TRIGGER tr_descontar_inventario_universal
AFTER UPDATE ON pedidos
FOR EACH ROW
BEGIN
    IF (NEW.estado = 'pagado' OR NEW.estado = 'completado_caja') 
       AND (OLD.estado != 'pagado' AND OLD.estado != 'completado_caja') THEN
       
       -- Descontar del stock total del producto
       UPDATE productos p
       JOIN pedido_detalles pd ON p.id = pd.producto_id
       SET p.stock_total = p.stock_total - COALESCE(pd.cantidad_real_despachada, pd.cantidad_solicitada)
       WHERE pd.pedido_id = NEW.id;
       
       -- Descontar del lote específico si está asignado
       UPDATE inventario_lotes il
       JOIN pedido_detalles pd ON il.id = pd.inventario_lote_id
       SET il.cantidad_restante = il.cantidad_restante - COALESCE(pd.cantidad_real_despachada, pd.cantidad_solicitada)
       WHERE pd.pedido_id = NEW.id AND pd.inventario_lote_id IS NOT NULL;
       
       -- Registrar en estadísticas diarias
       INSERT INTO estadisticas_ventas_diarias (fecha_reporte, total_pedidos, total_ingresos_usd, total_ingresos_ves)
       VALUES (CURDATE(), 1, NEW.total_usd, NEW.total_ves_calculado)
       ON DUPLICATE KEY UPDATE 
           total_pedidos = total_pedidos + 1,
           total_ingresos_usd = total_ingresos_usd + NEW.total_usd,
           total_ingresos_ves = total_ingresos_ves + NEW.total_ves_calculado;
    END IF;
END //

-- 4. Reintegrar Inventario por Devolución
CREATE TRIGGER tr_reintegrar_inventario_devolucion
AFTER UPDATE ON pedidos
FOR EACH ROW
BEGIN
    IF NEW.estado = 'devuelto' AND OLD.estado != 'devuelto' THEN
        -- Reintegrar al stock total del producto
        UPDATE productos p
        JOIN pedido_detalles pd ON p.id = pd.producto_id
        SET p.stock_total = p.stock_total + COALESCE(pd.cantidad_real_despachada, pd.cantidad_solicitada)
        WHERE pd.pedido_id = NEW.id;
        
        -- Reintegrar al lote específico si estaba asignado
        UPDATE inventario_lotes il
        JOIN pedido_detalles pd ON il.id = pd.inventario_lote_id
        SET il.cantidad_restante = il.cantidad_restante + COALESCE(pd.cantidad_real_despachada, pd.cantidad_solicitada)
        WHERE pd.pedido_id = NEW.id AND pd.inventario_lote_id IS NOT NULL;
        
        -- Ajustar estadísticas diarias (revertir la venta)
        UPDATE estadisticas_ventas_diarias
        SET total_pedidos = GREATEST(0, total_pedidos - 1),
            total_ingresos_usd = GREATEST(0, total_ingresos_usd - NEW.total_usd),
            total_ingresos_ves = GREATEST(0, total_ingresos_ves - NEW.total_ves_calculado)
        WHERE fecha_reporte = DATE(NEW.creado_at);
    END IF;
END //

-- 5. Reintegrar Inventario desde Devoluciones Detalladas
CREATE TRIGGER tr_reintegrar_desde_devolucion_detalle
AFTER INSERT ON devoluciones_detalle
FOR EACH ROW
BEGIN
    DECLARE producto_id_val INT;
    
    -- Obtener el producto_id del pedido_detalle
    SELECT producto_id INTO producto_id_val 
    FROM pedido_detalles 
    WHERE id = NEW.pedido_detalle_id;
    
    -- Reintegrar al stock total del producto
    UPDATE productos 
    SET stock_total = stock_total + NEW.cantidad_devuelta
    WHERE id = producto_id_val;
    
    -- Si tiene lote específico, reintegrar al lote
    IF NEW.inventario_lote_id IS NOT NULL THEN
        UPDATE inventario_lotes 
        SET cantidad_restante = cantidad_restante + NEW.cantidad_devuelta
        WHERE id = NEW.inventario_lote_id;
    END IF;
END //

DELIMITER ;

-- ----------------------------------------------------------
-- 10. ÍNDICES ADICIONALES PARA MEJOR PERFORMANCE
-- ----------------------------------------------------------
CREATE INDEX idx_inventario_lotes_producto ON inventario_lotes(producto_id);
CREATE INDEX idx_inventario_lotes_vencimiento ON inventario_lotes(fecha_vencimiento);
CREATE INDEX idx_pedidos_usuario ON pedidos(usuario_id);
CREATE INDEX idx_pedidos_estado ON pedidos(estado);
CREATE INDEX idx_pedido_detalles_pedido ON pedido_detalles(pedido_id);
CREATE INDEX idx_pedido_detalles_producto ON pedido_detalles(producto_id);
CREATE INDEX idx_pedido_detalles_lote ON pedido_detalles(inventario_lote_id);
CREATE INDEX idx_recetas_pedido ON recetas_veterinarias(pedido_id);
CREATE INDEX idx_recetas_cliente ON recetas_veterinarias(cliente_usuario_id);
CREATE INDEX idx_devoluciones_pedido ON devoluciones_detalle(pedido_id);
CREATE INDEX idx_historico_precios_producto ON historico_precios_productos(producto_id);
CREATE INDEX idx_historico_precios_fecha ON historico_precios_productos(creado_at);

-- ----------------------------------------------------------
-- 11. DATOS INICIALES DE CONFIGURACIÓN
-- ----------------------------------------------------------
-- Insertar roles básicos
INSERT INTO roles (nombre) VALUES 
('Admin'), 
('Vendedor'), 
('Almacenista'), 
('Cliente'),
('Repartidor');

-- Insertar configuración inicial de tienda
INSERT INTO configuracion_tienda (nombre_empresa, iva_porcentaje, modo_operativo) 
VALUES ('Agropecuaria Venezuela', 16.00, 'automatico');

-- Insertar serie de facturación inicial
INSERT INTO factura_ajustes (serie, proximo_numero, activo) 
VALUES ('A001', 1, TRUE);

-- Insertar horarios semanales laborables
INSERT INTO horarios_semanales (dia_semana, es_laborable, hora_apertura, hora_cierre) VALUES
(1, TRUE, '08:00:00', '17:00:00'), -- Lunes
(2, TRUE, '08:00:00', '17:00:00'), -- Martes
(3, TRUE, '08:00:00', '17:00:00'), -- Miércoles
(4, TRUE, '08:00:00', '17:00:00'), -- Jueves
(5, TRUE, '08:00:00', '17:00:00'), -- Viernes
(6, TRUE, '08:00:00', '12:00:00'), -- Sábado
(7, FALSE, NULL, NULL); -- Domingo

-- Insertar tasa de cambio inicial
INSERT INTO tasas_cambio (codigo_moneda, moneda_base, valor_tasa, fuente) 
VALUES ('USD', 'VES', 36.50, 'MANUAL');

-- Insertar caja física por defecto
INSERT INTO cajas_fisicas (nombre, activa) VALUES ('Caja Principal', TRUE);

-- Insertar marcas comunes agropecuarias
INSERT INTO marcas (nombre, pais_origen, activo) VALUES
('Bayer', 'Alemania', TRUE),
('Purina', 'Estados Unidos', TRUE),
('Zoetis', 'Estados Unidos', TRUE),
('MSD Animal Health', 'Estados Unidos', TRUE),
('Elanco', 'Estados Unidos', TRUE),
('Pioneer', 'Estados Unidos', TRUE),
('Monsanto', 'Estados Unidos', TRUE),
('Syngenta', 'Suiza', TRUE),
('BASF', 'Alemania', TRUE),
('Genética Avícola', 'Venezuela', TRUE);

-- Insertar categorías principales agropecuarias
INSERT INTO categorias (nombre, categoria_padre_id) VALUES
('Veterinaria', NULL),
('Nutrición Animal', NULL),
('Semillas', NULL),
('Fertilizantes', NULL),
('Ferretería Agrícola', NULL),
('Equipos y Maquinaria', NULL);

-- Subcategorías de Veterinaria
INSERT INTO categorias (nombre, categoria_padre_id) VALUES
('Antibióticos', 1),
('Vacunas', 1),
('Desparasitantes', 1),
('Vitaminas y Suplementos', 1),
('Productos de Higiene', 1);

-- Subcategorías de Nutrición Animal
INSERT INTO categorias (nombre, categoria_padre_id) VALUES
('Alimento para Bovinos', 2),
('Alimento para Porcinos', 2),
('Alimento para Aves', 2),
('Concentrados', 2),
('Sales Mineralizadas', 2);

-- Subcategorías de Ferretería Agrícola
INSERT INTO categorias (nombre, categoria_padre_id) VALUES
('Herramientas Manuales', 5),
('Mangueras y Riego', 5),
('Alambre y Cercas', 5),
('Guayas y Mecates', 5),
('Pinturas y Protectores', 5);

-- Insertar permisos básicos (ejemplos)
INSERT INTO permisos (nombre, descripcion) VALUES
('ver_inventario', 'Permite ver el inventario de productos'),
('editar_inventario', 'Permite editar productos y stock'),
('ver_ventas', 'Permite ver reportes de ventas'),
('crear_ventas', 'Permite crear nuevas ventas'),
('ver_clientes', 'Permite ver información de clientes'),
('editar_clientes', 'Permite editar información de clientes'),
('ver_facturas', 'Permite ver facturas emitidas'),
('emitir_facturas', 'Permite emitir nuevas facturas'),
('ver_caja', 'Permite ver estado de caja'),
('abrir_cerrar_caja', 'Permite abrir y cerrar cajas'),
('ver_reportes', 'Permite acceder a reportes'),
('ver_configuracion', 'Permite ver configuración del sistema'),
('editar_configuracion', 'Permite editar configuración del sistema'),
('ver_recetas', 'Permite ver recetas veterinarias'),
('aprobar_recetas', 'Permite aprobar/rechazar recetas');

-- Asignar permisos a rol Admin (todos los permisos)
INSERT INTO rol_permisos (rol_id, permiso_id)
SELECT 1, id FROM permisos; -- Rol Admin (id=1) obtiene todos los permisos

-- Permisos para Vendedor
INSERT INTO rol_permisos (rol_id, permiso_id) VALUES
(2, 1), -- ver_inventario
(2, 3), -- ver_ventas
(2, 4), -- crear_ventas
(2, 5), -- ver_clientes
(2, 6), -- editar_clientes
(2, 7), -- ver_facturas
(2, 8), -- emitir_facturas
(2, 9), -- ver_caja
(2, 10); -- abrir_cerrar_caja

-- Permisos para Almacenista
INSERT INTO rol_permisos (rol_id, permiso_id) VALUES
(3, 1), -- ver_inventario
(3, 2), -- editar_inventario
(3, 14); -- ver_recetas

-- Permisos para Repartidor
INSERT INTO rol_permisos (rol_id, permiso_id) VALUES
(5, 3); -- ver_ventas (solo para ver sus entregas)

-- Crear usuario administrador inicial (password: admin123)
INSERT INTO usuarios (rol_id, nombre, apellido, email, password_hash, telefono, documento_identidad, tipo_cliente, activo) 
VALUES (1, 'Admin', 'Sistema', 'admin@agropecuaria.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '04141234567', 'V12345678', 'juridico', TRUE);

-- Insertar zonas de delivery de ejemplo
INSERT INTO zonas_delivery (nombre_zona, precio_delivery_usd, tiempo_estimado_minutos, requiere_vehiculo_carga, activa) VALUES
('Zona Centro Urbano', 2.00, 30, FALSE, TRUE),
('Zona Periferia', 3.50, 45, FALSE, TRUE),
('Zona Rural Ligera', 5.00, 60, FALSE, TRUE),
('Zona Rural Pesada', 8.00, 90, TRUE, TRUE),
('Zona Fincas Lejanas', 12.00, 120, TRUE, TRUE);

-- Insertar producto de ejemplo
INSERT INTO productos (
    categoria_id, 
    marca_id, 
    nombre, 
    descripcion, 
    sku, 
    codigo_barras,
    precio_venta_usd,
    unidad_medida,
    contenido_neto,
    unidad_contenido,
    es_controlado,
    atributos_json,
    stock_total,
    venta_minima,
    paso_venta
) VALUES (
    1, -- Veterinaria
    1, -- Bayer
    'Ivermectina Inyectable 1%',
    'Antiparasitario de amplio espectro para bovinos, ovinos y porcinos',
    'IVM-001',
    '7891234567890',
    15.50,
    'ml',
    1000.000,
    'ml',
    TRUE,
    '{"ingrediente_activo": "Ivermectina 1%", "tiempo_retiro": "28 días", "toxicidad": "Banda Azul", "especies": ["bovino", "ovino", "porcino"], "dosis": "1 ml por 50 kg de peso"}',
    25.000,
    50.000, -- Venta mínima 50ml
    50.000  -- Paso de venta 50ml
);

-- Insertar lote de ejemplo para el producto
INSERT INTO inventario_lotes (
    producto_id,
    proveedor_id,
    numero_lote,
    fecha_vencimiento,
    cantidad_inicial,
    cantidad_restante,
    costo_unitario_usd,
    ubicacion_almacen
) VALUES (
    1, -- Producto Ivermectina
    NULL, -- Sin proveedor específico
    'LOTE-2024-001',
    '2025-06-30',
    25.000, -- 25 litros (25000 ml)
    25.000,
    8.7500, -- Costo por ml
    'Estante A1'
);

-- Insertar configuración de API de tasas de cambio (ejemplo)
INSERT INTO configuracion_api (nombre_api, url_base, api_key, api_secret, activo) VALUES
('BCV', 'https://api.bcv.org.ve', 'demo_key', 'demo_secret', TRUE),
('Monitor Dolar', 'https://monitordolar.com/api', 'demo_key', 'demo_secret', TRUE);

-- ----------------------------------------------------------
-- 12. VISTAS ÚTILES PARA REPORTES
-- ----------------------------------------------------------
CREATE VIEW view_inventario_valorado AS
SELECT 
    p.id,
    p.nombre,
    p.sku,
    p.stock_total,
    p.costo_promedio_usd,
    (p.stock_total * p.costo_promedio_usd) as valor_total_usd,
    p.precio_venta_usd,
    (p.stock_total * p.precio_venta_usd) as valor_venta_total_usd,
    c.nombre as categoria,
    m.nombre as marca,
    CASE 
        WHEN p.stock_total <= p.stock_minimo_alerta THEN 'CRÍTICO'
        WHEN p.stock_total <= (p.stock_minimo_alerta * 2) THEN 'BAJO'
        ELSE 'NORMAL'
    END as estado_stock
FROM productos p
LEFT JOIN categorias c ON p.categoria_id = c.id
LEFT JOIN marcas m ON p.marca_id = m.id
WHERE p.eliminado_at IS NULL;

CREATE VIEW view_productos_proximos_vencer AS
SELECT 
    il.id as lote_id,
    p.nombre as producto,
    il.numero_lote,
    il.fecha_vencimiento,
    DATEDIFF(il.fecha_vencimiento, CURDATE()) as dias_para_vencer,
    il.cantidad_restante,
    p.unidad_medida,
    CASE 
        WHEN DATEDIFF(il.fecha_vencimiento, CURDATE()) <= 30 THEN 'PRÓXIMO A VENCER'
        WHEN DATEDIFF(il.fecha_vencimiento, CURDATE()) <= 90 THEN 'ALERTA MEDIA'
        ELSE 'NORMAL'
    END as estado_vencimiento
FROM inventario_lotes il
JOIN productos p ON il.producto_id = p.id
WHERE il.activo = TRUE 
AND il.cantidad_restante > 0
AND il.fecha_vencimiento >= CURDATE()
ORDER BY il.fecha_vencimiento ASC;

CREATE VIEW view_ventas_diarias_detalladas AS
SELECT 
    DATE(p.creado_at) as fecha,
    COUNT(p.id) as total_pedidos,
    SUM(p.total_usd) as total_ventas_usd,
    SUM(p.total_ves_calculado) as total_ventas_ves,
    COUNT(DISTINCT p.usuario_id) as clientes_unicos,
    AVG(p.total_usd) as ticket_promedio_usd
FROM pedidos p
WHERE p.estado IN ('pagado', 'entregado', 'completado_caja')
GROUP BY DATE(p.creado_at)
ORDER BY fecha DESC;

CREATE VIEW view_productos_mas_vendidos AS
SELECT 
    p.id,
    p.nombre,
    p.sku,
    c.nombre as categoria,
    COUNT(pd.id) as veces_vendido,
    SUM(pd.cantidad_solicitada) as cantidad_total_vendida,
    p.unidad_medida,
    SUM(pd.cantidad_solicitada * pd.precio_historico_usd) as monto_total_usd
FROM pedido_detalles pd
JOIN productos p ON pd.producto_id = p.id
JOIN pedidos pe ON pd.pedido_id = pe.id
JOIN categorias c ON p.categoria_id = c.id
WHERE pe.estado IN ('pagado', 'entregado', 'completado_caja')
GROUP BY p.id, p.nombre, p.sku, c.nombre, p.unidad_medida
ORDER BY cantidad_total_vendida DESC;

-- ----------------------------------------------------------
-- 13. PROCEDIMIENTOS ALMACENADOS ÚTILES
-- ----------------------------------------------------------
DELIMITER //

-- Procedimiento para generar número de factura
CREATE PROCEDURE sp_generar_numero_factura(
    IN p_serie VARCHAR(10),
    OUT p_numero_factura VARCHAR(20)
)
BEGIN
    DECLARE v_proximo_numero INT;
    DECLARE v_serie_actual VARCHAR(10);
    
    -- Bloquear la tabla para evitar duplicados
    SELECT serie, proximo_numero INTO v_serie_actual, v_proximo_numero 
    FROM factura_ajustes 
    WHERE serie = p_serie AND activo = TRUE
    FOR UPDATE;
    
    IF v_serie_actual IS NOT NULL THEN
        -- Incrementar y actualizar
        UPDATE factura_ajustes 
        SET proximo_numero = proximo_numero + 1 
        WHERE serie = p_serie;
        
        -- Formatear número de factura (ej: A001-0000001)
        SET p_numero_factura = CONCAT(v_serie_actual, '-', LPAD(v_proximo_numero, 7, '0'));
    ELSE
        SET p_numero_factura = NULL;
    END IF;
END //

-- Procedimiento para procesar devolución completa de pedido
CREATE PROCEDURE sp_procesar_devolucion_pedido(
    IN p_pedido_id INT,
    IN p_usuario_id INT,
    IN p_motivo TEXT
)
BEGIN
    DECLARE v_pedido_existe INT;
    DECLARE v_detalle_id INT;
    DECLARE v_producto_id INT;
    DECLARE v_lote_id INT;
    DECLARE v_cantidad DECIMAL(12, 3);
    DECLARE v_finished INT DEFAULT 0;
    
    -- Cursor para recorrer detalles del pedido
    DECLARE cur_detalles CURSOR FOR
    SELECT id, producto_id, inventario_lote_id, cantidad_real_despachada 
    FROM pedido_detalles 
    WHERE pedido_id = p_pedido_id;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_finished = 1;
    
    -- Verificar que el pedido existe y no está ya devuelto
    SELECT COUNT(*) INTO v_pedido_existe FROM pedidos WHERE id = p_pedido_id;
    
    IF v_pedido_existe = 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'El pedido no existe';
    END IF;
    
    -- Actualizar estado del pedido
    UPDATE pedidos 
    SET estado = 'devuelto',
        motivo_devolucion = p_motivo,
        fecha_devolucion = NOW(),
        usuario_autorizo_devolucion_id = p_usuario_id
    WHERE id = p_pedido_id;
    
    -- Procesar cada detalle del pedido
    OPEN cur_detalles;
    
    read_loop: LOOP
        FETCH cur_detalles INTO v_detalle_id, v_producto_id, v_lote_id, v_cantidad;
        
        IF v_finished = 1 THEN
            LEAVE read_loop;
        END IF;
        
        -- Registrar en devoluciones_detalle
        INSERT INTO devoluciones_detalle (
            pedido_id,
            pedido_detalle_id,
            inventario_lote_id,
            cantidad_devuelta,
            motivo_devolucion,
            estado_producto,
            usuario_recibe_id,
            fecha_recepcion
        ) VALUES (
            p_pedido_id,
            v_detalle_id,
            v_lote_id,
            COALESCE(v_cantidad, 1), -- Si no hay cantidad_real_despachada, usar 1
            'cliente_arrepentido',
            'recibido',
            p_usuario_id,
            NOW()
        );
        
    END LOOP;
    
    CLOSE cur_detalles;
    
    -- Anular factura si existe
    UPDATE facturas 
    SET estado = 'anulada'
    WHERE pedido_id = p_pedido_id AND estado = 'emitida';
    
END //

-- Procedimiento para obtener productos con receta pendiente
CREATE PROCEDURE sp_obtener_productos_receta_pendiente(
    IN p_producto_id INT
)
BEGIN
    SELECT 
        rp.id,
        rv.cliente_usuario_id,
        CONCAT(u.nombre, ' ', u.apellido) as cliente_nombre,
        rv.veterinario_nombre,
        rv.fecha_prescription,
        rv.fecha_vencimiento_receta,
        rp.cantidad_prescrita,
        rp.dosis_instrucciones,
        rv.estado as estado_receta
    FROM receta_productos rp
    JOIN recetas_veterinarias rv ON rp.receta_id = rv.id
    JOIN usuarios u ON rv.cliente_usuario_id = u.id
    WHERE rp.producto_id = p_producto_id
    AND rv.estado = 'aprobada'
    AND rv.fecha_vencimiento_receta >= CURDATE()
    ORDER BY rv.fecha_prescription DESC;
END //

DELIMITER ;