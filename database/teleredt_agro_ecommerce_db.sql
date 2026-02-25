-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 24-02-2026 a las 16:37:24
-- Versión del servidor: 10.11.15-MariaDB-cll-lve
-- Versión de PHP: 8.4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `teleredt_agro_ecommerce_db`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_generar_numero_factura` (IN `p_serie` VARCHAR(10), OUT `p_numero_factura` VARCHAR(20))   BEGIN
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_productos_receta_pendiente` (IN `p_producto_id` INT)   BEGIN
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_procesar_devolucion_pedido` (IN `p_pedido_id` INT, IN `p_usuario_id` INT, IN `p_motivo` TEXT)   BEGIN
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
    
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_logs`
--

CREATE TABLE `auditoria_logs` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `evento` varchar(50) DEFAULT NULL,
  `tabla_afectada` varchar(50) DEFAULT NULL,
  `registro_id` int(11) DEFAULT NULL,
  `valores_anteriores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`valores_anteriores`)),
  `valores_nuevos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`valores_nuevos`)),
  `direccion_ip` varchar(45) DEFAULT NULL,
  `fecha` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajas_fisicas`
--

CREATE TABLE `cajas_fisicas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `activa` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--

CREATE TABLE `carrito` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad` INT NOT NULL DEFAULT 1,
  `observaciones` varchar(255) DEFAULT NULL,
  `actualizado_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  `categoria_padre_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_api`
--

CREATE TABLE `configuracion_api` (
  `id` int(11) NOT NULL,
  `nombre_api` varchar(100) NOT NULL,
  `url_base` varchar(255) DEFAULT NULL,
  `api_key` varchar(255) DEFAULT NULL,
  `api_secret` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `actualizado_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_tienda`
--

CREATE TABLE `configuracion_tienda` (
  `id` int(11) NOT NULL,
  `nombre_empresa` varchar(100) DEFAULT NULL,
  `iva_porcentaje` decimal(5,2) DEFAULT 16.00,
  `modo_operativo` enum('automatico','manual_abierto','manual_cerrado') DEFAULT 'automatico',
  `mensaje_cierre_emergencia` text DEFAULT NULL,
  `ultimo_editor_id` int(11) DEFAULT NULL,
  `actualizado_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas_bancarias`
--

CREATE TABLE `cuentas_bancarias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre_titular` varchar(255) DEFAULT NULL,
  `banco_entidad` varchar(255) DEFAULT NULL,
  `numero_cuenta` varchar(255) DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `identificacion` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `tipo_metodo` enum('pago_movil','zelle','efectivo_usd','efectivo_bs','transferencia','punto_venta','binance','biopago') NOT NULL,
  `instrucciones_adicionales` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cupones`
--

CREATE TABLE `cupones` (
  `id` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `tipo_descuento` enum('porcentaje','monto_fijo') NOT NULL,
  `valor_descuento` decimal(15,2) NOT NULL,
  `compra_minima_usd` decimal(15,2) DEFAULT 0.00,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `limite_uso_total` int(11) DEFAULT 1,
  `veces_usado` int(11) DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `devoluciones_detalle`
--

CREATE TABLE `devoluciones_detalle` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `pedido_detalle_id` int(11) NOT NULL,
  `inventario_lote_id` int(11) DEFAULT NULL,
  `cantidad_devuelta` decimal(12,3) NOT NULL,
  `motivo_devolucion` enum('producto_danado','no_corresponde','vencido','sobrante','error_despacho','cliente_arrepentido') NOT NULL,
  `estado_producto` enum('recibido','en_verificacion','reincorporado','descarte','devuelto_proveedor') DEFAULT 'recibido',
  `costo_reposicion_usd` decimal(15,4) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `usuario_recibe_id` int(11) DEFAULT NULL,
  `fecha_recepcion` timestamp NULL DEFAULT NULL,
  `usuario_procesa_id` int(11) DEFAULT NULL,
  `fecha_procesamiento` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Disparadores `devoluciones_detalle`
--
DELIMITER $$
CREATE TRIGGER `tr_reintegrar_desde_devolucion_detalle` AFTER INSERT ON `devoluciones_detalle` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direcciones_usuarios`
--

CREATE TABLE `direcciones_usuarios` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `alias` varchar(50) DEFAULT NULL,
  `direccion_texto` text NOT NULL,
  `referencia_punto` text DEFAULT NULL,
  `geo_latitud` decimal(10,8) DEFAULT NULL,
  `geo_longitud` decimal(11,8) DEFAULT NULL,
  `es_principal` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entrega_seguimiento`
--

CREATE TABLE `entrega_seguimiento` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) DEFAULT NULL,
  `repartidor_id` int(11) DEFAULT NULL,
  `hora_recogida` timestamp NULL DEFAULT NULL,
  `hora_entrega` timestamp NULL DEFAULT NULL,
  `estado_rastreo` varchar(100) DEFAULT NULL,
  `observaciones_entrega` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadisticas_ventas_diarias`
--

CREATE TABLE `estadisticas_ventas_diarias` (
  `id` int(11) NOT NULL,
  `fecha_reporte` date NOT NULL,
  `total_pedidos` int(11) DEFAULT 0,
  `total_ingresos_usd` decimal(15,2) DEFAULT 0.00,
  `total_ingresos_ves` decimal(15,2) DEFAULT 0.00,
  `unidades_vendidas` decimal(15,3) DEFAULT 0.000,
  `creado_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) DEFAULT NULL,
  `serie_usada` varchar(10) DEFAULT NULL,
  `numero_factura` varchar(20) NOT NULL,
  `cedula_rif_cliente` varchar(20) DEFAULT NULL,
  `nombre_razon_social` varchar(200) DEFAULT NULL,
  `direccion_fiscal` text DEFAULT NULL,
  `subtotal_usd` decimal(15,2) DEFAULT NULL,
  `impuesto_usd` decimal(15,2) DEFAULT NULL,
  `total_usd` decimal(15,2) DEFAULT NULL,
  `valor_tasa_bcv` decimal(15,4) DEFAULT NULL,
  `total_ves` decimal(15,2) DEFAULT NULL,
  `estado` enum('emitida','anulada','reembolsada') DEFAULT 'emitida',
  `fecha_emision` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura_ajustes`
--

CREATE TABLE `factura_ajustes` (
  `id` int(11) NOT NULL,
  `serie` varchar(10) NOT NULL,
  `proximo_numero` int(11) NOT NULL DEFAULT 1,
  `porcentaje_iva` decimal(5,2) DEFAULT 16.00,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `favoritos`
--

CREATE TABLE `favoritos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historico_precios_productos`
--

CREATE TABLE `historico_precios_productos` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `precio_anterior_usd` decimal(15,2) NOT NULL,
  `precio_nuevo_usd` decimal(15,2) NOT NULL,
  `motivo_cambio` varchar(255) DEFAULT NULL,
  `usuario_editor_id` int(11) DEFAULT NULL,
  `creado_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios_especiales`
--

CREATE TABLE `horarios_especiales` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `motivo` varchar(100) DEFAULT NULL,
  `es_laborable` tinyint(1) DEFAULT 0,
  `hora_apertura` time DEFAULT NULL,
  `hora_cierre` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios_semanales`
--

CREATE TABLE `horarios_semanales` (
  `id` int(11) NOT NULL,
  `dia_semana` int(11) NOT NULL,
  `es_laborable` tinyint(1) DEFAULT 1,
  `hora_apertura` time DEFAULT NULL,
  `hora_cierre` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario_lotes`
--

CREATE TABLE `inventario_lotes` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `proveedor_id` int(11) DEFAULT NULL,
  `numero_lote` varchar(50) DEFAULT NULL,
  `fecha_vencimiento` date NOT NULL,
  `cantidad_inicial` decimal(12,3) NOT NULL,
  `cantidad_restante` decimal(12,3) NOT NULL,
  `costo_unitario_usd` decimal(15,4) NOT NULL,
  `ubicacion_almacen` varchar(50) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `creado_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Disparadores `inventario_lotes`
--
DELIMITER $$
CREATE TRIGGER `tr_entrada_lote_costos` AFTER INSERT ON `inventario_lotes` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcas`
--

CREATE TABLE `marcas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `pais_origen` varchar(100) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_caja`
--

CREATE TABLE `movimientos_caja` (
  `id` int(11) NOT NULL,
  `sesion_caja_id` int(11) NOT NULL,
  `tipo` enum('ingreso','egreso') NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `monto_usd` decimal(15,2) DEFAULT 0.00,
  `monto_ves` decimal(15,2) DEFAULT 0.00,
  `creado_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) DEFAULT NULL,
  `metodo` enum('pago_movil','zelle','efectivo_usd','efectivo_bs','transferencia','punto_venta','binance','biopago') DEFAULT NULL,
  `referencia_bancaria` varchar(100) DEFAULT NULL,
  `monto_usd` decimal(15,2) DEFAULT NULL,
  `monto_ves` decimal(15,2) DEFAULT NULL,
  `captura_pago_url` varchar(255) DEFAULT NULL,
  `estado` enum('revision','aprobado','rechazado') DEFAULT 'revision',
  `verificado_por_usuario_id` int(11) DEFAULT NULL,
  `fecha_pago` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(150) NOT NULL,
  `token` varchar(255) NOT NULL,
  `creado_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `canal_venta` enum('web','tienda_fisica','whatsapp') DEFAULT 'web',
  `sesion_caja_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `zona_delivery_id` int(11) DEFAULT NULL,
  `tasa_cambio_id` int(11) DEFAULT NULL,
  `cupon_id` int(11) DEFAULT NULL,
  `subtotal_usd` decimal(15,2) DEFAULT NULL,
  `costo_delivery_usd` decimal(15,2) DEFAULT 0.00,
  `descuento_usd` decimal(15,2) DEFAULT 0.00,
  `total_usd` decimal(15,2) DEFAULT NULL,
  `total_ves_calculado` decimal(15,2) DEFAULT NULL,
  `estado` enum('pendiente','pagado','preparacion','en_ruta','entregado','devuelto','cancelado','completado_caja') DEFAULT 'pendiente',
  `direccion_texto` text DEFAULT NULL,
  `geo_latitud` decimal(10,8) DEFAULT NULL,
  `geo_longitud` decimal(11,8) DEFAULT NULL,
  `instrucciones_entrega` text DEFAULT NULL,
  `motivo_devolucion` text DEFAULT NULL,
  `fecha_devolucion` timestamp NULL DEFAULT NULL,
  `usuario_autorizo_devolucion_id` int(11) DEFAULT NULL,
  `creado_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Disparadores `pedidos`
--
DELIMITER $$
CREATE TRIGGER `tr_descontar_inventario_universal` AFTER UPDATE ON `pedidos` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_reintegrar_inventario_devolucion` AFTER UPDATE ON `pedidos` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_detalles`
--

CREATE TABLE `pedido_detalles` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `inventario_lote_id` int(11) DEFAULT NULL,
  `cantidad_solicitada` decimal(12,3) NOT NULL,
  `cantidad_real_despachada` decimal(12,3) DEFAULT NULL,
  `precio_historico_usd` decimal(15,2) DEFAULT NULL,
  `observaciones` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `creado_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos_extra_usuario`
--

CREATE TABLE `permisos_extra_usuario` (
  `usuario_id` int(11) NOT NULL,
  `permiso_id` int(11) NOT NULL,
  `accion` enum('permitir','denegar') DEFAULT 'permitir'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `marca_id` int(11) DEFAULT NULL,
  `proveedor_defecto_id` int(11) DEFAULT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen_url` VARCHAR(255),
  `sku` varchar(100) DEFAULT NULL,
  `codigo_barras` varchar(100) DEFAULT NULL,
  `costo_promedio_usd` decimal(15,4) DEFAULT 0.0000,
  `precio_venta_usd` decimal(15,2) NOT NULL,
  `precio_oferta_usd` decimal(15,2) DEFAULT NULL,
  `unidad_medida` enum('unidad','kg','g','mg','litro','ml','galon','saco','bulto','paquete','metro','rollo','dosis') DEFAULT 'unidad',
  `contenido_neto` decimal(10,3) DEFAULT NULL,
  `unidad_contenido` enum('kg','g','l','ml','unidad') DEFAULT 'kg',
  `es_controlado` tinyint(1) DEFAULT 0,
  `atributos_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`atributos_json`)),
  `stock_total` decimal(12,3) DEFAULT 0.000,
  `stock_minimo_alerta` decimal(12,3) DEFAULT 5.000,
  `venta_minima` decimal(10,3) DEFAULT 1.000,
  `paso_venta` decimal(10,3) DEFAULT 1.000,
  `es_combo` tinyint(1) DEFAULT 0,
  `destacado` tinyint(1) DEFAULT 0,
  `eliminado_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Disparadores `productos`
--
DELIMITER $$
CREATE TRIGGER `tr_registrar_cambio_precio` AFTER UPDATE ON `productos` FOR EACH ROW BEGIN
    IF NEW.precio_venta_usd != OLD.precio_venta_usd THEN
        INSERT INTO historico_precios_productos 
        (producto_id, precio_anterior_usd, precio_nuevo_usd, usuario_editor_id)
        VALUES 
        (NEW.id, OLD.precio_venta_usd, NEW.precio_venta_usd, NULL);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_compuestos`
--

CREATE TABLE `productos_compuestos` (
  `id` int(11) NOT NULL,
  `producto_padre_id` int(11) NOT NULL,
  `producto_hijo_id` int(11) NOT NULL,
  `cantidad_requerida` decimal(12,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_imagenes`
--

CREATE TABLE `producto_imagenes` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `url_imagen` varchar(255) DEFAULT NULL,
  `es_principal` tinyint(1) DEFAULT 0,
  `orden` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `razon_social` varchar(200) DEFAULT NULL,
  `rif` varchar(20) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `persona_contacto` varchar(100) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recetas_veterinarias`
--

CREATE TABLE `recetas_veterinarias` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `cliente_usuario_id` int(11) NOT NULL,
  `veterinario_nombre` varchar(200) NOT NULL,
  `veterinario_matricula` varchar(50) DEFAULT NULL,
  `cliente_animal_tipo` varchar(100) DEFAULT NULL,
  `cliente_animal_cantidad` int(11) DEFAULT NULL,
  `fecha_prescription` date DEFAULT NULL,
  `fecha_vencimiento_receta` date DEFAULT NULL,
  `archivo_url` varchar(255) DEFAULT NULL,
  `estado` enum('pendiente','aprobada','rechazada','expirada') DEFAULT 'pendiente',
  `observaciones` text DEFAULT NULL,
  `usuario_revisa_id` int(11) DEFAULT NULL,
  `fecha_revision` timestamp NULL DEFAULT NULL,
  `creado_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `receta_productos`
--

CREATE TABLE `receta_productos` (
  `id` int(11) NOT NULL,
  `receta_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad_prescrita` decimal(12,3) NOT NULL,
  `dosis_instrucciones` text DEFAULT NULL,
  `autorizado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `repartidores`
--

CREATE TABLE `repartidores` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `tipo_vehiculo` enum('moto','carro','camion_carga','pickup') DEFAULT 'moto',
  `placa` varchar(20) DEFAULT NULL,
  `disponible` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_permisos`
--

CREATE TABLE `rol_permisos` (
  `rol_id` int(11) NOT NULL,
  `permiso_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones_caja`
--

CREATE TABLE `sesiones_caja` (
  `id` int(11) NOT NULL,
  `caja_id` int(11) NOT NULL,
  `cajero_usuario_id` int(11) NOT NULL,
  `fecha_apertura` timestamp NULL DEFAULT current_timestamp(),
  `fecha_cierre` timestamp NULL DEFAULT NULL,
  `monto_inicial_usd` decimal(15,2) NOT NULL,
  `total_ventas_sistema_usd` decimal(15,2) DEFAULT 0.00,
  `total_ventas_sistema_ves` decimal(15,2) DEFAULT 0.00,
  `dinero_real_en_caja_usd` decimal(15,2) DEFAULT NULL,
  `dinero_real_en_caja_ves` decimal(15,2) DEFAULT NULL,
  `diferencia_usd` decimal(15,2) DEFAULT NULL,
  `observaciones_cierre` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tasas_cambio`
--

CREATE TABLE `tasas_cambio` (
  `id` int(11) NOT NULL,
  `codigo_moneda` varchar(10) DEFAULT 'USD',
  `moneda_base` varchar(10) DEFAULT 'VES',
  `valor_tasa` decimal(15,4) NOT NULL,
  `fuente` enum('API','MANUAL') DEFAULT 'API',
  `usuario_editor_id` int(11) DEFAULT NULL,
  `creado_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `rol_id` int(11) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `documento_identidad` varchar(20) DEFAULT NULL,
  `tipo_cliente` enum('natural','juridico','finca_productor') DEFAULT 'natural',
  `activo` tinyint(1) DEFAULT 1,
  `creado_at` timestamp NULL DEFAULT current_timestamp(),
  `actualizado_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `view_inventario_valorado`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `view_inventario_valorado` (
`id` int(11)
,`nombre` varchar(255)
,`sku` varchar(100)
,`stock_total` decimal(12,3)
,`costo_promedio_usd` decimal(15,4)
,`valor_total_usd` decimal(27,7)
,`precio_venta_usd` decimal(15,2)
,`valor_venta_total_usd` decimal(27,5)
,`categoria` varchar(100)
,`marca` varchar(100)
,`estado_stock` varchar(7)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `view_productos_mas_vendidos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `view_productos_mas_vendidos` (
`id` int(11)
,`nombre` varchar(255)
,`sku` varchar(100)
,`categoria` varchar(100)
,`veces_vendido` bigint(21)
,`cantidad_total_vendida` decimal(34,3)
,`unidad_medida` enum('unidad','kg','g','mg','litro','ml','galon','saco','bulto','paquete','metro','rollo','dosis')
,`monto_total_usd` decimal(49,5)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `view_productos_proximos_vencer`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `view_productos_proximos_vencer` (
`lote_id` int(11)
,`producto` varchar(255)
,`numero_lote` varchar(50)
,`fecha_vencimiento` date
,`dias_para_vencer` int(8)
,`cantidad_restante` decimal(12,3)
,`unidad_medida` enum('unidad','kg','g','mg','litro','ml','galon','saco','bulto','paquete','metro','rollo','dosis')
,`estado_vencimiento` varchar(16)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `view_ventas_diarias_detalladas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `view_ventas_diarias_detalladas` (
`fecha` date
,`total_pedidos` bigint(21)
,`total_ventas_usd` decimal(37,2)
,`total_ventas_ves` decimal(37,2)
,`clientes_unicos` bigint(21)
,`ticket_promedio_usd` decimal(19,6)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `zonas_delivery`
--

CREATE TABLE `zonas_delivery` (
  `id` int(11) NOT NULL,
  `nombre_zona` varchar(100) DEFAULT NULL,
  `precio_delivery_usd` decimal(10,2) NOT NULL,
  `tiempo_estimado_minutos` int(11) DEFAULT NULL,
  `requiere_vehiculo_carga` tinyint(1) DEFAULT 0,
  `activa` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `auditoria_logs`
--
ALTER TABLE `auditoria_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cajas_fisicas`
--
ALTER TABLE `cajas_fisicas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_padre_id` (`categoria_padre_id`);

--
-- Indices de la tabla `configuracion_api`
--
ALTER TABLE `configuracion_api`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre_api` (`nombre_api`);

--
-- Indices de la tabla `configuracion_tienda`
--
ALTER TABLE `configuracion_tienda`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cuentas_bancarias`
--
ALTER TABLE `cuentas_bancarias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cupones`
--
ALTER TABLE `cupones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `devoluciones_detalle`
--
ALTER TABLE `devoluciones_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_detalle_id` (`pedido_detalle_id`),
  ADD KEY `inventario_lote_id` (`inventario_lote_id`),
  ADD KEY `usuario_recibe_id` (`usuario_recibe_id`),
  ADD KEY `usuario_procesa_id` (`usuario_procesa_id`),
  ADD KEY `idx_devoluciones_pedido` (`pedido_id`);

--
-- Indices de la tabla `direcciones_usuarios`
--
ALTER TABLE `direcciones_usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `entrega_seguimiento`
--
ALTER TABLE `entrega_seguimiento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `repartidor_id` (`repartidor_id`);

--
-- Indices de la tabla `estadisticas_ventas_diarias`
--
ALTER TABLE `estadisticas_ventas_diarias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fecha_reporte` (`fecha_reporte`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_factura` (`numero_factura`),
  ADD UNIQUE KEY `pedido_id` (`pedido_id`);

--
-- Indices de la tabla `factura_ajustes`
--
ALTER TABLE `factura_ajustes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `favoritos`
--
ALTER TABLE `favoritos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`,`producto_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `historico_precios_productos`
--
ALTER TABLE `historico_precios_productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_editor_id` (`usuario_editor_id`),
  ADD KEY `idx_historico_precios_producto` (`producto_id`),
  ADD KEY `idx_historico_precios_fecha` (`creado_at`);

--
-- Indices de la tabla `horarios_especiales`
--
ALTER TABLE `horarios_especiales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fecha` (`fecha`);

--
-- Indices de la tabla `horarios_semanales`
--
ALTER TABLE `horarios_semanales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dia_semana` (`dia_semana`);

--
-- Indices de la tabla `inventario_lotes`
--
ALTER TABLE `inventario_lotes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proveedor_id` (`proveedor_id`),
  ADD KEY `idx_inventario_lotes_producto` (`producto_id`),
  ADD KEY `idx_inventario_lotes_vencimiento` (`fecha_vencimiento`);

--
-- Indices de la tabla `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `movimientos_caja`
--
ALTER TABLE `movimientos_caja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sesion_caja_id` (`sesion_caja_id`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `verificado_por_usuario_id` (`verificado_por_usuario_id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `email` (`email`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sesion_caja_id` (`sesion_caja_id`),
  ADD KEY `zona_delivery_id` (`zona_delivery_id`),
  ADD KEY `tasa_cambio_id` (`tasa_cambio_id`),
  ADD KEY `usuario_autorizo_devolucion_id` (`usuario_autorizo_devolucion_id`),
  ADD KEY `idx_pedidos_usuario` (`usuario_id`),
  ADD KEY `idx_pedidos_estado` (`estado`);

--
-- Indices de la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pedido_detalles_pedido` (`pedido_id`),
  ADD KEY `idx_pedido_detalles_producto` (`producto_id`),
  ADD KEY `idx_pedido_detalles_lote` (`inventario_lote_id`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `permisos_extra_usuario`
--
ALTER TABLE `permisos_extra_usuario`
  ADD PRIMARY KEY (`usuario_id`,`permiso_id`),
  ADD KEY `permiso_id` (`permiso_id`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD UNIQUE KEY `codigo_barras` (`codigo_barras`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `marca_id` (`marca_id`),
  ADD KEY `proveedor_defecto_id` (`proveedor_defecto_id`);

--
-- Indices de la tabla `productos_compuestos`
--
ALTER TABLE `productos_compuestos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_padre_id` (`producto_padre_id`),
  ADD KEY `producto_hijo_id` (`producto_hijo_id`);

--
-- Indices de la tabla `producto_imagenes`
--
ALTER TABLE `producto_imagenes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rif` (`rif`);

--
-- Indices de la tabla `recetas_veterinarias`
--
ALTER TABLE `recetas_veterinarias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_revisa_id` (`usuario_revisa_id`),
  ADD KEY `idx_recetas_pedido` (`pedido_id`),
  ADD KEY `idx_recetas_cliente` (`cliente_usuario_id`);

--
-- Indices de la tabla `receta_productos`
--
ALTER TABLE `receta_productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receta_id` (`receta_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `repartidores`
--
ALTER TABLE `repartidores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  ADD PRIMARY KEY (`rol_id`,`permiso_id`),
  ADD KEY `permiso_id` (`permiso_id`);

--
-- Indices de la tabla `sesiones_caja`
--
ALTER TABLE `sesiones_caja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `caja_id` (`caja_id`),
  ADD KEY `cajero_usuario_id` (`cajero_usuario_id`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `tasas_cambio`
--
ALTER TABLE `tasas_cambio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_editor_id` (`usuario_editor_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `rol_id` (`rol_id`);

--
-- Indices de la tabla `zonas_delivery`
--
ALTER TABLE `zonas_delivery`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auditoria_logs`
--
ALTER TABLE `auditoria_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cajas_fisicas`
--
ALTER TABLE `cajas_fisicas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `carrito`
--
ALTER TABLE `carrito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configuracion_api`
--
ALTER TABLE `configuracion_api`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configuracion_tienda`
--
ALTER TABLE `configuracion_tienda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cuentas_bancarias`
--
ALTER TABLE `cuentas_bancarias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cupones`
--
ALTER TABLE `cupones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `devoluciones_detalle`
--
ALTER TABLE `devoluciones_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `direcciones_usuarios`
--
ALTER TABLE `direcciones_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `entrega_seguimiento`
--
ALTER TABLE `entrega_seguimiento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estadisticas_ventas_diarias`
--
ALTER TABLE `estadisticas_ventas_diarias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `factura_ajustes`
--
ALTER TABLE `factura_ajustes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `favoritos`
--
ALTER TABLE `favoritos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historico_precios_productos`
--
ALTER TABLE `historico_precios_productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horarios_especiales`
--
ALTER TABLE `horarios_especiales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horarios_semanales`
--
ALTER TABLE `horarios_semanales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventario_lotes`
--
ALTER TABLE `inventario_lotes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `marcas`
--
ALTER TABLE `marcas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `movimientos_caja`
--
ALTER TABLE `movimientos_caja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos_compuestos`
--
ALTER TABLE `productos_compuestos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `producto_imagenes`
--
ALTER TABLE `producto_imagenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `recetas_veterinarias`
--
ALTER TABLE `recetas_veterinarias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `receta_productos`
--
ALTER TABLE `receta_productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `repartidores`
--
ALTER TABLE `repartidores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sesiones_caja`
--
ALTER TABLE `sesiones_caja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tasas_cambio`
--
ALTER TABLE `tasas_cambio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `zonas_delivery`
--
ALTER TABLE `zonas_delivery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Estructura para la vista `view_inventario_valorado`
--
DROP TABLE IF EXISTS `view_inventario_valorado`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_inventario_valorado`  AS SELECT `p`.`id` AS `id`, `p`.`nombre` AS `nombre`, `p`.`sku` AS `sku`, `p`.`stock_total` AS `stock_total`, `p`.`costo_promedio_usd` AS `costo_promedio_usd`, `p`.`stock_total`* `p`.`costo_promedio_usd` AS `valor_total_usd`, `p`.`precio_venta_usd` AS `precio_venta_usd`, `p`.`stock_total`* `p`.`precio_venta_usd` AS `valor_venta_total_usd`, `c`.`nombre` AS `categoria`, `m`.`nombre` AS `marca`, CASE WHEN `p`.`stock_total` <= `p`.`stock_minimo_alerta` THEN 'CRÍTICO' WHEN `p`.`stock_total` <= `p`.`stock_minimo_alerta` * 2 THEN 'BAJO' ELSE 'NORMAL' END AS `estado_stock` FROM ((`productos` `p` left join `categorias` `c` on(`p`.`categoria_id` = `c`.`id`)) left join `marcas` `m` on(`p`.`marca_id` = `m`.`id`)) WHERE `p`.`eliminado_at` is null ;

-- --------------------------------------------------------

--
-- Estructura para la vista `view_productos_mas_vendidos`
--
DROP TABLE IF EXISTS `view_productos_mas_vendidos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_productos_mas_vendidos`  AS SELECT `p`.`id` AS `id`, `p`.`nombre` AS `nombre`, `p`.`sku` AS `sku`, `c`.`nombre` AS `categoria`, count(`pd`.`id`) AS `veces_vendido`, sum(`pd`.`cantidad_solicitada`) AS `cantidad_total_vendida`, `p`.`unidad_medida` AS `unidad_medida`, sum(`pd`.`cantidad_solicitada` * `pd`.`precio_historico_usd`) AS `monto_total_usd` FROM (((`pedido_detalles` `pd` join `productos` `p` on(`pd`.`producto_id` = `p`.`id`)) join `pedidos` `pe` on(`pd`.`pedido_id` = `pe`.`id`)) join `categorias` `c` on(`p`.`categoria_id` = `c`.`id`)) WHERE `pe`.`estado` in ('pagado','entregado','completado_caja') GROUP BY `p`.`id`, `p`.`nombre`, `p`.`sku`, `c`.`nombre`, `p`.`unidad_medida` ORDER BY sum(`pd`.`cantidad_solicitada`) DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `view_productos_proximos_vencer`
--
DROP TABLE IF EXISTS `view_productos_proximos_vencer`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_productos_proximos_vencer`  AS SELECT `il`.`id` AS `lote_id`, `p`.`nombre` AS `producto`, `il`.`numero_lote` AS `numero_lote`, `il`.`fecha_vencimiento` AS `fecha_vencimiento`, to_days(`il`.`fecha_vencimiento`) - to_days(curdate()) AS `dias_para_vencer`, `il`.`cantidad_restante` AS `cantidad_restante`, `p`.`unidad_medida` AS `unidad_medida`, CASE WHEN to_days(`il`.`fecha_vencimiento`) - to_days(curdate()) <= 30 THEN 'PRÓXIMO A VENCER' WHEN to_days(`il`.`fecha_vencimiento`) - to_days(curdate()) <= 90 THEN 'ALERTA MEDIA' ELSE 'NORMAL' END AS `estado_vencimiento` FROM (`inventario_lotes` `il` join `productos` `p` on(`il`.`producto_id` = `p`.`id`)) WHERE `il`.`activo` = 1 AND `il`.`cantidad_restante` > 0 AND `il`.`fecha_vencimiento` >= curdate() ORDER BY `il`.`fecha_vencimiento` ASC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `view_ventas_diarias_detalladas`
--
DROP TABLE IF EXISTS `view_ventas_diarias_detalladas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_ventas_diarias_detalladas`  AS SELECT cast(`p`.`creado_at` as date) AS `fecha`, count(`p`.`id`) AS `total_pedidos`, sum(`p`.`total_usd`) AS `total_ventas_usd`, sum(`p`.`total_ves_calculado`) AS `total_ventas_ves`, count(distinct `p`.`usuario_id`) AS `clientes_unicos`, avg(`p`.`total_usd`) AS `ticket_promedio_usd` FROM `pedidos` AS `p` WHERE `p`.`estado` in ('pagado','entregado','completado_caja') GROUP BY cast(`p`.`creado_at` as date) ORDER BY cast(`p`.`creado_at` as date) DESC ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `carrito_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD CONSTRAINT `categorias_ibfk_1` FOREIGN KEY (`categoria_padre_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `devoluciones_detalle`
--
ALTER TABLE `devoluciones_detalle`
  ADD CONSTRAINT `devoluciones_detalle_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  ADD CONSTRAINT `devoluciones_detalle_ibfk_2` FOREIGN KEY (`pedido_detalle_id`) REFERENCES `pedido_detalles` (`id`),
  ADD CONSTRAINT `devoluciones_detalle_ibfk_3` FOREIGN KEY (`inventario_lote_id`) REFERENCES `inventario_lotes` (`id`),
  ADD CONSTRAINT `devoluciones_detalle_ibfk_4` FOREIGN KEY (`usuario_recibe_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `devoluciones_detalle_ibfk_5` FOREIGN KEY (`usuario_procesa_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `direcciones_usuarios`
--
ALTER TABLE `direcciones_usuarios`
  ADD CONSTRAINT `direcciones_usuarios_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `entrega_seguimiento`
--
ALTER TABLE `entrega_seguimiento`
  ADD CONSTRAINT `entrega_seguimiento_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  ADD CONSTRAINT `entrega_seguimiento_ibfk_2` FOREIGN KEY (`repartidor_id`) REFERENCES `repartidores` (`id`);

--
-- Filtros para la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`);

--
-- Filtros para la tabla `favoritos`
--
ALTER TABLE `favoritos`
  ADD CONSTRAINT `favoritos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `favoritos_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `historico_precios_productos`
--
ALTER TABLE `historico_precios_productos`
  ADD CONSTRAINT `historico_precios_productos_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `historico_precios_productos_ibfk_2` FOREIGN KEY (`usuario_editor_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `inventario_lotes`
--
ALTER TABLE `inventario_lotes`
  ADD CONSTRAINT `inventario_lotes_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  ADD CONSTRAINT `inventario_lotes_ibfk_2` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`);

--
-- Filtros para la tabla `movimientos_caja`
--
ALTER TABLE `movimientos_caja`
  ADD CONSTRAINT `movimientos_caja_ibfk_1` FOREIGN KEY (`sesion_caja_id`) REFERENCES `sesiones_caja` (`id`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  ADD CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`verificado_por_usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`sesion_caja_id`) REFERENCES `sesiones_caja` (`id`),
  ADD CONSTRAINT `pedidos_ibfk_3` FOREIGN KEY (`zona_delivery_id`) REFERENCES `zonas_delivery` (`id`),
  ADD CONSTRAINT `pedidos_ibfk_4` FOREIGN KEY (`tasa_cambio_id`) REFERENCES `tasas_cambio` (`id`),
  ADD CONSTRAINT `pedidos_ibfk_5` FOREIGN KEY (`usuario_autorizo_devolucion_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  ADD CONSTRAINT `pedido_detalles_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedido_detalles_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  ADD CONSTRAINT `pedido_detalles_ibfk_3` FOREIGN KEY (`inventario_lote_id`) REFERENCES `inventario_lotes` (`id`);

--
-- Filtros para la tabla `permisos_extra_usuario`
--
ALTER TABLE `permisos_extra_usuario`
  ADD CONSTRAINT `permisos_extra_usuario_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permisos_extra_usuario_ibfk_2` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
  ADD CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`marca_id`) REFERENCES `marcas` (`id`),
  ADD CONSTRAINT `productos_ibfk_3` FOREIGN KEY (`proveedor_defecto_id`) REFERENCES `proveedores` (`id`);

--
-- Filtros para la tabla `productos_compuestos`
--
ALTER TABLE `productos_compuestos`
  ADD CONSTRAINT `productos_compuestos_ibfk_1` FOREIGN KEY (`producto_padre_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `productos_compuestos_ibfk_2` FOREIGN KEY (`producto_hijo_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `producto_imagenes`
--
ALTER TABLE `producto_imagenes`
  ADD CONSTRAINT `producto_imagenes_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `recetas_veterinarias`
--
ALTER TABLE `recetas_veterinarias`
  ADD CONSTRAINT `recetas_veterinarias_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  ADD CONSTRAINT `recetas_veterinarias_ibfk_2` FOREIGN KEY (`cliente_usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `recetas_veterinarias_ibfk_3` FOREIGN KEY (`usuario_revisa_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `receta_productos`
--
ALTER TABLE `receta_productos`
  ADD CONSTRAINT `receta_productos_ibfk_1` FOREIGN KEY (`receta_id`) REFERENCES `recetas_veterinarias` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `receta_productos_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `repartidores`
--
ALTER TABLE `repartidores`
  ADD CONSTRAINT `repartidores_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  ADD CONSTRAINT `rol_permisos_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rol_permisos_ibfk_2` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sesiones_caja`
--
ALTER TABLE `sesiones_caja`
  ADD CONSTRAINT `sesiones_caja_ibfk_1` FOREIGN KEY (`caja_id`) REFERENCES `cajas_fisicas` (`id`),
  ADD CONSTRAINT `sesiones_caja_ibfk_2` FOREIGN KEY (`cajero_usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `tasas_cambio`
--
ALTER TABLE `tasas_cambio`
  ADD CONSTRAINT `tasas_cambio_ibfk_1` FOREIGN KEY (`usuario_editor_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;