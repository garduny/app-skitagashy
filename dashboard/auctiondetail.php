<?php
require_once 'init.php';
$id = (int)request('id', 'get');
$a = findQuery(" SELECT a.*,p.title,p.images,p.slug,acc.accountname,acc.wallet_address FROM auctions a JOIN products p ON p.id=a.product_id LEFT JOIN accounts acc ON acc.id=a.highest_bidder_id WHERE a.id=$id ");
if (!$a) redirect('auctions.php');
if (post('update_auction')) {
    $start = request('start_time', 'post');
    $end = request('end_time', 'post');
    $state = request('status', 'post');
    $reserve = (float)request('reserve_price_usd', 'post');
    execute(" UPDATE auctions SET start_time='$start',end_time='$end',status='$state',reserve_price_usd=$reserve WHERE id=$id ");
    redirect("auctiondetail.php?id=$id&msg=updated");
}
$bids = getQuery(" SELECT t.*,acc.accountname,acc.wallet_address FROM transactions t LEFT JOIN accounts acc ON acc.id=t.account_id WHERE t.type='auction_bid' AND t.reference_id=$id ORDER BY t.amount DESC,t.id DESC ");
$gashyRate = toGashy();
$imgs = json_decode($a['images'], true);
$img = $imgs[0] ?? '';
$usd = (float)$a['current_bid_usd'];
$gashy = $gashyRate ? ($usd / $gashyRate) : 0;
$resusd = (float)$a['reserve_price_usd'];
$resgashy = $gashyRate ? ($resusd / $gashyRate) : 0;
$startTs = strtotime($a['start_time']);
$endTs = strtotime($a['end_time']);
$diff = $endTs - time();
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex items-center gap-4 mb-6">
        <a href="auctions.php" class="p-2 rounded-xl bg-white dark:bg-white/5 text-gray-500 hover:text-primary-500"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white">Auction #<?= $id ?></h1>
            <p class="text-sm text-gray-500">Manage bids, timer and status</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="space-y-6">
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <div class="aspect-video rounded-xl overflow-hidden bg-gray-100 dark:bg-white/5 mb-4">
                    <img src="../<?= $img ?>" class="w-full h-full object-cover">
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white text-lg"><?= $a['title'] ?></h3>
                <?php if (!empty($a['slug'])): ?><a href="../product/<?= $a['slug'] ?>" target="_blank" class="text-xs text-primary-500 break-all"><?= $a['slug'] ?></a><?php endif; ?>
                <div class="grid grid-cols-2 gap-3 mt-4">
                    <div class="p-3 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/5">
                        <div class="text-xs text-gray-500 uppercase">Current Bid</div>
                        <div class="text-lg font-black text-primary-500">$<?= number_format($usd, 2) ?></div>
                        <div class="text-xs text-primary-500"><?= number_format($gashy, 2) ?> GASHY</div>
                    </div>
                    <div class="p-3 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/5">
                        <div class="text-xs text-gray-500 uppercase">Reserve</div>
                        <div class="text-lg font-black text-gray-900 dark:text-white">$<?= number_format($resusd, 2) ?></div>
                        <div class="text-xs text-gray-500"><?= number_format($resgashy, 2) ?> GASHY</div>
                    </div>
                    <div class="p-3 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/5">
                        <div class="text-xs text-gray-500 uppercase">Highest Bidder</div>
                        <div class="font-bold text-sm text-gray-900 dark:text-white"><?= $a['accountname'] ?: 'No Bids' ?></div>
                    </div>
                    <div class="p-3 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/5">
                        <div class="text-xs text-gray-500 uppercase">Countdown</div>
                        <div class="font-mono font-bold <?= $diff > 0 && $diff < 3600 ? 'text-red-500' : 'text-gray-900 dark:text-white' ?>"><?= $diff > 0 ? floor($diff / 3600) . 'h ' . floor(($diff % 3600) / 60) . 'm' : 'Ended' ?></div>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <span class="px-2 py-1 rounded text-[10px] uppercase font-bold <?= $a['status'] == 'active' ? 'bg-green-500/10 text-green-500' : ($a['status'] == 'ended' ? 'bg-blue-500/10 text-blue-500' : 'bg-red-500/10 text-red-500') ?>"><?= $a['status'] ?></span>
                    <span class="text-xs text-gray-500"><?= date('Y-m-d H:i', $startTs) ?> → <?= date('Y-m-d H:i', $endTs) ?></span>
                </div>
            </div>

            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Settings</h3>
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Start Time</label>
                        <input type="datetime-local" name="start_time" value="<?= date('Y-m-d\TH:i', $startTs) ?>" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">End Time</label>
                        <input type="datetime-local" name="end_time" value="<?= date('Y-m-d\TH:i', $endTs) ?>" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Reserve Price USD</label>
                        <input type="number" step="0.01" min="0" name="reserve_price_usd" value="<?= $resusd ?>" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label>
                        <select name="status" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white">
                            <option value="active" <?= $a['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="ended" <?= $a['status'] == 'ended' ? 'selected' : '' ?>>Ended</option>
                            <option value="cancelled" <?= $a['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" name="update_auction" value="1" class="w-full py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl">Update Auction</button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-900 dark:text-white">Bid History</h3>
                    <div class="text-sm text-gray-500"><?= count($bids) ?> bids</div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-white/5 text-gray-500">
                                <th class="pb-3">Bidder</th>
                                <th class="pb-3">Amount</th>
                                <th class="pb-3">Wallet</th>
                                <th class="pb-3">Signature</th>
                                <th class="pb-3 text-right">Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                            <?php foreach ($bids as $b):
                                $busd = (float)$b['amount'];
                                $bgashy = $gashyRate ? ($busd / $gashyRate) : 0;
                            ?>
                                <tr>
                                    <td class="py-3">
                                        <div class="font-bold text-gray-900 dark:text-white"><?= $b['accountname'] ?: 'Unknown' ?></div>
                                    </td>
                                    <td class="py-3 font-mono">
                                        <div class="text-primary-500 font-bold">$<?= number_format($busd, 2) ?></div>
                                        <div class="text-xs text-primary-500"><?= number_format($bgashy, 2) ?> GASHY</div>
                                    </td>
                                    <td class="py-3 text-xs text-gray-500 max-w-[160px] truncate"><?= $b['wallet_address'] ?></td>
                                    <td class="py-3 text-xs text-gray-500 font-mono max-w-[220px] truncate"><?= $b['tx_signature'] ?></td>
                                    <td class="py-3 text-right text-gray-500"><?= date('M d, H:i', strtotime($b['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (!$bids): ?>
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-gray-400">No bids yet</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>