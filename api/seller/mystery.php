<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$token   = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) {
    ob_clean();
    encode(['status' => false, 'message' => 'Unauthorized']);
}
$uid    = $session['account_id'];
$seller = findQuery(" SELECT account_id FROM sellers WHERE account_id=$uid AND is_approved=1 ");
if (!$seller) {
    ob_clean();
    encode(['status' => false, 'message' => 'Seller permission denied']);
}
$box_id = (int)request('box_id');
$box    = findQuery(" SELECT * FROM products WHERE id=$box_id AND seller_id=$uid AND type='mystery_box' AND status!='deleted' ");
if (!$box) {
    ob_clean();
    encode(['status' => false, 'message' => 'Mystery box not found']);
}
$action = request('action');
if ($action === 'list') {
    $loot = getQuery("
        SELECT l.*, p.title
        FROM mystery_box_loot l
        LEFT JOIN products p ON l.reward_product_id = p.id
        WHERE l.box_product_id = $box_id
        ORDER BY l.probability ASC
    ");
    $products = getQuery("
        SELECT id, title, price_gashy, type
        FROM products
        WHERE seller_id = $uid
          AND status = 'active'
          AND type != 'mystery_box'
        ORDER BY title ASC
    ");
    ob_clean();
    encode([
        'status'   => true,
        'loot'     => $loot,
        'products' => $products,
        'box'      => [
            'id'    => $box['id'],
            'title' => $box['title'],
            'price' => $box['price_gashy'],
            'stock' => $box['stock'],
        ]
    ]);
}
if ($action === 'add') {
    $reward_product_id = !empty(request('reward_product_id')) ? (int)request('reward_product_id') : null;
    $reward_amount     = (float)request('reward_amount');
    $probability       = (float)request('probability');
    $rarity            = secure(request('rarity'));
    $allowed_rarities = ['common', 'rare', 'epic', 'legendary'];
    if (!in_array($rarity, $allowed_rarities)) {
        ob_clean();
        encode(['status' => false, 'message' => 'Invalid rarity value']);
    }
    if ($probability <= 0 || $probability > 100) {
        ob_clean();
        encode(['status' => false, 'message' => 'Probability must be between 0.01 and 100']);
    }
    $current_total = countQuery(" SELECT SUM(probability) FROM mystery_box_loot WHERE box_product_id=$box_id ");
    if (($current_total + $probability) > 100) {
        ob_clean();
        encode([
            'status'  => false,
            'message' => 'Total probability would exceed 100% (currently at ' . number_format($current_total, 2) . '%)'
        ]);
    }
    if ($reward_product_id) {
        $rp = findQuery(" SELECT id FROM products WHERE id=$reward_product_id AND seller_id=$uid AND status='active' ");
        if (!$rp) {
            ob_clean();
            encode(['status' => false, 'message' => 'Reward product not found or not yours']);
        }
    }
    $rpVal = $reward_product_id ? $reward_product_id : 'NULL';
    execute("
        INSERT INTO mystery_box_loot (box_product_id, reward_product_id, reward_amount, probability, rarity)
        VALUES ($box_id, $rpVal, $reward_amount, $probability, '$rarity')
    ");
    ob_clean();
    encode(['status' => true, 'message' => 'Loot entry added']);
}
if ($action === 'delete') {
    $loot_id = (int)request('loot_id');
    $loot    = findQuery(" SELECT id FROM mystery_box_loot WHERE id=$loot_id AND box_product_id=$box_id ");
    if (!$loot) {
        ob_clean();
        encode(['status' => false, 'message' => 'Loot entry not found']);
    }
    execute(" DELETE FROM mystery_box_loot WHERE id=$loot_id AND box_product_id=$box_id ");
    ob_clean();
    encode(['status' => true, 'message' => 'Loot entry removed']);
}
ob_clean();
encode(['status' => false, 'message' => 'Invalid action']);
