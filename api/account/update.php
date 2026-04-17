<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) encode(['status' => false, 'message' => 'Unauthorized']);
$uid = (int)$session['account_id'];
$accountname = trim((string)request('accountname'));
$email = trim((string)request('email'));
if ($accountname === '' && $email === '') encode(['status' => false, 'message' => 'Nothing to update']);
$current = findQuery(" SELECT accountname,email FROM accounts WHERE id=$uid LIMIT 1 ");
if (!$current) encode(['status' => false, 'message' => 'Account not found']);
$updates = [];
if ($accountname !== '') {
    if (strlen($accountname) < 3 || strlen($accountname) > 30) encode(['status' => false, 'message' => 'Username must be 3-30 chars']);
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $accountname)) encode(['status' => false, 'message' => 'Username only letters numbers underscore']);
    if (strtolower($accountname) !== strtolower($current['accountname'])) {
        $check = findQuery(" SELECT id FROM accounts WHERE LOWER(accountname)=LOWER('$accountname') AND id!=$uid LIMIT 1 ");
        if ($check) encode(['status' => false, 'message' => 'Accountname already taken']);
        $updates[] = " accountname='$accountname' ";
    }
}
if ($email !== '') {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) encode(['status' => false, 'message' => 'Invalid email format']);
    if (strtolower($email) !== strtolower($current['email'])) {
        $check = findQuery(" SELECT id FROM accounts WHERE LOWER(email)=LOWER('$email') AND id!=$uid LIMIT 1 ");
        if ($check) encode(['status' => false, 'message' => 'Email already taken']);
        $updates[] = " email='$email' ";
    }
}
if (!$updates) encode(['status' => false, 'message' => 'No changes']);
$updates[] = " updated_at=NOW() ";
execute(" UPDATE accounts SET " . implode(',', $updates) . " WHERE id=$uid LIMIT 1 ");
encode([
    'status' => true,
    'message' => 'Profile updated successfully',
    'data' => [
        'accountname' => $accountname ?: $current['accountname'],
        'email' => $email ?: $current['email']
    ]
]);
