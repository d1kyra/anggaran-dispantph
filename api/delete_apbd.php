<?php
// api/delete_apbd.php
require 'koneksi.php';
require_once 'auth_middleware.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['kode'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid payload"]);
    exit;
}

$kode = $input['kode'];

try {
    $stmt = $pdo->prepare("DELETE FROM apbd_unit WHERE kode = ?");
    $stmt->execute([$kode]);
    echo json_encode(["status" => "success", "message" => "Unit APBD berhasil dihapus"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
