<?php
require_once __DIR__.'/init.php';
$expired = getQuery(" SELECT id,product_id,highest_bidder_id,current_bid,reserve_price FROM auctions WHERE status='active' AND end_time<=NOW()");
foreach ($expired as $a) {
    $aid = (int)$a['id'];
    echo "Processing Auction ID: {$aid}...\n";
    execute("START TRANSACTION");
    try {
        $locked = findQuery(" SELECT status FROM auctions WHERE id=$aid FOR UPDATE");
        if (!$locked || $locked['status'] !== 'active') {
            execute("ROLLBACK");
            echo " -> Skipped (already processed).\n";
            continue;
        }
        $pid = (int)$a['product_id'];
        $winner_id = (int)$a['highest_bidder_id'];
        $amount = (float)$a['current_bid'];
        $reserve = (float)($a['reserve_price'] ?? 0);
        if ($winner_id && $amount >= $reserve) {
            execute("UPDATE auctions SET status='ended' WHERE id=$aid");
            $prod = findQuery(" SELECT type FROM products WHERE id=$pid");
            if (!$prod) throw new Exception("Product missing");
            if ($prod['type'] === 'nft') {
                execute("UPDATE products SET seller_id=$winner_id,status='inactive' WHERE id=$pid");
            }
            $orderStatus = $prod['type'] === 'physical' ? 'processing' : 'completed';
            $txSig = 'AUC_WIN_' . $aid . '_' . microtime(true);
            execute("INSERT INTO orders (account_id,total_gashy,tx_signature,status,created_at) VALUES ($winner_id,$amount,'$txSig','$orderStatus',NOW())");
            $oid = findQuery(" SELECT LAST_INSERT_ID() id")['id'];
            execute("INSERT INTO order_items (order_id,product_id,quantity,price_at_purchase) VALUES ($oid,$pid,1,$amount)");
            if ($prod['type'] === 'gift_card' || $prod['type'] === 'digital') {
                $cards = getQuery(" SELECT id FROM gift_cards WHERE product_id=$pid AND is_sold=0 ORDER BY id ASC LIMIT 1");
                if (!empty($cards)) {
                    $cid = (int)$cards[0]['id'];
                    execute("UPDATE gift_cards SET is_sold=1,sold_to_order_id=$oid WHERE id=$cid");
                } else {
                    echo " -> WARNING: No code available to deliver!\n";
                }
            }
            execute("UPDATE transactions SET status='confirmed' WHERE reference_id=$aid AND type='auction_bid' AND account_id=$winner_id AND amount=$amount");
            execute("UPDATE transactions SET status='failed' WHERE reference_id=$aid AND type='auction_bid' AND status='pending'");
            $acc = findQuery(" SELECT email,accountname FROM accounts WHERE id=$winner_id");
            if (!empty($acc['email']) && function_exists('mailer')) {
                $body = "<h1>You Won!</h1><p>Hi {$acc['accountname']},</p><p>You won Auction #$aid.</p><p>Order #$oid created ($orderStatus).</p>";
                mailer("Auction Won", $body, "Gashy Auctions", $acc['email']);
            }
            echo " -> Sold to ID: $winner_id. Order #$oid ($orderStatus). Losers refunded.\n";
        } else {
            execute("UPDATE auctions SET status='ended' WHERE id=$aid");
            execute("UPDATE transactions SET status='failed' WHERE reference_id=$aid AND type='auction_bid'");
            if ($winner_id && $amount < $reserve) {
                echo " -> Reserve not met ($amount < $reserve). Bids refunded.\n";
            } else {
                echo " -> No Bids. Closed.\n";
            }
        }
        execute("COMMIT");
    } catch (Exception $e) {
        execute("ROLLBACK");
        echo " -> Error: " . $e->getMessage() . "\n";
    }
}
echo "Done.\n";
