<?php
// api/save_periode.php
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

if (!isset($input['type']) || !isset($input['periode'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Parameter type dan periode wajib diisi"]);
    exit;
}

$type = strtoupper(trim((string)$input['type']));
$periode = trim(strip_tags((string)$input['periode']));
$key = isset($input['key']) ? trim(strip_tags((string)$input['key'])) : null;
$namaKegiatan = isset($input['namaKegiatan']) ? trim(strip_tags((string)$input['namaKegiatan'])) : null;

if (empty($periode) || strlen($periode) > 100) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Nama periode tidak valid atau melebihi batas 100 karakter."]);
    exit;
}

try {
    if ($type === 'GLOBAL') {
        // Pastikan tabel app_settings sudah ada
        $pdo->exec("CREATE TABLE IF NOT EXISTS app_settings (
            setting_key VARCHAR(50) PRIMARY KEY,
            setting_value TEXT NOT NULL
        )");

        $stmt = $pdo->prepare("INSERT INTO app_settings (setting_key, setting_value) VALUES ('periode_aktif', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$periode, $periode]);

        logAuditEvent('periode_updated', ['type' => 'GLOBAL', 'periode' => $periode]);

        echo json_encode([
            "status" => "success",
            "message" => "Periode aktif global berhasil diperbarui menjadi: " . $periode,
            "periode" => $periode,
            "type" => "GLOBAL"
        ]);
        exit;

    } else if ($type === 'APBD') {
        if (empty($key)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Kode unit APBD wajib disertakan"]);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE apbd_unit SET periode_custom = ? WHERE kode = ?");
        $stmt->execute([$periode, $key]);

        logAuditEvent('periode_updated', ['type' => 'APBD', 'key' => $key, 'periode' => $periode]);

        echo json_encode([
            "status" => "success",
            "message" => "Periode unit APBD berhasil diperbarui menjadi: " . $periode,
            "periode" => $periode,
            "key" => $key,
            "type" => "APBD"
        ]);
        exit;

    } else if ($type === 'APBN') {
        // Pastikan kolom periode_custom ada di apbn_kegiatan
        try {
            $colCheck = $pdo->query("SHOW COLUMNS FROM apbn_kegiatan LIKE 'periode_custom'");
            if (!$colCheck || $colCheck->rowCount() == 0) {
                $pdo->exec("ALTER TABLE apbn_kegiatan ADD COLUMN periode_custom VARCHAR(100) NULL AFTER sisa_anggaran");
            }
        } catch (Exception $eCol) {}

        if (!empty($namaKegiatan)) {
            $stmt = $pdo->prepare("UPDATE apbn_kegiatan SET periode_custom = ? WHERE nama_kegiatan = ?");
            $stmt->execute([$periode, $namaKegiatan]);
        }

        if (!empty($key)) {
            if ($key === 'ditjen_tanaman_pangan') {
                $pdo->prepare("UPDATE apbn_kegiatan SET periode_custom = ? WHERE LOWER(nama_kegiatan) LIKE '%tanaman pangan%'")->execute([$periode]);
            } else if ($key === 'ditjen_hortikultura') {
                $pdo->prepare("UPDATE apbn_kegiatan SET periode_custom = ? WHERE LOWER(nama_kegiatan) LIKE '%hortikultura%'")->execute([$periode]);
            } else if ($key === 'ditjen_psp') {
                $pdo->prepare("UPDATE apbn_kegiatan SET periode_custom = ? WHERE LOWER(nama_kegiatan) LIKE '%psp%' OR LOWER(nama_kegiatan) LIKE '%prasarana%' OR LOWER(nama_kegiatan) LIKE '%sarana pertanian%'")->execute([$periode]);
            } else if ($key === 'bppsdmp') {
                $pdo->prepare("UPDATE apbn_kegiatan SET periode_custom = ? WHERE LOWER(nama_kegiatan) LIKE '%bppsdmp%'")->execute([$periode]);
            } else if ($key === 'bapanas') {
                $pdo->prepare("UPDATE apbn_kegiatan SET periode_custom = ? WHERE LOWER(nama_kegiatan) LIKE '%bapanas%' OR LOWER(nama_kegiatan) LIKE '%bkp%' OR LOWER(nama_kegiatan) LIKE '%pangan nasional%'")->execute([$periode]);
            } else if ($key === 'ditjen_lip') {
                $pdo->prepare("UPDATE apbn_kegiatan SET periode_custom = ? WHERE LOWER(nama_kegiatan) LIKE '%lip%' OR LOWER(nama_kegiatan) LIKE '%lahan%' OR LOWER(nama_kegiatan) LIKE '%irigasi%'")->execute([$periode]);
            }
        }

        logAuditEvent('periode_updated', ['type' => 'APBN', 'key' => $key, 'nama_kegiatan' => $namaKegiatan, 'periode' => $periode]);

        echo json_encode([
            "status" => "success",
            "message" => "Periode kegiatan APBN berhasil diperbarui menjadi: " . $periode,
            "periode" => $periode,
            "key" => $key,
            "type" => "APBN"
        ]);
        exit;

    } else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Tipe periode tidak valid (GLOBAL, APBD, atau APBN)"]);
        exit;
    }

} catch (Exception $e) {
    error_log("save_periode error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Terjadi kesalahan internal saat menyimpan periode."]);
}
?>
