<?php
require_once 'init.php';
$id = request('id', 'get');
$box = findQuery(" SELECT * FROM products WHERE id=$id AND type='mystery_box'");
if (!$box) redirect('products.php');
if (get('delete_loot')) {
    $lid = request('delete_loot', 'get');
    execute(" DELETE FROM mystery_box_loot WHERE id=$lid ");
    redirect("mystery-box-detail.php?id=$id");
}
if (post('add_loot')) {
    $rew_id = !empty($_POST['reward_product_id']) ? (int)$_POST['reward_product_id'] : 'NULL';
    $amt = (float)$_POST['reward_amount'];
    $prob = (float)$_POST['probability'];
    $rarity = $_POST['rarity'];
    execute(" INSERT INTO mystery_box_loot (box_product_id,reward_product_id,reward_amount,probability,rarity) VALUES ($id,$rew_id,$amt,$prob,'$rarity') ");
    redirect("mystery-box-detail.php?id=$id");
}
$loot = getQuery(" SELECT l.*,p.title FROM mystery_box_loot l LEFT JOIN products p ON l.reward_product_id=p.id WHERE l.box_product_id=$id ORDER BY l.probability ASC ");
$all_prods = getQuery(" SELECT id,title FROM products WHERE status='active' AND type!='mystery_box' ");
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex items-center gap-4 mb-6"><a href="products.php" class="p-2 rounded-lg bg-white dark:bg-white/5 text-gray-500 hover:text-white"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Loot Table: <?= $box['title'] ?></h1>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
            <h3 class="font-bold text-gray-900 dark:text-white mb-4">Current Contents</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-gray-500 border-b border-gray-200 dark:border-white/5">
                            <th class="pb-2">Reward</th>
                            <th class="pb-2">Amount</th>
                            <th class="pb-2">Rarity</th>
                            <th class="pb-2">Chance</th>
                            <th class="pb-2 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        <?php foreach ($loot as $l): ?><tr>
                                <td class="py-3 font-bold text-gray-900 dark:text-white"><?= $l['reward_product_id'] ? 'Product: ' . $l['title'] : 'Tokens' ?></td>
                                <td class="py-3"><?= number_format($l['reward_amount']) ?></td>
                                <td class="py-3 uppercase text-xs font-bold"><?= $l['rarity'] ?></td>
                                <td class="py-3"><?= $l['probability'] ?>%</td>
                                <td class="py-3 text-right"><a href="?id=<?= $id ?>&delete_loot=<?= $l['id'] ?>" class="text-red-500"><i class="fa-solid fa-trash"></i></a></td>
                            </tr><?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="lg:col-span-1 bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
            <h3 class="font-bold text-gray-900 dark:text-white mb-4">Add Item</h3>
            <form method="POST">
                <div class="space-y-4">
                    <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Reward Type</label><select name="reward_product_id" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-gray-900 dark:text-white outline-none">
                            <option value="">Tokens (GASHY)</option><?php foreach ($all_prods as $p): ?><option value="<?= $p['id'] ?>">Product: <?= $p['title'] ?></option><?php endforeach; ?>
                        </select></div>
                    <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Amount (If Tokens)</label><input type="number" name="reward_amount" value="0" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-gray-900 dark:text-white outline-none"></div>
                    <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Rarity</label><select name="rarity" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-gray-900 dark:text-white outline-none">
                            <option value="common">Common</option>
                            <option value="rare">Rare</option>
                            <option value="epic">Epic</option>
                            <option value="legendary">Legendary</option>
                        </select></div>
                    <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Probability (%)</label><input type="number" step="0.01" name="probability" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-gray-900 dark:text-white outline-none"></div>
                    <button type="submit" name="add_loot" value="1" class="w-full py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl">Add to Box</button>
                </div>
            </form>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>