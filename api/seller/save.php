<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$token = request('token', 'post') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) {
    ob_clean();
    encode(['status' => false, 'message' => 'Unauthorized']);
}
$uid = (int)$session['account_id'];
$seller = findQuery(" SELECT account_id FROM sellers WHERE account_id=$uid AND is_approved=1 ");
if (!$seller) {
    ob_clean();
    encode(['status' => false, 'message' => 'Seller permission denied']);
}
$action = trim((string)request('action', 'post'));
$validTypes = ['digital', 'gift_card', 'mystery_box', 'nft', 'physical'];
if ($action === 'delete') {
    $pid = (int)request('id', 'post');
    $prod = findQuery(" SELECT id,type FROM products WHERE id=$pid AND seller_id=$uid AND status!='deleted' ");
    if (!$prod) {
        ob_clean();
        encode(['status' => false, 'message' => 'Product not found']);
    }
    execute(" UPDATE products SET status='inactive' WHERE id=$pid AND seller_id=$uid ");
    ob_clean();
    encode(['status' => true, 'message' => 'Product removed']);
}
if ($action === 'toggle_status') {
    $pid = (int)request('id', 'post');
    $prod = findQuery(" SELECT id,status FROM products WHERE id=$pid AND seller_id=$uid ");
    if (!$prod) {
        ob_clean();
        encode(['status' => false, 'message' => 'Product not found']);
    }
    $next = strtolower($prod['status']) === 'active' ? 'inactive' : 'active';
    execute(" UPDATE products SET status='$next' WHERE id=$pid AND seller_id=$uid ");
    ob_clean();
    encode(['status' => true, 'message' => 'Status updated', 'new_status' => $next]);
}
if ($action === 'save') {
    $id = (int)request('id', 'post');
    $title = secure(trim((string)request('title', 'post')));
    $price_usd = (float)request('price', 'post');
    $stock = (int)request('stock', 'post');
    $type = secure(trim((string)request('type', 'post')));
    $cat = (int)request('category_id', 'post');
    $desc = secure(trim((string)request('description', 'post')));
    $status = strtolower(trim((string)request('status', 'post')));
    if ($status === '') $status = 'active';
    if (!in_array($status, ['active', 'inactive'])) $status = 'active';
    if ($title === '') {
        ob_clean();
        encode(['status' => false, 'message' => 'Title is required']);
    }
    if (mb_strlen($title) > 180) {
        ob_clean();
        encode(['status' => false, 'message' => 'Title too long']);
    }
    if ($price_usd < 0) {
        ob_clean();
        encode(['status' => false, 'message' => 'Invalid price']);
    }
    if ($stock < 0) {
        ob_clean();
        encode(['status' => false, 'message' => 'Invalid stock']);
    }
    if (!in_array($type, $validTypes)) {
        ob_clean();
        encode(['status' => false, 'message' => 'Invalid product type']);
    }
    $catRow = findQuery(" SELECT id FROM categories WHERE id=$cat ");
    if (!$catRow) {
        ob_clean();
        encode(['status' => false, 'message' => 'Invalid category']);
    }
    $dup = findQuery(" SELECT id FROM products WHERE seller_id=$uid AND title='$title' AND id!=$id LIMIT 1 ");
    if ($dup) {
        ob_clean();
        encode(['status' => false, 'message' => 'Product title already exists']);
    }
    $uploadPath = '../../server/uploads/products/';
    $dbPath = '/server/uploads/products/';
    $newImage = upload('image', $uploadPath);
    $oldImg = '';
    if ($id > 0) {
        $old = findQuery(" SELECT images FROM products WHERE id=$id AND seller_id=$uid ");
        if (!$old) {
            ob_clean();
            encode(['status' => false, 'message' => 'Product not found']);
        }
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
        execute(" UPDATE products SET title='$title',price_usd=$price_usd,stock=$stock,type='$type',category_id=$cat,description='$desc',images='$imgs',status='$status' WHERE id=$id AND seller_id=$uid ");
        ob_clean();
        encode(['status' => true, 'message' => 'Product updated']);
    }
    $slugBase = trim(strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $title)), '-');
    if ($slugBase === '') $slugBase = 'product';
    $slug = $slugBase;
    $i = 1;
    while (findQuery(" SELECT id FROM products WHERE slug='$slug' LIMIT 1 ")) {
        $i++;
        $slug = $slugBase . '-' . $i;
    }
    execute(" INSERT INTO products (seller_id,category_id,title,slug,description,price_usd,stock,type,images,status) VALUES ($uid,$cat,'$title','$slug','$desc',$price_usd,$stock,'$type','$imgs','$status') ");
    ob_clean();
    encode(['status' => true, 'message' => 'Product created']);
}
ob_clean();
encode(['status' => false, 'message' => 'Invalid action']);
