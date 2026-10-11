<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('nombre', 50)->unique();
            });
        }

        if (!Schema::hasTable('permisos')) {
            Schema::create('permisos', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('nombre', 100)->unique();
                $table->mediumText('descripcion')->nullable();
                $table->timestamp('creado_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('rol_permisos')) {
            Schema::create('rol_permisos', function (Blueprint $table) {
                $table->integer('rol_id');
                $table->integer('permiso_id');
                $table->primary(['rol_id', 'permiso_id']);
                $table->foreign('rol_id')->references('id')->on('roles')->onDelete('cascade');
                $table->foreign('permiso_id')->references('id')->on('permisos')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rol_permisos');
        Schema::dropIfExists('permisos');
        Schema::dropIfExists('roles');
    }
};