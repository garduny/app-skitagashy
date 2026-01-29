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
$total_earned = findQuery(" SELECT SUM(price_gashy) as t FROM products p JOIN order_items oi ON p.id=oi.product_id JOIN orders o ON oi.order_id=o.id WHERE p.seller_id=$uid AND o.status='completed' ")['t'] ?? 0;
$total_withdrawn = findQuery(" SELECT SUM(amount) as t FROM withdrawals WHERE account_id=$uid AND status!='rejected' ")['t'] ?? 0;
$available = $total_earned - $total_withdrawn;
if ($amount > $available) {
    encode(['status' => false, 'message' => 'Insufficient funds. Available: ' . number_format($available, 2)]);
}
execute(" INSERT INTO withdrawals (account_id,amount,status,created_at) VALUES ($uid,$amount,'pending',NOW()) ");
$account = findQuery(" SELECT email,accountname FROM accounts WHERE id=$uid ");
if ($account['email']) {
    $body = "<h1>Withdrawal Request</h1><p>User: {$account['accountname']}</p><p>Amount: $amount GASHY</p>";
    mailer('New Withdrawal Request', $body, 'System', 'admin@gashy.com');
}
encode(['status' => true, 'message' => 'Withdrawal requested successfully']);
