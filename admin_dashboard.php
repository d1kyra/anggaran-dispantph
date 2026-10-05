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
$adminLoginTimestamp = (int) ($_SESSION['login_time'] ?? time());
$adminLoginIp = (string) ($_SESSION['admin_login_ip'] ?? ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
$lastAdminLogin = getLatestAdminLogin();
$lastAdminName = $lastAdminLogin ? (string) ($lastAdminLogin['context']['username'] ?? $lastAdminLogin['user'] ?? $adminUser) : '—';
$lastAdminLoginTime = (string) ($lastAdminLogin['timestamp'] ?? '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrator - SISFOR Anggaran</title>
    <link rel="icon" type="image/svg+xml" href="logo.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=<?= filemtime(__DIR__ . '/style.css') ?>">
    <style>
        /* ==========================================================================
           ADMIN DASHBOARD DEDICATED STYLES (EMBEDDED TO PREVENT BROWSER CACHE LAG)
           ========================================================================== */
        .admin-stat-banner {
            display: flex !important;
            align-items: center !important;
            gap: 16px !important;
            padding: 16px 20px !important;
            background: #ffffff !important;
            border: 1px solid #e2ede5 !important;
            border-radius: 16px !important;
            box-shadow: 0 4px 18px rgba(10, 40, 20, 0.05) !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease !important;
            min-height: 84px !important;
        }
        .admin-stat-banner:hover {
            transform: translateY(-2px) !important;
            border-color: #a5d6a7 !important;
            box-shadow: 0 8px 24px rgba(10, 40, 20, 0.09) !important;
        }
        .admin-stat-banner-info {
            display: flex !important;
            flex-direction: column !important;
            gap: 2px !important;
        }
        .admin-stat-banner-info small {
            font-size: 0.70rem !important;
            font-weight: 800 !important;
            letter-spacing: 0.07em !important;
            color: #64748b !important;
            text-transform: uppercase !important;
            line-height: 1 !important;
        }
        .admin-stat-banner-info strong {
            font-family: 'Outfit', sans-serif !important;
            font-size: 1.45rem !important;
            font-weight: 800 !important;
            color: #1a2e22 !important;
            line-height: 1.15 !important;
        }
        .admin-stat-banner-link {
            margin-left: auto !important;
            width: 34px !important;
            height: 34px !important;
            display: grid !important;
            place-items: center !important;
            border-radius: 10px !important;
            background: #f0f7f2 !important;
            color: #2e7d32 !important;
            text-decoration: none !important;
            font-size: 0.85rem !important;
            border: 1px solid #dceddd !important;
            transition: all 0.2s ease !important;
        }
        .admin-stat-banner-link:hover {
            background: #2e7d32 !important;
            color: #ffffff !important;
            border-color: #2e7d32 !important;
            transform: translateX(2px) !important;
        }
        .admin-stat-banner-badge {
            margin-left: auto !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 5px !important;
            font-size: 0.74rem !important;
            font-weight: 700 !important;
            color: #1b5e20 !important;
            background: #e8f5e9 !important;
            border: 1px solid #c8e6c9 !important;
            padding: 5px 12px !important;
            border-radius: 30px !important;
        }

        /* Chart Card & Header */
        .admin-chart-card {
            padding: 22px 24px !important;
            border: 1px solid #e2ede5 !important;
            border-radius: 20px !important;
            background: #ffffff !important;
            box-shadow: 0 4px 20px rgba(10, 40, 20, 0.05) !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease !important;
        }
        .admin-chart-card:hover {
            box-shadow: 0 10px 28px rgba(10, 40, 20, 0.08) !important;
        }
        .admin-chart-header {
            display: flex !important;
            align-items: flex-start !important;
            justify-content: space-between !important;
            gap: 12px !important;
            flex-wrap: wrap !important;
            margin-bottom: 16px !important;
        }
        .admin-chart-heading-text p {
            margin: 0 0 3px !important;
            color: #64748b !important;
            font-size: 0.70rem !important;
            font-weight: 800 !important;
            letter-spacing: 0.08em !important;
            text-transform: uppercase !important;
        }
        .admin-chart-heading-text h2 {
            margin: 0 !important;
            color: #1a2e22 !important;
            font-family: 'Outfit', sans-serif !important;
            font-size: 1.25rem !important;
            font-weight: 800 !important;
            letter-spacing: -0.2px !important;
        }
        .admin-chart-controls {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            flex-wrap: wrap !important;
        }
        .admin-badge-year {
            display: inline-flex !important;
            align-items: center !important;
            padding: 5px 12px !important;
            border-radius: 50px !important;
            background: #e8f5e9 !important;
            color: #1b5e20 !important;
            border: 1px solid #c8e6c9 !important;
            font-size: 0.74rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.02em !important;
            white-space: nowrap !important;
        }
        .admin-chart-btn-group {
            display: inline-flex !important;
            align-items: center !important;
            background: #f1f5f2 !important;
            padding: 4px !important;
            border-radius: 50px !important;
            border: 1px solid #dceddd !important;
        }
        .btn-chart-toggle {
            display: inline-flex !important;
            align-items: center !important;
            border: none !important;
            background: transparent !important;
            color: #64748b !important;
            font-size: 0.74rem !important;
            font-weight: 700 !important;
            padding: 5px 12px !important;
            border-radius: 50px !important;
            cursor: pointer !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            white-space: nowrap !important;
        }
        .btn-chart-toggle:hover {
            color: #1b5e20 !important;
        }
        .btn-chart-toggle.active {
            background: #ffffff !important;
            color: #1b5e20 !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.09) !important;
        }

        /* KPI Chips Strip */
        .admin-kpi-grid {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 8px !important;
            margin-bottom: 16px !important;
        }
        @media (max-width: 576px) {
            .admin-kpi-grid {
                grid-template-columns: 1fr !important;
            }
        }
        .admin-kpi-card {
            background: #f8faf8 !important;
            border: 1px solid #edf4ee !important;
            border-radius: 12px !important;
            padding: 8px 12px !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 2px !important;
            transition: all 0.2s ease !important;
        }
        .admin-kpi-card:hover {
            background: #ffffff !important;
            border-color: #dceddd !important;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.04) !important;
        }
        .admin-kpi-card-label {
            font-size: 0.70rem !important;
            font-weight: 600 !important;
            color: #64748b !important;
            display: flex !important;
            align-items: center !important;
            gap: 4px !important;
        }
        .admin-kpi-card-value {
            font-family: 'Outfit', sans-serif !important;
            font-size: 0.96rem !important;
            font-weight: 800 !important;
            color: #1a2e22 !important;
            line-height: 1.25 !important;
        }
        .admin-overview-chart-wrap {
            position: relative !important;
            height: 250px !important;
            width: 100% !important;
            margin-top: 8px !important;
        }
    </style>
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
        <a class="admin-sidebar-brand" href="admin_dashboard.php"><span><i class="fa-solid fa-wheat-awn"></i></span><strong>SISFOR <small>ANGGARAN</small></strong></a>
        <p class="admin-sidebar-label">MENU UTAMA</p>
        <nav class="admin-sidebar-nav">
            <a class="active" href="admin_dashboard.php"><i class="fa-solid fa-table-cells-large"></i> Dashboard</a>
            <a href="admin.php"><i class="fa-solid fa-building-columns"></i> Kelola APBD</a>
            <a href="admin.php#apbn"><i class="fa-solid fa-landmark"></i> Kelola APBN</a>
        </nav>
        <p class="admin-sidebar-label mt-4">AKUN</p>
        <a href="admin_login_history.php" class="admin-last-login" title="Klik untuk melihat riwayat lengkap semua login">
            <span class="admin-last-login-icon"><i class="fa-solid fa-user-clock"></i></span>
            <div>
                <small>LOGIN TERAKHIR</small>
                <strong><?= htmlspecialchars($lastAdminName, ENT_QUOTES, 'UTF-8') ?></strong>
                <time id="adminLastLoginTime" datetime="<?= htmlspecialchars($lastAdminLoginTime, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(formatAuditTimestamp($lastAdminLoginTime), ENT_QUOTES, 'UTF-8') ?></time>
            </div>
            <span class="admin-last-login-arrow"><i class="fa-solid fa-chevron-right"></i></span>
        </a>
        <nav class="admin-sidebar-nav">
            <a href="admin_login_history.php"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Login</a>
            <a href="index.html" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat Web Publik</a>
            <button type="button" onclick="logoutAdmin()"><i class="fa-solid fa-right-from-bracket"></i> Keluar</button>
        </nav>
        <div class="admin-sidebar-user"><i class="fa-solid fa-user-shield"></i><span><?= htmlspecialchars($adminUser, ENT_QUOTES, 'UTF-8') ?><small>Administrator aktif</small></span></div>
    </aside>

    <main class="admin-workspace">
        <section class="admin-command-center min-vh-100">
            <div class="container px-3 px-md-4 py-4 py-md-5">
                <div class="admin-command-heading">
                    <div><span class="admin-eyebrow"><i class="fa-solid fa-shield-halved"></i> Sesi terlindungi</span><h1>Ruang Kendali Administrator</h1><p>Ringkasan sistem dan aktivitas sesi administrator.</p></div>
                    <div class="admin-session-state"><span></span> Mode admin aktif</div>
                </div>
                <!-- Stat Summary Banners -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-4">
                        <div class="admin-stat-banner">
                            <span class="status-icon blue"><i class="fa-solid fa-building-columns"></i></span>
                            <div class="admin-stat-banner-info">
                                <small>UNIT KERJA APBD</small>
                                <strong><span id="adminApbdCount">0</span> <span style="font-size: 0.85rem; font-weight: 600; color: #64748b;">Unit</span></strong>
                            </div>
                            <a href="admin.php" class="admin-stat-banner-link" title="Kelola Unit APBD"><i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="admin-stat-banner">
                            <span class="status-icon pink"><i class="fa-solid fa-landmark"></i></span>
                            <div class="admin-stat-banner-info">
                                <small>KEGIATAN APBN</small>
                                <strong><span id="adminApbnCount">0</span> <span style="font-size: 0.85rem; font-weight: 600; color: #64748b;">Kegiatan</span></strong>
                            </div>
                            <a href="admin.php#apbn" class="admin-stat-banner-link" title="Kelola Kegiatan APBN"><i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="admin-stat-banner">
                            <span class="status-icon green"><i class="fa-regular fa-calendar-check"></i></span>
                            <div class="admin-stat-banner-info">
                                <small>BATAS PERIODE AKTIF</small>
                                <strong id="adminCurrentPeriod">—</strong>
                            </div>
                            <span class="admin-stat-banner-badge"><i class="fa-solid fa-circle-check"></i> Aktif</span>
                        </div>
                    </div>
                </div>

                <!-- Dual Budget Charts (APBD & APBN) -->
                <div class="row g-3 mb-4">
                    <!-- KARTU APBD -->
                    <div class="col-12 col-xl-6">
                        <article class="admin-chart-card h-100">
                            <div class="admin-chart-header">
                                <div class="admin-chart-heading-text">
                                    <p><i class="fa-solid fa-chart-line text-primary me-1"></i> RINGKASAN ANGGARAN APBD</p>
                                    <h2>Tren Realisasi APBD</h2>
                                </div>
                                <div class="admin-chart-controls">
                                    <span class="admin-badge-year"><i class="fa-regular fa-calendar me-1"></i> 2022–2026</span>
                                    <div class="admin-chart-btn-group" role="group">
                                        <button type="button" class="btn-chart-toggle active" id="btnApbdNominal" onclick="setAdminApbdMode('nominal')">
                                            <i class="fa-solid fa-chart-column me-1"></i> Nominal (Rp)
                                        </button>
                                        <button type="button" class="btn-chart-toggle" id="btnApbdPercent" onclick="setAdminApbdMode('percent')">
                                            <i class="fa-solid fa-arrow-trend-up me-1"></i> Capaian (%)
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- KPI Summary Chips APBD -->
                            <div class="admin-kpi-grid">
                                <div class="admin-kpi-card">
                                    <span class="admin-kpi-card-label">Total Pagu:</span>
                                    <span class="admin-kpi-card-value" id="apbdKpiPagu">Rp 0</span>
                                </div>
                                <div class="admin-kpi-card">
                                    <span class="admin-kpi-card-label">Realisasi:</span>
                                    <span class="admin-kpi-card-value text-success" id="apbdKpiReal">Rp 0</span>
                                </div>
                                <div class="admin-kpi-card">
                                    <span class="admin-kpi-card-label">Sisa Pagu:</span>
                                    <span class="admin-kpi-card-value text-warning-emphasis" id="apbdKpiSisa">Rp 0</span>
                                </div>
                            </div>

                            <div class="admin-overview-chart-wrap">
                                <canvas id="adminApbdChart"></canvas>
                            </div>
                        </article>
                    </div>

                    <!-- KARTU APBN -->
                    <div class="col-12 col-xl-6">
                        <article class="admin-chart-card h-100">
                            <div class="admin-chart-header">
                                <div class="admin-chart-heading-text">
                                    <p><i class="fa-solid fa-chart-line text-success me-1"></i> RINGKASAN ANGGARAN APBN</p>
                                    <h2>Tren Realisasi APBN</h2>
                                </div>
                                <div class="admin-chart-controls">
                                    <span class="admin-badge-year"><i class="fa-regular fa-calendar me-1"></i> 2022–2026</span>
                                    <div class="admin-chart-btn-group" role="group">
                                        <button type="button" class="btn-chart-toggle active" id="btnApbnNominal" onclick="setAdminApbnMode('nominal')">
                                            <i class="fa-solid fa-chart-column me-1"></i> Nominal (Rp)
                                        </button>
                                        <button type="button" class="btn-chart-toggle" id="btnApbnPercent" onclick="setAdminApbnMode('percent')">
                                            <i class="fa-solid fa-arrow-trend-up me-1"></i> Capaian (%)
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- KPI Summary Chips APBN -->
                            <div class="admin-kpi-grid">
                                <div class="admin-kpi-card">
                                    <span class="admin-kpi-card-label">Total Pagu:</span>
                                    <span class="admin-kpi-card-value" id="apbnKpiPagu">Rp 0</span>
                                </div>
                                <div class="admin-kpi-card">
                                    <span class="admin-kpi-card-label">Realisasi:</span>
                                    <span class="admin-kpi-card-value text-success" id="apbnKpiReal">Rp 0</span>
                                </div>
                                <div class="admin-kpi-card">
                                    <span class="admin-kpi-card-label">Sisa Anggaran:</span>
                                    <span class="admin-kpi-card-value text-warning-emphasis" id="apbnKpiSisa">Rp 0</span>
                                </div>
                            </div>

                            <div class="admin-overview-chart-wrap">
                                <canvas id="adminApbnChart"></canvas>
                            </div>
                        </article>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-12 col-lg-5"><article class="admin-profile-card h-100"><div class="admin-profile-icon"><i class="fa-solid fa-user-shield"></i></div><div><p class="admin-card-label">ADMINISTRATOR YANG LOGIN</p><h2><?= htmlspecialchars($adminUser, ENT_QUOTES, 'UTF-8') ?></h2><p class="mb-0 text-muted">Akses penuh untuk pengelolaan data APBD dan APBN.</p></div></article></div>
                    <div class="col-12 col-sm-6 col-lg-3"><article class="admin-session-card h-100"><i class="fa-regular fa-calendar-check"></i><p>Waktu login</p><strong id="adminLoginTime">Memuat…</strong><small id="adminLoginDay"></small></article></div>
                    <div class="col-12 col-sm-6 col-lg-2"><article class="admin-session-card h-100"><i class="fa-solid fa-network-wired"></i><p>Alamat IP</p><strong class="admin-ip-value"><?= htmlspecialchars($adminLoginIp, ENT_QUOTES, 'UTF-8') ?></strong><small>IP saat login</small></article></div>
                    <div class="col-12 col-lg-2"><article class="admin-session-card admin-duration-card h-100"><i class="fa-regular fa-clock"></i><p>Durasi mode admin</p><strong id="adminSessionDuration">00:00:00</strong><small>Dicatat saat logout</small></article></div>
                </div>
                <div class="admin-dashboard-actions">
                    <a href="admin.php" class="admin-dashboard-action"><i class="fa-solid fa-building-columns"></i><span>Kelola Unit APBD</span><i class="fa-solid fa-arrow-right"></i></a>
                    <a href="admin.php#apbn" class="admin-dashboard-action"><i class="fa-solid fa-landmark"></i><span>Kelola Kegiatan APBN</span><i class="fa-solid fa-arrow-right"></i></a>
                    <a href="admin_login_history.php" class="admin-dashboard-action"><i class="fa-solid fa-clock-rotate-left"></i><span>Riwayat Login Admin</span><i class="fa-solid fa-arrow-right"></i></a>
                </div>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        const sessionStart = <?= json_encode($adminLoginTimestamp * 1000) ?>;
        function formatDuration(seconds) { const h = String(Math.floor(seconds / 3600)).padStart(2, '0'); const m = String(Math.floor(seconds % 3600 / 60)).padStart(2, '0'); const s = String(seconds % 60).padStart(2, '0'); return `${h}:${m}:${s}`; }
        function initSession() {
            const login = new Date(sessionStart);
            document.getElementById('adminLoginTime').textContent = login.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            document.getElementById('adminLoginDay').textContent = login.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            const last = document.getElementById('adminLastLoginTime');
            if (last && last.dateTime) {
                const lastDate = new Date(last.dateTime);
                last.textContent = Number.isNaN(lastDate.getTime()) ? 'Belum tersedia' : lastDate.toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
            }
            const update = () => document.getElementById('adminSessionDuration').textContent = formatDuration(Math.max(0, Math.floor((Date.now() - sessionStart) / 1000)));
            update(); window.setInterval(update, 1000);
        }

        // ==========================================
        // FORMATTER & APBN HELPER UTILITIES
        // ==========================================
        function formatRupiah(angka) {
            if (angka === 0 || !angka) return 'Rp 0';
            const isNegative = angka < 0;
            const formatted = Math.abs(angka).toLocaleString('id-ID');
            return (isNegative ? 'Rp - ' : 'Rp ') + formatted;
        }

        function formatPersen(angka) {
            if (angka === 0 || !angka) return '0%';
            return Number(angka).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 2 }) + '%';
        }

        function getAPBNItemValues(item) {
            const pAwal = item.paguDipa !== undefined ? item.paguDipa : (item.paguAwal || 0);
            const pRev = item.paguRevisi !== undefined ? item.paguRevisi : (item.paguBlokir || item.paguRev2024 || 0);
            const pTahunan = item.paguSetelahBlokir !== undefined ? item.paguSetelahBlokir : (pAwal + pRev);
            const realRp = item.realisasiRp || 0;
            const realPct = pTahunan > 0 ? (realRp / pTahunan) * 100 : (item.realisasiPersen || 0);
            const realFisik = item.realisasiFisik !== undefined ? item.realisasiFisik : 0;
            const sisa = item.sisaAnggaran !== undefined ? item.sisaAnggaran : (pTahunan - realRp);
            return { pAwal, pRev, pTahunan, realRp, realPct, realFisik, sisa };
        }

        // ==========================================
        // CHART INSTANCES & STATE
        // ==========================================
        let adminApbdChartInstance = null;
        let adminApbnChartInstance = null;
        let adminApbdMode = 'nominal';
        let adminApbnMode = 'nominal';
        let apbdChartData = null;
        let apbnChartData = null;

        const chartTooltipBase = {
            backgroundColor: 'rgba(15, 23, 42, 0.95)',
            titleFont: { family: "'Outfit', sans-serif", size: 13, weight: '700' },
            bodyFont: { family: "'Plus Jakarta Sans', sans-serif", size: 12 },
            padding: 10,
            cornerRadius: 8
        };

        function getNominalChartOptions() {
            return {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 12,
                            usePointStyle: true,
                            pointStyle: 'rectRounded',
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: '600' },
                            color: '#334155'
                        }
                    },
                    tooltip: {
                        ...chartTooltipBase,
                        callbacks: {
                            label: function (c) {
                                let l = c.dataset.label || '';
                                if (l) l += ': ';
                                if (c.parsed.y !== null) l += formatRupiah(c.parsed.y);
                                return l;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: '600' },
                            color: '#64748b'
                        }
                    },
                    y: {
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                            color: '#64748b',
                            callback: function (val) {
                                if (Math.abs(val) >= 1e9) {
                                    return (val / 1e9).toLocaleString('id-ID', { maximumFractionDigits: 1 }) + ' M';
                                } else if (Math.abs(val) >= 1e6) {
                                    return (val / 1e6).toLocaleString('id-ID', { maximumFractionDigits: 0 }) + ' Jt';
                                }
                                return val.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            };
        }

        function getPercentChartOptions() {
            return {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 12,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: '600' },
                            color: '#334155'
                        }
                    },
                    tooltip: {
                        ...chartTooltipBase,
                        callbacks: {
                            label: function (c) {
                                let l = c.dataset.label || '';
                                if (l) l += ': ';
                                if (c.parsed.y !== null) l += Number(c.parsed.y).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 2 }) + '%';
                                return l;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: '600' },
                            color: '#64748b'
                        }
                    },
                    y: {
                        min: 0,
                        max: 105,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: {
                            stepSize: 20,
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                            color: '#64748b',
                            callback: function (val) {
                                return val + '%';
                            }
                        }
                    }
                }
            };
        }

        function renderAdminApbdChart() {
            if (adminApbdChartInstance) {
                adminApbdChartInstance.destroy();
                adminApbdChartInstance = null;
            }
            const ctx = document.getElementById('adminApbdChart');
            if (!ctx || !apbdChartData) return;

            if (adminApbdMode === 'nominal') {
                adminApbdChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: apbdChartData.labels,
                        datasets: [
                            { label: 'Pagu Tahunan', data: apbdChartData.pagu, backgroundColor: 'rgba(30, 64, 175, 0.85)', borderColor: '#1e40af', borderWidth: 1.5, borderRadius: 6, maxBarThickness: 34 },
                            { label: 'Realisasi Keuangan', data: apbdChartData.real, backgroundColor: 'rgba(16, 185, 129, 0.85)', borderColor: '#059669', borderWidth: 1.5, borderRadius: 6, maxBarThickness: 34 },
                            { label: 'Sisa Pagu', data: apbdChartData.sisa, backgroundColor: 'rgba(245, 158, 11, 0.85)', borderColor: '#d97706', borderWidth: 1.5, borderRadius: 6, maxBarThickness: 34 }
                        ]
                    },
                    options: getNominalChartOptions()
                });
            } else {
                adminApbdChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: apbdChartData.labels,
                        datasets: [
                            { label: 'Realisasi Keuangan (%)', data: apbdChartData.pctKeu, borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.12)', borderWidth: 2.8, pointBackgroundColor: '#10b981', pointBorderColor: '#ffffff', pointBorderWidth: 2, pointRadius: 5, pointHoverRadius: 7, tension: 0.35, fill: true },
                            { label: 'Realisasi Fisik (%)', data: apbdChartData.pctFisik, borderColor: '#0284c7', backgroundColor: 'rgba(2, 132, 199, 0.08)', borderWidth: 2.8, pointBackgroundColor: '#0284c7', pointBorderColor: '#ffffff', pointBorderWidth: 2, pointRadius: 5, pointHoverRadius: 7, tension: 0.35, fill: true }
                        ]
                    },
                    options: getPercentChartOptions()
                });
            }
        }

        function renderAdminApbnChart() {
            if (adminApbnChartInstance) {
                adminApbnChartInstance.destroy();
                adminApbnChartInstance = null;
            }
            const ctx = document.getElementById('adminApbnChart');
            if (!ctx || !apbnChartData) return;

            if (adminApbnMode === 'nominal') {
                adminApbnChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: apbnChartData.labels,
                        datasets: [
                            { label: 'Pagu Tahunan', data: apbnChartData.pagu, backgroundColor: 'rgba(46, 125, 50, 0.85)', borderColor: '#2e7d32', borderWidth: 1.5, borderRadius: 6, maxBarThickness: 34 },
                            { label: 'Realisasi Keuangan', data: apbnChartData.real, backgroundColor: 'rgba(2, 136, 209, 0.85)', borderColor: '#0288d1', borderWidth: 1.5, borderRadius: 6, maxBarThickness: 34 },
                            { label: 'Sisa Anggaran', data: apbnChartData.sisa, backgroundColor: 'rgba(245, 158, 11, 0.85)', borderColor: '#d97706', borderWidth: 1.5, borderRadius: 6, maxBarThickness: 34 }
                        ]
                    },
                    options: getNominalChartOptions()
                });
            } else {
                adminApbnChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: apbnChartData.labels,
                        datasets: [
                            { label: 'Realisasi Keuangan (%)', data: apbnChartData.pctKeu, borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.12)', borderWidth: 2.8, pointBackgroundColor: '#10b981', pointBorderColor: '#ffffff', pointBorderWidth: 2, pointRadius: 5, pointHoverRadius: 7, tension: 0.35, fill: true },
                            { label: 'Realisasi Fisik (%)', data: apbnChartData.pctFisik, borderColor: '#0284c7', backgroundColor: 'rgba(2, 132, 199, 0.08)', borderWidth: 2.8, pointBackgroundColor: '#0284c7', pointBorderColor: '#ffffff', pointBorderWidth: 2, pointRadius: 5, pointHoverRadius: 7, tension: 0.35, fill: true }
                        ]
                    },
                    options: getPercentChartOptions()
                });
            }
        }

        function setAdminApbdMode(mode) {
            adminApbdMode = mode;
            const btnNominal = document.getElementById('btnApbdNominal');
            const btnPercent = document.getElementById('btnApbdPercent');
            if (btnNominal) btnNominal.classList.toggle('active', mode === 'nominal');
            if (btnPercent) btnPercent.classList.toggle('active', mode === 'percent');
            renderAdminApbdChart();
        }

        function setAdminApbnMode(mode) {
            adminApbnMode = mode;
            const btnNominal = document.getElementById('btnApbnNominal');
            const btnPercent = document.getElementById('btnApbnPercent');
            if (btnNominal) btnNominal.classList.toggle('active', mode === 'nominal');
            if (btnPercent) btnPercent.classList.toggle('active', mode === 'percent');
            renderAdminApbnChart();
        }

        async function loadDashboard() {
            const [apbdResponse, apbnResponse] = await Promise.all([fetch('api/get_apbd.php'), fetch('api/get_apbn.php')]);
            const apbd = await apbdResponse.json(); const apbn = await apbnResponse.json();
            const units = apbd.status === 'success' ? apbd.data : {}; const apbnData = apbn.status === 'success' ? apbn.data : {};

            document.getElementById('adminApbdCount').textContent = Object.keys(units).length;
            document.getElementById('adminApbnCount').textContent = Object.values(apbnData).reduce((count, entries) => count + (Array.isArray(entries) ? entries.length : 0), 0);
            const activePeriod = apbd.periodeAktif || apbn.periodeAktif || 's.d Juni';
            document.getElementById('adminCurrentPeriod').textContent = activePeriod;

            const years = ['2022', '2023', '2024', '2025', '2026'];
            const labels = years.map(th => th === '2026' ? `${th} (${activePeriod})` : th);

            // APBD Calculation
            const apbdPagu = [], apbdReal = [], apbdSisa = [], apbdPctKeu = [], apbdPctFisik = [];
            let totalApbdPagu = 0, totalApbdReal = 0, totalApbdSisa = 0;

            years.forEach(year => {
                let pSum = 0, rSum = 0, fSum = 0, count = 0;
                Object.values(units).forEach(unit => {
                    const entry = unit[year] || {};
                    pSum += Number(entry.paguApbd || entry.paguTahunan || 0);
                    rSum += Number(entry.realisasiKeuangan || 0);
                    if (entry.realisasiFisik !== undefined && entry.realisasiFisik !== null && (entry.paguApbd || entry.realisasiKeuangan)) {
                        fSum += Number(entry.realisasiFisik || 0);
                        count++;
                    }
                });
                const sSum = pSum - rSum;
                apbdPagu.push(pSum);
                apbdReal.push(rSum);
                apbdSisa.push(sSum);
                apbdPctKeu.push(pSum > 0 ? (rSum / pSum) * 100 : 0);
                apbdPctFisik.push(count > 0 ? fSum / count : 0);
                totalApbdPagu += pSum;
                totalApbdReal += rSum;
                totalApbdSisa += sSum;
            });

            const avgApbdKeu = totalApbdPagu > 0 ? (totalApbdReal / totalApbdPagu) * 100 : 0;
            const elApbdP = document.getElementById('apbdKpiPagu');
            const elApbdR = document.getElementById('apbdKpiReal');
            const elApbdS = document.getElementById('apbdKpiSisa');
            if (elApbdP) elApbdP.textContent = formatRupiah(totalApbdPagu);
            if (elApbdR) elApbdR.innerHTML = `${formatRupiah(totalApbdReal)} <span class="badge bg-success bg-opacity-10 text-success ms-1">${formatPersen(avgApbdKeu)}</span>`;
            if (elApbdS) elApbdS.textContent = formatRupiah(totalApbdSisa);

            apbdChartData = { labels, pagu: apbdPagu, real: apbdReal, sisa: apbdSisa, pctKeu: apbdPctKeu, pctFisik: apbdPctFisik };

            // APBN Calculation
            const apbnPagu = [], apbnReal = [], apbnSisa = [], apbnPctKeu = [], apbnPctFisik = [];
            let totalApbnPagu = 0, totalApbnReal = 0, totalApbnSisa = 0;

            years.forEach(year => {
                let pSum = 0, rSum = 0, sSum = 0, fSum = 0, count = 0;
                const entries = Array.isArray(apbnData[year]) ? apbnData[year] : [];
                entries.forEach(entry => {
                    const v = getAPBNItemValues(entry);
                    pSum += v.pTahunan;
                    rSum += v.realRp;
                    sSum += v.sisa;
                    fSum += v.realFisik;
                    count++;
                });
                apbnPagu.push(pSum);
                apbnReal.push(rSum);
                apbnSisa.push(sSum);
                apbnPctKeu.push(pSum > 0 ? (rSum / pSum) * 100 : 0);
                apbnPctFisik.push(count > 0 ? fSum / count : 0);
                totalApbnPagu += pSum;
                totalApbnReal += rSum;
                totalApbnSisa += sSum;
            });

            const avgApbnKeu = totalApbnPagu > 0 ? (totalApbnReal / totalApbnPagu) * 100 : 0;
            const elApbnP = document.getElementById('apbnKpiPagu');
            const elApbnR = document.getElementById('apbnKpiReal');
            const elApbnS = document.getElementById('apbnKpiSisa');
            if (elApbnP) elApbnP.textContent = formatRupiah(totalApbnPagu);
            if (elApbnR) elApbnR.innerHTML = `${formatRupiah(totalApbnReal)} <span class="badge bg-success bg-opacity-10 text-success ms-1">${formatPersen(avgApbnKeu)}</span>`;
            if (elApbnS) elApbnS.textContent = formatRupiah(totalApbnSisa);

            apbnChartData = { labels, pagu: apbnPagu, real: apbnReal, sisa: apbnSisa, pctKeu: apbnPctKeu, pctFisik: apbnPctFisik };

            renderAdminApbdChart();
            renderAdminApbnChart();
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
        initSession(); loadDashboard().catch(console.warn);
    </script>
</body>
</html>
