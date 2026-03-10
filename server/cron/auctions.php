<?php
require_once __DIR__ . '/init.php';
$expired = getQuery(" SELECT id,product_id,option_id,highest_bidder_id,current_bid_usd,reserve_price_usd FROM auctions WHERE status='active' AND end_time<=NOW() ");
$rate = toGashy();
foreach ($expired as $a) {
    $aid = (int)$a['id'];
    echo "Processing Auction ID: {$aid}...\n";
    execute(" START TRANSACTION ");
    try {
        $locked = findQuery(" SELECT status FROM auctions WHERE id=$aid FOR UPDATE ");
        if (!$locked || $locked['status'] !== 'active') {
            execute(" ROLLBACK ");
            echo " -> Skipped (already processed).\n";
            continue;
        }
        $pid = (int)$a['product_id'];
        $opt = (int)($a['option_id'] ?? 0);
        $winner_id = (int)$a['highest_bidder_id'];
        $amount_usd = (float)$a['current_bid_usd'];
        $reserve_usd = (float)($a['reserve_price_usd'] ?? 0);
        if ($winner_id && $amount_usd >= $reserve_usd) {
            execute(" UPDATE auctions SET status='ended' WHERE id=$aid ");
            $prod = findQuery(" SELECT type FROM products WHERE id=$pid ");
            if (!$prod) throw new Exception("Product missing");
            if ($prod['type'] === 'nft') execute(" UPDATE products SET seller_id=$winner_id,status='inactive' WHERE id=$pid ");
            $orderStatus = $prod['type'] === 'physical' ? 'processing' : 'completed';
            $amount_gashy = $rate > 0 ? ($amount_usd / $rate) : 0;
            $txSig = 'AUC_WIN_' . $aid . '_' . microtime(true);
            execute(" INSERT INTO orders (account_id,total_gashy,tx_signature,status,created_at) VALUES ($winner_id,$amount_gashy,'$txSig','$orderStatus',NOW()) ");
            $oid = findQuery(" SELECT LAST_INSERT_ID() id ")['id'];
            execute(" INSERT INTO order_items (order_id,product_id,option_id,quantity,price_at_purchase) VALUES ($oid,$pid,$opt,1,$amount_gashy) ");
            if ($prod['type'] === 'gift_card' || $prod['type'] === 'digital') {
                $cards = getQuery(" SELECT id FROM gift_cards WHERE product_id=$pid AND option_id=$opt AND is_sold=0 ORDER BY id ASC LIMIT 1 ");
                if (!empty($cards)) {
                    $cid = (int)$cards[0]['id'];
                    execute(" UPDATE gift_cards SET is_sold=1,sold_to_order_id=$oid WHERE id=$cid ");
                } else {
                    echo " -> WARNING: No code available to deliver!\n";
                }
            }
            execute(" UPDATE transactions SET status='confirmed' WHERE reference_id=$aid AND type='auction_bid' AND account_id=$winner_id AND amount=$amount_usd ");
            execute(" UPDATE transactions SET status='failed' WHERE reference_id=$aid AND type='auction_bid' AND status='pending' ");
            $acc = findQuery(" SELECT email,accountname FROM accounts WHERE id=$winner_id ");
            if (!empty($acc['email']) && function_exists('mailer')) {
                $body = "<h1>You Won!</h1><p>Hi {$acc['accountname']},</p><p>You won Auction #$aid.</p><p>Order #$oid created ($orderStatus).</p>";
                mailer("Auction Won", $body, "Gashy Auctions", $acc['email']);
            }
            echo " -> Sold to ID: $winner_id. Order #$oid ($orderStatus). Losers refunded.\n";
        } else {
            execute(" UPDATE auctions SET status='ended' WHERE id=$aid ");
            execute(" UPDATE transactions SET status='failed' WHERE reference_id=$aid AND type='auction_bid' ");
            if ($winner_id && $amount_usd < $reserve_usd) echo " -> Reserve not met ($amount_usd < $reserve_usd). Bids refunded.\n";
            else echo " -> No Bids. Closed.\n";
        }
        execute(" COMMIT ");
    } catch (Exception $e) {
        execute(" ROLLBACK ");
        echo " -> Error: " . $e->getMessage() . "\n";
    }
}
echo "Done.\n";
