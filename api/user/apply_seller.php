<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT user_id FROM sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) {
    encode(['status' => false, 'message' => 'Unauthorized']);
}
$uid = $session['user_id'];
$name = request('store_name');
$slug = request('store_slug');

if (!$name || !$slug) {
    encode(['status' => false, 'message' => 'Invalid Data']);
}

// Check Slug Uniqueness
$check = findQuery(" SELECT user_id FROM sellers WHERE store_slug='$slug' ");
if ($check) {
    encode(['status' => false, 'message' => 'Store URL already taken']);
}

execute(" INSERT INTO sellers (user_id, store_name, store_slug, is_approved) VALUES ($uid, '$name', '$slug', 0) ");
// Note: is_approved = 0 means pending. 1 means active.

encode(['status' => true, 'message' => 'Success']);
