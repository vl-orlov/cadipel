<?php

/**
 * Entrega las instrucciones adicionales del asistente (api/custom_prompt.txt, editadas desde el
 * admin) al servidor del chat (cadipel.pribridge.pro). Solo responde con el token compartido.
 * Soporta ETag / If-None-Match para que el chat no descargue nada si no cambiaron.
 */
require_once __DIR__ . '/bootstrap.php';

header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-store');

$sent = (string) ($_SERVER['HTTP_X_SYNC_TOKEN'] ?? '');
if (PROMPT_SYNC_TOKEN === '' || !hash_equals(PROMPT_SYNC_TOKEN, $sent)) {
    http_response_code(403);
    exit;
}

$path = __DIR__ . '/custom_prompt.txt';
$text = is_file($path) ? trim((string) file_get_contents($path)) : '';
$etag = '"' . md5($text) . '"';

header('ETag: ' . $etag);
if (($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') === $etag) {
    http_response_code(304);
    exit;
}

echo $text;
