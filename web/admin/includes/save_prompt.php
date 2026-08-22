<?php

require_once __DIR__ . '/../../api/bootstrap.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['cadipel_admin'])) {
    json_error(401, 'unauthorized');
}

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (stripos($contentType, 'application/json') !== 0) {
    // Un formulario cross-site no puede fijar este header (no está en la lista de
    // content-types "simples"), así que exigirlo bloquea el vector clásico de CSRF
    // vía <form enctype="text/plain"> con un body que casualmente es JSON válido.
    json_error(415, 'unsupported content type');
}

$input = read_json_body();
$instructions = isset($input['instructions']) ? (string) $input['instructions'] : '';
$instructions = trim($instructions);
$instructions = mb_substr($instructions, 0, 20000);

$path = __DIR__ . '/../../api/custom_prompt.txt';
$ok = file_put_contents($path, $instructions) !== false;

echo json_encode(['ok' => $ok ? 1 : 0]);
