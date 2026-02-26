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
$oid = (int)request('id');
if (!$oid) {
    encode(['status' => false, 'message' => 'Order ID required']);
}
$order = findQuery(" SELECT id, status, total_gashy FROM orders WHERE id=$oid AND account_id=$uid ");
if (!$order) {
    encode(['status' => false, 'message' => 'Order not found']);
}

if ($order['status'] !== 'completed') {
    encode(['status' => false, 'message' => 'Order not paid yet. Content is locked.']);
}
$cards = getQuery("
SELECT p.title,gc.id,gc.code_enc,gc.pin_enc
FROM gift_cards gc
JOIN products p ON gc.product_id=p.id
WHERE gc.sold_to_order_id=$oid
ORDER BY gc.id ASC
");
define('ENC_KEY', 'GashySecretKey2026');
define('ENC_ALGO', 'AES-256-CBC');
function decryptCode($str)
{
    if (!$str) return null;
    try {
        list($enc, $iv) = explode('::', base64_decode($str), 2);
        return openssl_decrypt($enc, ENC_ALGO, ENC_KEY, 0, $iv);
    } catch (Exception $e) {
        return 'Error Decrypting';
    }
}
$delivered_items = [];
foreach ($cards as $c) {
    $delivered_items[] = [
        'product' => $c['title'],
        'code'    => decryptCode($c['code_enc']),
        'pin'     => decryptCode($c['pin_enc'])
    ];
}
logActivity('account', $uid, 'view_secret', 'Viewed Order #' . $oid);
encode([
    'status' => true,
    'order_id' => $oid,
    'gift_cards' => $delivered_items
]);
