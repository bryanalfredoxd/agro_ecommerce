<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('usuarios')) {
            Schema::create('usuarios', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('rol_id')->nullable()->index();
                $table->string('nombre', 100)->nullable();
                $table->string('apellido', 100)->nullable();
                $table->string('email', 150)->nullable()->unique();
                $table->string('password_hash', 255)->nullable();
                $table->string('remember_token', 100)->nullable();
                $table->string('telefono', 20)->nullable();
                $table->string('documento_identidad', 20)->nullable()->index();
                $table->enum('tipo_cliente', ['natural', 'juridico', 'finca_productor'])->default('natural');
                $table->boolean('activo')->default(true);
                $table->timestamp('creado_at')->useCurrent();
                $table->timestamp('actualizado_at')->useCurrent()->useCurrentOnUpdate();

                $table->foreign('rol_id')->references('id')->on('roles');
            });
        }

        if (!Schema::hasTable('permisos_extra_usuario')) {
            Schema::create('permisos_extra_usuario', function (Blueprint $table) {
                $table->integer('usuario_id');
                $table->integer('permiso_id')->index();
                $table->enum('accion', ['permitir', 'denegar'])->default('permitir');
                $table->primary(['usuario_id', 'permiso_id']);
                $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
                $table->foreign('permiso_id')->references('id')->on('permisos')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('password_resets')) {
            Schema::create('password_resets', function (Blueprint $table) {
                $table->string('email', 150)->index();
                $table->string('token', 255);
                $table->timestamp('creado_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id', 255)->primary();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }

        if (!Schema::hasTable('personal_access_tokens')) {
            Schema::create('personal_access_tokens', function (Blueprint $table) {
                $table->id();
                $table->morphs('tokenable');
                $table->string('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('permisos_extra_usuario');
        Schema::dropIfExists('usuarios');
    }
};