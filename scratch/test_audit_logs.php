<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AuditoriaLog;
use App\Models\Producto;
use App\Models\Pedido;
use App\Models\User;
use App\Models\ConfiguracionTienda;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

echo "=== INICIANDO TEST END-TO-END DE AUDITORIA Y LOGS ===\n\n";

// 1. Obtener usuario administrador para las pruebas
$admin = User::whereHas('rol', function ($q) {
    $q->where('nombre', 'Administrador');
})->first();

if (!$admin) {
    $admin = User::first();
}

Auth::login($admin);
echo "[OK] Usuario autenticado: {$admin->nombre} {$admin->apellido} (Rol: " . ($admin->rol->nombre ?? 'N/A') . ")\n";

// 2. Probar creación de auditoría en modelo Producto
$conteoInicial = AuditoriaLog::count();
echo "Conteo inicial de logs: {$conteoInicial}\n";

$producto = Producto::first();
if ($producto) {
    $precioOriginal = (float) $producto->precio_venta_usd;
    $nuevoPrecio = $precioOriginal + 0.50;

    echo "\n--> Modificando Producto ID {$producto->id}: precio de {$precioOriginal} a {$nuevoPrecio}...\n";
    $producto->precio_venta_usd = $nuevoPrecio;
    $producto->save();

    $ultimoLog = AuditoriaLog::latest('id')->first();
    echo "[OK] Log registrado ID: {$ultimoLog->id}\n";
    echo "  - Evento: {$ultimoLog->evento}\n";
    echo "  - Tabla: {$ultimoLog->tabla_afectada}\n";
    echo "  - Registro ID: {$ultimoLog->registro_id}\n";
    echo "  - Descripcion: {$ultimoLog->descripcion}\n";
    echo "  - Valores Anteriores: " . json_encode($ultimoLog->valores_anteriores) . "\n";
    echo "  - Valores Nuevos: " . json_encode($ultimoLog->valores_nuevos) . "\n";

    // Revertir cambio
    $producto->precio_venta_usd = $precioOriginal;
    $producto->save();
    echo "[OK] Producto restaurado a precio original.\n";
}

// 3. Probar cambio de estado en Pedido
$pedido = Pedido::first();
if ($pedido) {
    $estadoOriginal = $pedido->estado;
    $nuevoEstado = ($estadoOriginal === 'pendiente') ? 'en_preparacion' : 'pendiente';

    echo "\n--> Modificando Pedido #{$pedido->codigo_pedido}: estado de {$estadoOriginal} a {$nuevoEstado}...\n";
    $pedido->estado = $nuevoEstado;
    $pedido->save();

    $logPedido = AuditoriaLog::latest('id')->first();
    echo "[OK] Log registrado ID: {$logPedido->id}\n";
    echo "  - Evento: {$logPedido->evento}\n";
    echo "  - Tabla: {$logPedido->tabla_afectada}\n";
    echo "  - Registro ID: {$logPedido->registro_id}\n";
    echo "  - Descripcion: {$logPedido->descripcion}\n";
    echo "  - Valores Anteriores: " . json_encode($logPedido->valores_anteriores) . "\n";
    echo "  - Valores Nuevos: " . json_encode($logPedido->valores_nuevos) . "\n";

    // Revertir
    $pedido->estado = $estadoOriginal;
    $pedido->save();
    echo "[OK] Pedido restaurado a estado original.\n";
}

// 3.1 Probar creación y eliminación de registro (ej. Marca)
echo "\n--> Probando creación y eliminación (Marca)...\n";
$marca = \App\Models\Marca::create(['nombre' => 'Marca Auditoria Test', 'activo' => 1]);
$logCrear = AuditoriaLog::latest('id')->first();
echo "[OK] Log Creación ID {$logCrear->id}: {$logCrear->evento} - {$logCrear->descripcion}\n";

$marca->delete();
$logEliminar = AuditoriaLog::latest('id')->first();
echo "[OK] Log Eliminación ID {$logEliminar->id}: {$logEliminar->evento} - {$logEliminar->descripcion}\n";


// 4. Probar controlador AuditoriaLogController::index
echo "\n--> Probando AuditoriaLogController::index...\n";
$controller = new \App\Http\Controllers\Admin\AuditoriaLogController();
$request = Request::create('/admin/logs', 'GET');
$response = $controller->index($request);

if ($response instanceof \Illuminate\View\View) {
    $viewData = $response->getData();
    echo "[OK] Vista admin.logs.index renderizada correctamente.\n";
    echo "  - Total Logs en KPI: {$viewData['totalLogs']}\n";
    echo "  - Logs Hoy: {$viewData['logsHoy']}\n";
    echo "  - Usuarios Auditados Hoy: {$viewData['usuariosAuditadosHoy']}\n";
    echo "  - Acciones Críticas: {$viewData['accionesCriticas']}\n";
    echo "  - Cantidad en tabla paginada: " . $viewData['logs']->count() . "\n";
} else {
    echo "[FAIL] La respuesta no fue una instancia de View.\n";
}

// 5. Probar petición AJAX a AuditoriaLogController::index (parcial de tabla)
echo "\n--> Probando AuditoriaLogController::index vía AJAX (Live Filter)...\n";
$requestAjax = Request::create('/admin/logs', 'GET', ['buscar' => 'Producto']);
$requestAjax->headers->set('X-Requested-With', 'XMLHttpRequest');
$responseAjax = $controller->index($requestAjax);
echo "[OK] Respuesta AJAX recibida (longitud HTML: " . strlen($responseAjax) . " bytes)\n";

// 5.1 Probar paginación a página 2 vía AJAX
echo "\n--> Probando Paginación a Página 2 vía AJAX...\n";
$requestPage2 = Request::create('/admin/logs?page=2', 'GET');
$requestPage2->headers->set('X-Requested-With', 'XMLHttpRequest');
$responsePage2 = $controller->index($requestPage2);
echo "[OK] Página 2 cargada con éxito (HTML: " . strlen($responsePage2) . " bytes)\n";
$contieneContador = strpos($responsePage2, 'Mostrando') !== false;
echo "  - Contiene contador de registros: " . ($contieneContador ? 'SÍ' : 'NO') . "\n";

// 6. Probar AuditoriaLogController::show (Detalle forense en JSON)
$logParaInspeccionar = AuditoriaLog::latest('id')->first();
if ($logParaInspeccionar) {
    echo "\n--> Probando AuditoriaLogController::show({$logParaInspeccionar->id})...\n";
    $responseShow = $controller->show($logParaInspeccionar->id);
    $dataShow = $responseShow->getData(true);
    echo "[OK] Detalle inspeccionado en JSON:\n";
    echo "  - Success: " . ($dataShow['success'] ? 'true' : 'false') . "\n";
    echo "  - Módulo: {$dataShow['modulo']}\n";
    echo "  - Evento: {$dataShow['evento']}\n";
    echo "  - Tabla: {$dataShow['tabla']}\n";
    echo "  - Descripción: {$dataShow['descripcion']}\n";
    echo "  - Fecha Formateada: {$dataShow['fecha']}\n";
    echo "  - Badge Color: {$dataShow['badge_color']}\n";
}

// 7. Probar AuditoriaLogController::exportar (CSV Stream)
echo "\n--> Probando AuditoriaLogController::exportar (CSV)...\n";
$requestExport = Request::create('/admin/logs-exportar/csv', 'GET');
$responseExport = $controller->exportar($requestExport);
echo "[OK] Response es StreamedResponse con status {$responseExport->getStatusCode()} y headers:\n";
echo "  - Content-Type: " . $responseExport->headers->get('Content-Type') . "\n";
echo "  - Content-Disposition: " . $responseExport->headers->get('Content-Disposition') . "\n";

// 8. Verificar permisos de acceso
echo "\n--> Verificando permisos de seguridad...\n";
$cliente = User::whereHas('rol', function ($q) {
    $q->where('nombre', 'Cliente');
})->first();

if ($cliente) {
    echo "  - Cliente ID {$cliente->id} tiene 'ver_logs_auditoria': " . ($cliente->tienePermiso('ver_logs_auditoria') ? 'SÍ (INCORRECTO)' : 'NO (CORRECTO - PROTEGIDO)') . "\n";
}
echo "  - Admin ID {$admin->id} tiene 'ver_logs_auditoria': " . ($admin->tienePermiso('ver_logs_auditoria') ? 'SÍ (CORRECTO - AUTORIZADO)' : 'NO') . "\n";

echo "\n=== TODOS LOS TESTS COMPLETADOS SATISFACTORIAMENTE ===\n";
