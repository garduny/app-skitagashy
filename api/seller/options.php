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
    execute(" INSERT INTO gift_card_options(product_id,name)VALUES($pid,'$name') ");
    encode(['status' => true]);
}
$rows = getQuery(" SELECT id,name FROM gift_card_options WHERE product_id=$pid ORDER BY id DESC ");
encode(['status' => true, 'options' => $rows]);
