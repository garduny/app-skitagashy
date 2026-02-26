<?php
$envPath = dirname(__DIR__) . '/.env';
static $env = [];

if (empty($env)) {
    if (!is_file($envPath)) {
        throw new RuntimeException(".env file not found: $envPath");
    }
    foreach (file($envPath, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES) as $line) {
        $line = ltrim($line);
        if ($line === '' || $line[0] === '#') continue;
        $eq = strpos($line, '=');
        if ($eq === false) continue;
        $env[substr($line, 0, $eq)] = substr($line, $eq + 1);
    }
}

$r = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

// FIX: Detect if running on live server path OR if REMOTE_ADDR is not local
$is_live_path = strpos(__DIR__, '/www/wwwroot/') !== false;
$is_not_local_ip = !preg_match('/^(127\.|192\.168\.|10\.|::1|localhost)/', $r);

$l = $is_live_path || (PHP_SAPI !== 'cli' && PHP_SAPI !== 'cli-server' && $is_not_local_ip);

return [
    'app' => [
        'timezone' => $env['APP_TIMEZONE'] ?? 'Asia/Baghdad',
    ],
    'db' => [
        'server'   => $env[$l ? 'DB_SERVER_LIVE' : 'DB_SERVER_LOCAL'] ?? 'root',
        'charset'  => $env['DB_CHARSET'] ?? 'utf8',
        'collate'  => $env['DB_COLLATE'] ?? 'utf8_general_ci',
        'timezone' => $env['DB_TIMEZONE'] ?? '+03:00',
        'user'     => $env[$l ? 'DB_USER_LIVE' : 'DB_USER_LOCAL'] ?? 'root',
        'pass'     => $env[$l ? 'DB_PASS_LIVE' : 'DB_PASS_LOCAL'] ?? '',
        'name'     => $env[$l ? 'DB_NAME_LIVE' : 'DB_NAME_LOCAL'] ?? '',
    ],
    'mailer' => [
        'smtp_auth'   => ($env['MAILER_SMTPAUTH'] ?? 'true') === 'true',
        'smtp_secure' => $env['MAILER_SMTPSECURE'] ?? 'tls',
        'host'        => $env['MAILER_HOST'] ?? '',
        'port'        => (int)($env['MAILER_PORT'] ?? 587),
        'username'    => $env['MAILER_USERNAME'] ?? '',
        'password'    => $env['MAILER_PASSWORD'] ?? '',
        'from'        => $env['MAILER_FROM'] ?? '',
        'sender'      => $env['MAILER_SENDER'] ?? '',
        'reply'       => $env['MAILER_REPLY'] ?? '',
    ],
    'api' => [
        'access_allow_origin'      => $env['ACCESS_ALLOW_ORIGIN'] ?? '*',
        'access_allow_credentials' => ($env['ACCESS_ALLOW_CREDENTIALS'] ?? 'true') === 'true',
        'content_type'             => $env['CONTENT_TYPE'] ?? 'application/json; charset=UTF-8',
        'access_allow_methods'     => $env['ACCESS_ALLOW_METHODS'] ?? 'GET, POST, PATCH, DELETE, PUT, OPTIONS',
        'access_allow_headers'     => $env['ACCESS_ALLOW_HEADERS'] ?? 'Origin, Content-Type, X-Auth-Token',
    ],
    'firebase' => [
        'server_key'  => $env['FIREBASE_SERVER_KEY'] ?? '',
        'request_url' => $env['FIREBASE_REQUEST_URL'] ?? '',
    ],
    'file' => [
        'extensions' => $env['FILE_EXTENSIONS'] !== '' ? explode(',', $env['FILE_EXTENSIONS']) : [],
        'max_size'   => (int)($env['FILE_SIZE'] ?? 50000000),
    ],
];
