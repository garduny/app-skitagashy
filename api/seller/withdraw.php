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
$seller = findQuery(" SELECT * FROM sellers WHERE account_id=$uid AND is_approved=1 ");
if (!$seller) {
    encode(['status' => false, 'message' => 'Seller profile not active']);
}
$amount = (float)request('amount');
if ($amount <= 0) {
    encode(['status' => false, 'message' => 'Invalid amount']);
}
$fee_row = findQuery(" SELECT value FROM settings WHERE key_name='platform_fee' ");
$fee_percent = floatval($fee_row['value'] ?? 5);
$gross = findQuery(" SELECT SUM(oi.price_at_purchase * oi.quantity) as t FROM order_items oi JOIN products p ON oi.product_id=p.id JOIN orders o ON oi.order_id=o.id WHERE p.seller_id=$uid AND o.status='completed' ")['t'] ?? 0;
$net_total = $gross * ((100 - $fee_percent) / 100);
$withdrawn = findQuery(" SELECT SUM(amount) as t FROM withdrawals WHERE account_id=$uid AND status!='rejected' ")['t'] ?? 0;
$available = $net_total - $withdrawn;
if ($amount > $available) {
    encode(['status' => false, 'message' => 'Insufficient funds. Net Available: ' . number_format($available, 2)]);
}
execute(" INSERT INTO withdrawals (account_id,amount,status,created_at) VALUES ($uid,$amount,'pending',NOW()) ");
$account = findQuery(" SELECT email,accountname FROM accounts WHERE id=$uid ");
if ($account['email']) {
    $body = "<h1>Withdrawal Request</h1><p>User: {$account['accountname']}</p><p>Amount: $amount GASHY</p><p>Net Balance After: " . ($available - $amount) . "</p>";
    if (function_exists('mailer')) {
        mailer('New Withdrawal Request', $body, 'System', 'darinkrd2020@gmail.com');
    }
}
encode(['status' => true, 'message' => 'Withdrawal requested successfully']);
