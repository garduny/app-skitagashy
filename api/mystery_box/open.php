<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) encode(['status' => false, 'message' => 'Unauthorized']);
$uid = (int)$session['account_id'];
$boxId = (int)request('box_id');
$txSig = request('tx_signature');
if (!$boxId || !$txSig) encode(['status' => false, 'message' => 'Box ID and Transaction Signature required']);
$box = findQuery(" SELECT id,price_usd,stock FROM products WHERE id=$boxId AND type='mystery_box' ");
if (!$box) encode(['status' => false, 'message' => 'Box not found']);
if ($box['stock'] < 1) encode(['status' => false, 'message' => 'Box out of stock']);
$dup = findQuery(" SELECT id FROM transactions WHERE tx_signature='$txSig' ");
if ($dup) encode(['status' => false, 'message' => 'Transaction already processed']);
$loot = getQuery(" SELECT * FROM mystery_box_loot WHERE box_product_id=$boxId ");
if (!$loot) encode(['status' => false, 'message' => 'Box configuration error']);
$rate = toGashy();
$boxPriceGashy = $rate > 0 ? ((float)$box['price_usd'] / $rate) : 0;
execute(" START TRANSACTION ");
try {
    execute(" INSERT INTO transactions (account_id,type,amount,tx_signature,status,created_at) VALUES ($uid,'purchase',$boxPriceGashy,'$txSig','confirmed',NOW()) ");
    execute(" UPDATE products SET stock=stock-1 WHERE id=$boxId AND stock>0 ");
    $roll = random_int(1, 10000);
    $current = 0;
    $won = null;
    foreach ($loot as $item) {
        $prob = $item['probability'] * 100;
        $current += $prob;
        if ($roll <= $current) {
            $won = $item;
            break;
        }
    }
    if (!$won) $won = $loot[count($loot) - 1];
    $reward = [];
    if ($won['reward_product_id']) {
        $pid = (int)$won['reward_product_id'];
        $opt = (int)($won['reward_option_id'] ?? 0);
        $prod = findQuery(" SELECT id,type,stock FROM products WHERE id=$pid ");
        if ($prod && $prod['stock'] > 0) {
            execute(" INSERT INTO orders (account_id,total_gashy,total_usd,tx_signature,status,created_at) VALUES ($uid,0,0,'$txSig-REWARD','completed',NOW()) ");
            $oid = findQuery(" SELECT LAST_INSERT_ID() id ")['id'];
            execute(" INSERT INTO order_items (order_id,product_id,option_id,quantity,price_at_purchase) VALUES ($oid,$pid,$opt,1,0) ");
            execute(" UPDATE products SET stock=stock-1 WHERE id=$pid AND stock>0 ");
            if ($prod['type'] === 'gift_card' || $prod['type'] === 'digital') {
                $card = findQuery(" SELECT id FROM gift_cards WHERE product_id=$pid AND gift_card_option_id=$opt AND is_sold=0 ORDER BY id ASC LIMIT 1 ");
                if ($card) execute(" UPDATE gift_cards SET is_sold=1,sold_to_order_id=$oid,sold_at=NOW() WHERE id={$card['id']} ");
            }
            $reward = ['type' => 'product', 'product_id' => $pid, 'option_id' => $opt];
        } else {
            $fallback = $boxPriceGashy / 2;
            execute(" INSERT INTO transactions (account_id,type,amount,reference_id,status,created_at) VALUES ($uid,'reward',$fallback,$boxId,'confirmed',NOW()) ");
            $reward = ['type' => 'token', 'amount' => $fallback, 'note' => 'Fallback (OOS)'];
        }
    } else {
        $amount = (float)$won['reward_amount'];
        execute(" INSERT INTO transactions (account_id,type,amount,reference_id,status,created_at) VALUES ($uid,'reward',$amount,$boxId,'confirmed',NOW()) ");
        $reward = ['type' => 'token', 'amount' => $amount];
    }
    updateQuestProgress($uid, 'burn', $boxPriceGashy);
    execute(" COMMIT ");
    encode(['status' => true, 'reward' => $reward, 'rarity' => $won['rarity']]);
} catch (Exception $e) {
    execute(" ROLLBACK ");
    encode(['status' => false, 'message' => $e->getMessage()]);
}
