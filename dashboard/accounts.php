<?php
require_once 'init.php';
if (get('ban')) {
    $id = request('ban', 'get');
    execute("UPDATE accounts SET is_banned=1 WHERE id=$id");
    redirect('accounts.php?msg=banned');
}
if (get('unban')) {
    $id = request('unban', 'get');
    execute("UPDATE accounts SET is_banned=0 WHERE id=$id");
    redirect('accounts.php?msg=active');
}
$search = request('search', 'get');
$page = max(1, (int)(request('page', 'get') ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;
$where = "WHERE 1=1";
if ($search) {
    $where .= " AND (wallet_address LIKE '%$search%' OR username LIKE '%$search%') ";
}
$accounts = getQuery(" SELECT a.*,(SELECT COUNT(*) FROM orders WHERE account_id=a.id) as orders_count FROM accounts a $where ORDER BY a.id DESC LIMIT $limit OFFSET $offset ");
$total = countQuery(" SELECT 1 FROM accounts $where ");
$pages = ceil($total / $limit);
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Web3 Accounts</h1>
            <p class="text-sm text-gray-500">Manage registered users.</p>
        </div>
    </div>
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200 dark:border-white/5 flex gap-4">
            <form class="flex-1 flex gap-2"><input type="text" name="search" value="<?= $search ?>" placeholder="Search wallet or username..." class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-gray-900 dark:text-white focus:border-primary-500 outline-none"><button type="submit" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 rounded-xl text-gray-600 dark:text-gray-300"><i class="fa-solid fa-search"></i></button></form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/5 text-xs uppercase text-gray-500 font-bold">
                        <th class="px-6 py-4">Account</th>
                        <th class="px-6 py-4">Tier</th>
                        <th class="px-6 py-4">Orders</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Joined</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php foreach ($accounts as $a): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-500 to-purple-500 p-0.5">
                                        <div class="w-full h-full bg-white dark:bg-dark-800 rounded-full"></div>
                                    </div>
                                    <div>
                                        <div class="font-mono text-xs text-primary-600 dark:text-primary-400 font-bold"><?= substr($a['wallet_address'], 0, 6) ?>...<?= substr($a['wallet_address'], -4) ?></div>
                                        <div class="text-xs text-gray-500"><?= $a['accountname'] ?? 'Anonymous' ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4"><span class="capitalize text-xs font-bold px-2 py-1 bg-gray-100 dark:bg-white/10 rounded-lg text-gray-600 dark:text-gray-300"><?= $a['tier'] ?></span></td>
                            <td class="px-6 py-4 text-sm font-bold"><?= $a['orders_count'] ?></td>
                            <td class="px-6 py-4"><?= $a['is_banned'] ? '<span class="text-red-500 text-xs font-bold">Banned</span>' : '<span class="text-green-500 text-xs font-bold">Active</span>' ?></td>
                            <td class="px-6 py-4 text-sm text-gray-500"><?= date('M d, Y', strtotime($a['created_at'])) ?></td>
                            <td class="px-6 py-4 text-right"><a href="accountdetail.php?id=<?= $a['id'] ?>" class="p-2 text-gray-400 hover:text-primary-500 transition-colors"><i class="fa-solid fa-eye"></i></a> <?= $a['is_banned'] ? '<a href="?unban=' . $a['id'] . '" class="p-2 text-green-500"><i class="fa-solid fa-check"></i></a>' : '<a href="?ban=' . $a['id'] . '" onclick="return confirm(\'Ban?\')" class="p-2 text-red-500"><i class="fa-solid fa-ban"></i></a>' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-white/5 flex justify-center gap-2"><?php for ($i = 1; $i <= $pages; $i++): ?><a href="?page=<?= $i ?>&search=<?= $search ?>" class="px-3 py-1 rounded-lg text-sm font-bold <?= $i == $page ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-white/5 text-gray-500 hover:bg-gray-200' ?>"><?= $i ?></a><?php endfor; ?></div>
    </div>
</main>
<?php require_once 'footer.php'; ?>