<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
$slug = request('slug', 'get');
$id = request('id', 'get');
if (!$slug && !$id) {
    encode(['status' => false, 'message' => 'Product identifier required']);
}
$cond = $slug ? "p.slug='$slug'" : "p.id='$id'";
$sql = " SELECT p.*,c.name as category_name,c.slug as category_slug,s.store_name,s.rating as seller_rating,s.account_id as seller_id FROM products p JOIN categories c ON p.category_id=c.id JOIN sellers s ON p.seller_id=s.account_id WHERE $cond AND p.status='active' LIMIT 1 ";
$product = findQuery($sql);
if (!$product) {
    encode(['status' => false, 'message' => 'Product not found']);
}
execute(" UPDATE products SET views=views+1 WHERE id={$product['id']} ");
$catId = $product['category_id'];
$pId = $product['id'];
$related = getQuery(" SELECT id,title,slug,price_gashy,images,type FROM products WHERE category_id=$catId AND id!=$pId AND status='active' AND stock>0 LIMIT 4 ");
encode(['status' => true, 'data' => $product, 'related' => $related]);
