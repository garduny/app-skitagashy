<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) {
    ob_clean();
    encode(['status' => false, 'message' => 'Unauthorized']);
}
$uid = (int)$session['account_id'];
$seller = findQuery(" SELECT account_id FROM sellers WHERE account_id=$uid AND is_approved=1 LIMIT 1 ");
if (!$seller) {
    ob_clean();
    encode(['status' => false, 'message' => 'Seller permission denied']);
}
$rate = (float)toGashy();
$box_id = (int)request('box_id');
$box = findQuery(" SELECT id,title,price_usd,stock FROM products WHERE id=$box_id AND seller_id=$uid AND type='mystery_box' AND status!='deleted' LIMIT 1 ");
if (!$box) {
    ob_clean();
    encode(['status' => false, 'message' => 'Mystery box not found']);
}
$action = trim((string)request('action'));
if ($action === 'list') {
    $loot = getQuery(" SELECT l.*,p.title FROM mystery_box_loot l LEFT JOIN products p ON l.reward_product_id=p.id WHERE l.box_product_id=$box_id ORDER BY l.probability DESC,l.id DESC ");
    foreach ($loot as &$row) {
        $row['reward_amount'] = number_format((float)($row['reward_amount'] ?? 0), 3, '.', '');
        $row['probability'] = number_format((float)($row['probability'] ?? 0), 2, '.', '');
        $row['title'] = $row['title'] ?: '';
    }
    unset($row);
    $products = getQuery(" SELECT id,title,price_usd,type FROM products WHERE seller_id=$uid AND status='active' AND type!='mystery_box' ORDER BY title ASC ");
    foreach ($products as &$p) {
        $usd = (float)($p['price_usd'] ?? 0);
        $p['price_usd'] = number_format($usd, 2, '.', '');
        $p['price_gashy'] = $rate > 0 ? number_format(($usd / $rate), 3, '.', '') : number_format(0, 3, '.', '');
    }
    unset($p);
    $total_probability = (float)(findQuery(" SELECT COALESCE(SUM(probability),0) t FROM mystery_box_loot WHERE box_product_id=$box_id ")['t'] ?? 0);
    ob_clean();
    encode([
        'status' => true,
        'loot' => $loot,
        'products' => $products,
        'box' => [
            'id' => (int)$box['id'],
            'title' => $box['title'],
            'price_usd' => number_format((float)$box['price_usd'], 2, '.', ''),
            'price_gashy' => $rate > 0 ? number_format(((float)$box['price_usd'] / $rate), 3, '.', '') : number_format(0, 3, '.', ''),
            'stock' => (int)$box['stock']
        ],
        'meta' => [
            'total_probability' => number_format($total_probability, 2, '.', ''),
            'remaining_probability' => number_format(max(100 - $total_probability, 0), 2, '.', '')
        ]
    ]);
}
if ($action === 'add') {
    $reward_product_id = request('reward_product_id') !== null && request('reward_product_id') !== '' ? (int)request('reward_product_id') : 0;
    $reward_option_id = request('reward_option_id') !== null && request('reward_option_id') !== '' ? (int)request('reward_option_id') : 0;
    $reward_amount = (float)request('reward_amount');
    $probability = (float)request('probability');
    $rarity = secure(trim((string)request('rarity')));
    $allowed_rarities = ['common', 'rare', 'epic', 'legendary'];
    if (!in_array($rarity, $allowed_rarities)) {
        ob_clean();
        encode(['status' => false, 'message' => 'Invalid rarity value']);
    }
    if ($probability <= 0 || $probability > 100) {
        ob_clean();
        encode(['status' => false, 'message' => 'Probability must be between 0.01 and 100']);
    }
    if ($reward_amount < 0) {
        ob_clean();
        encode(['status' => false, 'message' => 'Invalid reward amount']);
    }
    if ($reward_product_id <= 0 && $reward_amount <= 0) {
        ob_clean();
        encode(['status' => false, 'message' => 'Enter reward amount for token reward']);
    }
    if ($reward_product_id > 0) {
        $rp = findQuery(" SELECT id,type,status FROM products WHERE id=$reward_product_id AND seller_id=$uid AND status='active' LIMIT 1 ");
        if (!$rp) {
            ob_clean();
            encode(['status' => false, 'message' => 'Reward product not found or not yours']);
        }
        if ($reward_product_id === $box_id) {
            ob_clean();
            encode(['status' => false, 'message' => 'Mystery box cannot reward itself']);
        }
        if ($reward_option_id > 0) {
            $opt = findQuery(" SELECT id FROM gift_card_options WHERE id=$reward_option_id AND product_id=$reward_product_id AND is_active=1 LIMIT 1 ");
            if (!$opt) {
                ob_clean();
                encode(['status' => false, 'message' => 'Reward option not found']);
            }
        } else {
            $reward_option_id = 0;
        }
    } else {
        $reward_product_id = 0;
        $reward_option_id = 0;
    }
    $current_total = (float)(findQuery(" SELECT COALESCE(SUM(probability),0) t FROM mystery_box_loot WHERE box_product_id=$box_id ")['t'] ?? 0);
    if (($current_total + $probability) > 100) {
        ob_clean();
        encode(['status' => false, 'message' => 'Total probability would exceed 100% (currently at ' . number_format($current_total, 2) . '%)']);
    }
    $rpVal = $reward_product_id > 0 ? $reward_product_id : 'NULL';
    $roVal = $reward_option_id > 0 ? $reward_option_id : 'NULL';
    execute(" INSERT INTO mystery_box_loot (box_product_id,reward_product_id,reward_option_id,reward_amount,probability,rarity) VALUES ($box_id,$rpVal,$roVal,$reward_amount,$probability,'$rarity') ");
    ob_clean();
    encode([
        'status' => true,
        'message' => 'Loot entry added',
        'meta' => [
            'new_total_probability' => number_format($current_total + $probability, 2, '.', '')
        ]
    ]);
}
if ($action === 'delete') {
    $loot_id = (int)request('loot_id');
    $loot = findQuery(" SELECT id FROM mystery_box_loot WHERE id=$loot_id AND box_product_id=$box_id LIMIT 1 ");
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
