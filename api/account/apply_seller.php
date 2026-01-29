<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$account_session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$account_session) {
    encode(['status' => false, 'message' => 'Unauthorized']);
}
$uid = $account_session['account_id'];
$name = request('store_name');
$slug = request('store_slug');
if (!$name || !$slug) {
    encode(['status' => false, 'message' => 'Invalid Data']);
}
$check = findQuery(" SELECT account_id FROM sellers WHERE store_slug='$slug' ");
if ($check) {
    encode(['status' => false, 'message' => 'Store URL already taken']);
}
execute(" INSERT INTO sellers (account_id, store_name, store_slug, is_approved) VALUES ($uid, '$name', '$slug', 0) ");
encode(['status' => true, 'message' => 'Success']);
