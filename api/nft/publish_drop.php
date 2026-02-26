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
$seller = findQuery(" SELECT id FROM sellers WHERE account_id=$uid AND is_approved=1 ");
if (!$seller) {
    encode(['status' => false, 'message' => 'Only approved sellers can publish drops']);
}
$json = file_get_contents('php://input');
$data = json_decode($json, true);
$name = secure($data['collection_name'] ?? '');
$symbol = secure($data['symbol'] ?? '');
$desc = secure($data['description'] ?? '');
$img = secure($data['image_uri'] ?? '');
$price = (float)($data['price_gashy'] ?? 0);
$supply = (int)($data['max_supply'] ?? 0);
$start = secure($data['start_time'] ?? '');
$end = secure($data['end_time'] ?? '');
$royalty = (int)($data['royalties'] ?? 0);
if (!$name || !$symbol || !$img || $price <= 0 || $supply <= 0 || !$start || !$end) {
    encode(['status' => false, 'message' => 'Invalid input data']);
}
execute(" INSERT INTO nft_drops (seller_account_id,collection_name,symbol,description,price_gashy,max_supply,royalties,start_time,end_time,image_uri,status) VALUES ($uid,'$name','$symbol','$desc',$price,$supply,$royalty,'$start','$end','$img','pending') ");
encode(['status' => true, 'message' => 'Drop published and pending admin approval']);
