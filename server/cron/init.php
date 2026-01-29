<?php
if (php_sapi_name() !== 'cli') {
    exit('CLI Only');
}
if (!isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = '/';
}
if (!isset($_SERVER['HTTP_USER_AGENT'])) {
    $_SERVER['HTTP_USER_AGENT'] = 'CronJob';
}
require_once __DIR__ . '/../init.php';
echo "[" . date('Y-m-d H:i:s') . "] Cron Started\n";
