<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('rol_id', 1)->first() ?: App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($user);

echo "1. Probando CajaDiariaController@index...\n";
$cajaCtrl = new App\Http\Controllers\Admin\CajaDiariaController();
$req = new \Illuminate\Http\Request();
$viewIndex = $cajaCtrl->index($req);
$renderedIndex = $viewIndex->render();
echo "-> Vista index renderizada correctamente (" . strlen($renderedIndex) . " bytes)\n";

echo "2. Probando tab cajas AJAX...\n";
$reqCajas = new \Illuminate\Http\Request(['tab' => 'cajas']);
$reqCajas->headers->set('X-Requested-With', 'XMLHttpRequest');
$viewCajas = $cajaCtrl->index($reqCajas);
echo "-> Tab cajas renderizado correctamente (" . strlen($viewCajas) . " bytes)\n";

echo "3. Probando detalleTurno (ID: 1)...\n";
$viewDetalle = $cajaCtrl->detalleTurno(1);
echo "-> Detalle turno renderizado correctamente (" . strlen($viewDetalle) . " bytes)\n";

echo "4. Probando Apertura en PosController...\n";
$posCtrl = new App\Http\Controllers\Admin\PosController();
$viewPos = $posCtrl->index();
$renderedPos = $viewPos->render();
echo "-> Vista apertura/terminal POS renderizada correctamente (" . strlen($renderedPos) . " bytes)\n";

echo "5. Verificando correlativo activo...\n";
$ajuste = App\Models\FacturaAjuste::where('activo', 1)->first();
echo "-> Serie activa: {$ajuste->serie}, Próximo correlativo: {$ajuste->proximo_numero}\n";
