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
$mint = secure($data['nft_mint'] ?? '');
$txSig = secure($data['tx_signature'] ?? '');
if (!$mint || !$txSig) {
    encode(['status' => false, 'message' => 'Invalid request']);
}
$dup = findQuery(" SELECT id FROM nft_burns WHERE tx_signature='$txSig' ");
if ($dup) {
    encode(['status' => false, 'message' => 'Transaction used']);
}
execute(" START TRANSACTION ");
try {
    $nft = findQuery(" SELECT m.id,m.is_burned,d.price_gashy FROM nft_mints m JOIN nft_drops d ON m.drop_id=d.id WHERE m.mint_address='$mint' AND m.buyer_account_id=$uid FOR UPDATE ");
    if (!$nft) {
        throw new Exception("NFT not found or not owned by you");
    }
    if ($nft['is_burned']) {
        throw new Exception("NFT already burned");
    }
    execute(" UPDATE nft_mints SET is_burned=1 WHERE mint_address='$mint' ");
    execute(" INSERT INTO nft_burns (mint_address,owner_account_id,tx_signature,created_at) VALUES ('$mint',$uid,'$txSig',NOW()) ");
    // Example: Burning returns 50% of original mint price to user
    $refund = $nft['price_gashy'] * 0.5;
    execute(" INSERT INTO transactions (account_id,type,amount,tx_signature,status,created_at) VALUES ($uid,'reward',$refund,'$txSig','confirmed',NOW()) ");
    execute(" COMMIT ");
    encode(['status' => true, 'message' => "Burn successful. $refund GASHY reclaimed."]);
} catch (Exception $e) {
    execute(" ROLLBACK ");
    encode(['status' => false, 'message' => $e->getMessage()]);
}
