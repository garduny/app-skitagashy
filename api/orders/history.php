<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) {
    encode(['status' => false, 'message' => 'Unauthorized']);
}
$uid   = $session['account_id'];
$page  = max(1, (int)(request('page') ?? 1));
$limit = max(1, (int)(request('limit') ?? 50));
$offset = ($page - 1) * $limit;
$tab   = request('tab') ?? 'my';
$seller = findQuery(" SELECT account_id FROM sellers WHERE account_id=$uid AND is_approved=1 ");
$is_seller = (bool)$seller;
if ($tab === 'sold' && $is_seller) {
    $sql = "
        SELECT DISTINCT o.id, o.total_gashy, o.status, o.created_at, o.tx_signature, a.accountname AS buyer
        FROM orders o
        JOIN order_items oi ON oi.order_id = o.id
        JOIN products p ON oi.product_id = p.id
        JOIN accounts a ON a.id = o.account_id
        WHERE p.seller_id = $uid
        ORDER BY o.id DESC
        LIMIT $limit OFFSET $offset
    ";
    $total_sql = "
        SELECT COUNT(DISTINCT o.id)
        FROM orders o
        JOIN order_items oi ON oi.order_id = o.id
        JOIN products p ON oi.product_id = p.id
        WHERE p.seller_id = $uid
    ";
    $can_edit = true;
} else {
    $sql = " SELECT id, total_gashy, status, created_at, tx_signature FROM orders WHERE account_id=$uid ORDER BY id DESC LIMIT $limit OFFSET $offset ";
    $total_sql = " SELECT COUNT(1) FROM orders WHERE account_id=$uid ";
    $can_edit = false;
}
$orders = getQuery($sql);
$total  = countQuery($total_sql);
foreach ($orders as &$order) {
    $oid = $order['id'];
    $order['items'] = getQuery("
        SELECT oi.quantity, oi.price_at_purchase, p.title, p.images, p.type
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = $oid
    ");
}
encode([
    'status'    => true,
    'data'      => $orders,
    'meta'      => ['page' => $page, 'total' => $total],
    'is_seller' => $is_seller,
    'can_edit'  => $can_edit
]);
