<?php
// api/login.php
// Endpoint autentikasi administrator sisi server
require_once __DIR__ . DIRECTORY_SEPARATOR . 'koneksi.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'security.php';

emitSecurityHeaders();
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    exit;
}

if (rateLimitExceeded('admin_login', 5, 600)) {
    http_response_code(429);
    echo json_encode(["status" => "error", "message" => "Terlalu banyak percobaan login. Silakan tunggu beberapa menit lalu coba lagi."]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
if (!$input || !isset($input['username']) || !isset($input['password'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Username dan password wajib diisi."]);
    exit;
}

$username = trim((string) $input['username']);
$password = trim((string) $input['password']);

$expectedUser = function_exists('getEnvValue') ? getEnvValue('ADMIN_USER', 'admin') : (getenv('ADMIN_USER') ?: 'admin');
$expectedPass = function_exists('getEnvValue') ? getEnvValue('ADMIN_PASS', null) : getenv('ADMIN_PASS');

if (empty($expectedPass)) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Konfigurasi server belum lengkap: ADMIN_PASS belum diatur di file .env."]);
    exit;
}

$isUserValid = hash_equals($expectedUser, $username);
$isPassValid = normalizePasswordCheck($expectedPass, $password);

if ($isUserValid && $isPassValid) {
    ensureSecureSession();
    session_regenerate_id(true);

    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_user'] = $username;
    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();

    logAuditEvent('admin_login_success', ['username' => $username]);

    echo json_encode([
        "status" => "success",
        "message" => "Login berhasil! Mengalihkan ke panel administrator...",
        "redirect" => "admin.php"
    ]);
} else {
    logAuditEvent('admin_login_failed', ['username' => $username, 'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown']);
    usleep(250000);
    http_response_code(401);
    echo json_encode([
        "status" => "error",
        "message" => "Username atau password salah!"
    ]);
}
?>
