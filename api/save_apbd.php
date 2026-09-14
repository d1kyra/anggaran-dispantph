<?php
// api/save_apbd.php
require 'koneksi.php';
require_once 'auth_middleware.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['kode']) || !isset($input['nama']) || !isset($input['data'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid payload"]);
    exit;
}

$kode = strip_tags(trim((string)$input['kode']));
$nama = strip_tags(trim((string)$input['nama']));
$dataYears = is_array($input['data']) ? $input['data'] : [];

try {
    $pdo->beginTransaction();

    $rawPeriode = $input['periodeCustom'] ?? ($dataYears['periodeCustom'] ?? null);
    $periode_custom = !empty($rawPeriode) ? strip_tags(trim((string)$rawPeriode)) : null;

    // Cek apakah unit sudah ada, jika belum insert
    $stmt = $pdo->prepare("SELECT kode FROM apbd_unit WHERE kode = ?");
    $stmt->execute([$kode]);
    if ($stmt->rowCount() == 0) {
        // Ambil urutan terakhir untuk unit baru
        $maxUrutanStmt = $pdo->query("SELECT MAX(urutan) as max_urutan FROM apbd_unit");
        $maxUrutan = $maxUrutanStmt->fetchColumn() + 1;

        $insertUnit = $pdo->prepare("INSERT INTO apbd_unit (kode, nama, periode_custom, urutan) VALUES (?, ?, ?, ?)");
        $insertUnit->execute([$kode, $nama, $periode_custom, $maxUrutan]);
    } else {
        // Update nama unit dan periode_custom
        $updateUnit = $pdo->prepare("UPDATE apbd_unit SET nama = ?, periode_custom = ? WHERE kode = ?");
        $updateUnit->execute([$nama, $periode_custom, $kode]);
    }

    // Upsert anggaran per tahun
    foreach ($dataYears as $tahun => $data) {
        // Hanya proses jika key adalah tahun (angka numerik) dan value berupa array data
        if (!is_numeric($tahun) || !is_array($data)) continue;

        $tahun = (int)$tahun;

        $pagu_awal = $data['paguAwal'] ?? 0;
        $pagu_anggaran = $data['paguAnggaran'] ?? 0;
        $pagu_efisiensi = $data['paguEfisiensi'] ?? 0;
        $pagu_apbd = $data['paguApbd'] ?? 0;
        $pagu_tahunan = $data['paguTahunan'] ?? 0;
        $realisasi_keuangan = $data['realisasiKeuangan'] ?? 0;
        $realisasi_persen = $data['realisasiPersen'] ?? 0;
        $realisasi_fisik = $data['realisasiFisik'] ?? 0;

        // Cek apakah anggaran tahun tersebut sudah ada
        $checkAnggaran = $pdo->prepare("SELECT id FROM apbd_anggaran WHERE unit_kode = ? AND tahun = ?");
        $checkAnggaran->execute([$kode, $tahun]);

        if ($checkAnggaran->rowCount() > 0) {
            // Update
            $updateAnggaran = $pdo->prepare("
                UPDATE apbd_anggaran 
                SET pagu_awal = ?, pagu_anggaran = ?, pagu_efisiensi = ?, pagu_apbd = ?, pagu_tahunan = ?,
                    realisasi_keuangan = ?, realisasi_persen = ?, realisasi_fisik = ?
                WHERE unit_kode = ? AND tahun = ?
            ");
            $updateAnggaran->execute([
                $pagu_awal, $pagu_anggaran, $pagu_efisiensi, $pagu_apbd, $pagu_tahunan,
                $realisasi_keuangan, $realisasi_persen, $realisasi_fisik,
                $kode, $tahun
            ]);
        } else {
            // Insert
            $insertAnggaran = $pdo->prepare("
                INSERT INTO apbd_anggaran (
                    unit_kode, tahun, pagu_awal, pagu_anggaran, pagu_efisiensi, pagu_apbd, pagu_tahunan,
                    realisasi_keuangan, realisasi_persen, realisasi_fisik
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $insertAnggaran->execute([
                $kode, $tahun, $pagu_awal, $pagu_anggaran, $pagu_efisiensi, $pagu_apbd, $pagu_tahunan,
                $realisasi_keuangan, $realisasi_persen, $realisasi_fisik
            ]);
        }
    }

    $pdo->commit();
    echo json_encode(["status" => "success", "message" => "Data APBD berhasil disimpan"]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
