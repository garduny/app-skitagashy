<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT user_id FROM sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) {
    encode(['status' => false]);
}
$uid = $session['user_id'];
$seller = findQuery(" SELECT * FROM sellers WHERE user_id=$uid ");
if ($seller) {
    encode(['status' => true, 'data' => $seller]);
} else {
    encode(['status' => false]); // Not a seller yet
}
