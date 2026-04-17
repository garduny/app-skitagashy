<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) encode(['status' => false, 'message' => 'Unauthorized']);
$aid = (int)$session['account_id'];
$account = findQuery(" SELECT id,wallet_address,accountname,email,role,tier,is_verified,created_at FROM accounts WHERE id=$aid LIMIT 1 ");
if (!$account) encode(['status' => false, 'message' => 'User not found']);
$stats = findQuery(" SELECT COUNT(id) total_orders,COALESCE(SUM(total_gashy),0) total_spent FROM orders WHERE account_id=$aid AND LOWER(status)!='failed' ");
$burns = findQuery(" SELECT COALESCE(SUM(amount),0) total_burned FROM burn_log WHERE account_id=$aid ");
$account['stats'] = [
    'orders' => (int)($stats['total_orders'] ?? 0),
    'spent' => number_format((float)($stats['total_spent'] ?? 0), 3, '.', ''),
    'burned' => number_format((float)($burns['total_burned'] ?? 0), 3, '.', '')
];
$reward_balance = (float)(findQuery(" SELECT COALESCE(SUM(amount),0) t FROM transactions WHERE account_id=$aid AND status='confirmed' AND type IN ('reward','referral','bonus','mystery_reward') ")['t'] ?? 0);
$failedbid = (float)(findQuery(" SELECT COALESCE(SUM(amount),0) t FROM transactions WHERE account_id=$aid AND status='failed' AND type='auction_bid' ")['t'] ?? 0);
$isSeller = (bool)findQuery(" SELECT account_id FROM sellers WHERE account_id=$aid AND is_approved=1 LIMIT 1 ");
$seller_net = 0;
if ($isSeller) {
    $fee_percent = (float)(findQuery(" SELECT value FROM settings WHERE key_name='platform_fee' LIMIT 1 ")['value'] ?? 5);
    $seller_net = (float)(findQuery(" SELECT COALESCE(SUM(oi.price_at_purchase*oi.quantity),0) t FROM order_items oi JOIN products p ON oi.product_id=p.id JOIN orders o ON oi.order_id=o.id WHERE p.seller_id=$aid AND LOWER(o.status)='completed' ")['t'] ?? 0);
    $seller_net = $seller_net * ((100 - $fee_percent) / 100);
}
$withdrawn = (float)(findQuery(" SELECT COALESCE(SUM(amount),0) t FROM withdrawals WHERE account_id=$aid AND LOWER(status)='approved' ")['t'] ?? 0);
$total_pool = $reward_balance + $seller_net + $failedbid;
$withdrawable = max($total_pool - $withdrawn, 0);
$account['wallet_stats'] = [
    'reward_balance' => number_format($reward_balance, 3, '.', ''),
    'seller_earnings' => number_format($seller_net, 3, '.', ''),
    'failed_bid_return' => number_format($failedbid, 3, '.', ''),
    'withdrawn' => number_format($withdrawn, 3, '.', ''),
    'withdrawable' => number_format($withdrawable, 3, '.', '')
];
$tier = strtolower($account['tier'] ?: 'bronze');
$targets = [
    'bronze' => ['next' => 'silver', 'target' => 1000],
    'silver' => ['next' => 'gold', 'target' => 5000],
    'gold' => ['next' => 'platinum', 'target' => 25000],
    'platinum' => ['next' => 'diamond', 'target' => 100000],
    'diamond' => ['next' => 'max', 'target' => 100000]
];
$row = $targets[$tier] ?? $targets['bronze'];
$currentHeld = $reward_balance + $seller_net;
$percent = $row['target'] > 0 ? min(100, ($currentHeld / $row['target']) * 100) : 100;
$account['tier_progress'] = [
    'current' => number_format($currentHeld, 3, '.', ''),
    'next' => $row['next'],
    'target' => $row['target'],
    'percent' => round($percent, 2)
];
try {
    $quest = findQuery(" SELECT q.id,q.title,q.target_count,IFNULL(aq.progress,0) progress FROM quests q LEFT JOIN account_quests aq ON aq.quest_id=q.id AND aq.account_id=$aid WHERE q.is_active=1 AND (q.end_date IS NULL OR q.end_date>NOW()) AND (aq.id IS NULL OR aq.is_claimed=0) ORDER BY q.id DESC LIMIT 1 ");
} catch (Exception $e) {
    $quest = null;
}
if (!$quest) $quest = ['title' => 'wait for active quests', 'progress' => 0, 'target_count' => 0];
$account['active_quest'] = $quest;
encode(['status' => true, 'data' => $account]);
