<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
$page = max(1, (int)(request('page', 'get') ?? 1));
$limit = max(1, (int)(request('limit', 'get') ?? 20));
$offset = ($page - 1) * $limit;
$cat = request('category', 'get');
$type = request('type', 'get');
$sort = request('sort', 'get') ?? 'newest';
$search = request('search', 'get');
$where = "WHERE p.status='active' AND p.stock>0";
if ($cat) {
    $where .= " AND c.slug='$cat'";
}
if ($type) {
    $where .= " AND p.type='$type'";
}
if ($search) {
    $where .= " AND (p.title LIKE '%$search%' OR p.description LIKE '%$search%')";
}
$order = "ORDER BY p.id DESC";
if ($sort === 'price_asc') {
    $order = "ORDER BY p.price_gashy ASC";
}
if ($sort === 'price_desc') {
    $order = "ORDER BY p.price_gashy DESC";
}
$sql = " SELECT p.id,p.title,p.slug,p.price_gashy,p.images,p.type,p.stock,c.name as category_name,c.slug as category_slug,s.store_name,s.rating as seller_rating FROM products p JOIN categories c ON p.category_id=c.id JOIN sellers s ON p.seller_id=s.account_id $where $order LIMIT $limit OFFSET $offset ";
$data = getQuery($sql);
$countSql = " SELECT COUNT(*) FROM products p JOIN categories c ON p.category_id=c.id $where ";
$total = countQuery($countSql);
encode(['status' => true, 'data' => $data, 'meta' => ['current_page' => $page, 'per_page' => $limit, 'total_items' => $total, 'total_pages' => ceil($total / $limit)]]);
