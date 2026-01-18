<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT user_id FROM sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) {
    encode(['status' => false, 'message' => 'Unauthorized']);
}
$uid = $session['user_id'];
$user = findQuery(" SELECT id,wallet_address,username,email,role,tier,is_verified,created_at FROM users WHERE id=$uid ");
if (!$user) {
    encode(['status' => false, 'message' => 'User not found']);
}
$stats = findQuery(" SELECT COUNT(id) as total_orders,SUM(total_gashy) as total_spent FROM orders WHERE user_id=$uid AND status='completed' ");
$burns = findQuery(" SELECT SUM(amount) as total_burned FROM burn_log WHERE user_id=$uid ");
$user['stats'] = [
    'orders' => $stats['total_orders'] ?? 0,
    'spent' => $stats['total_spent'] ?? 0,
    'burned' => $burns['total_burned'] ?? 0
];
$nextTier = 'silver';
$progress = 0;
if ($user['tier'] === 'bronze') {
    $nextTier = 'silver';
    $target = 1000;
} elseif ($user['tier'] === 'silver') {
    $nextTier = 'gold';
    $target = 5000;
} elseif ($user['tier'] === 'gold') {
    $nextTier = 'platinum';
    $target = 25000;
} elseif ($user['tier'] === 'platinum') {
    $nextTier = 'diamond';
    $target = 100000;
} else {
    $nextTier = 'max';
    $target = 100000;
}
$currentHeld = 0;
$progress = ($target > 0) ? ($currentHeld / $target) * 100 : 100;
$user['tier_progress'] = ['current' => $currentHeld, 'next' => $nextTier, 'target' => $target, 'percent' => min(100, $progress)];
try {
    $quest = findQuery(" 
        SELECT q.title, q.target_count, IFNULL(uq.progress, 0) as progress 
        FROM quests q 
        LEFT JOIN user_quests uq ON q.id = uq.quest_id AND uq.user_id = $uid 
        WHERE q.reset_period = 'weekly' AND q.is_active = 1
        LIMIT 1 
    ");
} catch (Exception $e) {
    $quest = null;
}
if (!$quest) {
    $quest = ['title' => 'Burn 500 $GASHY', 'progress' => 0, 'target_count' => 500];
}
$user['active_quest'] = $quest;
encode(['status' => true, 'data' => $user]);
