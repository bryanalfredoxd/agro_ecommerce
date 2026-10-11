<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('productos') && !Schema::hasColumn('productos', 'precio_incluye_iva')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->boolean('precio_incluye_iva')->default(0)->after('precio_oferta_usd');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('productos') && Schema::hasColumn('productos', 'precio_incluye_iva')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->dropColumn('precio_incluye_iva');
            });
        }
    }
};
