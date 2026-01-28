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
$aid = request('auction_id');
$amount = request('amount');
if (!$aid || !$amount) {
    encode(['status' => false, 'message' => 'Invalid input']);
}
$auc = findQuery(" SELECT * FROM auctions WHERE id=$aid ");
if (!$auc) {
    encode(['status' => false, 'message' => 'Auction not found']);
}
if ($auc['status'] !== 'active') {
    encode(['status' => false, 'message' => 'Auction is not active']);
}
if (strtotime($auc['end_time']) < time()) {
    encode(['status' => false, 'message' => 'Auction has ended']);
}
if ($amount <= $auc['current_bid']) {
    encode(['status' => false, 'message' => 'Bid must be higher than current price']);
}
if ($amount <= $auc['start_price']) {
    encode(['status' => false, 'message' => 'Bid must be higher than start price']);
}
execute(" START TRANSACTION ");
try {
    execute(" UPDATE auctions SET current_bid=$amount,highest_bidder_id=$uid WHERE id=$aid ");
    execute(" INSERT INTO transactions (account_id,type,amount,reference_id,status,created_at) VALUES ($uid,'auction_bid',$amount,$aid,'pending',NOW()) ");
    execute(" COMMIT ");
    encode(['status' => true, 'new_bid' => $amount]);
} catch (Exception $e) {
    execute(" ROLLBACK ");
    encode(['status' => false, 'message' => 'Bid failed']);
}
