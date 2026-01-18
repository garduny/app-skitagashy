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
$username = request('username');
$email = request('email');
if (!$username && !$email) {
    encode(['status' => false, 'message' => 'Nothing to update']);
}
$updates = [];
if ($username) {
    $check = findQuery(" SELECT id FROM users WHERE username='$username' AND id!=$uid ");
    if ($check) {
        encode(['status' => false, 'message' => 'Username already taken']);
    }
    $updates[] = "username='$username'";
}
if ($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        encode(['status' => false, 'message' => 'Invalid email format']);
    }
    $check = findQuery(" SELECT id FROM users WHERE email='$email' AND id!=$uid ");
    if ($check) {
        encode(['status' => false, 'message' => 'Email already taken']);
    }
    $updates[] = "email='$email'";
}
if (empty($updates)) {
    encode(['status' => false, 'message' => 'No changes']);
}
$sql = " UPDATE users SET " . implode(', ', $updates) . " WHERE id=$uid ";
execute($sql);
encode(['status' => true, 'message' => 'Profile updated successfully']);
