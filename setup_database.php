<?php
// setup_database.php
// Script installer otomatis untuk membuat tabel dan mengimpor data awal ke database

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <title>Setup Database SISFOR Anggaran</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #0f172a; color: #f8fafc; padding: 40px 20px; display: flex; justify-content: center; }
        .card { background: #1e293b; border: 1px solid #334155; border-radius: 12px; padding: 30px; max-width: 600px; width: 100%; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        h2 { margin-top: 0; color: #38bdf8; }
        .success { background: rgba(34, 197, 94, 0.1); border-left: 4px solid #22c55e; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .error { background: rgba(239, 68, 68, 0.1); border-left: 4px solid #ef4444; padding: 15px; margin: 20px 0; border-radius: 4px; color: #fca5a5; }
        .btn { display: inline-block; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-weight: bold; margin-right: 10px; margin-top: 15px; }
        .btn-primary { background: #0284c7; color: white; }
        .btn-success { background: #16a34a; color: white; }
    </style>
</head>
<body>
<div class='card'>
    <h2>🚀 Installer Database SISFOR Anggaran</h2>
";

$lockFile = __DIR__ . '/setup.lock';
if (file_exists($lockFile)) {
    echo "<div class='error'>
        <h4 style='margin:0 0 8px 0; color:#f87171;'>🔒 Akses Dikunci demi Keamanan</h4>
        <p style='margin:0; font-size:14px;'>Setup database sudah pernah dijalankan sebelumnya. Untuk mencegah penimpaan data yang tidak disengaja, installer ini telah dikunci secara otomatis.<br><br>Jika kamu benar-benar ingin menjalankan ulang, hapus file <code>setup.lock</code> di File Manager terlebih dahulu.</p>
    </div>
    <div style='margin-top:20px;'>
        <a href='index.html' class='btn btn-primary'>Buka Website Utama</a>
        <a href='admin.php' class='btn btn-success'>Buka Panel Admin</a>
    </div>
    </div></body></html>";
    exit;
}

$koneksiFile = __DIR__ . '/api/koneksi.php';
if (!file_exists($koneksiFile)) {
    echo "<div class='error'><strong>Error:</strong> File <code>api/koneksi.php</code> tidak ditemukan.</div></div></body></html>";
    exit;
}

require_once $koneksiFile;

if (!isset($pdo) || !$pdo) {
    echo "<div class='error'><strong>Error:</strong> Gagal terhubung ke MySQL Database.<br>Pastikan file <code>.env</code> sudah dibuat dan diisi dengan kredensial database yang benar.</div></div></body></html>";
    exit;
}

$sqlFile = __DIR__ . '/db/sql/database.sql';
if (!file_exists($sqlFile)) {
    echo "<div class='error'><strong>Error:</strong> File SQL <code>db/sql/database.sql</code> tidak ditemukan.</div></div></body></html>";
    exit;
}

try {
    $sql = file_get_contents($sqlFile);
    // Jalankan seluruh query pembuatan tabel dan data awal
    $pdo->exec($sql);

    // Buat file pengunci otomatis
    @file_put_contents($lockFile, "Installed on " . date('Y-m-d H:i:s'));

    echo "<div class='success'>
        <h4 style='margin:0 0 8px 0; color:#4ade80;'>✓ Database Berhasil Dikonfigurasi!</h4>
        <p style='margin:0; font-size:14px;'>Tabel <code>apbd_unit</code>, <code>apbd_anggaran</code>, dan <code>apbn_kegiatan</code> beserta seluruh data awal berhasil dimasukkan ke database.</p>
    </div>";

    echo "<div style='margin-top:20px;'>
        <a href='index.html' class='btn btn-primary'>Buka Website Utama</a>
        <a href='admin.php' class='btn btn-success'>Buka Panel Admin</a>
    </div>";

    echo "<p style='margin-top:25px; font-size:12px; color:#94a3b8;'><strong>Keamanan:</strong> File pengunci <code>setup.lock</code> telah dibuat otomatis. Kamu juga dapat menghapus file <code>setup_database.php</code> ini dari File Manager.</p>";

} catch (Exception $e) {
    echo "<div class='error'>
        <strong>Terjadi Kesalahan saat mengeksekusi SQL:</strong><br>" . htmlspecialchars($e->getMessage()) . "
    </div>";
}

echo "</div></body></html>";
?>
