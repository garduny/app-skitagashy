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
$session = findQuery(" SELECT s.user_id,s.expires_at,u.wallet_address,u.role,u.tier,u.is_banned FROM sessions s JOIN users u ON s.user_id=u.id WHERE s.token='$token' ");
if (!$session) {
    encode(['status' => false, 'message' => 'Invalid Session']);
}
if (strtotime($session['expires_at']) < time()) {
    encode(['status' => false, 'message' => 'Session Expired']);
}
if ($session['is_banned'] == 1) {
    encode(['status' => false, 'message' => 'Account Banned']);
}
encode([
    'status' => true,
    'user' => [
        'id' => $session['user_id'],
        'wallet' => $session['wallet_address'],
        'role' => $session['role'],
        'tier' => $session['tier']
    ]
]);
