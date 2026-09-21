<?php
// api/save_apbd.php
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

if (!isset($input['kode']) || !isset($input['nama']) || !isset($input['data'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid payload"]);
    exit;
}

$kode = preg_replace('/[^A-Za-z0-9_.-]/', '', trim((string) $input['kode']));
$nama = trim((string) $input['nama']);
if ($kode === '' || strlen($nama) < 2 || strlen($nama) > 255) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Kode atau nama unit tidak valid."]);
    exit;
}

$dataYears = is_array($input['data']) ? $input['data'] : [];

try {
    $pdo->beginTransaction();

    $rawPeriode = $input['periodeCustom'] ?? ($dataYears['periodeCustom'] ?? null);
    $periode_custom = !empty($rawPeriode) ? trim(strip_tags((string) $rawPeriode)) : null;

    $stmt = $pdo->prepare("SELECT kode FROM apbd_unit WHERE kode = ?");
    $stmt->execute([$kode]);
    if ($stmt->rowCount() == 0) {
        $maxUrutanStmt = $pdo->query("SELECT MAX(urutan) as max_urutan FROM apbd_unit");
        $maxUrutan = (int) $maxUrutanStmt->fetchColumn() + 1;

        $insertUnit = $pdo->prepare("INSERT INTO apbd_unit (kode, nama, periode_custom, urutan) VALUES (?, ?, ?, ?)");
        $insertUnit->execute([$kode, $nama, $periode_custom, $maxUrutan]);
    } else {
        $updateUnit = $pdo->prepare("UPDATE apbd_unit SET nama = ?, periode_custom = ? WHERE kode = ?");
        $updateUnit->execute([$nama, $periode_custom, $kode]);
    }

    foreach ($dataYears as $tahun => $data) {
        if (!is_numeric($tahun) || !is_array($data)) continue;

        $tahun = (int) $tahun;

        $pagu_awal = (float) ($data['paguAwal'] ?? 0);
        $pagu_anggaran = (float) ($data['paguAnggaran'] ?? 0);
        $pagu_efisiensi = (float) ($data['paguEfisiensi'] ?? 0);
        $pagu_apbd = (float) ($data['paguApbd'] ?? 0);
        $pagu_tahunan = (float) ($data['paguTahunan'] ?? 0);
        $realisasi_keuangan = (float) ($data['realisasiKeuangan'] ?? 0);
        $realisasi_persen = (float) ($data['realisasiPersen'] ?? 0);
        $realisasi_fisik = (float) ($data['realisasiFisik'] ?? 0);

        $checkAnggaran = $pdo->prepare("SELECT id FROM apbd_anggaran WHERE unit_kode = ? AND tahun = ?");
        $checkAnggaran->execute([$kode, $tahun]);

        if ($checkAnggaran->rowCount() > 0) {
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
    logAuditEvent('apbd_saved', ['kode' => $kode, 'nama' => $nama, 'tahun_count' => count($dataYears)]);
    echo json_encode(["status" => "success", "message" => "Data APBD berhasil disimpan"]);

} catch (Exception $e) {
    $pdo->rollBack();
    logAuditEvent('apbd_save_failed', ['kode' => $kode, 'error' => $e->getMessage()]);
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
