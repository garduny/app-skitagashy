<?php
require_once 'init.php';
$status = request('status', 'get');
$search = trim((string)request('search', 'get'));
$page = max(1, (int)(request('page', 'get') ?: 1));
$limit = 20;
$offset = ($page - 1) * $limit;
$where = " WHERE 1=1 ";
if ($status) {
    $where .= " AND o.status='$status' ";
}
if ($search !== '') {
    $searchId = (int)$search;
    $where .= " AND (o.id=$searchId OR o.tx_signature LIKE '%$search%' OR a.accountname LIKE '%$search%' OR a.wallet_address LIKE '%$search%' OR a.email LIKE '%$search%') ";
}
$orders = getQuery(" SELECT o.*,a.accountname,a.wallet_address,a.email,(SELECT COALESCE(SUM(oi.quantity),0) FROM order_items oi WHERE oi.order_id=o.id) item_count FROM orders o JOIN accounts a ON o.account_id=a.id $where ORDER BY o.id DESC LIMIT $limit OFFSET $offset ");
$total = countQuery(" SELECT 1 FROM orders o JOIN accounts a ON o.account_id=a.id $where ");
$pages = ceil($total / $limit);
$pendingCount = (int)countQuery(" SELECT 1 FROM orders WHERE status='pending' ");
$processingCount = (int)countQuery(" SELECT 1 FROM orders WHERE status='processing' ");
$completedCount = (int)countQuery(" SELECT 1 FROM orders WHERE status='completed' ");
$totalSales = findQuery(" SELECT COALESCE(SUM(total_usd),0) total_usd,COALESCE(SUM(total_gashy),0) total_gashy FROM orders WHERE status IN ('processing','shipped','delivered','completed') ");
function filterUrl($key, $val)
{
    global $status, $search;
    $params = ['status' => $status, 'search' => $search];
    $params[$key] = $val;
    return '?' . http_build_query(array_filter($params, function ($v) {
        return $v !== null && $v !== '';
    }));
}
function orderStatusClass($status)
{
    if ($status === 'completed' || $status === 'delivered') return 'text-green-500 bg-green-500/10';
    if ($status === 'pending') return 'text-yellow-500 bg-yellow-500/10';
    if ($status === 'processing' || $status === 'shipped') return 'text-blue-500 bg-blue-500/10';
    if ($status === 'refunded') return 'text-purple-500 bg-purple-500/10';
    return 'text-red-500 bg-red-500/10';
}
$gashyRate = toGashy();
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Order Management</h1>
            <p class="text-sm text-gray-500">Track <?= $total ?> blockchain transactions.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="orders.php" class="px-4 py-2 rounded-xl text-xs font-bold <?= !$status ? 'bg-primary-600 text-white' : 'bg-white dark:bg-white/5 text-gray-500' ?>">All</a>
            <a href="?status=pending" class="px-4 py-2 rounded-xl text-xs font-bold <?= $status === 'pending' ? 'bg-yellow-500 text-white' : 'bg-white dark:bg-white/5 text-gray-500' ?>">Pending</a>
            <a href="?status=processing" class="px-4 py-2 rounded-xl text-xs font-bold <?= $status === 'processing' ? 'bg-blue-500 text-white' : 'bg-white dark:bg-white/5 text-gray-500' ?>">Processing</a>
            <a href="?status=completed" class="px-4 py-2 rounded-xl text-xs font-bold <?= $status === 'completed' ? 'bg-green-500 text-white' : 'bg-white dark:bg-white/5 text-gray-500' ?>">Completed</a>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-5">
            <div class="text-xs font-bold uppercase text-gray-500 mb-2">Pending</div>
            <div class="text-3xl font-black text-yellow-500"><?= $pendingCount ?></div>
        </div>
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-5">
            <div class="text-xs font-bold uppercase text-gray-500 mb-2">Processing</div>
            <div class="text-3xl font-black text-blue-500"><?= $processingCount ?></div>
        </div>
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-5">
            <div class="text-xs font-bold uppercase text-gray-500 mb-2">Completed</div>
            <div class="text-3xl font-black text-green-500"><?= $completedCount ?></div>
        </div>
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-5">
            <div class="text-xs font-bold uppercase text-gray-500 mb-2">Revenue</div>
            <div class="text-2xl font-black text-primary-500">$<?= number_format((float)($totalSales['total_usd'] ?? 0), 2) ?></div>
            <div class="text-xs text-yellow-500 mt-1"><?= number_format((float)($totalSales['total_gashy'] ?? 0), 2) ?> GASHY</div>
        </div>
    </div>
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200 dark:border-white/5">
            <form class="flex flex-col md:flex-row gap-4">
                <select name="status" class="bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-gray-900 dark:text-white outline-none focus:border-primary-500">
                    <option value="">All Statuses</option>
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="processing" <?= $status === 'processing' ? 'selected' : '' ?>>Processing</option>
                    <option value="shipped" <?= $status === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                    <option value="delivered" <?= $status === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                    <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="refunded" <?= $status === 'refunded' ? 'selected' : '' ?>>Refunded</option>
                    <option value="failed" <?= $status === 'failed' ? 'selected' : '' ?>>Failed</option>
                </select>
                <div class="flex-1 flex gap-2">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search ID, User, Email, Wallet, Signature..." class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-gray-900 dark:text-white outline-none focus:border-primary-500">
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
                        <th class="px-6 py-4">Items</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Signature</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php if (!$orders): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500">No orders found.</td>
                        </tr>
                        <?php else: foreach ($orders as $o):
                            $usd = (float)$o['total_usd'];
                            $gashy = (float)$o['total_gashy'];
                            if ($gashy <= 0 && $gashyRate) $gashy = $usd / $gashyRate;
                            $wallet = trim((string)$o['wallet_address']);
                            $walletShort = $wallet !== '' ? substr($wallet, 0, 6) . '...' . substr($wallet, -4) : '-';
                        ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">#<?= $o['id'] ?></td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($o['accountname'] ?: 'Guest') ?></div>
                                    <div class="text-xs text-gray-500"><?= htmlspecialchars($o['email'] ?: '') ?></div>
                                    <div class="text-xs text-gray-500 font-mono"><?= htmlspecialchars($walletShort) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded bg-gray-100 dark:bg-white/10 text-xs font-bold text-gray-700 dark:text-gray-300"><?= (int)$o['item_count'] ?></span>
                                </td>
                                <td class="px-6 py-4 font-mono font-bold text-primary-500">
                                    $<?= number_format($usd, 2) ?>
                                    <span class="text-yellow-500 ml-2"><?= number_format($gashy, 2) ?> GASHY</span>
                                </td>
                                <td class="px-6 py-4 text-xs font-mono text-gray-500 max-w-[160px] truncate"><?= htmlspecialchars($o['tx_signature']) ?></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded text-[10px] uppercase font-bold <?= orderStatusClass($o['status']) ?>">
                                        <?= htmlspecialchars($o['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500"><?= date('M d, H:i', strtotime($o['created_at'])) ?></td>
                                <td class="px-6 py-4 text-right">
                                    <a href="orderdetail.php?id=<?= $o['id'] ?>" class="p-2 text-gray-400 hover:text-primary-500 transition-colors">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                    <?php endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-white/5 flex flex-wrap justify-center gap-2">
            <?php if ($pages > 1):
                $start = max(1, $page - 2);
                $end = min($pages, $page + 2);
                if ($page > 1): ?>
                    <a href="<?= filterUrl('page', $page - 1) ?>" class="px-3 py-1 rounded-lg text-sm font-bold bg-gray-100 dark:bg-white/5 text-gray-500 hover:bg-gray-200 dark:hover:bg-white/10"><i class="fa-solid fa-chevron-left"></i></a>
                <?php endif;
                for ($i = $start; $i <= $end; $i++): ?>
                    <a href="<?= filterUrl('page', $i) ?>" class="px-3 py-1 rounded-lg text-sm font-bold <?= $i == $page ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-white/5 text-gray-500 hover:bg-gray-200 dark:hover:bg-white/10' ?>"><?= $i ?></a>
                <?php endfor;
                if ($page < $pages): ?>
                    <a href="<?= filterUrl('page', $page + 1) ?>" class="px-3 py-1 rounded-lg text-sm font-bold bg-gray-100 dark:bg-white/5 text-gray-500 hover:bg-gray-200 dark:hover:bg-white/10"><i class="fa-solid fa-chevron-right"></i></a>
            <?php endif;
            endif; ?>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>