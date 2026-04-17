<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) encode(['status' => false, 'message' => 'Unauthorized']);
$uid = (int)$session['account_id'];
$qid = (int)request('quest_id');
if ($qid <= 0) encode(['status' => false, 'message' => 'Invalid Quest']);
execute(" START TRANSACTION ");
try {
    $q = findQuery(" SELECT q.id,q.title,q.target_count,q.reward_gashy,q.is_active,IFNULL(aq.progress,0) progress,IFNULL(aq.is_claimed,0) is_claimed FROM quests q LEFT JOIN account_quests aq ON aq.quest_id=q.id AND aq.account_id=$uid WHERE q.id=$qid LIMIT 1 ");
    if (!$q) throw new Exception('Quest not found');
    if ((int)$q['is_active'] !== 1) throw new Exception('Quest unavailable');
    if ((float)$q['progress'] < (float)$q['target_count']) throw new Exception('Quest not finished yet');
    if ((int)$q['is_claimed'] === 1) throw new Exception('Reward already claimed');
    $exists = findQuery(" SELECT id FROM account_quests WHERE account_id=$uid AND quest_id=$qid ");
    if ($exists) {
        execute(" UPDATE account_quests SET is_claimed=1,updated_at=NOW() WHERE account_id=$uid AND quest_id=$qid ");
    } else {
        execute(" INSERT INTO account_quests(account_id,quest_id,progress,is_claimed,updated_at) VALUES($uid,$qid,{$q['target_count']},1,NOW()) ");
    }
    execute(" INSERT INTO transactions(account_id,type,amount,reference_id,status,created_at) VALUES($uid,'reward',{$q['reward_gashy']},$qid,'confirmed',NOW()) ");
    execute(" COMMIT ");
    encode([
        'status' => true,
        'message' => 'Reward Claimed! +' . number_format((float)$q['reward_gashy'], 2) . ' GASHY',
        'data' => [
            'quest_id' => $qid,
            'title' => $q['title'],
            'amount' => number_format((float)$q['reward_gashy'], 2, '.', '')
        ]
    ]);
} catch (Exception $e) {
    execute(" ROLLBACK ");
    encode(['status' => false, 'message' => $e->getMessage()]);
}
