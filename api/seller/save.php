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
$seller = findQuery(" SELECT * FROM sellers WHERE account_id=$uid AND is_approved=1 ");
if (!$seller) {
    encode(['status' => false, 'message' => 'Seller permission denied']);
}
$action = request('action');
if ($action === 'delete') {
    $pid = (int)request('id');
    execute(" UPDATE products SET status='inactive' WHERE id=$pid AND seller_id=$uid ");
    encode(['status' => true, 'message' => 'Product removed']);
}
if ($action === 'save') {
    $id = (int)request('id');
    $title = secure(request('title'));
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title))) . '-' . rand(100, 999);
    $price = (float)request('price');
    $stock = (int)request('stock');
    $type = secure(request('type'));
    $cat = (int)request('category_id');
    $desc = secure(request('description'));
    $imgs = json_encode(array_values(array_filter(explode("\n", request('images')))));
    if ($id > 0) {
        execute(" UPDATE products SET title='$title',price_gashy=$price,stock=$stock,type='$type',category_id=$cat,description='$desc',images='$imgs' WHERE id=$id AND seller_id=$uid ");
        encode(['status' => true, 'message' => 'Product updated']);
    } else {
        execute(" INSERT INTO products (seller_id,category_id,title,slug,description,price_gashy,stock,type,images,status) VALUES ($uid,$cat,'$title','$slug','$desc',$price,$stock,'$type','$imgs','active') ");
        encode(['status' => true, 'message' => 'Product created']);
    }
}
encode(['status' => false, 'message' => 'Invalid action']);
