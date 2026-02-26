<?php
require_once 'init.php';
$id = (int)request('id', 'get');
if (!$id) {
    $id = (int)request('approve', 'get');
}
if (!$id) {
    $id = (int)request('ban', 'get');
}
if (!$id) redirect('sellers.php');
$seller = findQuery(" SELECT s.*,a.accountname,a.email,a.wallet_address FROM sellers s JOIN accounts a ON s.account_id=a.id WHERE s.account_id=$id ");
if (empty($seller)) redirect('sellers.php');
if (get('approve')) {
    execute(" UPDATE sellers SET is_approved=1 WHERE account_id=$id ");
    redirect("sellerdetail.php?id=$id&msg=approved");
}
if (get('ban')) {
    execute(" UPDATE sellers SET is_approved=0 WHERE account_id=$id ");
    redirect("sellerdetail.php?id=$id&msg=banned");
}
$products = getQuery(" SELECT * FROM products WHERE seller_id=$id ORDER BY id DESC LIMIT 50 ");
$stats = findQuery(" SELECT COUNT(*) total_items,COALESCE(SUM(stock),0) total_stock FROM products WHERE seller_id=$id ");
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex items-center gap-4 mb-6">
        <a href="sellers.php" class="p-2 rounded-lg bg-white dark:bg-white/5 text-gray-500 hover:text-white transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Store Details</h1>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm text-center">
                <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-tr from-primary-500 to-blue-500 p-1 mb-4">
                    <div class="w-full h-full bg-white dark:bg-dark-800 rounded-full flex items-center justify-center text-2xl font-bold uppercase">
                        <?= strtoupper(substr($seller['store_name'], 0, 1)) ?>
                    </div>
                </div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1"><?= $seller['store_name'] ?></h2>
                <p class="text-sm text-gray-500 mb-4">@<?= $seller['store_slug'] ?></p>
                <div class="flex justify-center gap-2 mb-6">
                    <span class="px-3 py-1 rounded-lg text-xs font-bold <?= $seller['is_approved'] ? 'bg-green-500/10 text-green-500' : 'bg-yellow-500/10 text-yellow-500' ?>">
                        <?= $seller['is_approved'] ? 'Verified' : 'Pending' ?>
                    </span>
                    <span class="px-3 py-1 bg-gray-100 dark:bg-white/10 rounded-lg text-xs font-bold text-gray-500">
                        <i class="fa-solid fa-star text-yellow-500"></i> <?= $seller['rating'] ?>
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-4 border-t border-gray-100 dark:border-white/5 pt-4">
                    <div class="text-center">
                        <div class="text-xs text-gray-500 uppercase">Sales</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white"><?= sellerStats($seller['account_id'])['total_sale'] ?></div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-gray-500 uppercase">Commission</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white"><?= $seller['commission_rate'] ?>%</div>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Owner Info</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Username</span>
                        <span class="text-gray-900 dark:text-white font-medium"><?= $seller['accountname'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Email</span>
                        <span class="text-gray-900 dark:text-white font-medium"><?= $seller['email'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Wallet</span>
                        <span class="text-primary-500 font-mono text-xs">
                            <?= $seller['wallet_address'] ? substr($seller['wallet_address'], 0, 6) . '...' : '-' ?>
                        </span>
                    </div>
                </div>
                <div class="mt-6 flex gap-2">
                    <?php if ($seller['is_approved']): ?>
                        <a href="?ban=<?= $id ?>" class="flex-1 py-2 bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white text-center rounded-lg text-sm font-bold transition-colors">Ban Store</a>
                    <?php else: ?>
                        <a href="?approve=<?= $id ?>" class="flex-1 py-2 bg-green-500 hover:bg-green-600 text-white text-center rounded-lg text-sm font-bold transition-colors">Approve Store</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm overflow-hidden">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-gray-900 dark:text-white">
                        Products (<?= (int)$stats['total_items'] ?>)
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-gray-500 border-b border-gray-200 dark:border-white/5">
                                <th class="pb-3 pl-2">Product</th>
                                <th class="pb-3">Price</th>
                                <th class="pb-3">Stock</th>
                                <th class="pb-3">Status</th>
                                <th class="pb-3 text-right pr-2">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                            <?php foreach ($products as $p):
                                $img = json_decode($p['images'], true)[0] ?? 'assets/placeholder.png';
                            ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                    <td class="py-3 pl-2">
                                        <div class="flex items-center gap-3">
                                            <img src="../<?= ltrim($img, '/') ?>" class="w-8 h-8 rounded bg-gray-100 dark:bg-white/5 object-cover">
                                            <span class="font-bold text-gray-900 dark:text-white truncate max-w-[200px]"><?= $p['title'] ?></span>
                                        </div>
                                    </td>
                                    <td class="py-3 font-mono"><?= number_format($p['price_gashy'], 2) ?></td>
                                    <td class="py-3"><?= (int)$p['stock'] ?></td>
                                    <td class="py-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold <?= $p['status'] == 'active' ? 'text-green-500 bg-green-500/10' : 'text-red-500 bg-red-500/10' ?>">
                                            <?= $p['status'] ?>
                                        </span>
                                    </td>
                                    <td class="py-3 pr-2 text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="../product.php?slug=<?= $p['slug'] ?>" target="_blank" class="p-1 text-gray-400 hover:text-blue-500" title="View">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="productdetail.php?id=<?= $p['id'] ?>" class="p-1 text-gray-400 hover:text-primary-500" title="Edit">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>