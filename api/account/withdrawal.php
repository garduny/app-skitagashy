<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) encode(['status' => false, 'message' => 'Unauthorized']);
$aid = (int)$session['account_id'];
$amount = (float)request('amount');
$min = (float)(findQuery(" SELECT value FROM settings WHERE key_name='withdraw_minimum' LIMIT 1 ")['value'] ?? 1);
if ($amount <= 0) encode(['status' => false, 'message' => 'Invalid amount']);
if ($amount < $min) encode(['status' => false, 'message' => 'Minimum withdrawal is ' . number_format($min, 3)]);
$pending = findQuery(" SELECT id FROM withdrawals WHERE account_id=$aid AND LOWER(status)='pending' LIMIT 1 ");
if ($pending) encode(['status' => false, 'message' => 'You already have a pending withdrawal']);
$reward_balance = (float)(findQuery(" SELECT COALESCE(SUM(amount),0) t FROM transactions WHERE account_id=$aid AND status='confirmed' AND type IN ('reward','referral','bonus','mystery_reward') ")['t'] ?? 0);
$failedbid = (float)(findQuery(" SELECT COALESCE(SUM(amount),0) t FROM transactions WHERE account_id=$aid AND status='failed' AND type='auction_bid' ")['t'] ?? 0);
$isSeller = (bool)findQuery(" SELECT account_id FROM sellers WHERE account_id=$aid AND is_approved=1 LIMIT 1 ");
$seller_net = 0;
if ($isSeller) {
    $fee_percent = (float)(findQuery(" SELECT value FROM settings WHERE key_name='platform_fee' LIMIT 1 ")['value'] ?? 5);
    $seller_net = (float)(findQuery(" SELECT COALESCE(SUM(oi.price_at_purchase*oi.quantity),0) t FROM order_items oi JOIN products p ON oi.product_id=p.id JOIN orders o ON oi.order_id=o.id WHERE p.seller_id=$aid AND LOWER(o.status)='completed' ")['t'] ?? 0);
    $seller_net = $seller_net * ((100 - $fee_percent) / 100);
}
$withdrawn = (float)(findQuery(" SELECT COALESCE(SUM(amount),0) t FROM withdrawals WHERE account_id=$aid AND LOWER(status) IN ('approved','paid') ")['t'] ?? 0);
$total_pool = $reward_balance + $failedbid + $seller_net;
$withdrawable = max($total_pool - $withdrawn, 0);
if ($amount > $withdrawable) encode(['status' => false, 'message' => 'Insufficient funds. Available: ' . number_format($withdrawable, 3)]);
execute(" INSERT INTO withdrawals(account_id,amount,status,created_at) VALUES ($aid,$amount,'pending',NOW()) ");
$newAvailable = max($withdrawable - $amount, 0);
encode([
    'status' => true,
    'message' => 'Withdrawal requested successfully',
    'available' => number_format($newAvailable, 3, '.', '')
]);
