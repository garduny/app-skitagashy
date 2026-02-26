<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW()");
if (!$session) {
    encode(['status' => false, 'message' => 'Unauthorized']);
}
$uid = $session['account_id'];
$seller = findQuery(" SELECT * FROM sellers WHERE account_id=$uid AND is_approved=1");
if (!$seller) {
    encode(['status' => false, 'message' => 'Seller profile not active']);
}
$reward_balance = findQuery(" SELECT COALESCE(SUM(amount),0) t
FROM transactions
WHERE account_id=$uid
AND status='confirmed'
AND type IN ('reward','referral','bonus','mystery_reward')
")['t'] ?? 0;
$fee_row = findQuery(" SELECT value FROM settings WHERE key_name='platform_fee'");
$fee_percent = (float)($fee_row['value'] ?? 5);
$gross = findQuery(" SELECT COALESCE(SUM(oi.price_at_purchase*oi.quantity),0) t
FROM order_items oi
JOIN products p ON oi.product_id=p.id
JOIN orders o ON oi.order_id=o.id
WHERE p.seller_id=$uid AND o.status='completed'
")['t'] ?? 0;
$net_earnings = $gross * ((100 - $fee_percent) / 100);
$withdrawn = findQuery(" SELECT COALESCE(SUM(amount),0) t
FROM withdrawals
WHERE account_id=$uid
AND LOWER(status)='approved'
")['t'];
$total_earnings = $net_earnings + $reward_balance;
$available = max($total_earnings - $withdrawn, 0);
$product_count = countQuery(" SELECT 1 FROM products WHERE seller_id=$uid");
$withdrawals = getQuery(" SELECT id,amount,status,created_at
FROM withdrawals
WHERE account_id=$uid
ORDER BY id DESC
LIMIT 20
");
$products = getQuery(" SELECT p.id,p.title,p.description,p.price_gashy,p.stock,p.type,p.status,p.images,
c.name cat_name
FROM products p
LEFT JOIN categories c ON p.category_id=c.id
WHERE p.seller_id=$uid
ORDER BY p.id DESC
");
$sales = getQuery(" SELECT oi.price_at_purchase,oi.quantity,o.created_at,p.title,a.accountname
FROM order_items oi
JOIN orders o ON oi.order_id=o.id
JOIN products p ON oi.product_id=p.id
LEFT JOIN accounts a ON o.account_id=a.id
WHERE p.seller_id=$uid AND o.status='completed'
ORDER BY oi.id DESC
LIMIT 20
");
$total_units = findQuery(" SELECT SUM(oi.quantity) q
FROM order_items oi
JOIN products p ON oi.product_id=p.id
JOIN orders o ON oi.order_id=o.id
WHERE p.seller_id=$uid AND o.status='completed'
")['q'] ?? 0;
$stats = [
    'total_units' => (int)$total_units,
    'total_sales' => number_format($gross, 3, '.', ''),
    'rating' => $seller['rating'],
    'products' => $product_count,
    'earnings' => number_format($net_earnings, 3, '.', ''),
    'reward_balance' => number_format($reward_balance, 3, '.', ''),
    'available' => number_format(max($available, 0), 3, '.', ''),
    'gross_sales' => number_format($gross, 3, '.', ''),
    'fee_rate' => $fee_percent . '%'
];
encode([
    'status' => true,
    'seller' => $seller,
    'stats' => $stats,
    'products' => $products,
    'sales' => $sales,
    'withdrawals' => $withdrawals
]);
