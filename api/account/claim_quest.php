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
$qid = (int)request('quest_id');
if (!$qid) {
    encode(['status' => false, 'message' => 'Invalid Quest']);
}
execute(" START TRANSACTION ");
try {
    $q = findQuery(" SELECT q.*,aq.progress,aq.is_claimed FROM quests q LEFT JOIN account_quests aq ON q.id=aq.quest_id AND aq.account_id=$uid WHERE q.id=$qid ");
    if (!$q) {
        throw new Exception("Quest not found");
    }
    if ($q['progress'] < $q['target_count']) {
        throw new Exception("Quest not finished yet");
    }
    if ($q['is_claimed']) {
        throw new Exception("Reward already claimed");
    }
    execute(" UPDATE account_quests SET is_claimed=1 WHERE account_id=$uid AND quest_id=$qid ");
    execute(" INSERT INTO transactions (account_id,type,amount,reference_id,status,created_at) VALUES ($uid,'reward',{$q['reward_gashy']},$qid,'confirmed',NOW()) ");
    execute(" COMMIT ");
    encode(['status' => true, 'message' => 'Reward Claimed! +' . number_format($q['reward_gashy']) . ' GASHY']);
} catch (Exception $e) {
    execute(" ROLLBACK ");
    encode(['status' => false, 'message' => $e->getMessage()]);
}
