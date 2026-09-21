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
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass, [
        PDO::ATTR_TIMEOUT => 3
    ]);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Fallback otomatis ke MySQL lokal Laragon jika gagal koneksi di lingkungan development
    $isLocal = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '::1']) || 
               strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false || 
               strpos($_SERVER['HTTP_HOST'] ?? '', '.test') !== false ||
               php_sapi_name() === 'cli';

    if ($isLocal && ($host !== 'localhost' && $host !== '127.0.0.1')) {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=sisfor_anggaran;charset=utf8", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return;
        } catch (PDOException $exLocal) {
            // Lanjut ke penanganan error di bawah
        }
    }

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
