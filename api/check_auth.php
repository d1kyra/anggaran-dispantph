<?php
// api/check_auth.php
// Endpoint ringan untuk mengecek status session login saat ini

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Content-Type: application/json");

$isAuthenticated = !empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

echo json_encode([
    "status" => "success",
    "authenticated" => $isAuthenticated,
    "user" => $isAuthenticated ? ($_SESSION['admin_user'] ?? 'admin') : null
]);
?>
