<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$token = request('token') ?: str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() LIMIT 1 ");
if (!$session) encode(['status' => false, 'message' => 'Unauthorized']);
$uid = (int)$session['account_id'];
$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?: [];
$items = $data['items'] ?? [];
$txSig = trim($data['tx_signature'] ?? '');
if (!$items || !is_array($items) || $txSig === '') encode(['status' => false, 'message' => 'Invalid input data']);
$dup = findQuery(" SELECT id FROM transactions WHERE tx_signature='$txSig' LIMIT 1 ");
if ($dup) encode(['status' => false, 'message' => 'Transaction already processed']);
execute(" START TRANSACTION ");
try {
    $account = findQuery(" SELECT id,email,accountname,tier FROM accounts WHERE id=$uid LIMIT 1 ");
    if (!$account) throw new Exception('Account not found');
    $tier = $account['tier'] ?: 'bronze';
    $discountMap = ['bronze' => 0, 'silver' => 0.02, 'gold' => 0.05, 'platinum' => 0.10, 'diamond' => 0.15];
    $discountRate = $discountMap[$tier] ?? 0;
    $rate = (float)toGashy();
    if ($rate <= 0) throw new Exception('Rate unavailable');
    $hasAttrCol = (bool)findQuery(" SHOW COLUMNS FROM order_items LIKE 'attributes' ");
    $hasOptionCol = (bool)findQuery(" SHOW COLUMNS FROM order_items LIKE 'option_id' ");
    $subtotalUsd = 0;
    $subtotalGashy = 0;
    $orderStatus = 'completed';
    $prepared = [];
    foreach ($items as $row) {
        $pid = (int)($row['id'] ?? 0);
        $qty = (int)($row['qty'] ?? 1);
        $attrs = $row['attributes'] ?? [];
        $optionId = (int)($row['option_id'] ?? 0);
        if ($pid < 1 || $qty < 1) continue;
        $product = findQuery(" SELECT * FROM products WHERE id=$pid AND status='active' LIMIT 1 ");
        if (!$product) throw new Exception("Product not found #$pid");
        if ((int)$product['seller_id'] === $uid) throw new Exception('You cannot buy your own product');
        $locked = findQuery(" SELECT stock FROM products WHERE id=$pid FOR UPDATE ");
        $stock = (int)($locked['stock'] ?? 0);
        $reserved = (int)(findQuery(" SELECT COALESCE(SUM(oi.quantity),0) q FROM order_items oi INNER JOIN orders o ON o.id=oi.order_id WHERE oi.product_id=$pid AND o.status IN('pending','processing') ")['q'] ?? 0);
        $available = max(0, $stock - $reserved);
        if ($available < $qty) throw new Exception($product['title'] . ' out of stock');
        $unitUsd = (float)$product['price_usd'];
        $unitGashy = $unitUsd / $rate;
        if ($optionId > 0) {
            $opt = findQuery(" SELECT * FROM gift_card_options WHERE id=$optionId AND product_id=$pid LIMIT 1 ");
            if (!$opt) throw new Exception('Invalid option selected');
            if (isset($opt['price_usd']) && (float)$opt['price_usd'] > 0) {
                $unitUsd = (float)$opt['price_usd'];
                $unitGashy = $unitUsd / $rate;
            }
        }
        $lineUsd = $unitUsd * $qty;
        $lineGashy = $unitGashy * $qty;
        $subtotalUsd += $lineUsd;
        $subtotalGashy += $lineGashy;
        if ($product['type'] === 'physical') $orderStatus = 'processing';
        $prepared[] = ['pid' => $pid, 'qty' => $qty, 'product' => $product, 'option_id' => $optionId, 'attrs' => $attrs, 'unit_usd' => $unitUsd, 'unit_gashy' => $unitGashy, 'line_usd' => $lineUsd, 'line_gashy' => $lineGashy];
    }
    if (!$prepared) throw new Exception('No valid items');
    $finalUsd = $subtotalUsd * (1 - $discountRate);
    $finalGashy = $subtotalGashy * (1 - $discountRate);
    execute(" INSERT INTO orders(account_id,total_gashy,total_usd,tx_signature,status,created_at,updated_at) VALUES($uid,$finalGashy,$finalUsd,'$txSig','$orderStatus',NOW(),NOW()) ");
    $oid = (int)(findQuery(" SELECT LAST_INSERT_ID() id ")['id'] ?? 0);
    if ($oid < 1) throw new Exception('Order creation failed');
    $emailItems = '';
    foreach ($prepared as $row) {
        $pid = $row['pid'];
        $qty = $row['qty'];
        $product = $row['product'];
        $optionId = $row['option_id'];
        $attrs = is_array($row['attrs']) ? $row['attrs'] : [];
        $unitGashy = $row['unit_gashy'];
        $attrJson = json_encode($attrs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($hasAttrCol && $hasOptionCol) execute(" INSERT INTO order_items(order_id,product_id,quantity,price_at_purchase,option_id,attributes) VALUES($oid,$pid,$qty,$unitGashy,$optionId,'$attrJson') ");
        elseif ($hasAttrCol) execute(" INSERT INTO order_items(order_id,product_id,quantity,price_at_purchase,attributes) VALUES($oid,$pid,$qty,$unitGashy,'$attrJson') ");
        elseif ($hasOptionCol) execute(" INSERT INTO order_items(order_id,product_id,quantity,price_at_purchase,option_id) VALUES($oid,$pid,$qty,$unitGashy,$optionId) ");
        else execute(" INSERT INTO order_items(order_id,product_id,quantity,price_at_purchase) VALUES($oid,$pid,$qty,$unitGashy) ");
        execute(" UPDATE products SET stock=stock-$qty WHERE id=$pid ");
        if ($product['type'] === 'gift_card' || $product['type'] === 'digital') {
            $whereOpt = $optionId > 0 ? " AND gift_card_option_id=$optionId " : "";
            $cards = getQuery(" SELECT id FROM gift_cards WHERE product_id=$pid AND is_sold=0 $whereOpt ORDER BY id ASC LIMIT $qty ");
            if (count($cards) < $qty) throw new Exception('Inventory mismatch for ' . $product['title']);
            foreach ($cards as $card) execute(" UPDATE gift_cards SET is_sold=1,sold_to_order_id=$oid,sold_at=NOW() WHERE id={$card['id']} ");
        }
        $sellerId = (int)$product['seller_id'];
        $seller = findQuery(" SELECT account_id FROM sellers WHERE account_id=$sellerId LIMIT 1 ");
        if ($seller) execute(" UPDATE sellers SET total_sales=total_sales+$qty WHERE account_id=$sellerId ");
        $emailItems .= "<li>" . htmlspecialchars($product['title']) . " (x$qty)</li>";
    }
    execute(" INSERT INTO transactions(account_id,type,amount,tx_signature,reference_id,status,created_at) VALUES($uid,'purchase',-$finalGashy,'$txSig',$oid,'confirmed',NOW()) ");
    $ref = findQuery(" SELECT referrer_id FROM account_referrals WHERE referee_id=$uid LIMIT 1 ");
    if ($ref) {
        $refId = (int)$ref['referrer_id'];
        $exists = findQuery(" SELECT id FROM transactions WHERE account_id=$refId AND type='reward' AND reference_id=$oid LIMIT 1 ");
        if (!$exists) {
            $comm = $finalGashy * 0.05;
            if ($comm > 0) execute(" INSERT INTO transactions(account_id,type,amount,reference_id,status,created_at) VALUES($refId,'reward',$comm,$oid,'confirmed',NOW()) ");
        }
    }
    if (function_exists('updateQuestProgress')) updateQuestProgress($uid, 'buy', $finalGashy);
    if (function_exists('logActivity')) logActivity('account', $uid, 'purchase', "Order #$oid");
    if (($account['email'] ?? '') && function_exists('mailer')) {
        $name = $account['accountname'] ?: 'User';
        $subject = "Order #$oid Confirmed";
        $body = "<div style='font-family:Arial;padding:20px;color:#222'><h2 style='color:#00d48f'>Order Confirmed</h2><p>Hello {$name},</p><p>Your order has been placed successfully.</p><p><strong>Order:</strong> #$oid<br><strong>Status:</strong> " . strtoupper($orderStatus) . "<br><strong>Total:</strong> " . number_format($finalGashy, 2) . " GASHY</p><ul>$emailItems</ul><p><a href='https://gashybazaar.com/orders.php' style='padding:10px 18px;background:#00d48f;color:#fff;text-decoration:none;border-radius:6px'>View Orders</a></p></div>";
        mailer($subject, $body, 'Gashy Bazaar', $account['email']);
    }
    execute(" COMMIT ");
    encode(['status' => true, 'order_id' => $oid, 'discount_applied' => $discountRate * 100, 'final_total' => $finalGashy]);
} catch (Exception $e) {
    execute(" ROLLBACK ");
    encode(['status' => false, 'message' => $e->getMessage()]);
}
