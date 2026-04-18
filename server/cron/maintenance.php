<?php
require_once __DIR__.'/init.php';
echo "[".date('Y-m-d H:i:s')."] Maintenance Started\n";
if (php_sapi_name() !== 'cli') {
    if (!defined('gashy_exec')) define('gashy_exec', true);
}
if (!isset($_SERVER['REQUEST_URI'])) $_SERVER['REQUEST_URI'] = '/cron/maintenance';
if (!isset($_SERVER['HTTP_USER_AGENT'])) $_SERVER['HTTP_USER_AGENT'] = 'SystemCron';
if (!isset($_SERVER['REMOTE_ADDR'])) $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
require_once __DIR__ . '/../init.php';
function syncMaintenanceProductStock($productId)
{
    $productId = (int)$productId;
    $prod = findQuery(" SELECT id,type FROM products WHERE id=$productId ");
    if (!$prod) return;
    if ($prod['type'] === 'gift_card' || $prod['type'] === 'digital') {
        $available = (int)(findQuery(" SELECT COUNT(*) c FROM gift_cards WHERE product_id=$productId AND is_sold=0 ")['c'] ?? 0);
        execute(" UPDATE products SET stock=$available WHERE id=$productId ");
        $options = getQuery(" SELECT id FROM gift_card_options WHERE product_id=$productId AND is_active=1 ");
        foreach ($options as $opt) {
            $oid = (int)$opt['id'];
            $optStock = (int)(findQuery(" SELECT COUNT(*) c FROM gift_cards WHERE product_id=$productId AND gift_card_option_id=$oid AND is_sold=0 ")['c'] ?? 0);
            execute(" UPDATE gift_card_options SET stock=$optStock WHERE id=$oid AND product_id=$productId ");
        }
    }
}
echo "[" . date('Y-m-d H:i:s') . "] System Maintenance Started...\n";
$expired = getQuery(" SELECT id,product_id FROM gift_cards WHERE is_sold=0 AND expiry_date IS NOT NULL AND expiry_date<CURDATE() ORDER BY id ASC LIMIT 5000 ");
if (!empty($expired)) {
    echo "Found " . count($expired) . " expired gift cards.\n";
    execute(" START TRANSACTION ");
    try {
        $productIds = [];
        foreach ($expired as $card) {
            $cid = (int)$card['id'];
            $pid = (int)$card['product_id'];
            execute(" DELETE FROM gift_cards WHERE id=$cid AND is_sold=0 ");
            $productIds[$pid] = true;
            echo " -> Removed Card #$cid (Expired)\n";
        }
        foreach (array_keys($productIds) as $pid) {
            syncMaintenanceProductStock((int)$pid);
        }
        execute(" COMMIT ");
    } catch (Throwable $e) {
        execute(" ROLLBACK ");
        echo "Gift card cleanup failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "No expired gift cards found.\n";
}
execute(" DELETE FROM account_sessions WHERE expires_at<NOW() ");
execute(" DELETE FROM user_sessions WHERE expires_at<NOW() ");
echo "Expired sessions cleaned.\n";
execute(" DELETE FROM activity_log WHERE created_at<DATE_SUB(NOW(),INTERVAL 90 DAY) ");
echo "Rotated old activity logs.\n";
echo "Maintenance Complete.\n";
