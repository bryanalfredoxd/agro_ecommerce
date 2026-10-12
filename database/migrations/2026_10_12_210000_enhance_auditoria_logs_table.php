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
        Schema::table('auditoria_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('auditoria_logs', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('evento');
            }
            if (!Schema::hasColumn('auditoria_logs', 'url')) {
                $table->text('url')->nullable()->after('direccion_ip');
            }
            if (!Schema::hasColumn('auditoria_logs', 'metodo_http')) {
                $table->string('metodo_http', 10)->nullable()->after('url');
            }
            if (!Schema::hasColumn('auditoria_logs', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('metodo_http');
            }
        });

        // Asegurar que evento admita cadenas más largas
        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE auditoria_logs MODIFY evento VARCHAR(100) NULL");
        } catch (\Throwable $e) {
            // Ignorar si no se soporta en el motor
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auditoria_logs', function (Blueprint $table) {
            $table->dropColumn(['descripcion', 'url', 'metodo_http', 'user_agent']);
        });
    }
};
