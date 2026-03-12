<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) encode(['status' => false]);
$uid = $session['account_id'];
$pid = (int)request('product_id');
$action = request('action');
if ($action === 'add') {
    $name = secure(request('name'));
    $price = (float)request('price_usd');
    execute(" INSERT INTO gift_card_options(product_id,name,price_usd,stock,is_active,created_at) VALUES($pid,'$name',$price,0,1,NOW()) ");
    encode(['status' => true]);
}
$rows = getQuery(" SELECT id,name,price_usd FROM gift_card_options WHERE product_id=$pid AND is_active=1 ORDER BY id DESC ");
encode(['status' => true, 'options' => $rows]);
