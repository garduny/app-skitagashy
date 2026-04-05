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
$seller = findQuery(" SELECT account_id FROM sellers WHERE account_id=$uid AND is_approved=1 ");
if (!$seller) {
    ob_clean();
    encode(['status' => false, 'message' => 'Seller permission denied']);
}
$pid = (int)request('product_id');
$product = findQuery(" SELECT id,type FROM products WHERE id=$pid AND seller_id=$uid AND status!='deleted' ");
if (!$product) {
    ob_clean();
    encode(['status' => false, 'message' => 'Product not found']);
}
if ($product['type'] !== 'gift_card') {
    ob_clean();
    encode(['status' => false, 'message' => 'Options allowed only for gift cards']);
}
$action = request('action');
if ($action === 'add') {
    $name = secure(trim((string)request('name')));
    $price_usd = (float)request('price_usd');
    if ($name === '') {
        ob_clean();
        encode(['status' => false, 'message' => 'Option name required']);
    }
    if ($price_usd < 0) {
        ob_clean();
        encode(['status' => false, 'message' => 'Invalid price']);
    }
    $exists = findQuery(" SELECT id FROM gift_card_options WHERE product_id=$pid AND name='$name' AND is_active=1 ");
    if ($exists) {
        ob_clean();
        encode(['status' => false, 'message' => 'Option already exists']);
    }
    execute(" INSERT INTO gift_card_options (product_id,name,price_usd,stock,is_active,created_at) VALUES ($pid,'$name',$price_usd,0,1,NOW()) ");
    ob_clean();
    encode(['status' => true]);
}
if ($action === 'edit') {
    $oid = (int)request('option_id');
    $name = secure(trim((string)request('name')));
    $price_usd = (float)request('price_usd');
    $opt = findQuery(" SELECT id FROM gift_card_options WHERE id=$oid AND product_id=$pid ");
    if (!$opt) {
        ob_clean();
        encode(['status' => false, 'message' => 'Option not found']);
    }
    if ($name === '') {
        ob_clean();
        encode(['status' => false, 'message' => 'Option name required']);
    }
    if ($price_usd < 0) {
        ob_clean();
        encode(['status' => false, 'message' => 'Invalid price']);
    }
    execute(" UPDATE gift_card_options SET name='$name',price_usd=$price_usd WHERE id=$oid AND product_id=$pid ");
    ob_clean();
    encode(['status' => true]);
}
if ($action === 'delete') {
    $oid = (int)request('option_id');
    $opt = findQuery(" SELECT id FROM gift_card_options WHERE id=$oid AND product_id=$pid ");
    if (!$opt) {
        ob_clean();
        encode(['status' => false, 'message' => 'Option not found']);
    }
    $hasSold = countQuery(" SELECT 1 FROM gift_cards WHERE gift_card_option_id=$oid AND is_sold=1 ");
    if ($hasSold > 0) {
        ob_clean();
        encode(['status' => false, 'message' => 'Option has sold codes']);
    }
    execute(" DELETE FROM gift_card_options WHERE id=$oid AND product_id=$pid ");
    ob_clean();
    encode(['status' => true]);
}
$rows = getQuery(" SELECT id,name,price_usd,stock FROM gift_card_options WHERE product_id=$pid AND is_active=1 ORDER BY id DESC ");
ob_clean();
encode(['status' => true, 'options' => $rows]);
