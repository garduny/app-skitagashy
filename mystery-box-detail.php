<?php
require_once './server/init.php';
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW()");
if (!$session) redirect('seller.php');
$uid = $session['account_id'];
$id = (int)request('id', 'get');
$box = findQuery(" SELECT * FROM products WHERE id=$id AND seller_id=$uid AND type='mystery_box'");
if (!$box) redirect('seller-hub.php');
if (get('delete_loot')) {
    $lid = (int)get('delete_loot');
    execute("DELETE FROM mystery_box_loot WHERE id=$lid AND box_product_id=$id");
    redirect("mystery-box-detail.php?id=$id");
}
if (post('add_loot')) {
    $rew_id = !empty($_POST['reward_product_id']) ? (int)$_POST['reward_product_id'] : 'NULL';
    $amt = (float)$_POST['reward_amount'];
    $prob = (float)$_POST['probability'];
    $rarity = secure($_POST['rarity']);
    execute("INSERT INTO mystery_box_loot(box_product_id,reward_product_id,reward_amount,probability,rarity)VALUES($id,$rew_id,$amt,$prob,'$rarity')");
    redirect("mystery-box-detail.php?id=$id");
}
$loot = getQuery(" SELECT l.*,p.title FROM mystery_box_loot l LEFT JOIN products p ON l.reward_product_id=p.id WHERE l.box_product_id=$id ORDER BY l.probability ASC");
$all_prods = getQuery(" SELECT id,title FROM products WHERE seller_id=$uid AND status='active' AND type!='mystery_box'");
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen">
    <div class="flex items-center gap-4 mb-6">
        <a href="seller-hub.php" class="p-2 rounded-lg bg-white dark:bg-white/5 text-gray-500"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 class="text-2xl font-black">Loot Table: <?= $box['title'] ?></h1>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white dark:bg-dark-800 rounded-2xl p-6">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-gray-500">
                        <th class="pb-2">Reward</th>
                        <th class="pb-2">Amount</th>
                        <th class="pb-2">Rarity</th>
                        <th class="pb-2">Chance</th>
                        <th class="pb-2 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($loot as $l): ?>
                        <tr>
                            <td class="py-3 font-bold"><?= $l['reward_product_id'] ? 'Product: ' . $l['title'] : 'Tokens' ?></td>
                            <td class="py-3"><?= number_format($l['reward_amount'], 3, '.', '') ?></td>
                            <td class="py-3 uppercase text-xs font-bold"><?= $l['rarity'] ?></td>
                            <td class="py-3"><?= $l['probability'] ?>%</td>
                            <td class="py-3 text-right"><a href="?id=<?= $id ?>&delete_loot=<?= $l['id'] ?>" class="text-red-500"><i class="fa-solid fa-trash"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="lg:col-span-1 bg-white dark:bg-dark-800 rounded-2xl p-6">
            <form method="POST" class="space-y-4">
                <select name="reward_product_id" class="w-full bg-gray-50 dark:bg-dark-900 border rounded-xl px-4 py-2">
                    <option value="">Tokens (GASHY)</option>
                    <?php foreach ($all_prods as $p): ?>
                        <option value="<?= $p['id'] ?>">Product: <?= $p['title'] ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="reward_amount" value="0" class="w-full bg-gray-50 dark:bg-dark-900 border rounded-xl px-4 py-2">
                <select name="rarity" class="w-full bg-gray-50 dark:bg-dark-900 border rounded-xl px-4 py-2">
                    <option value="common">Common</option>
                    <option value="rare">Rare</option>
                    <option value="epic">Epic</option>
                    <option value="legendary">Legendary</option>
                </select>
                <input type="number" step="0.01" name="probability" required class="w-full bg-gray-50 dark:bg-dark-900 border rounded-xl px-4 py-2">
                <button type="submit" name="add_loot" value="1" class="w-full py-3 bg-primary-600 text-white font-bold rounded-xl">Add to Box</button>
            </form>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>