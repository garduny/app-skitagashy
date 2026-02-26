<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) {
    encode(['status' => false]);
}
$uid = $session['account_id'];
$mints = getQuery(" SELECT m.*,d.collection_name,d.image_uri FROM nft_mints m JOIN nft_drops d ON m.drop_id=d.id WHERE m.buyer_account_id=$uid AND m.is_burned=0 ORDER BY m.id DESC ");
encode(['status' => true, 'data' => $mints]);
