<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() LIMIT 1 ");
if (!$session) encode(['status' => false, 'message' => 'Unauthorized']);
$uid = (int)$session['account_id'];
$boxId = (int)request('box_id');
$txSig = trim(request('tx_signature') ?? '');
if ($boxId < 1 || $txSig === '') encode(['status' => false, 'message' => 'Box ID and Transaction Signature required']);
$dup = findQuery(" SELECT id FROM transactions WHERE tx_signature='$txSig' LIMIT 1 ");
if ($dup) encode(['status' => false, 'message' => 'Transaction already processed']);
$rate = (float)toGashy();
if ($rate <= 0) encode(['status' => false, 'message' => 'Invalid token rate']);
execute(" START TRANSACTION ");
try {
    $box = findQuery(" SELECT * FROM products WHERE id=$boxId AND type='mystery_box' AND status='active' LIMIT 1 FOR UPDATE ");
    if (!$box) throw new Exception('Box not found');
    if ((int)$box['stock'] < 1) throw new Exception('Box out of stock');
    $loot = getQuery(" SELECT * FROM mystery_box_loot WHERE box_product_id=$boxId AND is_active=1 ORDER BY probability DESC,id ASC ");
    if (!$loot) throw new Exception('Box configuration error');
    $boxPriceUsd = (float)$box['price_usd'];
    $boxPriceGashy = $boxPriceUsd / $rate;
    execute(" INSERT INTO transactions (account_id,type,amount,tx_signature,reference_id,status,created_at) VALUES ($uid,'purchase',-$boxPriceGashy,'$txSig',$boxId,'confirmed',NOW()) ");
    execute(" UPDATE products SET stock=stock-1 WHERE id=$boxId AND stock>0 ");
    $roll = random_int(1, 10000);
    $current = 0;
    $won = null;
    foreach ($loot as $item) {
        $prob = (float)$item['probability'] * 100;
        $current += $prob;
        if ($roll <= $current) {
            $won = $item;
            break;
        }
    }
    if (!$won) $won = $loot[count($loot) - 1];
    $reward = ['type' => 'none'];
    if ((int)$won['reward_product_id'] > 0) {
        $pid = (int)$won['reward_product_id'];
        $opt = (int)($won['reward_option_id'] ?? 0);
        $prod = findQuery(" SELECT * FROM products WHERE id=$pid LIMIT 1 FOR UPDATE ");
        if ($prod && (int)$prod['stock'] > 0 && $prod['status'] === 'active') {
            $orderStatus = $prod['type'] === 'physical' ? 'processing' : 'completed';
            $rewardTx = 'MB_REWARD_' . $boxId . '_' . time() . '_' . mt_rand(1000, 9999);
            execute(" INSERT INTO orders (account_id,total_gashy,total_usd,tx_signature,status,created_at,updated_at) VALUES ($uid,0,0,'$rewardTx','$orderStatus',NOW(),NOW()) ");
            $oid = (int)(findQuery(" SELECT LAST_INSERT_ID() id ")['id'] ?? 0);
            if ($oid < 1) throw new Exception('Reward order creation failed');
            $hasOptionCol = (bool)findQuery(" SHOW COLUMNS FROM order_items LIKE 'option_id' ");
            if ($hasOptionCol) execute(" INSERT INTO order_items (order_id,product_id,option_id,quantity,price_at_purchase) VALUES ($oid,$pid,$opt,1,0) ");
            else execute(" INSERT INTO order_items (order_id,product_id,quantity,price_at_purchase) VALUES ($oid,$pid,1,0) ");
            execute(" UPDATE products SET stock=stock-1 WHERE id=$pid AND stock>0 ");
            if ($prod['type'] === 'gift_card' || $prod['type'] === 'digital') {
                $whereOpt = $opt > 0 ? " AND gift_card_option_id=$opt " : "";
                $card = findQuery(" SELECT id FROM gift_cards WHERE product_id=$pid AND is_sold=0 $whereOpt ORDER BY id ASC LIMIT 1 FOR UPDATE ");
                if ($card) {
                    $cid = (int)$card['id'];
                    execute(" UPDATE gift_cards SET is_sold=1,sold_to_order_id=$oid,sold_at=NOW() WHERE id=$cid ");
                } else {
                    $fallback = max(0, $boxPriceGashy / 2);
                    execute(" INSERT INTO transactions (account_id,type,amount,reference_id,status,created_at) VALUES ($uid,'reward',$fallback,$boxId,'confirmed',NOW()) ");
                    $reward = ['type' => 'token', 'amount' => $fallback, 'note' => 'Fallback (No digital inventory)', 'rarity' => $won['rarity']];
                    execute(" UPDATE mystery_box_loot SET won_count=won_count+1,updated_at=NOW() WHERE id=" . (int)$won['id']);
                    if (function_exists('updateQuestProgress')) updateQuestProgress($uid, 'burn', $boxPriceGashy);
                    if (function_exists('logActivity')) logActivity('account', $uid, 'mystery_box_open', 'Box #' . $boxId . ' fallback reward');
                    execute(" COMMIT ");
                    encode(['status' => true, 'reward' => $reward, 'rarity' => $won['rarity']]);
                }
            }
            $reward = ['type' => 'product', 'product_id' => $pid, 'option_id' => $opt, 'order_id' => $oid, 'title' => $prod['title'] ?? 'Reward', 'rarity' => $won['rarity']];
        } else {
            $fallback = max(0, $boxPriceGashy / 2);
            execute(" INSERT INTO transactions (account_id,type,amount,reference_id,status,created_at) VALUES ($uid,'reward',$fallback,$boxId,'confirmed',NOW()) ");
            $reward = ['type' => 'token', 'amount' => $fallback, 'note' => 'Fallback (OOS)', 'rarity' => $won['rarity']];
        }
    } else {
        $amount = (float)$won['reward_amount'];
        execute(" INSERT INTO transactions (account_id,type,amount,reference_id,status,created_at) VALUES ($uid,'reward',$amount,$boxId,'confirmed',NOW()) ");
        $reward = ['type' => 'token', 'amount' => $amount, 'rarity' => $won['rarity']];
    }
    execute(" UPDATE mystery_box_loot SET won_count=won_count+1,updated_at=NOW() WHERE id=" . (int)$won['id']);
    if (function_exists('updateQuestProgress')) updateQuestProgress($uid, 'burn', $boxPriceGashy);
    if (function_exists('logActivity')) logActivity('account', $uid, 'mystery_box_open', 'Box #' . $boxId . ' opened');
    $acc = findQuery(" SELECT email,accountname FROM accounts WHERE id=$uid LIMIT 1 ");
    if (($acc['email'] ?? '') && function_exists('mailer')) {
        $name = $acc['accountname'] ?: 'User';
        $rewardText = $reward['type'] === 'product' ? 'You won a product reward!' : 'You won ' . number_format((float)($reward['amount'] ?? 0), 2) . ' GASHY';
        $subject = "Mystery Box Opened";
        $body = "<div style='font-family:Arial;padding:20px;color:#222'><h2 style='color:#7c3aed'>Mystery Box Result</h2><p>Hello {$name},</p><p>You opened <strong>{$box['title']}</strong>.</p><p><strong>Result:</strong> {$rewardText}<br><strong>Rarity:</strong> " . strtoupper($won['rarity']) . "</p><p><a href='https://gashybazaar.com/orders.php' style='padding:10px 18px;background:#7c3aed;color:#fff;text-decoration:none;border-radius:6px'>View Orders</a></p></div>";
        mailer($subject, $body, 'Gashy Bazaar', $acc['email']);
    }
    execute(" COMMIT ");
    encode(['status' => true, 'reward' => $reward, 'rarity' => $won['rarity']]);
} catch (Exception $e) {
    execute(" ROLLBACK ");
    encode(['status' => false, 'message' => $e->getMessage()]);
}
