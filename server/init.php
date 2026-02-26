<?php

declare(strict_types=1);
session_start();
session_regenerate_id(true);
ob_start('ob_gzhandler') || ob_start();
$config = require __DIR__ . '/config.php';
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
date_default_timezone_set($config['app']['timezone']);
spl_autoload_register(static fn(string $c) => is_file($f = __DIR__ . "/classes/$c.php") && require $f);
Database::setConfig($config['db']);
require __DIR__ . '/logic.php';
require_once __DIR__ . '/classes/RateLimiter.php';
$uri = $_SERVER['REQUEST_URI'];
if (strpos($uri, '/api/') !== false) {
    if (!RateLimiter::check('global_api', 60, 60)) {
        http_response_code(429);
        if (function_exists('encode')) encode(['status' => false, 'message' => 'Too Many Requests. Slow down.']);
        else exit('Too Many Requests');
    }
}
if (strpos($uri, 'login.php') !== false) {
    if (!RateLimiter::check('auth_attempt', 5, 60)) {
        http_response_code(429);
        if (strpos($uri, '/api/') !== false) encode(['status' => false, 'message' => 'Too many login attempts. Wait 1 minute.']);
        else exit('Too many login attempts. Please wait 1 minute.');
    }
}
if (strpos($uri, 'create.php') !== false || strpos($uri, 'enter.php') !== false) {
    if (!RateLimiter::check('transaction', 10, 60)) {
        if (function_exists('encode')) encode(['status' => false, 'message' => 'Transaction limit reached. Wait a moment.']);
        else exit('Transaction limit reached.');
    }
}
if (($_REQUEST['apimode'] ?? '') === '1' || apiMode()) require __DIR__ . '/restapi.php';
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
ini_set('display_errors', '1');
error_reporting(1);
$m_mode = findQuery(" SELECT value FROM settings WHERE key_name='maintenance_mode' ");
$is_maintenance = ($m_mode && $m_mode['value'] == '1');
$current_uri = $_SERVER['REQUEST_URI'];
$is_dashboard = strpos($current_uri, '/dashboard/') !== false;
$is_maintenance_page = strpos($current_uri, 'maintenance') !== false;
$is_admin_api = strpos($current_uri, './api/admin/') !== false;
if ($is_maintenance) {
    if (!$is_dashboard && !$is_admin_api && !$is_maintenance_page) {
        header("Location: ./maintenance");
        exit;
    }
} else {
    if ($is_maintenance_page) {
        header("Location: /");
        exit;
    }
}
