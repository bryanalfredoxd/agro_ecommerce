<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pedidos')) {
            Schema::create('pedidos', function (Blueprint $table) {
                $table->integer('id', true);
                $table->enum('canal_venta', ['web', 'tienda_fisica', 'whatsapp'])->default('web');
                $table->integer('sesion_caja_id')->nullable()->index();
                $table->integer('usuario_id')->nullable()->index();
                $table->integer('zona_delivery_id')->nullable()->index();
                $table->integer('tasa_cambio_id')->nullable()->index();
                $table->integer('cupon_id')->nullable();
                $table->decimal('subtotal_usd', 15, 2)->nullable();
                $table->decimal('costo_delivery_usd', 15, 2)->default(0.00);
                $table->decimal('descuento_usd', 15, 2)->default(0.00);
                $table->decimal('total_usd', 15, 2)->nullable();
                $table->decimal('total_ves_calculado', 15, 2)->nullable();
                $table->enum('estado', ['pendiente', 'pagado', 'preparacion', 'en_ruta', 'entregado', 'devuelto', 'cancelado', 'completado_caja'])->default('pendiente')->index();
                $table->mediumText('direccion_texto')->nullable();
                $table->decimal('geo_latitud', 10, 8)->nullable();
                $table->decimal('geo_longitud', 11, 8)->nullable();
                $table->mediumText('instrucciones_entrega')->nullable();
                $table->mediumText('motivo_devolucion')->nullable();
                $table->timestamp('fecha_devolucion')->nullable();
                $table->integer('usuario_autorizo_devolucion_id')->nullable()->index();
                $table->timestamp('creado_at')->useCurrent();

                $table->foreign('usuario_id')->references('id')->on('usuarios');
                $table->foreign('sesion_caja_id')->references('id')->on('sesiones_caja');
                $table->foreign('zona_delivery_id')->references('id')->on('zonas_delivery');
                $table->foreign('tasa_cambio_id')->references('id')->on('tasas_cambio');
                $table->foreign('usuario_autorizo_devolucion_id')->references('id')->on('usuarios');
            });
        }

        if (!Schema::hasTable('recetas_veterinarias')) {
            Schema::create('recetas_veterinarias', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('pedido_id')->index();
                $table->integer('cliente_usuario_id')->index();
                $table->string('veterinario_nombre', 200);
                $table->string('veterinario_matricula', 50)->nullable();
                $table->string('cliente_animal_tipo', 100)->nullable();
                $table->integer('cliente_animal_cantidad')->nullable();
                $table->date('fecha_prescription')->nullable();
                $table->date('fecha_vencimiento_receta')->nullable();
                $table->string('archivo_url', 255)->nullable();
                $table->enum('estado', ['pendiente', 'aprobada', 'rechazada', 'expirada'])->default('pendiente');
                $table->mediumText('observaciones')->nullable();
                $table->integer('usuario_revisa_id')->nullable()->index();
                $table->timestamp('fecha_revision')->nullable();
                $table->timestamp('creado_at')->useCurrent();

                $table->foreign('pedido_id')->references('id')->on('pedidos');
                $table->foreign('cliente_usuario_id')->references('id')->on('usuarios');
                $table->foreign('usuario_revisa_id')->references('id')->on('usuarios');
            });
        }

        if (!Schema::hasTable('receta_productos')) {
            Schema::create('receta_productos', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('receta_id')->index();
                $table->integer('producto_id')->index();
                $table->decimal('cantidad_prescrita', 12, 3);
                $table->mediumText('dosis_instrucciones')->nullable();
                $table->boolean('autorizado')->default(false);

                $table->foreign('receta_id')->references('id')->on('recetas_veterinarias')->cascadeOnDelete();
                $table->foreign('producto_id')->references('id')->on('productos');
            });
        }

        if (!Schema::hasTable('pedido_detalles')) {
            Schema::create('pedido_detalles', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('pedido_id')->nullable()->index();
                $table->integer('producto_id')->nullable()->index();
                $table->integer('inventario_lote_id')->nullable()->index();
                $table->decimal('cantidad_solicitada', 12, 3);
                $table->decimal('cantidad_real_despachada', 12, 3)->nullable();
                $table->decimal('precio_historico_usd', 15, 2)->nullable();
                $table->string('observaciones', 255)->nullable();

                $table->foreign('pedido_id')->references('id')->on('pedidos')->cascadeOnDelete();
                $table->foreign('producto_id')->references('id')->on('productos');
                $table->foreign('inventario_lote_id')->references('id')->on('inventario_lotes');
            });
        }

        if (!Schema::hasTable('pagos')) {
            Schema::create('pagos', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('pedido_id')->nullable()->index();
                $table->enum('metodo', ['pago_movil', 'zelle', 'efectivo_usd', 'efectivo_bs', 'transferencia', 'punto_venta', 'binance', 'biopago'])->nullable();
                $table->string('referencia_bancaria', 100)->nullable();
                $table->decimal('monto_usd', 15, 2)->nullable();
                $table->decimal('monto_ves', 15, 2)->nullable();
                $table->string('captura_pago_url', 255)->nullable();
                $table->enum('estado', ['revision', 'aprobado', 'rechazado'])->default('revision');
                $table->integer('verificado_por_usuario_id')->nullable()->index();
                $table->timestamp('fecha_pago')->useCurrent();

                $table->foreign('pedido_id')->references('id')->on('pedidos');
                $table->foreign('verificado_por_usuario_id')->references('id')->on('usuarios');
            });
        }

        if (!Schema::hasTable('factura_ajustes')) {
            Schema::create('factura_ajustes', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('serie', 10);
                $table->integer('proximo_numero')->default(1);
                $table->decimal('porcentaje_iva', 5, 2)->default(16.00);
                $table->boolean('activo')->default(true);
            });
        }

        if (!Schema::hasTable('facturas')) {
            Schema::create('facturas', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('pedido_id')->nullable()->unique();
                $table->string('serie_usada', 10)->nullable();
                $table->string('numero_factura', 20)->unique();
                $table->string('cedula_rif_cliente', 20)->nullable();
                $table->string('nombre_razon_social', 200)->nullable();
                $table->mediumText('direccion_fiscal')->nullable();
                $table->decimal('subtotal_usd', 15, 2)->nullable();
                $table->decimal('impuesto_usd', 15, 2)->nullable();
                $table->decimal('total_usd', 15, 2)->nullable();
                $table->decimal('valor_tasa_bcv', 15, 4)->nullable();
                $table->decimal('total_ves', 15, 2)->nullable();
                $table->enum('estado', ['emitida', 'anulada', 'reembolsada'])->default('emitida');
                $table->timestamp('fecha_emision')->useCurrent();

                $table->foreign('pedido_id')->references('id')->on('pedidos');
            });
        }

        if (!Schema::hasTable('devoluciones_detalle')) {
            Schema::create('devoluciones_detalle', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('pedido_id')->index();
                $table->integer('pedido_detalle_id')->index();
                $table->integer('inventario_lote_id')->nullable()->index();
                $table->decimal('cantidad_devuelta', 12, 3);
                $table->enum('motivo_devolucion', ['producto_danado', 'no_corresponde', 'vencido', 'sobrante', 'error_despacho', 'cliente_arrepentido']);
                $table->enum('estado_producto', ['recibido', 'en_verificacion', 'reincorporado', 'descarte', 'devuelto_proveedor'])->default('recibido');
                $table->decimal('costo_reposicion_usd', 15, 4)->nullable();
                $table->mediumText('observaciones')->nullable();
                $table->integer('usuario_recibe_id')->nullable()->index();
                $table->timestamp('fecha_recepcion')->nullable();
                $table->integer('usuario_procesa_id')->nullable()->index();
                $table->timestamp('fecha_procesamiento')->nullable();

                $table->foreign('pedido_id')->references('id')->on('pedidos');
                $table->foreign('pedido_detalle_id')->references('id')->on('pedido_detalles');
                $table->foreign('inventario_lote_id')->references('id')->on('inventario_lotes');
                $table->foreign('usuario_recibe_id')->references('id')->on('usuarios');
                $table->foreign('usuario_procesa_id')->references('id')->on('usuarios');
            });
        }

        if (!Schema::hasTable('entrega_seguimiento')) {
            Schema::create('entrega_seguimiento', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('pedido_id')->nullable()->index();
                $table->integer('repartidor_id')->nullable()->index();
                $table->timestamp('hora_recogida')->nullable();
                $table->timestamp('hora_entrega')->nullable();
                $table->string('estado_rastreo', 100)->nullable();
                $table->mediumText('observaciones_entrega')->nullable();

                $table->foreign('pedido_id')->references('id')->on('pedidos');
                $table->foreign('repartidor_id')->references('id')->on('repartidores');
            });
        }

        if (!Schema::hasTable('favoritos')) {
            Schema::create('favoritos', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('usuario_id')->nullable();
                $table->integer('producto_id')->nullable()->index();
                $table->unique(['usuario_id', 'producto_id']);

                $table->foreign('usuario_id')->references('id')->on('usuarios');
                $table->foreign('producto_id')->references('id')->on('productos');
            });
        }

        if (!Schema::hasTable('carrito')) {
            Schema::create('carrito', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('usuario_id')->nullable()->index();
                $table->integer('producto_id')->nullable()->index();
                $table->decimal('cantidad', 12, 3)->default(1.000);
                $table->string('observaciones', 255)->nullable();
                $table->timestamp('actualizado_at')->useCurrent()->useCurrentOnUpdate();

                $table->foreign('usuario_id')->references('id')->on('usuarios');
                $table->foreign('producto_id')->references('id')->on('productos');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('carrito');
        Schema::dropIfExists('favoritos');
        Schema::dropIfExists('entrega_seguimiento');
        Schema::dropIfExists('devoluciones_detalle');
        Schema::dropIfExists('facturas');
        Schema::dropIfExists('factura_ajustes');
        Schema::dropIfExists('pagos');
        Schema::dropIfExists('pedido_detalles');
        Schema::dropIfExists('receta_productos');
        Schema::dropIfExists('recetas_veterinarias');
        Schema::dropIfExists('pedidos');
    }
};