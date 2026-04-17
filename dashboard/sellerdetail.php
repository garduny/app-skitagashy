<?php
require_once 'init.php';
$id = (int)request('id', 'get');
if (!$id) $id = (int)request('approve', 'get');
if (!$id) $id = (int)request('ban', 'get');
if (!$id) redirect('sellers.php');
if (get('approve')) {
    execute(" UPDATE sellers SET is_approved=1 WHERE account_id=$id ");
    redirect("sellerdetail.php?id=$id&msg=approved");
}
if (get('ban')) {
    execute(" UPDATE sellers SET is_approved=0 WHERE account_id=$id ");
    redirect("sellerdetail.php?id=$id&msg=banned");
}
$seller = findQuery(" SELECT s.*,a.accountname,a.email,a.wallet_address,a.tier,a.is_verified,a.is_banned,a.created_at account_created FROM sellers s JOIN accounts a ON s.account_id=a.id WHERE s.account_id=$id ");
if (!$seller) redirect('sellers.php');
$products = getQuery(" SELECT * FROM products WHERE seller_id=$id ORDER BY id DESC LIMIT 50 ");
$stats = findQuery(" SELECT COUNT(*) total_items,COALESCE(SUM(stock),0) total_stock,COALESCE(SUM(CASE WHEN status='active' THEN 1 ELSE 0 END),0) active_items FROM products WHERE seller_id=$id ");
$sellerSaleStats = sellerStats($seller['account_id']);
$totalSales = $sellerSaleStats['total_sale'] ?? $seller['total_sales'] ?? 0;
$gashyRate = toGashy();
$msg = request('msg', 'get');
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <a href="sellers.php" class="p-2 rounded-xl bg-white dark:bg-white/5 text-gray-500 hover:text-primary-500 transition-colors"><i class="fa-solid fa-arrow-left"></i></a>
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white">Store Details</h1>
                <p class="text-sm text-gray-500">Seller profile, status and products</p>
            </div>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="../seller.php?slug=<?= urlencode($seller['store_slug']) ?>" target="_blank" class="px-4 py-2 rounded-xl bg-blue-500 text-white text-sm font-bold">View Store</a>
            <button type="button" onclick="navigator.clipboard.writeText('<?= htmlspecialchars($seller['wallet_address'], ENT_QUOTES) ?>')" class="px-4 py-2 rounded-xl bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-white text-sm font-bold">Copy Wallet</button>
        </div>
    </div>

    <?php if ($msg): ?>
        <div class="mb-6 p-4 rounded-2xl font-bold text-center bg-primary-500/10 text-primary-500 border border-primary-500/20"><?= strtoupper(htmlspecialchars($msg)) ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm text-center">
                <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-tr from-primary-500 to-blue-500 p-1 mb-4">
                    <div class="w-full h-full bg-white dark:bg-dark-800 rounded-full flex items-center justify-center text-2xl font-bold uppercase"><?= strtoupper(substr($seller['store_name'], 0, 1)) ?></div>
                </div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1"><?= htmlspecialchars($seller['store_name']) ?></h2>
                <p class="text-sm text-primary-500 mb-4">@<?= htmlspecialchars($seller['store_slug']) ?></p>

                <div class="flex flex-wrap justify-center gap-2 mb-6">
                    <span class="px-3 py-1 rounded-lg text-xs font-bold <?= $seller['is_approved'] ? 'bg-green-500/10 text-green-500' : 'bg-yellow-500/10 text-yellow-500' ?>"><?= $seller['is_approved'] ? 'Live' : 'Pending' ?></span>
                    <span class="px-3 py-1 bg-gray-100 dark:bg-white/10 rounded-lg text-xs font-bold text-gray-500"><i class="fa-solid fa-star text-yellow-500"></i> <?= number_format((float)$seller['rating'], 2) ?></span>
                    <?php if ($seller['is_verified']): ?><span class="px-3 py-1 rounded-lg text-xs font-bold bg-blue-500/10 text-blue-500">Verified</span><?php endif; ?>
                    <?php if ($seller['is_banned']): ?><span class="px-3 py-1 rounded-lg text-xs font-bold bg-red-500/10 text-red-500">Banned</span><?php endif; ?>
                </div>

                <div class="grid grid-cols-2 gap-4 border-t border-gray-100 dark:border-white/5 pt-4">
                    <div>
                        <div class="text-xs text-gray-500 uppercase">Sales</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white"><?= number_format((float)$totalSales, 2) ?></div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase">Commission</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white"><?= number_format((float)$seller['commission_rate'], 2) ?>%</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase">Products</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white"><?= (int)$stats['total_items'] ?></div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase">Stock</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white"><?= (int)$stats['total_stock'] ?></div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Owner Info</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Username</span><span class="font-medium text-gray-900 dark:text-white text-right"><?= htmlspecialchars($seller['accountname']) ?></span></div>
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Email</span><span class="font-medium text-gray-900 dark:text-white text-right break-all"><?= htmlspecialchars($seller['email']) ?></span></div>
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Wallet</span><span class="font-mono text-primary-500 text-xs text-right break-all"><?= htmlspecialchars($seller['wallet_address'] ?: '-') ?></span></div>
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Tier</span><span class="font-medium text-gray-900 dark:text-white"><?= strtoupper($seller['tier']) ?></span></div>
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Joined</span><span class="font-medium text-gray-900 dark:text-white"><?= !empty($seller['account_created']) ? date('Y-m-d', strtotime($seller['account_created'])) : '-' ?></span></div>
                </div>

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <?php if ($seller['is_approved']): ?>
                        <a href="?ban=<?= $id ?>" class="py-2 bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white text-center rounded-lg text-sm font-bold transition-colors">Suspend Store</a>
                    <?php else: ?>
                        <a href="?approve=<?= $id ?>" class="py-2 bg-green-500 hover:bg-green-600 text-white text-center rounded-lg text-sm font-bold transition-colors">Approve Store</a>
                    <?php endif; ?>
                    <a href="sellers.php" class="py-2 bg-gray-100 dark:bg-white/5 text-center rounded-lg text-sm font-bold text-gray-700 dark:text-white">Back</a>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-5">
                    <div class="text-xs font-bold uppercase text-gray-500 mb-2">Active Items</div>
                    <div class="text-3xl font-black text-green-500"><?= (int)$stats['active_items'] ?></div>
                </div>
                <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-5">
                    <div class="text-xs font-bold uppercase text-gray-500 mb-2">Total Stock</div>
                    <div class="text-3xl font-black text-primary-500"><?= (int)$stats['total_stock'] ?></div>
                </div>
                <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-5">
                    <div class="text-xs font-bold uppercase text-gray-500 mb-2">Rating</div>
                    <div class="text-3xl font-black text-yellow-500"><?= number_format((float)$seller['rating'], 2) ?></div>
                </div>
            </div>

            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">Products (<?= (int)$stats['total_items'] ?>)</h3>
                        <p class="text-sm text-gray-500"><?= (int)$stats['active_items'] ?> active items</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-gray-500 border-b border-gray-200 dark:border-white/5">
                                <th class="pb-3 pl-2">Product</th>
                                <th class="pb-3">Type</th>
                                <th class="pb-3">Price</th>
                                <th class="pb-3">Stock</th>
                                <th class="pb-3">Status</th>
                                <th class="pb-3 text-right pr-2">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                            <?php foreach ($products as $p):
                                $imgs = json_decode($p['images'], true);
                                $img = $imgs[0] ?? 'assets/placeholder.png';
                                $usd = (float)$p['price_usd'];
                                $gashy = $gashyRate ? ($usd / $gashyRate) : 0;
                            ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                    <td class="py-3 pl-2">
                                        <div class="flex items-center gap-3 min-w-[240px]">
                                            <img src="../<?= ltrim($img, '/') ?>" class="w-10 h-10 rounded bg-gray-100 dark:bg-white/5 object-cover">
                                            <div>
                                                <div class="font-bold text-gray-900 dark:text-white truncate max-w-[260px]"><?= htmlspecialchars($p['title']) ?></div>
                                                <?php if (!empty($p['slug'])): ?><div class="text-xs text-primary-500"><?= htmlspecialchars($p['slug']) ?></div><?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 uppercase text-xs font-bold text-gray-500"><?= htmlspecialchars($p['type']) ?></td>
                                    <td class="py-3 font-mono">
                                        <div class="text-gray-900 dark:text-white">$<?= number_format($usd, 2) ?></div>
                                        <div class="text-xs text-primary-500"><?= number_format($gashy, 2) ?> GASHY</div>
                                    </td>
                                    <td class="py-3"><?= (int)$p['stock'] ?></td>
                                    <td class="py-3"><span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold <?= $p['status'] == 'active' ? 'text-green-500 bg-green-500/10' : 'text-red-500 bg-red-500/10' ?>"><?= htmlspecialchars($p['status']) ?></span></td>
                                    <td class="py-3 pr-2 text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="../product.php?slug=<?= urlencode($p['slug']) ?>" target="_blank" class="p-1 text-gray-400 hover:text-blue-500"><i class="fa-solid fa-eye"></i></a>
                                            <a href="productdetail.php?id=<?= $p['id'] ?>" class="p-1 text-gray-400 hover:text-primary-500"><i class="fa-solid fa-pen"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (!$products): ?>
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-gray-400">No products found for this seller</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>