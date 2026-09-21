<?php
// api/delete_apbd.php
require 'koneksi.php';
require_once 'auth_middleware.php';
require_once 'security.php';

emitSecurityHeaders();
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    exit;
}

if (!validateCsrfToken()) {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Token keamanan tidak valid. Silakan muat ulang halaman dan coba lagi."]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['kode'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid payload"]);
    exit;
}

$kode = preg_replace('/[^A-Za-z0-9_.-]/', '', trim((string) $input['kode']));

try {
    $stmt = $pdo->prepare("DELETE FROM apbd_unit WHERE kode = ?");
    $stmt->execute([$kode]);
    logAuditEvent('apbd_deleted', ['kode' => $kode]);
    echo json_encode(["status" => "success", "message" => "Unit APBD berhasil dihapus"]);
} catch (Exception $e) {
    logAuditEvent('apbd_delete_failed', ['kode' => $kode, 'error' => $e->getMessage()]);
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
