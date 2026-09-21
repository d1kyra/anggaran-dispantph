<?php
// api/login.php
// Endpoint autentikasi administrator sisi server

header("Content-Type: application/json");

// Muat konfigurasi database & .env
require_once __DIR__ . DIRECTORY_SEPARATOR . 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
if (!$input || !isset($input['username']) || !isset($input['password'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Username dan password wajib diisi."]);
    exit;
}

$username = trim((string)$input['username']);
$password = trim((string)$input['password']);

$expectedUser = getEnvValue('ADMIN_USER', 'admin');
$expectedPass = getEnvValue('ADMIN_PASS', 'perencanaan2026');

if (empty($expectedPass)) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Konfigurasi server belum lengkap: ADMIN_PASS belum diatur di file .env."]);
    exit;
}

// Gunakan hash_equals untuk mencegah timing attack
$isUserValid = hash_equals($expectedUser, $username);
$isPassValid = hash_equals($expectedPass, $password);

if ($isUserValid && $isPassValid) {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        session_start();
    }

    // Regenerasi session id untuk mencegah session fixation attack
    session_regenerate_id(true);

    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_user'] = $username;
    $_SESSION['login_time'] = time();

    echo json_encode([
        "status" => "success",
        "message" => "Login berhasil! Mengalihkan ke panel administrator...",
        "redirect" => "admin.php"
    ]);
} else {
    // Beri sedikit delay untuk memitigasi brute-force
    usleep(250000); // 0.25 detik

    http_response_code(401);
    echo json_encode([
        "status" => "error",
        "message" => "Username atau password salah!"
    ]);
}
?>
