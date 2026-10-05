<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'security.php';

emitSecurityHeaders();
ensureSecureSession();

if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true || isSessionExpired()) {
    $_SESSION = [];
    session_destroy();
    header('Location: login.php?auth=required');
    exit;
}

refreshSessionActivity();
$adminUser = (string) ($_SESSION['admin_user'] ?? 'admin');
$showAll = isset($_GET['all']) && ($_GET['all'] === '1' || $_GET['all'] === 'true');
$excludeLocalhost = !$showAll;
$lastAdminLogin = getLatestAdminLogin($excludeLocalhost);
$lastAdminName = (string) ($lastAdminLogin['context']['username'] ?? $lastAdminLogin['user'] ?? $adminUser);
$lastAdminLoginTime = (string) ($lastAdminLogin['timestamp'] ?? '');
$allAdminLogins = getRecentAdminLogins(100, $excludeLocalhost);
$totalLogins = count($allAdminLogins);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Login Administrator - SISFOR Anggaran</title>
    <link rel="icon" type="image/svg+xml" href="logo.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=<?= filemtime(__DIR__ . '/style.css') ?>">
</head>
<body>
    <nav class="navbar navbar-dark navbar-admin sticky-top">
        <div class="container px-3 px-md-4 d-flex align-items-center justify-content-between">
            <a href="admin_dashboard.php" class="navbar-brand d-flex align-items-center gap-2">
                <div class="brand-badge-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <i class="fa-solid fa-user-shield text-dark"></i>
                </div>
                <div class="d-flex flex-column">
                    <span class="d-none d-sm-inline fw-bold text-white">Sistem Informasi Anggaran DISPANTPH</span>
                    <span class="d-inline d-sm-none fw-bold text-white">SISFOR DISPANTPH</span>
                    <span class="admin-badge-pill" style="width: fit-content;">
                        <i class="fa-solid fa-shield-halved"></i> PANEL ADMINISTRATOR
                    </span>
                </div>
            </a>
            <button class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold" onclick="logoutAdmin()" title="Keluar dari Panel Administrator"><i class="fa-solid fa-right-from-bracket me-1"></i><span class="d-none d-sm-inline">Keluar</span></button>
        </div>
    </nav>

    <aside class="admin-sidebar" aria-label="Navigasi administrator">
        <a class="admin-sidebar-brand" href="admin_dashboard.php">
            <span><i class="fa-solid fa-wheat-awn"></i></span>
            <strong>SISFOR <small>ANGGARAN</small></strong>
        </a>
        <p class="admin-sidebar-label">MENU UTAMA</p>
        <nav class="admin-sidebar-nav">
            <a href="admin_dashboard.php"><i class="fa-solid fa-table-cells-large"></i> Dashboard</a>
            <a href="admin.php"><i class="fa-solid fa-building-columns"></i> Kelola APBD</a>
            <a href="admin.php#apbn"><i class="fa-solid fa-landmark"></i> Kelola APBN</a>
        </nav>
        <p class="admin-sidebar-label mt-4">AKUN</p>
        <a href="admin_login_history.php" class="admin-last-login active-card" title="Halaman Riwayat Login Administrator">
            <span class="admin-last-login-icon"><i class="fa-solid fa-user-clock"></i></span>
            <div>
                <small>LOGIN TERAKHIR</small>
                <strong><?= htmlspecialchars($lastAdminName, ENT_QUOTES, 'UTF-8') ?></strong>
                <time><?= htmlspecialchars(formatAuditTimestamp($lastAdminLoginTime), ENT_QUOTES, 'UTF-8') ?></time>
            </div>
            <span class="admin-last-login-arrow"><i class="fa-solid fa-chevron-right"></i></span>
        </a>
        <nav class="admin-sidebar-nav">
            <a class="active" href="admin_login_history.php"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Login</a>
            <a href="index.html" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat Web Publik</a>
            <button type="button" onclick="logoutAdmin()"><i class="fa-solid fa-right-from-bracket"></i> Keluar</button>
        </nav>
        <div class="admin-sidebar-user">
            <i class="fa-solid fa-user-shield"></i>
            <span><?= htmlspecialchars($adminUser, ENT_QUOTES, 'UTF-8') ?><small>Administrator aktif</small></span>
        </div>
    </aside>

    <main class="admin-workspace">
        <section class="admin-command-center min-vh-100">
            <div class="container px-3 px-md-4 py-4 py-md-5">
                <!-- Heading -->
                <div class="admin-command-heading mb-4">
                    <div>
                        <span class="admin-eyebrow"><i class="fa-solid fa-shield-halved"></i> Log Keamanan & Audit</span>
                        <h1>Riwayat Login Administrator</h1>
                        <p>Catatan seluruh aktivitas login administrator yang tersimpan aman pada log server.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="admin_dashboard.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Dashboard
                        </a>
                        <div class="admin-session-state"><span></span> Mode admin aktif</div>
                    </div>
                </div>

                <!-- Stats Cards Row -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-4">
                        <div class="audit-stats-card">
                            <div class="audit-stats-icon green">
                                <i class="fa-solid fa-clipboard-check"></i>
                            </div>
                            <div>
                                <p>Total Sesi Tercatat</p>
                                <strong><?= $totalLogins ?> Sesi Login</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="audit-stats-card">
                            <div class="audit-stats-icon blue">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                            <div>
                                <p>Login Terakhir</p>
                                <strong style="font-size: 1.05rem;"><?= htmlspecialchars(formatAuditTimestamp($lastAdminLoginTime), ENT_QUOTES, 'UTF-8') ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="audit-stats-card">
                            <div class="audit-stats-icon amber">
                                <i class="fa-solid fa-user-shield"></i>
                            </div>
                            <div>
                                <p>Admin Yang Sedang Aktif</p>
                                <strong><?= htmlspecialchars($adminUser, ENT_QUOTES, 'UTF-8') ?></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Table Card -->
                <article class="audit-table-card">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 pb-3 border-bottom">
                        <div>
                            <h2 style="font-size: 1.15rem; font-weight: 800; color: #1a2e22; margin: 0 0 3px;">
                                <i class="fa-solid fa-table-list text-success me-2"></i>Daftar Sesi Login
                            </h2>
                            <small class="text-muted">Diurutkan berdasarkan waktu login paling baru (WITA / UTC+8).</small>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <div class="audit-search-wrap">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" id="auditSearchInput" class="audit-search-input" placeholder="Cari user, IP, atau browser..." onkeyup="filterLoginTable()">
                            </div>
                            <?php if ($excludeLocalhost): ?>
                                <a href="admin_login_history.php?all=1" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1 small d-inline-flex align-items-center gap-1" title="Saat ini menyaring IP 127.0.0.1. Klik untuk menampilkan seluruh IP.">
                                    <i class="fa-solid fa-filter text-success"></i>
                                    <span style="font-size: 0.78rem;">Menyaring 127.0.0.1</span>
                                </a>
                            <?php else: ?>
                                <a href="admin_login_history.php" class="btn btn-sm btn-success rounded-pill px-2 py-1 small d-inline-flex align-items-center gap-1 text-white" title="Semua IP tampil termasuk 127.0.0.1. Klik untuk menyaring IP lokal.">
                                    <i class="fa-solid fa-eye"></i>
                                    <span style="font-size: 0.78rem;">Semua IP (Klik Saring)</span>
                                </a>
                            <?php endif; ?>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-3 py-2 fw-semibold d-none d-sm-inline-flex align-items-center">
                                <i class="fa-solid fa-file-shield me-1"></i> Audit Server Aktif
                            </span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table admin-dashboard-login-table align-middle mb-0" id="loginAuditTable">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 50px;">#</th>
                                    <th scope="col">Administrator</th>
                                    <th scope="col">Waktu Login</th>
                                    <th scope="col">Alamat IP</th>
                                    <th scope="col">Perangkat & Browser</th>
                                    <th scope="col" class="text-center" style="width: 120px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($allAdminLogins)): ?>
                                    <tr id="emptyRow">
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <i class="fa-solid fa-inbox fa-2x mb-2 d-block text-muted"></i>
                                            <?php if ($excludeLocalhost): ?>
                                                Belum ada rekaman riwayat login dari IP eksternal / publik.<br>
                                                <small class="text-muted">Aktivitas login dari IP lokal <code>127.0.0.1</code> tetap tersimpan di log audit server. <a href="admin_login_history.php?all=1" class="text-success fw-bold text-decoration-none ms-1">Tampilkan Semua (Termasuk Localhost)</a></small>
                                            <?php else: ?>
                                                Belum ada rekaman riwayat login di file log audit.
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($allAdminLogins as $index => $item): ?>
                                        <tr class="audit-row" data-search="<?= strtolower(htmlspecialchars($item['username'] . ' ' . $item['ip'] . ' ' . $item['device'] . ' ' . $item['formatted_time'], ENT_QUOTES, 'UTF-8')) ?>">
                                            <td class="text-muted fw-bold"><?= $index + 1 ?></td>
                                            <td>
                                                <div class="admin-user-cell">
                                                    <div class="user-avatar">
                                                        <i class="fa-solid fa-user-shield"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark"><?= htmlspecialchars($item['username'], ENT_QUOTES, 'UTF-8') ?></div>
                                                        <?php if ($index === 0): ?>
                                                            <small class="text-success fw-semibold"><i class="fa-solid fa-circle fa-2xs me-1"></i>Sesi Terkini</small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-dark"><?= htmlspecialchars($item['formatted_time'], ENT_QUOTES, 'UTF-8') ?></div>
                                                <small class="text-muted">Zona WITA</small>
                                            </td>
                                            <td>
                                                <span class="badge-ip-address">
                                                    <i class="fa-solid fa-network-wired me-1"></i><?= htmlspecialchars($item['ip'], ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="device-badge">
                                                    <i class="fa-solid fa-laptop"></i>
                                                    <?= htmlspecialchars($item['device'], ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge-login-success">
                                                    <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($item['status'], ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-4 pt-3 border-top text-muted" style="font-size: 0.78rem;">
                        <div>
                            <i class="fa-solid fa-shield-halved text-success me-1"></i> Status: Audit Log Server Terproteksi (Sisi Server)
                        </div>
                        <div id="rowCountDisplay">
                            Menampilkan <?= $totalLogins ?> catatan sesi login
                        </div>
                    </div>
                </article>
            </div>
        </section>
    </main>

    <!-- Modal Konfirmasi Logout Administrator -->
    <div class="modal fade modal-logout" id="modalConfirmLogout" tabindex="-1" aria-labelledby="modalLogoutTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
            <div class="modal-content text-center p-4">
                <div class="modal-logout-icon-wrap">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </div>
                <h4 class="modal-logout-title" id="modalLogoutTitle">Konfirmasi Keluar</h4>
                <p class="modal-logout-desc">
                    Apakah Anda yakin ingin mengakhiri sesi dan keluar dari Panel Administrator?
                </p>
                <div class="modal-logout-user-tag">
                    <i class="fa-solid fa-user-shield text-success"></i>
                    <span>Sesi: <strong><?= htmlspecialchars($adminUser, ENT_QUOTES, 'UTF-8') ?></strong></span>
                </div>
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <button type="button" class="btn btn-modal-logout-cancel flex-grow-1" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="button" class="btn btn-modal-logout-confirm flex-grow-1" id="btnConfirmLogout" onclick="executeLogoutAdmin()">
                        <i class="fa-solid fa-right-from-bracket me-1"></i> Ya, Keluar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterLoginTable() {
            const query = (document.getElementById('auditSearchInput').value || '').trim().toLowerCase();
            const rows = document.querySelectorAll('#loginAuditTable tbody tr.audit-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const searchData = row.getAttribute('data-search') || '';
                if (!query || searchData.includes(query)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            const countDisplay = document.getElementById('rowCountDisplay');
            if (countDisplay) {
                countDisplay.textContent = query 
                    ? `Menampilkan ${visibleCount} dari ${rows.length} catatan (hasil filter)` 
                    : `Menampilkan ${rows.length} catatan sesi login`;
            }
        }

        function logoutAdmin() {
            const modalEl = document.getElementById('modalConfirmLogout');
            if (modalEl) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            } else if (confirm('Apakah Anda yakin ingin keluar dari Panel Administrator?')) {
                executeLogoutAdmin();
            }
        }

        function executeLogoutAdmin() {
            const btn = document.getElementById('btnConfirmLogout');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mengeluarkan...';
            }
            localStorage.removeItem('sisfor_admin_logged_in');
            localStorage.removeItem('isAdminLoggedIn');
            fetch('api/logout.php', { method: 'POST' })
                .finally(() => {
                    window.location.href = 'index.html?logout=1';
                });
        }
    </script>
</body>
</html>
