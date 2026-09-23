<?php

require_once __DIR__ . '/../../../src/admin.php';
admin_session_start();
admin_require_json_post();

if (!admin_logged_in()) {
    admin_json(401, ['ok' => 0]);
}

$input = admin_read_json_body();
$instructions = isset($input['instructions']) ? (string) $input['instructions'] : '';
$ok = admin_write_prompt($instructions);

admin_json(200, ['ok' => $ok ? 1 : 0]);
