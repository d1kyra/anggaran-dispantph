<?php
// api/get_apbn.php
require 'koneksi.php';

header("Content-Type: application/json");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

try {
    $stmt = $pdo->query("SELECT * FROM apbn_kegiatan ORDER BY tahun DESC, id ASC");
    $kegiatans = $stmt->fetchAll();

    $result = [];

    foreach ($kegiatans as $k) {
        $tahun = $k['tahun'];
        
        if (!isset($result[$tahun])) {
            $result[$tahun] = [];
        }

        $result[$tahun][] = [
            "id" => (int)$k['id'], // Termasuk ID untuk keperluan CRUD nanti
            "kodeSatker" => $k['kode_satker'],
            "namaKegiatan" => $k['nama_kegiatan'],
            "kewenangan" => $k['kewenangan'],
            "paguDipa" => (int)$k['pagu_dipa'],
            "paguRevisi" => (int)$k['pagu_revisi'],
            "paguSetelahBlokir" => (int)$k['pagu_setelah_blokir'],
            "realisasiRp" => (int)$k['realisasi_rp'],
            "realisasiPersen" => (float)$k['realisasi_persen'],
            "realisasiFisik" => (float)$k['realisasi_fisik'],
            "sisaAnggaran" => (int)$k['sisa_anggaran'],
            "periodeCustom" => $k['periode_custom'] ?? null
        ];
    }

    echo json_encode(["status" => "success", "data" => $result]);

} catch (Exception $e) {
    error_log("get_apbn error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Terjadi kesalahan internal saat mengambil data APBN."]);
}
?>
