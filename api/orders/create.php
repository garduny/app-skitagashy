<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) {
    encode(['status' => false, 'message' => 'Unauthorized']);
}
$uid = $session['account_id'];
$json = file_get_contents('php://input');
$data = json_decode($json, true);
$items = $data['items'] ?? null;
$txSig = $data['tx_signature'] ?? null;
if (empty($items) || !is_array($items) || empty($txSig)) {
    encode(['status' => false, 'message' => 'Invalid input data']);
}
$dup = findQuery(" SELECT id FROM transactions WHERE tx_signature='$txSig' ");
if ($dup) {
    encode(['status' => false, 'message' => 'Transaction already processed']);
}
execute(" START TRANSACTION ");
try {
    $account = findQuery(" SELECT tier FROM accounts WHERE id=$uid ");
    $tier = $account['tier'] ?? 'bronze';
    $discount_map = ['bronze' => 0, 'silver' => 0.02, 'gold' => 0.05, 'platinum' => 0.10, 'diamond' => 0.15];
    $discount_rate = $discount_map[$tier] ?? 0;
    $subtotal = 0;
    foreach ($items as $i) {
        $pid = (int)$i['id'];
        $qty = (int)$i['qty'];
        if ($qty < 1) continue;
        $p = findQuery(" SELECT price_gashy,stock FROM products WHERE id=$pid ");
        if (!$p || $p['stock'] < $qty) {
            throw new Exception("Product #$pid Out of Stock");
        }
        $subtotal += ($p['price_gashy'] * $qty);
    }
    $final_total = $subtotal * (1 - $discount_rate);
    execute(" INSERT INTO orders (account_id,total_gashy,tx_signature,status,created_at) VALUES ($uid,$final_total,'$txSig','completed',NOW()) ");
    $lastOrd = findQuery(" SELECT LAST_INSERT_ID() as id ");
    $oid = $lastOrd['id'];
    foreach ($items as $i) {
        $pid = (int)$i['id'];
        $qty = (int)$i['qty'];
        if ($qty < 1) continue;
        $prod = findQuery(" SELECT price_gashy,type FROM products WHERE id=$pid ");
        execute(" INSERT INTO order_items (order_id,product_id,quantity,price_at_purchase) VALUES ($oid,$pid,$qty,{$prod['price_gashy']}) ");
        execute(" UPDATE products SET stock=stock-$qty WHERE id=$pid ");
        if ($prod['type'] === 'gift_card') {
            $cards = getQuery(" SELECT id FROM gift_cards WHERE product_id=$pid AND is_sold=0 LIMIT $qty ");
            if (count($cards) < $qty) {
                throw new Exception("Gift Card inventory mismatch for Product #$pid");
            }
            foreach ($cards as $c) {
                execute(" UPDATE gift_cards SET is_sold=1,sold_to_order_id=$oid WHERE id={$c['id']} ");
            }
        }
    }
    execute(" INSERT INTO transactions (account_id,type,amount,tx_signature,reference_id,status) VALUES ($uid,'purchase',-$final_total,'$txSig',$oid,'confirmed') ");
    $ref = findQuery(" SELECT referrer_account_id FROM account_referrals WHERE referee_account_id=$uid LIMIT 1 ");
    if ($ref) {
        $ref_id = $ref['referrer_account_id'];
        $comm = $final_total * 0.05;
        if ($comm > 0) {
            execute(" INSERT INTO transactions (account_id,type,amount,reference_id,status,created_at) VALUES ($ref_id,'reward',$comm,$oid,'confirmed',NOW()) ");
        }
    }
    execute(" COMMIT ");
    encode(['status' => true, 'order_id' => $oid, 'discount_applied' => $discount_rate * 100, 'final_total' => $final_total]);
} catch (Exception $e) {
    execute(" ROLLBACK ");
    encode(['status' => false, 'message' => $e->getMessage()]);
}
