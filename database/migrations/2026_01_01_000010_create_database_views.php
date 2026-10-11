<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS `view_inventario_valorado`");
        DB::statement("
            CREATE VIEW `view_inventario_valorado` AS 
            SELECT 
                p.id AS id,
                p.nombre AS nombre,
                p.sku AS sku,
                p.stock_total AS stock_total,
                p.costo_promedio_usd AS costo_promedio_usd,
                p.stock_total * p.costo_promedio_usd AS valor_total_usd,
                p.precio_venta_usd AS precio_venta_usd,
                p.stock_total * p.precio_venta_usd AS valor_venta_total_usd,
                c.nombre AS categoria,
                m.nombre AS marca,
                CASE 
                    WHEN p.stock_total <= p.stock_minimo_alerta THEN 'CRÍTICO' 
                    WHEN p.stock_total <= p.stock_minimo_alerta * 2 THEN 'BAJO' 
                    ELSE 'NORMAL' 
                END AS estado_stock 
            FROM productos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            LEFT JOIN marcas m ON p.marca_id = m.id 
            WHERE p.eliminado_at IS NULL
        ");

        DB::statement("DROP VIEW IF EXISTS `view_productos_mas_vendidos`");
        DB::statement("
            CREATE VIEW `view_productos_mas_vendidos` AS 
            SELECT 
                p.id AS id,
                p.nombre AS nombre,
                p.sku AS sku,
                c.nombre AS categoria,
                COUNT(pd.id) AS veces_vendido,
                SUM(pd.cantidad_solicitada) AS cantidad_total_vendida,
                p.unidad_medida AS unidad_medida,
                SUM(pd.cantidad_solicitada * pd.precio_historico_usd) AS monto_total_usd 
            FROM pedido_detalles pd 
            JOIN productos p ON pd.producto_id = p.id 
            JOIN pedidos pe ON pd.pedido_id = pe.id 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            WHERE pe.estado IN ('pagado','entregado','completado_caja') 
            GROUP BY p.id, p.nombre, p.sku, c.nombre, p.unidad_medida 
            ORDER BY SUM(pd.cantidad_solicitada) DESC
        ");

        DB::statement("DROP VIEW IF EXISTS `view_productos_proximos_vencer`");
        DB::statement("
            CREATE VIEW `view_productos_proximos_vencer` AS 
            SELECT 
                il.id AS lote_id,
                p.nombre AS producto,
                il.numero_lote AS numero_lote,
                il.fecha_vencimiento AS fecha_vencimiento,
                DATEDIFF(il.fecha_vencimiento, CURDATE()) AS dias_para_vencer,
                il.cantidad_restante AS cantidad_restante,
                p.unidad_medida AS unidad_medida,
                CASE 
                    WHEN DATEDIFF(il.fecha_vencimiento, CURDATE()) <= 30 THEN 'PRÓXIMO A VENCER' 
                    WHEN DATEDIFF(il.fecha_vencimiento, CURDATE()) <= 90 THEN 'ALERTA MEDIA' 
                    ELSE 'NORMAL' 
                END AS estado_vencimiento 
            FROM inventario_lotes il 
            JOIN productos p ON il.producto_id = p.id 
            WHERE il.activo = 1 
              AND il.cantidad_restante > 0 
              AND il.fecha_vencimiento >= CURDATE() 
            ORDER BY il.fecha_vencimiento ASC
        ");

        DB::statement("DROP VIEW IF EXISTS `view_ventas_diarias_detalladas`");
        DB::statement("
            CREATE VIEW `view_ventas_diarias_detalladas` AS 
            SELECT 
                CAST(p.creado_at AS DATE) AS fecha,
                COUNT(p.id) AS total_pedidos,
                SUM(p.total_usd) AS total_ventas_usd,
                SUM(p.total_ves_calculado) AS total_ventas_ves,
                COUNT(DISTINCT p.usuario_id) AS clientes_unicos,
                AVG(p.total_usd) AS ticket_promedio_usd 
            FROM pedidos p 
            WHERE p.estado IN ('pagado','entregado','completado_caja') 
            GROUP BY CAST(p.creado_at AS DATE) 
            ORDER BY CAST(p.creado_at AS DATE) DESC
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS `view_ventas_diarias_detalladas`");
        DB::statement("DROP VIEW IF EXISTS `view_productos_proximos_vencer`");
        DB::statement("DROP VIEW IF EXISTS `view_productos_mas_vendidos`");
        DB::statement("DROP VIEW IF EXISTS `view_inventario_valorado`");
    }
};