<?php
// api/get_apbd.php
require 'koneksi.php';

header("Content-Type: application/json");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

try {
    // Pastikan tabel app_settings tersedia
    $periodeAktif = "s.d Juni";
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS app_settings (
            setting_key VARCHAR(50) PRIMARY KEY,
            setting_value TEXT NOT NULL
        )");
        $setStmt = $pdo->query("SELECT setting_value FROM app_settings WHERE setting_key = 'periode_aktif'");
        if ($setRow = $setStmt->fetch()) {
            if (!empty($setRow['setting_value'])) {
                $periodeAktif = $setRow['setting_value'];
            }
        } else {
            $pdo->exec("INSERT INTO app_settings (setting_key, setting_value) VALUES ('periode_aktif', 's.d Juni')");
        }
    } catch (Exception $eSet) {
        // Fallback ke default jika ada kendala
    }

    $unitsStmt = $pdo->query("SELECT * FROM apbd_unit ORDER BY urutan ASC");
    $units = $unitsStmt->fetchAll();

    $result = [];

    foreach ($units as $unit) {
        $kode = $unit['kode'];
        $result[$kode] = [
            "nama" => $unit['nama']
        ];
        if (!empty($unit['periode_custom'])) {
            $result[$kode]["periodeCustom"] = $unit['periode_custom'];
        }

        $anggaranStmt = $pdo->prepare("SELECT * FROM apbd_anggaran WHERE unit_kode = ? ORDER BY tahun ASC");
        $anggaranStmt->execute([$kode]);
        $anggarans = $anggaranStmt->fetchAll();

        foreach ($anggarans as $anggaran) {
            $tahun = $anggaran['tahun'];
            $result[$kode][$tahun] = [
                "paguAwal" => (int)$anggaran['pagu_awal'],
                "paguAnggaran" => (int)$anggaran['pagu_anggaran'],
                "paguEfisiensi" => (int)$anggaran['pagu_efisiensi'],
                "paguApbd" => (int)$anggaran['pagu_apbd'],
                "paguTahunan" => (int)$anggaran['pagu_tahunan'],
                "realisasiKeuangan" => (int)$anggaran['realisasi_keuangan'],
                "realisasiPersen" => (float)$anggaran['realisasi_persen'],
                "realisasiFisik" => (float)$anggaran['realisasi_fisik']
            ];
        }
    }

    echo json_encode([
        "status" => "success",
        "periodeAktif" => $periodeAktif,
        "data" => $result
    ]);

} catch (Exception $e) {
    error_log("get_apbd error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Terjadi kesalahan internal saat mengambil data APBD."]);
}
?>
