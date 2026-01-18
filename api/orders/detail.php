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
$oid = (int)request('id', 'get');
if (!$oid) {
    encode(['status' => false, 'message' => 'Order ID required']);
}
$order = findQuery(" SELECT * FROM orders WHERE id=$oid AND user_id=$uid ");
if (!$order) {
    encode(['status' => false, 'message' => 'Order not found']);
}
$items = getQuery(" SELECT oi.*,p.title,p.slug,p.images,p.type FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id=$oid ");
$gift_cards = [];
if ($order['status'] === 'completed' || $order['status'] === 'pending') {
    $gift_cards = getQuery(" SELECT gc.code_enc,gc.pin_enc,p.title FROM gift_cards gc JOIN products p ON gc.product_id=p.id WHERE gc.sold_to_order_id=$oid ");
}
encode(['status' => true, 'data' => $order, 'items' => $items, 'gift_cards' => $gift_cards]);
