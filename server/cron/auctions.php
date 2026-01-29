<?php
require_once 'init.php';
$expired = getQuery(" SELECT * FROM auctions WHERE status='active' AND end_time<=NOW() ");
foreach ($expired as $a) {
    echo "Processing Auction ID: {$a['id']}...\n";
    execute(" START TRANSACTION ");
    try {
        $aid = $a['id'];
        $pid = $a['product_id'];
        $winner_id = $a['highest_bidder_id'];
        $amount = $a['current_bid'];
        if ($winner_id) {
            execute(" UPDATE auctions SET status='ended' WHERE id=$aid ");
            execute(" UPDATE products SET seller_id=$winner_id, status='inactive' WHERE id=$pid ");
            $txSig = 'AUC_WIN_' . $aid . '_' . time();
            execute(" INSERT INTO orders (account_id,total_gashy,tx_signature,status,created_at) VALUES ($winner_id,$amount,'$txSig','completed',NOW()) ");
            $oid = findQuery(" SELECT LAST_INSERT_ID() as id ")['id'];
            execute(" INSERT INTO order_items (order_id,product_id,quantity,price_at_purchase) VALUES ($oid,$pid,1,$amount) ");
            echo " -> Sold to Account ID: $winner_id\n";
        } else {
            execute(" UPDATE auctions SET status='ended' WHERE id=$aid ");
            echo " -> No Bids. Closed.\n";
        }
        execute(" COMMIT ");
    } catch (Exception $e) {
        execute(" ROLLBACK ");
        echo " -> Error: " . $e->getMessage() . "\n";
    }
}
echo "Done.\n";
