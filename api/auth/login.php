<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}

// 1. Get JSON Input (Fix for JS fetch)
$json = file_get_contents('php://input');
$data = json_decode($json, true) ?? [];

// 2. Extract Variables
$wallet = $data['wallet_address'] ?? null;
$sig    = $data['signature'] ?? null;
$msg    = $data['message'] ?? null;
$ref    = $data['referral_code'] ?? null;

// 3. Validation
if (!$wallet) {
    encode(['status' => false, 'message' => 'Wallet address required']);
}

// 4. Find or Create User
$account = findQuery(" SELECT id,role,is_verified,tier,is_banned FROM accounts WHERE wallet_address='$wallet' "); // Note: Table is 'accounts' now

if ($account) {
    if ($account['is_banned'] == 1) {
        encode(['status' => false, 'message' => 'Account Banned']);
    }
    $uid = $account['id'];
    $role = $account['role'];
    $tier = $account['tier'];
} else {
    // Register
    execute(" START TRANSACTION ");
    try {
        $nonce = bin2hex(random_bytes(16));
        $my_ref = substr(md5(uniqid($wallet, true)), 0, 8);

        execute(" INSERT INTO accounts (wallet_address,nonce,role,my_referral_code,created_at) VALUES ('$wallet','$nonce','account','$my_ref',NOW()) ");
        $last = findQuery(" SELECT LAST_INSERT_ID() as id ");
        $uid = $last['id'];
        $role = 'account';
        $tier = 'bronze';

        if ($ref) {
            $referrer = findQuery(" SELECT id FROM accounts WHERE my_referral_code='$ref' ");
            if ($referrer) {
                $rid = $referrer['id'];
                execute(" INSERT INTO account_referrals (referrer_account_id, referee_account_id, created_at) VALUES ($rid, $uid, NOW()) ");
            }
        }
        execute(" COMMIT ");
    } catch (Exception $e) {
        execute(" ROLLBACK ");
        encode(['status' => false, 'message' => 'Registration Failed: ' . $e->getMessage()]);
    }
}

// 5. Create Session
$token = bin2hex(random_bytes(32));
$exp = date('Y-m-d H:i:s', strtotime('+7 days'));
$ip = $_SERVER['REMOTE_ADDR'];
$ua = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255);

// Note: Table is 'account_sessions'
execute(" INSERT INTO account_sessions (account_id, token, ip_address, account_agent, expires_at) VALUES ($uid, '$token', '$ip', '$ua', '$exp') ");

encode([
    'status' => true,
    'token' => $token,
    'account' => [ // JS expects 'account' object
        'id' => $uid,
        'wallet_address' => $wallet,
        'role' => $role,
        'tier' => $tier
    ]
]);
