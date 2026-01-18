<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
$round = findQuery(" SELECT * FROM lottery_rounds WHERE status='open' ORDER BY id DESC LIMIT 1 ");
$pool = $round['prize_pool'] ?? 0;
$rid = $round['id'] ?? 0;
?>
<main class="min-h-screen pt-20 lg:pl-64 bg-gray-50 dark:bg-[#0B0E14] text-gray-800 dark:text-gray-200 transition-colors duration-300 relative overflow-hidden">
    <div class="relative z-10 p-4 sm:p-6 lg:p-8 max-w-5xl mx-auto">
        <div class="relative bg-white dark:bg-[#151A23] rounded-3xl border border-gray-200 dark:border-white/5 overflow-hidden p-8 md:p-12 text-center mb-12 shadow-xl dark:shadow-none">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-green-400 via-emerald-500 to-green-600"></div>
            <div class="absolute inset-0 bg-[url('assets/pattern.svg')] opacity-5"></div>
            <span class="inline-block py-1 px-3 rounded-full bg-green-500/10 text-green-600 dark:text-green-400 text-xs font-bold uppercase tracking-widest border border-green-500/20 mb-6">Round #<?= $round['round_number'] ?? 1 ?> Live</span>
            <h1 class="text-5xl md:text-7xl font-black text-gray-900 dark:text-white mb-4 tracking-tighter">
                <?= number_format($pool) ?> <span class="text-green-500">GASHY</span>
            </h1>
            <p class="text-gray-500 dark:text-gray-400 text-lg mb-8">Current Prize Pool • Draws Weekly</p>
            <div class="max-w-md mx-auto bg-gray-50 dark:bg-black/30 rounded-2xl p-6 border border-gray-200 dark:border-white/5 backdrop-blur-sm">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-gray-500 dark:text-gray-400 text-sm">Ticket Price</span>
                    <span class="text-gray-900 dark:text-white font-bold">10 GASHY</span>
                </div>
                <div class="flex gap-2">
                    <input type="number" id="ticket-qty" value="1" min="1" class="w-20 bg-white dark:bg-[#0B0E14] border border-gray-200 dark:border-white/10 rounded-lg text-center text-gray-900 dark:text-white font-bold focus:border-green-500 outline-none">
                    <button onclick="buyTickets(<?= $rid ?>)" class="flex-1 bg-green-600 hover:bg-green-500 text-white font-bold rounded-lg transition-all shadow-lg shadow-green-600/20">Buy Tickets</button>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white dark:bg-[#151A23] rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-lg dark:shadow-none">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">How it works</h3>
                <ul class="space-y-4">
                    <li class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center font-bold text-gray-500 dark:text-gray-400">1</div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 pt-1">Buy tickets using $GASHY. 100% of tokens go into the pool.</p>
                    </li>
                    <li class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center font-bold text-gray-500 dark:text-gray-400">2</div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 pt-1">Wait for the draw. Winners are selected via Chainlink VRF.</p>
                    </li>
                    <li class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center font-bold text-gray-500 dark:text-gray-400">3</div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 pt-1">Prizes are automatically airdropped to winners' wallets.</p>
                    </li>
                </ul>
            </div>
            <div class="bg-white dark:bg-[#151A23] rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-lg dark:shadow-none">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Last Winners</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-white/5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-500 to-purple-500"></div>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">8x...4a2</span>
                        </div>
                        <span class="text-green-500 dark:text-green-400 font-mono font-bold">+5,000 GASHY</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-white/5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-yellow-500 to-red-500"></div>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">3k...9pL</span>
                        </div>
                        <span class="text-green-500 dark:text-green-400 font-mono font-bold">+2,500 GASHY</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script src="public/js/pages/lottery.js"></script>
<?php require_once 'footer.php'; ?>