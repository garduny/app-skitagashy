<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
$banners = getQuery(" SELECT image_path,link_url FROM banners WHERE is_active=1 ORDER BY sort_order ASC ");
$featured = getQuery(" SELECT p.id,p.title,p.slug,p.price_gashy,p.images,p.type,s.store_name FROM products p JOIN sellers s ON p.seller_id=s.account_id WHERE p.status='active' AND p.stock>0 ORDER BY p.views DESC LIMIT 8 ");
$auctions = getQuery(" SELECT a.id,a.end_time,a.current_bid,p.title,p.images,p.slug FROM auctions a JOIN products p ON a.product_id=p.id WHERE a.status='active' AND a.end_time>NOW() ORDER BY a.end_time ASC LIMIT 4 ");
$new_arrivals = getQuery(" SELECT p.id,p.title,p.slug,p.price_gashy,p.images,p.type FROM products p WHERE p.status='active' AND p.stock>0 ORDER BY p.created_at DESC LIMIT 8 ");
encode([
    'status' => true,
    'data' => [
        'banners' => $banners,
        'featured_products' => $featured,
        'live_auctions' => $auctions,
        'new_arrivals' => $new_arrivals
    ]
]);
