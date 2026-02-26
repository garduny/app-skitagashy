<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
if (!$token) {
    encode(['status' => false, 'message' => 'Token required']);
}
$account_session = findQuery(" SELECT s.account_id,s.expires_at,u.wallet_address,u.role,u.tier,u.is_banned FROM account_sessions s JOIN accounts u ON s.account_id=u.id WHERE s.token='$token' ");
if (!$account_session) {
    encode(['status' => false, 'message' => 'Invalid Session']);
}
if (strtotime($account_session['expires_at']) < time()) {
    encode(['status' => false, 'message' => 'Session Expired']);
}
if ($account_session['is_banned'] == 1) {
    encode(['status' => false, 'message' => 'Account Banned']);
}
encode([
    'status' => true,
    'account' => [
        'id' => $account_session['account_id'],
        'wallet' => $account_session['wallet_address'],
        'role' => $account_session['role'],
        'tier' => $account_session['tier']
    ]
]);
