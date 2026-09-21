<?php
// api/auth_middleware.php
// Middleware untuk mengunci endpoint API agar hanya bisa diakses oleh administrator yang sah
require_once __DIR__ . DIRECTORY_SEPARATOR . 'security.php';

emitSecurityHeaders();
ensureSecureSession();

if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true || isSessionExpired()) {
    $_SESSION = [];
    session_destroy();

    http_response_code(401);
    header("Content-Type: application/json");
    echo json_encode([
        "status" => "error",
        "code" => 401,
        "message" => "Sesi administrator telah berakhir atau tidak valid. Silakan login kembali."
    ]);
    exit;
}

refreshSessionActivity();
?>
