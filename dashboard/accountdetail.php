<?php
require_once 'init.php';
$id = request('id', 'get');
$acc = findQuery(" SELECT * FROM accounts WHERE id=$id ");
if (!$acc) redirect('accounts.php');
$orders = getQuery(" SELECT * FROM orders WHERE account_id=$id ORDER BY id DESC LIMIT 20 ");
$txs = getQuery(" SELECT * FROM transactions WHERE account_id=$id ORDER BY id DESC LIMIT 20 ");
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex items-center gap-4 mb-6">
        <a href="accounts.php" class="p-2 rounded-lg bg-white dark:bg-white/5 text-gray-500 hover:text-white"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Account #<?= $id ?></h1>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm text-center">
                <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-tr from-blue-500 to-purple-500 p-1 mb-4">
                    <div class="w-full h-full bg-white dark:bg-dark-800 rounded-full"></div>
                </div>
                <div class="font-mono text-sm font-bold text-primary-500 mb-1"><?= $acc['wallet_address'] ?></div>
                <div class="text-gray-500 text-sm mb-4"><?= $acc['username'] ?? 'Anonymous' ?></div>
                <div class="flex justify-center gap-2">
                    <span class="px-3 py-1 bg-gray-100 dark:bg-white/10 rounded-lg text-xs font-bold uppercase"><?= $acc['tier'] ?></span>
                    <span class="px-3 py-1 bg-gray-100 dark:bg-white/10 rounded-lg text-xs font-bold uppercase"><?= $acc['role'] ?></span>
                </div>
            </div>
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Details</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Joined</span> <span class="text-gray-900 dark:text-white"><?= date('M d, Y', strtotime($acc['created_at'])) ?></span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Referral Code</span> <span class="text-gray-900 dark:text-white font-mono"><?= $acc['my_referral_code'] ?? '-' ?></span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Status</span> <span class="<?= $acc['is_banned'] ? 'text-red-500' : 'text-green-500' ?> font-bold"><?= $acc['is_banned'] ? 'Banned' : 'Active' ?></span></div>
                </div>
            </div>
        </div>
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm overflow-hidden">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Recent Orders</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-gray-500 border-b border-gray-200 dark:border-white/5">
                                <th class="pb-2">ID</th>
                                <th class="pb-2">Total</th>
                                <th class="pb-2">Status</th>
                                <th class="pb-2 text-right">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                            <?php foreach ($orders as $o): ?>
                                <tr>
                                    <td class="py-3 font-bold text-gray-900 dark:text-white">#<?= $o['id'] ?></td>
                                    <td class="py-3 font-bold text-primary-500"><?= number_format($o['total_gashy'], 2) ?></td>
                                    <td class="py-3"><span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?= $o['status'] == 'completed' ? 'bg-green-500/10 text-green-500' : 'bg-yellow-500/10 text-yellow-500' ?>"><?= $o['status'] ?></span></td>
                                    <td class="py-3 text-right text-gray-500"><?= date('M d, H:i', strtotime($o['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm overflow-hidden">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Transactions</h3>
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
                                <tr>
                                    <td class="py-3 uppercase font-bold text-xs text-gray-500"><?= $t['type'] ?></td>
                                    <td class="py-3 font-bold text-white"><?= number_format($t['amount'], 2) ?></td>
                                    <td class="py-3 font-mono text-xs text-gray-500 truncate max-w-[150px]"><?= $t['tx_signature'] ?></td>
                                    <td class="py-3 text-right text-gray-500"><?= date('M d, H:i', strtotime($t['created_at'])) ?></td>
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