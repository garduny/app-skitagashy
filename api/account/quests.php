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
$quests = getQuery(" SELECT q.id,q.title,q.action_type,q.target_count,q.reward_gashy,q.reset_period,IFNULL(aq.progress,0) as progress,IFNULL(aq.is_claimed,0) as is_claimed FROM quests q LEFT JOIN account_quests aq ON q.id=aq.quest_id AND aq.account_id=$uid WHERE q.is_active=1 ORDER BY q.id DESC ");
$data = [];
foreach ($quests as $q) {
    $status = 'active';
    if ($q['is_claimed'] == 1) {
        $status = 'completed';
    } elseif ($q['progress'] >= $q['target_count']) {
        $status = 'claimable';
    }
    $q['status'] = $status;
    $data[] = $q;
}
encode(['status' => true, 'data' => $data]);
