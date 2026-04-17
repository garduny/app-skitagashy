<?php
require_once 'init.php';
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
$pid = (int)request('product_id', 'get');
$prod = findQuery(" SELECT * FROM products WHERE id=$pid AND type='gift_card'");
if (!$prod) redirect('products.php');
if (post('add_option')) {
    $n = secure(request('name', 'post'));
    $p = (float)request('price_usd', 'post');
    if ($n && $p > 0) execute("INSERT INTO gift_card_options(product_id,name,price_usd) VALUES($pid,'$n',$p)");
    redirect("inventory.php?product_id=$pid");
}
if (post('edit_option')) {
    $oid = (int)request('oid', 'post');
    $n = secure(request('name', 'post'));
    $p = (float)request('price_usd', 'post');
    if ($oid && $n && $p > 0) execute("UPDATE gift_card_options SET name='$n',price_usd=$p WHERE id=$oid AND product_id=$pid");
    redirect("inventory.php?product_id=$pid&option_id=$oid");
}
if (get('delete_option')) {
    $oid = (int)request('delete_option', 'get');
    execute("DELETE FROM gift_card_options WHERE id=$oid AND product_id=$pid");
    redirect("inventory.php?product_id=$pid");
}
$options = getQuery(" SELECT * FROM gift_card_options WHERE product_id=$pid ORDER BY id ASC");
$oid = (int)request('option_id', 'get');
$hasOptions = count($options);
if ($hasOptions == 1) $oid = $options[0]['id'];
$selectedOption = null;
foreach ($options as $o) {
    if ($o['id'] == $oid) $selectedOption = $o;
}
if (post('add_codes')) {
    if ($hasOptions > 1 && !$oid) redirect("inventory.php?product_id=$pid");
    $raw = explode("\n", $_POST['codes']);
    foreach ($raw as $l) {
        $l = trim($l);
        if ($l) {
            $p = explode('|', $l);
            $c = encryptCode($p[0]);
            $pin = isset($p[1]) ? encryptCode($p[1]) : NULL;
            execute("INSERT INTO gift_cards(product_id,gift_card_option_id,code_enc,pin_enc,is_sold) VALUES($pid," . ($oid ?: "NULL") . ",'$c'," . ($pin ? "'$pin'" : "NULL") . ",0)");
        }
    }
    redirect("inventory.php?product_id=$pid&option_id=$oid");
}
if (get('delete')) {
    $cid = (int)request('delete', 'get');
    execute("DELETE FROM gift_cards WHERE id=$cid AND is_sold=0");
    redirect("inventory.php?product_id=$pid&option_id=$oid");
}
$q = " SELECT * FROM gift_cards WHERE product_id=$pid";
if ($oid) $q .= " AND gift_card_option_id=$oid";
$q .= " ORDER BY is_sold ASC,id DESC";
$codes = getQuery($q);
$sold_count = countQuery(" SELECT 1 FROM gift_cards WHERE product_id=$pid" . ($oid ? " AND gift_card_option_id=$oid" : "") . " AND is_sold=1");
$total_count = count($codes);
$countOptions = count($options);
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white">Inventory</h1>
            <p class="text-sm text-gray-500"><?= $prod['title'] ?></p>
        </div>
        <div class="flex gap-2">
            <a href="inventory.php?product_id=<?= $pid ?>" class="px-4 py-2 bg-gray-200 dark:bg-white/10 rounded-lg text-xs font-bold">Refresh</a>
            <button onclick="openModal('optionModal')" class="px-4 py-2 bg-primary-600 text-white rounded-lg text-xs font-bold">Add Option</button>
        </div>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <?php foreach ($options as $o): if ($oid && $o['id'] != $oid) continue; ?>
            <div class="p-4 bg-white dark:bg-dark-800 border rounded-xl">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="font-bold text-sm"><?= $o['name'] ?></div>
                        <div class="text-xs text-gray-500">$<?= number_format($o['price_usd'], 2) ?></div>
                    </div>
                    <div class="flex gap-2">
                        <?php if ($oid && $o['id'] == $oid): ?>
                            <a href="?product_id=<?= $pid ?>" class="text-orange-500 text-xs font-bold">Close</a>
                        <?php else: ?>
                            <a href="?product_id=<?= $pid ?>&option_id=<?= $o['id'] ?>" class="text-blue-500 text-xs <?= $countOptions > 1 ? 'block' : 'hidden' ?>">Open</a>
                            <a href="?product_id=<?= $pid ?>&delete_option=<?= $o['id'] ?>" class="text-red-500 text-xs">Del</a>
                            <button onclick="editOption(<?= $o['id'] ?>,'<?= htmlspecialchars($o['name'], ENT_QUOTES) ?>','<?= $o['price_usd'] ?>')" class="text-gray-500 text-xs">Edit</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if ($hasOptions <= 1 || $oid): ?>
        <div class="mb-4 text-sm font-bold text-gray-700 dark:text-gray-300">Option: <?= $selectedOption ? $selectedOption['name'] . ' ($' . number_format($selectedOption['price_usd'], 2) . ')' : 'Default' ?></div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl border text-center">
                <div class="text-xs text-gray-500">Total Codes</div>
                <div class="text-3xl font-black"><?= $total_count ?></div>
            </div>
            <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl border text-center">
                <div class="text-xs text-gray-500">Available</div>
                <div class="text-3xl font-black text-green-500"><?= $total_count - $sold_count ?></div>
            </div>
            <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl border text-center">
                <div class="text-xs text-gray-500">Sold</div>
                <div class="text-3xl font-black text-blue-500"><?= $sold_count ?></div>
            </div>
        </div>
        <div class="flex justify-between mb-4">
            <button onclick="openModal('addModal')" class="px-4 py-2 bg-primary-600 text-white rounded-lg text-xs font-bold">Add Codes</button>
        </div>
        <div class="bg-white dark:bg-dark-800 rounded-2xl border overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 text-xs text-gray-500">
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Code</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Sold</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php foreach ($codes as $c): ?>
                        <tr>
                            <td class="px-6 py-4 text-xs font-mono">#<?= $c['id'] ?></td>
                            <td class="px-6 py-4 font-mono">****-****-****-<?= substr(decryptCode($c['code_enc']), -4) ?></td>
                            <td class="px-6 py-4"><?= $c['is_sold'] ? '<span class="text-blue-500 text-xs">Sold</span>' : '<span class="text-green-500 text-xs">Available</span>' ?></td>
                            <td class="px-6 py-4 text-xs"><?= $c['sold_at'] ? date('M d,Y', strtotime($c['sold_at'])) : '-' ?></td>
                            <td class="px-6 py-4 text-right"><?php if (!$c['is_sold']): ?><a href="?product_id=<?= $pid ?>&option_id=<?= $oid ?>&delete=<?= $c['id'] ?>" class="text-red-500 text-xs">Delete</a><?php endif; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>
<div id="optionModal" class="fixed inset-0 hidden z-[60]">
    <div class="absolute inset-0 bg-black/60" onclick="closeModal('optionModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white dark:bg-dark-800 rounded-2xl p-6">
        <h3 class="font-bold mb-4">Add Option</h3>
        <form method="POST">
            <input type="text" name="name" placeholder="Option name" required class="w-full mb-3 px-3 py-2 border rounded">
            <input type="number" step="0.01" name="price_usd" placeholder="Price USD" required class="w-full mb-3 px-3 py-2 border rounded">
            <button type="submit" name="add_option" value="1" class="w-full py-2 bg-primary-600 text-white rounded">Save</button>
        </form>
    </div>
</div>
<div id="editModal" class="fixed inset-0 hidden z-[60]">
    <div class="absolute inset-0 bg-black/60" onclick="closeModal('editModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white dark:bg-dark-800 rounded-2xl p-6">
        <h3 class="font-bold mb-4">Edit Option</h3>
        <form method="POST">
            <input type="hidden" name="oid" id="edit_oid">
            <input type="text" name="name" id="edit_name" required class="w-full mb-3 px-3 py-2 border rounded">
            <input type="number" step="0.01" name="price_usd" id="edit_price" required class="w-full mb-3 px-3 py-2 border rounded">
            <button type="submit" name="edit_option" value="1" class="w-full py-2 bg-primary-600 text-white rounded">Update</button>
        </form>
    </div>
</div>
<div id="addModal" class="fixed inset-0 hidden z-[60]">
    <div class="absolute inset-0 bg-black/60" onclick="closeModal('addModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white dark:bg-dark-800 rounded-2xl p-6">
        <h3 class="font-bold mb-4">Add Codes</h3>
        <form method="POST">
            <textarea name="codes" rows="10" required class="w-full mb-3 px-3 py-2 border rounded bg-white dark:bg-dark-800"></textarea>
            <button type="submit" name="add_codes" value="1" class="w-full py-2 bg-primary-600 text-white rounded">Import</button>
        </form>
    </div>
</div>
<div id="confirmModal" class="fixed inset-0 hidden z-[80]">
    <div class="absolute inset-0 bg-black/70" onclick="closeModal('confirmModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-sm bg-white dark:bg-dark-800 rounded-2xl p-6 shadow-2xl">
        <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-2xl font-black">!</div>
        <h3 class="text-lg font-black text-center mb-2 text-gray-900 dark:text-white">Confirmation</h3>
        <p id="confirmText" class="text-sm text-center text-gray-500 mb-6"></p>
        <div class="grid grid-cols-2 gap-3">
            <button onclick="closeModal('confirmModal')" class="py-2 rounded-xl bg-gray-200 dark:bg-white/10 font-bold text-sm">Cancel</button>
            <button onclick="runConfirm()" class="py-2 rounded-xl bg-red-600 text-white font-bold text-sm">Delete</button>
        </div>
    </div>
</div>
<script>
    let confirmUrl = ''

    function openModal(i) {
        document.getElementById(i).classList.remove('hidden')
    }

    function closeModal(i) {
        document.getElementById(i).classList.add('hidden')
    }

    function editOption(id, n, p) {
        document.getElementById('edit_oid').value = id
        document.getElementById('edit_name').value = n
        document.getElementById('edit_price').value = p
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
            openConfirm(href, 'Delete this option?')
            return
        }
        if (href.includes('&delete=')) {
            e.preventDefault()
            openConfirm(href, 'Delete this inventory code?')
        }
    })
</script>
<?php require_once 'footer.php'; ?>