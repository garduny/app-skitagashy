<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
if (!hasRequest('wallet_address')) {
    encode(['status' => false, 'message' => 'Wallet address required']);
}
$wallet = request('wallet_address');
$ref_code = request('referral_code');
$sig = request('signature');
$msg = request('message');
$account = findQuery(" SELECT id,role,is_verified,tier,is_banned FROM accounts WHERE wallet_address='$wallet' ");
if ($account) {
    if ($account['is_banned'] == 1) {
        encode(['status' => false, 'message' => 'Account Banned - Contact Support']);
    }
    $uid = $account['id'];
    $role = $account['role'];
    $tier = $account['tier'];
} else {
    execute(" START TRANSACTION ");
    try {
        $nonce = bin2hex(random_bytes(16));
        $my_ref = substr(md5(uniqid($wallet, true)), 0, 8);
        execute(" INSERT INTO accounts (wallet_address,nonce,role,my_referral_code,created_at) VALUES ('$wallet','$nonce','account','$my_ref',NOW()) ");
        $last = findQuery(" SELECT LAST_INSERT_ID() as id ");
        $uid = $last['id'];
        $role = 'account';
        $tier = 'bronze';
        if ($ref_code) {
            $referrer = findQuery(" SELECT id FROM accounts WHERE my_referral_code='$ref_code' ");
            if ($referrer) {
                $rid = $referrer['id'];
                execute(" INSERT INTO account_referrals (referrer_id,referee_id,created_at) VALUES ($rid,$uid,NOW()) ");
            }
        }
        execute(" COMMIT ");
    } catch (Exception $e) {
        execute(" ROLLBACK ");
        encode(['status' => false, 'message' => 'Registration Failed']);
    }
}
$token = bin2hex(random_bytes(32));
$exp = date('Y-m-d H:i:s', strtotime('+7 days'));
$ip = $_SERVER['REMOTE_ADDR'];
$ua = substr($_SERVER['HTTP_ACCOUNT_AGENT'] ?? 'Unknown', 0, 255);
execute(" INSERT INTO account_sessions (account_id,token,ip_address,account_agent,expires_at) VALUES ($uid,'$token','$ip','$ua','$exp') ");
encode([
    'status' => true,
    'token' => $token,
    'account' => [
        'id' => $uid,
        'wallet_address' => $wallet,
        'role' => $role,
        'tier' => $tier
    ]
]);
