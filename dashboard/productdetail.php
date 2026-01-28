<?php
require_once 'init.php';
$id = (int)request('id', 'get');
if ($id === 0) {
    echo "<div class='ml-0 lg:ml-64 pt-20 p-6 text-red-500 font-bold'>Invalid Product ID</div>";
    require_once 'footer.php';
    exit;
}
$p = findQuery(" SELECT p.*,c.name as cat_name,s.store_name FROM products p LEFT JOIN categories c ON p.category_id=c.id LEFT JOIN sellers s ON p.seller_id=s.account_id WHERE p.id=$id ");
if (!is_array($p)) {
    echo "<div class='ml-0 lg:ml-64 pt-20 p-6 text-red-500 font-bold'>Product Not Found in Database</div>";
    require_once 'footer.php';
    exit;
}
$orders = getQuery(" SELECT o.id,o.created_at,o.status,oi.quantity,oi.price_at_purchase,a.accountname FROM order_items oi JOIN orders o ON oi.order_id=o.id LEFT JOIN accounts a ON o.account_id=a.id WHERE oi.product_id=$id ORDER BY o.id DESC LIMIT 50 ");
if (!is_array($orders)) $orders = [];
$stats = findQuery(" SELECT SUM(quantity) as sold, SUM(quantity*price_at_purchase) as revenue FROM order_items WHERE product_id=$id ");
if (!is_array($stats)) $stats = ['sold' => 0, 'revenue' => 0];
$img_data = json_decode($p['images'], true);
$main_image = (is_array($img_data) && !empty($img_data)) ? $img_data[0] : 'https://via.placeholder.com/300?text=No+Image';
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex items-center gap-4 mb-6"><a href="products.php" class="p-2 rounded-lg bg-white dark:bg-white/5 text-gray-500 hover:text-white transition-colors"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Product Details</h1>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <div class="aspect-square rounded-xl bg-gray-100 dark:bg-white/5 mb-4 overflow-hidden relative"><img src="<?= $main_image ?>" class="w-full h-full object-cover"><span class="absolute top-2 right-2 px-2 py-1 bg-black/60 text-white text-[10px] font-bold rounded uppercase backdrop-blur"><?= $p['type'] ?></span></div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-1"><?= $p['title'] ?></h3>
                <p class="text-sm text-gray-500 mb-4">SKU: #<?= $p['id'] ?></p>
                <div class="grid grid-cols-2 gap-4 border-t border-gray-100 dark:border-white/5 pt-4">
                    <div class="text-center">
                        <div class="text-xs text-gray-500 uppercase">Price</div>
                        <div class="text-lg font-bold text-primary-500"><?= number_format($p['price_gashy'], 2) ?></div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-gray-500 uppercase">Stock</div>
                        <div class="text-lg font-bold <?= $p['stock'] < 5 ? 'text-red-500' : 'text-gray-900 dark:text-white' ?>"><?= $p['stock'] ?></div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-white/5">
                    <div class="flex justify-between text-sm mb-2"><span class="text-gray-500">Category</span><span class="font-bold text-gray-900 dark:text-white"><?= $p['cat_name'] ?? 'Uncategorized' ?></span></div>
                    <div class="flex justify-between text-sm mb-2"><span class="text-gray-500">Seller</span><span class="font-bold text-gray-900 dark:text-white"><?= $p['store_name'] ?? 'System' ?></span></div>
                    <div class="flex justify-between text-sm"><span class="text-gray-500">Status</span><span class="font-bold uppercase <?= $p['status'] == 'active' ? 'text-green-500' : 'text-red-500' ?>"><?= $p['status'] ?></span></div>
                </div>
            </div>
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Analytics</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center"><span class="text-gray-500 text-sm">Total Units Sold</span><span class="font-bold text-gray-900 dark:text-white"><?= number_format($stats['sold'] ?? 0) ?></span></div>
                    <div class="flex justify-between items-center"><span class="text-gray-500 text-sm">Total Revenue Generated</span><span class="font-bold text-green-500"><?= number_format($stats['revenue'] ?? 0) ?> G</span></div>
                    <div class="flex justify-between items-center"><span class="text-gray-500 text-sm">Page Views</span><span class="font-bold text-gray-900 dark:text-white"><?= number_format($p['views']) ?></span></div>
                </div>
            </div>
        </div>
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Description</h3>
                <div class="prose prose-sm dark:prose-invert text-gray-500 max-w-none">
                    <p><?= nl2br($p['description']) ?></p>
                </div>
            </div>
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm overflow-hidden">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Order History</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-gray-500 border-b border-gray-200 dark:border-white/5">
                                <th class="pb-2">Order ID</th>
                                <th class="pb-2">Buyer</th>
                                <th class="pb-2">Qty</th>
                                <th class="pb-2">Total</th>
                                <th class="pb-2">Status</th>
                                <th class="pb-2 text-right">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                            <?php if (empty($orders)): ?><tr>
                                    <td colspan="6" class="py-8 text-center text-gray-500">No orders yet for this product.</td>
                                </tr><?php else: foreach ($orders as $o): ?>
                                    <tr>
                                        <td class="py-3 font-bold text-gray-900 dark:text-white">#<?= $o['id'] ?></td>
                                        <td class="py-3"><?= $o['accountname'] ?? 'Guest' ?></td>
                                        <td class="py-3"><?= $o['quantity'] ?></td>
                                        <td class="py-3 font-bold text-primary-500"><?= number_format($o['price_at_purchase'] * $o['quantity'], 2) ?></td>
                                        <td class="py-3"><span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold <?= $o['status'] == 'completed' ? 'bg-green-500/10 text-green-500' : 'bg-gray-100 dark:bg-white/10 text-gray-500' ?>"><?= $o['status'] ?></span></td>
                                        <td class="py-3 text-right text-gray-500"><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
                                    </tr>
                            <?php endforeach;
                                    endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>