<?php
require_once 'init.php';
$id = (int)request('id', 'get');
if (!$id) redirect('products.php');
$box = findQuery(" SELECT p.*,c.name cat_name,s.store_name FROM products p LEFT JOIN categories c ON p.category_id=c.id LEFT JOIN sellers s ON p.seller_id=s.account_id WHERE p.id=$id AND p.type='mystery_box' ");
if (!$box) redirect('products.php');
function boxDetailUrl($id, $extra = [])
{
    $params = array_merge(['id' => $id], $extra);
    return 'mystery-box-detail.php?' . http_build_query($params);
}
if (get('delete_loot')) {
    $lid = (int)request('delete_loot', 'get');
    if ($lid) execute(" DELETE FROM mystery_box_loot WHERE id=$lid AND box_product_id=$id ");
    redirect(boxDetailUrl($id, ['msg' => 'deleted']));
}
if (post('add_loot')) {
    $rew_id = request('reward_product_id', 'post');
    $rew_id = $rew_id !== null && $rew_id !== '' ? (int)$rew_id : 'NULL';
    $amt = max(0, (float)request('reward_amount', 'post'));
    $prob = max(0, (float)request('probability', 'post'));
    $rarity = secure(trim((string)request('rarity', 'post')));
    if (!in_array($rarity, ['common', 'rare', 'epic', 'legendary'])) $rarity = 'common';
    if ($rew_id !== 'NULL') {
        $rewardExists = findQuery(" SELECT id FROM products WHERE id=$rew_id AND status='active' AND type!='mystery_box' ");
        if (!$rewardExists) redirect(boxDetailUrl($id, ['msg' => 'invalid_reward']));
    }
    if ($prob > 0) {
        execute(" INSERT INTO mystery_box_loot (box_product_id,reward_product_id,reward_amount,probability,rarity) VALUES ($id," . ($rew_id === 'NULL' ? 'NULL' : $rew_id) . ",$amt,$prob,'$rarity') ");
    }
    redirect(boxDetailUrl($id, ['msg' => 'added']));
}
if (post('edit_loot')) {
    $lid = (int)request('loot_id', 'post');
    $rew_id = request('reward_product_id', 'post');
    $rew_id = $rew_id !== null && $rew_id !== '' ? (int)$rew_id : 'NULL';
    $amt = max(0, (float)request('reward_amount', 'post'));
    $prob = max(0, (float)request('probability', 'post'));
    $rarity = secure(trim((string)request('rarity', 'post')));
    if (!in_array($rarity, ['common', 'rare', 'epic', 'legendary'])) $rarity = 'common';
    if ($rew_id !== 'NULL') {
        $rewardExists = findQuery(" SELECT id FROM products WHERE id=$rew_id AND status='active' AND type!='mystery_box' ");
        if (!$rewardExists) redirect(boxDetailUrl($id, ['msg' => 'invalid_reward']));
    }
    if ($lid && $prob > 0) {
        execute(" UPDATE mystery_box_loot SET reward_product_id=" . ($rew_id === 'NULL' ? 'NULL' : $rew_id) . ",reward_amount=$amt,probability=$prob,rarity='$rarity' WHERE id=$lid AND box_product_id=$id ");
    }
    redirect(boxDetailUrl($id, ['msg' => 'updated']));
}
$loot = getQuery(" SELECT l.*,p.title reward_title,p.type reward_type,p.stock reward_stock FROM mystery_box_loot l LEFT JOIN products p ON l.reward_product_id=p.id WHERE l.box_product_id=$id ORDER BY l.probability DESC,l.id DESC ");
$all_prods = getQuery(" SELECT id,title,type,stock FROM products WHERE status='active' AND type!='mystery_box' ORDER BY title ASC ");
$probabilityTotal = 0;
$rewardCount = count($loot);
$tokensCount = 0;
$productRewardCount = 0;
$lowStockRewards = 0;
foreach ($loot as $row) {
    $probabilityTotal += (float)$row['probability'];
    if ((int)$row['reward_product_id'] > 0) {
        $productRewardCount++;
        if (isset($row['reward_stock']) && (int)$row['reward_stock'] <= 0) $lowStockRewards++;
    } else {
        $tokensCount++;
    }
}
$msg = request('msg', 'get');
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <a href="products.php" class="p-2 rounded-xl bg-white dark:bg-white/5 text-gray-500 hover:text-primary-500"><i class="fa-solid fa-arrow-left"></i></a>
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Loot Table: <?= $box['title'] ?></h1>
                <p class="text-sm text-gray-500">Manage mystery box rewards, rarity and win chances</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="productdetail.php?id=<?= $id ?>" class="px-4 py-2 rounded-xl bg-gray-200 dark:bg-white/10 text-xs font-bold">Product Detail</a>
            <a href="products.php" class="px-4 py-2 rounded-xl bg-gray-200 dark:bg-white/10 text-xs font-bold">Products</a>
            <button onclick="openModal('addModal')" class="px-4 py-2 rounded-xl bg-primary-600 text-white text-xs font-bold">Add Reward</button>
        </div>
    </div>
    <?php if ($msg === 'added'): ?>
        <div class="p-4 mb-6 bg-green-100 dark:bg-green-500/20 border border-green-200 dark:border-green-500/30 text-green-600 dark:text-green-400 rounded-xl font-bold text-center"><i class="fa-solid fa-check-circle mr-2"></i> Reward added successfully.</div>
    <?php elseif ($msg === 'updated'): ?>
        <div class="p-4 mb-6 bg-green-100 dark:bg-green-500/20 border border-green-200 dark:border-green-500/30 text-green-600 dark:text-green-400 rounded-xl font-bold text-center"><i class="fa-solid fa-check-circle mr-2"></i> Reward updated successfully.</div>
    <?php elseif ($msg === 'deleted'): ?>
        <div class="p-4 mb-6 bg-green-100 dark:bg-green-500/20 border border-green-200 dark:border-green-500/30 text-green-600 dark:text-green-400 rounded-xl font-bold text-center"><i class="fa-solid fa-check-circle mr-2"></i> Reward deleted successfully.</div>
    <?php elseif ($msg === 'invalid_reward'): ?>
        <div class="p-4 mb-6 bg-red-100 dark:bg-red-500/20 border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 rounded-xl font-bold text-center"><i class="fa-solid fa-triangle-exclamation mr-2"></i> Invalid reward product selected.</div>
    <?php endif; ?>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4 mb-6">
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-5">
            <div class="text-xs font-bold uppercase text-gray-500 mb-2">Total Rewards</div>
            <div class="text-3xl font-black text-gray-900 dark:text-white"><?= $rewardCount ?></div>
        </div>
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-5">
            <div class="text-xs font-bold uppercase text-gray-500 mb-2">Product Rewards</div>
            <div class="text-3xl font-black text-primary-500"><?= $productRewardCount ?></div>
        </div>
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-5">
            <div class="text-xs font-bold uppercase text-gray-500 mb-2">Token Rewards</div>
            <div class="text-3xl font-black text-amber-500"><?= $tokensCount ?></div>
        </div>
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-5">
            <div class="text-xs font-bold uppercase text-gray-500 mb-2">Total Chance</div>
            <div class="text-3xl font-black <?= abs($probabilityTotal - 100) < 0.0001 ? 'text-green-500' : ($probabilityTotal > 100 ? 'text-red-500' : 'text-amber-500') ?>"><?= number_format($probabilityTotal, 2) ?>%</div>
        </div>
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-5">
            <div class="text-xs font-bold uppercase text-gray-500 mb-2">Low Stock Rewards</div>
            <div class="text-3xl font-black <?= $lowStockRewards > 0 ? 'text-red-500' : 'text-green-500' ?>"><?= $lowStockRewards ?></div>
        </div>
    </div>
    <?php if (!$rewardCount): ?>
        <div class="p-4 mb-6 bg-red-100 dark:bg-red-500/20 border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 rounded-xl font-bold"><i class="fa-solid fa-triangle-exclamation mr-2"></i>This mystery box has no rewards yet. It should not go live until the loot table is filled.</div>
    <?php elseif ($probabilityTotal > 100): ?>
        <div class="p-4 mb-6 bg-red-100 dark:bg-red-500/20 border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 rounded-xl font-bold"><i class="fa-solid fa-triangle-exclamation mr-2"></i>Total probability is above 100%. Adjust the loot table before production use.</div>
    <?php elseif ($probabilityTotal < 100): ?>
        <div class="p-4 mb-6 bg-amber-100 dark:bg-amber-500/20 border border-amber-200 dark:border-amber-500/30 text-amber-700 dark:text-amber-400 rounded-xl font-bold"><i class="fa-solid fa-circle-exclamation mr-2"></i>Total probability is below 100%. There is unused chance space in the loot table.</div>
    <?php endif; ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4 mb-4">
                <h3 class="font-bold text-gray-900 dark:text-white">Current Contents</h3>
                <div class="text-xs text-gray-500">Sorted by highest chance</div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-gray-500 border-b border-gray-200 dark:border-white/5">
                            <th class="pb-3">Reward</th>
                            <th class="pb-3 text-center">Type</th>
                            <th class="pb-3 text-center">Amount</th>
                            <th class="pb-3 text-center">Rarity</th>
                            <th class="pb-3 text-center">Chance</th>
                            <th class="pb-3 text-center">Stock</th>
                            <th class="pb-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        <?php if (!$loot): ?>
                            <tr>
                                <td colspan="7" class="py-10 text-center text-gray-500">No rewards added yet.</td>
                            </tr>
                            <?php else: foreach ($loot as $l): ?>
                                <tr>
                                    <td class="py-3">
                                        <div class="font-bold text-gray-900 dark:text-white"><?= (int)$l['reward_product_id'] > 0 ? 'Product: ' . htmlspecialchars($l['reward_title'] ?: 'Deleted Product') : 'Tokens (GASHY)' ?></div>
                                        <?php if ((int)$l['reward_product_id'] > 0 && $l['reward_type']): ?><div class="text-xs text-gray-500 uppercase"><?= htmlspecialchars($l['reward_type']) ?></div><?php endif; ?>
                                    </td>
                                    <td class="py-3 text-center"><?= (int)$l['reward_product_id'] > 0 ? 'Product' : 'Tokens' ?></td>
                                    <td class="py-3 text-center"><?= number_format((float)$l['reward_amount'], 2) ?></td>
                                    <td class="py-3 text-center">
                                        <?php
                                        $rarity = strtolower($l['rarity']);
                                        $rarityClass = 'bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-gray-300';
                                        if ($rarity === 'rare') $rarityClass = 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400';
                                        if ($rarity === 'epic') $rarityClass = 'bg-purple-50 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400';
                                        if ($rarity === 'legendary') $rarityClass = 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400';
                                        ?>
                                        <span class="px-2 py-1 rounded-lg text-[10px] uppercase font-bold <?= $rarityClass ?>"><?= htmlspecialchars($l['rarity']) ?></span>
                                    </td>
                                    <td class="py-3 text-center font-bold <?= (float)$l['probability'] >= 50 ? 'text-green-500' : ((float)$l['probability'] <= 5 ? 'text-amber-500' : 'text-gray-900 dark:text-white') ?>"><?= number_format((float)$l['probability'], 2) ?>%</td>
                                    <td class="py-3 text-center">
                                        <?php if ((int)$l['reward_product_id'] > 0): ?>
                                            <span class="<?= (int)$l['reward_stock'] > 0 ? 'text-green-500' : 'text-red-500' ?> font-bold"><?= (int)$l['reward_stock'] ?></span>
                                        <?php else: ?>
                                            <span class="text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" onclick='editLoot(<?= json_encode(["id" => (int)$l["id"], "reward_product_id" => $l["reward_product_id"], "reward_amount" => $l["reward_amount"], "probability" => $l["probability"], "rarity" => $l["rarity"]], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="text-primary-500 hover:text-primary-400"><i class="fa-solid fa-pen-to-square"></i></button>
                                            <a href="<?= boxDetailUrl($id, ['delete_loot' => $l['id']]) ?>" class="text-red-500 hover:text-red-400 delete-loot-link"><i class="fa-solid fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                        <?php endforeach;
                        endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Box Info</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Category</span><span class="font-bold text-gray-900 dark:text-white text-right"><?= htmlspecialchars($box['cat_name'] ?: '-') ?></span></div>
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Seller</span><span class="font-bold text-gray-900 dark:text-white text-right"><?= htmlspecialchars($box['store_name'] ?: 'System') ?></span></div>
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Price USD</span><span class="font-bold text-gray-900 dark:text-white">$<?= number_format((float)$box['price_usd'], 2) ?></span></div>
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Stock</span><span class="font-bold text-gray-900 dark:text-white"><?= (int)$box['stock'] ?></span></div>
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Status</span><span class="font-bold <?= $box['status'] === 'active' ? 'text-green-500' : ($box['status'] === 'banned' ? 'text-red-500' : 'text-gray-500') ?>"><?= htmlspecialchars($box['status']) ?></span></div>
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Created</span><span class="font-bold text-gray-900 dark:text-white"><?= !empty($box['created_at']) ? date('M d, Y', strtotime($box['created_at'])) : '-' ?></span></div>
                </div>
            </div>
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Rarity Breakdown</h3>
                <?php
                $rarityCounts = ['common' => 0, 'rare' => 0, 'epic' => 0, 'legendary' => 0];
                $rarityProb = ['common' => 0, 'rare' => 0, 'epic' => 0, 'legendary' => 0];
                foreach ($loot as $row) {
                    $key = strtolower($row['rarity']);
                    if (isset($rarityCounts[$key])) {
                        $rarityCounts[$key]++;
                        $rarityProb[$key] += (float)$row['probability'];
                    }
                }
                ?>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Common</span><span class="font-bold text-gray-900 dark:text-white"><?= $rarityCounts['common'] ?> / <?= number_format($rarityProb['common'], 2) ?>%</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Rare</span><span class="font-bold text-blue-500"><?= $rarityCounts['rare'] ?> / <?= number_format($rarityProb['rare'], 2) ?>%</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Epic</span><span class="font-bold text-purple-500"><?= $rarityCounts['epic'] ?> / <?= number_format($rarityProb['epic'], 2) ?>%</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Legendary</span><span class="font-bold text-amber-500"><?= $rarityCounts['legendary'] ?> / <?= number_format($rarityProb['legendary'], 2) ?>%</span></div>
                </div>
            </div>
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Quick Notes</h3>
                <div class="space-y-3 text-sm text-gray-500">
                    <div class="p-3 rounded-xl bg-gray-50 dark:bg-white/5">Use token reward when no product is selected.</div>
                    <div class="p-3 rounded-xl bg-gray-50 dark:bg-white/5">Keep total chance at 100% for a fully defined box.</div>
                    <div class="p-3 rounded-xl bg-gray-50 dark:bg-white/5">If a reward product reaches zero stock, review the box before launch.</div>
                </div>
            </div>
        </div>
    </div>
</main>
<div id="addModal" class="fixed inset-0 z-[70] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('addModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white dark:bg-dark-800 rounded-2xl shadow-2xl p-0 max-h-[90vh] overflow-hidden flex flex-col">
        <div class="p-6 border-b border-gray-200 dark:border-white/10 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Add Reward</h3>
            <button onclick="closeModal('addModal')" class="text-gray-500 hover:text-red-500"><i class="fa-solid fa-times text-xl"></i></button>
        </div>
        <div class="overflow-y-auto p-6 flex-1">
            <form method="POST" class="space-y-4">
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Reward Product</label><select name="reward_product_id" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none">
                        <option value="">Tokens (GASHY)</option>
                        <?php foreach ($all_prods as $p): ?><option value="<?= $p['id'] ?>">Product: <?= htmlspecialchars($p['title']) ?> [<?= htmlspecialchars($p['type']) ?> | Stock <?= (int)$p['stock'] ?>]</option><?php endforeach; ?>
                    </select></div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Reward Amount</label><input type="number" step="0.01" min="0" name="reward_amount" value="0" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Rarity</label><select name="rarity" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none">
                        <option value="common">Common</option>
                        <option value="rare">Rare</option>
                        <option value="epic">Epic</option>
                        <option value="legendary">Legendary</option>
                    </select></div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Probability (%)</label><input type="number" step="0.01" min="0.01" name="probability" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                <button type="submit" name="add_loot" value="1" class="w-full py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl">Add to Box</button>
            </form>
        </div>
    </div>
</div>
<div id="editModal" class="fixed inset-0 z-[70] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('editModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white dark:bg-dark-800 rounded-2xl shadow-2xl p-0 max-h-[90vh] overflow-hidden flex flex-col">
        <div class="p-6 border-b border-gray-200 dark:border-white/10 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Edit Reward</h3>
            <button onclick="closeModal('editModal')" class="text-gray-500 hover:text-red-500"><i class="fa-solid fa-times text-xl"></i></button>
        </div>
        <div class="overflow-y-auto p-6 flex-1">
            <form method="POST" class="space-y-4">
                <input type="hidden" name="loot_id" id="edit_loot_id">
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Reward Product</label><select name="reward_product_id" id="edit_reward_product_id" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none">
                        <option value="">Tokens (GASHY)</option>
                        <?php foreach ($all_prods as $p): ?><option value="<?= $p['id'] ?>">Product: <?= htmlspecialchars($p['title']) ?> [<?= htmlspecialchars($p['type']) ?> | Stock <?= (int)$p['stock'] ?>]</option><?php endforeach; ?>
                    </select></div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Reward Amount</label><input type="number" step="0.01" min="0" name="reward_amount" id="edit_reward_amount" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Rarity</label><select name="rarity" id="edit_rarity" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none">
                        <option value="common">Common</option>
                        <option value="rare">Rare</option>
                        <option value="epic">Epic</option>
                        <option value="legendary">Legendary</option>
                    </select></div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Probability (%)</label><input type="number" step="0.01" min="0.01" name="probability" id="edit_probability" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                <button type="submit" name="edit_loot" value="1" class="w-full py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl">Update Reward</button>
            </form>
        </div>
    </div>
</div>
<div id="confirmModal" class="fixed inset-0 hidden z-[80]">
    <div class="absolute inset-0 bg-black/70" onclick="closeModal('confirmModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-sm bg-white dark:bg-dark-800 rounded-2xl p-6 shadow-2xl border border-gray-200 dark:border-white/5">
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

    function openModal(id) {
        const el = document.getElementById(id)
        if (el) el.classList.remove('hidden')
    }

    function closeModal(id) {
        const el = document.getElementById(id)
        if (el) el.classList.add('hidden')
    }

    function openConfirm(url, msg) {
        confirmUrl = url
        document.getElementById('confirmText').innerText = msg
        openModal('confirmModal')
    }

    function runConfirm() {
        if (confirmUrl) window.location = confirmUrl
    }

    function editLoot(item) {
        document.getElementById('edit_loot_id').value = item.id || ''
        document.getElementById('edit_reward_product_id').value = item.reward_product_id || ''
        document.getElementById('edit_reward_amount').value = item.reward_amount || 0
        document.getElementById('edit_probability').value = item.probability || ''
        document.getElementById('edit_rarity').value = item.rarity || 'common'
        openModal('editModal')
    }
    document.addEventListener('click', function(e) {
        const a = e.target.closest('.delete-loot-link')
        if (!a) return
        e.preventDefault()
        openConfirm(a.getAttribute('href'), 'Delete this reward from the mystery box?')
    })
</script>
<?php require_once 'footer.php'; ?>