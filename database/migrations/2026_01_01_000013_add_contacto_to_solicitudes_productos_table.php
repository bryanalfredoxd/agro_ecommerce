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
        if (Schema::hasTable('solicitudes_productos')) {
            Schema::table('solicitudes_productos', function (Blueprint $table) {
                if (!Schema::hasColumn('solicitudes_productos', 'nombre_contacto')) {
                    $table->string('nombre_contacto', 150)->nullable()->after('usuario_id');
                }
                if (!Schema::hasColumn('solicitudes_productos', 'telefono_contacto')) {
                    $table->string('telefono_contacto', 50)->nullable()->after('nombre_contacto');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('solicitudes_productos')) {
            Schema::table('solicitudes_productos', function (Blueprint $table) {
                if (Schema::hasColumn('solicitudes_productos', 'telefono_contacto')) {
                    $table->dropColumn('telefono_contacto');
                }
                if (Schema::hasColumn('solicitudes_productos', 'nombre_contacto')) {
                    $table->dropColumn('nombre_contacto');
                }
            });
        }
    }
};
