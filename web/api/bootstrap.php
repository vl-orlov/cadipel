<?php

$configFile = __DIR__ . '/config.php';
if (is_file($configFile)) {
    require_once $configFile;
} else {
    require_once __DIR__ . '/config.example.php';
}

if (!defined('GEMINI_KEY'))      define('GEMINI_KEY', '');
if (!defined('OPENAI_KEY'))      define('OPENAI_KEY', '');
if (!defined('AZURE_TTS_KEY'))   define('AZURE_TTS_KEY', '');
if (!defined('AZURE_TTS_REGION')) define('AZURE_TTS_REGION', '');
if (!defined('GOOGLE_TTS_KEY'))  define('GOOGLE_TTS_KEY', '');
if (!defined('ADMIN_LOGIN'))          define('ADMIN_LOGIN', 'admin');
if (!defined('ADMIN_PASSWORD_HASH'))  define('ADMIN_PASSWORD_HASH', '');

header('Content-Type: application/json; charset=utf-8');

function json_error(int $status, string $message): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => $message]);
    exit;
}

/** @return array<string, mixed> */
function read_json_body(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        return [];
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}
