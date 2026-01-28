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
$accountname = request('accountname');
$email = request('email');
if (!$accountname && !$email) {
    encode(['status' => false, 'message' => 'Nothing to update']);
}
$updates = [];
if ($accountname) {
    $check = findQuery(" SELECT id FROM accounts WHERE accountname='$accountname' AND id!=$uid ");
    if ($check) {
        encode(['status' => false, 'message' => 'Accountname already taken']);
    }
    $updates[] = "accountname='$accountname'";
}
if ($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        encode(['status' => false, 'message' => 'Invalid email format']);
    }
    $check = findQuery(" SELECT id FROM accounts WHERE email='$email' AND id!=$uid ");
    if ($check) {
        encode(['status' => false, 'message' => 'Email already taken']);
    }
    $updates[] = "email='$email'";
}
if (empty($updates)) {
    encode(['status' => false, 'message' => 'No changes']);
}
$sql = " UPDATE accounts SET " . implode(', ', $updates) . " WHERE id=$uid ";
execute($sql);
encode(['status' => true, 'message' => 'Profile updated successfully']);
