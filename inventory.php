<?php
require_once './server/init.php';
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
// $token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
// $session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW()");
// if (!$session) redirect('seller.php');
// $uid = $session['account_id'];
$pid = (int)request('product_id', 'get');
$prod = findQuery(" SELECT * FROM products WHERE id=$pid AND type IN('gift_card','digital')");
if (!$prod) redirect('seller-hub.php');
if (post('add_codes')) {
    $raw = explode("\n", $_POST['codes']);
    $cnt = 0;
    foreach ($raw as $line) {
        $line = trim($line);
        if ($line) {
            $parts = explode('|', $line);
            $c = encryptCode($parts[0]);
            $p = isset($parts[1]) ? encryptCode($parts[1]) : NULL;
            execute("INSERT INTO gift_cards(product_id,code_enc,pin_enc,is_sold)VALUES($pid,'$c'," . ($p ? "'$p'" : "NULL") . ",0)");
            $cnt++;
        }
    }
    execute("UPDATE products SET stock=stock+$cnt WHERE id=$pid");
    redirect("inventory.php?product_id=$pid&msg=added");
}
if (get('delete')) {
    $cid = (int)get('delete');
    execute("DELETE FROM gift_cards WHERE id=$cid AND product_id=$pid AND is_sold=0");
    execute("UPDATE products SET stock=IF(stock>0,stock-1,0) WHERE id=$pid");
    redirect("inventory.php?product_id=$pid&msg=deleted");
}
$codes = getQuery(" SELECT * FROM gift_cards WHERE product_id=$pid ORDER BY is_sold ASC,id DESC");
$sold_count = countQuery(" SELECT 1 FROM gift_cards WHERE product_id=$pid AND is_sold=1");
$total_count = count($codes);
require_once './header.php';
require_once './sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Manage Inventory</h1>
            <p class="text-sm text-gray-500"><?= $prod['title'] ?></p>
        </div>
        <button onclick="openModal('addModal')" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl flex items-center gap-2"><i class="fa-solid fa-plus"></i> Add Codes</button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl text-center">
            <div class="text-xs font-bold text-gray-500 uppercase mb-1">Total Codes</div>
            <div class="text-3xl font-black"><?= $total_count ?></div>
        </div>
        <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl text-center">
            <div class="text-xs font-bold text-gray-500 uppercase mb-1">Available</div>
            <div class="text-3xl font-black text-green-500"><?= $total_count - $sold_count ?></div>
        </div>
        <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl text-center">
            <div class="text-xs font-bold text-gray-500 uppercase mb-1">Sold</div>
            <div class="text-3xl font-black text-blue-500"><?= $sold_count ?></div>
        </div>
    </div>
    <div class="bg-white dark:bg-dark-800 rounded-2xl overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-xs uppercase text-gray-500 font-bold">
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Code</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($codes as $c): ?>
                        <tr>
                            <td class="px-6 py-4 text-xs font-mono">#<?= $c['id'] ?></td>
                            <td class="px-6 py-4 font-mono">****-****-****-<?= substr(decryptCode($c['code_enc']), -4) ?></td>
                            <td class="px-6 py-4"><?= $c['is_sold'] ? '<span class="text-blue-500 text-xs font-bold">Sold</span>' : '<span class="text-green-500 text-xs font-bold">Available</span>' ?></td>
                            <td class="px-6 py-4 text-right"><?php if (!$c['is_sold']): ?><a href="?product_id=<?= $pid ?>&delete=<?= $c['id'] ?>" class="text-red-500"><i class="fa-solid fa-trash"></i></a><?php endif; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<div id="addModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60" onclick="closeModal('addModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white dark:bg-dark-800 rounded-2xl p-6">
        <form method="POST">
            <textarea name="codes" rows="10" required class="w-full bg-gray-50 dark:bg-dark-900 border rounded-xl px-4 py-2.5 font-mono text-xs"></textarea>
            <button type="submit" name="add_codes" value="1" class="w-full mt-4 py-3 bg-primary-600 text-white font-bold rounded-xl">Secure Import</button>
        </form>
    </div>
</div>
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden')
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden')
    }
</script>
<?php require_once './footer.php'; ?>