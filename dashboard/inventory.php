<?php
require_once 'init.php';
define('ENC_KEY', 'GashySecretKey2026');
define('ENC_ALGO', 'AES-256-CBC');
function encryptCode($str)
{
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(ENC_ALGO));
    $enc = openssl_encrypt($str, ENC_ALGO, ENC_KEY, 0, $iv);
    return base64_encode($enc . '::' . $iv);
}
function decryptCode($str)
{
    list($enc, $iv) = explode('::', base64_decode($str), 2);
    return openssl_decrypt($enc, ENC_ALGO, ENC_KEY, 0, $iv);
}
$pid = (int)request('product_id', 'get');
$prod = findQuery(" SELECT * FROM products WHERE id=$pid AND type='gift_card' ");
if (!$prod) {
    //redirect('products.php');
}
if (post('add_codes')) {
    $raw = explode("\n", $_POST['codes']);
    $cnt = 0;
    foreach ($raw as $line) {
        $line = trim($line);
        if ($line) {
            $parts = explode('|', $line);
            $c = encryptCode($parts[0]);
            $p = isset($parts[1]) ? encryptCode($parts[1]) : NULL;
            execute(" INSERT INTO gift_cards (product_id,code_enc,pin_enc,is_sold) VALUES ($pid,'$c'," . ($p ? "'$p'" : "NULL") . ",0) ");
            $cnt++;
        }
    }
    execute(" UPDATE products SET stock=stock+$cnt WHERE id=$pid ");
    redirect("inventory.php?product_id=$pid&msg=added");
}
if (get('delete')) {
    $cid = (int)get('delete');
    execute(" DELETE FROM gift_cards WHERE id=$cid AND is_sold=0 ");
    execute(" UPDATE products SET stock=stock-1 WHERE id=$pid ");
    redirect("inventory.php?product_id=$pid&msg=deleted");
}
$codes = getQuery(" SELECT * FROM gift_cards WHERE product_id=$pid ORDER BY is_sold ASC, id DESC ");
$sold_count = countQuery(" SELECT 1 FROM gift_cards WHERE product_id=$pid AND is_sold=1 ");
$total_count = count($codes);
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Manage Inventory</h1>
            <p class="text-sm text-gray-500"><?= $prod['title'] ?></p>
        </div>
        <button onclick="openModal('addModal')" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl shadow-lg shadow-primary-500/20 transition-all flex items-center gap-2"><i class="fa-solid fa-plus"></i> Add Codes</button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm text-center">
            <div class="text-xs font-bold text-gray-500 uppercase mb-1">Total Codes</div>
            <div class="text-3xl font-black text-gray-900 dark:text-white"><?= $total_count ?></div>
        </div>
        <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm text-center">
            <div class="text-xs font-bold text-gray-500 uppercase mb-1">Available</div>
            <div class="text-3xl font-black text-green-500"><?= $total_count - $sold_count ?></div>
        </div>
        <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm text-center">
            <div class="text-xs font-bold text-gray-500 uppercase mb-1">Sold</div>
            <div class="text-3xl font-black text-blue-500"><?= $sold_count ?></div>
        </div>
    </div>
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/5 text-xs uppercase text-gray-500 font-bold">
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Code (Masked)</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Sold Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php foreach ($codes as $c): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 text-xs font-mono text-gray-500">#<?= $c['id'] ?></td>
                            <td class="px-6 py-4 font-mono text-sm text-gray-900 dark:text-white">****-****-****-<?= substr(decryptCode($c['code_enc']), -4) ?></td>
                            <td class="px-6 py-4"><?= $c['is_sold'] ? '<span class="text-blue-500 text-xs font-bold bg-blue-500/10 px-2 py-1 rounded">Sold</span>' : '<span class="text-green-500 text-xs font-bold bg-green-500/10 px-2 py-1 rounded">Available</span>' ?></td>
                            <td class="px-6 py-4 text-sm text-gray-500"><?= $c['sold_at'] ? date('M d, Y', strtotime($c['sold_at'])) : '-' ?></td>
                            <td class="px-6 py-4 text-right"><?php if (!$c['is_sold']): ?><a href="?product_id=<?= $pid ?>&delete=<?= $c['id'] ?>" onclick="return confirm('Delete code?')" class="p-2 text-gray-400 hover:text-red-500"><i class="fa-solid fa-trash"></i></a><?php endif; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<div id="addModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('addModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white dark:bg-dark-800 rounded-2xl shadow-2xl p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Add Inventory</h3>
        <form method="POST">
            <div class="space-y-4">
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Paste Codes (One per line)</label><textarea name="codes" rows="10" placeholder="CODE-123&#10;CODE-456|PIN-789" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none font-mono text-xs"></textarea>
                    <p class="text-[10px] text-gray-500 mt-2">Format: <code>CODE</code> or <code>CODE|PIN</code>. Codes are encrypted before storage.</p>
                </div><button type="submit" name="add_codes" value="1" class="w-full py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl">Secure Import</button>
            </div>
        </form>
    </div>
</div>
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
</script>
<?php require_once 'footer.php'; ?>