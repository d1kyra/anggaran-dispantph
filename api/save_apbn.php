<?php
// api/save_apbn.php
require 'koneksi.php';
require_once 'auth_middleware.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['tahun']) || !isset($input['kodeSatker'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid payload"]);
    exit;
}

$id = isset($input['id']) ? (int)$input['id'] : -1;
$tahun = (int)$input['tahun'];
$kode_satker = $input['kodeSatker'];
$nama_kegiatan = $input['namaKegiatan'];
$kewenangan = $input['kewenangan'];
$pagu_dipa = $input['paguDipa'] ?? 0;
$pagu_revisi = $input['paguRevisi'] ?? 0;
$pagu_setelah_blokir = $input['paguSetelahBlokir'] ?? 0;
$realisasi_rp = $input['realisasiRp'] ?? 0;
$realisasi_persen = $input['realisasiPersen'] ?? 0;
$realisasi_fisik = $input['realisasiFisik'] ?? 0;
$sisa_anggaran = $input['sisaAnggaran'] ?? 0;
$periode_custom = !empty($input['periodeCustom']) ? trim($input['periodeCustom']) : null;

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
        // Cek apakah ID exist
        $check = $pdo->prepare("SELECT id FROM apbn_kegiatan WHERE id = ?");
        $check->execute([$id]);
        
        if ($check->rowCount() > 0) {
            // Update
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
    
    // Insert jika ID = -1 atau ID tidak ditemukan
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

    echo json_encode(["status" => "success", "message" => "Data APBN berhasil ditambahkan", "new_id" => $pdo->lastInsertId()]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
