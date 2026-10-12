<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('configuracion_tienda', function (Blueprint $table) {
            if (!Schema::hasColumn('configuracion_tienda', 'rif')) {
                $table->string('rif', 30)->nullable()->default('J-50493821-4')->after('nombre_empresa');
            }
            if (!Schema::hasColumn('configuracion_tienda', 'slogan')) {
                $table->string('slogan', 200)->nullable()->default('Insumos & Soluciones para el Campo')->after('rif');
            }
            if (!Schema::hasColumn('configuracion_tienda', 'descripcion_empresa')) {
                $table->text('descripcion_empresa')->nullable()->after('slogan');
            }
            if (!Schema::hasColumn('configuracion_tienda', 'telefono')) {
                $table->string('telefono', 50)->nullable()->default('+58 412-9614615')->after('descripcion_empresa');
            }
            if (!Schema::hasColumn('configuracion_tienda', 'whatsapp')) {
                $table->string('whatsapp', 50)->nullable()->default('+58 412-9614615')->after('telefono');
            }
            if (!Schema::hasColumn('configuracion_tienda', 'email')) {
                $table->string('email', 100)->nullable()->default('fitnessbigworld@gmail.com')->after('whatsapp');
            }
            if (!Schema::hasColumn('configuracion_tienda', 'direccion')) {
                $table->string('direccion', 255)->nullable()->default('Barinitas, Estado Barinas, Venezuela')->after('email');
            }
            if (!Schema::hasColumn('configuracion_tienda', 'horario_atencion')) {
                $table->string('horario_atencion', 150)->nullable()->default('Lun a Sáb: 8:00 AM - 5:00 PM')->after('direccion');
            }
            if (!Schema::hasColumn('configuracion_tienda', 'red_facebook')) {
                $table->string('red_facebook', 255)->nullable()->default('https://facebook.com')->after('horario_atencion');
            }
            if (!Schema::hasColumn('configuracion_tienda', 'red_instagram')) {
                $table->string('red_instagram', 255)->nullable()->default('https://instagram.com')->after('red_facebook');
            }
            if (!Schema::hasColumn('configuracion_tienda', 'red_tiktok')) {
                $table->string('red_tiktok', 255)->nullable()->after('red_instagram');
            }
        });

        // Asegurar que la fila existente (ID 1) contenga los valores por defecto si estaban nulos
        try {
            DB::table('configuracion_tienda')->where('id', 1)->update([
                'rif' => DB::raw("COALESCE(rif, 'J-50493821-4')"),
                'slogan' => DB::raw("COALESCE(slogan, 'Insumos & Soluciones para el Campo')"),
                'descripcion_empresa' => DB::raw("COALESCE(descripcion_empresa, 'Tu aliado estratégico en el desarrollo agrícola y pecuario. Ofrecemos fertilizantes, semillas certificadas, implementos y farmacología veterinaria con entrega confiable a nivel regional y nacional.')"),
                'telefono' => DB::raw("COALESCE(telefono, '+58 412-9614615')"),
                'whatsapp' => DB::raw("COALESCE(whatsapp, '+58 412-9614615')"),
                'email' => DB::raw("COALESCE(email, 'fitnessbigworld@gmail.com')"),
                'direccion' => DB::raw("COALESCE(direccion, 'Barinitas, Estado Barinas, Venezuela')"),
                'horario_atencion' => DB::raw("COALESCE(horario_atencion, 'Lun a Sáb: 8:00 AM - 5:00 PM')"),
                'red_facebook' => DB::raw("COALESCE(red_facebook, 'https://facebook.com')"),
                'red_instagram' => DB::raw("COALESCE(red_instagram, 'https://instagram.com')"),
            ]);
        } catch (\Throwable $e) {
            // Ignorar si aún no existe la fila
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configuracion_tienda', function (Blueprint $table) {
            $columnas = [
                'rif', 'slogan', 'descripcion_empresa', 'telefono', 'whatsapp', 
                'email', 'direccion', 'horario_atencion', 'red_facebook', 
                'red_instagram', 'red_tiktok'
            ];
            foreach ($columnas as $col) {
                if (Schema::hasColumn('configuracion_tienda', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
