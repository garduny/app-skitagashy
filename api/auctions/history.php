<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) encode(['status' => false, 'message' => 'Unauthorized']);
$uid = (int)$session['account_id'];
$page = max(1, (int)(request('page', 'get') ?? 1));
$limit = max(1, min(50, (int)(request('limit', 'get') ?? 10)));
$offset = ($page - 1) * $limit;
$rate = (float)toGashy();
if ($rate <= 0) $rate = 1;
$sql = "
SELECT
t.id,
ABS(t.amount) bid_gashy,
t.created_at bid_time,
a.id auction_id,
a.status,
a.end_time,
a.current_bid_usd,
a.highest_bidder_id,
a.reserve_price_usd,
p.id product_id,
p.title,
p.slug,
p.images,
p.seller_id,
acc.accountname seller_name
FROM transactions t
JOIN auctions a ON a.id=t.reference_id
JOIN products p ON p.id=a.product_id
LEFT JOIN accounts acc ON acc.id=p.seller_id
WHERE t.account_id=$uid
AND t.type='auction_bid'
ORDER BY t.id DESC
LIMIT $limit OFFSET $offset
";
$data = getQuery($sql);
foreach ($data as &$r) {
    $imgs = json_decode($r['images'] ?? '[]', true);
    $r['image'] = $imgs[0] ?? 'assets/placeholder.png';
    $r['my_bid'] = (float)$r['bid_gashy'];
    $r['highest_bid'] = round((float)$r['current_bid_usd'] / $rate, 8);
    $r['reserve_price'] = round((float)$r['reserve_price_usd'] / $rate, 8);
    $r['is_winning'] = (int)$r['highest_bidder_id'] === $uid;
    $r['ended'] = strtotime($r['end_time']) <= time();
    $r['can_bid'] = $r['status'] === 'active' && !$r['ended'];
    unset($r['images'], $r['bid_gashy'], $r['current_bid_usd'], $r['reserve_price_usd'], $r['highest_bidder_id']);
}
$total = countQuery(" SELECT COUNT(1) FROM transactions WHERE account_id=$uid AND type='auction_bid' ");
encode([
    'status' => true,
    'data' => $data,
    'meta' => [
        'page' => $page,
        'limit' => $limit,
        'total' => $total,
        'pages' => ceil($total / $limit)
    ]
]);
