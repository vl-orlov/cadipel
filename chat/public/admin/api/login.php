<?php

require_once __DIR__ . '/../../../src/admin.php';
admin_session_start();
admin_require_json_post();

$input = admin_read_json_body();
$login = isset($input['login']) ? trim((string) $input['login']) : '';
$pass  = isset($input['pass']) ? (string) $input['pass'] : '';

$result = admin_try_login($login, $pass);
if (($result['ok'] ?? 0) === 1) {
    session_regenerate_id(true);
    $_SESSION['cadipel_admin'] = ADMIN_LOGIN;
}

admin_json(200, $result);
