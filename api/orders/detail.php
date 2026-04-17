<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$token = request('token') ?: str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() LIMIT 1 ");
if (!$session) encode(['status' => false, 'message' => 'Unauthorized']);
$uid = (int)$session['account_id'];
$oid = (int)request('id');
if ($oid < 1) encode(['status' => false, 'message' => 'Order ID required']);
$order = findQuery(" SELECT id,status,total_gashy,total_usd,created_at FROM orders WHERE id=$oid AND account_id=$uid LIMIT 1 ");
if (!$order) encode(['status' => false, 'message' => 'Order not found']);
$items = getQuery(" SELECT oi.*,p.title,p.slug,p.type,p.images FROM order_items oi INNER JOIN products p ON p.id=oi.product_id WHERE oi.order_id=$oid ORDER BY oi.id ASC ");
$giftCards = [];
$digitalItems = [];
if ($order['status'] === 'completed') {
    $cards = getQuery(" SELECT gc.*,p.title,p.slug FROM gift_cards gc INNER JOIN products p ON p.id=gc.product_id WHERE gc.sold_to_order_id=$oid ORDER BY gc.id ASC ");
    define('ENC_KEY', 'GashySecretKey2026');
    define('ENC_ALGO', 'AES-256-CBC');
    function decryptOrderValue($str)
    {
        if (!$str) return '';
        try {
            $raw = base64_decode($str, true);
            if ($raw === false) return '';
            $parts = explode('::', $raw, 2);
            if (count($parts) !== 2) return '';
            return (string)openssl_decrypt($parts[0], ENC_ALGO, ENC_KEY, 0, $parts[1]);
        } catch (Exception $e) {
            return '';
        }
    }
    foreach ($cards as $c) {
        $row = [
            'id' => (int)$c['id'],
            'product' => $c['title'],
            'slug' => $c['slug'],
            'code' => decryptOrderValue($c['code_enc']),
            'pin' => decryptOrderValue($c['pin_enc']),
            'expiry_date' => $c['expiry_date'] ?? null
        ];
        $giftCards[] = $row;
        $digitalItems[] = $row;
    }
}
$list = [];
foreach ($items as $it) {
    $img = 'assets/placeholder.png';
    $imgs = json_decode($it['images'] ?? '[]', true);
    if (is_array($imgs) && !empty($imgs[0])) $img = $imgs[0];
    $attrs = [];
    if (isset($it['attributes']) && $it['attributes'] !== '') {
        $tmp = json_decode($it['attributes'], true);
        if (is_array($tmp)) $attrs = $tmp;
    }
    $list[] = [
        'id' => (int)$it['id'],
        'product_id' => (int)$it['product_id'],
        'title' => $it['title'],
        'slug' => $it['slug'],
        'type' => $it['type'],
        'quantity' => (int)$it['quantity'],
        'price_at_purchase' => (float)$it['price_at_purchase'],
        'option_id' => (int)($it['option_id'] ?? 0),
        'attributes' => $attrs,
        'image' => $img
    ];
}
if (function_exists('logActivity')) logActivity('account', $uid, 'view_order_detail', "Viewed Order #$oid");
encode([
    'status' => true,
    'order' => [
        'id' => (int)$order['id'],
        'status' => $order['status'],
        'total_gashy' => (float)$order['total_gashy'],
        'total_usd' => (float)($order['total_usd'] ?? 0),
        'created_at' => $order['created_at']
    ],
    'items' => $list,
    'gift_cards' => $giftCards,
    'digital_items' => $digitalItems,
    'locked' => $order['status'] !== 'completed'
]);
