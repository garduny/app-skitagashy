<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="min-h-screen pt-20 lg:pl-64 bg-gray-50 dark:bg-[#0B0E14] text-gray-800 dark:text-gray-200 transition-colors duration-300">
    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">My Orders</h1>
                <p class="text-sm text-gray-500 mt-1">Track your purchases and reveal digital codes.</p>
            </div>
            <div class="flex gap-2">
                <button onclick="App.fetchOrders()" class="p-2 bg-white dark:bg-[#151A23] border border-gray-200 dark:border-white/10 rounded-lg hover:text-blue-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            </div>
        </div>
        <div id="orders-container" class="space-y-4">
            <div class="text-center py-12">
                <svg class="w-12 h-12 text-gray-400 mx-auto animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-500 mt-4 text-sm">Loading blockchain data...</p>
            </div>
        </div>
        <div id="empty-state" class="hidden flex flex-col items-center justify-center py-20 text-center bg-white dark:bg-[#151A23] rounded-2xl border border-gray-200 dark:border-white/5 border-dashed">
            <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-white/5 flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">No orders yet</h3>
            <p class="text-gray-500 mb-6 max-w-xs mx-auto">Start trading on the marketplace to see your history here.</p>
            <a href="market.php" class="px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg transition-colors">Browse Market</a>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>