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
$drop_id = (int)($data['drop_id'] ?? 0);
$txSig = $data['tx_signature'] ?? '';
if (!$drop_id || !$txSig) {
    encode(['status' => false, 'message' => 'Invalid request']);
}
$dup = findQuery(" SELECT id FROM nft_mints WHERE tx_signature='$txSig' ");
if ($dup) {
    encode(['status' => false, 'message' => 'Transaction used']);
}
execute(" START TRANSACTION ");
try {
    $drop = findQuery(" SELECT * FROM nft_drops WHERE id=$drop_id FOR UPDATE ");
    if (!$drop) {
        throw new Exception("Drop not found");
    }
    if ($drop['status'] !== 'approved') {
        throw new Exception("Drop is not active");
    }
    if (strtotime($drop['start_time']) > time()) {
        throw new Exception("Mint not started");
    }
    if (strtotime($drop['end_time']) < time()) {
        throw new Exception("Mint ended");
    }
    if ($drop['minted_count'] >= $drop['max_supply']) {
        throw new Exception("Sold Out");
    }
    $price = $drop['price_gashy'];
    $seller_id = $drop['seller_account_id'];
    $fee_row = findQuery(" SELECT value FROM settings WHERE key_name='platform_fee' ");
    $fee = (float)($fee_row['value'] ?? 5);
    $net = $price * ((100 - $fee) / 100);
    $mint_num = $drop['minted_count'] + 1;
    $mock_mint_address = 'MINT_' . strtoupper(uniqid()) . '_' . $mint_num;
    execute(" UPDATE nft_drops SET minted_count=$mint_num WHERE id=$drop_id ");
    execute(" INSERT INTO nft_mints (drop_id,buyer_account_id,mint_address,tx_signature,mint_price) VALUES ($drop_id,$uid,'$mock_mint_address','$txSig',$price) ");
    execute(" INSERT INTO transactions (account_id,type,amount,tx_signature,reference_id,status,created_at) VALUES ($uid,'purchase',-$price,'$txSig',$drop_id,'confirmed',NOW()) ");
    execute(" INSERT INTO transactions (account_id,type,amount,reference_id,status,created_at) VALUES ($seller_id,'reward',$net,$drop_id,'confirmed',NOW()) ");
    if (function_exists('updateQuestProgress')) {
        updateQuestProgress($uid, 'buy', $price);
    }
    execute(" COMMIT ");
    encode(['status' => true, 'message' => "Minted successfully! You received #$mint_num"]);
} catch (Exception $e) {
    execute(" ROLLBACK ");
    encode(['status' => false, 'message' => $e->getMessage()]);
}
