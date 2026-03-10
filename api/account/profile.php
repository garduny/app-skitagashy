<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) encode(['status' => false, 'message' => 'Unauthorized']);
$aid = $session['account_id'];
$account = findQuery(" SELECT id,wallet_address,accountname,email,role,tier,is_verified,created_at FROM accounts WHERE id=$aid ");
if (!$account) encode(['status' => false, 'message' => 'User not found']);
$stats = findQuery(" SELECT COUNT(id) total_orders,SUM(total_gashy) total_spent FROM orders WHERE account_id=$aid AND status!='failed' ");
$burns = findQuery(" SELECT SUM(amount) total_burned FROM burn_log WHERE account_id=$aid ");
$account['stats'] = ['orders' => $stats['total_orders'] ?? 0, 'spent' => $stats['total_spent'] ?? 0, 'burned' => $burns['total_burned'] ?? 0];
$reward_balance = findQuery(" SELECT COALESCE(SUM(amount),0) t FROM transactions WHERE account_id=$aid AND status='confirmed' AND type IN ('reward','referral','bonus','mystery_reward') ")['t'] ?? 0;
$failedbid = findQuery(" SELECT COALESCE(SUM(amount),0) t FROM transactions WHERE account_id=$aid AND status='failed' AND type='auction_bid' ")['t'] ?? 0;
$isSeller = (bool)findQuery(" SELECT account_id FROM sellers WHERE account_id=$aid AND is_approved=1 ");
$seller_net = 0;
if ($isSeller) {
    $fee_row = findQuery(" SELECT value FROM settings WHERE key_name='platform_fee' ");
    $fee_percent = (float)($fee_row['value'] ?? 5);
    $rate = (float)toGashy();
    $gross_usd = findQuery(" SELECT COALESCE(SUM(oi.price_at_purchase*oi.quantity),0) t FROM order_items oi JOIN products p ON oi.product_id=p.id JOIN orders o ON oi.order_id=o.id WHERE p.seller_id=$aid AND o.status='completed' ")['t'] ?? 0;
    $gross = $rate > 0 ? ($gross_usd / $rate) : 0;
    $seller_net = $gross * ((100 - $fee_percent) / 100);
}
$withdrawn = findQuery(" SELECT COALESCE(SUM(amount),0) t FROM withdrawals WHERE account_id=$aid AND LOWER(status)='approved' ")['t'] ?? 0;
$total_pool = $reward_balance + $seller_net + $failedbid;
$withdrawable = max($total_pool - $withdrawn, 0);
$account['wallet_stats'] = ['reward_balance' => number_format($reward_balance, 3, '.', ''), 'seller_earnings' => number_format($seller_net, 3, '.', ''), 'failed_bid_return' => number_format($failedbid, 3, '.', ''), 'withdrawn' => number_format($withdrawn, 3, '.', ''), 'withdrawable' => number_format($withdrawable, 3, '.', '')];
$currentHeld = $reward_balance;
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
$account['tier_progress'] = ['current' => $currentHeld, 'next' => $nextTier, 'target' => $target, 'percent' => min(100, $progress)];
try {
    $quest = findQuery(" SELECT q.id,q.title,q.target_count,IFNULL(aq.progress,0) progress FROM quests q LEFT JOIN account_quests aq ON aq.quest_id=q.id AND aq.account_id=$aid WHERE q.is_active=1 AND (q.end_date IS NULL OR q.end_date>NOW()) AND (aq.id IS NULL OR aq.is_claimed=0) ORDER BY q.id DESC LIMIT 1 ");
} catch (Exception $e) {
    $quest = null;
}
if (!$quest) $quest = ['title' => 'wait for active quests', 'progress' => 0, 'target_count' => 0];
$account['active_quest'] = $quest;
encode(['status' => true, 'data' => $account]);
