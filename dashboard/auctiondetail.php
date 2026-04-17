<?php
require_once 'init.php';
$id = (int)request('id', 'get');
if (!$id) redirect('auctions.php');
$a = findQuery(" SELECT a.*,p.title,p.images,p.slug,p.type,p.price_usd,s.accountname seller_name,u.accountname bidder_name,u.email bidder_email,u.wallet_address bidder_wallet FROM auctions a JOIN products p ON p.id=a.product_id LEFT JOIN accounts s ON s.id=p.seller_id LEFT JOIN accounts u ON u.id=a.highest_bidder_id WHERE a.id=$id ");
if (!$a) redirect('auctions.php');
$bids = getQuery(" SELECT t.*,acc.accountname,acc.email FROM transactions t LEFT JOIN accounts acc ON acc.id=t.account_id WHERE t.reference_id=$id AND t.type='auction_bid' ORDER BY t.id DESC ");
$rate = toGashy();
if (post('save_status')) {
    $st = request('status', 'post');
    $allowed = ['active', 'ended', 'cancelled'];
    if (!in_array($st, $allowed)) $st = 'active';
    execute(" UPDATE auctions SET status='$st' WHERE id=$id ");
    redirect("auctiondetail.php?id=$id&msg=updated");
}
$msg = request('msg', 'get');
$img = '';
$imgs = json_decode($a['images'], true);
if (is_array($imgs) && !empty($imgs[0])) $img = $imgs[0];
$currUsd = (float)$a['current_bid_usd'];
$currGashy = $rate ? $currUsd / $rate : 0;
$startUsd = (float)$a['start_price_usd'];
$startGashy = $rate ? $startUsd / $rate : 0;
$resUsd = (float)$a['reserve_price_usd'];
$resGashy = $rate ? $resUsd / $rate : 0;
$diff = strtotime($a['end_time']) - time();
function badgeAuction($s)
{
    if ($s === 'active') return 'bg-green-500/10 text-green-500';
    if ($s === 'ended') return 'bg-blue-500/10 text-blue-500';
    return 'bg-red-500/10 text-red-500';
}
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <a href="auctions.php" class="p-2 rounded-lg bg-white dark:bg-white/5 text-gray-500 hover:text-primary-500"><i class="fa-solid fa-arrow-left"></i></a>
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white">Auction #<?= $id ?></h1>
                <p class="text-sm text-gray-500">Detailed admin auction view</p>
            </div>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase <?= badgeAuction($a['status']) ?>"><?= htmlspecialchars($a['status']) ?></span>
    </div>

    <?php if ($msg === 'updated'): ?>
        <div class="p-4 mb-6 bg-green-100 dark:bg-green-500/20 border border-green-200 dark:border-green-500/30 text-green-600 dark:text-green-400 rounded-xl font-bold text-center">Auction updated successfully.</div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-5">
            <div class="text-xs font-bold uppercase text-gray-500 mb-2">Current Bid</div>
            <div class="text-2xl font-black text-primary-500">$<?= number_format($currUsd, 2) ?></div>
            <div class="text-sm text-yellow-500"><?= number_format($currGashy, 2) ?> GASHY</div>
        </div>
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-5">
            <div class="text-xs font-bold uppercase text-gray-500 mb-2">Reserve</div>
            <div class="text-2xl font-black text-gray-900 dark:text-white">$<?= number_format($resUsd, 2) ?></div>
            <div class="text-sm text-gray-500"><?= number_format($resGashy, 2) ?> GASHY</div>
        </div>
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-5">
            <div class="text-xs font-bold uppercase text-gray-500 mb-2">Bids</div>
            <div class="text-3xl font-black text-gray-900 dark:text-white"><?= count($bids) ?></div>
        </div>
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-5">
            <div class="text-xs font-bold uppercase text-gray-500 mb-2">Countdown</div>
            <div class="text-2xl font-black <?= $diff > 0 ? 'text-green-500' : 'text-red-500' ?>">
                <?= $diff > 0 ? floor($diff / 3600) . 'h ' . floor(($diff % 3600) / 60) . 'm' : 'Ended' ?>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <img src="../<?= $img ?>" class="w-20 h-20 rounded-2xl object-cover bg-gray-100 dark:bg-white/5">
                    <div class="min-w-0">
                        <div class="text-xl font-black text-gray-900 dark:text-white"><?= htmlspecialchars($a['title']) ?></div>
                        <div class="text-sm text-gray-500"><?= htmlspecialchars($a['type']) ?></div>
                        <div class="text-xs text-primary-500 mt-1"><?= htmlspecialchars($a['slug']) ?></div>
                    </div>
                </div>
                <div class="grid md:grid-cols-3 gap-4 mt-6">
                    <div class="rounded-xl bg-gray-50 dark:bg-white/5 p-4">
                        <div class="text-xs text-gray-500 uppercase font-bold mb-1">Start Price</div>
                        <div class="font-black">$<?= number_format($startUsd, 2) ?></div>
                    </div>
                    <div class="rounded-xl bg-gray-50 dark:bg-white/5 p-4">
                        <div class="text-xs text-gray-500 uppercase font-bold mb-1">Seller</div>
                        <div class="font-black"><?= htmlspecialchars($a['seller_name'] ?: '-') ?></div>
                    </div>
                    <div class="rounded-xl bg-gray-50 dark:bg-white/5 p-4">
                        <div class="text-xs text-gray-500 uppercase font-bold mb-1">Winner</div>
                        <div class="font-black"><?= htmlspecialchars($a['bidder_name'] ?: 'No Bids') ?></div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-900 dark:text-white">Bid History</h3>
                    <div class="text-xs text-gray-500"><?= count($bids) ?> record(s)</div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-white/5 text-gray-500">
                                <th class="pb-3">User</th>
                                <th class="pb-3 text-right">Amount</th>
                                <th class="pb-3 text-right">Status</th>
                                <th class="pb-3 text-right">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                            <?php if (!$bids): ?>
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-500">No bids yet.</td>
                                </tr>
                                <?php else: foreach ($bids as $b):
                                    $usd = (float)$b['amount'];
                                    $g = $rate ? $usd / $rate : 0;
                                ?>
                                    <tr>
                                        <td class="py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($b['accountname'] ?: 'Unknown') ?></td>
                                        <td class="py-3 text-right">
                                            $<?= number_format($usd, 2) ?>
                                            <div class="text-xs text-gray-500"><?= number_format($g, 2) ?> GASHY</div>
                                        </td>
                                        <td class="py-3 text-right"><span class="px-2 py-1 rounded text-[10px] uppercase font-bold bg-gray-100 dark:bg-white/5"><?= htmlspecialchars($b['status']) ?></span></td>
                                        <td class="py-3 text-right text-gray-500"><?= !empty($b['created_at']) ? date('M d H:i', strtotime($b['created_at'])) : '-' ?></td>
                                    </tr>
                            <?php endforeach;
                            endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div class="space-y-6">

            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Winner Info</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between gap-3">
                        <span class="text-gray-500">Name</span>
                        <span class="font-medium text-right"><?= htmlspecialchars($a['bidder_name'] ?: '-') ?></span>
                    </div>
                    <div class="flex justify-between gap-3">
                        <span class="text-gray-500">Email</span>
                        <span class="font-medium text-right break-all"><?= htmlspecialchars($a['bidder_email'] ?: '-') ?></span>
                    </div>
                    <div class="pt-3 border-t border-gray-100 dark:border-white/5">
                        <span class="text-xs text-gray-500 uppercase block mb-1">Wallet</span>
                        <span class="text-xs font-mono break-all bg-gray-100 dark:bg-white/5 px-2 py-1 rounded block"><?= htmlspecialchars($a['bidder_wallet'] ?: '-') ?></span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Workflow</h3>
                <form method="POST" class="space-y-4">
                    <select name="status" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white">
                        <option value="active" <?= $a['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="ended" <?= $a['status'] === 'ended' ? 'selected' : '' ?>>Ended</option>
                        <option value="cancelled" <?= $a['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                    <button type="submit" name="save_status" value="1" class="w-full py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl">Save Status</button>
                </form>
            </div>

        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>