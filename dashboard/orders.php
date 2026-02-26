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
    $where .= " AND (o.tx_signature LIKE '%$search%' OR a.accountname LIKE '%$search%' OR a.wallet_address LIKE '%$search%') ";
}
$orders = getQuery(" SELECT o.*,a.accountname,a.wallet_address FROM orders o JOIN accounts a ON o.account_id=a.id $where ORDER BY o.id DESC LIMIT $limit OFFSET $offset ");
$total = countQuery(" SELECT 1 FROM orders o JOIN accounts a ON o.account_id=a.id $where ");
$pages = ceil($total / $limit);
function filterUrl($key, $val)
{
    global $status, $search;
    $p = ['status' => $status, 'search' => $search];
    $p[$key] = $val;
    return '?' . http_build_query(array_filter($p));
}
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Order Management</h1>
            <p class="text-sm text-gray-500">Track <?= $total ?> blockchain transactions.</p>
        </div>
        <div class="flex gap-2">
            <a href="orders.php" class="px-4 py-2 rounded-xl text-xs font-bold <?= !$status ? 'bg-primary-600 text-white' : 'bg-white dark:bg-white/5 text-gray-500' ?>">All</a>
            <a href="?status=pending" class="px-4 py-2 rounded-xl text-xs font-bold <?= $status == 'pending' ? 'bg-yellow-500 text-white' : 'bg-white dark:bg-white/5 text-gray-500' ?>">Pending</a>
            <a href="?status=processing" class="px-4 py-2 rounded-xl text-xs font-bold <?= $status == 'processing' ? 'bg-blue-500 text-white' : 'bg-white dark:bg-white/5 text-gray-500' ?>">Processing</a>
            <a href="?status=completed" class="px-4 py-2 rounded-xl text-xs font-bold <?= $status == 'completed' ? 'bg-green-500 text-white' : 'bg-white dark:bg-white/5 text-gray-500' ?>">Completed</a>
        </div>
    </div>
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200 dark:border-white/5">
            <form class="flex flex-col md:flex-row gap-4">
                <select name="status" class="bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-gray-900 dark:text-white outline-none focus:border-primary-500">
                    <option value="">All Statuses</option>
                    <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="processing" <?= $status == 'processing' ? 'selected' : '' ?>>Processing</option>
                    <option value="shipped" <?= $status == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                    <option value="delivered" <?= $status == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                    <option value="completed" <?= $status == 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="refunded" <?= $status == 'refunded' ? 'selected' : '' ?>>Refunded</option>
                    <option value="failed" <?= $status == 'failed' ? 'selected' : '' ?>>Failed</option>
                </select>
                <div class="flex-1 flex gap-2">
                    <input type="text" name="search" value="<?= $search ?>" placeholder="Search ID, User, Wallet..." class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-gray-900 dark:text-white outline-none focus:border-primary-500">
                    <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-500 text-white rounded-xl transition-colors"><i class="fa-solid fa-search"></i></button>
                    <a href="orders.php" class="px-4 py-2 bg-gray-100 dark:bg-white/10 text-gray-500 hover:text-red-500 rounded-xl flex items-center justify-center transition-colors"><i class="fa-solid fa-times"></i></a>
                </div>
            </form>
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
                            <td class="px-6 py-4"><span class="px-2 py-1 rounded text-[10px] uppercase font-bold <?= $o['status'] == 'completed' ? 'text-green-500 bg-green-500/10' : ($o['status'] == 'pending' ? 'text-yellow-500 bg-yellow-500/10' : ($o['status'] == 'processing' ? 'text-blue-500 bg-blue-500/10' : 'text-red-500 bg-red-500/10')) ?>"><?= $o['status'] ?></span></td>
                            <td class="px-6 py-4 text-sm text-gray-500"><?= date('M d, H:i', strtotime($o['created_at'])) ?></td>
                            <td class="px-6 py-4 text-right"><a href="orderdetail.php?id=<?= $o['id'] ?>" class="p-2 text-gray-400 hover:text-primary-500 transition-colors"><i class="fa-solid fa-eye"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-white/5 flex flex-wrap justify-center gap-2">
            <?php if ($pages > 1):
                $start = max(1, $page - 2);
                $end = min($pages, $page + 2);
                if ($page > 1): ?><a href="<?= filterUrl('page', $page - 1) ?>" class="px-3 py-1 rounded-lg text-sm font-bold bg-gray-100 dark:bg-white/5 text-gray-500 hover:bg-gray-200 dark:hover:bg-white/10"><i class="fa-solid fa-chevron-left"></i></a><?php endif;
                                                                                                                                                                                                                                    for ($i = $start; $i <= $end; $i++): ?><a href="<?= filterUrl('page', $i) ?>" class="px-3 py-1 rounded-lg text-sm font-bold <?= $i == $page ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-white/5 text-gray-500 hover:bg-gray-200 dark:hover:bg-white/10' ?>"><?= $i ?></a><?php endfor;
                                                                                                                                                                                                                                                            if ($page < $pages): ?><a href="<?= filterUrl('page', $page + 1) ?>" class="px-3 py-1 rounded-lg text-sm font-bold bg-gray-100 dark:bg-white/5 text-gray-500 hover:bg-gray-200 dark:hover:bg-white/10"><i class="fa-solid fa-chevron-right"></i></a><?php endif;
                                                                                                                                                                                                                                                        endif; ?>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>