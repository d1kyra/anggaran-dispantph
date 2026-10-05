<?php
// api/get_login_history.php
// Endpoint API untuk mengambil daftar riwayat login administrator terbaru
require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth_middleware.php';

header("Content-Type: application/json");

$limit = isset($_GET['limit']) ? max(1, min(100, (int) $_GET['limit'])) : 20;
$showAll = isset($_GET['all']) && ($_GET['all'] === '1' || $_GET['all'] === 'true');
$logins = getRecentAdminLogins($limit, !$showAll);

echo json_encode([
    "status" => "success",
    "total" => count($logins),
    "data" => $logins
]);
