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
$burnTx = $data['burn_tx'] ?? '';
$tickets = (int)($data['tickets'] ?? 0);
if (!$burnTx || $tickets < 1) {
    encode(['status' => false, 'message' => 'Invalid input']);
}
$round = findQuery(" SELECT id,status FROM lottery_rounds WHERE status='open' ORDER BY id DESC LIMIT 1 ");
if (!$round) {
    encode(['status' => false, 'message' => 'No active lottery round']);
}
$used = findQuery(" SELECT id FROM lottery_entries WHERE burn_tx='$burnTx' ");
if ($used) {
    encode(['status' => false, 'message' => 'Transaction signature already used']);
}
$rid = $round['id'];
$cost = $tickets * 10;
execute(" START TRANSACTION ");
try {
    execute(" INSERT INTO lottery_entries (round_id,account_id,burn_tx,ticket_count) VALUES ($rid,$uid,'$burnTx',$tickets) ");
    execute(" INSERT INTO burn_log (account_id,amount,purpose,tx_signature,created_at) VALUES ($uid,$cost,'lottery_entry','$burnTx',NOW()) ");
    execute(" UPDATE lottery_rounds SET prize_pool=prize_pool+$cost WHERE id=$rid ");
    execute(" INSERT INTO transactions (account_id,type,amount,tx_signature,reference_id,status,created_at) VALUES ($uid,'lottery_ticket',-$cost,'$burnTx',$rid,'confirmed',NOW()) ");
    if (function_exists('updateQuestProgress')) {
        updateQuestProgress($uid, 'burn', $cost);
    }
    execute(" COMMIT ");
    encode(['status' => true, 'message' => 'Tickets purchased successfully', 'round_id' => $rid, 'tickets' => $tickets]);
} catch (Exception $e) {
    execute(" ROLLBACK ");
    encode(['status' => false, 'message' => $e->getMessage()]);
}
