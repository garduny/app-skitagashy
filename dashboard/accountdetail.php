<?php
require_once 'init.php';
$id = (int)request('id', 'get');
$acc = findQuery(" SELECT * FROM accounts WHERE id=$id ");
if (!$acc) redirect('accounts.php');
$orders = getQuery(" SELECT * FROM orders WHERE account_id=$id ORDER BY id DESC LIMIT 20 ");
$txs = getQuery(" SELECT * FROM transactions WHERE account_id=$id ORDER BY id DESC LIMIT 20 ");
$orderStats = findQuery(" SELECT COUNT(*) total_orders,COALESCE(SUM(total_gashy),0) total_spent FROM orders WHERE account_id=$id ");
$txStats = findQuery(" SELECT COUNT(*) total_txs,COALESCE(SUM(amount),0) total_amount FROM transactions WHERE account_id=$id ");
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex items-center gap-4 mb-6">
        <a href="accounts.php" class="p-2 rounded-xl bg-white dark:bg-white/5 text-gray-500 hover:text-primary-500 transition-colors"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white">Account #<?= $id ?></h1>
            <p class="text-sm text-gray-500">User profile, orders and transactions</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-1 space-y-6">

            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm text-center">
                <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-tr from-blue-500 to-purple-500 p-1 mb-4">
                    <div class="w-full h-full bg-white dark:bg-dark-800 rounded-full flex items-center justify-center text-2xl font-bold text-gray-800 dark:text-white"><?= strtoupper(substr($acc['username'] ?: $acc['accountname'] ?: 'A', 0, 1)) ?></div>
                </div>
                <div class="font-mono text-xs font-bold text-primary-500 mb-2 break-all"><?= $acc['wallet_address'] ?></div>
                <div class="text-gray-900 dark:text-white font-bold"><?= $acc['username'] ?: $acc['accountname'] ?: 'Anonymous' ?></div>
                <?php if (!empty($acc['email'])): ?><div class="text-sm text-gray-500 mb-4"><?= $acc['email'] ?></div><?php else: ?><div class="text-sm text-gray-500 mb-4">No email</div><?php endif; ?>
                <div class="flex justify-center gap-2 flex-wrap">
                    <span class="px-3 py-1 bg-gray-100 dark:bg-white/10 rounded-lg text-xs font-bold uppercase"><?= $acc['tier'] ?: 'basic' ?></span>
                    <span class="px-3 py-1 bg-gray-100 dark:bg-white/10 rounded-lg text-xs font-bold uppercase"><?= $acc['role'] ?: 'user' ?></span>
                    <?php if (!empty($acc['is_verified'])): ?><span class="px-3 py-1 bg-blue-500/10 text-blue-500 rounded-lg text-xs font-bold uppercase">Verified</span><?php endif; ?>
                </div>
            </div>

            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Details</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Joined</span><span class="text-gray-900 dark:text-white text-right"><?= !empty($acc['created_at']) ? date('M d, Y', strtotime($acc['created_at'])) : '-' ?></span></div>
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Referral Code</span><span class="text-gray-900 dark:text-white font-mono text-right"><?= $acc['my_referral_code'] ?: '-' ?></span></div>
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Referred By</span><span class="text-gray-900 dark:text-white font-mono text-right"><?= $acc['used_referral_code'] ?: '-' ?></span></div>
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Orders</span><span class="text-gray-900 dark:text-white font-bold"><?= number_format((int)$orderStats['total_orders']) ?></span></div>
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Spent</span><span class="text-primary-500 font-bold"><?= number_format((float)$orderStats['total_spent'], 2) ?> G</span></div>
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Status</span><span class="<?= $acc['is_banned'] ? 'text-red-500' : 'text-green-500' ?> font-bold"><?= $acc['is_banned'] ? 'Banned' : 'Active' ?></span></div>
                </div>
            </div>

        </div>

        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-900 dark:text-white">Recent Orders</h3>
                    <span class="text-sm text-gray-500"><?= count($orders) ?> shown</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-gray-500 border-b border-gray-200 dark:border-white/5">
                                <th class="pb-2">ID</th>
                                <th class="pb-2">USD</th>
                                <th class="pb-2">GASHY</th>
                                <th class="pb-2">Status</th>
                                <th class="pb-2 text-right">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                            <?php foreach ($orders as $o): ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                    <td class="py-3 font-bold text-gray-900 dark:text-white">#<?= $o['id'] ?></td>
                                    <td class="py-3">$<?= number_format((float)$o['total_usd'], 2) ?></td>
                                    <td class="py-3 font-bold text-primary-500"><?= number_format((float)$o['total_gashy'], 2) ?></td>
                                    <td class="py-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?= $o['status'] == 'completed' ? 'bg-green-500/10 text-green-500' : ($o['status'] == 'cancelled' ? 'bg-red-500/10 text-red-500' : 'bg-yellow-500/10 text-yellow-500') ?>"><?= $o['status'] ?></span>
                                    </td>
                                    <td class="py-3 text-right text-gray-500"><?= date('M d, H:i', strtotime($o['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (!$orders): ?>
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-gray-400">No orders found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-900 dark:text-white">Transactions</h3>
                    <span class="text-sm text-gray-500"><?= number_format((int)$txStats['total_txs']) ?> total</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-gray-500 border-b border-gray-200 dark:border-white/5">
                                <th class="pb-2">Type</th>
                                <th class="pb-2">Amount</th>
                                <th class="pb-2">Signature</th>
                                <th class="pb-2 text-right">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                            <?php foreach ($txs as $t): ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                    <td class="py-3 uppercase font-bold text-xs text-gray-500"><?= $t['type'] ?></td>
                                    <td class="py-3 font-bold text-primary-500"><?= number_format((float)$t['amount'], 2) ?></td>
                                    <td class="py-3 font-mono text-xs text-gray-500 truncate max-w-[180px]"><?= $t['tx_signature'] ?: '-' ?></td>
                                    <td class="py-3 text-right text-gray-500"><?= date('M d, H:i', strtotime($t['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (!$txs): ?>
                                <tr>
                                    <td colspan="4" class="py-10 text-center text-gray-400">No transactions found</td>
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