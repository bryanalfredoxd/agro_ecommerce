<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('categorias')) {
            Schema::create('categorias', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('nombre', 100)->nullable();
                $table->string('imagen_url', 255)->nullable();
                $table->integer('categoria_padre_id')->nullable()->index();
                $table->timestamp('eliminado_at')->nullable();

                $table->foreign('categoria_padre_id')->references('id')->on('categorias')->nullOnDelete();
            });
        }

        if (!Schema::hasTable('marcas')) {
            Schema::create('marcas', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('nombre', 100)->unique();
                $table->string('pais_origen', 100)->nullable();
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('proveedores')) {
            Schema::create('proveedores', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('razon_social', 200)->nullable();
                $table->string('rif', 20)->nullable()->unique();
                $table->string('telefono', 20)->nullable();
                $table->string('persona_contacto', 100)->nullable();
                $table->boolean('activo')->default(true);
            });
        }

        if (!Schema::hasTable('productos')) {
            Schema::create('productos', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('categoria_id')->nullable()->index();
                $table->integer('marca_id')->nullable()->index();
                $table->integer('proveedor_defecto_id')->nullable()->index();
                $table->string('nombre', 255);
                $table->mediumText('descripcion')->nullable();
                $table->string('imagen_url', 255)->nullable();
                $table->string('sku', 100)->nullable()->unique();
                $table->string('codigo_barras', 100)->nullable()->unique();
                $table->decimal('costo_promedio_usd', 15, 2)->default(0.00);
                $table->decimal('precio_venta_usd', 15, 2);
                $table->decimal('precio_oferta_usd', 15, 2)->nullable();
                $table->enum('unidad_medida', ['unidad', 'kg', 'g', 'mg', 'litro', 'ml', 'galon', 'saco', 'bulto', 'paquete', 'metro', 'rollo', 'dosis'])->default('unidad');
                $table->decimal('contenido_neto', 10, 3)->nullable();
                $table->enum('unidad_contenido', ['kg', 'g', 'l', 'ml', 'unidad'])->default('kg');
                $table->boolean('es_controlado')->default(false);
                $table->longText('atributos_json')->nullable();
                $table->decimal('stock_total', 12, 3)->default(0.000);
                $table->decimal('stock_minimo_alerta', 12, 0)->default(5);
                $table->decimal('venta_minima', 10, 3)->default(1.000);
                $table->decimal('paso_venta', 10, 3)->default(1.000);
                $table->boolean('es_combo')->default(false);
                $table->boolean('destacado')->default(false);
                $table->timestamp('eliminado_at')->nullable();

                $table->foreign('categoria_id')->references('id')->on('categorias');
                $table->foreign('marca_id')->references('id')->on('marcas');
                $table->foreign('proveedor_defecto_id')->references('id')->on('proveedores');
            });
        }

        if (!Schema::hasTable('producto_imagenes')) {
            Schema::create('producto_imagenes', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('producto_id')->index();
                $table->string('url_imagen', 255)->nullable();
                $table->boolean('es_principal')->default(false);
                $table->integer('orden')->default(0);

                $table->foreign('producto_id')->references('id')->on('productos')->cascadeOnDelete();
            });
        }

        if (!Schema::hasTable('productos_compuestos')) {
            Schema::create('productos_compuestos', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('producto_padre_id')->index();
                $table->integer('producto_hijo_id')->index();
                $table->decimal('cantidad_requerida', 12, 3);

                $table->foreign('producto_padre_id')->references('id')->on('productos')->cascadeOnDelete();
                $table->foreign('producto_hijo_id')->references('id')->on('productos')->cascadeOnDelete();
            });
        }

        if (!Schema::hasTable('historico_precios_productos')) {
            Schema::create('historico_precios_productos', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('producto_id')->index();
                $table->decimal('precio_anterior_usd', 15, 2);
                $table->decimal('precio_nuevo_usd', 15, 2);
                $table->string('motivo_cambio', 255)->nullable();
                $table->integer('usuario_editor_id')->nullable()->index();
                $table->timestamp('creado_at')->useCurrent()->index();

                $table->foreign('producto_id')->references('id')->on('productos')->cascadeOnDelete();
                $table->foreign('usuario_editor_id')->references('id')->on('usuarios');
            });
        }

        if (!Schema::hasTable('solicitudes_productos')) {
            Schema::create('solicitudes_productos', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('usuario_id')->nullable()->index();
                $table->string('nombre_producto', 255);
                $table->mediumText('descripcion_adicional')->nullable();
                $table->enum('estado', ['pendiente', 'revisado', 'agregado', 'rechazado'])->default('pendiente')->index();
                $table->timestamp('fecha_peticion')->useCurrent();
                $table->timestamp('actualizado_at')->useCurrent()->useCurrentOnUpdate();

                $table->foreign('usuario_id')->references('id')->on('usuarios')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_productos');
        Schema::dropIfExists('historico_precios_productos');
        Schema::dropIfExists('productos_compuestos');
        Schema::dropIfExists('producto_imagenes');
        Schema::dropIfExists('productos');
        Schema::dropIfExists('proveedores');
        Schema::dropIfExists('marcas');
        Schema::dropIfExists('categorias');
    }
};