<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
$round = findQuery(" SELECT * FROM lottery_rounds WHERE status='open' ORDER BY id DESC LIMIT 1 ");
if (!$round) {
    $nextDraw = date('Y-m-d H:i:s', strtotime('+7 days'));
    execute(" INSERT INTO lottery_rounds (round_number,prize_pool,draw_time,status) VALUES (1,0,'$nextDraw','open') ");
    $round = findQuery(" SELECT * FROM lottery_rounds WHERE status='open' ORDER BY id DESC LIMIT 1 ");
}
$rid = $round['id'];
$entries = countQuery(" SELECT SUM(ticket_count) FROM lottery_entries WHERE round_id=$rid ");
$lastRound = findQuery(" SELECT * FROM lottery_rounds WHERE status='closed' ORDER BY id DESC LIMIT 1 ");
$lastWinners = [];
if ($lastRound) {
    $lrid = $lastRound['id'];
    $lastWinners = getQuery(" SELECT u.username,u.wallet_address,le.ticket_count FROM lottery_entries le JOIN users u ON le.user_id=u.id WHERE le.round_id=$lrid AND le.is_winner=1 ");
}
$userEntries = 0;
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT user_id FROM sessions WHERE token='$token' AND expires_at>NOW() ");
if ($session) {
    $uid = $session['user_id'];
    $ue = findQuery(" SELECT SUM(ticket_count) as total FROM lottery_entries WHERE round_id=$rid AND user_id=$uid ");
    $userEntries = $ue['total'] ?? 0;
}
encode([
    'status' => true,
    'round' => [
        'id' => $round['id'],
        'number' => $round['round_number'],
        'prize_pool' => $round['prize_pool'],
        'draw_time' => $round['draw_time'],
        'time_left' => strtotime($round['draw_time']) - time(),
        'total_tickets' => $entries
    ],
    'user_entries' => $userEntries,
    'last_winners' => $lastWinners
]);
