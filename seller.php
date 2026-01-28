<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';

// Check if account is already a seller
$is_seller = false;
$seller_data = [];
$uid = 0;

// Simulate finding account ID from token (In JS we handle the real check, here we prepare UI)
// We rely on JS to show/hide sections based on Auth, but if we had session:
// $uid = $_SESSION['uid']; 
// $seller = findQuery("SELECT * FROM sellers WHERE account_id = $uid");
?>
<main class="min-h-screen pt-20 lg:pl-64 bg-gray-50 dark:bg-[#0B0E14] text-gray-800 dark:text-gray-200 relative transition-colors duration-300">
    <div class="max-w-5xl mx-auto p-4 sm:p-6 lg:p-8">

        <!-- HEADER -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-black text-gray-900 dark:text-white mb-4">Seller <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-purple-500">Center</span></h1>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">Join the decentralized economy. List your digital assets, gift cards, and codes on Gashy Bazaar and get paid in crypto instantly.</p>
        </div>

        <!-- LOADING STATE -->
        <div id="seller-loading" class="text-center py-12">
            <svg class="w-10 h-10 text-blue-500 mx-auto animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <!-- VIEW 1: APPLICATION FORM (Shown if not a seller) -->
        <div id="seller-form-view" class="hidden bg-white dark:bg-[#151A23] rounded-3xl p-8 border border-gray-200 dark:border-white/5 shadow-xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Open Your Store</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Store Name</label>
                            <input type="text" id="store-name" placeholder="e.g. CyberTech Digital" class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:border-blue-500 outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Store Handle (Slug)</label>
                            <input type="text" id="store-slug" placeholder="e.g. cybertech" class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:border-blue-500 outline-none transition-colors">
                            <p class="text-xs text-gray-500 mt-2">Your URL: gashybazaar.com/shop/<span id="slug-preview" class="text-blue-500">...</span></p>
                        </div>
                        <button onclick="applySeller()" class="w-full py-4 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition-all mt-4">
                            Submit Application
                        </button>
                    </div>
                </div>
                <div class="hidden md:block relative">
                    <div class="absolute inset-0 bg-blue-500/20 blur-3xl rounded-full"></div>
                    <img src="assets/seller-illustration.svg" onerror="this.src='https://cdn-icons-png.flaticon.com/512/2921/2921226.png'" class="relative z-10 w-3/4 mx-auto drop-shadow-2xl">
                    <div class="mt-8 grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-xl">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">5%</div>
                            <div class="text-xs text-gray-500">Flat Fee</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-xl">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">Instant</div>
                            <div class="text-xs text-gray-500">Payouts</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- VIEW 2: PENDING APPROVAL -->
        <div id="seller-pending-view" class="hidden text-center py-12">
            <div class="w-20 h-20 bg-yellow-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Application Pending</h2>
            <p class="text-gray-500">Our team is reviewing your store details. Check back soon.</p>
        </div>

        <!-- VIEW 3: SELLER DASHBOARD (Already Approved) -->
        <div id="seller-dashboard-view" class="hidden">
            <div class="bg-green-500/10 border border-green-500/20 rounded-2xl p-8 text-center">
                <div class="w-20 h-20 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">You are a Seller!</h2>
                <p class="text-gray-400 mb-6">Access your inventory and sales in the Admin Dashboard.</p>
                <a href="/dashboard" class="px-8 py-3 bg-green-600 hover:bg-green-500 text-white font-bold rounded-xl shadow-lg transition-all inline-flex items-center gap-2">
                    Go to Dashboard <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>

    </div>
</main>
<script src="public/js/pages/seller.js"></script>
<?php require_once 'footer.php'; ?>