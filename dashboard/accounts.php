<?php
require_once 'init.php';
if (get('ban')) {
    $id = (int)request('ban', 'get');
    execute(" UPDATE accounts SET is_banned=1 WHERE id=$id ");
    redirect('accounts.php?msg=banned');
}
if (get('unban')) {
    $id = (int)request('unban', 'get');
    execute(" UPDATE accounts SET is_banned=0 WHERE id=$id ");
    redirect('accounts.php?msg=active');
}
$search = trim((string)request('search', 'get'));
$page = max(1, (int)(request('page', 'get') ?: 1));
$limit = 10;
$offset = ($page - 1) * $limit;
$where = " WHERE 1=1 ";
if ($search) $where .= " AND (a.wallet_address LIKE '%$search%' OR a.accountname LIKE '%$search%' OR a.email LIKE '%$search%' OR a.username LIKE '%$search%') ";
$accounts = getQuery(" SELECT a.*,(SELECT COUNT(*) FROM orders WHERE account_id=a.id) orders_count,(SELECT COUNT(*) FROM transactions WHERE account_id=a.id) transactions_count FROM accounts a $where ORDER BY a.id DESC LIMIT $limit OFFSET $offset ");
$total = countQuery(" SELECT 1 FROM accounts a $where ");
$pages = max(1, ceil($total / $limit));
function pageUrl($page, $search)
{
    return '?' . http_build_query(['page' => $page, 'search' => $search]);
}
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Web3 Accounts</h1>
            <p class="text-sm text-gray-500">Manage registered users. <?= number_format($total) ?> accounts.</p>
        </div>
    </div>
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200 dark:border-white/5">
            <form class="grid md:grid-cols-[1fr_50px] gap-3">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search wallet, username or email..." class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-gray-900 dark:text-white focus:border-primary-500 outline-none">
                <button type="submit" class="bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 rounded-xl text-gray-600 dark:text-gray-300"><i class="fa-solid fa-search"></i></button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/5 text-xs uppercase text-gray-500 font-bold">
                        <th class="px-6 py-4">Account</th>
                        <th class="px-6 py-4">Tier</th>
                        <th class="px-6 py-4">Orders</th>
                        <th class="px-6 py-4">Transactions</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Joined</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php foreach ($accounts as $a): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3 min-w-[240px]">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-500 to-purple-500 p-0.5">
                                        <div class="w-full h-full bg-white dark:bg-dark-800 rounded-full flex items-center justify-center text-xs font-bold text-gray-700 dark:text-white"><?= strtoupper(substr($a['accountname'] ?: 'A', 0, 1)) ?></div>
                                    </div>
                                    <div>
                                        <div class="font-mono text-xs text-primary-600 dark:text-primary-400 font-bold"><?= substr($a['wallet_address'] ?: '-', 0, 6) ?>...<?= substr($a['wallet_address'] ?: '-', -4) ?></div>
                                        <div class="text-xs text-gray-500"><?= $a['accountname'] ?: 'Anonymous' ?></div>
                                        <?php if (!empty($a['email'])): ?><div class="text-xs text-gray-400"><?= $a['email'] ?></div><?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4"><span class="capitalize text-xs font-bold px-2 py-1 bg-gray-100 dark:bg-white/10 rounded-lg text-gray-600 dark:text-gray-300"><?= $a['tier'] ?: 'basic' ?></span></td>
                            <td class="px-6 py-4 text-sm font-bold"><?= number_format((int)$a['orders_count']) ?></td>
                            <td class="px-6 py-4 text-sm font-bold"><?= number_format((int)$a['transactions_count']) ?></td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-xs font-bold <?= $a['is_banned'] ? 'text-red-500' : 'text-green-500' ?>"><?= $a['is_banned'] ? 'Banned' : 'Active' ?></span>
                                    <?php if (!empty($a['is_verified'])): ?><span class="text-[10px] font-bold text-blue-500">Verified</span><?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500"><?= !empty($a['created_at']) ? date('M d, Y', strtotime($a['created_at'])) : '-' ?></td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="accountdetail.php?id=<?= $a['id'] ?>" class="p-2 text-gray-400 hover:text-primary-500 transition-colors"><i class="fa-solid fa-eye"></i></a>
                                <button type="button" onclick="navigator.clipboard.writeText('<?= $a['wallet_address'] ?>')" class="p-2 text-gray-400 hover:text-emerald-500 transition-colors"><i class="fa-solid fa-copy"></i></button>
                                <?php if ($a['is_banned']): ?>
                                    <a href="?unban=<?= $a['id'] ?>" class="p-2 text-green-500"><i class="fa-solid fa-check"></i></a>
                                <?php else: ?>
                                    <button type="button" onclick="confirmBan(<?= $a['id'] ?>)" class="p-2 text-red-500"><i class="fa-solid fa-ban"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$accounts): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400">No accounts found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($pages > 1): ?>
            <div class="p-4 border-t border-gray-200 dark:border-white/5 flex justify-center gap-2 flex-wrap">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <a href="<?= pageUrl($i, $search) ?>" class="px-3 py-1 rounded-lg text-sm font-bold <?= $i == $page ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-white/5 text-gray-500 hover:bg-gray-200 dark:hover:bg-white/10' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<div id="banModal" class="fixed inset-0 z-[70] hidden">
    <div class="absolute inset-0 bg-black/60" onclick="closeBanModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-sm p-4">
        <div class="bg-white dark:bg-dark-800 rounded-2xl p-6 shadow-2xl">
            <div class="text-lg font-bold text-gray-900 dark:text-white mb-2">Ban Account?</div>
            <div class="text-sm text-gray-500 mb-6">This account will be blocked from using the platform.</div>
            <div class="grid grid-cols-2 gap-3">
                <button type="button" onclick="closeBanModal()" class="py-2 rounded-xl bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-white font-bold">Cancel</button>
                <a id="banLink" href="#" class="py-2 rounded-xl bg-red-500 hover:bg-red-600 text-white text-center font-bold">Ban</a>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmBan(id) {
        document.getElementById('banLink').href = '?ban=' + id
        document.getElementById('banModal').classList.remove('hidden')
    }

    function closeBanModal() {
        document.getElementById('banModal').classList.add('hidden')
    }
</script>
<?php require_once 'footer.php'; ?>