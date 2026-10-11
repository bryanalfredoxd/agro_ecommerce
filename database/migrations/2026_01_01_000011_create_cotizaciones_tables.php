<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cotizaciones')) {
            Schema::create('cotizaciones', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('numero_cotizacion', 30)->unique();
                $table->integer('vendedor_id')->nullable()->index();
                $table->integer('cliente_id')->nullable()->index();
                $table->string('cliente_nombre', 200);
                $table->string('cliente_documento', 30)->nullable();
                $table->string('cliente_telefono', 50)->nullable();
                $table->string('cliente_email', 150)->nullable();
                $table->mediumText('cliente_direccion')->nullable();
                $table->integer('tasa_cambio_id')->nullable()->index();
                $table->decimal('valor_tasa_bcv', 15, 4)->default(1.0000);
                $table->decimal('subtotal_usd', 15, 2)->default(0.00);
                $table->decimal('iva_porcentaje', 5, 2)->default(0.00);
                $table->decimal('iva_usd', 15, 2)->default(0.00);
                $table->decimal('total_usd', 15, 2)->default(0.00);
                $table->decimal('total_ves', 15, 2)->default(0.00);
                $table->integer('validez_dias')->default(7);
                $table->timestamp('fecha_emision')->useCurrent();
                $table->timestamp('fecha_vencimiento')->nullable();
                $table->enum('estado', ['pendiente', 'aprobada', 'rechazada', 'vencida'])->default('pendiente')->index();
                $table->mediumText('observaciones')->nullable();
                $table->timestamp('creado_at')->useCurrent();
                $table->timestamp('actualizado_at')->nullable()->useCurrentOnUpdate();

                $table->foreign('vendedor_id')->references('id')->on('usuarios')->nullOnDelete();
                $table->foreign('cliente_id')->references('id')->on('usuarios')->nullOnDelete();
                $table->foreign('tasa_cambio_id')->references('id')->on('tasas_cambio')->nullOnDelete();
            });
        }

        if (!Schema::hasTable('cotizacion_detalles')) {
            Schema::create('cotizacion_detalles', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('cotizacion_id')->index();
                $table->integer('producto_id')->nullable()->index();
                $table->string('descripcion', 255);
                $table->decimal('cantidad', 10, 2)->default(1.00);
                $table->string('unidad_medida', 30)->nullable()->default('ud');
                $table->decimal('precio_unitario_usd', 15, 2)->default(0.00);
                $table->decimal('total_usd', 15, 2)->default(0.00);
                $table->timestamp('creado_at')->useCurrent();

                $table->foreign('cotizacion_id')->references('id')->on('cotizaciones')->cascadeOnDelete();
                $table->foreign('producto_id')->references('id')->on('productos')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizacion_detalles');
        Schema::dropIfExists('cotizaciones');
    }
};
