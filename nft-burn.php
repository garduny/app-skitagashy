<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300 bg-gray-50 dark:bg-[#060709] text-gray-900 dark:text-white">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-black mb-4">NFT <span class="text-red-500">Incinerator</span></h1>
            <p class="text-gray-500">Burn your minted NFTs to reclaim 50% of the GASHY value.</p>
        </div>
        <div id="burn-loader" class="text-center py-20"><svg class="w-12 h-12 text-red-500 mx-auto animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg></div>
        <div id="burn-grid" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"></div>
        <div id="burn-empty" class="hidden text-center py-20 bg-white dark:bg-[#151A23] rounded-3xl border border-gray-200 dark:border-white/5">
            <p class="text-gray-500">You do not own any active NFTs.</p>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>