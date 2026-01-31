<?php

/**
 * DolarJob Standalone - Laravel Integration
 * Este archivo se puede ejecutar vía Cron Job en cPanel:
 * /usr/local/bin/php /home/usuario/public_html/dolar_job.php
 */

class DolarJob
{
    private $pdo;
    private $env = [];
    private $id_usuario_sistema = null; // Puedes poner un ID de usuario por defecto si existe

    public function __construct()
    {
        $this->cargarEnv();
        $this->conectarBaseDeDatos();
    }

    private function cargarEnv()
    {
        $path = __DIR__ . '/.env';
        if (!file_exists($path)) {
            die("Error: No se encontró el archivo .env en " . $path);
        }

        $lineas = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lineas as $linea) {
            if (strpos(trim($linea), '#') === 0) continue;
            list($nombre, $valor) = explode('=', $linea, 2);
            $this->env[trim($nombre)] = trim($valor, " \t\n\r\0\x0B\"");
        }
    }

    private function conectarBaseDeDatos()
    {
        try {
            $host = $this->env['DB_HOST'] ?? '127.0.0.1';
            $db   = $this->env['DB_DATABASE'] ?? '';
            $user = $this->env['DB_USERNAME'] ?? '';
            $pass = $this->env['DB_PASSWORD'] ?? '';
            $port = $this->env['DB_PORT'] ?? '3306';
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;port=$port;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public function ejecutar()
    {
        try {
            $factor = $this->obtenerTasaDolarBCV();
            
            if ($factor === null || $factor <= 0) {
                throw new Exception("Factor de conversión inválido.");
            }

            $this->guardarTasa($factor);

            echo "[" . date('Y-m-d H:i:s') . "] Tasa actualizada exitosamente: " . $factor . PHP_EOL;
            return true;

        } catch (Exception $e) {
            error_log("Error en DolarJob: " . $e->getMessage());
            echo "Error: " . $e->getMessage() . PHP_EOL;
            return false;
        }
    }

    private function obtenerTasaDolarBCV()
    {
        $url = "https://www.bcv.org.ve/estadisticas/tipo-de-cambio-de-referencia";
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/91.0.4472.124 Safari/537.36',
        ]);
        
        $respuesta = curl_exec($curl);
        curl_close($curl);

        if (empty($respuesta)) return null;

        // Intentar con Regex
        $pattern = '/<div\s+id="dolar"[^>]*>.*?<strong>\s*([\d,]+)\s*<\/strong>/s';
        if (preg_match($pattern, $respuesta, $matches)) {
            return (float) str_replace(',', '.', trim($matches[1]));
        }

        // Fallback simple por si falla el regex complejo
        $pattern2 = '/USD.*?<strong>\s*([\d,]+)\s*<\/strong>/s';
        if (preg_match($pattern2, $respuesta, $matches)) {
            return (float) str_replace(',', '.', trim($matches[1]));
        }

        return null;
    }

    private function guardarTasa($valor)
    {
        // Insertar registro en la nueva tabla
        $sql = "INSERT INTO tasas_cambio (codigo_moneda, moneda_base, valor_tasa, fuente, usuario_editor_id, creado_at) 
                VALUES (:codigo, :base, :valor, :fuente, :usuario, NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'codigo'  => 'USD',
            'base'    => 'VES',
            'valor'   => $valor,
            'fuente'  => 'API',
            'usuario' => $this->id_usuario_sistema
        ]);
    }
}

// Ejecución del Script
$job = new DolarJob();
$job->ejecutar();