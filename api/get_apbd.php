<?php
// api/get_apbd.php
require 'koneksi.php';

header("Content-Type: application/json");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

try {
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

    echo json_encode(["status" => "success", "data" => $result]);

} catch (Exception $e) {
    error_log("get_apbd error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Terjadi kesalahan internal saat mengambil data APBD."]);
}
?>
