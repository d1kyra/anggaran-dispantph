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

function normalizePasswordCheck(string $expectedPassword, string $providedPassword): bool
{
    $info = password_get_info($expectedPassword);
    if ($info && !empty($info['algo'])) {
        return password_verify($providedPassword, $expectedPassword);
    }

    return hash_equals($expectedPassword, $providedPassword);
}
