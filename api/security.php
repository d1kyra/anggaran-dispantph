<?php
// api/security.php
// Shared security helpers for public deployment hardening.

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === '443'),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

function emitSecurityHeaders(): void
{
    if (headers_sent()) {
        return;
    }

    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com; font-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self'; frame-ancestors 'self';");

    if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === '443')) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

function ensureSecureSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === '443'),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        session_start();
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    if (empty($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = time();
    }
}

function isSessionExpired(int $idleSeconds = 1800): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        return false;
    }

    $lastActivity = $_SESSION['last_activity'] ?? time();
    return (time() - (int) $lastActivity) > $idleSeconds;
}

function refreshSessionActivity(): void
{
    if (session_status() !== PHP_SESSION_NONE) {
        $_SESSION['last_activity'] = time();
    }
}

function logAuditEvent(string $action, array $context = []): void
{
    $logDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0770, true);
    }

    $entry = [
        'timestamp' => gmdate('c'),
        'action' => $action,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
        'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
        'user' => $_SESSION['admin_user'] ?? 'guest',
        'context' => $context,
    ];

    @file_put_contents(
        $logDir . DIRECTORY_SEPARATOR . 'admin_audit.log',
        json_encode($entry, JSON_UNESCAPED_SLASHES) . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );
}

/**
 * Mem-parse user agent sederhana untuk informasi browser & platform
 */
function parseAuditUserAgent(string $userAgent): array
{
    $browser = 'Browser';
    $platform = 'Lainnya';

    if (stripos($userAgent, 'Edg/') !== false) {
        $browser = 'Microsoft Edge';
    } elseif (stripos($userAgent, 'Chrome/') !== false || stripos($userAgent, 'CriOS/') !== false) {
        $browser = 'Google Chrome';
    } elseif (stripos($userAgent, 'Firefox/') !== false) {
        $browser = 'Mozilla Firefox';
    } elseif (stripos($userAgent, 'Safari/') !== false) {
        $browser = 'Apple Safari';
    } elseif (stripos($userAgent, 'curl/') !== false) {
        $browser = 'cURL CLI';
    }

    if (stripos($userAgent, 'Windows NT 10') !== false) {
        $platform = 'Windows 10/11';
    } elseif (stripos($userAgent, 'Windows') !== false) {
        $platform = 'Windows';
    } elseif (stripos($userAgent, 'Macintosh') !== false || stripos($userAgent, 'Mac OS') !== false) {
        $platform = 'macOS';
    } elseif (stripos($userAgent, 'Linux') !== false) {
        $platform = 'Linux';
    } elseif (stripos($userAgent, 'Android') !== false) {
        $platform = 'Android';
    } elseif (stripos($userAgent, 'iPhone') !== false || stripos($userAgent, 'iPad') !== false) {
        $platform = 'iOS';
    }

    $device = ($platform !== 'Lainnya' && $browser !== 'Browser') ? "$browser ($platform)" : ($browser !== 'Browser' ? $browser : $platform);

    return [
        'browser' => $browser,
        'platform' => $platform,
        'device' => $device,
    ];
}

/**
 * Format timestamp audit log ke format waktu lokal (WITA / UTC+8)
 */
function formatAuditTimestamp(?string $isoString, string $format = 'd M Y, H:i'): string
{
    if (empty($isoString)) {
        return '—';
    }

    try {
        $dt = new DateTime($isoString);
        $dt->setTimezone(new DateTimeZone('Asia/Makassar'));
        return $dt->format($format);
    } catch (Exception $e) {
        return (string) $isoString;
    }
}

/**
 * Memeriksa apakah suatu alamat IP merupakan alamat lokal / loopback (127.0.0.1, ::1, localhost)
 */
function isLocalhostIp(?string $ip): bool
{
    $clean = trim((string) $ip);
    return in_array($clean, ['127.0.0.1', '::1', 'localhost'], true)
        || str_starts_with($clean, '127.')
        || $clean === '';
}

/**
 * Mengambil rekaman login administrator terakhir dari audit log lokal.
 * Tidak menggunakan data yang dikirim browser; seluruh metadata berasal dari server.
 * Secara default mengabaikan IP lokal/loopback (127.0.0.1, ::1).
 */
function getLatestAdminLogin(bool $excludeLocalhost = true): ?array
{
    $logFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'admin_audit.log';
    if (!is_readable($logFile)) {
        return null;
    }

    $lines = @file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!is_array($lines)) {
        return null;
    }

    foreach (array_reverse($lines) as $line) {
        $entry = json_decode($line, true);
        if (is_array($entry) && ($entry['action'] ?? '') === 'admin_login_success') {
            $ip = (string) ($entry['context']['ip'] ?? $entry['ip'] ?? '');
            if ($excludeLocalhost && isLocalhostIp($ip)) {
                continue;
            }
            return $entry;
        }
    }

    return null;
}

/**
 * Mengambil daftar riwayat login administrator terbaru dari audit log lokal dalam bentuk array terstruktur.
 * Secara default menyaring rekaman dari IP localhost (127.0.0.1, ::1).
 */
function getRecentAdminLogins(int $limit = 20, bool $excludeLocalhost = true): array
{
    $logFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'admin_audit.log';
    if (!is_readable($logFile)) {
        return [];
    }

    $lines = @file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!is_array($lines)) {
        return [];
    }

    $results = [];
    foreach (array_reverse($lines) as $line) {
        $entry = json_decode($line, true);
        if (is_array($entry) && ($entry['action'] ?? '') === 'admin_login_success') {
            $ip = (string) ($entry['context']['ip'] ?? $entry['ip'] ?? 'unknown');
            if ($excludeLocalhost && isLocalhostIp($ip)) {
                continue;
            }

            $timestamp = (string) ($entry['timestamp'] ?? '');
            $username = (string) ($entry['context']['username'] ?? $entry['user'] ?? 'admin');
            $rawUa = (string) ($entry['user_agent'] ?? '');
            $uaInfo = parseAuditUserAgent($rawUa);

            $results[] = [
                'timestamp' => $timestamp,
                'formatted_time' => formatAuditTimestamp($timestamp),
                'short_time' => formatAuditTimestamp($timestamp, 'd M, H:i'),
                'username' => $username,
                'ip' => $ip,
                'user_agent' => $rawUa,
                'browser' => $uaInfo['browser'],
                'platform' => $uaInfo['platform'],
                'device' => $uaInfo['device'],
                'status' => 'Berhasil'
            ];

            if (count($results) >= $limit) {
                break;
            }
        }
    }

    return $results;
}

function getCsrfToken(): string
{
    ensureSecureSession();
    return (string) ($_SESSION['csrf_token'] ?? '');
}

function validateCsrfToken(): bool
{
    ensureSecureSession();

    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['csrf_token'] ?? '';
    if (!is_string($token) || $token === '') {
        return false;
    }

    return hash_equals((string) ($_SESSION['csrf_token'] ?? ''), $token);
}

function rateLimitExceeded(string $key, int $limit = 5, int $windowSeconds = 600): bool
{
    $remoteIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    // Beri toleransi lebih longgar untuk koneksi lokal/developer
    if (in_array($remoteIp, ['127.0.0.1', '::1', 'localhost'], true) && $limit < 20) {
        $limit = 25;
    }

    $now = time();
    $rateLimitFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sisfor_rate_limits.json';
    $handle = fopen($rateLimitFile, 'c+');
    if ($handle === false) {
        return false;
    }

    flock($handle, LOCK_EX);
    $contents = stream_get_contents($handle);
    $limits = is_string($contents) && $contents !== '' ? json_decode($contents, true) : [];
    $limits = is_array($limits) ? $limits : [];
    $bucketKey = hash('sha256', $key . '|' . $remoteIp);
    $bucket = $limits[$bucketKey] ?? ['count' => 0, 'reset_at' => $now + $windowSeconds];

    if ($now > (int) $bucket['reset_at']) {
        $bucket = ['count' => 0, 'reset_at' => $now + $windowSeconds];
    }

    $bucket['count']++;
    $limits[$bucketKey] = $bucket;
    ftruncate($handle, 0);
    rewind($handle);
    fwrite($handle, json_encode($limits));
    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);

    return $bucket['count'] > $limit;
}

function clearRateLimit(string $key): void
{
    $remoteIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $rateLimitFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sisfor_rate_limits.json';
    if (!file_exists($rateLimitFile)) {
        return;
    }
    $handle = fopen($rateLimitFile, 'c+');
    if ($handle === false) {
        return;
    }
    flock($handle, LOCK_EX);
    $contents = stream_get_contents($handle);
    $limits = is_string($contents) && $contents !== '' ? json_decode($contents, true) : [];
    $limits = is_array($limits) ? $limits : [];
    $bucketKey = hash('sha256', $key . '|' . $remoteIp);
    unset($limits[$bucketKey]);
    ftruncate($handle, 0);
    rewind($handle);
    fwrite($handle, json_encode($limits));
    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);
}

function normalizePasswordCheck(string $expectedPassword, string $providedPassword): bool
{
    $info = password_get_info($expectedPassword);
    if ($info && !empty($info['algo'])) {
        return password_verify($providedPassword, $expectedPassword);
    }

    return hash_equals($expectedPassword, $providedPassword);
}
