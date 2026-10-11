<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('inventario_lotes')) {
            Schema::create('inventario_lotes', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('producto_id')->index();
                $table->integer('proveedor_id')->nullable()->index();
                $table->string('numero_lote', 50)->nullable();
                $table->date('fecha_vencimiento')->index();
                $table->decimal('cantidad_inicial', 12, 3);
                $table->decimal('cantidad_restante', 12, 3);
                $table->decimal('costo_unitario_usd', 15, 4);
                $table->string('ubicacion_almacen', 50)->nullable();
                $table->boolean('activo')->default(true);
                $table->timestamp('creado_at')->useCurrent();

                $table->foreign('producto_id')->references('id')->on('productos');
                $table->foreign('proveedor_id')->references('id')->on('proveedores');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario_lotes');
    }
};