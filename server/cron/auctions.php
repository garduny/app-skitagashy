<?php
require_once __DIR__ . '/init.php';
$expired = getQuery(" SELECT id,product_id,option_id,highest_bidder_id,current_bid_usd,reserve_price_usd FROM auctions WHERE status='active' AND end_time<=NOW() ORDER BY id ASC ");
$rate = toGashy();
foreach ($expired as $a) {
    $aid = (int)$a['id'];
    echo "Processing Auction ID: {$aid}...\n";
    execute(" START TRANSACTION ");
    try {
        $lock = findQuery(" SELECT id,status,product_id,option_id,highest_bidder_id,current_bid_usd,reserve_price_usd FROM auctions WHERE id=$aid FOR UPDATE ");
        if (!$lock || $lock['status'] !== 'active') {
            execute(" ROLLBACK ");
            echo " -> Skipped.\n";
            continue;
        }
        $pid = (int)$lock['product_id'];
        $opt = (int)$lock['option_id'];
        $winnerId = (int)$lock['highest_bidder_id'];
        $amountUsd = (float)$lock['current_bid_usd'];
        $reserveUsd = (float)$lock['reserve_price_usd'];
        $prod = findQuery(" SELECT id,title,type,status,stock,seller_id FROM products WHERE id=$pid FOR UPDATE ");
        if (!$prod) throw new Exception('Product missing');
        if ($winnerId > 0 && $amountUsd > 0 && $amountUsd >= $reserveUsd) {
            $orderStatus = $prod['type'] === 'physical' ? 'processing' : 'completed';
            $amountGashy = $rate > 0 ? ($amountUsd / $rate) : 0;
            if ($amountGashy <= 0) throw new Exception('Invalid rate');
            execute(" UPDATE auctions SET status='ended' WHERE id=$aid ");
            if ($prod['type'] === 'nft') execute(" UPDATE products SET seller_id=$winnerId,status='inactive' WHERE id=$pid ");
            elseif ($prod['type'] === 'physical') {
                if ((int)$prod['stock'] < 1) throw new Exception('Out of stock');
                execute(" UPDATE products SET stock=stock-1 WHERE id=$pid ");
            }
            $txSig = 'AUC_WIN_' . $aid . '_' . time() . '_' . mt_rand(1000, 9999);
            execute(" INSERT INTO orders (account_id,total_gashy,total_usd,tx_signature,status,created_at) VALUES ($winnerId,$amountGashy,$amountUsd,'$txSig','$orderStatus',NOW()) ");
            $oid = findQuery(" SELECT LAST_INSERT_ID() id ")['id'];
            execute(" INSERT INTO order_items (order_id,product_id,option_id,quantity,price_at_purchase) VALUES ($oid,$pid,$opt,1,$amountGashy) ");
            if ($prod['type'] === 'gift_card' || $prod['type'] === 'digital') {
                $gc = getQuery(" SELECT id FROM gift_cards WHERE product_id=$pid AND gift_card_option_id=$opt AND is_sold=0 ORDER BY id ASC LIMIT 1 ");
                if (!$gc) throw new Exception('No inventory code available');
                $cid = (int)$gc[0]['id'];
                execute(" UPDATE gift_cards SET is_sold=1,sold_to_order_id=$oid,sold_at=NOW() WHERE id=$cid ");
            }
            execute(" UPDATE transactions SET status='confirmed' WHERE reference_id=$aid AND type='auction_bid' AND account_id=$winnerId AND status='pending' ORDER BY id DESC LIMIT 1 ");
            execute(" UPDATE transactions SET status='failed' WHERE reference_id=$aid AND type='auction_bid' AND status='pending' ");
            $acc = findQuery(" SELECT email,accountname FROM accounts WHERE id=$winnerId ");
            if (!empty($acc['email']) && function_exists('mailer')) {
                $name = $acc['accountname'] ?: 'User';
                $body = "<div style='font-family:Arial;padding:20px;color:#222'><h2 style='color:#00d48f'>Auction Won</h2><p>Hello {$name},</p><p>You won auction <b>#{$aid}</b>.</p><p><strong>Order:</strong> #{$oid}<br><strong>Item:</strong> {$prod['title']}<br><strong>Total:</strong> " . number_format($amountGashy, 2) . " GASHY</p><p><a href='https://gashybazaar.com/orders.php' style='padding:10px 18px;background:#00d48f;color:#fff;text-decoration:none;border-radius:6px'>View Orders</a></p></div>";
                mailer("Auction #$aid Won", $body, "Gashy Auctions", $acc['email']);
            }
            echo " -> Winner ID {$winnerId}. Order #{$oid} created.\n";
        } else {
            execute(" UPDATE auctions SET status='ended' WHERE id=$aid ");
            execute(" UPDATE transactions SET status='failed' WHERE reference_id=$aid AND type='auction_bid' AND status='pending' ");
            echo $winnerId > 0 ? " -> Reserve not met.\n" : " -> No bids.\n";
        }
        execute(" COMMIT ");
    } catch (Exception $e) {
        execute(" ROLLBACK ");
        echo " -> Error: " . $e->getMessage() . "\n";
    }
}
echo "Done.\n";
