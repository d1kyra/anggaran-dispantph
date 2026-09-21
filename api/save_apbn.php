<?php
// api/save_apbn.php
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

if (!isset($input['tahun']) || !isset($input['kodeSatker'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid payload"]);
    exit;
}

$id = isset($input['id']) ? (int) $input['id'] : -1;
$tahun = (int) $input['tahun'];
$kode_satker = preg_replace('/[^A-Za-z0-9_.-]/', '', trim((string) ($input['kodeSatker'] ?? '')));
$nama_kegiatan = trim((string) ($input['namaKegiatan'] ?? ''));
$kewenangan = strtoupper(trim((string) ($input['kewenangan'] ?? '')));
$pagu_dipa = (float) ($input['paguDipa'] ?? 0);
$pagu_revisi = (float) ($input['paguRevisi'] ?? 0);
$pagu_setelah_blokir = (float) ($input['paguSetelahBlokir'] ?? 0);
$realisasi_rp = (float) ($input['realisasiRp'] ?? 0);
$realisasi_persen = (float) ($input['realisasiPersen'] ?? 0);
$realisasi_fisik = (float) ($input['realisasiFisik'] ?? 0);
$sisa_anggaran = (float) ($input['sisaAnggaran'] ?? 0);
$periode_custom = !empty($input['periodeCustom']) ? trim(strip_tags((string) $input['periodeCustom'])) : null;

if ($tahun < 2000 || $tahun > 2100 || $kode_satker === '' || strlen($nama_kegiatan) < 2 || strlen($nama_kegiatan) > 255) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Data kegiatan tidak valid."]);
    exit;
}

try {
    $hasPeriodeCol = false;
    try {
        $colCheck = $pdo->query("SHOW COLUMNS FROM apbn_kegiatan LIKE 'periode_custom'");
        if ($colCheck && $colCheck->rowCount() > 0) {
            $hasPeriodeCol = true;
        } else {
            $pdo->exec("ALTER TABLE apbn_kegiatan ADD COLUMN periode_custom VARCHAR(100) NULL AFTER sisa_anggaran");
            $hasPeriodeCol = true;
        }
    } catch (Exception $eCol) {
        $hasPeriodeCol = false;
    }

    if ($id > 0) {
        $check = $pdo->prepare("SELECT id FROM apbn_kegiatan WHERE id = ?");
        $check->execute([$id]);

        if ($check->rowCount() > 0) {
            if ($hasPeriodeCol) {
                $stmt = $pdo->prepare("
                    UPDATE apbn_kegiatan SET 
                        tahun = ?, kode_satker = ?, nama_kegiatan = ?, kewenangan = ?,
                        pagu_dipa = ?, pagu_revisi = ?, pagu_setelah_blokir = ?,
                        realisasi_rp = ?, realisasi_persen = ?, realisasi_fisik = ?, sisa_anggaran = ?,
                        periode_custom = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $tahun, $kode_satker, $nama_kegiatan, $kewenangan,
                    $pagu_dipa, $pagu_revisi, $pagu_setelah_blokir,
                    $realisasi_rp, $realisasi_persen, $realisasi_fisik, $sisa_anggaran,
                    $periode_custom,
                    $id
                ]);
            } else {
                $stmt = $pdo->prepare("
                    UPDATE apbn_kegiatan SET 
                        tahun = ?, kode_satker = ?, nama_kegiatan = ?, kewenangan = ?,
                        pagu_dipa = ?, pagu_revisi = ?, pagu_setelah_blokir = ?,
                        realisasi_rp = ?, realisasi_persen = ?, realisasi_fisik = ?, sisa_anggaran = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $tahun, $kode_satker, $nama_kegiatan, $kewenangan,
                    $pagu_dipa, $pagu_revisi, $pagu_setelah_blokir,
                    $realisasi_rp, $realisasi_persen, $realisasi_fisik, $sisa_anggaran,
                    $id
                ]);
            }

            echo json_encode(["status" => "success", "message" => "Data APBN berhasil diperbarui"]);
            exit;
        }
    }

    if ($hasPeriodeCol) {
        $stmt = $pdo->prepare("
            INSERT INTO apbn_kegiatan (
                tahun, kode_satker, nama_kegiatan, kewenangan,
                pagu_dipa, pagu_revisi, pagu_setelah_blokir,
                realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran,
                periode_custom
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $tahun, $kode_satker, $nama_kegiatan, $kewenangan,
            $pagu_dipa, $pagu_revisi, $pagu_setelah_blokir,
            $realisasi_rp, $realisasi_persen, $realisasi_fisik, $sisa_anggaran,
            $periode_custom
        ]);
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO apbn_kegiatan (
                tahun, kode_satker, nama_kegiatan, kewenangan,
                pagu_dipa, pagu_revisi, pagu_setelah_blokir,
                realisasi_rp, realisasi_persen, realisasi_fisik, sisa_anggaran
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $tahun, $kode_satker, $nama_kegiatan, $kewenangan,
            $pagu_dipa, $pagu_revisi, $pagu_setelah_blokir,
            $realisasi_rp, $realisasi_persen, $realisasi_fisik, $sisa_anggaran
        ]);
    }

    logAuditEvent('apbn_saved', ['id' => $id, 'tahun' => $tahun, 'kode_satker' => $kode_satker, 'nama_kegiatan' => $nama_kegiatan]);
    echo json_encode(["status" => "success", "message" => "Data APBN berhasil ditambahkan", "new_id" => $pdo->lastInsertId()]);

} catch (Exception $e) {
    logAuditEvent('apbn_save_failed', ['id' => $id, 'tahun' => $tahun, 'error' => $e->getMessage()]);
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
