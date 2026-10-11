<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cupones')) {
            Schema::create('cupones', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('codigo', 50)->unique();
                $table->enum('tipo_descuento', ['porcentaje', 'monto_fijo']);
                $table->decimal('valor_descuento', 15, 2);
                $table->decimal('compra_minima_usd', 15, 2)->default(0.00);
                $table->dateTime('fecha_inicio')->nullable();
                $table->dateTime('fecha_fin')->nullable();
                $table->integer('limite_uso_total')->default(1);
                $table->integer('veces_usado')->default(0);
                $table->boolean('activo')->default(true);
            });
        }

        if (!Schema::hasTable('tasas_cambio')) {
            Schema::create('tasas_cambio', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('codigo_moneda', 10)->default('USD');
                $table->string('moneda_base', 10)->default('VES');
                $table->decimal('valor_tasa', 15, 4);
                $table->enum('fuente', ['API', 'MANUAL'])->default('API');
                $table->integer('usuario_editor_id')->nullable()->index();
                $table->timestamp('creado_at')->useCurrent();

                $table->foreign('usuario_editor_id')->references('id')->on('usuarios');
            });
        }

        if (!Schema::hasTable('cuentas_bancarias')) {
            Schema::create('cuentas_bancarias', function (Blueprint $table) {
                $table->id();
                $table->string('nombre_titular')->nullable();
                $table->string('banco_entidad')->nullable();
                $table->string('numero_cuenta')->nullable();
                $table->string('telefono')->nullable();
                $table->string('identificacion')->nullable();
                $table->string('email')->nullable();
                $table->enum('tipo_metodo', ['pago_movil', 'zelle', 'efectivo_usd', 'efectivo_bs', 'transferencia', 'punto_venta', 'binance', 'biopago']);
                $table->text('instrucciones_adicionales')->nullable();
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('zonas_delivery')) {
            Schema::create('zonas_delivery', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('nombre_zona', 100)->nullable();
                $table->decimal('precio_delivery_usd', 10, 2);
                $table->integer('tiempo_estimado_minutos')->nullable();
                $table->boolean('requiere_vehiculo_carga')->default(false);
                $table->boolean('activa')->default(true);
                $table->decimal('latitud', 10, 7)->default(8.7622000);
                $table->decimal('longitud', 10, 7)->default(-70.4106000);
                $table->decimal('radio_km', 5, 2)->default(3.00);
                $table->string('color_mapa', 20)->default('#1e549e');
                $table->text('descripcion')->nullable();
            });
        }

        if (!Schema::hasTable('direcciones_usuarios')) {
            Schema::create('direcciones_usuarios', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('usuario_id')->index();
                $table->integer('zona_delivery_id')->nullable()->index();
                $table->string('alias', 50)->nullable();
                $table->mediumText('direccion_texto');
                $table->mediumText('referencia_punto')->nullable();
                $table->decimal('geo_latitud', 10, 8)->nullable();
                $table->decimal('geo_longitud', 11, 8)->nullable();
                $table->boolean('es_principal')->default(false);

                $table->foreign('usuario_id')->references('id')->on('usuarios')->cascadeOnDelete();
                $table->foreign('zona_delivery_id')->references('id')->on('zonas_delivery')->nullOnDelete();
            });
        }

        if (!Schema::hasTable('repartidores')) {
            Schema::create('repartidores', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('usuario_id')->nullable()->index();
                $table->enum('tipo_vehiculo', ['moto', 'carro', 'camion_carga', 'pickup'])->default('moto');
                $table->string('placa', 20)->nullable();
                $table->boolean('disponible')->default(true);

                $table->foreign('usuario_id')->references('id')->on('usuarios');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('repartidores');
        Schema::dropIfExists('direcciones_usuarios');
        Schema::dropIfExists('zonas_delivery');
        Schema::dropIfExists('cuentas_bancarias');
        Schema::dropIfExists('tasas_cambio');
        Schema::dropIfExists('cupones');
    }
};