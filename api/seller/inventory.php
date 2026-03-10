<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
define('ENC_KEY', 'GashySecretKey2026');
define('ENC_ALGO', 'AES-256-CBC');
function encryptCode($s)
{
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(ENC_ALGO));
    $e = openssl_encrypt($s, ENC_ALGO, ENC_KEY, 0, $iv);
    return base64_encode($e . '::' . $iv);
}
function decryptCode($s)
{
    list($e, $iv) = explode('::', base64_decode($s), 2);
    return openssl_decrypt($e, ENC_ALGO, ENC_KEY, 0, $iv);
}
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) {
    ob_clean();
    encode(['status' => false, 'message' => 'Unauthorized']);
}
$uid = $session['account_id'];
$seller = findQuery(" SELECT account_id FROM sellers WHERE account_id=$uid AND is_approved=1 ");
if (!$seller) {
    ob_clean();
    encode(['status' => false, 'message' => 'Seller permission denied']);
}
$pid = (int)request('product_id');
$prod = findQuery(" SELECT id,type FROM products WHERE id=$pid AND seller_id=$uid AND type IN('digital','gift_card') AND status!='deleted' ");
if (!$prod) {
    ob_clean();
    encode(['status' => false, 'message' => 'Product not found or not eligible']);
}
$options = getQuery(" SELECT id,name,price_usd FROM gift_card_options WHERE product_id=$pid ORDER BY id ASC ");
$oid = (int)request('option_id');
if (count($options) == 1) $oid = $options[0]['id'];
$action = request('action');
if ($action === 'list') {
    $q = " SELECT id,code_enc,pin_enc,is_sold FROM gift_cards WHERE product_id=$pid ";
    if ($oid) $q .= " AND gift_card_option_id=$oid ";
    $q .= " ORDER BY is_sold ASC,id DESC ";
    $codes = getQuery($q);
    $result = [];
    foreach ($codes as $c) {
        $full = decryptCode($c['code_enc']);
        $result[] = ['id' => $c['id'], 'code_tail' => substr($full, -4), 'has_pin' => !empty($c['pin_enc']), 'is_sold' => (int)$c['is_sold']];
    }
    ob_clean();
    encode(['status' => true, 'codes' => $result, 'options' => $options, 'selected_option' => $oid]);
}
if ($action === 'add') {
    $raw = request('codes');
    if (empty(trim($raw))) {
        ob_clean();
        encode(['status' => false, 'message' => 'No codes provided']);
    }
    $lines = explode("\n", $raw);
    $cnt = 0;
    foreach ($lines as $line) {
        $line = trim($line);
        if (!$line) continue;
        $parts = explode('|', $line);
        $cEnc = encryptCode(trim($parts[0]));
        $pEnc = isset($parts[1]) && trim($parts[1]) !== '' ? encryptCode(trim($parts[1])) : null;
        $pVal = $pEnc ? "'$pEnc'" : 'NULL';
        execute(" INSERT INTO gift_cards (product_id,gift_card_option_id,code_enc,pin_enc,is_sold) VALUES ($pid," . ($oid ? $oid : "NULL") . ",'$cEnc',$pVal,0) ");
        $cnt++;
    }
    if ($cnt > 0) execute(" UPDATE products SET stock=stock+$cnt WHERE id=$pid ");
    ob_clean();
    encode(['status' => true, 'message' => "$cnt code(s) imported successfully", 'added' => $cnt]);
}
if ($action === 'delete') {
    $cid = (int)request('code_id');
    $code = findQuery(" SELECT id,is_sold FROM gift_cards WHERE id=$cid AND product_id=$pid ");
    if (!$code) {
        ob_clean();
        encode(['status' => false, 'message' => 'Code not found']);
    }
    if ($code['is_sold']) {
        ob_clean();
        encode(['status' => false, 'message' => 'Cannot delete a sold code']);
    }
    execute(" DELETE FROM gift_cards WHERE id=$cid AND product_id=$pid AND is_sold=0 ");
    execute(" UPDATE products SET stock=IF(stock>0,stock-1,0) WHERE id=$pid ");
    ob_clean();
    encode(['status' => true, 'message' => 'Code deleted']);
}
ob_clean();
encode(['status' => false, 'message' => 'Invalid action']);
