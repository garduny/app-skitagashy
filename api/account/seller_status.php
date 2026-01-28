<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$account_session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$account_session) {
    encode(['status' => false]);
}
$uid = $account_session['account_id'];
$seller = findQuery(" SELECT * FROM sellers WHERE account_id=$uid ");
if ($seller) {
    encode(['status' => true, 'data' => $seller]);
} else {
    encode(['status' => false]); // Not a seller yet
}
