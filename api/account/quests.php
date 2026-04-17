<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;

$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) encode(['status' => false, 'message' => 'Unauthorized']);

$uid = (int)$session['account_id'];

$quests = getQuery(" SELECT q.id,q.title,q.action_type,q.target_count,q.reward_gashy,q.reset_period,q.is_active,IFNULL(aq.progress,0) progress,IFNULL(aq.is_claimed,0) is_claimed,aq.updated_at FROM quests q LEFT JOIN account_quests aq ON aq.quest_id=q.id AND aq.account_id=$uid WHERE q.is_active=1 ORDER BY q.id DESC ");

$data = [];
$totalEarn = 0;

foreach ($quests as $q) {
    $progress = (float)$q['progress'];
    $target = max((float)$q['target_count'], 1);
    $claimed = (int)$q['is_claimed'];
    $percent = min(100, round(($progress / $target) * 100, 2));

    $status = 'active';
    if ($claimed === 1) $status = 'claimed';
    elseif ($progress >= $target) $status = 'claimable';

    if ($claimed === 1) $totalEarn += (float)$q['reward_gashy'];

    $data[] = [
        'id' => (int)$q['id'],
        'title' => $q['title'],
        'action_type' => $q['action_type'],
        'target_count' => $q['target_count'],
        'reward_gashy' => number_format((float)$q['reward_gashy'], 2, '.', ''),
        'reset_period' => $q['reset_period'],
        'progress' => $progress,
        'is_claimed' => $claimed,
        'percent' => $percent,
        'status' => $status,
        'updated_at' => $q['updated_at']
    ];
}

encode([
    'status' => true,
    'data' => $data,
    'meta' => [
        'total' => count($data),
        'claimed_rewards' => number_format($totalEarn, 2, '.', '')
    ]
]);
