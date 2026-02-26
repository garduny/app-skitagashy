<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$token = request('token', 'post') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW()");
if (!$session) {
    ob_clean();
    encode(['status' => false, 'message' => 'Unauthorized']);
}
$uid = $session['account_id'];
$seller = findQuery(" SELECT account_id FROM sellers WHERE account_id=$uid AND is_approved=1");
if (!$seller) {
    ob_clean();
    encode(['status' => false, 'message' => 'Seller permission denied']);
}
$action = request('action', 'post');
if ($action === 'delete') {
    $pid = (int)request('id', 'post');
    execute("UPDATE products SET status='inactive' WHERE id=$pid AND seller_id=$uid");
    ob_clean();
    encode(['status' => true, 'message' => 'Product removed']);
}
if ($action === 'save') {
    $id = (int)request('id', 'post');
    $title = secure(request('title', 'post'));
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title))) . '-' . rand(100, 999);
    $price = (float)request('price', 'post');
    $stock = (int)request('stock', 'post');
    $type = secure(request('type', 'post'));
    $cat = (int)request('category_id', 'post');
    $desc = secure(request('description', 'post'));
    $uploadPath = '../../server/uploads/products/';
    $dbPath = '/server/uploads/products/';
    $newImage = upload('image', $uploadPath);
    $oldImg = '';
    if ($id > 0) {
        $old = findQuery(" SELECT images FROM products WHERE id=$id AND seller_id=$uid");
        if (!empty($old['images'])) {
            $tmp = json_decode($old['images'], true);
            $oldImg = $tmp[0] ?? '';
        }
    }
    $finalImg = $newImage ? $dbPath . $newImage : $oldImg;
    if ($newImage && $oldImg) {
        $f = '../../' . ltrim($oldImg, '/');
        if (file_exists($f)) @unlink($f);
    }
    $imgs = $finalImg ? json_encode([$finalImg]) : json_encode([]);
    if ($id > 0) {
        execute("UPDATE products SET title='$title',price_gashy=$price,stock=$stock,type='$type',category_id=$cat,description='$desc',images='$imgs' WHERE id=$id AND seller_id=$uid");
        ob_clean();
        encode(['status' => true, 'message' => 'Product updated']);
    } else {
        execute("INSERT INTO products (seller_id,category_id,title,slug,description,price_gashy,stock,type,images,status) VALUES ($uid,$cat,'$title','$slug','$desc',$price,$stock,'$type','$imgs','active')");
        ob_clean();
        encode(['status' => true, 'message' => 'Product created']);
    }
}
ob_clean();
encode(['status' => false, 'message' => 'Invalid action']);
