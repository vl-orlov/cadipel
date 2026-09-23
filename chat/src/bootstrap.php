<?php

declare(strict_types=1);

$configFile = __DIR__ . '/../config.php';
require_once is_file($configFile) ? $configFile : __DIR__ . '/../config.example.php';

foreach ([
    'GEMINI_KEY' => '', 'OPENAI_KEY' => '', 'AZURE_TTS_KEY' => '', 'AZURE_TTS_REGION' => '',
    'GOOGLE_TTS_KEY' => '', 'ALLOWED_HOSTS' => 'cadipel.pribridge.pro', 'APP_ENV' => 'prod',
    'TRUST_PROXY' => false,
] as $name => $default) {
    if (!defined($name)) {
        define($name, $default);
    }
}

const CADIPEL_VAR_DIR = __DIR__ . '/../var';

const MAX_HISTORY_MESSAGES = 20;
const MAX_MESSAGE_CHARS    = 2000;

header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');
header('X-Content-Type-Options: nosniff');

function json_error(int $status, string $message): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => $message]);
    exit;
}

/** @return array<string, mixed> */
function read_json_body(int $maxBytes = 200000): array
{
    $raw = file_get_contents('php://input', false, null, 0, $maxBytes + 1);
    if ($raw === false || $raw === '') {
        return [];
    }
    if (strlen($raw) > $maxBytes) {
        json_error(413, 'Payload too large');
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function require_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        header('Allow: POST');
        json_error(405, 'Method not allowed');
    }
}

/**
 * Filtro barato de uso accidental/ajeno: exige que Origin (o Referer) sea uno de los dominios
 * permitidos. No frena a un script que falsifique el header — para eso está el rate limit.
 */
function require_allowed_origin(): void
{
    $allowed = array_filter(array_map('trim', explode(',', strtolower(ALLOWED_HOSTS))));
    if (APP_ENV === 'dev') {
        array_push($allowed, 'localhost', '127.0.0.1');
    }

    $source = $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? '';
    $host   = strtolower((string) parse_url($source, PHP_URL_HOST));
    if ($host === '' || !in_array($host, $allowed, true)) {
        json_error(403, 'Forbidden origin');
    }
}

function client_ip(): string
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

/**
 * Rate limit por IP con ventana fija por minuto y por día, en archivos (sin BD).
 * Responde 429 + Retry-After y corta la ejecución si se supera alguno.
 */
function rate_limit(string $bucket, int $perMinute, int $perDay): void
{
    $dir = CADIPEL_VAR_DIR . '/ratelimit';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    $file = $dir . '/' . $bucket . '_' . sha1(client_ip()) . '.json';
    $fh   = @fopen($file, 'c+');
    if ($fh === false) {
        error_log('cadipel-chat: var/ratelimit no es escribible — request rechazado');
        json_error(503, 'Service unavailable');
    }

    // Limpieza ocasional: un archivo por IP y bucket crecería sin límite.
    if (random_int(1, 200) === 1) {
        foreach (glob($dir . '/*.json') ?: [] as $old) {
            if (filemtime($old) < time() - 2 * 86400) {
                @unlink($old);
            }
        }
    }
    flock($fh, LOCK_EX);

    $now  = time();
    $data = json_decode((string) stream_get_contents($fh), true);
    $data = is_array($data) ? $data : [];
    $min  = $data['min'] ?? ['start' => $now, 'count' => 0];
    $day  = $data['day'] ?? ['start' => $now, 'count' => 0];
    if ($now - (int) $min['start'] >= 60) {
        $min = ['start' => $now, 'count' => 0];
    }
    if ($now - (int) $day['start'] >= 86400) {
        $day = ['start' => $now, 'count' => 0];
    }

    $retry = 0;
    if ($min['count'] >= $perMinute) {
        $retry = 60 - ($now - (int) $min['start']);
    } elseif ($day['count'] >= $perDay) {
        $retry = 86400 - ($now - (int) $day['start']);
    } else {
        $min['count']++;
        $day['count']++;
    }

    ftruncate($fh, 0);
    rewind($fh);
    fwrite($fh, json_encode(['min' => $min, 'day' => $day]));
    flock($fh, LOCK_UN);
    fclose($fh);

    if ($retry > 0) {
        header('Retry-After: ' . max(1, $retry));
        json_error(429, 'Too many requests');
    }
}

/**
 * Deja solo los últimos MAX_HISTORY_MESSAGES mensajes válidos, con contenido recortado.
 * @param mixed $messages
 * @return list<array{role: string, content: string}>
 */
function sanitize_messages($messages): array
{
    if (!is_array($messages)) {
        return [];
    }
    $clean = [];
    foreach ($messages as $m) {
        if (!is_array($m)) {
            continue;
        }
        $role    = ($m['role'] ?? '') === 'assistant' ? 'assistant' : 'user';
        $content = trim((string) ($m['content'] ?? ''));
        if ($content === '') {
            continue;
        }
        $content = mb_substr($content, 0, MAX_MESSAGE_CHARS);
        $last    = count($clean) - 1;
        // Gemini rechaza dos turnos seguidos del mismo rol. Un error o un refresh a mitad
        // de respuesta dejan dos mensajes de usuario pegados: se funden en uno.
        if ($last >= 0 && $clean[$last]['role'] === $role) {
            $clean[$last]['content'] = mb_substr($clean[$last]['content'] . "\n\n" . $content, 0, MAX_MESSAGE_CHARS);
            continue;
        }
        $clean[] = ['role' => $role, 'content' => $content];
    }
    $clean = array_slice($clean, -MAX_HISTORY_MESSAGES);
    while ($clean && $clean[0]['role'] !== 'user') {
        array_shift($clean);
    }
    return $clean;
}
