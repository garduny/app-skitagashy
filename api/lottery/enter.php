<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
execute(" SET time_zone = '+03:00' ");
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$token = trim((string)$token);
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) encode(['status' => false, 'message' => 'Unauthorized']);
$uid = (int)$session['account_id'];
$json = file_get_contents('php://input');
$data = json_decode($json, true);
if (!is_array($data)) $data = [];
$burnTx = trim((string)($data['burn_tx'] ?? request('burn_tx') ?? ''));
$tickets = (int)($data['tickets'] ?? request('tickets') ?? 0);
$postedRoundId = (int)($data['round_id'] ?? request('round_id') ?? 0);
if ($burnTx === '' || $tickets < 1) encode(['status' => false, 'message' => 'Invalid input']);
execute(" START TRANSACTION ");
try {
    $round = findQuery(" SELECT id,round_number,draw_time,status,prize_pool FROM lottery_rounds WHERE status='open' ORDER BY id DESC LIMIT 1 FOR UPDATE ");
    if (!$round) {
        execute(" ROLLBACK ");
        encode(['status' => false, 'message' => 'No active lottery round']);
    }
    $rid = (int)$round['id'];
    if ($postedRoundId > 0 && $postedRoundId !== $rid) {
        execute(" ROLLBACK ");
        encode(['status' => false, 'message' => 'Lottery round changed, refresh page']);
    }
    $timeLeft = strtotime($round['draw_time']) - time();
    if ($timeLeft <= 0) {
        execute(" ROLLBACK ");
        encode(['status' => false, 'message' => 'Lottery just closed, try next round']);
    }
    $used = findQuery(" SELECT id FROM lottery_entries WHERE burn_tx='$burnTx' LIMIT 1 ");
    if ($used) {
        execute(" ROLLBACK ");
        encode(['status' => false, 'message' => 'Transaction signature already used']);
    }
    $pricePerTicket = 10;
    $cost = $tickets * $pricePerTicket;
    execute(" INSERT INTO lottery_entries (round_id,account_id,burn_tx,ticket_count,is_winner) VALUES ($rid,$uid,'$burnTx',$tickets,'no') ");
    execute(" INSERT INTO burn_log (account_id,amount,purpose,tx_signature,created_at) VALUES ($uid,$cost,'lottery_entry','$burnTx',NOW()) ");
    execute(" UPDATE lottery_rounds SET prize_pool=prize_pool+$cost WHERE id=$rid ");
    execute(" INSERT INTO transactions (account_id,type,amount,tx_signature,reference_id,status,created_at) VALUES ($uid,'lottery_ticket',-$cost,'$burnTx',$rid,'confirmed',NOW()) ");
    if (function_exists('updateQuestProgress')) updateQuestProgress($uid, 'burn', $cost);
    $ticketsRow = findQuery(" SELECT COALESCE(SUM(ticket_count),0) as total FROM lottery_entries WHERE round_id=$rid ");
    execute(" COMMIT ");
    encode([
        'status' => true,
        'message' => 'Tickets purchased successfully',
        'round_id' => $rid,
        'tickets' => $tickets,
        'total_tickets' => (int)($ticketsRow['total'] ?? 0)
    ]);
} catch (Throwable $e) {
    execute(" ROLLBACK ");
    encode(['status' => false, 'message' => $e->getMessage()]);
}
