<?php
// api/auth_middleware.php
// Middleware untuk mengunci endpoint API agar hanya bisa diakses oleh administrator yang sah

if (session_status() === PHP_SESSION_NONE) {
    // Pengaturan cookie session yang aman
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    header("Content-Type: application/json");
    echo json_encode([
        "status" => "error",
        "code" => 401,
        "message" => "Akses ditolak: Anda harus login sebagai administrator untuk melakukan tindakan ini."
    ]);
    exit;
}
?>
