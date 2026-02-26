<?php
require_once 'init.php';
$id = (int)request('id', 'get');
$a = findQuery(" SELECT a.*,p.title,p.images,acc.accountname,acc.wallet_address FROM auctions a JOIN products p ON a.product_id=p.id LEFT JOIN accounts acc ON a.highest_bidder_id=acc.id WHERE a.id=$id ");
if (!$a) redirect('auctions.php');
if (post('update_auction')) {
    $start = request('start_time', 'post');
    $end = request('end_time', 'post');
    $st = request('status', 'post');
    $res = (float)request('reserve_price', 'post');
    execute(" UPDATE auctions SET start_time='$start', end_time='$end', status='$st', reserve_price=$res WHERE id=$id ");
    redirect("auctiondetail.php?id=$id&msg=updated");
}
$bids = getQuery(" SELECT t.*,acc.accountname FROM transactions t JOIN accounts acc ON t.account_id=acc.id WHERE t.type='auction_bid' AND t.reference_id=$id ORDER BY t.amount DESC ");
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex items-center gap-4 mb-6"><a href="auctions.php" class="p-2 rounded-lg bg-white dark:bg-white/5 text-gray-500 hover:text-white transition-colors"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Auction #<?= $id ?></h1>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <div class="aspect-video rounded-xl bg-gray-100 dark:bg-white/5 mb-4 overflow-hidden"><img src="../<?= json_decode($a['images'])[0] ?? '' ?>" class="w-full h-full object-cover"></div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-1"><?= $a['title'] ?></h3>
                <div class="flex justify-between items-center mt-4 p-3 bg-gray-50 dark:bg-white/5 rounded-xl border border-gray-100 dark:border-white/5">
                    <div>
                        <div class="text-xs text-gray-500 uppercase">Current Bid</div>
                        <div class="text-lg font-bold text-primary-500"><?= number_format($a['current_bid'], 2) ?></div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-500 uppercase">Status</div><span class="text-sm font-bold uppercase"><?= $a['status'] ?></span>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Settings</h3>
                <form method="POST">
                    <div class="space-y-4">
                        <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Start Time</label><input type="datetime-local" name="start_time" value="<?= date('Y-m-d\TH:i', strtotime($a['start_time'])) ?>" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                        <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">End Time</label><input type="datetime-local" name="end_time" value="<?= date('Y-m-d\TH:i', strtotime($a['end_time'])) ?>" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                        <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Reserve Price</label><input type="number" step="0.01" name="reserve_price" value="<?= $a['reserve_price'] ?>" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                        <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label><select name="status" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none">
                                <option value="active" <?= $a['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="ended" <?= $a['status'] == 'ended' ? 'selected' : '' ?>>Ended</option>
                                <option value="cancelled" <?= $a['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select></div>
                        <button type="submit" name="update_auction" value="1" class="w-full py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-bold rounded-xl">Update</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="lg:col-span-2 bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
            <h3 class="font-bold text-gray-900 dark:text-white mb-4">Bid History</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-gray-500 border-b border-gray-200 dark:border-white/5">
                            <th class="pb-2">Bidder</th>
                            <th class="pb-2">Amount</th>
                            <th class="pb-2">Signature</th>
                            <th class="pb-2 text-right">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        <?php foreach ($bids as $b): ?><tr>
                                <td class="py-3 font-bold text-gray-900 dark:text-white"><?= $b['accountname'] ?></td>
                                <td class="py-3 font-mono text-primary-500"><?= number_format($b['amount'], 2) ?></td>
                                <td class="py-3 text-xs text-gray-500 font-mono truncate max-w-[150px]"><?= $b['tx_signature'] ?></td>
                                <td class="py-3 text-right text-gray-500"><?= date('M d, H:i', strtotime($b['created_at'])) ?></td>
                            </tr><?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>