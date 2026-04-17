<?php
require_once 'init.php';
$id = (int)request('id', 'get');
if (!$id) redirect('orders.php');
$o = findQuery(" SELECT o.*,a.accountname,a.email,a.wallet_address FROM orders o JOIN accounts a ON o.account_id=a.id WHERE o.id=$id ");
if (!$o) redirect('orders.php');
$items = getQuery(" SELECT oi.*,p.title,p.images,p.type,gco.name option_name,gco.price_usd option_price_usd FROM order_items oi JOIN products p ON oi.product_id=p.id LEFT JOIN gift_card_options gco ON gco.id=oi.gift_card_option_id WHERE oi.order_id=$id ORDER BY oi.id ASC ");
$gashyRate = toGashy();
function orderStatusClass($status)
{
    if ($status === 'completed' || $status === 'delivered') return 'bg-green-500/10 text-green-500';
    if ($status === 'pending') return 'bg-yellow-500/10 text-yellow-500';
    if ($status === 'processing' || $status === 'shipped') return 'bg-blue-500/10 text-blue-500';
    if ($status === 'refunded') return 'bg-purple-500/10 text-purple-500';
    return 'bg-red-500/10 text-red-500';
}
if (post('update_status')) {
    $st = request('status', 'post');
    $allowed = ['pending', 'processing', 'shipped', 'delivered', 'completed', 'refunded', 'failed'];
    if (!in_array($st, $allowed)) $st = 'pending';
    $note = trim((string)request('note', 'post'));
    execute(" UPDATE orders SET status='$st' WHERE id=$id ");
    if ($note !== '') {
        $safe_note = secure($note);
        logActivity('admin', user()['id'], 'order_update', "Changed Order #$id to $st: $safe_note");
    }
    if ($st === 'delivered' && $o['email']) {
        $subject = "Order #$id Delivered";
        $body = "<h1>Your Order is Delivered!</h1><p>Status: <b>$st</b></p><p>Please check your dashboard.</p>";
        if (function_exists('mailer')) mailer($subject, $body, "Gashy Team", $o['email']);
    }
    redirect("orderdetail.php?id=$id&msg=updated");
}
$itemCount = 0;
$calcUsd = 0;
$calcGashy = 0;
foreach ($items as $row) {
    $qty = (int)$row['quantity'];
    $priceUsd = (float)$row['price_usd_at_purchase'];
    $priceGashy = (float)$row['price_at_purchase'];
    if ($priceUsd <= 0 && $gashyRate && $priceGashy > 0) $priceUsd = $priceGashy * $gashyRate;
    if ($priceGashy <= 0 && $gashyRate && $priceUsd > 0) $priceGashy = $priceUsd / $gashyRate;
    $itemCount += $qty;
    $calcUsd += $priceUsd * $qty;
    $calcGashy += $priceGashy * $qty;
}
$msg = request('msg', 'get');
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <a href="orders.php" class="p-2 rounded-lg bg-white dark:bg-white/5 text-gray-500 hover:text-primary-500 transition-colors"><i class="fa-solid fa-arrow-left"></i></a>
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Order #<?= $id ?></h1>
                <p class="text-sm text-gray-500">Detailed admin view for this order</p>
            </div>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase <?= orderStatusClass($o['status']) ?>"><?= htmlspecialchars($o['status']) ?></span>
    </div>
    <?php if ($msg === 'updated'): ?>
        <div class="p-4 mb-6 bg-green-100 dark:bg-green-500/20 border border-green-200 dark:border-green-500/30 text-green-600 dark:text-green-400 rounded-xl font-bold text-center"><i class="fa-solid fa-check-circle mr-2"></i> Order workflow updated successfully.</div>
    <?php endif; ?>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-5">
            <div class="text-xs font-bold uppercase text-gray-500 mb-2">Items</div>
            <div class="text-3xl font-black text-gray-900 dark:text-white"><?= $itemCount ?></div>
        </div>
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-5">
            <div class="text-xs font-bold uppercase text-gray-500 mb-2">Stored USD</div>
            <div class="text-2xl font-black text-primary-500">$<?= number_format((float)$o['total_usd'], 2) ?></div>
        </div>
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-5">
            <div class="text-xs font-bold uppercase text-gray-500 mb-2">Stored GASHY</div>
            <div class="text-2xl font-black text-yellow-500"><?= number_format((float)$o['total_gashy'], 2) ?></div>
        </div>
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-5">
            <div class="text-xs font-bold uppercase text-gray-500 mb-2">Calculated USD</div>
            <div class="text-2xl font-black <?= abs($calcUsd - (float)$o['total_usd']) < 0.01 ? 'text-green-500' : 'text-amber-500' ?>">$<?= number_format($calcUsd, 2) ?></div>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <h3 class="font-bold text-gray-900 dark:text-white">Items</h3>
                    <div class="text-xs text-gray-500"><?= count($items) ?> line item(s)</div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-gray-500 border-b border-gray-200 dark:border-white/5">
                                <th class="pb-3 pl-2">Item</th>
                                <th class="pb-3 text-right">Qty</th>
                                <th class="pb-3 text-right">USD</th>
                                <th class="pb-3 text-right">GASHY</th>
                                <th class="pb-3 text-right pr-2">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                            <?php if (!$items): ?>
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-gray-500">No order items found.</td>
                                </tr>
                                <?php else: foreach ($items as $i):
                                    $img = json_decode($i['images'], true)[0] ?? '';
                                    $usd = (float)$i['price_usd_at_purchase'];
                                    $gashy = (float)$i['price_at_purchase'];
                                    if ($usd <= 0 && $gashyRate && $gashy > 0) $usd = $gashy * $gashyRate;
                                    if ($gashy <= 0 && $gashyRate && $usd > 0) $gashy = $usd / $gashyRate;
                                    $totalUsd = $usd * (int)$i['quantity'];
                                    $totalGashy = $gashy * (int)$i['quantity'];
                                    $meta = [];
                                    if (!empty($i['meta_data'])) {
                                        $tmp = json_decode($i['meta_data'], true);
                                        if (is_array($tmp)) $meta = $tmp;
                                    }
                                ?>
                                    <tr>
                                        <td class="py-4 pl-2">
                                            <div class="flex items-start gap-3">
                                                <img src="../<?= ltrim($img, '/') ?>" class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-white/5 object-cover">
                                                <div class="min-w-0">
                                                    <div class="font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($i['title']) ?></div>
                                                    <div class="text-[10px] uppercase text-gray-500 mt-1"><?= htmlspecialchars($i['type']) ?></div>
                                                    <?php if (!empty($i['option_name'])): ?>
                                                        <div class="mt-2"><span class="inline-flex px-2 py-1 rounded-lg bg-primary-500/10 text-primary-500 text-[10px] font-bold">Option: <?= htmlspecialchars($i['option_name']) ?></span></div>
                                                    <?php endif; ?>
                                                    <?php if ($meta): ?>
                                                        <div class="flex flex-wrap gap-2 mt-2">
                                                            <?php foreach ($meta as $mk => $mv): ?>
                                                                <span class="inline-flex px-2 py-1 rounded-lg bg-gray-100 dark:bg-white/10 text-[10px] font-bold text-gray-600 dark:text-gray-300"><?= htmlspecialchars((string)$mk) ?>: <?= htmlspecialchars(is_array($mv) ? implode(', ', $mv) : (string)$mv) ?></span>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if ((int)$i['gift_card_option_id'] > 0 && empty($i['option_name'])): ?>
                                                        <div class="mt-2"><span class="inline-flex px-2 py-1 rounded-lg bg-amber-500/10 text-amber-500 text-[10px] font-bold">Option ID #<?= (int)$i['gift_card_option_id'] ?></span></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 text-right font-bold text-gray-900 dark:text-white"><?= (int)$i['quantity'] ?></td>
                                        <td class="py-4 text-right">
                                            $<?= number_format($usd, 2) ?>
                                        </td>
                                        <td class="py-4 text-right text-primary-500 font-medium">
                                            <?= number_format($gashy, 2) ?>
                                        </td>
                                        <td class="py-4 text-right pr-2 font-bold text-primary-500">
                                            $<?= number_format($totalUsd, 2) ?>
                                            <div class="text-xs text-gray-500"><?= number_format($totalGashy, 2) ?> GASHY</div>
                                        </td>
                                    </tr>
                            <?php endforeach;
                            endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-200 dark:border-white/5 mt-4 pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="rounded-2xl bg-gray-50 dark:bg-white/5 p-4">
                            <div class="text-xs text-gray-500 uppercase font-bold mb-1">Stored Total</div>
                            <div class="text-xl font-black text-primary-500">$<?= number_format((float)$o['total_usd'], 2) ?></div>
                            <div class="text-sm text-yellow-500"><?= number_format((float)$o['total_gashy'], 2) ?> GASHY</div>
                        </div>
                        <div class="rounded-2xl bg-gray-50 dark:bg-white/5 p-4">
                            <div class="text-xs text-gray-500 uppercase font-bold mb-1">Calculated from Items</div>
                            <div class="text-xl font-black <?= abs($calcUsd - (float)$o['total_usd']) < 0.01 ? 'text-green-500' : 'text-amber-500' ?>">$<?= number_format($calcUsd, 2) ?></div>
                            <div class="text-sm text-gray-500"><?= number_format($calcGashy, 2) ?> GASHY</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Customer</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between gap-3">
                        <span class="text-gray-500">Username</span>
                        <span class="text-gray-900 dark:text-white font-medium text-right"><?= htmlspecialchars($o['accountname'] ?: 'Guest') ?></span>
                    </div>
                    <div class="flex justify-between gap-3">
                        <span class="text-gray-500">Email</span>
                        <span class="text-gray-900 dark:text-white font-medium text-right break-all"><?= htmlspecialchars($o['email'] ?: '-') ?></span>
                    </div>
                    <div class="pt-3 border-t border-gray-100 dark:border-white/5">
                        <span class="text-xs text-gray-500 uppercase block mb-1">Wallet</span>
                        <span class="text-xs font-mono text-primary-500 break-all bg-primary-500/10 px-2 py-1 rounded block"><?= htmlspecialchars($o['wallet_address'] ?: '-') ?></span>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Order Info</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between gap-3">
                        <span class="text-gray-500">Order ID</span>
                        <span class="font-bold text-gray-900 dark:text-white">#<?= $id ?></span>
                    </div>
                    <div class="flex justify-between gap-3">
                        <span class="text-gray-500">Status</span>
                        <span class="px-2 py-1 rounded text-[10px] uppercase font-bold <?= orderStatusClass($o['status']) ?>"><?= htmlspecialchars($o['status']) ?></span>
                    </div>
                    <div class="flex justify-between gap-3">
                        <span class="text-gray-500">Created</span>
                        <span class="font-medium text-gray-900 dark:text-white"><?= !empty($o['created_at']) ? date('M d, Y H:i', strtotime($o['created_at'])) : '-' ?></span>
                    </div>
                    <div class="pt-3 border-t border-gray-100 dark:border-white/5">
                        <span class="text-xs text-gray-500 uppercase block mb-1">Transaction Signature</span>
                        <span class="text-xs font-mono text-gray-600 dark:text-gray-300 break-all bg-gray-100 dark:bg-white/5 px-2 py-1 rounded block"><?= htmlspecialchars($o['tx_signature'] ?: '-') ?></span>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Workflow</h3>
                <form method="POST">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Update Status</label>
                            <select name="status" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none">
                                <option value="pending" <?= $o['status'] === 'pending' ? 'selected' : '' ?>>Pending Payment</option>
                                <option value="processing" <?= $o['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                                <option value="shipped" <?= $o['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                <option value="delivered" <?= $o['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                <option value="completed" <?= $o['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="refunded" <?= $o['status'] === 'refunded' ? 'selected' : '' ?>>Refunded</option>
                                <option value="failed" <?= $o['status'] === 'failed' ? 'selected' : '' ?>>Failed</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Internal Note</label>
                            <textarea name="note" rows="3" placeholder="Tracking number, manual note, issue reason..." class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></textarea>
                        </div>
                        <button type="submit" name="update_status" value="1" class="w-full py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl transition-all">Update Workflow</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>