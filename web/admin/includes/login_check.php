<?php

require_once __DIR__ . '/../../api/bootstrap.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const LOGIN_MAX_ATTEMPTS = 5;
const LOGIN_LOCK_SECONDS = 900; // 15 min

$attemptsPath = __DIR__ . '/../../api/login_attempts.json';

function client_ip_admin(): string
{
    $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
    return $ip !== '' ? $ip : '0.0.0.0';
}

/** @return array{count: int, window_start: int, locked_until: int} */
function blank_attempt(): array
{
    return ['count' => 0, 'window_start' => 0, 'locked_until' => 0];
}

$input = read_json_body();
$login = isset($input['login']) ? trim((string) $input['login']) : '';
$pass  = isset($input['pass']) ? (string) $input['pass'] : '';
$ip    = client_ip_admin();
$now   = time();

$fh = @fopen($attemptsPath, 'c+');
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
        if ($idle > LOGIN_LOCK_SECONDS && (int) ($rowOld['locked_until'] ?? 0) <= $now) {
            unset($ips[$key]);
        }
    }
}

$row = (isset($ips[$ip]) && is_array($ips[$ip])) ? $ips[$ip] : blank_attempt();
if ($haveFile && (int) ($row['locked_until'] ?? 0) > $now) {
    flock($fh, LOCK_UN);
    fclose($fh);
    echo json_encode(['ok' => 0, 'locked' => 1]);
    exit;
}

$ok = 0;
if ($login !== '' && $pass !== '' && ADMIN_PASSWORD_HASH !== ''
    && $login === ADMIN_LOGIN && password_verify($pass, ADMIN_PASSWORD_HASH)
) {
    session_regenerate_id(true);
    $_SESSION['cadipel_admin'] = $login;
    $ok = 1;
    $ips[$ip] = blank_attempt();
} elseif ($haveFile) {
    if ($now - (int) ($row['window_start'] ?? 0) > LOGIN_LOCK_SECONDS) {
        $row = ['count' => 0, 'window_start' => $now, 'locked_until' => 0];
    }
    if ((int) ($row['window_start'] ?? 0) === 0) {
        $row['window_start'] = $now;
    }
    $row['count'] = (int) ($row['count'] ?? 0) + 1;
    if ($row['count'] >= LOGIN_MAX_ATTEMPTS) {
        $row['locked_until'] = $now + LOGIN_LOCK_SECONDS;
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
    error_log('cadipel admin: no se puede escribir api/login_attempts.json');
}

echo json_encode(['ok' => $ok]);
