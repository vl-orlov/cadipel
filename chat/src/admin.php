<?php

declare(strict_types=1);

/**
 * Panel /admin/ del chat: un usuario, sin base de datos.
 * El prompt editable vive en var/custom_prompt.txt (fuera del docroot).
 */

$configFile = __DIR__ . '/../config.php';
require_once is_file($configFile) ? $configFile : __DIR__ . '/../config.example.php';

foreach ([
    'ADMIN_LOGIN' => 'admin',
    'ADMIN_PASSWORD_HASH' => '',
    'TRUST_PROXY' => false,
    'APP_ENV' => 'prod',
] as $name => $default) {
    if (!defined($name)) {
        define($name, $default);
    }
}

if (!defined('CADIPEL_VAR_DIR')) {
    define('CADIPEL_VAR_DIR', __DIR__ . '/../var');
}

const ADMIN_PROMPT_MAX = 20000;
const ADMIN_LOGIN_MAX_ATTEMPTS = 5;
const ADMIN_LOGIN_LOCK_SECONDS = 900;

function admin_prompt_path(): string
{
    return CADIPEL_VAR_DIR . '/custom_prompt.txt';
}

function admin_attempts_path(): string
{
    return CADIPEL_VAR_DIR . '/login_attempts.json';
}

function admin_page_headers(): void
{
    header('X-Robots-Tag: noindex, nofollow');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: same-origin');
    header('Cache-Control: no-store');
}

function admin_session_start(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (TRUST_PROXY && (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'));
    session_name('cadipel_admin');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/admin',
        'secure'   => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function admin_logged_in(): bool
{
    return isset($_SESSION['cadipel_admin']) && $_SESSION['cadipel_admin'] === ADMIN_LOGIN;
}

/** Misma regla de IP que client_ip() en bootstrap.php. */
function admin_client_ip(): string
{
    if (TRUST_PROXY) {
        $cf = trim((string) ($_SERVER['HTTP_CF_CONNECTING_IP'] ?? ''));
        if (filter_var($cf, FILTER_VALIDATE_IP)) {
            return $cf;
        }
        $forwarded = trim(explode(',', (string) ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? ''))[0]);
        if (filter_var($forwarded, FILTER_VALIDATE_IP)) {
            return $forwarded;
        }
    }
    $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
    return $ip !== '' ? $ip : '0.0.0.0';
}

function admin_read_prompt(): string
{
    $path = admin_prompt_path();
    if (!is_file($path)) {
        return '';
    }
    $text = file_get_contents($path);
    return is_string($text) ? trim($text) : '';
}

function admin_write_prompt(string $instructions): bool
{
    $instructions = trim($instructions);
    if (function_exists('mb_substr')) {
        $instructions = mb_substr($instructions, 0, ADMIN_PROMPT_MAX);
    } else {
        $instructions = substr($instructions, 0, ADMIN_PROMPT_MAX);
    }

    $dir = CADIPEL_VAR_DIR;
    if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
        error_log('cadipel admin: var/ no existe y no se puede crear');
        return false;
    }

    $path = admin_prompt_path();
    $tmp  = $path . '.' . getmypid() . '.tmp';
    if (file_put_contents($tmp, $instructions) === false) {
        error_log('cadipel admin: no se puede escribir el prompt');
        return false;
    }
    if (!rename($tmp, $path)) {
        @unlink($tmp);
        error_log('cadipel admin: no se puede reemplazar el prompt');
        return false;
    }
    return true;
}

/** @return array{count: int, window_start: int, locked_until: int} */
function admin_blank_attempt(): array
{
    return ['count' => 0, 'window_start' => 0, 'locked_until' => 0];
}

/**
 * @return array{ok: int, locked?: int}
 */
function admin_try_login(string $login, string $pass): array
{
    $ip  = admin_client_ip();
    $now = time();
    $fh  = @fopen(admin_attempts_path(), 'c+');
    $ips = [];
    $haveFile = $fh !== false;
    if ($haveFile) {
        flock($fh, LOCK_EX);
        $data = json_decode((string) stream_get_contents($fh), true);
        if (is_array($data) && isset($data['ips']) && is_array($data['ips'])) {
            $ips = $data['ips'];
        }
        foreach ($ips as $key => $rowOld) {
            if (!is_array($rowOld)) {
                unset($ips[$key]);
                continue;
            }
            $idle = $now - (int) ($rowOld['window_start'] ?? 0);
            if ($idle > ADMIN_LOGIN_LOCK_SECONDS && (int) ($rowOld['locked_until'] ?? 0) <= $now) {
                unset($ips[$key]);
            }
        }
    }

    $row = (isset($ips[$ip]) && is_array($ips[$ip])) ? $ips[$ip] : admin_blank_attempt();
    if ($haveFile && (int) ($row['locked_until'] ?? 0) > $now) {
        flock($fh, LOCK_UN);
        fclose($fh);
        return ['ok' => 0, 'locked' => 1];
    }

    $ok = 0;
    if ($login !== '' && $pass !== '' && ADMIN_PASSWORD_HASH !== ''
        && $login === ADMIN_LOGIN && password_verify($pass, ADMIN_PASSWORD_HASH)
    ) {
        $ok = 1;
        $ips[$ip] = admin_blank_attempt();
    } elseif ($haveFile) {
        if ($now - (int) ($row['window_start'] ?? 0) > ADMIN_LOGIN_LOCK_SECONDS) {
            $row = ['count' => 0, 'window_start' => $now, 'locked_until' => 0];
        }
        if ((int) ($row['window_start'] ?? 0) === 0) {
            $row['window_start'] = $now;
        }
        $row['count'] = (int) ($row['count'] ?? 0) + 1;
        if ($row['count'] >= ADMIN_LOGIN_MAX_ATTEMPTS) {
            $row['locked_until'] = $now + ADMIN_LOGIN_LOCK_SECONDS;
        }
        $ips[$ip] = $row;
    }

    if ($haveFile) {
        ftruncate($fh, 0);
        rewind($fh);
        fwrite($fh, json_encode(['ips' => $ips]));
        fflush($fh);
        flock($fh, LOCK_UN);
        fclose($fh);
    } else {
        error_log('cadipel admin: no se puede escribir var/login_attempts.json');
    }

    return ['ok' => $ok];
}

/** @param array<string, mixed> $payload */
function admin_json(int $status, array $payload): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('X-Robots-Tag: noindex, nofollow');
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: no-store');
    echo json_encode($payload);
    exit;
}

function admin_require_json_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        header('Allow: POST');
        admin_json(405, ['ok' => 0, 'error' => 'method']);
    }
    $type = (string) ($_SERVER['CONTENT_TYPE'] ?? '');
    if (stripos($type, 'application/json') !== 0) {
        admin_json(415, ['ok' => 0, 'error' => 'content-type']);
    }
}

/** @return array<string, mixed> */
function admin_read_json_body(int $maxBytes = 200000): array
{
    $raw = file_get_contents('php://input', false, null, 0, $maxBytes + 1);
    if ($raw === false || $raw === '') {
        return [];
    }
    if (strlen($raw) > $maxBytes) {
        admin_json(413, ['ok' => 0, 'error' => 'too-large']);
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}
