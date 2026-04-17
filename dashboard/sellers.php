<?php
require_once 'init.php';
if (get('approve')) {
    $id = (int)request('approve', 'get');
    execute(" UPDATE sellers SET is_approved=1 WHERE account_id=$id ");
    redirect('sellers.php?msg=approved');
}
if (get('reject')) {
    $id = (int)request('reject', 'get');
    execute(" UPDATE sellers SET is_approved=0 WHERE account_id=$id ");
    redirect('sellers.php?msg=rejected');
}
if (get('delete')) {
    $id = (int)request('delete', 'get');
    execute(" DELETE FROM sellers WHERE account_id=$id ");
    redirect('sellers.php?msg=deleted');
}
$search = trim((string)request('search', 'get'));
$page = max(1, (int)(request('page', 'get') ?: 1));
$limit = 10;
$offset = ($page - 1) * $limit;
$where = " WHERE 1=1 ";
if ($search) $where .= " AND (s.store_name LIKE '%$search%' OR s.store_slug LIKE '%$search%' OR a.email LIKE '%$search%' OR a.accountname LIKE '%$search%') ";
$sellers = getQuery(" SELECT s.*,a.accountname,a.email,a.wallet_address,a.tier,a.is_verified,a.is_banned,a.created_at account_created FROM sellers s JOIN accounts a ON a.id=s.account_id $where ORDER BY s.is_approved ASC,s.total_sales DESC,s.account_id DESC LIMIT $limit OFFSET $offset ");
$total = countQuery(" SELECT 1 FROM sellers s JOIN accounts a ON a.id=s.account_id $where ");
$pages = max(1, ceil($total / $limit));
function pageUrl($page, $search)
{
    return '?' . http_build_query(['page' => $page, 'search' => $search]);
}
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white">Seller Management</h1>
            <p class="text-sm text-gray-500">Approve and monitor vendor stores. <?= number_format($total) ?> sellers.</p>
        </div>
    </div>

    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-white/5">
            <form class="grid md:grid-cols-[1fr_50px] gap-3">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search store, slug, email..." class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-gray-900 dark:text-white outline-none">
                <button class="bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 rounded-xl text-gray-600 dark:text-gray-300"><i class="fa-solid fa-search"></i></button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/5 text-xs uppercase text-gray-500 font-bold">
                        <th class="px-6 py-4">Store</th>
                        <th class="px-6 py-4">Owner</th>
                        <th class="px-6 py-4">Sales</th>
                        <th class="px-6 py-4">Commission</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php foreach ($sellers as $s):
                        $stats = sellerStats($s['account_id']);
                        $sales = $stats['total_sale'] ?? $s['total_sales'] ?? 0;
                    ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="px-6 py-4 min-w-[230px]">
                                <div class="font-bold text-gray-900 dark:text-white"><?= $s['store_name'] ?></div>
                                <div class="text-xs text-primary-500">@<?= $s['store_slug'] ?></div>
                            </td>
                            <td class="px-6 py-4 min-w-[230px]">
                                <div class="text-sm text-gray-900 dark:text-white"><?= $s['accountname'] ?></div>
                                <div class="text-xs text-gray-500"><?= $s['email'] ?></div>
                                <div class="text-xs text-gray-400 font-mono"><?= substr($s['wallet_address'], 0, 8) ?>...</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-primary-500"><?= number_format($sales, 2) ?> G</div>
                                <div class="text-xs text-gray-500">Tier: <?= strtoupper($s['tier']) ?></div>
                            </td>
                            <td class="px-6 py-4 font-bold"><?= $s['commission_rate'] ?>%</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="px-2 py-1 rounded text-[10px] font-bold uppercase w-fit <?= $s['is_approved'] ? 'bg-green-500/10 text-green-500' : 'bg-yellow-500/10 text-yellow-500' ?>"><?= $s['is_approved'] ? 'Live' : 'Pending' ?></span>
                                    <?php if ($s['is_verified']): ?><span class="px-2 py-1 rounded text-[10px] font-bold uppercase w-fit bg-blue-500/10 text-blue-500">Verified</span><?php endif; ?>
                                    <?php if ($s['is_banned']): ?><span class="px-2 py-1 rounded text-[10px] font-bold uppercase w-fit bg-red-500/10 text-red-500">Banned</span><?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="../seller.php?slug=<?= $s['store_slug'] ?>" target="_blank" class="p-2 text-gray-400 hover:text-blue-500" title="View"><i class="fa-solid fa-eye"></i></a>
                                <a href="sellerdetail.php?id=<?= $s['account_id'] ?>" class="p-2 text-gray-400 hover:text-purple-500" title="Details"><i class="fa-solid fa-boxes-stacked"></i></a>
                                <button type="button" onclick="navigator.clipboard.writeText('<?= $s['wallet_address'] ?>')" class="p-2 text-gray-400 hover:text-emerald-500" title="Copy Wallet"><i class="fa-solid fa-copy"></i></button>
                                <?php if (!$s['is_approved']): ?>
                                    <a href="?approve=<?= $s['account_id'] ?>" class="px-3 py-1 bg-green-600 hover:bg-green-500 text-white rounded-lg text-xs font-bold ml-1">Approve</a>
                                <?php else: ?>
                                    <a href="?reject=<?= $s['account_id'] ?>" class="px-3 py-1 bg-yellow-600 hover:bg-yellow-500 text-white rounded-lg text-xs font-bold ml-1">Suspend</a>
                                <?php endif; ?>
                                <button onclick="confirmDelete(<?= $s['account_id'] ?>)" class="p-2 text-gray-400 hover:text-red-500 ml-1" title="Delete"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$sellers): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400">No sellers found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($pages > 1): ?>
            <div class="p-4 border-t border-gray-200 dark:border-white/5 flex flex-wrap justify-center gap-2">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <a href="<?= pageUrl($i, $search) ?>" class="px-3 py-1 rounded-lg text-sm font-bold <?= $i == $page ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-white/5 text-gray-500' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<div id="deleteModal" class="fixed inset-0 z-[70] hidden">
    <div class="absolute inset-0 bg-black/60" onclick="closeDelete()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-sm p-4">
        <div class="bg-white dark:bg-dark-800 rounded-2xl p-6">
            <div class="text-lg font-bold text-gray-900 dark:text-white mb-2">Delete Seller?</div>
            <div class="text-sm text-gray-500 mb-6">Store row will be removed.</div>
            <div class="grid grid-cols-2 gap-3">
                <button onclick="closeDelete()" class="py-2 rounded-xl bg-gray-100 dark:bg-white/5">Cancel</button>
                <a id="deleteLink" href="#" class="py-2 rounded-xl bg-red-500 text-white text-center">Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        document.getElementById('deleteLink').href = '?delete=' + id
        document.getElementById('deleteModal').classList.remove('hidden')
    }

    function closeDelete() {
        document.getElementById('deleteModal').classList.add('hidden')
    }
</script>
<?php require_once 'footer.php'; ?>