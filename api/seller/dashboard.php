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
$fee_row = findQuery(" SELECT value FROM settings WHERE key_name='platform_fee' ");
$fee_percent = floatval($fee_row['value'] ?? 5);
$gross = findQuery(" SELECT SUM(oi.price_at_purchase * oi.quantity) as t FROM order_items oi JOIN products p ON oi.product_id=p.id JOIN orders o ON oi.order_id=o.id WHERE p.seller_id=$uid AND o.status='completed' ")['t'] ?? 0;
$net_earnings = $gross * ((100 - $fee_percent) / 100);
$stats = [
    'total_sales' => $seller['total_sales'],
    'rating' => $seller['rating'],
    'products' => countQuery(" SELECT COUNT(*) FROM products WHERE seller_id=$uid "),
    'earnings' => $net_earnings,
    'gross_sales' => $gross,
    'fee_rate' => $fee_percent . '%'
];
$products = getQuery(" SELECT p.id,p.title,p.price_gashy,p.stock,p.type,p.status,p.images,c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id=c.id WHERE p.seller_id=$uid ORDER BY p.id DESC ");
$sales = getQuery(" SELECT oi.price_at_purchase,oi.quantity,o.created_at,p.title,a.accountname FROM order_items oi JOIN orders o ON oi.order_id=o.id JOIN products p ON oi.product_id=p.id LEFT JOIN accounts a ON o.account_id=a.id WHERE p.seller_id=$uid AND o.status='completed' ORDER BY o.id DESC LIMIT 20 ");
encode(['status' => true, 'seller' => $seller, 'stats' => $stats, 'products' => $products, 'sales' => $sales]);
