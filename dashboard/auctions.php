<?php
require_once 'init.php';
if (get('delete')) {
    $id = (int)get('delete');
    execute(" DELETE FROM auctions WHERE id=$id ");
    redirect('auctions.php?msg=deleted');
}
if (post('add_auction')) {
    $pid = (int)$_POST['product_id'];
    $start = request('start_time', 'post');
    $end = request('end_time', 'post');
    $price = (float)$_POST['start_price'];
    execute(" INSERT INTO auctions (product_id,start_time,end_time,start_price,current_bid,status) VALUES ($pid,'$start','$end',$price,$price,'active') ");
    redirect('auctions.php?msg=created');
}
$auctions = getQuery(" SELECT a.*,p.title,p.images,acc.accountname as bidder_name FROM auctions a JOIN products p ON a.product_id=p.id LEFT JOIN accounts acc ON a.highest_bidder_id=acc.id ORDER BY a.status ASC, a.end_time ASC ");
$products = getQuery(" SELECT id,title FROM products WHERE status='active' AND stock>0 ");
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Live Auctions</h1>
            <p class="text-sm text-gray-500">Manage real-time bidding events.</p>
        </div>
        <button onclick="openModal('addModal')" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl shadow-lg shadow-primary-500/20 transition-all flex items-center gap-2"><i class="fa-solid fa-gavel"></i> Create Auction</button>
    </div>
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/5 text-xs uppercase text-gray-500 font-bold">
                        <th class="px-6 py-4">Item</th>
                        <th class="px-6 py-4">Current Bid</th>
                        <th class="px-6 py-4">Top Bidder</th>
                        <th class="px-6 py-4">Ends In</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php foreach ($auctions as $a): $img = json_decode($a['images'])[0] ?? '';
                        $time = strtotime($a['end_time']);
                        $diff = $time - time(); ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3"><img src="<?= $img ?>" class="w-10 h-10 rounded-lg object-cover bg-gray-100 dark:bg-white/5">
                                    <div class="font-bold text-gray-900 dark:text-white truncate max-w-[200px]"><?= $a['title'] ?></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-primary-500"><?= number_format($a['current_bid'], 2) ?></td>
                            <td class="px-6 py-4 text-sm"><?= $a['bidder_name'] ?? '<span class="text-gray-400">No Bids</span>' ?></td>
                            <td class="px-6 py-4 text-sm <?= $diff < 3600 && $diff > 0 ? 'text-red-500 font-bold' : '' ?>"><?= $diff > 0 ? floor($diff / 3600) . 'h ' . floor(($diff % 3600) / 60) . 'm' : 'Ended' ?></td>
                            <td class="px-6 py-4"><span class="px-2 py-1 rounded text-[10px] uppercase font-bold <?= $a['status'] == 'active' ? 'bg-green-500/10 text-green-500' : 'bg-gray-100 dark:bg-white/10 text-gray-500' ?>"><?= $a['status'] ?></span></td>
                            <td class="px-6 py-4 text-right"><a href="auctiondetail.php?id=<?= $a['id'] ?>" class="p-2 text-gray-400 hover:text-primary-500"><i class="fa-solid fa-pen-to-square"></i></a> <a href="?delete=<?= $a['id'] ?>" onclick="return confirm('Delete auction?')" class="p-2 text-gray-400 hover:text-red-500"><i class="fa-solid fa-trash"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<div id="addModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('addModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white dark:bg-dark-800 rounded-2xl shadow-2xl p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">New Auction</h3>
        <form method="POST">
            <div class="space-y-4">
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Product</label><select name="product_id" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"><?php foreach ($products as $p): ?><option value="<?= $p['id'] ?>"><?= $p['title'] ?></option><?php endforeach; ?></select></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Start Time</label><input type="datetime-local" name="start_time" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                    <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">End Time</label><input type="datetime-local" name="end_time" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                </div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Starting Price</label><input type="number" step="0.01" name="start_price" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div><button type="submit" name="add_auction" value="1" class="w-full py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl">Launch Auction</button>
            </div>
        </form>
    </div>
</div>
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
</script>
<?php require_once 'footer.php'; ?>