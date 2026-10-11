<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cajas_fisicas')) {
            Schema::create('cajas_fisicas', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('nombre', 50);
                $table->integer('cajero_asignado_id')->nullable()->index();
                $table->boolean('activa')->default(true);

                $table->foreign('cajero_asignado_id')->references('id')->on('usuarios')->nullOnDelete();
            });
        }

        if (!Schema::hasTable('sesiones_caja')) {
            Schema::create('sesiones_caja', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('caja_id')->index();
                $table->integer('cajero_usuario_id')->index();
                $table->timestamp('fecha_apertura')->useCurrent();
                $table->timestamp('fecha_cierre')->nullable();
                $table->decimal('monto_inicial_usd', 15, 2);
                $table->decimal('total_ventas_sistema_usd', 15, 2)->default(0.00);
                $table->decimal('total_ventas_sistema_ves', 15, 2)->default(0.00);
                $table->decimal('dinero_real_en_caja_usd', 15, 2)->nullable();
                $table->decimal('dinero_real_en_caja_ves', 15, 2)->nullable();
                $table->decimal('diferencia_usd', 15, 2)->nullable();
                $table->mediumText('observaciones_cierre')->nullable();

                $table->foreign('caja_id')->references('id')->on('cajas_fisicas');
                $table->foreign('cajero_usuario_id')->references('id')->on('usuarios');
            });
        }

        if (!Schema::hasTable('movimientos_caja')) {
            Schema::create('movimientos_caja', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('sesion_caja_id')->index();
                $table->enum('tipo', ['ingreso', 'egreso']);
                $table->string('motivo', 255)->nullable();
                $table->decimal('monto_usd', 15, 2)->default(0.00);
                $table->decimal('monto_ves', 15, 2)->default(0.00);
                $table->timestamp('creado_at')->useCurrent();

                $table->foreign('sesion_caja_id')->references('id')->on('sesiones_caja');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_caja');
        Schema::dropIfExists('sesiones_caja');
        Schema::dropIfExists('cajas_fisicas');
    }
};