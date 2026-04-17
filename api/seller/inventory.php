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
    $decoded = base64_decode($s);
    if (!$decoded || strpos($decoded, '::') === false) return '';
    list($e, $iv) = explode('::', $decoded, 2);
    return openssl_decrypt($e, ENC_ALGO, ENC_KEY, 0, $iv);
}
function syncGiftCardStocks($pid)
{
    $pid = (int)$pid;
    $options = getQuery(" SELECT id FROM gift_card_options WHERE product_id=$pid AND is_active=1 ");
    foreach ($options as $opt) {
        $oid = (int)$opt['id'];
        $available = countQuery(" SELECT 1 FROM gift_cards WHERE product_id=$pid AND gift_card_option_id=$oid AND is_sold=0 ");
        execute(" UPDATE gift_card_options SET stock=$available WHERE id=$oid AND product_id=$pid ");
    }
    $defaultAvailable = countQuery(" SELECT 1 FROM gift_cards WHERE product_id=$pid AND gift_card_option_id IS NULL AND is_sold=0 ");
    $optionsAvailable = findQuery(" SELECT COALESCE(SUM(stock),0) total FROM gift_card_options WHERE product_id=$pid AND is_active=1 ")['total'] ?? 0;
    $productStock = (int)$defaultAvailable + (int)$optionsAvailable;
    execute(" UPDATE products SET stock=$productStock WHERE id=$pid ");
}
function parseInventoryLines($raw)
{
    $lines = preg_split('/\r\n|\r|\n/', (string)$raw);
    $items = [];
    $seen = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') continue;
        $parts = explode('|', $line, 2);
        $code = trim($parts[0] ?? '');
        $pin = trim($parts[1] ?? '');
        if ($code === '') continue;
        $key = strtolower($code . '|' . $pin);
        if (isset($seen[$key])) continue;
        $seen[$key] = true;
        $items[] = ['code' => $code, 'pin' => $pin];
    }
    return $items;
}
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
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
$pid = (int)request('product_id');
$prod = findQuery(" SELECT id,title,type,stock FROM products WHERE id=$pid AND seller_id=$uid AND type IN ('digital','gift_card') AND status!='deleted' LIMIT 1 ");
if (!$prod) {
    ob_clean();
    encode(['status' => false, 'message' => 'Product not found or not eligible']);
}
$options = getQuery(" SELECT id,name,price_usd,stock FROM gift_card_options WHERE product_id=$pid AND is_active=1 ORDER BY id ASC ");
$oidRaw = request('option_id');
$oid = $oidRaw !== null && $oidRaw !== '' ? (int)$oidRaw : 0;
if ($oid > 0) {
    $option = findQuery(" SELECT id FROM gift_card_options WHERE id=$oid AND product_id=$pid AND is_active=1 LIMIT 1 ");
    if (!$option) {
        ob_clean();
        encode(['status' => false, 'message' => 'Option not found']);
    }
}
if ($oid === 0 && count($options) === 1) $oid = (int)$options[0]['id'];
$action = trim((string)request('action'));
if ($action === 'list') {
    $q = " SELECT id,code_enc,pin_enc,is_sold,gift_card_option_id,created_at FROM gift_cards WHERE product_id=$pid ";
    if ($oid > 0) $q .= " AND gift_card_option_id=$oid ";
    elseif (count($options) > 0) $q .= " AND gift_card_option_id IS NULL ";
    $q .= " ORDER BY is_sold ASC,id DESC ";
    $codes = getQuery($q);
    $result = [];
    foreach ($codes as $c) {
        $full = decryptCode($c['code_enc']);
        $result[] = [
            'id' => (int)$c['id'],
            'code_tail' => substr((string)$full, -4),
            'has_pin' => !empty($c['pin_enc']),
            'is_sold' => (int)$c['is_sold'],
            'gift_card_option_id' => $c['gift_card_option_id'] ? (int)$c['gift_card_option_id'] : null,
            'created_at' => $c['created_at']
        ];
    }
    syncGiftCardStocks($pid);
    $freshOptions = getQuery(" SELECT id,name,price_usd,stock FROM gift_card_options WHERE product_id=$pid AND is_active=1 ORDER BY id ASC ");
    $product = findQuery(" SELECT stock FROM products WHERE id=$pid LIMIT 1 ");
    $soldCount = count(array_filter($result, function ($r) {
        return (int)$r['is_sold'] === 1;
    }));
    ob_clean();
    encode([
        'status' => true,
        'product' => ['id' => $pid, 'title' => $prod['title'], 'type' => $prod['type']],
        'codes' => $result,
        'options' => $freshOptions,
        'selected_option' => $oid,
        'product_stock' => (int)($product['stock'] ?? 0),
        'stats' => [
            'total' => count($result),
            'sold' => $soldCount,
            'available' => count($result) - $soldCount
        ]
    ]);
}
if ($action === 'add') {
    $raw = (string)request('codes');
    $items = parseInventoryLines($raw);
    if (!$items) {
        ob_clean();
        encode(['status' => false, 'message' => 'No valid codes provided']);
    }
    $existingRows = getQuery(" SELECT id,code_enc,pin_enc FROM gift_cards WHERE product_id=$pid ");
    $existing = [];
    foreach ($existingRows as $row) {
        $existing[strtolower(decryptCode($row['code_enc']) . '|' . decryptCode($row['pin_enc']))] = true;
    }
    $cnt = 0;
    foreach ($items as $item) {
        $key = strtolower($item['code'] . '|' . $item['pin']);
        if (isset($existing[$key])) continue;
        $cEnc = encryptCode($item['code']);
        $pEnc = $item['pin'] !== '' ? encryptCode($item['pin']) : null;
        $pVal = $pEnc ? "'$pEnc'" : 'NULL';
        $oidSql = $oid > 0 ? $oid : "NULL";
        execute(" INSERT INTO gift_cards (product_id,gift_card_option_id,code_enc,pin_enc,is_sold) VALUES ($pid,$oidSql,'$cEnc',$pVal,0) ");
        $existing[$key] = true;
        $cnt++;
    }
    if ($cnt < 1) {
        ob_clean();
        encode(['status' => false, 'message' => 'All codes already exist or invalid']);
    }
    syncGiftCardStocks($pid);
    ob_clean();
    encode(['status' => true, 'message' => $cnt . ' code(s) imported successfully', 'added' => $cnt]);
}
if ($action === 'delete') {
    $cid = (int)request('code_id');
    $code = findQuery(" SELECT id,is_sold,gift_card_option_id FROM gift_cards WHERE id=$cid AND product_id=$pid LIMIT 1 ");
    if (!$code) {
        ob_clean();
        encode(['status' => false, 'message' => 'Code not found']);
    }
    if ((int)$code['is_sold'] === 1) {
        ob_clean();
        encode(['status' => false, 'message' => 'Cannot delete a sold code']);
    }
    if ($oid > 0 && (int)($code['gift_card_option_id'] ?? 0) !== $oid) {
        ob_clean();
        encode(['status' => false, 'message' => 'Code does not belong to selected option']);
    }
    execute(" DELETE FROM gift_cards WHERE id=$cid AND product_id=$pid AND is_sold=0 ");
    syncGiftCardStocks($pid);
    ob_clean();
    encode(['status' => true, 'message' => 'Code deleted']);
}
ob_clean();
encode(['status' => false, 'message' => 'Invalid action']);
