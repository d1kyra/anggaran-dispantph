<?php
// ==============================================================================
// login.php - Halaman Login Administrator SISFOR Anggaran DISPANTPH Kaltim
// ==============================================================================
require_once __DIR__ . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'security.php';

emitSecurityHeaders();
ensureSecureSession();

// Jika admin sudah memiliki sesi aktif yang valid, langsung alihkan ke dashboard
if (!empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true && !isSessionExpired()) {
    refreshSessionActivity();
    header('Location: admin_dashboard.php');
    exit;
}

// Cek parameter notifikasi dari query string
$alertType = '';
$alertIcon = '';
$alertMsg = '';

if (isset($_GET['auth']) && $_GET['auth'] === 'required') {
    $alertType = 'warning';
    $alertIcon = 'fa-triangle-exclamation';
    $alertMsg = 'Sesi otentikasi diperlukan. Silakan login untuk mengakses Panel Administrator.';
} elseif (isset($_GET['expired']) && $_GET['expired'] == '1') {
    $alertType = 'info';
    $alertIcon = 'fa-clock-rotate-left';
    $alertMsg = 'Sesi Anda telah kedaluwarsa demi keamanan. Silakan masuk kembali.';
} elseif (isset($_GET['logout']) && $_GET['logout'] == '1') {
    $alertType = 'success';
    $alertIcon = 'fa-circle-check';
    $alertMsg = 'Anda telah berhasil keluar dari Panel Administrator.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Login Administrator - SISFOR Anggaran DISPANTPH Kaltim</title>

    <!-- Favicon / Tab Icon -->
    <link rel="icon" type="image/svg+xml" href="logo.svg">
    <link rel="alternate icon" type="image/png" href="logo.png">
    <link rel="shortcut icon" href="favicon.ico">

    <!-- Google Fonts: Outfit & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Dedicated Login Styling -->
    <style>
        :root {
            --primary-dark: #051d10;
            --primary-deep: #072a19;
            --primary-emerald: #1b5e20;
            --emerald-bright: #2e7d32;
            --emerald-light: #4caf50;
            --emerald-subtle: #e8f5e9;
            --accent-gold: #fbc02d;
            --accent-green: #81c784;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: radial-gradient(circle at 50% 15%, #1e5c33 0%, #0c381e 55%, #051d10 100%) no-repeat fixed center;
            background-size: cover;
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px 16px;
            margin: 0;
            position: relative;
            overflow-x: hidden;
            color: #ffffff;
        }

        /* Ambient Glowing Background Orbs Container (Fixed to prevent page overflow) */
        .bg-decorations {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }

        .bg-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            pointer-events: none;
            opacity: 0.45;
        }

        .shape-1 {
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(46, 125, 50, 0.6) 0%, rgba(5, 29, 16, 0) 70%);
            top: -120px;
            left: -100px;
            animation: floatSlow 18s ease-in-out infinite alternate;
        }

        .shape-2 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(27, 94, 32, 0.7) 0%, rgba(5, 29, 16, 0) 70%);
            bottom: -100px;
            right: -80px;
            animation: floatSlow 22s ease-in-out infinite alternate-reverse;
        }

        .shape-3 {
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(251, 192, 45, 0.22) 0%, rgba(5, 29, 16, 0) 70%);
            top: 40%;
            right: 15%;
            animation: floatSlow 26s ease-in-out infinite alternate;
        }

        .bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
            background-size: 28px 28px;
            opacity: 0.5;
            z-index: 0;
            pointer-events: none;
        }

        @keyframes floatSlow {
            0% { transform: translateY(0) scale(1); }
            100% { transform: translateY(30px) scale(1.08); }
        }

        /* Container & Card */
        .login-container {
            width: 100%;
            max-width: 430px;
            margin: auto;
            position: relative;
            z-index: 10;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.65);
            border-radius: 20px;
            padding: 28px 26px 22px;
            box-shadow: 0 20px 50px rgba(5, 29, 16, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.2);
            color: #1a2e1d;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .brand-badge {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);
            border: 2px solid rgba(255, 255, 255, 0.4);
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #a5d6a7;
            font-size: 1.45rem;
            box-shadow: 0 8px 20px rgba(27, 94, 32, 0.35);
            margin-bottom: 10px;
        }

        .brand-instansi-sub {
            font-size: 0.70rem;
            font-weight: 700;
            letter-spacing: 1.1px;
            color: #2e7d32;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .login-title {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.48rem;
            color: #072a19;
            margin-bottom: 4px;
            letter-spacing: -0.3px;
        }

        .login-subtitle {
            font-size: 0.83rem;
            color: #5d7563;
            line-height: 1.4;
            margin-bottom: 16px;
        }

        /* Form Controls */
        .form-label-custom {
            font-size: 0.79rem;
            font-weight: 700;
            color: #2c4233;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .input-group-custom {
            position: relative;
            display: flex;
            align-items: center;
            margin-bottom: 13px;
        }

        .input-group-custom .input-icon {
            position: absolute;
            left: 13px;
            color: #7b9481;
            font-size: 0.92rem;
            z-index: 5;
            transition: color 0.2s ease;
            pointer-events: none;
        }

        .input-group-custom .form-control-custom {
            width: 100%;
            height: 44px;
            padding: 8px 14px 8px 38px;
            background: #f7faf7;
            border: 1.5px solid #d5e4d7;
            border-radius: 11px;
            font-size: 0.90rem;
            font-family: inherit;
            color: #0c381e;
            transition: all 0.2s ease;
        }

        .input-group-custom.has-toggle .form-control-custom {
            padding-right: 40px;
        }

        .input-group-custom .form-control-custom:focus {
            background: #ffffff;
            border-color: #2e7d32;
            outline: none;
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.15);
        }

        .input-group-custom .form-control-custom:focus + .input-icon,
        .input-group-custom:focus-within .input-icon {
            color: #2e7d32;
        }

        .btn-toggle-password {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #7b9481;
            padding: 6px 8px;
            cursor: pointer;
            z-index: 5;
            border-radius: 6px;
            font-size: 0.85rem;
            transition: color 0.2s ease;
        }

        .btn-toggle-password:hover {
            color: #1b5e20;
        }

        /* Submit Button */
        .btn-submit-login {
            width: 100%;
            height: 44px;
            background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 11px;
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 0.94rem;
            letter-spacing: 0.2px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 6px 18px rgba(27, 94, 32, 0.3);
            transition: all 0.25s ease;
            cursor: pointer;
            margin-top: 4px;
        }

        .btn-submit-login:hover {
            background: linear-gradient(135deg, #388e3c 0%, #206f26 100%);
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(27, 94, 32, 0.38);
            color: #ffffff;
        }

        .btn-submit-login:active {
            transform: translateY(0);
            box-shadow: 0 3px 10px rgba(27, 94, 32, 0.28);
        }

        .btn-submit-login:disabled {
            opacity: 0.75;
            cursor: not-allowed;
            transform: none;
        }

        /* Nav Back Link */
        .back-link-wrapper {
            text-align: center;
            margin-top: 15px;
            padding-top: 13px;
            border-top: 1px solid #eaf1eb;
        }

        .back-link {
            color: #4b6653;
            text-decoration: none;
            font-size: 0.83rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .back-link i {
            transition: transform 0.2s ease;
        }

        .back-link:hover {
            color: #1b5e20;
        }

        .back-link:hover i {
            transform: translateX(-3px);
        }

        /* Alerts & Feedback */
        .status-alert {
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 0.82rem;
            margin-bottom: 14px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
            border: 1px solid transparent;
            line-height: 1.4;
        }

        .status-alert.alert-warning {
            background: #fff8e1;
            border-color: #ffe082;
            color: #8d6e1f;
        }

        .status-alert.alert-info {
            background: #e1f5fe;
            border-color: #81d4fa;
            color: #0277bd;
        }

        .status-alert.alert-success {
            background: #e8f5e9;
            border-color: #a5d6a7;
            color: #1b5e20;
        }

        .status-alert.alert-danger {
            background: #ffebee;
            border-color: #ffcdd2;
            color: #c62828;
        }

        /* Shake animation on error */
        @keyframes shakeEffect {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }

        .shake {
            animation: shakeEffect 0.45s ease-in-out;
        }

        /* Security Footer Info */
        .login-footer-info {
            text-align: center;
            margin-top: 15px;
            font-size: 0.74rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .login-footer-info a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
        }

        .login-footer-info a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            body {
                padding: 16px 12px;
            }

            .login-card {
                padding: 22px 18px 18px;
                border-radius: 18px;
            }

            .login-title {
                font-size: 1.35rem;
            }

            .brand-badge {
                width: 46px;
                height: 46px;
                font-size: 1.3rem;
                border-radius: 12px;
                margin-bottom: 8px;
            }
        }

        @media (max-height: 640px) {
            body {
                padding: 10px 12px;
            }
            .login-card {
                padding: 18px 20px 14px;
            }
            .brand-badge {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
                margin-bottom: 6px;
            }
            .login-subtitle {
                margin-bottom: 10px;
            }
            .input-group-custom {
                margin-bottom: 8px;
            }
            .input-group-custom .form-control-custom,
            .btn-submit-login {
                height: 38px;
            }
            .back-link-wrapper {
                margin-top: 10px;
                padding-top: 8px;
            }
            .login-footer-info {
                margin-top: 8px;
            }
        }
    </style>
</head>
<body>

    <!-- Ambient Glowing Shapes (Contained to prevent excess scroll/overflow) -->
    <div class="bg-decorations" aria-hidden="true">
        <div class="bg-shape shape-1"></div>
        <div class="bg-shape shape-2"></div>
        <div class="bg-shape shape-3"></div>
        <div class="bg-pattern"></div>
    </div>

    <div class="login-container">
        <div class="login-card" id="loginCard">
            <!-- Header Identitas Instansi -->
            <div class="text-center">
                <div class="brand-badge">
                    <i class="fa-solid fa-wheat-awn"></i>
                </div>
                <div class="brand-instansi-sub">DISPANTPH PROVINSI KALIMANTAN TIMUR</div>
                <h1 class="login-title">Panel Administrator</h1>
                <p class="login-subtitle">
                    Masuk ke Sistem Informasi Anggaran untuk mengelola data APBD & APBN.
                </p>
            </div>

            <!-- Pesan Status / Notifikasi -->
            <?php if (!empty($alertMsg)): ?>
            <div class="status-alert alert-<?= htmlspecialchars($alertType) ?>">
                <i class="fa-solid <?= htmlspecialchars($alertIcon) ?> mt-1 fs-6 flex-shrink-0"></i>
                <div><?= htmlspecialchars($alertMsg) ?></div>
            </div>
            <?php endif; ?>

            <!-- Client-side Error Alert Box -->
            <div id="clientAlert" class="status-alert alert-danger d-none">
                <i class="fa-solid fa-circle-exclamation mt-1 fs-6 flex-shrink-0"></i>
                <div id="clientAlertText">Username atau password tidak valid!</div>
            </div>

            <!-- Form Login -->
            <form id="formAdminLogin" onsubmit="handleFormLogin(event)" autocomplete="on">
                <div class="mb-1">
                    <label class="form-label-custom" for="loginUsername">
                        <span>Username</span>
                        <span class="text-muted fw-normal small">Wajib diisi</span>
                    </label>
                    <div class="input-group-custom">
                        <i class="fa-solid fa-user input-icon"></i>
                        <input type="text" 
                               id="loginUsername" 
                               name="username" 
                               class="form-control-custom" 
                               placeholder="Masukkan username admin" 
                               required 
                               autofocus 
                               autocomplete="username">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label-custom" for="loginPassword">
                        <span>Password</span>
                        <span class="text-muted fw-normal small">Wajib diisi</span>
                    </label>
                    <div class="input-group-custom has-toggle">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" 
                               id="loginPassword" 
                               name="password" 
                               class="form-control-custom" 
                               placeholder="Masukkan password" 
                               required 
                               autocomplete="current-password">
                        <button type="button" 
                                class="btn-toggle-password" 
                                id="btnTogglePass" 
                                onclick="togglePasswordVisibility()" 
                                title="Tampilkan / Sembunyikan sandi"
                                aria-label="Toggle password visibility">
                            <i class="fa-solid fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit-login" id="btnSubmit">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span id="btnSubmitText">Masuk ke Panel Admin</span>
                </button>
            </form>

            <!-- Navigasi Kembali ke Beranda -->
            <div class="back-link-wrapper">
                <a href="index.html" class="back-link" title="Kembali ke Halaman Beranda">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Kembali ke Halaman Utama</span>
                </a>
            </div>
        </div>

        <!-- Footer Copyright & Info Keamanan -->
        <div class="login-footer-info">
            <div>SIA DPTPH Kaltim &copy; 2026</div>
        </div>
    </div>

    <!-- Script Logika Autentikasi -->
    <script>
        const STORAGE_KEY_ADMIN = 'sisfor_admin_logged_in';

        // Toggle visibilitas password
        function togglePasswordVisibility() {
            const passInput = document.getElementById('loginPassword');
            const icon = document.getElementById('toggleIcon');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Tampilkan pesan error
        function showError(message) {
            const alertBox = document.getElementById('clientAlert');
            const alertText = document.getElementById('clientAlertText');
            const card = document.getElementById('loginCard');

            alertText.textContent = message;
            alertBox.classList.remove('d-none');
            alertBox.classList.remove('alert-success');
            alertBox.classList.add('alert-danger');

            // Trigger shake effect
            card.classList.remove('shake');
            void card.offsetWidth; // reflow
            card.classList.add('shake');
        }

        // Tampilkan pesan sukses
        function showSuccess(message) {
            const alertBox = document.getElementById('clientAlert');
            const alertText = document.getElementById('clientAlertText');

            alertText.textContent = message;
            alertBox.classList.remove('d-none');
            alertBox.classList.remove('alert-danger');
            alertBox.classList.add('alert-success');
        }

        // Handle form login submit
        async function handleFormLogin(event) {
            event.preventDefault();
            const u = document.getElementById('loginUsername').value.trim();
            const p = document.getElementById('loginPassword').value.trim();
            const alertBox = document.getElementById('clientAlert');
            const btnSubmit = document.getElementById('btnSubmit');
            const btnText = document.getElementById('btnSubmitText');

            if (!u || !p) {
                showError("Username dan password wajib diisi!");
                return;
            }

            alertBox.classList.add('d-none');
            btnSubmit.disabled = true;
            btnText.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Memverifikasi...';

            const isStaticPlatform = window.location.hostname.includes('github.io') || 
                                     window.location.hostname.includes('vercel.app') || 
                                     window.location.protocol === 'file:';

            let loginSuccess = false;
            let targetRedirect = "admin_dashboard.php";
            let responseMessage = "Berhasil masuk! Mengalihkan ke Dashboard...";

            // 1. Verifikasi via backend PHP (api/login.php)
            if (!isStaticPlatform) {
                try {
                    const res = await fetch('api/login.php', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ username: u, password: p })
                    });

                    const contentType = res.headers.get("content-type") || "";
                    if (contentType.includes("application/json")) {
                        const data = await res.json();
                        if (res.ok && data.status === 'success') {
                            loginSuccess = true;
                            targetRedirect = data.redirect || "admin_dashboard.php";
                            responseMessage = data.message || responseMessage;
                        } else {
                            showError(data.message || "Username atau password salah!");
                            btnSubmit.disabled = false;
                            btnText.textContent = "Masuk ke Panel Admin";
                            return;
                        }
                    } else {
                        // Jika server tidak mengembalikan JSON
                        showError("Terjadi respon tidak valid dari server (Status: " + res.status + ").");
                        btnSubmit.disabled = false;
                        btnText.textContent = "Masuk ke Panel Admin";
                        return;
                    }
                } catch (err) {
                    console.warn("Backend API tidak merespons, beralih ke validasi demo:", err);
                }
            }

            // 2. Fallback demo statis (GitHub Pages / preview file lokal)
            if (!loginSuccess && isStaticPlatform) {
                if (u === "admin" && p === "perencanaan2026") {
                    loginSuccess = true;
                    targetRedirect = "admin_dashboard.php";
                    responseMessage = "Berhasil masuk (Mode Demo)! Mengalihkan...";
                } else {
                    showError("Username atau password salah!");
                    btnSubmit.disabled = false;
                    btnText.textContent = "Masuk ke Panel Admin";
                    return;
                }
            }

            // 3. Eksekusi pengalihan setelah login sukses
            if (loginSuccess) {
                localStorage.setItem(STORAGE_KEY_ADMIN, 'true');
                showSuccess(responseMessage);
                btnText.innerHTML = '<i class="fa-solid fa-check me-2"></i> Berhasil! Mengalihkan...';
                setTimeout(() => {
                    window.location.href = targetRedirect;
                }, 600);
            } else {
                showError("Gagal memverifikasi login. Silakan hubungi administrator.");
                btnSubmit.disabled = false;
                btnText.textContent = "Masuk ke Panel Admin";
            }
        }
    </script>
</body>
</html>
