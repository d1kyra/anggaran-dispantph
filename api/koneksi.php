<?php
// api/koneksi.php

// Fungsi pembaca .env sederhana tanpa dependency eksternal
if (!function_exists('loadEnvFile')) {
    function loadEnvFile($filePath) {
        if (!file_exists($filePath)) return;
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($key, $val) = explode('=', $line, 2);
                $key = trim($key);
                $val = trim($val);
                $val = trim($val, "\"'");
                if (getenv($key) === false) {
                    @putenv("$key=$val");
                    $_ENV[$key] = $val;
                    $_SERVER[$key] = $val;
                }
            }
        }
    }
}

// Fungsi pembantu untuk mengambil nilai environment dengan fallback
if (!function_exists('getEnvValue')) {
    function getEnvValue($key, $default = null) {
        if (isset($_ENV[$key]) && $_ENV[$key] !== '') return $_ENV[$key];
        if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') return $_SERVER[$key];
        $val = getenv($key);
        return ($val !== false && $val !== '') ? $val : $default;
    }
}

// Muat konfigurasi dari .env di root proyek
loadEnvFile(dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env');

$host = getEnvValue('DB_HOST', 'localhost');
$db   = getEnvValue('DB_NAME', 'sisfor_anggaran');
$user = getEnvValue('DB_USER', 'root');
$pass = getEnvValue('DB_PASS', '');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    // Atur error mode menjadi exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set default fetch mode ke associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Catat detail error ke log server untuk debugging internal
    error_log("Database connection error: " . $e->getMessage());
    
    header("Content-Type: application/json");
    http_response_code(500);
    echo json_encode([
        "status" => "error", 
        "message" => "Gagal terhubung ke database. Silakan periksa konfigurasi server."
    ]);
    exit;
}
?>
