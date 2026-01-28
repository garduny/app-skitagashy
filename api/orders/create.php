<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$account_session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$account_session) {
    encode(['status' => false, 'message' => 'Unauthorized']);
}
$uid = $account_session['account_id'];
$json = file_get_contents('php://input');
$data = json_decode($json, true);
$items = $data['items'] ?? null;
$txSig = $data['tx_signature'] ?? null;
$total = $data['total'] ?? null;
if (empty($items) || !is_array($items) || empty($txSig) || !isset($total)) {
    encode(['status' => false, 'message' => 'Invalid input data']);
}
$dup = findQuery(" SELECT id FROM transactions WHERE tx_signature='$txSig' ");
if ($dup) {
    encode(['status' => false, 'message' => 'Transaction already processed']);
}
execute(" START TRANSACTION ");
try {
    execute(" INSERT INTO orders (account_id,total_gashy,tx_signature,status,created_at) VALUES ($uid,$total,'$txSig','pending',NOW()) ");
    $lastOrd = findQuery(" SELECT LAST_INSERT_ID() as id ");
    $oid = $lastOrd['id'];
    foreach ($items as $item) {
        $pid = (int)$item['id'];
        $qty = (int)$item['qty'];
        if ($qty < 1) continue;
        $prod = findQuery(" SELECT price_gashy,stock,type FROM products WHERE id=$pid FOR UPDATE ");
        if (!$prod || $prod['stock'] < $qty) {
            throw new Exception("Product ID $pid is out of stock");
        }
        $price = $prod['price_gashy'];
        execute(" INSERT INTO order_items (order_id,product_id,quantity,price_at_purchase) VALUES ($oid,$pid,$qty,$price) ");
        execute(" UPDATE products SET stock=stock-$qty WHERE id=$pid ");
        if ($prod['type'] === 'gift_card') {
            $cards = getQuery(" SELECT id FROM gift_cards WHERE product_id=$pid AND is_sold=0 LIMIT $qty ");
            if (count($cards) < $qty) {
                throw new Exception("Not enough gift card codes available for ID $pid");
            }
            foreach ($cards as $card) {
                $cid = $card['id'];
                execute(" UPDATE gift_cards SET is_sold=1,sold_to_order_id=$oid WHERE id=$cid ");
            }
        }
    }
    execute(" INSERT INTO transactions (account_id,type,amount,tx_signature,status) VALUES ($uid,'purchase',$total,'$txSig','pending') ");
    execute(" COMMIT ");
    encode(['status' => true, 'order_id' => $oid]);
} catch (Exception $e) {
    execute(" ROLLBACK ");
    encode(['status' => false, 'message' => $e->getMessage()]);
}
