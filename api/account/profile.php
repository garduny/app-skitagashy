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
$account = findQuery(" SELECT id,wallet_address,accountname,email,role,tier,is_verified,created_at FROM accounts WHERE id=$uid ");
if (!$account) {
    encode(['status' => false, 'message' => 'Account not found']);
}
$stats = findQuery(" SELECT COUNT(id) as total_orders,SUM(total_gashy) as total_spent FROM orders WHERE account_id=$uid AND status='completed' ");
$burns = findQuery(" SELECT SUM(amount) as total_burned FROM burn_log WHERE account_id=$uid ");
$account['stats'] = [
    'orders' => $stats['total_orders'] ?? 0,
    'spent' => $stats['total_spent'] ?? 0,
    'burned' => $burns['total_burned'] ?? 0
];
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
$currentHeld = 0;
$progress = ($target > 0) ? ($currentHeld / $target) * 100 : 100;
$account['tier_progress'] = ['current' => $currentHeld, 'next' => $nextTier, 'target' => $target, 'percent' => min(100, $progress)];
try {
    $quest = findQuery(" 
        SELECT q.title, q.target_count, IFNULL(uq.progress, 0) as progress 
        FROM quests q 
        LEFT JOIN account_quests uq ON q.id = uq.quest_id AND uq.account_id = $uid 
        WHERE q.reset_period = 'weekly' AND q.is_active = 1
        LIMIT 1 
    ");
} catch (Exception $e) {
    $quest = null;
}
if (!$quest) {
    $quest = ['title' => 'Burn 500 $GASHY', 'progress' => 0, 'target_count' => 500];
}
$account['active_quest'] = $quest;
encode(['status' => true, 'data' => $account]);
