<?php
require_once 'init.php';
if (!defined('ENC_KEY')) define('ENC_KEY', 'GashySecretKey2026');
if (!defined('ENC_ALGO')) define('ENC_ALGO', 'AES-256-CBC');
function encryptCode($s)
{
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(ENC_ALGO));
    $e = openssl_encrypt($s, ENC_ALGO, ENC_KEY, 0, $iv);
    return base64_encode($e . '::' . $iv);
}
function decryptCode($s)
{
    $d = base64_decode($s, true);
    if ($d === false || strpos($d, '::') === false) return '';
    list($e, $iv) = explode('::', $d, 2);
    return openssl_decrypt($e, ENC_ALGO, ENC_KEY, 0, $iv) ?: '';
}
function inventoryBaseUrl($pid, $oid = 0)
{
    return 'inventory.php?product_id=' . $pid . ($oid ? '&option_id=' . $oid : '');
}
function syncInventoryStock($pid)
{
    $available = (int)countQuery(" SELECT 1 FROM gift_cards WHERE product_id=$pid AND is_sold=0 ");
    execute(" UPDATE products SET stock=$available WHERE id=$pid ");
    return $available;
}
$pid = (int)request('product_id', 'get');
$prod = findQuery(" SELECT p.*,c.name cat_name,s.store_name FROM products p LEFT JOIN categories c ON p.category_id=c.id LEFT JOIN sellers s ON p.seller_id=s.account_id WHERE p.id=$pid AND p.type IN ('digital','gift_card') ");
if (!$prod) redirect('products.php');
$isGiftCard = $prod['type'] == 'gift_card';
$gashyRate = toGashy();
$msg = request('msg', 'get');
$skipped = 0;
$added = 0;
if ($isGiftCard && post('add_option')) {
    $name = secure(trim((string)request('name', 'post')));
    $price = (float)request('price_usd', 'post');
    if ($name !== '' && $price > 0) {
        $exists = (int)countQuery(" SELECT 1 FROM gift_card_options WHERE product_id=$pid AND name='$name' ");
        if (!$exists) execute(" INSERT INTO gift_card_options (product_id,name,price_usd) VALUES ($pid,'$name',$price) ");
    }
    syncInventoryStock($pid);
    redirect(inventoryBaseUrl($pid));
}
if ($isGiftCard && post('edit_option')) {
    $oid = (int)request('oid', 'post');
    $name = secure(trim((string)request('name', 'post')));
    $price = (float)request('price_usd', 'post');
    if ($oid && $name !== '' && $price > 0) {
        $exists = (int)countQuery(" SELECT 1 FROM gift_card_options WHERE product_id=$pid AND name='$name' AND id!=$oid ");
        if (!$exists) execute(" UPDATE gift_card_options SET name='$name',price_usd=$price WHERE id=$oid AND product_id=$pid ");
    }
    syncInventoryStock($pid);
    redirect(inventoryBaseUrl($pid, $oid));
}
if ($isGiftCard && get('delete_option')) {
    $oid = (int)request('delete_option', 'get');
    if ($oid) {
        $optionData = findQuery(" SELECT id,(SELECT COUNT(1) FROM gift_cards WHERE product_id=$pid AND gift_card_option_id=$oid) total_codes,(SELECT COUNT(1) FROM gift_cards WHERE product_id=$pid AND gift_card_option_id=$oid AND is_sold=1) sold_codes FROM gift_card_options WHERE id=$oid AND product_id=$pid ");
        if ($optionData && !(int)$optionData['total_codes'] && !(int)$optionData['sold_codes']) {
            execute(" DELETE FROM gift_card_options WHERE id=$oid AND product_id=$pid ");
        }
    }
    syncInventoryStock($pid);
    redirect(inventoryBaseUrl($pid));
}
$options = $isGiftCard ? getQuery(" SELECT o.*,(SELECT COUNT(1) FROM gift_cards g WHERE g.product_id=$pid AND g.gift_card_option_id=o.id) total_codes,(SELECT COUNT(1) FROM gift_cards g WHERE g.product_id=$pid AND g.gift_card_option_id=o.id AND g.is_sold=1) sold_codes FROM gift_card_options o WHERE o.product_id=$pid ORDER BY o.id ASC ") : [];
$oid = (int)request('option_id', 'get');
$countOptions = count($options);
if ($isGiftCard && $countOptions == 1) $oid = (int)$options[0]['id'];
if (!$isGiftCard) $oid = 0;
$selectedOption = null;
foreach ($options as $optionRow) {
    if ((int)$optionRow['id'] === $oid) $selectedOption = $optionRow;
}
if ($isGiftCard && $countOptions > 1 && !$oid && !post('add_codes')) {
    syncInventoryStock($pid);
}
if (post('add_codes')) {
    if ($isGiftCard && $countOptions > 1 && !$oid) redirect(inventoryBaseUrl($pid));
    $rawLines = preg_split("/\r\n|\n|\r/", (string)request('codes', 'post'));
    $seen = [];
    foreach ($rawLines as $line) {
        $line = trim((string)$line);
        if ($line === '') continue;
        $parts = array_map('trim', explode('|', $line, 2));
        $code = (string)($parts[0] ?? '');
        $pin = (string)($parts[1] ?? '');
        if ($code === '') {
            $skipped++;
            continue;
        }
        $key = mb_strtolower($code . '|' . $pin);
        if (isset($seen[$key])) {
            $skipped++;
            continue;
        }
        $seen[$key] = 1;
        $encCode = encryptCode($code);
        $encPin = $pin !== '' ? encryptCode($pin) : null;
        $existsRows = getQuery(" SELECT id,code_enc,pin_enc FROM gift_cards WHERE product_id=$pid " . ($isGiftCard ? ($oid ? " AND gift_card_option_id=$oid " : " AND gift_card_option_id IS NULL ") : " AND gift_card_option_id IS NULL ") . " ");
        $duplicateFound = false;
        foreach ($existsRows as $row) {
            $rowCode = decryptCode($row['code_enc']);
            $rowPin = $row['pin_enc'] ? decryptCode($row['pin_enc']) : '';
            if ($rowCode === $code && $rowPin === $pin) {
                $duplicateFound = true;
                break;
            }
        }
        if ($duplicateFound) {
            $skipped++;
            continue;
        }
        execute(" INSERT INTO gift_cards (product_id,gift_card_option_id,code_enc,pin_enc,is_sold) VALUES ($pid," . ($isGiftCard ? ($oid ? $oid : "NULL") : "NULL") . ",'$encCode'," . ($encPin ? "'$encPin'" : "NULL") . ",0) ");
        $added++;
    }
    syncInventoryStock($pid);
    redirect(inventoryBaseUrl($pid, $oid) . '&msg=' . ($added ? 'imported' : 'nochange') . '&added=' . $added . '&skipped=' . $skipped);
}
if (get('delete')) {
    $cid = (int)request('delete', 'get');
    if ($cid) {
        $whereOption = $isGiftCard ? ($oid ? " AND gift_card_option_id=$oid " : " AND gift_card_option_id IS NULL ") : " AND gift_card_option_id IS NULL ";
        execute(" DELETE FROM gift_cards WHERE id=$cid AND product_id=$pid $whereOption AND is_sold=0 ");
    }
    syncInventoryStock($pid);
    redirect(inventoryBaseUrl($pid, $oid) . '&msg=deleted');
}
$currentAvailable = syncInventoryStock($pid);
$whereCodes = " WHERE product_id=$pid ";
if ($isGiftCard) {
    if ($countOptions > 1 && $oid) $whereCodes .= " AND gift_card_option_id=$oid ";
    if ($countOptions <= 1) $whereCodes .= " AND gift_card_option_id " . ($selectedOption ? '=' . $selectedOption['id'] : 'IS NULL') . " ";
} else {
    $whereCodes .= " AND gift_card_option_id IS NULL ";
}
$codes = getQuery(" SELECT * FROM gift_cards $whereCodes ORDER BY is_sold ASC,id DESC ");
$sold_count = (int)countQuery(" SELECT 1 FROM gift_cards $whereCodes AND is_sold=1 ");
$total_count = (int)countQuery(" SELECT 1 FROM gift_cards $whereCodes ");
$available_count = max(0, $total_count - $sold_count);
$total_all_codes = (int)countQuery(" SELECT 1 FROM gift_cards WHERE product_id=$pid ");
$total_all_sold = (int)countQuery(" SELECT 1 FROM gift_cards WHERE product_id=$pid AND is_sold=1 ");
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white">Inventory</h1>
            <p class="text-sm text-gray-500"><?= $prod['title'] ?></p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="productdetail.php?id=<?= $pid ?>" class="px-4 py-2 bg-gray-200 dark:bg-white/10 rounded-lg text-xs font-bold">Product Detail</a>
            <a href="<?= inventoryBaseUrl($pid) ?>" class="px-4 py-2 bg-gray-200 dark:bg-white/10 rounded-lg text-xs font-bold">Refresh</a>
            <?php if ($isGiftCard): ?>
                <button onclick="openModal('optionModal')" class="px-4 py-2 bg-primary-600 text-white rounded-lg text-xs font-bold">Add Option</button>
            <?php endif; ?>
            <?php if ((!$isGiftCard) || $countOptions <= 1 || $oid): ?>
                <button onclick="openModal('addModal')" class="px-4 py-2 bg-primary-600 text-white rounded-lg text-xs font-bold">Add Codes</button>
            <?php endif; ?>
        </div>
    </div>
    <?php if ($msg === 'imported'): ?>
        <div class="mb-6 p-4 rounded-2xl border border-green-200 bg-green-50 text-green-700 dark:bg-green-500/10 dark:border-green-500/20 dark:text-green-400 text-sm font-bold">Import completed. Added <?= (int)request('added', 'get') ?> code(s), skipped <?= (int)request('skipped', 'get') ?> duplicate/invalid line(s).</div>
    <?php elseif ($msg === 'nochange'): ?>
        <div class="mb-6 p-4 rounded-2xl border border-amber-200 bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400 text-sm font-bold">No new codes were added. All lines were duplicate or invalid.</div>
    <?php elseif ($msg === 'deleted'): ?>
        <div class="mb-6 p-4 rounded-2xl border border-green-200 bg-green-50 text-green-700 dark:bg-green-500/10 dark:border-green-500/20 dark:text-green-400 text-sm font-bold">Inventory item deleted successfully.</div>
    <?php endif; ?>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-dark-800 p-5 rounded-2xl border border-gray-200 dark:border-white/5">
            <div class="text-xs font-bold text-gray-500 uppercase mb-2">Product Type</div>
            <div class="text-lg font-black text-gray-900 dark:text-white uppercase"><?= $prod['type'] ?></div>
        </div>
        <div class="bg-white dark:bg-dark-800 p-5 rounded-2xl border border-gray-200 dark:border-white/5">
            <div class="text-xs font-bold text-gray-500 uppercase mb-2">Product Stock</div>
            <div class="text-3xl font-black text-primary-500"><?= $currentAvailable ?></div>
        </div>
        <div class="bg-white dark:bg-dark-800 p-5 rounded-2xl border border-gray-200 dark:border-white/5">
            <div class="text-xs font-bold text-gray-500 uppercase mb-2">All Codes</div>
            <div class="text-3xl font-black text-gray-900 dark:text-white"><?= $total_all_codes ?></div>
        </div>
        <div class="bg-white dark:bg-dark-800 p-5 rounded-2xl border border-gray-200 dark:border-white/5">
            <div class="text-xs font-bold text-gray-500 uppercase mb-2">All Sold</div>
            <div class="text-3xl font-black text-blue-500"><?= $total_all_sold ?></div>
        </div>
    </div>
    <?php if ($isGiftCard): ?>
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-5 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <div>
                    <h2 class="text-lg font-black text-gray-900 dark:text-white">Gift Card Options</h2>
                    <p class="text-sm text-gray-500"><?= $countOptions ?> option(s)</p>
                </div>
                <?php if ($countOptions > 1 && $oid): ?>
                    <a href="<?= inventoryBaseUrl($pid) ?>" class="px-4 py-2 bg-orange-500 text-white rounded-lg text-xs font-bold w-full md:w-auto text-center">Close Option Mode</a>
                <?php endif; ?>
            </div>
            <?php if (!$options): ?>
                <div class="p-5 rounded-2xl border border-dashed border-gray-200 dark:border-white/10 text-sm text-gray-500">No options created yet.</div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    <?php foreach ($options as $o): $optionAvailable = max(0, (int)$o['total_codes'] - (int)$o['sold_codes']);
                        $optionGashy = $gashyRate ? ((float)$o['price_usd'] / $gashyRate) : 0;
                        if ($oid && (int)$o['id'] !== $oid) continue; ?>
                        <div class="p-4 bg-gray-50 dark:bg-white/5 rounded-2xl border border-gray-200 dark:border-white/5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="font-black text-gray-900 dark:text-white truncate"><?= htmlspecialchars($o['name']) ?></div>
                                    <div class="text-xs text-gray-500 mt-1">$<?= number_format($o['price_usd'], 2) ?> <span class="text-primary-500"><?= number_format($optionGashy, 2) ?> GASHY</span></div>
                                </div>
                                <div class="shrink-0">
                                    <?php if ($oid && (int)$o['id'] === $oid): ?>
                                        <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-primary-600 text-white">OPEN</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-2 mt-4 text-center">
                                <div class="rounded-xl bg-white dark:bg-dark-900 p-3 border border-gray-200 dark:border-white/5">
                                    <div class="text-[10px] text-gray-500 uppercase">Total</div>
                                    <div class="font-black text-gray-900 dark:text-white"><?= (int)$o['total_codes'] ?></div>
                                </div>
                                <div class="rounded-xl bg-white dark:bg-dark-900 p-3 border border-gray-200 dark:border-white/5">
                                    <div class="text-[10px] text-gray-500 uppercase">Available</div>
                                    <div class="font-black text-green-500"><?= $optionAvailable ?></div>
                                </div>
                                <div class="rounded-xl bg-white dark:bg-dark-900 p-3 border border-gray-200 dark:border-white/5">
                                    <div class="text-[10px] text-gray-500 uppercase">Sold</div>
                                    <div class="font-black text-blue-500"><?= (int)$o['sold_codes'] ?></div>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2 mt-4">
                                <?php if (!$oid || (int)$o['id'] !== $oid): ?>
                                    <a href="<?= inventoryBaseUrl($pid, (int)$o['id']) ?>" class="px-3 py-2 rounded-xl bg-blue-500 text-white text-xs font-bold">Open</a>
                                <?php endif; ?>
                                <button onclick="editOption(<?= (int)$o['id'] ?>,'<?= htmlspecialchars($o['name'], ENT_QUOTES) ?>','<?= $o['price_usd'] ?>')" class="px-3 py-2 rounded-xl bg-gray-200 dark:bg-white/10 text-gray-700 dark:text-gray-200 text-xs font-bold">Edit</button>
                                <?php if ((int)$o['total_codes'] === 0 && (int)$o['sold_codes'] === 0): ?>
                                    <a href="<?= inventoryBaseUrl($pid) ?>&delete_option=<?= (int)$o['id'] ?>" class="px-3 py-2 rounded-xl bg-red-500 text-white text-xs font-bold">Delete</a>
                                <?php else: ?>
                                    <span class="px-3 py-2 rounded-xl bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 text-xs font-bold">Has Codes</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php if ((!$isGiftCard) || $countOptions <= 1 || $oid): ?>
        <div class="mb-4 text-sm font-bold text-gray-700 dark:text-gray-300">Mode: <?= $isGiftCard ? ($selectedOption ? htmlspecialchars($selectedOption['name']) . ' ($' . number_format($selectedOption['price_usd'], 2) . ')' : 'Default Option') : 'Default Digital Inventory' ?></div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl border border-gray-200 dark:border-white/5 text-center">
                <div class="text-xs text-gray-500">Total Codes</div>
                <div class="text-3xl font-black"><?= $total_count ?></div>
            </div>
            <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl border border-gray-200 dark:border-white/5 text-center">
                <div class="text-xs text-gray-500">Available</div>
                <div class="text-3xl font-black text-green-500"><?= $available_count ?></div>
            </div>
            <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl border border-gray-200 dark:border-white/5 text-center">
                <div class="text-xs text-gray-500">Sold</div>
                <div class="text-3xl font-black text-blue-500"><?= $sold_count ?></div>
            </div>
        </div>
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-white/5 text-xs text-gray-500">
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Code</th>
                            <th class="px-6 py-4">PIN</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Sold</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        <?php if (!$codes): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">No inventory codes yet.</td>
                            </tr>
                            <?php else: foreach ($codes as $c): $decodedCode = decryptCode($c['code_enc']);
                                $decodedPin = $c['pin_enc'] ? decryptCode($c['pin_enc']) : '';
                                $maskedCode = $decodedCode !== '' ? str_repeat('*', max(strlen($decodedCode) - 4, 0)) . substr($decodedCode, -4) : '****';
                                $maskedPin = $decodedPin !== '' ? str_repeat('*', max(strlen($decodedPin) - 2, 0)) . substr($decodedPin, -2) : '-'; ?>
                                <tr>
                                    <td class="px-6 py-4 text-xs font-mono">#<?= $c['id'] ?></td>
                                    <td class="px-6 py-4 font-mono text-sm"><?= $maskedCode ?></td>
                                    <td class="px-6 py-4 font-mono text-sm"><?= $maskedPin ?></td>
                                    <td class="px-6 py-4"><?= $c['is_sold'] ? '<span class="text-blue-500 text-xs font-bold">Sold</span>' : '<span class="text-green-500 text-xs font-bold">Available</span>' ?></td>
                                    <td class="px-6 py-4 text-xs"><?= $c['sold_at'] ? date('M d, Y', strtotime($c['sold_at'])) : '-' ?></td>
                                    <td class="px-6 py-4 text-right">
                                        <?php if (!$c['is_sold']): ?>
                                            <a href="<?= inventoryBaseUrl($pid, $oid) ?>&delete=<?= $c['id'] ?>" class="text-red-500 text-xs font-bold">Delete</a>
                                        <?php else: ?>
                                            <span class="text-gray-400 text-xs font-bold">Locked</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                        <?php endforeach;
                        endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</main>
<?php if ($isGiftCard): ?>
    <div id="optionModal" class="fixed inset-0 hidden z-[60]">
        <div class="absolute inset-0 bg-black/60" onclick="closeModal('optionModal')"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md px-4">
            <div class="bg-white dark:bg-dark-800 rounded-2xl p-6 border border-gray-200 dark:border-white/5 shadow-2xl">
                <h3 class="font-black mb-4 text-gray-900 dark:text-white">Add Option</h3>
                <form method="POST" class="space-y-3">
                    <input type="text" name="name" placeholder="Option name" required class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-dark-900 text-gray-900 dark:text-white outline-none">
                    <input type="number" step="0.01" min="0.01" name="price_usd" placeholder="Price USD" required class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-dark-900 text-gray-900 dark:text-white outline-none">
                    <button type="submit" name="add_option" value="1" class="w-full py-3 bg-primary-600 text-white rounded-xl font-bold text-sm">Save</button>
                </form>
            </div>
        </div>
    </div>
    <div id="editModal" class="fixed inset-0 hidden z-[60]">
        <div class="absolute inset-0 bg-black/60" onclick="closeModal('editModal')"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md px-4">
            <div class="bg-white dark:bg-dark-800 rounded-2xl p-6 border border-gray-200 dark:border-white/5 shadow-2xl">
                <h3 class="font-black mb-4 text-gray-900 dark:text-white">Edit Option</h3>
                <form method="POST" class="space-y-3">
                    <input type="hidden" name="oid" id="edit_oid">
                    <input type="text" name="name" id="edit_name" required class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-dark-900 text-gray-900 dark:text-white outline-none">
                    <input type="number" step="0.01" min="0.01" name="price_usd" id="edit_price" required class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-dark-900 text-gray-900 dark:text-white outline-none">
                    <button type="submit" name="edit_option" value="1" class="w-full py-3 bg-primary-600 text-white rounded-xl font-bold text-sm">Update</button>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>
<div id="addModal" class="fixed inset-0 hidden z-[60]">
    <div class="absolute inset-0 bg-black/60" onclick="closeModal('addModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl px-4">
        <div class="bg-white dark:bg-dark-800 rounded-2xl p-6 border border-gray-200 dark:border-white/5 shadow-2xl">
            <h3 class="font-black mb-2 text-gray-900 dark:text-white">Add Codes</h3>
            <p class="text-xs text-gray-500 mb-4">One line per item. Use <span class="font-mono">CODE</span> or <span class="font-mono">CODE|PIN</span>.</p>
            <form method="POST">
                <textarea name="codes" rows="12" required class="w-full mb-4 px-4 py-3 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-dark-900 text-gray-900 dark:text-white outline-none"></textarea>
                <button type="submit" name="add_codes" value="1" class="w-full py-3 bg-primary-600 text-white rounded-xl font-bold text-sm">Import</button>
            </form>
        </div>
    </div>
</div>
<div id="confirmModal" class="fixed inset-0 hidden z-[80]">
    <div class="absolute inset-0 bg-black/70" onclick="closeModal('confirmModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-sm px-4">
        <div class="bg-white dark:bg-dark-800 rounded-2xl p-6 shadow-2xl border border-gray-200 dark:border-white/5">
            <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-2xl font-black">!</div>
            <h3 class="text-lg font-black text-center mb-2 text-gray-900 dark:text-white">Confirmation</h3>
            <p id="confirmText" class="text-sm text-center text-gray-500 mb-6"></p>
            <div class="grid grid-cols-2 gap-3">
                <button type="button" onclick="closeModal('confirmModal')" class="py-3 rounded-xl bg-gray-200 dark:bg-white/10 font-bold text-sm">Cancel</button>
                <button type="button" onclick="runConfirm()" class="py-3 rounded-xl bg-red-600 text-white font-bold text-sm">Delete</button>
            </div>
        </div>
    </div>
</div>
<script>
    let confirmUrl = ''

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden')
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden')
    }

    function editOption(id, name, price) {
        document.getElementById('edit_oid').value = id
        document.getElementById('edit_name').value = name
        document.getElementById('edit_price').value = price
        openModal('editModal')
    }

    function openConfirm(url, msg) {
        confirmUrl = url
        document.getElementById('confirmText').innerText = msg
        openModal('confirmModal')
    }

    function runConfirm() {
        if (confirmUrl) window.location = confirmUrl
    }
    document.addEventListener('click', function(e) {
        const a = e.target.closest('a')
        if (!a) return
        const href = a.getAttribute('href') || ''
        if (href.includes('delete_option=')) {
            e.preventDefault()
            openConfirm(href, 'Delete this option? It will work only when there are no codes inside it.')
            return
        }
        if (href.includes('&delete=')) {
            e.preventDefault()
            openConfirm(href, 'Delete this inventory code?')
        }
    })
</script>
<?php require_once 'footer.php'; ?>