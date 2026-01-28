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
$burnTx = request('burn_tx');
$tickets = (int)request('tickets');
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
execute(" START TRANSACTION ");
try {
    execute(" INSERT INTO lottery_entries (round_id,account_id,burn_tx,ticket_count) VALUES ($rid,$uid,'$burnTx',$tickets) ");
    execute(" INSERT INTO burn_log (account_id,amount,purpose,tx_signature,created_at) VALUES ($uid,0,'lottery_entry','$burnTx',NOW()) ");
    execute(" UPDATE lottery_rounds SET prize_pool=prize_pool+($tickets*10) WHERE id=$rid ");
    updateQuestProgress($uid, 'burn', $tickets * 10);
    execute(" COMMIT ");
    encode(['status' => true, 'message' => 'Tickets purchased successfully', 'round_id' => $rid, 'tickets' => $tickets]);
} catch (Exception $e) {
    execute(" ROLLBACK ");
    encode(['status' => false, 'message' => $e->getMessage()]);
}
