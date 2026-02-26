<?php
require_once 'init.php';
$search = request('search', 'get');
$page = max(1, (int)(request('page', 'get') ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;
$where = "WHERE 1=1";
if ($search) {
    $where .= " AND (a.accountname LIKE '%$search%' OR l.nft_mint_address LIKE '%$search%') ";
}
$logs = getQuery(" SELECT l.*, a.accountname, a.wallet_address, c.name as campaign_name 
                 FROM nft_burn_logs l 
                 JOIN accounts a ON l.account_id=a.id 
                 JOIN nft_burn_campaigns c ON l.campaign_id=c.id 
                 $where ORDER BY l.id DESC LIMIT $limit OFFSET $offset ");
$total = countQuery(" SELECT 1 FROM nft_burn_logs l JOIN accounts a ON l.account_id=a.id $where ");
$pages = ceil($total / $limit);
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">NFT Burn History</h1>
            <p class="text-sm text-gray-500">Track destroyed assets.</p>
        </div>
        <a href="nft-campaigns.php" class="px-6 py-2.5 bg-gray-100 dark:bg-white/10 hover:bg-gray-200 dark:hover:bg-white/20 text-gray-700 dark:text-white font-bold rounded-xl transition-all flex items-center gap-2"><i class="fa-solid fa-arrow-left"></i> Campaigns</a>
    </div>
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200 dark:border-white/5">
            <form class="flex gap-2">
                <input type="text" name="search" value="<?= $search ?>" placeholder="Search user or mint address..." class="flex-1 bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-gray-900 dark:text-white focus:border-primary-500 outline-none">
                <button type="submit" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 rounded-xl text-gray-600 dark:text-gray-300"><i class="fa-solid fa-search"></i></button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/5 text-xs uppercase text-gray-500 font-bold">
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Campaign</th>
                        <th class="px-6 py-4">NFT Mint</th>
                        <th class="px-6 py-4">Reward Paid</th>
                        <th class="px-6 py-4 text-right">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php if (empty($logs)): ?><tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">No burns recorded yet.</td>
                        </tr><?php else: foreach ($logs as $l): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 dark:text-white"><?= $l['accountname'] ?></div>
                                    <div class="text-xs text-gray-500 font-mono"><?= substr($l['wallet_address'], 0, 6) ?>...</div>
                                </td>
                                <td class="px-6 py-4"><span class="px-2 py-1 rounded text-xs font-bold bg-blue-100 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400"><?= $l['campaign_name'] ?></span></td>
                                <td class="px-6 py-4 text-xs font-mono text-gray-500 truncate max-w-[150px]" title="<?= $l['nft_mint_address'] ?>"><?= $l['nft_mint_address'] ?></td>
                                <td class="px-6 py-4 font-mono font-bold text-green-500">+<?= number_format($l['reward_paid']) ?> G</td>
                                <td class="px-6 py-4 text-right text-sm text-gray-500"><?= date('M d, H:i', strtotime($l['created_at'])) ?></td>
                            </tr>
                    <?php endforeach;
                            endif; ?>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-white/5 flex justify-center gap-2"><?php for ($i = 1; $i <= $pages; $i++): ?><a href="?page=<?= $i ?>&search=<?= $search ?>" class="px-3 py-1 rounded-lg text-sm font-bold <?= $i == $page ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-white/5 text-gray-500 hover:bg-gray-200 dark:hover:bg-white/10' ?>"><?= $i ?></a><?php endfor; ?></div>
    </div>
</main>
<?php require_once 'footer.php'; ?>