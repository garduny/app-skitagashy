<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else exit;
$round = findQuery(" SELECT * FROM lottery_rounds WHERE status='open' ORDER BY id DESC LIMIT 1 ");
if (!$round) {
    $nextDraw = date('Y-m-d H:i:s', strtotime('+7 days'));
    execute(" INSERT INTO lottery_rounds (round_number,prize_pool,draw_time,status) VALUES (1,0,'$nextDraw','open') ");
    $round = findQuery(" SELECT * FROM lottery_rounds WHERE status='open' ORDER BY id DESC LIMIT 1 ");
}
$rid = (int)$round['id'];
$entriesRow = findQuery(" SELECT COALESCE(SUM(ticket_count),0) as total FROM lottery_entries WHERE round_id=$rid ");
$totalTickets = (int)($entriesRow['total'] ?? 0);
$token = request('token') ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? '');
$token = str_replace('Bearer ', '', $token);
$accountEntries = 0;
if ($token) {
    $session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
    if ($session) {
        $uid = (int)$session['account_id'];
        $ue = findQuery(" SELECT COALESCE(SUM(ticket_count),0) as total FROM lottery_entries WHERE round_id=$rid AND account_id=$uid ");
        $accountEntries = (int)($ue['total'] ?? 0);
    }
}
$lastRound = findQuery(" SELECT * FROM lottery_rounds WHERE status='closed' ORDER BY id DESC LIMIT 1 ");
$lastWinners = [];
if ($lastRound) {
    $lrid = (int)$lastRound['id'];
    $lastWinners = getQuery("
SELECT u.accountname,u.wallet_address,le.ticket_count
FROM lottery_entries le
JOIN accounts u ON le.account_id=u.id
WHERE le.round_id=$lrid AND le.is_winner='yes'
");
    if (!$lastWinners || count($lastWinners) === 0) {
        $lastWinners = getQuery("
SELECT u.accountname,u.wallet_address,le.ticket_count
FROM lottery_entries le
JOIN accounts u ON le.account_id=u.id
WHERE le.round_id=$lrid
ORDER BY le.ticket_count DESC
LIMIT 3
");
    }
}
encode([
    'status' => true,
    'round' => [
        'time' => date('Y-m-d H:i:s'),
        'id' => $round['id'],
        'number' => $round['round_number'],
        'prize_pool' => $round['prize_pool'],
        'draw_time' => $round['draw_time'],
        'time_left' => max(0, strtotime($round['draw_time']) - time()),
        'total_tickets' => $totalTickets
    ],
    'account_entries' => $accountEntries,
    'last_winners' => $lastWinners
]);
