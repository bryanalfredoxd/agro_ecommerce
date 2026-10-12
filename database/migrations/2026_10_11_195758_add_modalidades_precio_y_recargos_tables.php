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
        // 1. Columnas en configuracion_tienda
        if (Schema::hasTable('configuracion_tienda')) {
            Schema::table('configuracion_tienda', function (Blueprint $table) {
                if (!Schema::hasColumn('configuracion_tienda', 'recargo_divisas_pct')) {
                    $table->decimal('recargo_divisas_pct', 5, 2)->default(0.00)->after('iva_porcentaje');
                }
                if (!Schema::hasColumn('configuracion_tienda', 'recargo_divisas_aplica')) {
                    $table->string('recargo_divisas_aplica', 20)->default('ambas')->after('recargo_divisas_pct');
                }
                if (!Schema::hasColumn('configuracion_tienda', 'recargo_bolivares_pct')) {
                    $table->decimal('recargo_bolivares_pct', 5, 2)->default(0.00)->after('recargo_divisas_aplica');
                }
                if (!Schema::hasColumn('configuracion_tienda', 'recargo_bolivares_aplica')) {
                    $table->string('recargo_bolivares_aplica', 20)->default('ambas')->after('recargo_bolivares_pct');
                }
                if (!Schema::hasColumn('configuracion_tienda', 'recargo_cashea_pct')) {
                    $table->decimal('recargo_cashea_pct', 5, 2)->default(0.00)->after('recargo_bolivares_aplica');
                }
                if (!Schema::hasColumn('configuracion_tienda', 'recargo_cashea_aplica')) {
                    $table->string('recargo_cashea_aplica', 20)->default('ambas')->after('recargo_cashea_pct');
                }
            });
        }

        // 2. Columnas en cuentas_bancarias
        if (Schema::hasTable('cuentas_bancarias')) {
            // Modificar tipo_metodo a VARCHAR(50) para permitir 'cashea'
            try {
                DB::statement("ALTER TABLE cuentas_bancarias MODIFY COLUMN tipo_metodo VARCHAR(50) NOT NULL");
            } catch (\Exception $e) {
                // Si el driver no es MySQL directo, intentar mediante schema
            }

            Schema::table('cuentas_bancarias', function (Blueprint $table) {
                if (!Schema::hasColumn('cuentas_bancarias', 'modalidad_pago')) {
                    $table->string('modalidad_pago', 20)->default('bolivares')->after('tipo_metodo');
                }
            });

            // Actualizar modalidades por defecto para los métodos existentes
            DB::table('cuentas_bancarias')
                ->whereIn('tipo_metodo', ['zelle', 'binance', 'efectivo_usd'])
                ->update(['modalidad_pago' => 'divisas']);

            DB::table('cuentas_bancarias')
                ->whereIn('tipo_metodo', ['pago_movil', 'transferencia', 'punto_venta', 'biopago', 'efectivo_bs'])
                ->update(['modalidad_pago' => 'bolivares']);

            DB::table('cuentas_bancarias')
                ->where('tipo_metodo', 'cashea')
                ->update(['modalidad_pago' => 'cashea']);
        }

        // 3. Modificar columna metodo en pagos para soportar 'cashea'
        if (Schema::hasTable('pagos')) {
            try {
                DB::statement("ALTER TABLE pagos MODIFY COLUMN metodo VARCHAR(50) NULL");
            } catch (\Exception $e) {
                // Continúa
            }
        }

        // 4. Columnas en pedidos
        if (Schema::hasTable('pedidos')) {
            Schema::table('pedidos', function (Blueprint $table) {
                if (!Schema::hasColumn('pedidos', 'modalidad_pago')) {
                    $table->string('modalidad_pago', 20)->nullable()->after('tasa_cambio_id');
                }
                if (!Schema::hasColumn('pedidos', 'recargo_porcentaje')) {
                    $table->decimal('recargo_porcentaje', 5, 2)->default(0.00)->after('descuento_usd');
                }
                if (!Schema::hasColumn('pedidos', 'recargo_usd')) {
                    $table->decimal('recargo_usd', 15, 2)->default(0.00)->after('recargo_porcentaje');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('configuracion_tienda')) {
            Schema::table('configuracion_tienda', function (Blueprint $table) {
                $columns = [
                    'recargo_divisas_pct', 'recargo_divisas_aplica',
                    'recargo_bolivares_pct', 'recargo_bolivares_aplica',
                    'recargo_cashea_pct', 'recargo_cashea_aplica'
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('configuracion_tienda', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('cuentas_bancarias')) {
            Schema::table('cuentas_bancarias', function (Blueprint $table) {
                if (Schema::hasColumn('cuentas_bancarias', 'modalidad_pago')) {
                    $table->dropColumn('modalidad_pago');
                }
            });
        }

        if (Schema::hasTable('pedidos')) {
            Schema::table('pedidos', function (Blueprint $table) {
                $cols = ['modalidad_pago', 'recargo_porcentaje', 'recargo_usd'];
                foreach ($cols as $c) {
                    if (Schema::hasColumn('pedidos', $c)) {
                        $table->dropColumn($c);
                    }
                }
            });
        }
    }
};
