<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) encode(['status' => false, 'message' => 'Unauthorized']);
$uid = $session['account_id'];
$amount = (float)request('amount');
if ($amount <= 0) encode(['status' => false, 'message' => 'Invalid amount']);
$isSeller = (bool)findQuery(" SELECT account_id FROM sellers WHERE account_id=$uid AND is_approved=1 ");
$reward_balance = findQuery(" SELECT COALESCE(SUM(amount),0) t
FROM transactions
WHERE account_id=$uid
AND status='confirmed'
AND type IN ('reward','referral','bonus','mystery_reward') ")['t'] ?? 0;
$seller_net = 0;
if ($isSeller) {
    $fee_row = findQuery(" SELECT value FROM settings WHERE key_name='platform_fee' ");
    $fee_percent = (float)($fee_row['value'] ?? 5);
    $gross = findQuery(" SELECT COALESCE(SUM(oi.price_at_purchase*oi.quantity),0) t
    FROM order_items oi
    JOIN products p ON oi.product_id=p.id
    JOIN orders o ON oi.order_id=o.id
    WHERE p.seller_id=$uid AND o.status='completed'
    ")['t'] ?? 0;
    $seller_net = $gross * ((100 - $fee_percent) / 100);
}
$withdrawn = findQuery(" SELECT COALESCE(SUM(amount),0) t
FROM withdrawals
WHERE account_id=$uid
AND LOWER(status)='approved'
")['t'] ?? 0;
$total_pool = $reward_balance + $seller_net;
$withdrawable = max($total_pool - $withdrawn, 0);
if ($amount > $withdrawable) {
    encode([
        'status' => false,
        'message' => 'Insufficient funds. Available: ' . number_format($withdrawable, 3)
    ]);
}
execute(" INSERT INTO withdrawals (account_id,amount,status,created_at)
VALUES ($uid,$amount,'pending',NOW())
");
$account = findQuery(" SELECT email,accountname FROM accounts WHERE id=$uid ");
if ($account && function_exists('mailer')) {
    $adminEmail = findQuery(" SELECT value FROM settings WHERE key_name='email' ")['value'] ?? '';
    $body = "<h1>Withdrawal Request</h1>
    <p>User: {$account['accountname']}</p>
    <p>Amount: {$amount} GASHY</p>
    <p>Available Before: " . number_format($withdrawable, 3) . "</p>";
    mailer("New Withdrawal Request", $body, "Gashy Admin - Payout", $adminEmail);
}
encode([
    'status' => true,
    'message' => 'Withdrawal requested successfully',
    'available' => round($withdrawable - $amount, 3)
]);
