<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
$auctions = getQuery(" SELECT a.id,a.end_time,a.current_bid,a.start_price,a.status,p.title,p.images,p.slug,p.description,u.accountname as high_bidder FROM auctions a JOIN products p ON a.product_id=p.id LEFT JOIN accounts u ON a.highest_bidder_id=u.id WHERE a.status='active' AND a.end_time>NOW() ORDER BY a.end_time ASC ");
?>
<main class="min-h-screen pt-20 lg:pl-64 bg-gray-50 dark:bg-[#0B0E14] text-gray-800 dark:text-gray-200 transition-colors duration-300 relative overflow-hidden">
    <div class="relative z-10 p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white mb-2 tracking-tight">Live Auctions <span class="text-red-500 animate-pulse text-lg align-top">●</span></h1>
                <p class="text-gray-500 dark:text-gray-400">Bid on exclusive items with $GASHY tokens.</p>
            </div>
            <div class="flex gap-2">
                <button class="px-4 py-2 bg-white dark:bg-[#151A23] border border-gray-200 dark:border-white/10 rounded-lg text-sm text-gray-700 dark:text-white hover:border-red-500/50 transition-colors shadow-sm">Ending Soon</button>
                <button class="px-4 py-2 bg-white dark:bg-[#151A23] border border-gray-200 dark:border-white/10 rounded-lg text-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors shadow-sm">My Bids</button>
            </div>
        </div>
        <?php if (empty($auctions)): ?>
            <div class="flex flex-col items-center justify-center py-24 bg-white dark:bg-[#151A23] rounded-2xl border border-gray-200 dark:border-white/5 border-dashed">
                <div class="w-16 h-16 bg-gray-100 dark:bg-white/5 rounded-full flex items-center justify-center mb-4 text-gray-400"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg></div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">No Live Auctions</h3>
                <p class="text-gray-500 mt-2">Check back later for new drops.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php foreach ($auctions as $a): $img = json_decode($a['images'])[0] ?? 'assets/placeholder.png';
                    $timeLeft = strtotime($a['end_time']) - time(); ?>
                    <div class="group bg-white dark:bg-[#151A23] rounded-2xl border border-gray-200 dark:border-white/5 overflow-hidden hover:border-red-500/30 transition-all duration-300 flex flex-col shadow-lg dark:shadow-none hover:shadow-xl hover:-translate-y-1">
                        <div class="relative h-64 overflow-hidden">
                            <img src="<?= $img ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/50 via-transparent to-transparent opacity-60"></div>
                            <div class="absolute top-3 right-3 bg-white/90 dark:bg-red-600 backdrop-blur text-red-600 dark:text-white text-xs font-bold px-2 py-1 rounded flex items-center gap-1 shadow-lg animate-pulse">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="countdown-timer" data-end="<?= strtotime($a['end_time']) ?>"><?= gmdate("H:i:s", $timeLeft) ?></span>
                            </div>
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1 truncate"><?= $a['title'] ?></h3>
                            <p class="text-xs text-gray-500 mb-4 line-clamp-2"><?= $a['description'] ?></p>
                            <div class="grid grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 dark:bg-black/20 rounded-xl border border-gray-100 dark:border-white/5">
                                <div>
                                    <p class="text-[10px] text-gray-500 uppercase font-bold">Current Bid</p>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white"><?= number_format($a['current_bid'], 2) ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] text-gray-500 uppercase font-bold">Top Bidder</p>
                                    <p class="text-sm font-medium text-blue-500 dark:text-blue-400 truncate max-w-[100px] ml-auto"><?= $a['high_bidder'] ?? 'No Bids' ?></p>
                                </div>
                            </div>
                            <div class="mt-auto flex gap-3">
                                <input type="number" id="bid-amount-<?= $a['id'] ?>" placeholder="<?= number_format($a['current_bid'] * 1.05, 2) ?>" class="flex-1 bg-gray-100 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-lg px-4 text-gray-900 dark:text-white text-sm focus:border-blue-500 outline-none transition-colors">
                                <button onclick="placeBid(<?= $a['id'] ?>)" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg shadow-lg shadow-blue-600/20 transition-all">Bid</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>
<script src="public/js/pages/auctions.js"></script>
<?php require_once 'footer.php'; ?>