<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT user_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) {
    encode(['status' => false, 'message' => 'Unauthorized']);
}
$uid = $session['user_id'];
$seller = findQuery(" SELECT * FROM sellers WHERE account_id=$uid AND is_approved=1 ");
if (!$seller) {
    encode(['status' => false, 'message' => 'Seller profile not active']);
}
$stats = [
    'total_sales' => $seller['total_sales'],
    'rating' => $seller['rating'],
    'products' => countQuery(" SELECT COUNT(*) FROM products WHERE seller_id=$uid "),
    'earnings' => findQuery(" SELECT SUM(price_gashy) as t FROM products p JOIN order_items oi ON p.id=oi.product_id JOIN orders o ON oi.order_id=o.id WHERE p.seller_id=$uid AND o.status='completed' ")['t'] ?? 0
];
$products = getQuery(" SELECT p.id,p.title,p.price_gashy,p.stock,p.type,p.status,p.images,c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id=c.id WHERE p.seller_id=$uid ORDER BY p.id DESC ");
encode(['status' => true, 'seller' => $seller, 'stats' => $stats, 'products' => $products]);
