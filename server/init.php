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
if (($_REQUEST['apimode'] ?? '') === '1' || apiMode()) require __DIR__ . '/restapi.php';

header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
ini_set('display_errors', 1);
error_reporting(1);
