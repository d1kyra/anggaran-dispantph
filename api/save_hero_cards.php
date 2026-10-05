<?php
// api/save_hero_cards.php - Menyimpan konfigurasi kartu Hero dari Panel Administrator
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
    echo json_encode(["status" => "error", "message" => "Token keamanan tidak valid. Silakan muat ulang halaman."]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['cards']) || !is_array($input['cards'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Data kartu hero tidak valid."]);
    exit;
}

$sanitizedCards = [];

foreach ($input['cards'] as $index => $c) {
    if (!is_array($c)) continue;

    $id = preg_replace('/[^A-Za-z0-9_-]/', '', trim((string)($c['id'] ?? 'hero_' . ($index + 1))));
    if (empty($id)) {
        $id = 'hero_' . uniqid();
    }

    $title = trim(strip_tags((string)($c['title'] ?? 'Kartu Hero')));
    if (empty($title)) {
        $title = 'Kartu ' . ($index + 1);
    }
    if (strlen($title) > 100) {
        $title = substr($title, 0, 100);
    }

    $subtitle = trim(strip_tags((string)($c['subtitle'] ?? '')));
    if (strlen($subtitle) > 150) {
        $subtitle = substr($subtitle, 0, 150);
    }

    $icon = preg_replace('/[^a-z0-9_-]/i', '', trim((string)($c['icon'] ?? 'fa-building-user')));
    if (empty($icon)) {
        $icon = 'fa-building-user';
    }

    $type = in_array(($c['type'] ?? 'units'), ['macro', 'apbn', 'units'], true) ? $c['type'] : 'units';
    $target = preg_replace('/[^a-z0-9_-]/i', '', trim((string)($c['target'] ?? '')));

    $unitKeys = [];
    if (isset($c['unit_keys']) && is_array($c['unit_keys'])) {
        foreach ($c['unit_keys'] as $uKey) {
            $cleanedKey = preg_replace('/[^A-Za-z0-9_.-]/', '', trim((string)$uKey));
            if (!empty($cleanedKey) && !in_array($cleanedKey, $unitKeys, true)) {
                $unitKeys[] = $cleanedKey;
            }
        }
    }

    $sanitizedCards[] = [
        "id" => $id,
        "title" => $title,
        "subtitle" => $subtitle,
        "icon" => $icon,
        "type" => $type,
        "target" => $target,
        "unit_keys" => $unitKeys
    ];
}

if (count($sanitizedCards) === 0) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Minimal harus ada 1 kartu hero aktif."]);
    exit;
}

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS app_settings (
        setting_key VARCHAR(50) PRIMARY KEY,
        setting_value TEXT NOT NULL
    )");

    $stmt = $pdo->prepare("INSERT INTO app_settings (setting_key, setting_value) VALUES ('hero_cards', ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    $stmt->execute([json_encode($sanitizedCards)]);

    logAuditEvent('admin_save_hero_cards', [
        'username' => $_SESSION['admin_user'] ?? 'admin',
        'card_count' => count($sanitizedCards)
    ]);

    echo json_encode([
        "status" => "success",
        "message" => "Konfigurasi kartu hero berhasil disimpan.",
        "data" => $sanitizedCards
    ]);

} catch (Exception $e) {
    error_log("save_hero_cards error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Terjadi kesalahan internal saat menyimpan konfigurasi kartu hero."]);
}
