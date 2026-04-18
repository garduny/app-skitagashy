<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$token = request('token', 'post') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) encode(['status' => false, 'message' => 'Unauthorized']);
$uid = (int)$session['account_id'];
$seller = findQuery(" SELECT * FROM sellers WHERE account_id=$uid AND is_approved=1 LIMIT 1 ");
if (!$seller) encode(['status' => false, 'message' => 'Seller profile not active']);
$rate = (float)toGashy();
$fee_percent = (float)(findQuery(" SELECT value FROM settings WHERE key_name='platform_fee' LIMIT 1 ")['value'] ?? 5);
$reward_balance = (float)(findQuery(" SELECT COALESCE(SUM(amount),0) t FROM transactions WHERE account_id=$uid AND status='confirmed' AND type IN ('reward','referral','bonus','mystery_reward') ")['t'] ?? 0);
$failedbid = (float)(findQuery(" SELECT COALESCE(SUM(amount),0) t FROM transactions WHERE account_id=$uid AND status='failed' AND type='auction_bid' ")['t'] ?? 0);
$gross = (float)(findQuery(" SELECT COALESCE(SUM(oi.price_at_purchase*oi.quantity),0) t FROM order_items oi JOIN products p ON oi.product_id=p.id JOIN orders o ON oi.order_id=o.id WHERE p.seller_id=$uid AND LOWER(o.status)='completed' ")['t'] ?? 0);
$net_earnings = $gross * ((100 - $fee_percent) / 100);
$withdrawn = (float)(findQuery(" SELECT COALESCE(SUM(amount),0) t FROM withdrawals WHERE account_id=$uid AND LOWER(status) IN ('approved','paid') ")['t'] ?? 0);
$total_balance = $net_earnings + $reward_balance + $failedbid;
$available = max($total_balance - $withdrawn, 0);
$product_count = (int)countQuery(" SELECT 1 FROM products WHERE seller_id=$uid ");
$active_products = (int)countQuery(" SELECT 1 FROM products WHERE seller_id=$uid AND LOWER(status)='active' ");
$gift_card_products = (int)countQuery(" SELECT 1 FROM products WHERE seller_id=$uid AND type='gift_card' ");
$mystery_box_products = (int)countQuery(" SELECT 1 FROM products WHERE seller_id=$uid AND type='mystery_box' ");
$digital_products = (int)countQuery(" SELECT 1 FROM products WHERE seller_id=$uid AND type='digital' ");
$withdrawals = getQuery(" SELECT id,amount,status,created_at,tx_signature FROM withdrawals WHERE account_id=$uid ORDER BY id DESC LIMIT 20 ");
$products = getQuery(" SELECT p.id,p.title,p.description,p.price_usd,p.stock,p.type,p.status,p.images,p.category_id,c.name cat_name,p.created_at FROM products p LEFT JOIN categories c ON p.category_id=c.id WHERE p.seller_id=$uid ORDER BY p.id DESC ");
foreach ($products as &$pr) {
    $pid = (int)$pr['id'];
    $usd = (float)($pr['price_usd'] ?? 0);
    $pr['price_usd'] = number_format($usd, 2, '.', '');
    $pr['price_gashy'] = $rate > 0 ? number_format(($usd / $rate), 3, '.', '') : number_format(0, 3, '.', '');
    $pr['stock'] = (int)($pr['stock'] ?? 0);
    $pr['images'] = $pr['images'] ?: '[]';
    $pr['cat_name'] = $pr['cat_name'] ?: '';
    if ($pr['type'] === 'gift_card') {
        $pr['options_count'] = (int)(findQuery(" SELECT COUNT(*) c FROM gift_card_options WHERE product_id=$pid ")['c'] ?? 0);
        $pr['inventory_total'] = (int)(findQuery(" SELECT COUNT(*) c FROM gift_cards WHERE product_id=$pid ")['c'] ?? 0);
        $pr['inventory_sold'] = (int)(findQuery(" SELECT COUNT(*) c FROM gift_cards WHERE product_id=$pid AND is_sold=1 ")['c'] ?? 0);
        $pr['inventory_available'] = max($pr['inventory_total'] - $pr['inventory_sold'], 0);
    } else {
        $pr['options_count'] = 0;
        $pr['inventory_total'] = 0;
        $pr['inventory_sold'] = 0;
        $pr['inventory_available'] = 0;
    }
    if ($pr['type'] === 'mystery_box') {
        $pr['loot_count'] = (int)(findQuery(" SELECT COUNT(*) c FROM mystery_box_loot WHERE box_product_id=$pid ")['c'] ?? 0);
        $pr['loot_probability_total'] = (float)(findQuery(" SELECT COALESCE(SUM(probability),0) t FROM mystery_box_loot WHERE box_product_id=$pid ")['t'] ?? 0);
    } else {
        $pr['loot_count'] = 0;
        $pr['loot_probability_total'] = 0;
    }
}
unset($pr);
$sales = getQuery(" SELECT oi.price_at_purchase,oi.quantity,o.created_at,p.title,a.accountname,o.id order_id,p.id product_id FROM order_items oi JOIN orders o ON oi.order_id=o.id JOIN products p ON oi.product_id=p.id LEFT JOIN accounts a ON o.account_id=a.id WHERE p.seller_id=$uid AND LOWER(o.status)='completed' ORDER BY oi.id DESC LIMIT 20 ");
foreach ($sales as &$sale) {
    $price_at_purchase = (float)($sale['price_at_purchase'] ?? 0);
    $quantity = (int)($sale['quantity'] ?? 0);
    $sale['price_at_purchase'] = number_format($price_at_purchase, 3, '.', '');
    $sale['quantity'] = $quantity;
    $sale['line_total'] = number_format($price_at_purchase * $quantity, 3, '.', '');
    $sale['accountname'] = $sale['accountname'] ?: 'Guest';
}
unset($sale);
$total_units = (int)(findQuery(" SELECT COALESCE(SUM(oi.quantity),0) q FROM order_items oi JOIN products p ON oi.product_id=p.id JOIN orders o ON oi.order_id=o.id WHERE p.seller_id=$uid AND LOWER(o.status)='completed' ")['q'] ?? 0);
$pending_withdrawals = (int)(findQuery(" SELECT COUNT(*) c FROM withdrawals WHERE account_id=$uid AND LOWER(status)='pending' ")['c'] ?? 0);
$pending_withdrawals_amount = (float)(findQuery(" SELECT COALESCE(SUM(amount),0) t FROM withdrawals WHERE account_id=$uid AND LOWER(status)='pending' ")['t'] ?? 0);
$stats = [
    'total_units' => $total_units,
    'total_sales' => number_format($gross, 3, '.', ''),
    'rating' => number_format((float)($seller['rating'] ?? 0), 1, '.', ''),
    'products' => $product_count,
    'active_products' => $active_products,
    'gift_card_products' => $gift_card_products,
    'mystery_box_products' => $mystery_box_products,
    'digital_products' => $digital_products,
    'earnings' => number_format($net_earnings, 3, '.', ''),
    'reward_balance' => number_format($reward_balance, 3, '.', ''),
    'failed_bid' => number_format($failedbid, 3, '.', ''),
    'available' => number_format($available, 3, '.', ''),
    'gross_sales' => number_format($gross, 3, '.', ''),
    'withdrawn' => number_format($withdrawn, 3, '.', ''),
    'pending_withdrawals' => $pending_withdrawals,
    'pending_withdrawals_amount' => number_format($pending_withdrawals_amount, 3, '.', ''),
    'fee_rate' => $fee_percent . '%'
];
encode([
    'status' => true,
    'seller' => $seller,
    'stats' => $stats,
    'products' => $products,
    'sales' => $sales,
    'withdrawals' => $withdrawals,
    'rate' => number_format($rate, 6, '.', '')
]);
