<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$account_session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$account_session) {
    encode(['status' => false, 'message' => 'Unauthorized']);
}
$uid = $account_session['account_id'];
$page = max(1, (int)(request('page', 'get') ?? 1));
$limit = max(1, (int)(request('limit', 'get') ?? 10));
$offset = ($page - 1) * $limit;
$sql = " SELECT t.amount as my_bid,t.created_at as bid_time,a.id as auction_id,a.status,a.end_time,a.current_bid as highest_bid,p.title,p.images,p.slug FROM transactions t JOIN auctions a ON t.reference_id=a.id JOIN products p ON a.product_id=p.id WHERE t.account_id=$uid AND t.type='auction_bid' ORDER BY t.created_at DESC LIMIT $limit OFFSET $offset ";
$data = getQuery($sql);
$total = countQuery(" SELECT 1 FROM transactions WHERE account_id=$uid AND type='auction_bid' ");
encode(['status' => true, 'data' => $data, 'meta' => ['page' => $page, 'total' => $total]]);
