<?php

require_once __DIR__ . '/../../api/bootstrap.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const LOGIN_MAX_ATTEMPTS = 5;
const LOGIN_LOCK_SECONDS = 900; // 15 min

$attemptsPath = __DIR__ . '/../../api/login_attempts.json';

function load_login_attempts(string $path): array
{
    $data = is_file($path) ? json_decode((string) file_get_contents($path), true) : null;
    return is_array($data) ? $data : ['count' => 0, 'window_start' => 0, 'locked_until' => 0];
}

function save_login_attempts(string $path, array $state): void
{
    file_put_contents($path, json_encode($state));
}

$state = load_login_attempts($attemptsPath);
$now = time();

if ($state['locked_until'] > $now) {
    echo json_encode(['ok' => 0, 'locked' => 1]);
    exit;
}

$input = read_json_body();
$login = isset($input['login']) ? trim((string) $input['login']) : '';
$pass = isset($input['pass']) ? (string) $input['pass'] : '';

$ok = 0;
if ($login !== '' && $pass !== '' && ADMIN_PASSWORD_HASH !== ''
    && $login === ADMIN_LOGIN && password_verify($pass, ADMIN_PASSWORD_HASH)
) {
    session_regenerate_id(true);
    $_SESSION['cadipel_admin'] = $login;
    $ok = 1;
    save_login_attempts($attemptsPath, ['count' => 0, 'window_start' => 0, 'locked_until' => 0]);
} else {
    if ($now - $state['window_start'] > LOGIN_LOCK_SECONDS) {
        $state = ['count' => 0, 'window_start' => $now, 'locked_until' => 0];
    }
    $state['count']++;
    if ($state['count'] >= LOGIN_MAX_ATTEMPTS) {
        $state['locked_until'] = $now + LOGIN_LOCK_SECONDS;
    }
    save_login_attempts($attemptsPath, $state);
}

echo json_encode(['ok' => $ok]);
