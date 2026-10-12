<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('devoluciones')) {
            Schema::create('devoluciones', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('codigo_devolucion', 30)->unique();
                $table->integer('factura_id')->nullable()->index();
                $table->integer('pedido_id')->index();
                $table->integer('cliente_id')->nullable()->index();
                $table->integer('usuario_id')->index(); // Usuario admin / cajero que procesa
                $table->enum('tipo_devolucion', ['total', 'parcial'])->default('parcial');
                $table->decimal('monto_total_usd', 15, 2)->default(0.00);
                $table->decimal('monto_total_ves', 15, 2)->default(0.00);
                $table->decimal('tasa_bcv', 15, 4)->default(1.0000);
                $table->enum('metodo_reembolso', [
                    'efectivo_usd', 'efectivo_bs', 'pago_movil', 
                    'transferencia', 'punto_venta', 'nota_credito', 'otro'
                ])->default('efectivo_usd');
                $table->string('motivo_general', 255);
                $table->mediumText('observaciones')->nullable();
                $table->boolean('reingresar_stock')->default(true);
                $table->enum('estado', ['procesada', 'anulada'])->default('procesada');
                $table->timestamp('fecha_devolucion')->useCurrent();
                $table->timestamps();

                $table->foreign('factura_id')->references('id')->on('facturas')->nullOnDelete();
                $table->foreign('pedido_id')->references('id')->on('pedidos')->cascadeOnDelete();
                $table->foreign('cliente_id')->references('id')->on('usuarios')->nullOnDelete();
                $table->foreign('usuario_id')->references('id')->on('usuarios');
            });
        }

        if (Schema::hasTable('devoluciones_detalle')) {
            Schema::table('devoluciones_detalle', function (Blueprint $table) {
                if (!Schema::hasColumn('devoluciones_detalle', 'devolucion_id')) {
                    $table->integer('devolucion_id')->nullable()->after('id')->index();
                    $table->foreign('devolucion_id')->references('id')->on('devoluciones')->cascadeOnDelete();
                }
                if (!Schema::hasColumn('devoluciones_detalle', 'producto_id')) {
                    $table->integer('producto_id')->nullable()->after('pedido_detalle_id')->index();
                    $table->foreign('producto_id')->references('id')->on('productos')->nullOnDelete();
                }
                if (!Schema::hasColumn('devoluciones_detalle', 'precio_unitario_usd')) {
                    $table->decimal('precio_unitario_usd', 15, 2)->default(0.00)->after('cantidad_devuelta');
                }
                if (!Schema::hasColumn('devoluciones_detalle', 'monto_subtotal_usd')) {
                    $table->decimal('monto_subtotal_usd', 15, 2)->default(0.00)->after('precio_unitario_usd');
                }
                if (!Schema::hasColumn('devoluciones_detalle', 'created_at')) {
                    $table->timestamps();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('devoluciones_detalle')) {
            Schema::table('devoluciones_detalle', function (Blueprint $table) {
                if (Schema::hasColumn('devoluciones_detalle', 'devolucion_id')) {
                    $table->dropForeign(['devolucion_id']);
                    $table->dropColumn('devolucion_id');
                }
                if (Schema::hasColumn('devoluciones_detalle', 'producto_id')) {
                    $table->dropForeign(['producto_id']);
                    $table->dropColumn('producto_id');
                }
                if (Schema::hasColumn('devoluciones_detalle', 'precio_unitario_usd')) {
                    $table->dropColumn('precio_unitario_usd');
                }
                if (Schema::hasColumn('devoluciones_detalle', 'monto_subtotal_usd')) {
                    $table->dropColumn('monto_subtotal_usd');
                }
            });
        }

        Schema::dropIfExists('devoluciones');
    }
};
