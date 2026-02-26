<?php
require_once 'init.php';
$id = (int)request('id', 'get');
$o = findQuery(" SELECT o.*,a.accountname,a.email,a.wallet_address FROM orders o JOIN accounts a ON o.account_id=a.id WHERE o.id=$id ");
if (!$o) redirect('orders.php');
$items = getQuery(" SELECT oi.*,p.title,p.images,p.type FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id=$id ");
if (post('update_status')) {
    $st = request('status', 'post');
    $note = request('note', 'post');
    execute(" UPDATE orders SET status='$st' WHERE id=$id ");
    if ($note) {
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
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex items-center gap-4 mb-6"><a href="orders.php" class="p-2 rounded-lg bg-white dark:bg-white/5 text-gray-500 hover:text-white transition-colors"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Order #<?= $id ?></h1><span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-blue-500/10 text-blue-500"><?= $o['status'] ?></span>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm overflow-hidden">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Items</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-gray-500 border-b border-gray-200 dark:border-white/5">
                                <th class="pb-3 pl-2">Item</th>
                                <th class="pb-3 text-right">Qty</th>
                                <th class="pb-3 text-right">Price</th>
                                <th class="pb-3 text-right pr-2">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5"><?php foreach ($items as $i): $img = json_decode($i['images'])[0] ?? ''; ?><tr>
                                    <td class="py-3 pl-2">
                                        <div class="flex items-center gap-3"><img src="../<?= $img ?>" class="w-10 h-10 rounded bg-gray-100 dark:bg-white/5 object-cover">
                                            <div><span class="font-bold text-gray-900 dark:text-white block"><?= $i['title'] ?></span><span class="text-[10px] uppercase text-gray-500"><?= $i['type'] ?></span></div>
                                        </div>
                                    </td>
                                    <td class="py-3 text-right"><?= $i['quantity'] ?></td>
                                    <td class="py-3 text-right"><?= number_format($i['price_at_purchase'], 2) ?></td>
                                    <td class="py-3 text-right pr-2 font-bold text-primary-500"><?= number_format($i['price_at_purchase'] * $i['quantity'], 2) ?></td>
                                </tr><?php endforeach; ?></tbody>
                    </table>
                </div>
                <div class="border-t border-gray-200 dark:border-white/5 mt-4 pt-4 text-right">
                    <div class="text-xs text-gray-500 uppercase font-bold">Total Paid</div>
                    <div class="text-2xl font-black text-gray-900 dark:text-white"><?= number_format($o['total_gashy'], 2) ?> <span class="text-sm text-primary-500">GASHY</span></div>
                </div>
            </div>
        </div>
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Customer</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Username</span> <span class="text-gray-900 dark:text-white font-medium"><?= $o['accountname'] ?? 'Guest' ?></span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Email</span> <span class="text-gray-900 dark:text-white font-medium"><?= $o['email'] ?? '-' ?></span></div>
                    <div class="pt-3 border-t border-gray-100 dark:border-white/5"><span class="text-xs text-gray-500 uppercase block mb-1">Wallet</span><span class="text-xs font-mono text-primary-500 break-all bg-primary-500/10 px-2 py-1 rounded block"><?= $o['wallet_address'] ?></span></div>
                </div>
            </div>
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Workflow</h3>
                <form method="POST">
                    <div class="space-y-4">
                        <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Update Status</label><select name="status" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none">
                                <option value="pending" <?= $o['status'] == 'pending' ? 'selected' : '' ?>>Pending Payment</option>
                                <option value="processing" <?= $o['status'] == 'processing' ? 'selected' : '' ?>>Processing (Packing)</option>
                                <option value="shipped" <?= $o['status'] == 'shipped' ? 'selected' : '' ?>>Shipped / On Way</option>
                                <option value="delivered" <?= $o['status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                <option value="completed" <?= $o['status'] == 'completed' ? 'selected' : '' ?>>Completed (Final)</option>
                                <option value="refunded" <?= $o['status'] == 'refunded' ? 'selected' : '' ?>>Refunded</option>
                                <option value="failed" <?= $o['status'] == 'failed' ? 'selected' : '' ?>>Failed</option>
                            </select></div>
                        <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Internal Note</label><textarea name="note" rows="2" placeholder="Tracking # or Reason..." class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></textarea></div><button type="submit" name="update_status" value="1" class="w-full py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl transition-all">Update Workflow</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>