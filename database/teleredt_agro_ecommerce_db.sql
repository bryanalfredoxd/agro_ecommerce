-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: agro_ecommerce_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `auditoria_logs`
--

DROP TABLE IF EXISTS `auditoria_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auditoria_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `evento` varchar(50) DEFAULT NULL,
  `tabla_afectada` varchar(50) DEFAULT NULL,
  `registro_id` int(11) DEFAULT NULL,
  `valores_anteriores` longtext DEFAULT NULL CHECK (json_valid(`valores_anteriores`)),
  `valores_nuevos` longtext DEFAULT NULL CHECK (json_valid(`valores_nuevos`)),
  `direccion_ip` varchar(45) DEFAULT NULL,
  `fecha` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auditoria_logs`
--

LOCK TABLES `auditoria_logs` WRITE;
/*!40000 ALTER TABLE `auditoria_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `auditoria_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cajas_fisicas`
--

DROP TABLE IF EXISTS `cajas_fisicas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cajas_fisicas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `activa` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cajas_fisicas`
--

LOCK TABLES `cajas_fisicas` WRITE;
/*!40000 ALTER TABLE `cajas_fisicas` DISABLE KEYS */;
INSERT INTO `cajas_fisicas` VALUES (1,'Caja Principal',1);
/*!40000 ALTER TABLE `cajas_fisicas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carrito`
--

DROP TABLE IF EXISTS `carrito`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carrito` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad` decimal(12,3) NOT NULL DEFAULT 1.000,
  `observaciones` varchar(255) DEFAULT NULL,
  `actualizado_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `carrito_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carrito`
--

LOCK TABLES `carrito` WRITE;
/*!40000 ALTER TABLE `carrito` DISABLE KEYS */;
INSERT INTO `carrito` VALUES (14,2,7,1.000,NULL,'2026-02-25 11:59:40');
/*!40000 ALTER TABLE `carrito` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  `categoria_padre_id` int(11) DEFAULT NULL,
  `eliminado_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categoria_padre_id` (`categoria_padre_id`),
  CONSTRAINT `categorias_ibfk_1` FOREIGN KEY (`categoria_padre_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Veterinaria','categorias/lRozx04x0SyO67En6HHfU3Inuyk5kj648k13XnLZ.jpg',NULL,NULL),(2,'Nutrición Animal','/img/upload/categorias/carne2.jpg',NULL,NULL),(3,'Semillas','/img/upload/categorias/carne3.jpg',NULL,NULL),(4,'Fertilizantes','img/upload/categorias/1772062952_699f88e87c8d9.png',NULL,NULL),(5,'Ferretería Agrícola','img/upload/categorias/1772060114_699f7dd297b30.jpg',NULL,NULL),(6,'Equipos y Maquinaria',NULL,NULL,NULL),(7,'Antibióticos','categorias/itZQL8CiMCW8WTGs3MRMMgQSkHJT87K6wa6Tc6nh.png',NULL,NULL),(8,'Vacunas',NULL,1,NULL),(9,'Desparasitantes',NULL,1,NULL),(10,'Vitaminas y Suplementos',NULL,1,NULL),(11,'Productos de Higiene',NULL,1,NULL),(12,'Alimento para Bovinos',NULL,2,NULL),(13,'Alimento para Porcinos',NULL,2,NULL),(14,'Alimento para Aves',NULL,2,NULL),(15,'Concentrados',NULL,2,NULL),(16,'Sales Mineralizadas',NULL,2,NULL),(17,'Herramientas Manuales',NULL,5,NULL),(19,'Alambre y Cercas',NULL,5,NULL),(20,'Guayas y Mecates',NULL,5,NULL),(22,'xcvxcvxcvx','img/upload/CARPETAPANTALLA/1772059293_699f7a9d8eed8.jpg',NULL,NULL),(23,'123qweq','img/upload/CARPETAPANTALLA/1772059484_699f7b5c777e7.jpg',NULL,NULL),(24,'Fertilizant','img/upload/categorias/1772060114_699f7dd297b30.jpg',NULL,NULL),(25,'Prueba de imagen','img/upload/categorias/1772060658_699f7ff21d9b9.png',NULL,NULL);
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuracion_api`
--

DROP TABLE IF EXISTS `configuracion_api`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configuracion_api` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_api` varchar(100) NOT NULL,
  `url_base` varchar(255) DEFAULT NULL,
  `api_key` varchar(255) DEFAULT NULL,
  `api_secret` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `actualizado_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre_api` (`nombre_api`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuracion_api`
--

LOCK TABLES `configuracion_api` WRITE;
/*!40000 ALTER TABLE `configuracion_api` DISABLE KEYS */;
INSERT INTO `configuracion_api` VALUES (1,'BCV','https://api.bcv.org.ve','demo_key','demo_secret',1,'2026-01-31 21:43:55'),(2,'Monitor Dolar','https://monitordolar.com/api','demo_key','demo_secret',1,'2026-01-31 21:43:55');
/*!40000 ALTER TABLE `configuracion_api` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuracion_tienda`
--

DROP TABLE IF EXISTS `configuracion_tienda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configuracion_tienda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_empresa` varchar(100) DEFAULT NULL,
  `iva_porcentaje` decimal(5,2) DEFAULT 16.00,
  `modo_operativo` enum('automatico','manual_abierto','manual_cerrado') DEFAULT 'automatico',
  `mensaje_cierre_emergencia` mediumtext DEFAULT NULL,
  `ultimo_editor_id` int(11) DEFAULT NULL,
  `actualizado_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuracion_tienda`
--

LOCK TABLES `configuracion_tienda` WRITE;
/*!40000 ALTER TABLE `configuracion_tienda` DISABLE KEYS */;
INSERT INTO `configuracion_tienda` VALUES (1,'Agrosuministros Rodríguez',16.00,'automatico',NULL,NULL,'2026-10-08 21:14:35');
/*!40000 ALTER TABLE `configuracion_tienda` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cuentas_bancarias`
--

DROP TABLE IF EXISTS `cuentas_bancarias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cuentas_bancarias` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuentas_bancarias`
--

LOCK TABLES `cuentas_bancarias` WRITE;
/*!40000 ALTER TABLE `cuentas_bancarias` DISABLE KEYS */;
INSERT INTO `cuentas_bancarias` VALUES (1,'Agropecuaria Vzla','Banco de Venezuela',NULL,'0414-7654321','J-12345678-9',NULL,'pago_movil','Por favor reporta tu pago inmediatamente.',1,'2026-02-04 16:06:19','2026-04-27 15:59:07'),(2,'Agropecuaria Vzla','Banesco','01010101010101010101',NULL,'J-12345678-9',NULL,'transferencia','Envía el comprobante al WhatsApp.',1,'2026-02-04 16:06:19','2026-03-09 12:45:11'),(3,'Agropecuaria Vzla','Zelle',NULL,NULL,NULL,'pagos@agropecuaria.com','zelle','Coloca tu número de pedido en la nota del Zelle.',1,'2026-02-04 16:06:19','2026-02-04 16:06:19'),(4,NULL,NULL,NULL,NULL,NULL,NULL,'efectivo_usd','Paga directamente en caja o al repartidor.',1,'2026-02-04 16:06:19','2026-02-04 16:06:19'),(5,NULL,NULL,NULL,NULL,NULL,NULL,'punto_venta','Solo disponible para retiro en tienda.',1,'2026-02-04 16:06:19','2026-02-04 16:06:19'),(6,'Admin','Binance',NULL,NULL,'ID: 12345678','crypto@agro.com','binance','Aceptamos USDT a través de Binance Pay.',1,'2026-02-04 16:06:19','2026-02-04 16:06:19');
/*!40000 ALTER TABLE `cuentas_bancarias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cupones`
--

DROP TABLE IF EXISTS `cupones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cupones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) NOT NULL,
  `tipo_descuento` enum('porcentaje','monto_fijo') NOT NULL,
  `valor_descuento` decimal(15,2) NOT NULL,
  `compra_minima_usd` decimal(15,2) DEFAULT 0.00,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `limite_uso_total` int(11) DEFAULT 1,
  `veces_usado` int(11) DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cupones`
--

LOCK TABLES `cupones` WRITE;
/*!40000 ALTER TABLE `cupones` DISABLE KEYS */;
/*!40000 ALTER TABLE `cupones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `devoluciones_detalle`
--

DROP TABLE IF EXISTS `devoluciones_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devoluciones_detalle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) NOT NULL,
  `pedido_detalle_id` int(11) NOT NULL,
  `inventario_lote_id` int(11) DEFAULT NULL,
  `cantidad_devuelta` decimal(12,3) NOT NULL,
  `motivo_devolucion` enum('producto_danado','no_corresponde','vencido','sobrante','error_despacho','cliente_arrepentido') NOT NULL,
  `estado_producto` enum('recibido','en_verificacion','reincorporado','descarte','devuelto_proveedor') DEFAULT 'recibido',
  `costo_reposicion_usd` decimal(15,4) DEFAULT NULL,
  `observaciones` mediumtext DEFAULT NULL,
  `usuario_recibe_id` int(11) DEFAULT NULL,
  `fecha_recepcion` timestamp NULL DEFAULT NULL,
  `usuario_procesa_id` int(11) DEFAULT NULL,
  `fecha_procesamiento` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pedido_detalle_id` (`pedido_detalle_id`),
  KEY `inventario_lote_id` (`inventario_lote_id`),
  KEY `usuario_recibe_id` (`usuario_recibe_id`),
  KEY `usuario_procesa_id` (`usuario_procesa_id`),
  KEY `idx_devoluciones_pedido` (`pedido_id`),
  CONSTRAINT `devoluciones_detalle_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  CONSTRAINT `devoluciones_detalle_ibfk_2` FOREIGN KEY (`pedido_detalle_id`) REFERENCES `pedido_detalles` (`id`),
  CONSTRAINT `devoluciones_detalle_ibfk_3` FOREIGN KEY (`inventario_lote_id`) REFERENCES `inventario_lotes` (`id`),
  CONSTRAINT `devoluciones_detalle_ibfk_4` FOREIGN KEY (`usuario_recibe_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `devoluciones_detalle_ibfk_5` FOREIGN KEY (`usuario_procesa_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devoluciones_detalle`
--

LOCK TABLES `devoluciones_detalle` WRITE;
/*!40000 ALTER TABLE `devoluciones_detalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `devoluciones_detalle` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER tr_reintegrar_desde_devolucion_detalle
AFTER INSERT ON devoluciones_detalle
FOR EACH ROW
BEGIN
    DECLARE producto_id_val INT;
    
    SELECT producto_id INTO producto_id_val 
    FROM pedido_detalles 
    WHERE id = NEW.pedido_detalle_id;
    
    UPDATE productos 
    SET stock_total = stock_total + NEW.cantidad_devuelta
    WHERE id = producto_id_val;
    
    IF NEW.inventario_lote_id IS NOT NULL THEN
        UPDATE inventario_lotes 
        SET cantidad_restante = cantidad_restante + NEW.cantidad_devuelta
        WHERE id = NEW.inventario_lote_id;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `direcciones_usuarios`
--

DROP TABLE IF EXISTS `direcciones_usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `direcciones_usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `zona_delivery_id` int(11) DEFAULT NULL,
  `alias` varchar(50) DEFAULT NULL,
  `direccion_texto` mediumtext NOT NULL,
  `referencia_punto` mediumtext DEFAULT NULL,
  `geo_latitud` decimal(10,8) DEFAULT NULL,
  `geo_longitud` decimal(11,8) DEFAULT NULL,
  `es_principal` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `fk_direcciones_zona` (`zona_delivery_id`),
  CONSTRAINT `direcciones_usuarios_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_direcciones_zona` FOREIGN KEY (`zona_delivery_id`) REFERENCES `zonas_delivery` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `direcciones_usuarios`
--

LOCK TABLES `direcciones_usuarios` WRITE;
/*!40000 ALTER TABLE `direcciones_usuarios` DISABLE KEYS */;
INSERT INTO `direcciones_usuarios` VALUES (1,1,1,'Casa','Calle 8 con carrera 7','Plaza Bolivar',8.75739418,-70.41233955,1);
/*!40000 ALTER TABLE `direcciones_usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entrega_seguimiento`
--

DROP TABLE IF EXISTS `entrega_seguimiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entrega_seguimiento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) DEFAULT NULL,
  `repartidor_id` int(11) DEFAULT NULL,
  `hora_recogida` timestamp NULL DEFAULT NULL,
  `hora_entrega` timestamp NULL DEFAULT NULL,
  `estado_rastreo` varchar(100) DEFAULT NULL,
  `observaciones_entrega` mediumtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pedido_id` (`pedido_id`),
  KEY `repartidor_id` (`repartidor_id`),
  CONSTRAINT `entrega_seguimiento_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  CONSTRAINT `entrega_seguimiento_ibfk_2` FOREIGN KEY (`repartidor_id`) REFERENCES `repartidores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entrega_seguimiento`
--

LOCK TABLES `entrega_seguimiento` WRITE;
/*!40000 ALTER TABLE `entrega_seguimiento` DISABLE KEYS */;
/*!40000 ALTER TABLE `entrega_seguimiento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estadisticas_ventas_diarias`
--

DROP TABLE IF EXISTS `estadisticas_ventas_diarias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estadisticas_ventas_diarias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_reporte` date NOT NULL,
  `total_pedidos` int(11) DEFAULT 0,
  `total_ingresos_usd` decimal(15,2) DEFAULT 0.00,
  `total_ingresos_ves` decimal(15,2) DEFAULT 0.00,
  `unidades_vendidas` decimal(15,3) DEFAULT 0.000,
  `creado_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `fecha_reporte` (`fecha_reporte`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estadisticas_ventas_diarias`
--

LOCK TABLES `estadisticas_ventas_diarias` WRITE;
/*!40000 ALTER TABLE `estadisticas_ventas_diarias` DISABLE KEYS */;
INSERT INTO `estadisticas_ventas_diarias` VALUES (1,'2026-02-25',1,19.98,8139.42,0.000,'2026-02-25 12:51:12'),(2,'2026-03-08',7,395.50,161118.24,7.500,'2026-03-08 20:31:50'),(9,'2026-03-09',10,389.18,158543.61,20.500,'2026-03-09 12:12:03'),(20,'2026-10-08',2,914.60,400782.30,51.000,'2026-10-08 21:49:28');
/*!40000 ALTER TABLE `estadisticas_ventas_diarias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `factura_ajustes`
--

DROP TABLE IF EXISTS `factura_ajustes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `factura_ajustes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serie` varchar(10) NOT NULL,
  `proximo_numero` int(11) NOT NULL DEFAULT 1,
  `porcentaje_iva` decimal(5,2) DEFAULT 16.00,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factura_ajustes`
--

LOCK TABLES `factura_ajustes` WRITE;
/*!40000 ALTER TABLE `factura_ajustes` DISABLE KEYS */;
INSERT INTO `factura_ajustes` VALUES (1,'F',9,16.00,1);
/*!40000 ALTER TABLE `factura_ajustes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facturas`
--

DROP TABLE IF EXISTS `facturas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facturas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) DEFAULT NULL,
  `serie_usada` varchar(10) DEFAULT NULL,
  `numero_factura` varchar(20) NOT NULL,
  `cedula_rif_cliente` varchar(20) DEFAULT NULL,
  `nombre_razon_social` varchar(200) DEFAULT NULL,
  `direccion_fiscal` mediumtext DEFAULT NULL,
  `subtotal_usd` decimal(15,2) DEFAULT NULL,
  `impuesto_usd` decimal(15,2) DEFAULT NULL,
  `total_usd` decimal(15,2) DEFAULT NULL,
  `valor_tasa_bcv` decimal(15,4) DEFAULT NULL,
  `total_ves` decimal(15,2) DEFAULT NULL,
  `estado` enum('emitida','anulada','reembolsada') DEFAULT 'emitida',
  `fecha_emision` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_factura` (`numero_factura`),
  UNIQUE KEY `pedido_id` (`pedido_id`),
  CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facturas`
--

LOCK TABLES `facturas` WRITE;
/*!40000 ALTER TABLE `facturas` DISABLE KEYS */;
INSERT INTO `facturas` VALUES (1,15,'F','F-0000001','V-29923421','Bryan Moreno','Retiro en Tienda',10.00,1.60,11.60,407.3784,4725.59,'emitida','2026-03-09 13:04:15'),(2,16,'F','F-0000002','V-00000000','Consumidor Final','Compra en Tienda Física',15.00,2.40,17.40,407.3786,7088.39,'emitida','2026-03-09 13:09:42'),(3,17,'F','F-0000003','V-29923421','Bryan Moreno','Retiro en Tienda',35.00,5.60,40.60,407.3786,16539.57,'emitida','2026-03-09 14:32:54'),(4,18,'F','F-0000004','V-29923421','Bryan Moreno','Retiro en Tienda',25.00,4.00,29.00,407.3786,11813.98,'emitida','2026-03-09 14:35:35'),(5,19,'F','F-0000005','V-29923421','Bryan Moreno','Retiro en Tienda',25.00,4.00,29.00,407.3786,11813.98,'emitida','2026-03-09 14:37:03'),(6,21,'F','F-0000006','V-29923421','Bryan Moreno','Retiro en Tienda',26.00,4.16,30.16,407.3786,12286.54,'emitida','2026-03-09 15:25:38'),(7,24,'F','F-0000008','V-29923421','Bryan Moreno','Calle 8 con carrera 7 (Ref: Plaza Bolivar)',10.00,3.60,13.60,438.2051,5959.59,'emitida','2026-10-08 21:50:23');
/*!40000 ALTER TABLE `facturas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `favoritos`
--

DROP TABLE IF EXISTS `favoritos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `favoritos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario_id` (`usuario_id`,`producto_id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `favoritos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `favoritos_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `favoritos`
--

LOCK TABLES `favoritos` WRITE;
/*!40000 ALTER TABLE `favoritos` DISABLE KEYS */;
/*!40000 ALTER TABLE `favoritos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historico_precios_productos`
--

DROP TABLE IF EXISTS `historico_precios_productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `historico_precios_productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `producto_id` int(11) NOT NULL,
  `precio_anterior_usd` decimal(15,2) NOT NULL,
  `precio_nuevo_usd` decimal(15,2) NOT NULL,
  `motivo_cambio` varchar(255) DEFAULT NULL,
  `usuario_editor_id` int(11) DEFAULT NULL,
  `creado_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `usuario_editor_id` (`usuario_editor_id`),
  KEY `idx_historico_precios_producto` (`producto_id`),
  KEY `idx_historico_precios_fecha` (`creado_at`),
  CONSTRAINT `historico_precios_productos_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `historico_precios_productos_ibfk_2` FOREIGN KEY (`usuario_editor_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historico_precios_productos`
--

LOCK TABLES `historico_precios_productos` WRITE;
/*!40000 ALTER TABLE `historico_precios_productos` DISABLE KEYS */;
INSERT INTO `historico_precios_productos` VALUES (1,11,10.00,8.62,NULL,NULL,'2026-03-09 13:34:47'),(2,11,8.62,10.00,'Actualización regular desde catálogo',1,'2026-03-09 14:05:43');
/*!40000 ALTER TABLE `historico_precios_productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `horarios_especiales`
--

DROP TABLE IF EXISTS `horarios_especiales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `horarios_especiales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `motivo` varchar(100) DEFAULT NULL,
  `es_laborable` tinyint(1) DEFAULT 0,
  `hora_apertura` time DEFAULT NULL,
  `hora_cierre` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fecha` (`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horarios_especiales`
--

LOCK TABLES `horarios_especiales` WRITE;
/*!40000 ALTER TABLE `horarios_especiales` DISABLE KEYS */;
/*!40000 ALTER TABLE `horarios_especiales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `horarios_semanales`
--

DROP TABLE IF EXISTS `horarios_semanales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `horarios_semanales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dia_semana` int(11) NOT NULL,
  `es_laborable` tinyint(1) DEFAULT 1,
  `hora_apertura` time DEFAULT NULL,
  `hora_cierre` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dia_semana` (`dia_semana`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horarios_semanales`
--

LOCK TABLES `horarios_semanales` WRITE;
/*!40000 ALTER TABLE `horarios_semanales` DISABLE KEYS */;
INSERT INTO `horarios_semanales` VALUES (1,1,1,'08:00:00','10:00:00'),(2,2,1,'08:00:00','17:00:00'),(3,3,1,'08:00:00','17:00:00'),(4,4,1,'08:00:00','17:00:00'),(5,5,1,'08:00:00','17:00:00'),(6,6,1,'08:00:00','15:00:00'),(7,7,1,'08:00:00','18:00:00');
/*!40000 ALTER TABLE `horarios_semanales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventario_lotes`
--

DROP TABLE IF EXISTS `inventario_lotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventario_lotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `producto_id` int(11) NOT NULL,
  `proveedor_id` int(11) DEFAULT NULL,
  `numero_lote` varchar(50) DEFAULT NULL,
  `fecha_vencimiento` date NOT NULL,
  `cantidad_inicial` decimal(12,3) NOT NULL,
  `cantidad_restante` decimal(12,3) NOT NULL,
  `costo_unitario_usd` decimal(15,4) NOT NULL,
  `ubicacion_almacen` varchar(50) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `creado_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `proveedor_id` (`proveedor_id`),
  KEY `idx_inventario_lotes_producto` (`producto_id`),
  KEY `idx_inventario_lotes_vencimiento` (`fecha_vencimiento`),
  CONSTRAINT `inventario_lotes_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  CONSTRAINT `inventario_lotes_ibfk_2` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventario_lotes`
--

LOCK TABLES `inventario_lotes` WRITE;
/*!40000 ALTER TABLE `inventario_lotes` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventario_lotes` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER tr_entrada_lote_costos
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `marcas`
--

DROP TABLE IF EXISTS `marcas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marcas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `pais_origen` varchar(100) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marcas`
--

LOCK TABLES `marcas` WRITE;
/*!40000 ALTER TABLE `marcas` DISABLE KEYS */;
INSERT INTO `marcas` VALUES (1,'Bayer','Alemania',1,'2026-02-25 10:41:53','2026-02-25 10:41:53'),(2,'Purina','Estados Unidos',1,'2026-02-25 10:41:53','2026-02-25 10:41:53'),(3,'Zoetis','Estados Unidos',1,'2026-02-25 10:41:53','2026-02-25 10:41:53'),(4,'MSD Animal Health','Estados Unidos',1,'2026-02-25 10:41:53','2026-02-25 10:41:53'),(5,'Elanco','Estados Unidos',1,'2026-02-25 10:41:53','2026-02-25 10:41:53'),(6,'Pioneer','Estados Unidos',1,'2026-02-25 10:41:53','2026-02-25 10:41:53'),(7,'Monsanto','Estados Unidos',1,'2026-02-25 10:41:53','2026-03-08 19:15:23'),(8,'Syngenta','Suiza',1,'2026-02-25 10:41:53','2026-02-25 10:41:53'),(9,'BASF','Alemania',1,'2026-02-25 10:41:53','2026-02-25 10:45:02'),(10,'Genética Avícola','Venezuela',1,'2026-02-25 10:41:53','2026-02-25 10:41:53');
/*!40000 ALTER TABLE `marcas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `movimientos_caja`
--

DROP TABLE IF EXISTS `movimientos_caja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movimientos_caja` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sesion_caja_id` int(11) NOT NULL,
  `tipo` enum('ingreso','egreso') NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `monto_usd` decimal(15,2) DEFAULT 0.00,
  `monto_ves` decimal(15,2) DEFAULT 0.00,
  `creado_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `sesion_caja_id` (`sesion_caja_id`),
  CONSTRAINT `movimientos_caja_ibfk_1` FOREIGN KEY (`sesion_caja_id`) REFERENCES `sesiones_caja` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movimientos_caja`
--

LOCK TABLES `movimientos_caja` WRITE;
/*!40000 ALTER TABLE `movimientos_caja` DISABLE KEYS */;
INSERT INTO `movimientos_caja` VALUES (1,1,'ingreso','Venta POS Pedido #6',0.00,0.00,'2026-03-08 21:38:12'),(2,1,'ingreso','Venta POS Pedido #7',17.40,7088.39,'2026-03-08 22:32:34'),(3,1,'ingreso','Venta POS Pedido #8',23.20,9451.18,'2026-03-08 23:06:02'),(4,1,'ingreso','Venta POS Pedido #10',17.40,7088.39,'2026-03-08 23:21:54'),(5,1,'ingreso','Venta POS Pedido #12',11.60,4725.59,'2026-03-09 12:12:03'),(6,1,'ingreso','Venta POS Pedido #13',17.40,7088.39,'2026-03-09 12:16:47'),(7,1,'ingreso','Venta POS Pedido #14',12.76,5198.15,'2026-03-09 12:19:05'),(8,1,'ingreso','Venta POS Pedido #16',17.40,7088.39,'2026-03-09 13:09:42');
/*!40000 ALTER TABLE `movimientos_caja` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagos`
--

DROP TABLE IF EXISTS `pagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pagos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) DEFAULT NULL,
  `metodo` enum('pago_movil','zelle','efectivo_usd','efectivo_bs','transferencia','punto_venta','binance','biopago') DEFAULT NULL,
  `referencia_bancaria` varchar(100) DEFAULT NULL,
  `monto_usd` decimal(15,2) DEFAULT NULL,
  `monto_ves` decimal(15,2) DEFAULT NULL,
  `captura_pago_url` varchar(255) DEFAULT NULL,
  `estado` enum('revision','aprobado','rechazado') DEFAULT 'revision',
  `verificado_por_usuario_id` int(11) DEFAULT NULL,
  `fecha_pago` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `pedido_id` (`pedido_id`),
  KEY `verificado_por_usuario_id` (`verificado_por_usuario_id`),
  CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`verificado_por_usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagos`
--

LOCK TABLES `pagos` WRITE;
/*!40000 ALTER TABLE `pagos` DISABLE KEYS */;
INSERT INTO `pagos` VALUES (1,1,'efectivo_usd',NULL,48.72,19847.49,NULL,'rechazado',1,'2026-02-25 03:43:17'),(2,2,'pago_movil','123123123',19.98,8139.42,'pagos/xd6mp2Z84wgc7EH3TT2yTC1HwjU8xb9fsoGof8ms.jpg','aprobado',1,'2026-02-25 04:21:40'),(3,3,'efectivo_usd',NULL,61.16,24915.28,NULL,'aprobado',1,'2026-02-25 15:45:50'),(4,4,'punto_venta',NULL,178.64,72774.11,NULL,'aprobado',1,'2026-02-26 03:19:21'),(5,5,'pago_movil','1231',189.66,77263.43,'img/upload/pagos/1772076674_699fbe8254c4c.jpg','aprobado',1,'2026-02-26 03:31:14'),(6,6,'pago_movil',NULL,10.00,4073.79,NULL,'aprobado',1,'2026-03-08 21:38:12'),(7,7,'pago_movil','9765674',17.40,7088.39,NULL,'aprobado',1,'2026-03-08 22:32:34'),(8,8,'efectivo_usd',NULL,23.20,9451.18,NULL,'aprobado',1,'2026-03-08 23:06:02'),(9,9,'efectivo_usd',NULL,58.00,23627.96,NULL,'aprobado',1,'2026-03-08 23:11:10'),(10,10,'efectivo_usd',NULL,17.40,7088.39,NULL,'aprobado',1,'2026-03-08 23:21:54'),(11,11,'efectivo_usd',NULL,11.60,4725.59,NULL,'aprobado',1,'2026-03-08 23:22:43'),(12,12,'efectivo_usd',NULL,11.60,4725.59,NULL,'aprobado',1,'2026-03-09 12:12:03'),(13,13,'efectivo_usd',NULL,17.40,7088.39,NULL,'aprobado',1,'2026-03-09 12:16:47'),(14,14,'efectivo_usd',NULL,12.76,5198.15,NULL,'aprobado',1,'2026-03-09 12:19:05'),(15,15,'transferencia','455685468',11.60,4725.59,NULL,'aprobado',1,'2026-03-09 13:03:48'),(16,16,'efectivo_usd',NULL,17.40,7088.39,NULL,'aprobado',1,'2026-03-09 13:09:42'),(17,17,'punto_venta',NULL,40.60,16539.57,NULL,'aprobado',1,'2026-03-09 14:32:37'),(18,18,'punto_venta',NULL,29.00,11813.98,NULL,'aprobado',1,'2026-03-09 14:34:59'),(19,19,'punto_venta',NULL,29.00,11813.98,NULL,'aprobado',1,'2026-03-09 14:36:56'),(20,20,'punto_venta',NULL,40.60,16539.57,NULL,'revision',NULL,'2026-03-09 15:07:34'),(21,21,'punto_venta',NULL,30.16,12286.54,NULL,'aprobado',1,'2026-03-09 15:25:09'),(22,22,'punto_venta',NULL,11.60,5083.18,NULL,'revision',NULL,'2026-04-27 16:04:04'),(23,23,'pago_movil','REF-1791496111',901.00,394822.71,NULL,'revision',NULL,'2026-10-08 21:48:31'),(24,24,'pago_movil','REF-1791496223',13.60,5959.59,NULL,'aprobado',1,'2026-10-08 21:50:23');
/*!40000 ALTER TABLE `pagos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(150) NOT NULL,
  `token` varchar(255) NOT NULL,
  `creado_at` timestamp NULL DEFAULT current_timestamp(),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedido_detalles`
--

DROP TABLE IF EXISTS `pedido_detalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pedido_detalles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `inventario_lote_id` int(11) DEFAULT NULL,
  `cantidad_solicitada` decimal(12,3) NOT NULL,
  `cantidad_real_despachada` decimal(12,3) DEFAULT NULL,
  `precio_historico_usd` decimal(15,2) DEFAULT NULL,
  `observaciones` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pedido_detalles_pedido` (`pedido_id`),
  KEY `idx_pedido_detalles_producto` (`producto_id`),
  KEY `idx_pedido_detalles_lote` (`inventario_lote_id`),
  CONSTRAINT `pedido_detalles_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pedido_detalles_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  CONSTRAINT `pedido_detalles_ibfk_3` FOREIGN KEY (`inventario_lote_id`) REFERENCES `inventario_lotes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido_detalles`
--

LOCK TABLES `pedido_detalles` WRITE;
/*!40000 ALTER TABLE `pedido_detalles` DISABLE KEYS */;
INSERT INTO `pedido_detalles` VALUES (1,1,4,NULL,1.000,NULL,11.00,NULL),(2,1,6,NULL,1.000,NULL,15.50,NULL),(3,1,1,NULL,1.000,NULL,15.50,NULL),(4,2,6,NULL,1.000,NULL,15.50,NULL),(5,3,7,NULL,3.000,NULL,13.00,NULL),(6,3,9,NULL,1.000,NULL,12.00,NULL),(7,4,1,NULL,1.000,NULL,15.50,NULL),(8,4,6,NULL,1.000,NULL,15.50,NULL),(9,4,10,NULL,1.000,NULL,123.00,NULL),(10,5,10,NULL,1.000,NULL,123.00,NULL),(11,5,6,NULL,1.000,NULL,15.50,NULL),(12,5,9,NULL,1.000,NULL,12.00,NULL),(13,5,7,NULL,1.000,NULL,13.00,NULL),(14,6,7,NULL,1.000,1.000,10.00,NULL),(15,7,11,NULL,1.500,1.500,10.00,NULL),(16,8,11,NULL,2.000,2.000,10.00,NULL),(17,9,11,NULL,5.000,NULL,10.00,NULL),(18,10,11,NULL,1.500,1.500,10.00,NULL),(19,11,11,NULL,1.000,NULL,10.00,NULL),(20,12,7,NULL,1.000,1.000,10.00,NULL),(21,13,11,NULL,1.500,1.500,10.00,NULL),(22,14,9,NULL,1.000,1.000,11.00,NULL),(23,15,11,NULL,1.000,NULL,10.00,NULL),(24,16,11,NULL,1.500,1.500,10.00,NULL),(25,17,11,NULL,3.500,NULL,10.00,NULL),(26,18,11,NULL,2.500,NULL,10.00,NULL),(27,19,11,NULL,2.500,NULL,10.00,NULL),(28,20,11,NULL,3.500,NULL,10.00,NULL),(29,21,7,NULL,2.000,NULL,13.00,NULL),(30,22,12,NULL,1.000,NULL,10.00,NULL),(31,23,5,NULL,50.000,NULL,15.50,NULL),(32,24,7,NULL,1.000,NULL,10.00,NULL);
/*!40000 ALTER TABLE `pedido_detalles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedidos`
--

DROP TABLE IF EXISTS `pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `direccion_texto` mediumtext DEFAULT NULL,
  `geo_latitud` decimal(10,8) DEFAULT NULL,
  `geo_longitud` decimal(11,8) DEFAULT NULL,
  `instrucciones_entrega` mediumtext DEFAULT NULL,
  `motivo_devolucion` mediumtext DEFAULT NULL,
  `fecha_devolucion` timestamp NULL DEFAULT NULL,
  `usuario_autorizo_devolucion_id` int(11) DEFAULT NULL,
  `creado_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `sesion_caja_id` (`sesion_caja_id`),
  KEY `zona_delivery_id` (`zona_delivery_id`),
  KEY `tasa_cambio_id` (`tasa_cambio_id`),
  KEY `usuario_autorizo_devolucion_id` (`usuario_autorizo_devolucion_id`),
  KEY `idx_pedidos_usuario` (`usuario_id`),
  KEY `idx_pedidos_estado` (`estado`),
  CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`sesion_caja_id`) REFERENCES `sesiones_caja` (`id`),
  CONSTRAINT `pedidos_ibfk_3` FOREIGN KEY (`zona_delivery_id`) REFERENCES `zonas_delivery` (`id`),
  CONSTRAINT `pedidos_ibfk_4` FOREIGN KEY (`tasa_cambio_id`) REFERENCES `tasas_cambio` (`id`),
  CONSTRAINT `pedidos_ibfk_5` FOREIGN KEY (`usuario_autorizo_devolucion_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedidos`
--

LOCK TABLES `pedidos` WRITE;
/*!40000 ALTER TABLE `pedidos` DISABLE KEYS */;
INSERT INTO `pedidos` VALUES (1,'web',NULL,1,NULL,25,NULL,42.00,0.00,0.00,48.72,19847.49,'pagado','Retiro en Tienda',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-25 03:43:17'),(2,'web',NULL,1,1,25,NULL,15.50,2.00,0.00,19.98,8139.42,'pagado','Calle 8 con carrera 7 (Ref: Plaza Bolivar)',8.75739418,-70.41233955,NULL,NULL,NULL,NULL,'2026-02-25 04:21:39'),(3,'web',NULL,1,1,25,NULL,51.00,2.00,0.00,61.16,24915.28,'entregado','Calle 8 con carrera 7 (Ref: Plaza Bolivar)',8.75739418,-70.41233955,NULL,NULL,NULL,NULL,'2026-02-25 15:45:50'),(4,'web',NULL,1,NULL,25,NULL,154.00,0.00,0.00,178.64,72774.11,'pagado','Retiro en Tienda',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-26 03:19:21'),(5,'web',NULL,1,NULL,25,NULL,163.50,0.00,0.00,189.66,77263.43,'pagado','Retiro en Tienda',NULL,NULL,NULL,NULL,NULL,NULL,'2026-02-26 03:31:14'),(6,'tienda_fisica',NULL,NULL,NULL,25,NULL,10.00,0.00,0.00,10.00,4073.79,'completado_caja',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-03-08 21:38:12'),(7,'tienda_fisica',NULL,NULL,NULL,25,NULL,15.00,0.00,0.00,17.40,7088.39,'completado_caja',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-03-08 22:32:33'),(8,'tienda_fisica',NULL,NULL,NULL,25,NULL,20.00,0.00,0.00,23.20,9451.18,'completado_caja',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-03-08 23:06:02'),(9,'web',NULL,1,NULL,25,NULL,50.00,0.00,0.00,58.00,23627.96,'pagado','Retiro en Tienda',NULL,NULL,NULL,NULL,NULL,NULL,'2026-03-08 23:11:10'),(10,'tienda_fisica',NULL,NULL,NULL,25,NULL,15.00,0.00,0.00,17.40,7088.39,'completado_caja',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-03-08 23:21:54'),(11,'web',NULL,1,NULL,25,NULL,10.00,0.00,0.00,11.60,4725.59,'pagado','Retiro en Tienda',NULL,NULL,NULL,NULL,NULL,NULL,'2026-03-08 23:22:43'),(12,'tienda_fisica',NULL,NULL,NULL,25,NULL,10.00,0.00,0.00,11.60,4725.59,'completado_caja',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-03-09 12:12:02'),(13,'tienda_fisica',NULL,NULL,NULL,25,NULL,15.00,0.00,0.00,17.40,7088.39,'completado_caja',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-03-09 12:16:47'),(14,'tienda_fisica',NULL,NULL,NULL,25,NULL,11.00,0.00,0.00,12.76,5198.15,'completado_caja',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-03-09 12:19:05'),(15,'web',NULL,1,NULL,25,NULL,10.00,0.00,0.00,11.60,4725.59,'pagado','Retiro en Tienda',NULL,NULL,NULL,NULL,NULL,NULL,'2026-03-09 13:03:48'),(16,'tienda_fisica',NULL,NULL,NULL,25,NULL,15.00,0.00,0.00,17.40,7088.39,'completado_caja',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-03-09 13:09:42'),(17,'web',NULL,1,NULL,25,NULL,35.00,0.00,0.00,40.60,16539.57,'pagado','Retiro en Tienda',NULL,NULL,NULL,NULL,NULL,NULL,'2026-03-09 14:32:37'),(18,'web',NULL,1,NULL,25,NULL,25.00,0.00,0.00,29.00,11813.98,'pagado','Retiro en Tienda',NULL,NULL,NULL,NULL,NULL,NULL,'2026-03-09 14:34:59'),(19,'web',NULL,1,NULL,25,NULL,25.00,0.00,0.00,29.00,11813.98,'pagado','Retiro en Tienda',NULL,NULL,NULL,NULL,NULL,NULL,'2026-03-09 14:36:56'),(20,'web',NULL,1,NULL,25,NULL,35.00,0.00,0.00,40.60,16539.57,'pendiente','Retiro en Tienda',NULL,NULL,NULL,NULL,NULL,NULL,'2026-03-09 15:07:34'),(21,'web',NULL,1,NULL,25,NULL,26.00,0.00,0.00,30.16,12286.54,'pagado','Retiro en Tienda',NULL,NULL,NULL,NULL,NULL,NULL,'2026-03-09 15:25:09'),(22,'web',NULL,1,NULL,27,NULL,10.00,0.00,0.00,11.60,5083.18,'pendiente','Retiro en Tienda',NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-27 16:04:04'),(23,'web',NULL,1,1,27,NULL,775.00,2.00,0.00,901.00,394822.71,'pagado','Calle 8 con carrera 7 (Ref: Plaza Bolivar)',8.75739418,-70.41233955,'Prueba automatizada integral del flujo de compra',NULL,NULL,NULL,'2026-10-08 21:48:31'),(24,'web',NULL,1,1,27,NULL,10.00,2.00,0.00,13.60,5959.59,'pagado','Calle 8 con carrera 7 (Ref: Plaza Bolivar)',8.75739418,-70.41233955,'Prueba automatizada integral del flujo de compra',NULL,NULL,NULL,'2026-10-08 21:50:23');
/*!40000 ALTER TABLE `pedidos` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER tr_descontar_inventario_universal
AFTER UPDATE ON pedidos
FOR EACH ROW
BEGIN
    DECLARE v_unidades DECIMAL(15,3) DEFAULT 0;

    IF (NEW.estado = 'pagado' OR NEW.estado = 'completado_caja') 
       AND (OLD.estado != 'pagado' AND OLD.estado != 'completado_caja') THEN
       
       SELECT COALESCE(SUM(cantidad_solicitada), 0) INTO v_unidades
       FROM pedido_detalles 
       WHERE pedido_id = NEW.id;
       
       UPDATE productos p
       JOIN pedido_detalles pd ON p.id = pd.producto_id
       SET p.stock_total = p.stock_total - COALESCE(pd.cantidad_real_despachada, pd.cantidad_solicitada)
       WHERE pd.pedido_id = NEW.id;
       
       UPDATE inventario_lotes il
       JOIN pedido_detalles pd ON il.id = pd.inventario_lote_id
       SET il.cantidad_restante = il.cantidad_restante - COALESCE(pd.cantidad_real_despachada, pd.cantidad_solicitada)
       WHERE pd.pedido_id = NEW.id AND pd.inventario_lote_id IS NOT NULL;
       
       INSERT INTO estadisticas_ventas_diarias (fecha_reporte, total_pedidos, total_ingresos_usd, total_ingresos_ves, unidades_vendidas)
       VALUES (CURDATE(), 1, NEW.total_usd, NEW.total_ves_calculado, v_unidades)
       ON DUPLICATE KEY UPDATE 
           total_pedidos = total_pedidos + 1,
           total_ingresos_usd = total_ingresos_usd + NEW.total_usd,
           total_ingresos_ves = total_ingresos_ves + NEW.total_ves_calculado,
           unidades_vendidas = unidades_vendidas + v_unidades;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER tr_reintegrar_inventario_devolucion
AFTER UPDATE ON pedidos
FOR EACH ROW
BEGIN
    DECLARE v_unidades DECIMAL(15,3) DEFAULT 0;

    IF NEW.estado = 'devuelto' AND OLD.estado != 'devuelto' THEN
        SELECT COALESCE(SUM(cantidad_solicitada), 0) INTO v_unidades
        FROM pedido_detalles 
        WHERE pedido_id = NEW.id;

        UPDATE productos p
        JOIN pedido_detalles pd ON p.id = pd.producto_id
        SET p.stock_total = p.stock_total + COALESCE(pd.cantidad_real_despachada, pd.cantidad_solicitada)
        WHERE pd.pedido_id = NEW.id;

        UPDATE inventario_lotes il
        JOIN pedido_detalles pd ON il.id = pd.inventario_lote_id
        SET il.cantidad_restante = il.cantidad_restante + COALESCE(pd.cantidad_real_despachada, pd.cantidad_solicitada)
        WHERE pd.pedido_id = NEW.id AND pd.inventario_lote_id IS NOT NULL;

        UPDATE estadisticas_ventas_diarias
        SET total_pedidos = GREATEST(0, total_pedidos - 1),
            total_ingresos_usd = GREATEST(0, total_ingresos_usd - NEW.total_usd),
            total_ingresos_ves = GREATEST(0, total_ingresos_ves - NEW.total_ves_calculado),
            unidades_vendidas = GREATEST(0, unidades_vendidas - v_unidades)
        WHERE fecha_reporte = DATE(NEW.creado_at);
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `permisos`
--

DROP TABLE IF EXISTS `permisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permisos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` mediumtext DEFAULT NULL,
  `creado_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permisos`
--

LOCK TABLES `permisos` WRITE;
/*!40000 ALTER TABLE `permisos` DISABLE KEYS */;
INSERT INTO `permisos` VALUES (1,'ver_dashboard','Permite visualizar la pantalla principal del panel de control y sus gráficas.','2026-02-25 07:20:28'),(2,'ver_kpis_financieros','Permite ver las tarjetas de ventas del día, ingresos y tasas de cambio.','2026-02-25 07:20:28'),(3,'ver_kpis_operativos','Permite ver tarjetas de pedidos pendientes, alertas de stock y recetas.','2026-02-25 07:20:28'),(4,'ver_pedidos','Permite visualizar el listado de pedidos web y sus detalles.','2026-02-25 07:20:28'),(5,'actualizar_estado_pedido','Permite cambiar un pedido a preparación, en ruta, entregado o cancelado.','2026-02-25 07:20:28'),(6,'gestionar_pagos','Permite ver, aprobar o rechazar los comprobantes de pago de los clientes.','2026-02-25 07:20:28'),(7,'crear_venta_manual','Permite crear pedidos o ventas directamente desde el panel (POS).','2026-02-25 07:20:28'),(8,'procesar_devoluciones','Permite registrar y procesar devoluciones parciales o totales de pedidos.','2026-02-25 07:20:28'),(9,'ver_productos','Permite visualizar el catálogo de productos.','2026-02-25 07:20:28'),(10,'gestionar_productos','Permite crear, editar, eliminar productos y armar combos.','2026-02-25 07:20:28'),(11,'gestionar_inventario_lotes','Permite registrar compras a proveedores, editar lotes y ver fechas de vencimiento.','2026-02-25 07:20:28'),(12,'gestionar_categorias_marcas','Permite crear y editar las categorías y las marcas.','2026-02-25 07:20:28'),(13,'ver_historico_precios','Permite visualizar el log de cambios de precios de los productos.','2026-02-25 07:20:28'),(14,'ver_usuarios','Permite ver el listado de clientes y personal registrado.','2026-02-25 07:20:28'),(15,'gestionar_usuarios','Permite editar datos de usuarios o inhabilitar cuentas.','2026-02-25 07:20:28'),(16,'gestionar_roles_permisos','Permite crear roles, asignar permisos a roles y otorgar permisos extra a usuarios.','2026-02-25 07:20:28'),(17,'ver_logistica','Permite acceder al módulo de logística.','2026-02-25 07:20:28'),(18,'gestionar_zonas_delivery','Permite crear o editar zonas de envío y sus tarifas.','2026-02-25 07:20:28'),(19,'gestionar_repartidores','Permite asignar usuarios como repartidores y ver sus datos.','2026-02-25 07:20:28'),(20,'ver_seguimiento_rutas','Permite monitorear el estado y ubicación de los pedidos en ruta.','2026-02-25 07:20:28'),(21,'ver_facturas','Permite ver el listado de facturas emitidas.','2026-02-25 07:20:28'),(22,'configurar_facturacion','Permite editar las series fiscales y el porcentaje de IVA.','2026-02-25 07:20:28'),(23,'gestionar_caja_diaria','Permite realizar aperturas, cierres y registrar movimientos de efectivo en tienda.','2026-02-25 07:20:28'),(24,'gestionar_tasas_cambio','Permite actualizar manualmente la tasa de cambio o ver su histórico.','2026-02-25 07:20:28'),(25,'ver_recetas','Permite ver la lista de recetas subidas por los clientes.','2026-02-25 07:20:28'),(26,'auditar_recetas','Permite aprobar o rechazar los récipes para liberar la venta de productos controlados.','2026-02-25 07:20:28'),(27,'configurar_tienda','Permite cambiar el nombre, modo operativo (abierto/cerrado) y avisos de la tienda.','2026-02-25 07:20:28'),(28,'configurar_apis','Permite cambiar credenciales del BCV, pasarelas u otras APIs externas.','2026-02-25 07:20:28'),(29,'gestionar_cuentas_banco','Permite administrar los métodos de pago que verá el cliente (Zelle, Pago Móvil).','2026-02-25 07:20:28'),(30,'gestionar_horarios','Permite definir el horario laboral semanal y las excepciones (feriados).','2026-02-25 07:20:28'),(31,'ver_logs_auditoria','Permite ver el registro completo de acciones que realizan los administradores en el sistema.','2026-02-25 07:20:28');
/*!40000 ALTER TABLE `permisos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permisos_extra_usuario`
--

DROP TABLE IF EXISTS `permisos_extra_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permisos_extra_usuario` (
  `usuario_id` int(11) NOT NULL,
  `permiso_id` int(11) NOT NULL,
  `accion` enum('permitir','denegar') DEFAULT 'permitir',
  PRIMARY KEY (`usuario_id`,`permiso_id`),
  KEY `permiso_id` (`permiso_id`),
  CONSTRAINT `permisos_extra_usuario_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `permisos_extra_usuario_ibfk_2` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permisos_extra_usuario`
--

LOCK TABLES `permisos_extra_usuario` WRITE;
/*!40000 ALTER TABLE `permisos_extra_usuario` DISABLE KEYS */;
INSERT INTO `permisos_extra_usuario` VALUES (1,2,'permitir'),(1,21,'permitir'),(1,24,'permitir'),(1,25,'permitir'),(1,26,'permitir'),(2,1,'denegar'),(2,2,'denegar'),(2,3,'denegar'),(2,4,'denegar'),(2,5,'denegar'),(2,6,'denegar'),(2,7,'denegar'),(2,8,'denegar'),(2,9,'denegar'),(2,10,'denegar'),(2,11,'denegar'),(2,12,'denegar'),(2,13,'denegar'),(2,14,'denegar'),(2,15,'denegar'),(2,16,'denegar'),(2,17,'denegar'),(2,18,'denegar'),(2,19,'denegar'),(2,20,'denegar'),(2,21,'denegar'),(2,22,'denegar'),(2,23,'denegar'),(2,24,'denegar'),(2,25,'denegar'),(2,26,'denegar'),(2,27,'denegar'),(2,28,'denegar'),(2,29,'denegar'),(2,30,'denegar'),(2,31,'denegar');
/*!40000 ALTER TABLE `permisos_extra_usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `producto_imagenes`
--

DROP TABLE IF EXISTS `producto_imagenes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `producto_imagenes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `producto_id` int(11) NOT NULL,
  `url_imagen` varchar(255) DEFAULT NULL,
  `es_principal` tinyint(1) DEFAULT 0,
  `orden` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `producto_imagenes_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `producto_imagenes`
--

LOCK TABLES `producto_imagenes` WRITE;
/*!40000 ALTER TABLE `producto_imagenes` DISABLE KEYS */;
INSERT INTO `producto_imagenes` VALUES (1,9,'productos/RdItqxzxuNsMQ9z3wJmIxMJw73xWG9ZVVjhX5x9U.png',1,0);
/*!40000 ALTER TABLE `producto_imagenes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoria_id` int(11) DEFAULT NULL,
  `marca_id` int(11) DEFAULT NULL,
  `proveedor_defecto_id` int(11) DEFAULT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` mediumtext DEFAULT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `codigo_barras` varchar(100) DEFAULT NULL,
  `costo_promedio_usd` decimal(15,2) DEFAULT 0.00,
  `precio_venta_usd` decimal(15,2) NOT NULL,
  `precio_oferta_usd` decimal(15,2) DEFAULT NULL,
  `unidad_medida` enum('unidad','kg','g','mg','litro','ml','galon','saco','bulto','paquete','metro','rollo','dosis') DEFAULT 'unidad',
  `contenido_neto` decimal(10,3) DEFAULT NULL,
  `unidad_contenido` enum('kg','g','l','ml','unidad') DEFAULT 'kg',
  `es_controlado` tinyint(1) DEFAULT 0,
  `atributos_json` longtext DEFAULT NULL CHECK (json_valid(`atributos_json`)),
  `stock_total` decimal(12,3) DEFAULT 0.000,
  `stock_minimo_alerta` decimal(12,0) DEFAULT 5,
  `venta_minima` decimal(10,3) DEFAULT 1.000,
  `paso_venta` decimal(10,3) DEFAULT 1.000,
  `es_combo` tinyint(1) DEFAULT 0,
  `destacado` tinyint(1) DEFAULT 0,
  `eliminado_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`),
  UNIQUE KEY `codigo_barras` (`codigo_barras`),
  KEY `categoria_id` (`categoria_id`),
  KEY `marca_id` (`marca_id`),
  KEY `proveedor_defecto_id` (`proveedor_defecto_id`),
  CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
  CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`marca_id`) REFERENCES `marcas` (`id`),
  CONSTRAINT `productos_ibfk_3` FOREIGN KEY (`proveedor_defecto_id`) REFERENCES `proveedores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,25,1,NULL,'Ivermectina Inyectable 1%','Antiparasitario de amplio espectro para bovinos, ovinos y porcinos',NULL,'IVM-001','7891234567890',4.38,15.50,NULL,'ml',1000.000,'ml',1,'{\"ingrediente_activo\": \"Ivermectina 1%\", \"tiempo_retiro\": \"28 días\", \"toxicidad\": \"Banda Azul\", \"especies\": [\"bovino\", \"ovino\", \"porcino\"], \"dosis\": \"1 ml por 50 kg de peso\"}',48.000,5,50.000,50.000,0,0,NULL),(4,1,2,NULL,'prueba 1','Antiparasitario de amplio espectro para bovinos, ovinos y porcinos',NULL,'IVM-002','74564564',4.38,11.00,NULL,'ml',1000.000,'ml',1,'{\"ingrediente_activo\": \"Ivermectina 1%\", \"tiempo_retiro\": \"28 días\", \"toxicidad\": \"Banda Azul\", \"especies\": [\"bovino\", \"ovino\", \"porcino\"], \"dosis\": \"1 ml por 50 kg de peso\"}',49.000,5,50.000,50.000,0,0,NULL),(5,1,2,NULL,'prueba 2','Antiparasitario de amplio espectro para bovinos, ovinos y porcinos',NULL,'IVM-003','56756756',4.38,15.50,NULL,'ml',1000.000,'ml',1,'{\"ingrediente_activo\": \"Ivermectina 1%\", \"tiempo_retiro\": \"28 días\", \"toxicidad\": \"Banda Azul\", \"especies\": [\"bovino\", \"ovino\", \"porcino\"], \"dosis\": \"1 ml por 50 kg de peso\"}',0.000,5,50.000,50.000,0,0,NULL),(6,2,6,NULL,'prueba 4','Antiparasitario de amplio espectro para bovinos, ovinos y porcinos','img/upload/productos/1772069646_699fa30e2399a.webp','IVM-004','567567564234',4.38,15.50,NULL,'ml',1000.000,'ml',1,'{\"ingrediente_activo\": \"Ivermectina 1%\", \"tiempo_retiro\": \"28 días\", \"toxicidad\": \"Banda Azul\", \"especies\": [\"bovino\", \"ovino\", \"porcino\"], \"dosis\": \"1 ml por 50 kg de peso\"}',45.000,5,50.000,50.000,0,0,NULL),(7,1,4,NULL,'prueba pantalla','prueba de funcionalidad de pantalla','productos/jMkut3Qj2GvuEgm07ZaXWM7x9HlITIzH3CMNhnCn.png','prueba','123123123',0.00,13.00,10.00,'unidad',1000.000,'kg',0,NULL,3.000,5,1.000,1.000,0,1,NULL),(9,4,2,NULL,'pantalla 2','sfdsdfsdf',NULL,'fsdfsd','24234215',4.00,12.00,11.00,'unidad',10.000,'kg',0,'\"[]\"',67.000,5,1.000,1.000,0,1,NULL),(10,3,4,NULL,'cvcvccvcvc','cvcvcv','img/upload/productos/1772069632_699fa3006b304.png','cvcvcvcvcv','34342342',0.00,123.00,25.00,'unidad',NULL,'kg',0,NULL,121.000,5,1.000,1.000,0,0,NULL),(11,13,7,NULL,'PRUEBA Carrito','prueba de carrito','img/upload/productos/1772990740_69adb11436e17.jpg','PR-1','08761239',6.00,10.00,NULL,'kg',1.500,'kg',0,'\"{\\\"Ingrediente Activo\\\":\\\"1era prueba\\\",\\\"Funcionamiento\\\":\\\"Esperemos que funcione\\\"}\"',82.500,10,1.500,0.500,0,0,NULL),(12,23,5,NULL,'codigo de barras','asdasdasd',NULL,'ASDAS31','12',0.00,10.00,NULL,'unidad',10.000,'kg',0,NULL,100.000,5,1.000,1.000,0,0,NULL);
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos_compuestos`
--

DROP TABLE IF EXISTS `productos_compuestos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productos_compuestos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `producto_padre_id` int(11) NOT NULL,
  `producto_hijo_id` int(11) NOT NULL,
  `cantidad_requerida` decimal(12,3) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `producto_padre_id` (`producto_padre_id`),
  KEY `producto_hijo_id` (`producto_hijo_id`),
  CONSTRAINT `productos_compuestos_ibfk_1` FOREIGN KEY (`producto_padre_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `productos_compuestos_ibfk_2` FOREIGN KEY (`producto_hijo_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos_compuestos`
--

LOCK TABLES `productos_compuestos` WRITE;
/*!40000 ALTER TABLE `productos_compuestos` DISABLE KEYS */;
/*!40000 ALTER TABLE `productos_compuestos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `razon_social` varchar(200) DEFAULT NULL,
  `rif` varchar(20) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `persona_contacto` varchar(100) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rif` (`rif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proveedores`
--

LOCK TABLES `proveedores` WRITE;
/*!40000 ALTER TABLE `proveedores` DISABLE KEYS */;
/*!40000 ALTER TABLE `proveedores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `receta_productos`
--

DROP TABLE IF EXISTS `receta_productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `receta_productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `receta_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad_prescrita` decimal(12,3) NOT NULL,
  `dosis_instrucciones` mediumtext DEFAULT NULL,
  `autorizado` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `receta_id` (`receta_id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `receta_productos_ibfk_1` FOREIGN KEY (`receta_id`) REFERENCES `recetas_veterinarias` (`id`) ON DELETE CASCADE,
  CONSTRAINT `receta_productos_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `receta_productos`
--

LOCK TABLES `receta_productos` WRITE;
/*!40000 ALTER TABLE `receta_productos` DISABLE KEYS */;
/*!40000 ALTER TABLE `receta_productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recetas_veterinarias`
--

DROP TABLE IF EXISTS `recetas_veterinarias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recetas_veterinarias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `observaciones` mediumtext DEFAULT NULL,
  `usuario_revisa_id` int(11) DEFAULT NULL,
  `fecha_revision` timestamp NULL DEFAULT NULL,
  `creado_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `usuario_revisa_id` (`usuario_revisa_id`),
  KEY `idx_recetas_pedido` (`pedido_id`),
  KEY `idx_recetas_cliente` (`cliente_usuario_id`),
  CONSTRAINT `recetas_veterinarias_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  CONSTRAINT `recetas_veterinarias_ibfk_2` FOREIGN KEY (`cliente_usuario_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `recetas_veterinarias_ibfk_3` FOREIGN KEY (`usuario_revisa_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recetas_veterinarias`
--

LOCK TABLES `recetas_veterinarias` WRITE;
/*!40000 ALTER TABLE `recetas_veterinarias` DISABLE KEYS */;
/*!40000 ALTER TABLE `recetas_veterinarias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `repartidores`
--

DROP TABLE IF EXISTS `repartidores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `repartidores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `tipo_vehiculo` enum('moto','carro','camion_carga','pickup') DEFAULT 'moto',
  `placa` varchar(20) DEFAULT NULL,
  `disponible` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `repartidores_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `repartidores`
--

LOCK TABLES `repartidores` WRITE;
/*!40000 ALTER TABLE `repartidores` DISABLE KEYS */;
/*!40000 ALTER TABLE `repartidores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rol_permisos`
--

DROP TABLE IF EXISTS `rol_permisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rol_permisos` (
  `rol_id` int(11) NOT NULL,
  `permiso_id` int(11) NOT NULL,
  PRIMARY KEY (`rol_id`,`permiso_id`),
  KEY `permiso_id` (`permiso_id`),
  CONSTRAINT `rol_permisos_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rol_permisos_ibfk_2` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rol_permisos`
--

LOCK TABLES `rol_permisos` WRITE;
/*!40000 ALTER TABLE `rol_permisos` DISABLE KEYS */;
INSERT INTO `rol_permisos` VALUES (1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(1,8),(1,9),(1,10),(1,11),(1,12),(1,13),(1,14),(1,15),(1,16),(1,17),(1,18),(1,19),(1,20),(1,21),(1,22),(1,23),(1,24),(1,25),(1,26),(1,27),(1,28),(1,29),(1,30),(1,31),(5,13),(5,14),(5,15),(5,17),(5,18),(5,20);
/*!40000 ALTER TABLE `rol_permisos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin'),(3,'Almacenista'),(4,'Cliente'),(5,'Repartidor'),(2,'Vendedor');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sesiones_caja`
--

DROP TABLE IF EXISTS `sesiones_caja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sesiones_caja` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `observaciones_cierre` mediumtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `caja_id` (`caja_id`),
  KEY `cajero_usuario_id` (`cajero_usuario_id`),
  CONSTRAINT `sesiones_caja_ibfk_1` FOREIGN KEY (`caja_id`) REFERENCES `cajas_fisicas` (`id`),
  CONSTRAINT `sesiones_caja_ibfk_2` FOREIGN KEY (`cajero_usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sesiones_caja`
--

LOCK TABLES `sesiones_caja` WRITE;
/*!40000 ALTER TABLE `sesiones_caja` DISABLE KEYS */;
INSERT INTO `sesiones_caja` VALUES (1,1,1,'2026-03-08 21:29:17','2026-03-09 13:24:11',100.00,127.16,51802.27,157.00,NULL,-70.16,'prueba');
/*!40000 ALTER TABLE `sesiones_caja` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('3IhIebOANzYMWiBZtJijZLYmnGvdSgfj5JSo6EPd',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:157.0) Gecko/20100101 Firefox/157.0','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiZTBNSW82eWFlaEpmWm9UV3F1dHNQaFpoa3FzZzdIdkp1UXdtWFA1diI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MTI6InNwbGFzaF9zaG93biI7YjoxO3M6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jYXJyaXRvIjt9fQ==',1791493064),('llYCdTvdKLwUlAgSVXtcE0vuLFVbaPNx5RB6eS6H',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:157.0) Gecko/20100101 Firefox/157.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMHBSWmlOaHY3WmlUbzRyWmdFSGlGTUF3eGxlc0FxbnJ6Uk5lVzM3bCI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czoyMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1791491583);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `solicitudes_productos`
--

DROP TABLE IF EXISTS `solicitudes_productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `solicitudes_productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `nombre_producto` varchar(255) NOT NULL,
  `descripcion_adicional` mediumtext DEFAULT NULL,
  `estado` enum('pendiente','revisado','agregado','rechazado') DEFAULT 'pendiente',
  `fecha_peticion` timestamp NULL DEFAULT current_timestamp(),
  `actualizado_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_solicitudes_usuario` (`usuario_id`),
  KEY `idx_solicitudes_estado` (`estado`),
  CONSTRAINT `fk_solicitudes_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitudes_productos`
--

LOCK TABLES `solicitudes_productos` WRITE;
/*!40000 ALTER TABLE `solicitudes_productos` DISABLE KEYS */;
INSERT INTO `solicitudes_productos` VALUES (1,1,'solicitud de producto','asdasceceaf','pendiente','2026-04-27 17:51:56','2026-04-27 17:51:56');
/*!40000 ALTER TABLE `solicitudes_productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasas_cambio`
--

DROP TABLE IF EXISTS `tasas_cambio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tasas_cambio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_moneda` varchar(10) DEFAULT 'USD',
  `moneda_base` varchar(10) DEFAULT 'VES',
  `valor_tasa` decimal(15,4) NOT NULL,
  `fuente` enum('API','MANUAL') DEFAULT 'API',
  `usuario_editor_id` int(11) DEFAULT NULL,
  `creado_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `usuario_editor_id` (`usuario_editor_id`),
  CONSTRAINT `tasas_cambio_ibfk_1` FOREIGN KEY (`usuario_editor_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasas_cambio`
--

LOCK TABLES `tasas_cambio` WRITE;
/*!40000 ALTER TABLE `tasas_cambio` DISABLE KEYS */;
INSERT INTO `tasas_cambio` VALUES (4,'USD','VES',370.2544,'API',NULL,'2026-02-02 14:00:02'),(5,'USD','VES',372.1057,'API',NULL,'2026-02-03 14:00:03'),(6,'USD','VES',375.0825,'API',NULL,'2026-02-04 14:00:02'),(7,'USD','VES',378.4582,'API',NULL,'2026-02-05 14:00:03'),(8,'USD','VES',381.1074,'API',NULL,'2026-02-06 14:00:03'),(9,'USD','VES',382.6318,'API',NULL,'2026-02-07 14:00:03'),(10,'USD','VES',382.6318,'API',NULL,'2026-02-08 14:00:02'),(11,'USD','VES',385.2720,'API',NULL,'2026-02-10 14:02:02'),(12,'USD','VES',388.7394,'API',NULL,'2026-02-11 14:02:02'),(13,'USD','VES',390.2944,'API',NULL,'2026-02-12 14:02:04'),(14,'USD','VES',393.2216,'API',NULL,'2026-02-13 14:00:03'),(15,'USD','VES',396.3674,'API',NULL,'2026-02-14 14:00:02'),(16,'USD','VES',396.3674,'API',NULL,'2026-02-15 14:00:02'),(17,'USD','VES',396.3674,'API',NULL,'2026-02-16 14:00:02'),(18,'USD','VES',396.3674,'API',NULL,'2026-02-17 14:00:01'),(19,'USD','VES',396.3674,'API',NULL,'2026-02-18 14:00:02'),(20,'USD','VES',398.7456,'API',NULL,'2026-02-19 14:00:02'),(21,'USD','VES',402.3343,'API',NULL,'2026-02-20 14:00:02'),(22,'USD','VES',405.3518,'API',NULL,'2026-02-21 14:00:02'),(23,'USD','VES',405.3518,'API',NULL,'2026-02-22 14:00:02'),(24,'USD','VES',405.3518,'API',NULL,'2026-02-23 14:00:02'),(25,'USD','VES',407.3786,'API',NULL,'2026-02-24 14:00:02'),(27,'USD','VES',438.2050,'API',NULL,'2026-03-11 14:25:37');
/*!40000 ALTER TABLE `tasas_cambio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rol_id` int(11) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `documento_identidad` varchar(20) DEFAULT NULL,
  `tipo_cliente` enum('natural','juridico','finca_productor') DEFAULT 'natural',
  `activo` tinyint(1) DEFAULT 1,
  `creado_at` timestamp NULL DEFAULT current_timestamp(),
  `actualizado_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `rol_id` (`rol_id`),
  KEY `documento_identidad` (`documento_identidad`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,1,'Bryan','Moreno','bryanalfredoxd@gmail.com','$2y$12$CC0ZUW3GE6DUYAAo8EbjZeXeA2E4zXk4jVnmCeDvz7y6eOrD/oJxe','eam5LPvXUcm7Fp9DzqNiLpGJFiRpeWJFI1wj0kZIboYEHuzdKTUKATjZDyx6','412-8275037','V-29923421','natural',1,'2026-02-25 03:11:11','2026-02-25 06:04:22'),(2,1,'prueba','prueba','123123123@gmail.com','$2y$12$2.YAhDo1kqEolkCgtNKGLONds2CrCjzXdSE7FiNbLb8Ex2PlilGH2',NULL,'+58123123123','V-123123123','natural',1,'2026-02-25 09:16:57','2026-02-25 09:28:22'),(3,4,'nuevo','nuevo','nuevo@gmail.com','$2y$12$cK.nT4yQsZ1BuXVtqZ8G8esr9gYlQPsrcBfylPqRD1SK5EKG1kRhq',NULL,'+5809090909009','V-909090909','natural',1,'2026-03-08 23:38:44','2026-03-08 23:38:44');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `view_inventario_valorado`
--

DROP TABLE IF EXISTS `view_inventario_valorado`;
/*!50001 DROP VIEW IF EXISTS `view_inventario_valorado`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `view_inventario_valorado` AS SELECT
 1 AS `id`,
  1 AS `nombre`,
  1 AS `sku`,
  1 AS `stock_total`,
  1 AS `costo_promedio_usd`,
  1 AS `valor_total_usd`,
  1 AS `precio_venta_usd`,
  1 AS `valor_venta_total_usd`,
  1 AS `categoria`,
  1 AS `marca`,
  1 AS `estado_stock` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_productos_mas_vendidos`
--

DROP TABLE IF EXISTS `view_productos_mas_vendidos`;
/*!50001 DROP VIEW IF EXISTS `view_productos_mas_vendidos`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `view_productos_mas_vendidos` AS SELECT
 1 AS `id`,
  1 AS `nombre`,
  1 AS `sku`,
  1 AS `categoria`,
  1 AS `veces_vendido`,
  1 AS `cantidad_total_vendida`,
  1 AS `unidad_medida`,
  1 AS `monto_total_usd` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_productos_proximos_vencer`
--

DROP TABLE IF EXISTS `view_productos_proximos_vencer`;
/*!50001 DROP VIEW IF EXISTS `view_productos_proximos_vencer`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `view_productos_proximos_vencer` AS SELECT
 1 AS `lote_id`,
  1 AS `producto`,
  1 AS `numero_lote`,
  1 AS `fecha_vencimiento`,
  1 AS `dias_para_vencer`,
  1 AS `cantidad_restante`,
  1 AS `unidad_medida`,
  1 AS `estado_vencimiento` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_ventas_diarias_detalladas`
--

DROP TABLE IF EXISTS `view_ventas_diarias_detalladas`;
/*!50001 DROP VIEW IF EXISTS `view_ventas_diarias_detalladas`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `view_ventas_diarias_detalladas` AS SELECT
 1 AS `fecha`,
  1 AS `total_pedidos`,
  1 AS `total_ventas_usd`,
  1 AS `total_ventas_ves`,
  1 AS `clientes_unicos`,
  1 AS `ticket_promedio_usd` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `zonas_delivery`
--

DROP TABLE IF EXISTS `zonas_delivery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `zonas_delivery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_zona` varchar(100) DEFAULT NULL,
  `precio_delivery_usd` decimal(10,2) NOT NULL,
  `tiempo_estimado_minutos` int(11) DEFAULT NULL,
  `requiere_vehiculo_carga` tinyint(1) DEFAULT 0,
  `activa` tinyint(1) DEFAULT 1,
  `latitud` decimal(10,7) DEFAULT 8.7622000,
  `longitud` decimal(10,7) DEFAULT -70.4106000,
  `radio_km` decimal(5,2) DEFAULT 3.00,
  `color_mapa` varchar(20) DEFAULT '#1e549e',
  `descripcion` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `zonas_delivery`
--

LOCK TABLES `zonas_delivery` WRITE;
/*!40000 ALTER TABLE `zonas_delivery` DISABLE KEYS */;
INSERT INTO `zonas_delivery` VALUES (1,'Zona Centro Urbano',2.00,30,0,1,8.7622000,-70.4106000,2.50,'#1e549e','Casco central de Barinitas y sectores aledaños'),(2,'Zona Periferia',3.50,45,0,1,8.7580000,-70.4050000,4.50,'#3492cf','Sectores periféricos, urbanizaciones y salidas hacia Barinas'),(3,'Zona Rural Ligera',5.00,60,0,1,8.7450000,-70.4250000,7.00,'#10b981','Zonas rurales de fácil acceso para moto o vehículo liviano'),(4,'Zona Rural Pesada',8.00,90,1,1,8.7200000,-70.4500000,12.00,'#f59e0b','Zonas rurales profundas que requieren camioneta o camión'),(5,'Zona Fincas Lejanas',12.00,120,1,1,8.6900000,-70.5000000,18.00,'#ef4444','Fincas y hatos distantes');
/*!40000 ALTER TABLE `zonas_delivery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'agro_ecommerce_db'
--
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_generar_numero_factura` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_generar_numero_factura`(
    IN p_serie VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, 
    OUT p_numero_factura VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    DECLARE v_proximo_numero INT;
    DECLARE v_serie_actual VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    
    -- Bloquear la tabla para evitar duplicados
    SELECT serie, proximo_numero INTO v_serie_actual, v_proximo_numero 
    FROM factura_ajustes 
    WHERE serie = p_serie AND activo = 1
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
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_obtener_productos_receta_pendiente` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_productos_receta_pendiente`(IN p_producto_id INT)
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
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_procesar_devolucion_pedido` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_procesar_devolucion_pedido`(
    IN p_pedido_id INT, 
    IN p_usuario_id INT, 
    IN p_motivo TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    DECLARE v_pedido_existe INT;
    DECLARE v_detalle_id INT;
    DECLARE v_producto_id INT;
    DECLARE v_lote_id INT;
    DECLARE v_cantidad DECIMAL(12, 3);
    DECLARE v_finished INT DEFAULT 0;
    
    DECLARE cur_detalles CURSOR FOR
    SELECT id, producto_id, inventario_lote_id, cantidad_real_despachada 
    FROM pedido_detalles 
    WHERE pedido_id = p_pedido_id;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_finished = 1;
    
    SELECT COUNT(*) INTO v_pedido_existe FROM pedidos WHERE id = p_pedido_id;
    
    IF v_pedido_existe = 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'El pedido no existe';
    END IF;
    
    UPDATE pedidos 
    SET estado = 'devuelto',
        motivo_devolucion = p_motivo,
        fecha_devolucion = NOW(),
        usuario_autorizo_devolucion_id = p_usuario_id
    WHERE id = p_pedido_id;
    
    OPEN cur_detalles;
    
    read_loop: LOOP
        FETCH cur_detalles INTO v_detalle_id, v_producto_id, v_lote_id, v_cantidad;
        
        IF v_finished = 1 THEN
            LEAVE read_loop;
        END IF;
        
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
            COALESCE(v_cantidad, 1),
            'cliente_arrepentido',
            'recibido',
            p_usuario_id,
            NOW()
        );
        
    END LOOP;
    
    CLOSE cur_detalles;
    
    UPDATE facturas 
    SET estado = 'anulada'
    WHERE pedido_id = p_pedido_id AND estado = 'emitida';
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `view_inventario_valorado`
--

/*!50001 DROP VIEW IF EXISTS `view_inventario_valorado`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_inventario_valorado` AS select `p`.`id` AS `id`,`p`.`nombre` AS `nombre`,`p`.`sku` AS `sku`,`p`.`stock_total` AS `stock_total`,`p`.`costo_promedio_usd` AS `costo_promedio_usd`,`p`.`stock_total` * `p`.`costo_promedio_usd` AS `valor_total_usd`,`p`.`precio_venta_usd` AS `precio_venta_usd`,`p`.`stock_total` * `p`.`precio_venta_usd` AS `valor_venta_total_usd`,`c`.`nombre` AS `categoria`,`m`.`nombre` AS `marca`,case when `p`.`stock_total` <= `p`.`stock_minimo_alerta` then 'CRÍTICO' when `p`.`stock_total` <= `p`.`stock_minimo_alerta` * 2 then 'BAJO' else 'NORMAL' end AS `estado_stock` from ((`productos` `p` left join `categorias` `c` on(`p`.`categoria_id` = `c`.`id`)) left join `marcas` `m` on(`p`.`marca_id` = `m`.`id`)) where `p`.`eliminado_at` is null */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_productos_mas_vendidos`
--

/*!50001 DROP VIEW IF EXISTS `view_productos_mas_vendidos`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_productos_mas_vendidos` AS select `p`.`id` AS `id`,`p`.`nombre` AS `nombre`,`p`.`sku` AS `sku`,`c`.`nombre` AS `categoria`,count(`pd`.`id`) AS `veces_vendido`,sum(`pd`.`cantidad_solicitada`) AS `cantidad_total_vendida`,`p`.`unidad_medida` AS `unidad_medida`,sum(`pd`.`cantidad_solicitada` * `pd`.`precio_historico_usd`) AS `monto_total_usd` from (((`pedido_detalles` `pd` join `productos` `p` on(`pd`.`producto_id` = `p`.`id`)) join `pedidos` `pe` on(`pd`.`pedido_id` = `pe`.`id`)) join `categorias` `c` on(`p`.`categoria_id` = `c`.`id`)) where `pe`.`estado` in ('pagado','entregado','completado_caja') group by `p`.`id`,`p`.`nombre`,`p`.`sku`,`c`.`nombre`,`p`.`unidad_medida` order by sum(`pd`.`cantidad_solicitada`) desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_productos_proximos_vencer`
--

/*!50001 DROP VIEW IF EXISTS `view_productos_proximos_vencer`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_productos_proximos_vencer` AS select `il`.`id` AS `lote_id`,`p`.`nombre` AS `producto`,`il`.`numero_lote` AS `numero_lote`,`il`.`fecha_vencimiento` AS `fecha_vencimiento`,to_days(`il`.`fecha_vencimiento`) - to_days(curdate()) AS `dias_para_vencer`,`il`.`cantidad_restante` AS `cantidad_restante`,`p`.`unidad_medida` AS `unidad_medida`,case when to_days(`il`.`fecha_vencimiento`) - to_days(curdate()) <= 30 then 'PRÓXIMO A VENCER' when to_days(`il`.`fecha_vencimiento`) - to_days(curdate()) <= 90 then 'ALERTA MEDIA' else 'NORMAL' end AS `estado_vencimiento` from (`inventario_lotes` `il` join `productos` `p` on(`il`.`producto_id` = `p`.`id`)) where `il`.`activo` = 1 and `il`.`cantidad_restante` > 0 and `il`.`fecha_vencimiento` >= curdate() order by `il`.`fecha_vencimiento` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_ventas_diarias_detalladas`
--

/*!50001 DROP VIEW IF EXISTS `view_ventas_diarias_detalladas`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_ventas_diarias_detalladas` AS select cast(`p`.`creado_at` as date) AS `fecha`,count(`p`.`id`) AS `total_pedidos`,sum(`p`.`total_usd`) AS `total_ventas_usd`,sum(`p`.`total_ves_calculado`) AS `total_ventas_ves`,count(distinct `p`.`usuario_id`) AS `clientes_unicos`,avg(`p`.`total_usd`) AS `ticket_promedio_usd` from `pedidos` `p` where `p`.`estado` in ('pagado','entregado','completado_caja') group by cast(`p`.`creado_at` as date) order by cast(`p`.`creado_at` as date) desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-08 18:13:22
