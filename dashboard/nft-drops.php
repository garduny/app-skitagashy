<?php
require_once 'init.php';
if (get('action')) {
    $id = (int)request('id', 'get');
    $act = request('action', 'get');
    if ($act === 'approve') {
        execute(" UPDATE nft_drops SET status='approved' WHERE id=$id ");
        redirect('nft-drops.php?msg=approved');
    }
    if ($act === 'reject') {
        execute(" UPDATE nft_drops SET status='rejected' WHERE id=$id ");
        redirect('nft-drops.php?msg=rejected');
    }
    if ($act === 'delete') {
        execute(" DELETE FROM nft_drops WHERE id=$id ");
        redirect('nft-drops.php?msg=deleted');
    }
}
$drops = getQuery(" SELECT d.*,a.accountname FROM nft_drops d JOIN accounts a ON d.seller_account_id=a.id ORDER BY d.id DESC ");
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white">NFT Drops</h1>
            <p class="text-sm text-gray-500">Manage seller collections</p>
        </div>
    </div>
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/5 text-xs uppercase text-gray-500 font-bold">
                        <th class="px-6 py-4">Collection</th>
                        <th class="px-6 py-4">Seller</th>
                        <th class="px-6 py-4">Price / Supply</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php foreach ($drops as $d): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3"><img src="<?= $d['image_uri'] ?>" class="w-10 h-10 rounded bg-gray-100 dark:bg-white/5 object-cover">
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white"><?= $d['collection_name'] ?></div>
                                        <div class="text-xs text-gray-500"><?= $d['symbol'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold"><?= $d['accountname'] ?></td>
                            <td class="px-6 py-4">
                                <div class="font-mono text-green-500 font-bold"><?= number_format($d['price_gashy'], 2) ?> G</div>
                                <div class="text-xs text-gray-500"><?= $d['minted_count'] ?> / <?= $d['max_supply'] ?></div>
                            </td>
                            <td class="px-6 py-4"><span class="px-2 py-1 rounded text-[10px] uppercase font-bold <?= $d['status'] == 'approved' ? 'bg-green-500/10 text-green-500' : ($d['status'] == 'pending' ? 'bg-yellow-500/10 text-yellow-500' : 'bg-red-500/10 text-red-500') ?>"><?= $d['status'] ?></span></td>
                            <td class="px-6 py-4 text-right">
                                <?php if ($d['status'] == 'pending'): ?><a href="?action=approve&id=<?= $d['id'] ?>" class="p-2 text-green-500 hover:bg-green-500/10 rounded"><i class="fa-solid fa-check"></i></a><a href="?action=reject&id=<?= $d['id'] ?>" class="p-2 text-red-500 hover:bg-red-500/10 rounded"><i class="fa-solid fa-times"></i></a><?php endif; ?>
                                <a href="?action=delete&id=<?= $d['id'] ?>" onclick="return confirm('Delete drop?')" class="p-2 text-gray-400 hover:text-red-500"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>