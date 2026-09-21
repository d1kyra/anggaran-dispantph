<?php
// api/check_auth.php
// Endpoint ringan untuk mengecek status session login saat ini
require_once __DIR__ . DIRECTORY_SEPARATOR . 'security.php';

emitSecurityHeaders();
ensureSecureSession();

header("Content-Type: application/json");

$isAuthenticated = !empty($_SESSION['admin_logged_in'])
    && $_SESSION['admin_logged_in'] === true
    && !isSessionExpired();

if ($isAuthenticated) {
    refreshSessionActivity();
}

echo json_encode([
    "status" => "success",
    "authenticated" => $isAuthenticated,
    "user" => $isAuthenticated ? ($_SESSION['admin_user'] ?? 'admin') : null
]);
?>
