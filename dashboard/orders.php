<?php
require_once 'init.php';
$status = request('status', 'get');
$search = request('search', 'get');
$page = max(1, (int)(request('page', 'get') ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;
$where = "WHERE 1=1";
if ($status) {
    $where .= " AND o.status='$status' ";
}
if ($search) {
    $where .= " AND (o.tx_signature LIKE '%$search%' OR a.accountname LIKE '%$search%') ";
}
$orders = getQuery(" SELECT o.*,a.accountname,a.wallet_address FROM orders o JOIN accounts a ON o.account_id=a.id $where ORDER BY o.id DESC LIMIT $limit OFFSET $offset ");
$total = countQuery(" SELECT COUNT(*) FROM orders o JOIN accounts a ON o.account_id=a.id $where ");
$pages = ceil($total / $limit);
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Order Management</h1>
            <p class="text-sm text-gray-500">Track all blockchain transactions.</p>
        </div>
        <div class="flex bg-white dark:bg-dark-800 p-1 rounded-xl border border-gray-200 dark:border-white/10">
            <a href="orders.php" class="px-3 py-1.5 rounded-lg text-xs font-bold <?= !$status ? 'bg-primary-500 text-white' : 'text-gray-500' ?>">All</a>
            <a href="?status=completed" class="px-3 py-1.5 rounded-lg text-xs font-bold <?= $status == 'completed' ? 'bg-primary-500 text-white' : 'text-gray-500' ?>">Paid</a>
            <a href="?status=pending" class="px-3 py-1.5 rounded-lg text-xs font-bold <?= $status == 'pending' ? 'bg-primary-500 text-white' : 'text-gray-500' ?>">Pending</a>
        </div>
    </div>
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200 dark:border-white/5 flex gap-4">
            <form class="flex-1 flex gap-2"><input type="hidden" name="status" value="<?= $status ?>"><input type="text" name="search" value="<?= $search ?>" placeholder="Search transaction or user..." class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-gray-900 dark:text-white focus:border-primary-500 outline-none"><button type="submit" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 rounded-xl text-gray-600 dark:text-gray-300"><i class="fa-solid fa-search"></i></button></form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/5 text-xs uppercase text-gray-500 font-bold">
                        <th class="px-6 py-4">Order ID</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Signature</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php foreach ($orders as $o): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">#<?= $o['id'] ?></td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900 dark:text-white"><?= $o['accountname'] ?? 'Guest' ?></div>
                                <div class="text-xs text-gray-500 font-mono"><?= substr($o['wallet_address'], 0, 6) ?>...</div>
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-primary-500"><?= number_format($o['total_gashy'], 2) ?></td>
                            <td class="px-6 py-4 text-xs font-mono text-gray-500 truncate max-w-[100px]"><?= $o['tx_signature'] ?></td>
                            <td class="px-6 py-4"><span class="px-2 py-1 rounded text-[10px] uppercase font-bold <?= $o['status'] == 'completed' ? 'text-green-500 bg-green-500/10' : ($o['status'] == 'pending' ? 'text-yellow-500 bg-yellow-500/10' : 'text-red-500 bg-red-500/10') ?>"><?= $o['status'] ?></span></td>
                            <td class="px-6 py-4 text-sm text-gray-500"><?= date('M d, H:i', strtotime($o['created_at'])) ?></td>
                            <td class="px-6 py-4 text-right"><a href="orderdetail.php?id=<?= $o['id'] ?>" class="p-2 text-gray-400 hover:text-primary-500"><i class="fa-solid fa-eye"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-white/5 flex justify-center gap-2"><?php for ($i = 1; $i <= $pages; $i++): ?><a href="?page=<?= $i ?>&search=<?= $search ?>&status=<?= $status ?>" class="px-3 py-1 rounded-lg text-sm font-bold <?= $i == $page ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-white/5 text-gray-500 hover:bg-gray-200' ?>"><?= $i ?></a><?php endfor; ?></div>
    </div>
</main>
<?php require_once 'footer.php'; ?>