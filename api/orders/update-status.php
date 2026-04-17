<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$id = (int)request('id');
$newStatus = trim(request('status') ?? '');
if ($id < 1 || $newStatus === '') encode(['status' => false, 'message' => 'INVALID_REQUEST']);
$token = request('token') ?: str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() LIMIT 1 ");
if (!$session) encode(['status' => false, 'message' => 'UNAUTHORIZED']);
$uid = (int)$session['account_id'];
$acc = findQuery(" SELECT role,accountname FROM accounts WHERE id=$uid LIMIT 1 ");
$role = $acc['role'] ?? 'user';
$allowed = ['pending', 'processing', 'shipped', 'delivered', 'completed', 'failed', 'refunded'];
if (!in_array($newStatus, $allowed, true)) encode(['status' => false, 'message' => 'INVALID_STATUS']);
$order = findQuery(" SELECT id,account_id,status,total_gashy FROM orders WHERE id=$id LIMIT 1 ");
if (!$order) encode(['status' => false, 'message' => 'ORDER_NOT_FOUND']);
$oldStatus = $order['status'];
if ($oldStatus === $newStatus) encode(['status' => true, 'message' => 'NO_CHANGE']);
$customerId = (int)$order['account_id'];
$customer = findQuery(" SELECT email,accountname FROM accounts WHERE id=$customerId LIMIT 1 ");
$canUpdate = false;
if ($role === 'admin') $canUpdate = true;
else {
    $owns = findQuery(" SELECT 1 FROM order_items oi INNER JOIN products p ON p.id=oi.product_id WHERE oi.order_id=$id AND p.seller_id=$uid LIMIT 1 ");
    if ($owns) $canUpdate = true;
}
if (!$canUpdate) encode(['status' => false, 'message' => 'FORBIDDEN']);
execute(" START TRANSACTION ");
try {
    execute(" UPDATE orders SET status='$newStatus',updated_at=NOW() WHERE id=$id ");
    if ($newStatus === 'refunded' && $oldStatus !== 'refunded') {
        $exists = findQuery(" SELECT id FROM transactions WHERE type='refund' AND reference_id=$id LIMIT 1 ");
        if (!$exists) {
            $amount = (float)$order['total_gashy'];
            if ($amount > 0) execute(" INSERT INTO transactions(account_id,type,amount,reference_id,status,created_at) VALUES($customerId,'refund',$amount,$id,'confirmed',NOW()) ");
        }
    }
    if (function_exists('logActivity')) logActivity('account', $uid, 'update_order_status', "Order #$id : $oldStatus -> $newStatus");
    execute(" COMMIT ");
    if (!empty($customer['email']) && function_exists('mailer')) {
        $name = $customer['accountname'] ?: 'User';
        $subject = "Order #$id Updated";
        $body = "<div style='font-family:Arial;padding:20px;color:#222'><h2 style='color:#00d48f'>Order Status Updated</h2><p>Hello {$name},</p><p>Your order <strong>#$id</strong> status is now <strong>" . strtoupper($newStatus) . "</strong>.</p><p>Please check your dashboard for details.</p></div>";
        mailer($subject, $body, 'Gashy Bazaar', $customer['email']);
    }
    encode(['status' => true, 'message' => 'UPDATED', 'old_status' => $oldStatus, 'new_status' => $newStatus]);
} catch (Exception $e) {
    execute(" ROLLBACK ");
    encode(['status' => false, 'message' => $e->getMessage()]);
}
