<?php
// api/logout.php
// Endpoint logout untuk menghancurkan session administrator

require_once __DIR__ . DIRECTORY_SEPARATOR . 'security.php';

emitSecurityHeaders();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['admin_user'] ?? 'guest';
logAuditEvent('admin_logout', ['username' => $user]);

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

header("Content-Type: application/json");
echo json_encode([
    "status" => "success",
    "message" => "Logout berhasil",
    "redirect" => "index.html"
]);
?>
