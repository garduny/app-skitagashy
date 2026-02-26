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
$aid = $session['account_id'];
$account = findQuery(" SELECT id,wallet_address,accountname,email,role,tier,is_verified,created_at FROM accounts WHERE id=$aid ");
if (!$account) {
    encode(['status' => false, 'message' => 'User not found']);
}
$stats = findQuery(" SELECT COUNT(id) as total_orders,SUM(total_gashy) as total_spent FROM orders WHERE account_id=$aid AND status!='failed' ");
$burns = findQuery(" SELECT SUM(amount) as total_burned FROM burn_log WHERE account_id=$aid ");
$account['stats'] = [
    'orders' => $stats['total_orders'] ?? 0,
    'spent' => $stats['total_spent'] ?? 0,
    'burned' => $burns['total_burned'] ?? 0
];
$bal_query = findQuery(" SELECT SUM(amount) as balance FROM transactions WHERE account_id=$aid AND status='confirmed' ");
$currentHeld = (float)($bal_query['balance'] ?? 0);
$nextTier = 'silver';
$progress = 0;
if ($account['tier'] === 'bronze') {
    $nextTier = 'silver';
    $target = 1000;
} elseif ($account['tier'] === 'silver') {
    $nextTier = 'gold';
    $target = 5000;
} elseif ($account['tier'] === 'gold') {
    $nextTier = 'platinum';
    $target = 25000;
} elseif ($account['tier'] === 'platinum') {
    $nextTier = 'diamond';
    $target = 100000;
} else {
    $nextTier = 'max';
    $target = 100000;
}
$progress = ($target > 0) ? ($currentHeld / $target) * 100 : 100;
$account['tier_progress'] = [
    'current' => $currentHeld,
    'next' => $nextTier,
    'target' => $target,
    'percent' => min(100, $progress)
];
try {
    $quest = findQuery(" SELECT 
q.id,
q.title,
q.target_count,
IFNULL(aq.progress,0) progress
FROM quests q
LEFT JOIN account_quests aq 
ON aq.quest_id=q.id 
AND aq.account_id=$aid
WHERE 
q.is_active=1
AND (q.end_date IS NULL OR q.end_date>NOW())
AND (
aq.id IS NULL 
OR aq.is_claimed=0
)
ORDER BY q.id DESC
LIMIT 1
");
} catch (Exception $e) {
    $quest = null;
}
if (!$quest) {
    $quest = ['title' => 'wait for active quests', 'progress' => 0, 'target_count' => 0];
}
$account['active_quest'] = $quest;
encode(['status' => true, 'data' => $account]);
