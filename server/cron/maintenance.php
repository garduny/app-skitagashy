<?php
if (php_sapi_name() !== 'cli') {
    if (!defined('gashy_exec')) define('gashy_exec', true);
}
if (!isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = '/cron/maintenance';
}
if (!isset($_SERVER['HTTP_USER_AGENT'])) {
    $_SERVER['HTTP_USER_AGENT'] = 'SystemCron';
}
if (!isset($_SERVER['REMOTE_ADDR'])) {
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
}
require_once __DIR__ . '/../init.php';
echo "[" . date('Y-m-d H:i:s') . "] System Maintenance Started...\n";
$expired = getQuery(" SELECT id FROM gift_cards WHERE is_sold=0 AND expiry_date IS NOT NULL AND expiry_date < CURDATE() ");
if (!empty($expired)) {
    echo "Found " . count($expired) . " expired gift cards.\n";
    foreach ($expired as $card) {
        $cid = $card['id'];
        $pid = findQuery(" SELECT product_id FROM gift_cards WHERE id=$cid")['product_id'];
        execute(" UPDATE products SET stock = GREATEST(stock - 1, 0) WHERE id=$pid ");
        execute(" DELETE FROM gift_cards WHERE id=$cid ");
        echo " -> Removed Card #$cid (Expired)\n";
    }
} else {
    echo "No expired gift cards found.\n";
}
$deleted_sessions = execute(" DELETE FROM account_sessions WHERE expires_at < NOW() ");
$deleted_admin = execute(" DELETE FROM user_sessions WHERE expires_at < NOW() ");
echo "Cleaned up sessions (Client: $deleted_sessions, Admin: $deleted_admin)\n";
execute(" DELETE FROM activity_log WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY) ");
echo "Rotated old activity logs.\n";
echo "Maintenance Complete.\n";
