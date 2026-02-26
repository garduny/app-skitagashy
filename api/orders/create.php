<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) encode(['status' => false, 'message' => 'Unauthorized']);
$uid = $session['account_id'];
$json = file_get_contents('php://input');
$data = json_decode($json, true);
$items = $data['items'] ?? null;
$txSig = $data['tx_signature'] ?? null;
if (empty($items) || !is_array($items) || empty($txSig)) encode(['status' => false, 'message' => 'Invalid input data']);
$dup = findQuery(" SELECT id FROM transactions WHERE tx_signature='$txSig' ");
if ($dup) encode(['status' => false, 'message' => 'Transaction already processed']);
execute(" START TRANSACTION ");
try {
    $account = findQuery(" SELECT tier,email,accountname FROM accounts WHERE id=$uid ");
    $tier = $account['tier'] ?? 'bronze';
    $discount_map = ['bronze' => 0, 'silver' => 0.02, 'gold' => 0.05, 'platinum' => 0.10, 'diamond' => 0.15];
    $discount_rate = $discount_map[$tier] ?? 0;
    $subtotal = 0;
    $status = 'processing';
    $total_qty = 0;
    foreach ($items as $i) {
        $pid = (int)$i['id'];
        $qty = (int)$i['qty'];
        if ($qty < 1) continue;
        $total_qty += $qty;
        $p = findQuery(" SELECT price_gashy,stock,type,seller_id FROM products WHERE id=$pid ");
        if (!$p) throw new Exception("Product #$pid Not Found");
        if ((int)$p['seller_id'] === (int)$uid) throw new Exception("You cannot buy your own product");
        if ($p['stock'] < $qty) throw new Exception("Product #$pid Out of Stock");
        $subtotal += ($p['price_gashy'] * $qty);
        if ($p['type'] === 'physical') $status = 'processing';
        else $status = 'completed';
    }
    $final_total = $subtotal * (1 - $discount_rate);
    execute(" INSERT INTO orders (account_id,total_gashy,tx_signature,status,created_at) VALUES ($uid,$final_total,'$txSig','$status',NOW()) ");
    $lastOrd = findQuery(" SELECT LAST_INSERT_ID() as id ");
    $oid = $lastOrd['id'];
    $email_items_html = "";
    foreach ($items as $i) {
        $pid = (int)$i['id'];
        $qty = (int)$i['qty'];
        if ($qty < 1) continue;
        $prod = findQuery(" SELECT title,price_gashy,type,seller_id FROM products WHERE id=$pid ");
        if (!$prod) throw new Exception("Product #$pid Not Found");
        if ((int)$prod['seller_id'] === (int)$uid) throw new Exception("You cannot buy your own product");
        execute(" INSERT INTO order_items (order_id,product_id,quantity,price_at_purchase) VALUES ($oid,$pid,$qty,{$prod['price_gashy']}) ");
        execute(" UPDATE products SET stock=stock-$qty WHERE id=$pid ");
        $email_items_html .= "<li>{$prod['title']} (x$qty)</li>";
        if ($prod['type'] === 'gift_card' || $prod['type'] === 'digital') {
            $cards = getQuery(" SELECT id FROM gift_cards WHERE product_id=$pid AND is_sold=0 LIMIT $qty ");
            if (count($cards) < $qty) throw new Exception("Gift Card inventory mismatch for Product #$pid");
            foreach ($cards as $c) {
                execute(" UPDATE gift_cards SET is_sold=1,sold_to_order_id=$oid WHERE id={$c['id']} ");
            }
        }
    }
    execute(" INSERT INTO transactions (account_id,type,amount,tx_signature,reference_id,status) VALUES ($uid,'purchase',-$final_total,'$txSig',$oid,'confirmed') ");
    $ref = findQuery(" SELECT referrer_id FROM account_referrals WHERE referee_id=$uid LIMIT 1 ");
    if ($ref) {
        $ref_id = $ref['referrer_id'];
        $comm = $final_total * 0.05;
        if ($comm > 0) execute(" INSERT INTO transactions (account_id,type,amount,reference_id,status,created_at) VALUES ($ref_id,'reward',$comm,$oid,'confirmed',NOW()) ");
    }
    if (function_exists('updateQuestProgress')) {
        updateQuestProgress($uid, 'buy', $final_total);
    }
    if (($account['email'] ?? '') && function_exists('mailer')) {
        $subject = "Order #$oid Confirmed";
        $body = "<div style='font-family: Arial, sans-serif; color: #333; padding: 20px;'><h2 style='color: #00d48f;'>Order Confirmed!</h2><p>Hi {$account['accountname']},</p><p>Your order has been successfully placed.</p><p><strong>Order ID:</strong> #$oid<br><strong>Status:</strong> " . strtoupper($status) . "<br><strong>Total:</strong> " . number_format($final_total, 2) . " GASHY</p><hr style='border: 0; border-top: 1px solid #eee;'><h3>Items:</h3><ul>$email_items_html</ul><p style='margin-top: 20px;'><a href='https://gashybazaar.com/orders.php' style='background: #00d48f; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View Order & Reveal Codes</a></p></div>";
        mailer($subject, $body, "Gashy Bazaar", $account['email']);
    }
    execute(" COMMIT ");
    encode(['status' => true, 'order_id' => $oid, 'discount_applied' => $discount_rate * 100, 'final_total' => $final_total]);
} catch (Exception $e) {
    execute(" ROLLBACK ");
    encode(['status' => false, 'message' => $e->getMessage()]);
}
