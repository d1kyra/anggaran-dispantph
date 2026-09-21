<?php
// api/delete_apbn.php
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

if (!isset($input['id'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid payload"]);
    exit;
}

$id = (int) $input['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM apbn_kegiatan WHERE id = ?");
    $stmt->execute([$id]);
    logAuditEvent('apbn_deleted', ['id' => $id]);
    echo json_encode(["status" => "success", "message" => "Kegiatan APBN berhasil dihapus"]);
} catch (Exception $e) {
    logAuditEvent('apbn_delete_failed', ['id' => $id, 'error' => $e->getMessage()]);
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
