<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
$page = max(1, (int)(request('page', 'get') ?? 1));
$limit = max(1, (int)(request('limit', 'get') ?? 20));
$offset = ($page - 1) * $limit;
$sql = " SELECT p.id,p.title,p.slug,p.price_gashy,p.images,p.description,p.stock FROM products p WHERE p.type='mystery_box' AND p.status='active' AND p.stock>0 ORDER BY p.price_gashy ASC LIMIT $limit OFFSET $offset ";
$data = getQuery($sql);
$total = countQuery(" SELECT 1 FROM products WHERE type='mystery_box' AND status='active' AND stock>0 ");
foreach ($data as &$box) {
    $bid = $box['id'];
    $box['possible_rewards'] = getQuery(" SELECT l.probability,l.rarity,l.reward_amount,rp.title as product_name,rp.images as product_images FROM mystery_box_loot l LEFT JOIN products rp ON l.reward_product_id=rp.id WHERE l.box_product_id=$bid ORDER BY l.probability ASC ");
}
encode(['status' => true, 'data' => $data, 'meta' => ['page' => $page, 'total' => $total]]);
