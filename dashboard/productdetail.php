<?php
require_once 'init.php';
$id = (int)request('id', 'get');
if (!$id) redirect('products.php');
$productfound = findQuery(" SELECT p.*,c.name as cat_name,s.store_name FROM products p LEFT JOIN categories c ON p.category_id=c.id LEFT JOIN sellers s ON p.seller_id=s.account_id WHERE p.id=$id ");
if (!$productfound) redirect('products.php');
$sold_data = findQuery(" SELECT SUM(oi.quantity) as total FROM order_items oi JOIN orders o ON oi.order_id=o.id WHERE oi.product_id=$id AND o.status IN ('completed','processing','shipped','delivered') ");
$sold = (int)($sold_data['total'] ?? 0);
$revenue = $sold * (float)$productfound['price_gashy'];
$orders = getQuery(" SELECT o.id,o.status,o.created_at,oi.quantity,a.accountname FROM order_items oi JOIN orders o ON oi.order_id=o.id LEFT JOIN accounts a ON o.account_id=a.id WHERE oi.product_id=$id ORDER BY o.id DESC LIMIT 20 ");
$img = json_decode($productfound['images'], true)[0] ?? '';
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex items-center gap-4 mb-6"><a href="products.php" class="p-2 rounded-lg bg-white dark:bg-white/5 text-gray-500 hover:text-white transition-colors"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Product Overview</h1>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm">
            <div class="text-xs font-bold text-gray-500 uppercase mb-2">Total Revenue</div>
            <div class="text-3xl font-black text-primary-500"><?= number_format($revenue, 2) ?> <span class="text-sm text-gray-400">GASHY</span></div>
        </div>
        <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm">
            <div class="text-xs font-bold text-gray-500 uppercase mb-2">Inventory Status</div>
            <div class="flex items-end gap-2">
                <div class="text-3xl font-black text-gray-900 dark:text-white"><?= $productfound['stock'] ?></div><span class="text-sm text-gray-500 mb-1">In Stock</span>
            </div>
            <div class="w-full bg-gray-100 dark:bg-white/10 h-1.5 rounded-full mt-3 overflow-hidden">
                <div class="bg-blue-500 h-full" style="width:<?= min(100, ($productfound['stock'] / ($productfound['stock'] + $sold + 1)) * 100) ?>%"></div>
            </div>
            <div class="text-[10px] text-gray-400 mt-2 text-right"><?= $sold ?> Units Sold</div>
        </div>
        <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm flex items-center gap-4">
            <div class="w-16 h-16 rounded-xl bg-gray-100 dark:bg-white/5 flex-shrink-0 overflow-hidden"><img src="../<?= $img ?>" class="w-full h-full object-cover"></div>
            <div class="overflow-hidden">
                <div class="text-xs font-bold text-primary-500 uppercase mb-1"><?= $productfound['cat_name'] ?></div>
                <h3 class="font-bold text-gray-900 dark:text-white truncate"><?= $productfound['title'] ?></h3>
                <p class="text-xs text-gray-500 truncate">Seller: <?= $productfound['store_name'] ?? 'System' ?></p>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm p-6">
            <h3 class="font-bold text-gray-900 dark:text-white mb-4">Recent Orders</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-gray-500 border-b border-gray-200 dark:border-white/5">
                            <th class="pb-2">Order</th>
                            <th class="pb-2">Buyer</th>
                            <th class="pb-2 text-center">Qty</th>
                            <th class="pb-2 text-center">Status</th>
                            <th class="pb-2 text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        <?php if (empty($orders)): ?><tr>
                                <td colspan="5" class="py-8 text-center text-gray-500">No orders yet.</td>
                            </tr><?php else: foreach ($orders as $o): ?>
                                <tr>
                                    <td class="py-3 font-bold text-primary-500"><a href="orderdetail.php?id=<?= $o['id'] ?>">#<?= $o['id'] ?></a></td>
                                    <td class="py-3 font-bold text-gray-900 dark:text-white"><?= $o['accountname'] ?? 'Guest' ?></td>
                                    <td class="py-3 text-center"><?= $o['quantity'] ?></td>
                                    <td class="py-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold <?= $o['status'] == 'completed' ? 'bg-green-500/10 text-green-500' : 'bg-gray-100 dark:bg-white/10 text-gray-500' ?>"><?= $o['status'] ?></span></td>
                                    <td class="py-3 text-right text-gray-500"><?= date('M d', strtotime($o['created_at'])) ?></td>
                                </tr>
                        <?php endforeach;
                                endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="lg:col-span-1 bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm p-6">
            <h3 class="font-bold text-gray-900 dark:text-white mb-4">Product Details</h3>
            <div class="space-y-4 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Unit Price</span><span class="font-mono font-bold text-gray-900 dark:text-white"><?= number_format($productfound['price_gashy'], 2) ?></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Type</span><span class="uppercase font-bold text-gray-900 dark:text-white"><?= $productfound['type'] ?></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Created</span><span class="text-gray-900 dark:text-white"><?= date('M d, Y', strtotime($productfound['created_at'])) ?></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Views</span><span class="text-gray-900 dark:text-white"><?= number_format($productfound['views']) ?></span></div>
                <div class="pt-4 border-t border-gray-100 dark:border-white/5"><span class="block text-gray-500 mb-2">Description</span>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed text-xs"><?= nl2br($productfound['description']) ?></p>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>