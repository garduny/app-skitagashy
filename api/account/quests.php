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
$quests = getQuery(" SELECT q.id,q.title,q.action_type,q.target_count,q.reward_gashy,q.reset_period,IFNULL(uq.progress,0) as progress,IFNULL(uq.is_claimed,0) as is_claimed FROM quests q LEFT JOIN account_quests uq ON q.id=uq.quest_id AND uq.account_id=$uid WHERE q.reset_period='daily' ");
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
