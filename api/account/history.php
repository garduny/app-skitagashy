<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) {
    encode(['status' => false, 'message' => 'Unauthorized']);
}
$uid = $session['account_id'];
$type = request('type');
$where = "WHERE account_id=$uid";
if ($type) {
    $where .= " AND type='$type' ";
}
$history = getQuery(" SELECT * FROM transactions $where ORDER BY id DESC LIMIT 50 ");
encode(['status' => true, 'data' => $history]);
