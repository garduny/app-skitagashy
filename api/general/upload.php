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
if (!isset($_FILES['file'])) {
    encode(['status' => false, 'message' => 'No file uploaded']);
}
$file = $_FILES['file'];
$allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowed)) {
    encode(['status' => false, 'message' => 'Invalid file type']);
}
if ($file['size'] > 5000000) {
    encode(['status' => false, 'message' => 'File too large (Max 5MB)']);
}
$dir = '../../server/uploads/';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}
$name = uniqid('img_', true) . '.' . $ext;
$path = $dir . $name;
if (move_uploaded_file($file['tmp_name'], $path)) {
    $url = '/server/uploads/' . $name;
    encode(['status' => true, 'url' => $url]);
} else {
    encode(['status' => false, 'message' => 'Upload failed']);
}
