<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$token = request('token') ?: str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() LIMIT 1 ");
if (!$session) encode(['status' => false, 'message' => 'Unauthorized']);
$uid = (int)$session['account_id'];
$page = max(1, (int)(request('page') ?? 1));
$limit = max(1, min(100, (int)(request('limit') ?? 20)));
$offset = ($page - 1) * $limit;
$tab = request('tab') ?: 'my';
$seller = findQuery(" SELECT account_id FROM sellers WHERE account_id=$uid AND is_approved=1 LIMIT 1 ");
$isSeller = (bool)$seller;
if ($tab === 'sold' && $isSeller) {
    $sql = " SELECT DISTINCT o.id,o.total_gashy,o.total_usd,o.status,o.created_at,o.updated_at,o.tx_signature,a.accountname buyer FROM orders o INNER JOIN order_items oi ON oi.order_id=o.id INNER JOIN products p ON p.id=oi.product_id INNER JOIN accounts a ON a.id=o.account_id WHERE p.seller_id=$uid ORDER BY o.id DESC LIMIT $limit OFFSET $offset ";
    $totalSql = " SELECT COUNT(DISTINCT o.id) c FROM orders o INNER JOIN order_items oi ON oi.order_id=o.id INNER JOIN products p ON p.id=oi.product_id WHERE p.seller_id=$uid ";
    $canEdit = true;
} else {
    $sql = " SELECT id,total_gashy,total_usd,status,created_at,updated_at,tx_signature FROM orders WHERE account_id=$uid ORDER BY id DESC LIMIT $limit OFFSET $offset ";
    $totalSql = " SELECT COUNT(1) c FROM orders WHERE account_id=$uid ";
    $canEdit = false;
}
$orders = getQuery($sql);
$total = (int)(findQuery($totalSql)['c'] ?? 0);
$data = [];
foreach ($orders as $order) {
    $oid = (int)$order['id'];
    $rows = getQuery(" SELECT oi.*,p.title,p.slug,p.images,p.type FROM order_items oi INNER JOIN products p ON p.id=oi.product_id WHERE oi.order_id=$oid ORDER BY oi.id ASC ");
    $items = [];
    $totalQty = 0;
    foreach ($rows as $r) {
        $img = 'assets/placeholder.png';
        $imgs = json_decode($r['images'] ?? '[]', true);
        if (is_array($imgs) && !empty($imgs[0])) $img = $imgs[0];
        $attrs = [];
        if (isset($r['attributes']) && $r['attributes'] !== '') {
            $tmp = json_decode($r['attributes'], true);
            if (is_array($tmp)) $attrs = $tmp;
        }
        $qty = (int)$r['quantity'];
        $totalQty += $qty;
        $items[] = [
            'id' => (int)$r['id'],
            'product_id' => (int)$r['product_id'],
            'title' => $r['title'],
            'slug' => $r['slug'],
            'type' => $r['type'],
            'quantity' => $qty,
            'price_at_purchase' => (float)$r['price_at_purchase'],
            'option_id' => (int)($r['option_id'] ?? 0),
            'attributes' => $attrs,
            'image' => $img
        ];
    }
    $data[] = [
        'id' => $oid,
        'buyer' => $order['buyer'] ?? null,
        'total_gashy' => (float)$order['total_gashy'],
        'total_usd' => (float)($order['total_usd'] ?? 0),
        'status' => $order['status'],
        'created_at' => $order['created_at'],
        'updated_at' => $order['updated_at'] ?? $order['created_at'],
        'tx_signature' => $order['tx_signature'],
        'total_qty' => $totalQty,
        'items' => $items
    ];
}
encode([
    'status' => true,
    'data' => $data,
    'meta' => [
        'page' => $page,
        'limit' => $limit,
        'total' => $total,
        'pages' => (int)ceil($total / $limit)
    ],
    'is_seller' => $isSeller,
    'can_edit' => $canEdit
]);
