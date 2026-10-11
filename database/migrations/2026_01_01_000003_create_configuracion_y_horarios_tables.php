<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('configuracion_tienda')) {
            Schema::create('configuracion_tienda', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('nombre_empresa', 100)->nullable();
                $table->decimal('iva_porcentaje', 5, 2)->default(16.00);
                $table->enum('modo_operativo', ['automatico', 'manual_abierto', 'manual_cerrado'])->default('automatico');
                $table->boolean('delivery_activo')->default(true);
                $table->mediumText('mensaje_cierre_emergencia')->nullable();
                $table->integer('ultimo_editor_id')->nullable();
                $table->timestamp('actualizado_at')->useCurrent()->useCurrentOnUpdate();
                $table->decimal('latitud', 10, 7)->nullable();
                $table->decimal('longitud', 10, 7)->nullable();
            });
        }

        if (!Schema::hasTable('configuracion_api')) {
            Schema::create('configuracion_api', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('nombre_api', 100)->unique();
                $table->string('url_base', 255)->nullable();
                $table->string('api_key', 255)->nullable();
                $table->string('api_secret', 255)->nullable();
                $table->boolean('activo')->default(true);
                $table->timestamp('actualizado_at')->useCurrent()->useCurrentOnUpdate();
            });
        }

        if (!Schema::hasTable('horarios_semanales')) {
            Schema::create('horarios_semanales', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('dia_semana')->unique();
                $table->boolean('es_laborable')->default(true);
                $table->time('hora_apertura')->nullable();
                $table->time('hora_cierre')->nullable();
            });
        }

        if (!Schema::hasTable('horarios_especiales')) {
            Schema::create('horarios_especiales', function (Blueprint $table) {
                $table->integer('id', true);
                $table->date('fecha')->unique();
                $table->string('motivo', 100)->nullable();
                $table->boolean('es_laborable')->default(false);
                $table->time('hora_apertura')->nullable();
                $table->time('hora_cierre')->nullable();
            });
        }

        if (!Schema::hasTable('auditoria_logs')) {
            Schema::create('auditoria_logs', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('usuario_id')->nullable();
                $table->string('evento', 50)->nullable();
                $table->string('tabla_afectada', 50)->nullable();
                $table->integer('registro_id')->nullable();
                $table->longText('valores_anteriores')->nullable();
                $table->longText('valores_nuevos')->nullable();
                $table->string('direccion_ip', 45)->nullable();
                $table->timestamp('fecha')->useCurrent();
            });
        }

        if (!Schema::hasTable('estadisticas_ventas_diarias')) {
            Schema::create('estadisticas_ventas_diarias', function (Blueprint $table) {
                $table->integer('id', true);
                $table->date('fecha_reporte')->unique();
                $table->integer('total_pedidos')->default(0);
                $table->decimal('total_ingresos_usd', 15, 2)->default(0.00);
                $table->decimal('total_ingresos_ves', 15, 2)->default(0.00);
                $table->decimal('unidades_vendidas', 15, 3)->default(0.000);
                $table->timestamp('creado_at')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('estadisticas_ventas_diarias');
        Schema::dropIfExists('auditoria_logs');
        Schema::dropIfExists('horarios_especiales');
        Schema::dropIfExists('horarios_semanales');
        Schema::dropIfExists('configuracion_api');
        Schema::dropIfExists('configuracion_tienda');
    }
};