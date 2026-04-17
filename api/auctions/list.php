<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$page = max(1, (int)(request('page', 'get') ?? 1));
$limit = max(1, min(50, (int)(request('limit', 'get') ?? 20)));
$offset = ($page - 1) * $limit;
$raw = json_decode(file_get_contents('php://input'), true);
$filter = $raw['filter'] ?? request('filter', 'get') ?? 'ending_soon';
$uid = 0;
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if ($session) $uid = (int)$session['account_id'];
$rate = (float)toGashy();
if ($rate <= 0) $rate = 1;
$where = " WHERE a.status='active' AND a.end_time>NOW() AND p.status='active' ";
$order = " ORDER BY a.end_time ASC ";
if ($filter === 'hot') $order = " ORDER BY a.current_bid_usd DESC,a.end_time ASC ";
elseif ($filter === 'newest') $order = " ORDER BY a.id DESC ";
elseif ($filter === 'lowest') $order = " ORDER BY a.current_bid_usd ASC,a.end_time ASC ";
elseif ($filter === 'my_bids') {
    if (!$uid) encode(['status' => false, 'message' => 'Please login to view your bids']);
    $where .= " AND EXISTS(SELECT 1 FROM transactions t WHERE t.reference_id=a.id AND t.account_id=$uid AND t.type='auction_bid')";
    $order = " ORDER BY a.end_time ASC ";
}
$sql = "
SELECT
a.id,
a.product_id,
a.option_id,
a.start_time,
a.end_time,
a.start_price_usd,
a.reserve_price_usd,
a.current_bid_usd,
a.highest_bidder_id,
a.status,
p.title,
p.slug,
p.description,
p.images,
p.stock,
p.type,
p.seller_id,
acc.accountname seller_name,
hb.accountname high_bidder
FROM auctions a
JOIN products p ON p.id=a.product_id
LEFT JOIN accounts acc ON acc.id=p.seller_id
LEFT JOIN accounts hb ON hb.id=a.highest_bidder_id
$where
$order
LIMIT $limit OFFSET $offset
";
$data = getQuery($sql);
$total = countQuery(" SELECT COUNT(1) FROM auctions a JOIN products p ON p.id=a.product_id $where ");
foreach ($data as &$row) {
    $imgs = json_decode($row['images'] ?? '[]', true);
    $row['image'] = $imgs[0] ?? 'assets/placeholder.png';
    $row['current_bid'] = round((float)$row['current_bid_usd'] / $rate, 8);
    $row['start_price'] = round((float)$row['start_price_usd'] / $rate, 8);
    $row['reserve_price'] = round((float)$row['reserve_price_usd'] / $rate, 8);
    $row['reserve_met'] = (float)$row['current_bid_usd'] >= (float)$row['reserve_price_usd'];
    $row['time_left'] = max(0, strtotime($row['end_time']) - time());
    $row['bid_count'] = countQuery(" SELECT COUNT(1) FROM transactions WHERE type='auction_bid' AND reference_id=" . $row['id'] . " ");
    $row['is_owner'] = $uid > 0 && (int)$row['seller_id'] === $uid;
    $row['is_winning'] = $uid > 0 && (int)$row['highest_bidder_id'] === $uid;
    $row['next_min_bid'] = round((max((float)$row['current_bid_usd'], (float)$row['start_price_usd']) + max(1, max((float)$row['current_bid_usd'], (float)$row['start_price_usd']) * 0.05)) / $rate, 8);
    unset($row['images'], $row['current_bid_usd'], $row['start_price_usd'], $row['reserve_price_usd']);
}
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
