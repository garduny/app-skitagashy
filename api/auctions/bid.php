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
$aid = (int)($data['auction_id'] ?? 0);
$amount = (float)($data['amount'] ?? 0);
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
$end_time = strtotime($auc['end_time']);
if ($end_time < time()) {
    encode(['status' => false, 'message' => 'Auction has ended']);
}
$min_inc = max(1, $auc['current_bid'] * 0.05);
$min_bid = $auc['current_bid'] + $min_inc;
if ($amount < $min_bid) {
    encode(['status' => false, 'message' => 'Bid too low. Minimum bid is ' . number_format($min_bid, 2)]);
}
$extend_sql = "";
if (($end_time - time()) < 300) {
    $new_end = date('Y-m-d H:i:s', $end_time + 300);
    $extend_sql = ", end_time='$new_end'";
}
$txSig = 'BID_' . time() . '_' . $uid;
execute(" START TRANSACTION ");
try {
    execute(" UPDATE auctions SET current_bid=$amount, highest_bidder_id=$uid $extend_sql WHERE id=$aid ");
    execute(" INSERT INTO transactions (account_id,type,amount,tx_signature,reference_id,status,created_at) VALUES ($uid,'auction_bid',$amount,'$txSig',$aid,'pending',NOW()) ");
    execute(" COMMIT ");
    encode(['status' => true, 'new_bid' => $amount, 'message' => 'Bid placed successfully' . ($extend_sql ? ' (Time Extended)' : '')]);
} catch (Exception $e) {
    execute(" ROLLBACK ");
    encode(['status' => false, 'message' => 'Bid failed']);
}
