<?php
if (php_sapi_name() !== 'cli') {
    if (!defined('gashy_exec')) define('gashy_exec', true);
}
if (!isset($_SERVER['REQUEST_URI'])) $_SERVER['REQUEST_URI'] = '/cron/auctions';
if (!isset($_SERVER['HTTP_USER_AGENT'])) $_SERVER['HTTP_USER_AGENT'] = 'CronJob';
require_once __DIR__ . '/../init.php';
$expired = getQuery(" SELECT * FROM auctions WHERE status='active' AND end_time<=NOW() ");
foreach ($expired as $a) {
    echo "Processing Auction ID: {$a['id']}...\n";
    execute(" START TRANSACTION ");
    try {
        $aid = $a['id'];
        $pid = $a['product_id'];
        $winner_id = $a['highest_bidder_id'];
        $amount = $a['current_bid'];
        $reserve = $a['reserve_price'] ?? 0;
        if ($winner_id && $amount >= $reserve) {
            execute(" UPDATE auctions SET status='ended' WHERE id=$aid ");
            execute(" UPDATE products SET seller_id=$winner_id, status='inactive' WHERE id=$pid ");
            $txSig = 'AUC_WIN_' . $aid . '_' . time();
            execute(" INSERT INTO orders (account_id,total_gashy,tx_signature,status,created_at) VALUES ($winner_id,$amount,'$txSig','completed',NOW()) ");
            $oid = findQuery(" SELECT LAST_INSERT_ID() as id ")['id'];
            execute(" INSERT INTO order_items (order_id,product_id,quantity,price_at_purchase) VALUES ($oid,$pid,1,$amount) ");
            $acc = findQuery(" SELECT email,accountname FROM accounts WHERE id=$winner_id ");
            if ($acc['email'] && function_exists('mailer')) {
                $body = "<h1>You Won the Auction!</h1><p>Hi {$acc['accountname']},</p><p>You won <b>{$a['id']}</b> for <b>" . number_format($amount) . " GASHY</b>.</p><p>The item has been transferred to your inventory.</p>";
                mailer("Auction Won: #$aid", $body, "Gashy Auctions", $acc['email']);
            }
            echo " -> Sold to Account ID: $winner_id\n";
        } else {
            execute(" UPDATE auctions SET status='ended' WHERE id=$aid ");
            if ($winner_id && $amount < $reserve) {
                echo " -> Reserve not met ($amount < $reserve). Closed.\n";
            } else {
                echo " -> No Bids. Closed.\n";
            }
        }
        execute(" COMMIT ");
    } catch (Exception $e) {
        execute(" ROLLBACK ");
        echo " -> Error: " . $e->getMessage() . "\n";
    }
}
echo "Done.\n";
