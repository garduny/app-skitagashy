<?php
require_once __DIR__.'/init.php';
echo "[".date('Y-m-d H:i:s')."] Auctions Started\n";
function syncAuctionProductStock($productId)
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
$expired = getQuery(" SELECT id,product_id,option_id,highest_bidder_id,current_bid_usd,reserve_price_usd FROM auctions WHERE status='active' AND end_time<=NOW() ORDER BY id ASC ");
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
        $rate = (float)toGashy();
        $prod = findQuery(" SELECT id,title,type,status,stock,seller_id FROM products WHERE id=$pid FOR UPDATE ");
        if (!$prod) throw new Exception('Product missing');
        if ($winnerId > 0 && $amountUsd > 0 && $amountUsd >= $reserveUsd) {
            $orderStatus = $prod['type'] === 'physical' ? 'processing' : 'completed';
            $amountGashy = $rate > 0 ? ($amountUsd / $rate) : 0;
            if ($amountGashy <= 0) throw new Exception('Invalid rate');
            execute(" UPDATE auctions SET status='ended' WHERE id=$aid ");
            if ($prod['type'] === 'nft') {
                execute(" UPDATE products SET seller_id=$winnerId,status='inactive' WHERE id=$pid ");
            } elseif ($prod['type'] === 'physical') {
                if ((int)$prod['stock'] < 1) throw new Exception('Out of stock');
                execute(" UPDATE products SET stock=stock-1 WHERE id=$pid ");
            }
            $txSig = 'AUC_WIN_' . $aid . '_' . date('YmdHis') . '_' . mt_rand(100000, 999999);
            execute(" INSERT INTO orders (account_id,total_gashy,total_usd,tx_signature,status,created_at) VALUES ($winnerId,$amountGashy,$amountUsd,'$txSig','$orderStatus',NOW()) ");
            $oid = (int)(findQuery(" SELECT LAST_INSERT_ID() id ")['id'] ?? 0);
            if ($oid < 1) throw new Exception('Order creation failed');
            execute(" INSERT INTO order_items (order_id,product_id,option_id,gift_card_option_id,quantity,price_usd_at_purchase,price_at_purchase,meta_data) VALUES ($oid,$pid," . ($opt > 0 ? $opt : "NULL") . "," . ($opt > 0 ? $opt : "NULL") . ",1,$amountUsd,$amountGashy,NULL) ");
            if ($prod['type'] === 'gift_card' || $prod['type'] === 'digital') {
                if ($opt > 0) {
                    $gc = findQuery(" SELECT id FROM gift_cards WHERE product_id=$pid AND gift_card_option_id=$opt AND is_sold=0 ORDER BY id ASC LIMIT 1 FOR UPDATE ");
                } else {
                    $gc = findQuery(" SELECT id FROM gift_cards WHERE product_id=$pid AND gift_card_option_id IS NULL AND is_sold=0 ORDER BY id ASC LIMIT 1 FOR UPDATE ");
                }
                if (!$gc) throw new Exception('No inventory code available');
                $cid = (int)$gc['id'];
                execute(" UPDATE gift_cards SET is_sold=1,sold_to_order_id=$oid,sold_at=NOW() WHERE id=$cid AND is_sold=0 ");
            }
            $winnerTx = findQuery(" SELECT id FROM transactions WHERE reference_id=$aid AND type='auction_bid' AND account_id=$winnerId AND status='pending' ORDER BY id DESC LIMIT 1 FOR UPDATE ");
            if ($winnerTx) {
                $winnerTxId = (int)$winnerTx['id'];
                execute(" UPDATE transactions SET status='confirmed' WHERE id=$winnerTxId ");
            }
            execute(" UPDATE transactions SET status='failed' WHERE reference_id=$aid AND type='auction_bid' AND account_id!=$winnerId AND status='pending' ");
            if ($prod['type'] === 'gift_card' || $prod['type'] === 'digital') syncAuctionProductStock($pid);
            if ((int)$prod['seller_id'] > 0) execute(" UPDATE sellers SET total_sales=COALESCE(total_sales,0)+1 WHERE account_id=" . (int)$prod['seller_id']);
            execute(" COMMIT ");
            $acc = findQuery(" SELECT email,accountname FROM accounts WHERE id=$winnerId ");
            if (!empty($acc['email']) && function_exists('mailer')) {
                $name = $acc['accountname'] ?: 'User';
                $body = "<div style='font-family:Arial;padding:20px;color:#222'><h2 style='color:#00d48f'>Auction Won</h2><p>Hello {$name},</p><p>You won auction <b>#{$aid}</b>.</p><p><strong>Order:</strong> #{$oid}<br><strong>Item:</strong> {$prod['title']}<br><strong>Total:</strong> " . number_format($amountGashy, 3) . " GASHY</p><p><a href='https://gashybazaar.com/orders.php' style='padding:10px 18px;background:#00d48f;color:#fff;text-decoration:none;border-radius:6px'>View Orders</a></p></div>";
                mailer("Auction #$aid Won", $body, "Gashy Auctions", $acc['email']);
            }
            echo " -> Winner ID {$winnerId}. Order #{$oid} created.\n";
        } else {
            execute(" UPDATE auctions SET status='ended' WHERE id=$aid ");
            execute(" UPDATE transactions SET status='failed' WHERE reference_id=$aid AND type='auction_bid' AND status='pending' ");
            execute(" COMMIT ");
            echo $winnerId > 0 ? " -> Reserve not met.\n" : " -> No bids.\n";
        }
    } catch (Exception $e) {
        execute(" ROLLBACK ");
        echo " -> Error: " . $e->getMessage() . "\n";
    }
}
echo "Done.\n";
