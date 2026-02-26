<?php
require_once 'init.php';
if (get('approve')) {
    $id = request('approve', 'get');
    execute("UPDATE sellers SET is_approved=1 WHERE account_id=$id");
    redirect('sellers.php?msg=approved');
}
if (get('reject')) {
    $id = request('reject', 'get');
    execute("UPDATE sellers SET is_approved=0 WHERE account_id=$id");
    redirect('sellers.php?msg=rejected');
}
if (get('delete')) {
    $id = request('delete', 'get');
    execute("DELETE FROM sellers WHERE account_id=$id");
    redirect('sellers.php?msg=deleted');
}
$search = request('search', 'get');
$page = max(1, (int)(request('page', 'get') ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;
$where = "WHERE 1=1";
if ($search) {
    $where .= " AND (s.store_name LIKE '%$search%' OR a.email LIKE '%$search%') ";
}
$sellers = getQuery(" SELECT s.*,a.accountname,a.email,a.wallet_address FROM sellers s JOIN accounts a ON s.account_id=a.id $where ORDER BY s.is_approved ASC, s.total_sales DESC LIMIT $limit OFFSET $offset ");
$total = countQuery(" SELECT 1 FROM sellers s JOIN accounts a ON s.account_id=a.id $where ");
$pages = ceil($total / $limit);
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Seller Management</h1>
            <p class="text-sm text-gray-500">Approve and monitor vendor stores.</p>
        </div>
    </div>
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200 dark:border-white/5 flex gap-4">
            <form class="flex-1 flex gap-2"><input type="text" name="search" value="<?= $search ?>" placeholder="Search store or email..." class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-gray-900 dark:text-white focus:border-primary-500 outline-none"><button type="submit" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 rounded-xl text-gray-600 dark:text-gray-300"><i class="fa-solid fa-search"></i></button></form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/5 text-xs uppercase text-gray-500 font-bold">
                        <th class="px-6 py-4">Store Info</th>
                        <th class="px-6 py-4">Owner</th>
                        <th class="px-6 py-4">Sales</th>
                        <th class="px-6 py-4">Rate</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php foreach ($sellers as $s): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-white"><?= $s['store_name'] ?></div>
                                <div class="text-xs text-blue-500">@<?= $s['store_slug'] ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white"><?= $s['accountname'] ?></div>
                                <div class="text-xs text-gray-500 font-mono"><?= substr($s['wallet_address'], 0, 6) ?>...</div>
                            </td>
                            <td class="px-6 py-4 font-bold"><?= sellerStats($s['account_id'])['total_sale'] ?>G</td>
                            <td class="px-6 py-4"><?= $s['commission_rate'] ?>%</td>
                            <td class="px-6 py-4"><?= $s['is_approved'] ? '<span class="text-green-500 text-xs font-bold bg-green-500/10 px-2 py-1 rounded">Live</span>' : '<span class="text-yellow-500 text-xs font-bold bg-yellow-500/10 px-2 py-1 rounded">Pending</span>' ?></td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="../seller.php?slug=<?= $s['store_slug'] ?>" target="_blank" class="p-2 text-gray-400 hover:text-blue-500" title="View Store">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="sellerdetail.php?id=<?= $s['account_id'] ?>" class="p-2 text-gray-400 hover:text-purple-500" title="Seller Products">
                                        <i class="fa-solid fa-boxes-stacked"></i>
                                    </a>
                                    <button type="button" onclick="navigator.clipboard.writeText('<?= $s['wallet_address'] ?>');" class="p-2 text-gray-400 hover:text-emerald-500" title="Copy Wallet">
                                        <i class="fa-solid fa-copy"></i>
                                    </button>
                                    <?php if (!$s['is_approved']): ?>
                                        <a href="?approve=<?= $s['account_id'] ?>" class="px-3 py-1 bg-green-600 hover:bg-green-500 text-white rounded-lg text-xs font-bold ml-2">Approve</a>
                                    <?php else: ?>
                                        <a href="?reject=<?= $s['account_id'] ?>" class="px-3 py-1 bg-yellow-600 hover:bg-yellow-500 text-white rounded-lg text-xs font-bold ml-2">Suspend</a>
                                    <?php endif; ?>
                                    <a href="?delete=<?= $s['account_id'] ?>" onclick="return confirm('Delete store?')" class="p-2 text-gray-400 hover:text-red-500 ml-1" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-white/5 flex justify-center gap-2"><?php for ($i = 1; $i <= $pages; $i++): ?><a href="?page=<?= $i ?>&search=<?= $search ?>" class="px-3 py-1 rounded-lg text-sm font-bold <?= $i == $page ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-white/5 text-gray-500 hover:bg-gray-200' ?>"><?= $i ?></a><?php endfor; ?></div>
    </div>
</main>
<?php require_once 'footer.php'; ?>