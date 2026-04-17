<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$page = max(1, (int)(request('page', 'get') ?? 1));
$limit = max(1, min(50, (int)(request('limit', 'get') ?? 20)));
$offset = ($page - 1) * $limit;
$rate = (float)toGashy();
$sql = " SELECT p.id,p.title,p.slug,p.price_usd,p.images,p.description,p.stock,p.views,p.created_at FROM products p WHERE p.type='mystery_box' AND p.status='active' AND p.stock>0 ORDER BY p.price_usd ASC,p.id DESC LIMIT $limit OFFSET $offset ";
$data = getQuery($sql);
$total = countQuery(" SELECT COUNT(1) c FROM products WHERE type='mystery_box' AND status='active' AND stock>0 ");
foreach ($data as &$box) {
    $id = (int)$box['id'];
    $box['price_usd'] = (float)$box['price_usd'];
    $box['price_gashy'] = $rate > 0 ? round($box['price_usd'] / $rate, 8) : 0;
    $box['stock'] = (int)$box['stock'];
    $box['views'] = (int)($box['views'] ?? 0);
    $imgs = json_decode($box['images'], true);
    $box['images'] = is_array($imgs) ? $imgs : [];
    $loot = getQuery(" SELECT l.id,l.reward_product_id,l.reward_option_id,l.reward_amount,l.probability,l.rarity,l.won_count,l.is_active,rp.title product_name,rp.images product_images,rp.type product_type,rp.price_usd reward_price_usd FROM mystery_box_loot l LEFT JOIN products rp ON rp.id=l.reward_product_id WHERE l.box_product_id=$id AND l.is_active=1 ORDER BY l.probability DESC,l.id ASC ");
    $rewards = [];
    $chance = 0;
    foreach ($loot as $r) {
        $chance += (float)$r['probability'];
        $img = json_decode($r['product_images'] ?? '[]', true);
        $rewards[] = [
            'id' => (int)$r['id'],
            'reward_product_id' => (int)$r['reward_product_id'],
            'reward_option_id' => (int)$r['reward_option_id'],
            'reward_amount' => (int)$r['reward_amount'],
            'probability' => (float)$r['probability'],
            'rarity' => $r['rarity'] ?: 'common',
            'won_count' => (int)$r['won_count'],
            'product_name' => $r['product_name'] ?: 'Mystery Reward',
            'product_type' => $r['product_type'] ?: null,
            'price_usd' => (float)($r['reward_price_usd'] ?? 0),
            'price_gashy' => $rate > 0 ? round((float)($r['reward_price_usd'] ?? 0) / $rate, 8) : 0,
            'image' => (is_array($img) && !empty($img[0])) ? $img[0] : 'assets/placeholder.png'
        ];
    }
    $box['loot_count'] = count($rewards);
    $box['chance_total'] = round($chance, 4);
    $box['possible_rewards'] = $rewards;
}
encode([
    'status' => true,
    'data' => $data,
    'meta' => [
        'page' => $page,
        'limit' => $limit,
        'total' => (int)$total,
        'pages' => $limit > 0 ? (int)ceil($total / $limit) : 1
    ]
]);
