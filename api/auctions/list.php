<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
$page = max(1, (int)(request('page', 'get') ?? 1));
$limit = max(1, (int)(request('limit', 'get') ?? 20));
$offset = ($page - 1) * $limit;
$sql = " SELECT a.id,a.end_time,a.current_bid,a.start_price,a.status,p.title,p.images,p.slug,p.description FROM auctions a JOIN products p ON a.product_id=p.id WHERE a.status='active' AND a.end_time>NOW() ORDER BY a.end_time ASC LIMIT $limit OFFSET $offset ";
$data = getQuery($sql);
$total = countQuery(" SELECT COUNT(*) FROM auctions WHERE status='active' AND end_time>NOW() ");
foreach ($data as &$row) {
    $row['time_left'] = strtotime($row['end_time']) - time();
}
encode(['status' => true, 'data' => $data, 'meta' => ['page' => $page, 'total' => $total]]);
