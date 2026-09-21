<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'security.php';

emitSecurityHeaders();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

ensureSecureSession();

echo json_encode([
    'status' => 'success',
    'csrfToken' => getCsrfToken()
]);
