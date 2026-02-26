<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
$page = max(1, (int)(request('page', 'get') ?? 1));
$limit = max(1, (int)(request('limit', 'get') ?? 20));
$offset = ($page - 1) * $limit;
$json = json_decode(file_get_contents('php://input'), true);
$filter = $json['filter'] ?? request('filter', 'get') ?? 'ending_soon';
$uid = 0;
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if ($session) $uid = $session['account_id'];
$where = " WHERE a.status='active' AND a.end_time>NOW() ";
$order = " ORDER BY a.end_time ASC ";
if ($filter === 'hot') {
    $order = " ORDER BY a.current_bid DESC ";
} elseif ($filter === 'my_bids') {
    if (!$uid) {
        encode(['status' => false, 'message' => 'Please login to view your bids']);
    }
    $where .= " AND a.id IN (SELECT reference_id FROM transactions WHERE account_id=$uid AND type='auction_bid') ";
}
$sql = " SELECT a.id,a.end_time,a.current_bid,a.start_price,a.status,p.title,p.images,p.slug,p.description,
              u.accountname as high_bidder
       FROM auctions a 
       JOIN products p ON a.product_id=p.id 
       LEFT JOIN accounts u ON a.highest_bidder_id=u.id
       $where 
       $order 
       LIMIT $limit OFFSET $offset ";
$data = getQuery($sql);
$total = countQuery(" SELECT 1 FROM auctions a $where ");
foreach ($data as &$row) {
    $row['time_left'] = strtotime($row['end_time']) - time();
}
encode(['status' => true, 'data' => $data, 'meta' => ['page' => $page, 'total' => $total]]);
