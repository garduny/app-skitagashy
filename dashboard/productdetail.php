<?php
require_once 'init.php';
$id = (int)request('id', 'get');
if (!$id) redirect('products.php');
$productfound = findQuery(" SELECT p.*,c.name cat_name,s.store_name FROM products p LEFT JOIN categories c ON p.category_id=c.id LEFT JOIN sellers s ON p.seller_id=s.account_id WHERE p.id=$id ");
if (!$productfound) redirect('products.php');
$img = json_decode($productfound['images'], true)[0] ?? '';
$gashyRate = toGashy();
$options = [];
if ($productfound['type'] == 'gift_card') {
    $options = getQuery(" SELECT * FROM gift_card_options WHERE product_id=$id ORDER BY id ASC ");
}
$codesTotal = 0;
$codesSold = 0;
if (in_array($productfound['type'], ['digital', 'gift_card'])) {
    $codesTotal = (int)countQuery(" SELECT 1 FROM gift_cards WHERE product_id=$id ");
    $codesSold = (int)countQuery(" SELECT 1 FROM gift_cards WHERE product_id=$id AND is_sold=1 ");
}
$sold_data = findQuery(" SELECT COALESCE(SUM(oi.quantity),0) total_qty,COALESCE(SUM(oi.price_usd_at_purchase*oi.quantity),0) total_rev FROM order_items oi JOIN orders o ON oi.order_id=o.id WHERE oi.product_id=$id AND o.status IN ('completed','processing','shipped','delivered') ");
$sold = (int)($sold_data['total_qty'] ?? 0);
$revenue = (float)($sold_data['total_rev'] ?? 0);
$orders = getQuery(" SELECT o.id,o.status,o.created_at,oi.quantity,oi.price_usd_at_purchase,a.accountname FROM order_items oi JOIN orders o ON oi.order_id=o.id LEFT JOIN accounts a ON o.account_id=a.id WHERE oi.product_id=$id ORDER BY o.id DESC LIMIT 20 ");
$usd = (float)$productfound['price_usd'];
$gashy = $gashyRate ? ($usd / $gashyRate) : 0;
$attrs = [];
if (!empty($productfound['attributes'])) {
    $tmp = json_decode($productfound['attributes'], true);
    if (is_array($tmp)) $attrs = $tmp;
}
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <a href="products.php" class="p-2 rounded-xl bg-white dark:bg-white/5 text-gray-500 hover:text-primary-500"><i class="fa-solid fa-arrow-left"></i></a>
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white">Product Detail</h1>
                <p class="text-sm text-gray-500">Control center for this product</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <?php if (in_array($productfound['type'], ['digital', 'gift_card'])): ?>
                <a href="inventory.php?product_id=<?= $id ?>" class="px-4 py-2 rounded-xl bg-primary-600 text-white text-xs font-bold">Open Inventory</a>
            <?php endif; ?>
            <?php if ($productfound['type'] == 'mystery_box'): ?>
                <a href="mystery-box-detail.php?id=<?= $id ?>" class="px-4 py-2 rounded-xl bg-amber-500 text-white text-xs font-bold">Open Mystery Box</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-5">
            <div class="text-xs font-bold uppercase text-gray-500 mb-2">Revenue</div>
            <div class="text-3xl font-black text-primary-500">$<?= number_format($revenue, 2) ?></div>
        </div>
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-5">
            <div class="text-xs font-bold uppercase text-gray-500 mb-2">Sold Units</div>
            <div class="text-3xl font-black text-gray-900 dark:text-white"><?= number_format($sold) ?></div>
        </div>
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-5">
            <div class="text-xs font-bold uppercase text-gray-500 mb-2">Stock</div>
            <div class="text-3xl font-black <?= $productfound['stock'] < 5 ? 'text-red-500' : 'text-green-500' ?>"><?= number_format($productfound['stock']) ?></div>
        </div>
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-5">
            <div class="text-xs font-bold uppercase text-gray-500 mb-2">Views</div>
            <div class="text-3xl font-black text-gray-900 dark:text-white"><?= number_format($productfound['views']) ?></div>
        </div>
    </div>
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6">
                <div class="flex items-start gap-4">
                    <div class="w-24 h-24 rounded-2xl overflow-hidden bg-gray-100 dark:bg-white/5 shrink-0">
                        <?php if ($img): ?>
                            <img src="../<?= ltrim($img, '/') ?>" class="w-full h-full object-cover">
                        <?php endif; ?>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-xs font-bold uppercase text-primary-500 mb-1"><?= $productfound['cat_name'] ?></div>
                        <h2 class="text-xl font-black text-gray-900 dark:text-white mb-2"><?= $productfound['title'] ?></h2>
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span class="px-2 py-1 rounded-lg text-[10px] uppercase font-bold bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400"><?= $productfound['type'] ?></span>
                            <?php if ($productfound['status'] == 'active'): ?>
                                <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-green-500/10 text-green-500">Active</span>
                            <?php elseif ($productfound['status'] == 'banned'): ?>
                                <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-red-500/10 text-red-500">Banned</span>
                            <?php else: ?>
                                <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-gray-500/10 text-gray-500">Inactive</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-sm text-gray-500">Seller: <?= $productfound['store_name'] ?: 'System' ?></p>
                    </div>
                </div>
                <?php if ($productfound['description']): ?>
                    <div class="mt-5 pt-5 border-t border-gray-100 dark:border-white/5">
                        <div class="text-xs font-bold uppercase text-gray-500 mb-2">Description</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed"><?= nl2br($productfound['description']) ?></div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-black text-gray-900 dark:text-white">Recent Orders</h3>
                    <span class="text-xs text-gray-500">Latest 20</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-white/5 text-gray-500">
                                <th class="py-2 text-left">Order</th>
                                <th class="py-2 text-left">Buyer</th>
                                <th class="py-2 text-center">Qty</th>
                                <th class="py-2 text-right">Paid</th>
                                <th class="py-2 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                            <?php if (!$orders): ?>
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-500">No orders yet</td>
                                </tr>
                                <?php else: foreach ($orders as $o): ?>
                                    <tr>
                                        <td class="py-3 font-bold text-primary-500"><a href="orderdetail.php?id=<?= $o['id'] ?>">#<?= $o['id'] ?></a></td>
                                        <td class="py-3 text-gray-900 dark:text-white"><?= $o['accountname'] ?: 'Guest' ?></td>
                                        <td class="py-3 text-center"><?= $o['quantity'] ?></td>
                                        <td class="py-3 text-right">$<?= number_format($o['price_usd_at_purchase'] * $o['quantity'], 2) ?></td>
                                        <td class="py-3 text-center"><span class="px-2 py-1 rounded text-[10px] uppercase font-bold <?= $o['status'] == 'completed' ? 'bg-green-500/10 text-green-500' : 'bg-gray-100 dark:bg-white/10 text-gray-500' ?>"><?= $o['status'] ?></span></td>
                                    </tr>
                            <?php endforeach;
                            endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="space-y-6">
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6">
                <h3 class="font-black text-gray-900 dark:text-white mb-4">Pricing</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">USD</span><span class="font-bold">$<?= number_format($usd, 2) ?></span></div>
                    <div class="flex justify-between"><span class="text-gray-500">GASHY</span><span class="font-bold text-primary-500"><?= number_format($gashy, 2) ?></span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Created</span><span><?= date('M d,Y', strtotime($productfound['created_at'])) ?></span></div>
                </div>
            </div>
            <?php if (in_array($productfound['type'], ['digital', 'gift_card'])): ?>
                <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6">
                    <h3 class="font-black text-gray-900 dark:text-white mb-4">Inventory Info</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Total Codes</span><span class="font-bold"><?= $codesTotal ?></span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Available</span><span class="font-bold text-green-500"><?= max(0, $codesTotal - $codesSold) ?></span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Sold</span><span class="font-bold text-blue-500"><?= $codesSold ?></span></div>
                    </div>
                    <div class="mt-4">
                        <a href="inventory.php?product_id=<?= $id ?>" class="block text-center px-4 py-2 rounded-xl bg-primary-600 text-white text-xs font-bold">Manage Inventory</a>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($productfound['type'] == 'gift_card'): ?>
                <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6">
                    <h3 class="font-black text-gray-900 dark:text-white mb-4">Gift Card Options</h3>
                    <div class="space-y-2">
                        <?php if (!$options): ?>
                            <div class="text-sm text-gray-500">No options</div>
                            <?php else: foreach ($options as $op): $og = $gashyRate ? ($op['price_usd'] / $gashyRate) : 0; ?>
                                <div class="flex justify-between text-sm bg-gray-50 dark:bg-white/5 px-3 py-2 rounded-xl">
                                    <span><?= $op['name'] ?></span>
                                    <span class="font-bold">$<?= number_format($op['price_usd'], 2) ?> <span class="text-primary-500 text-xs"><?= number_format($og, 2) ?></span></span>
                                </div>
                        <?php endforeach;
                        endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($attrs): ?>
                <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6">
                    <h3 class="font-black text-gray-900 dark:text-white mb-4">Attributes</h3>
                    <div class="space-y-3">
                        <?php foreach ($attrs as $k => $v): ?>
                            <div>
                                <div class="text-xs font-bold uppercase text-gray-500 mb-2"><?= htmlspecialchars($k) ?></div>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ((array)$v as $vv): ?>
                                        <span class="px-2 py-1 rounded-lg bg-gray-100 dark:bg-white/5 text-xs"><?= htmlspecialchars($vv) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($productfound['type'] == 'mystery_box'): ?>
                <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6">
                    <h3 class="font-black text-gray-900 dark:text-white mb-4">Mystery Box</h3>
                    <p class="text-sm text-gray-500 mb-4">Manage rewards, odds and box content.</p>
                    <a href="mystery-box-detail.php?id=<?= $id ?>" class="block text-center px-4 py-2 rounded-xl bg-amber-500 text-white text-xs font-bold">Open Mystery Box Panel</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>