<?php
require_once '../../server/init.php';
$id = (int)request('id');
$status = request('status');
if (!$id || !$status) encode(['status' => false, 'message' => 'INVALID_REQUEST']);
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW()");
if (!$session) encode(['status' => false, 'message' => 'UNAUTHORIZED']);
$uid = (int)$session['account_id'];
$acc = findQuery(" SELECT role FROM accounts WHERE id=$uid");
$role = $acc['role'] ?? 'user';
$allowed = ['pending', 'processing', 'shipped', 'delivered', 'completed', 'failed', 'refunded'];
if (!in_array($status, $allowed)) encode(['status' => false, 'message' => 'INVALID_STATUS']);
$order = findQuery(" SELECT id,account_id FROM orders WHERE id=$id");
if (!$order) encode(['status' => false, 'message' => 'ORDER_NOT_FOUND']);
$customer_id = $order['account_id'];
$customer = findQuery(" SELECT email FROM accounts WHERE id=$customer_id ");
$canUpdate = false;
if ($role === 'admin') {
    $canUpdate = true;
} else {
    $owns = findQuery(" SELECT 1
        FROM order_items oi
        JOIN products p ON p.id=oi.product_id
        WHERE oi.order_id=$id AND p.seller_id=$uid
        LIMIT 1
    ");
    if ($owns) $canUpdate = true;
}
if (!$canUpdate) encode(['status' => false, 'message' => 'FORBIDDEN']);
execute("UPDATE orders SET status='$status' WHERE id=$id");
if ($status === 'delivered' && !empty($customer['email']) && function_exists('mailer')) {
    $subject = "Order #$id Delivered";
    $body = "<h1>Your Order is Delivered!</h1><p>Status: <b>$status</b></p><p>Please check your dashboard.</p>";
    mailer($subject, $body, "Gashy Team", $customer['email']);
}
encode(['status' => true, 'message' => 'UPDATED']);
