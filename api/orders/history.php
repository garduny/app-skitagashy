<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT user_id FROM sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) {
    encode(['status' => false, 'message' => 'Unauthorized']);
}
$uid = $session['user_id'];
$page = max(1, (int)(request('page', 'get') ?? 1));
$limit = max(1, (int)(request('limit', 'get') ?? 10));
$offset = ($page - 1) * $limit;
$sql = " SELECT id,total_gashy,status,created_at,tx_signature FROM orders WHERE user_id=$uid ORDER BY id DESC LIMIT $limit OFFSET $offset ";
$orders = getQuery($sql);
$total = countQuery(" SELECT COUNT(*) FROM orders WHERE user_id=$uid ");
foreach ($orders as &$order) {
    $oid = $order['id'];
    $order['items'] = getQuery(" SELECT oi.quantity,oi.price_at_purchase,p.title,p.images,p.type FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id=$oid ");
}
encode(['status' => true, 'data' => $orders, 'meta' => ['page' => $page, 'total' => $total]]);
