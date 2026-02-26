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
$boxId = request('box_id');
$txSig = request('tx_signature');
if (!$boxId || !$txSig) {
    encode(['status' => false, 'message' => 'Box ID and Transaction Signature required']);
}
$box = findQuery(" SELECT * FROM products WHERE id=$boxId AND type='mystery_box' ");
if (!$box) {
    encode(['status' => false, 'message' => 'Box not found']);
}
if ($box['stock'] < 1) {
    encode(['status' => false, 'message' => 'Box out of stock']);
}
$dup = findQuery(" SELECT id FROM transactions WHERE tx_signature='$txSig' ");
if ($dup) {
    encode(['status' => false, 'message' => 'Transaction already processed']);
}
$loot = getQuery(" SELECT * FROM mystery_box_loot WHERE box_product_id=$boxId ");
if (!$loot) {
    encode(['status' => false, 'message' => 'Box configuration error']);
}
execute(" START TRANSACTION ");
try {
    execute(" INSERT INTO transactions (account_id,type,amount,tx_signature,status,created_at) VALUES ($uid,'purchase',{$box['price_gashy']},'$txSig','confirmed',NOW()) ");
    execute(" UPDATE products SET stock=stock-1 WHERE id=$boxId ");
    $roll = random_int(1, 10000);
    $current = 0;
    $wonItem = null;
    foreach ($loot as $item) {
        $prob = $item['probability'] * 100;
        $current += $prob;
        if ($roll <= $current) {
            $wonItem = $item;
            break;
        }
    }
    if (!$wonItem) {
        $wonItem = $loot[count($loot) - 1];
    }
    $rewardData = [];
    if ($wonItem['reward_product_id']) {
        $pid = $wonItem['reward_product_id'];
        $prod = findQuery(" SELECT price_gashy,type,stock FROM products WHERE id=$pid ");
        if ($prod && $prod['stock'] > 0) {
            execute(" INSERT INTO orders (account_id,total_gashy,tx_signature,status,created_at) VALUES ($uid,0,'$txSig-REWARD','completed',NOW()) ");
            $oid = findQuery(" SELECT LAST_INSERT_ID() as id ")['id'];
            execute(" INSERT INTO order_items (order_id,product_id,quantity,price_at_purchase) VALUES ($oid,$pid,1,0) ");
            execute(" UPDATE products SET stock=stock-1 WHERE id=$pid ");
            if ($prod['type'] === 'gift_card') {
                $card = findQuery(" SELECT id FROM gift_cards WHERE product_id=$pid AND is_sold=0 LIMIT 1 ");
                if ($card) {
                    execute(" UPDATE gift_cards SET is_sold=1,sold_to_order_id=$oid WHERE id={$card['id']} ");
                }
            }
            $rewardData = ['type' => 'product', 'product_id' => $pid];
        } else {
            $fallbackAmount = $box['price_gashy'] / 2;
            execute(" INSERT INTO transactions (account_id,type,amount,reference_id,status,created_at) VALUES ($uid,'reward',$fallbackAmount,$boxId,'confirmed',NOW()) ");
            $rewardData = ['type' => 'token', 'amount' => $fallbackAmount, 'note' => 'Fallback (OOS)'];
        }
    } else {
        $amount = $wonItem['reward_amount'];
        execute(" INSERT INTO transactions (account_id,type,amount,reference_id,status,created_at) VALUES ($uid,'reward',$amount,$boxId,'confirmed',NOW()) ");
        $rewardData = ['type' => 'token', 'amount' => $amount];
    }
    updateQuestProgress($uid, 'burn', $box['price_gashy']);
    execute(" COMMIT ");
    encode(['status' => true, 'reward' => $rewardData, 'rarity' => $wonItem['rarity']]);
} catch (Exception $e) {
    execute(" ROLLBACK ");
    encode(['status' => false, 'message' => $e->getMessage()]);
}
