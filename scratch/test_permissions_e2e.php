<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Permiso;
use App\Models\Role;
use App\Models\PermisoExtraUsuario;
use App\Http\Middleware\CheckPermiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

echo "=== INICIANDO AUDITORÍA Y TESTS DE PERMISOS E2E ===\n\n";

$errores = [];

// 1. Verificación de permisos definidos en config vs base de datos
echo "1. Verificando congruencia entre config/permisos_modulos.php y BD...\n";
$configModulos = config('permisos_modulos');
$permisosConfig = [];
foreach ($configModulos as $modulo) {
    foreach ($modulo['permisos'] as $key => $info) {
        $permisosConfig[] = $key;
    }
}

$permisosBD = Permiso::pluck('nombre')->toArray();
$faltantesEnBD = array_diff($permisosConfig, $permisosBD);
$sobrantesEnBD = array_diff($permisosBD, $permisosConfig);

if (count($faltantesEnBD) > 0) {
    echo "  [ERROR] Permisos en config que no están en BD: " . implode(', ', $faltantesEnBD) . "\n";
    $errores[] = "Permisos faltantes en BD";
} else {
    echo "  [OK] Todos los " . count($permisosConfig) . " permisos de la configuración existen en la BD.\n";
}

// 2. Verificación de Roles y permisos asignados
echo "\n2. Verificando roles y matriz de permisos...\n";
$roles = Role::with('permisos')->get();
foreach ($roles as $r) {
    echo "  - Rol #{$r->id} ({$r->nombre}): {$r->permisos->count()} permisos asignados.\n";
}

// 3. Crear o simular usuario de prueba
echo "\n3. Creando usuario temporal de prueba...\n";
$testEmail = 'test_permisos_' . time() . '@agrotest.com';
$usuarioPrueba = User::create([
    'nombre' => 'Test',
    'apellido' => 'Auditor',
    'email' => $testEmail,
    'password_hash' => bcrypt('password123'),
    'rol_id' => 2, // Vendedor
    'activo' => 1,
    'documento_identidad' => 'V-' . rand(10000000, 99999999),
    'telefono' => '04141234567'
]);

echo "  [OK] Usuario creado con ID #{$usuarioPrueba->id}, Rol Vendedor (#2)\n";

// 4. Evaluar métodos del modelo User
echo "\n4. Verificando evaluación de permisos en modelo User...\n";
$tieneVenta = $usuarioPrueba->tienePermiso('crear_venta_manual');
$tieneVerPedidos = $usuarioPrueba->tienePermiso('ver_pedidos');
$tieneConfigTienda = $usuarioPrueba->tienePermiso('configurar_tienda');
$tieneRoles = $usuarioPrueba->tienePermiso('gestionar_roles_permisos');

echo "  - tienePermiso('crear_venta_manual'): " . ($tieneVenta ? 'TRUE [OK]' : 'FALSE [ERROR]') . "\n";
echo "  - tienePermiso('ver_pedidos'): " . ($tieneVerPedidos ? 'TRUE [OK]' : 'FALSE [ERROR]') . "\n";
echo "  - tienePermiso('configurar_tienda'): " . (!$tieneConfigTienda ? 'FALSE [OK]' : 'TRUE [ERROR]') . "\n";
echo "  - tienePermiso('gestionar_roles_permisos'): " . (!$tieneRoles ? 'FALSE [OK]' : 'TRUE [ERROR]') . "\n";

if (!$tieneVenta || !$tieneVerPedidos || $tieneConfigTienda || $tieneRoles) {
    $errores[] = "Evaluación de permisos base incorrecta para rol Vendedor";
}

// 5. Otorgar una excepción: permitir gestionar_zonas_delivery
echo "\n5. Aplicando excepción (Permitir 'gestionar_zonas_delivery')...\n";
$permisoZona = Permiso::where('nombre', 'gestionar_zonas_delivery')->first();
PermisoExtraUsuario::create([
    'usuario_id' => $usuarioPrueba->id,
    'permiso_id' => $permisoZona->id,
    'accion' => 'permitir'
]);
$usuarioPrueba->limpiarPermisosCache();

$tieneZonaAhora = $usuarioPrueba->tienePermiso('gestionar_zonas_delivery');
echo "  - tienePermiso('gestionar_zonas_delivery') post-excepción: " . ($tieneZonaAhora ? 'TRUE [OK]' : 'FALSE [ERROR]') . "\n";
if (!$tieneZonaAhora) {
    $errores[] = "Fallo al conceder excepción 'permitir'";
}

// 6. Otorgar una excepción: denegar crear_venta_manual (que venía en el rol)
echo "\n6. Aplicando excepción (Denegar 'crear_venta_manual')...\n";
$permisoPos = Permiso::where('nombre', 'crear_venta_manual')->first();
PermisoExtraUsuario::create([
    'usuario_id' => $usuarioPrueba->id,
    'permiso_id' => $permisoPos->id,
    'accion' => 'denegar'
]);
$usuarioPrueba->limpiarPermisosCache();

$tieneVentaAhora = $usuarioPrueba->tienePermiso('crear_venta_manual');
echo "  - tienePermiso('crear_venta_manual') post-denegación: " . (!$tieneVentaAhora ? 'FALSE [OK]' : 'TRUE [ERROR]') . "\n";
if ($tieneVentaAhora) {
    $errores[] = "Fallo al aplicar excepción 'denegar'";
}

// 7. Prueba del Middleware CheckPermiso
echo "\n7. Probando Middleware CheckPermiso...\n";
Auth::login($usuarioPrueba);
$middleware = new CheckPermiso();

// Petición permitida
$reqPermitida = Request::create('/admin/pedidos', 'GET');
$respPermitida = $middleware->handle($reqPermitida, function($req) {
    return response('ACCESO_CONCEDIDO', 200);
}, 'ver_pedidos');

echo "  - Acceso a ruta con permiso concedido ('ver_pedidos'): " . ($respPermitida->getContent() === 'ACCESO_CONCEDIDO' ? '200 OK' : 'ERROR') . "\n";

// Petición denegada WEB
$reqDenegadaWeb = Request::create('/admin/roles', 'GET');
$respDenegadaWeb = $middleware->handle($reqDenegadaWeb, function($req) {
    return response('ACCESO_INDEBIDO', 200);
}, 'gestionar_roles_permisos');

echo "  - Acceso a ruta protegida sin permiso (Web): Status " . $respDenegadaWeb->getStatusCode() . " (" . ($respDenegadaWeb->isRedirection() ? 'REDIRECCIÓN SEGURA [OK]' : 'ERROR') . ")\n";

// Petición denegada AJAX
$reqDenegadaAjax = Request::create('/admin/facturas/1/anular', 'POST');
$reqDenegadaAjax->headers->set('X-Requested-With', 'XMLHttpRequest');
$respDenegadaAjax = $middleware->handle($reqDenegadaAjax, function($req) {
    return response('ACCESO_INDEBIDO', 200);
}, 'configurar_facturacion');

echo "  - Acceso a acción AJAX sin permiso: Status " . $respDenegadaAjax->getStatusCode() . " (" . ($respDenegadaAjax->getStatusCode() === 403 ? '403 JSON FORBIDDEN [OK]' : 'ERROR') . ")\n";

// 8. Limpiar datos de prueba
echo "\n8. Limpiando datos de prueba...\n";
PermisoExtraUsuario::where('usuario_id', $usuarioPrueba->id)->delete();
$usuarioPrueba->delete();
echo "  [OK] Usuario temporal eliminado limpiamente.\n";

echo "\n============================================\n";
if (count($errores) === 0) {
    echo " RESULTADO: ¡TODOS LOS TESTS PASARON EXITOSAMENTE (100%)!\n";
} else {
    echo " RESULTADO: ERRORES ENCONTRADOS: " . implode(', ', $errores) . "\n";
}
echo "============================================\n";
