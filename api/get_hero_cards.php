<?php
// api/get_hero_cards.php - Mengambil konfigurasi kartu Hero untuk beranda publik
require 'koneksi.php';

header("Content-Type: application/json");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Template kartu bawaan awal jika database belum memiliki konfigurasi kustom
$defaultCards = [
    [
        "id" => "hero_apbd",
        "title" => "APBD 2022-2026",
        "subtitle" => "Rekap Seluruh Unit",
        "icon" => "fa-file-invoice-dollar",
        "type" => "macro",
        "target" => "apbd",
        "unit_keys" => []
    ],
    [
        "id" => "hero_sekretariat",
        "title" => "Sekretariat",
        "subtitle" => "Sekretariat Dinas",
        "icon" => "fa-building-user",
        "type" => "units",
        "target" => "",
        "unit_keys" => ["1"]
    ],
    [
        "id" => "hero_gaji",
        "title" => "Gaji dan Tunjangan",
        "subtitle" => "Gaji & Tunjangan Pegawai",
        "icon" => "fa-money-check-dollar",
        "type" => "units",
        "target" => "",
        "unit_keys" => ["A"]
    ],
    [
        "id" => "hero_apbn",
        "title" => "APBN 2022-2026",
        "subtitle" => "Rekap Seluruh Satker",
        "icon" => "fa-table-list",
        "type" => "macro",
        "target" => "apbn",
        "unit_keys" => []
    ]
];

try {
    // Pastikan tabel app_settings tersedia
    $pdo->exec("CREATE TABLE IF NOT EXISTS app_settings (
        setting_key VARCHAR(50) PRIMARY KEY,
        setting_value TEXT NOT NULL
    )");

    $stmt = $pdo->prepare("SELECT setting_value FROM app_settings WHERE setting_key = 'hero_cards'");
    $stmt->execute();
    $row = $stmt->fetch();

    if ($row && !empty($row['setting_value'])) {
        $decoded = json_decode($row['setting_value'], true);
        if (is_array($decoded) && count($decoded) > 0) {
            echo json_encode([
                "status" => "success",
                "data" => $decoded
            ]);
            exit;
        }
    }

    // Jika belum ada di database, simpan defaultCards ke database dan kembalikan
    $saveStmt = $pdo->prepare("INSERT INTO app_settings (setting_key, setting_value) VALUES ('hero_cards', ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    $saveStmt->execute([json_encode($defaultCards)]);

    echo json_encode([
        "status" => "success",
        "data" => $defaultCards
    ]);

} catch (Exception $e) {
    error_log("get_hero_cards error: " . $e->getMessage());
    echo json_encode([
        "status" => "success",
        "data" => $defaultCards
    ]);
}
