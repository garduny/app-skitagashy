<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="min-h-screen pt-20 lg:pl-64 bg-gray-50 dark:bg-[#0B0E14] text-gray-800 dark:text-gray-200 relative transition-colors duration-300">
    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">

        <!-- STATE 1: GUEST / NOT CONNECTED -->
        <div id="guest-view" class="flex flex-col items-center justify-center min-h-[60vh] text-center space-y-6 animate-fade-in">
            <div class="relative">
                <div class="absolute inset-0 bg-blue-500/20 blur-xl rounded-full"></div>
                <div class="relative w-24 h-24 bg-white dark:bg-[#151A23] rounded-3xl border border-gray-200 dark:border-white/10 flex items-center justify-center shadow-2xl">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>
            <div>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white mb-2">Connect Your Wallet</h1>
                <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">Access your dashboard, track orders, and manage your assets on the Solana blockchain.</p>
            </div>
            <button onclick="App.connectWallet()" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-600/25 transition-all transform hover:-translate-y-1 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Connect Phantom
            </button>
        </div>

        <!-- STATE 2: AUTHORIZED DASHBOARD (Hidden by default) -->
        <div id="auth-view" class="hidden space-y-8 animate-fade-in">
            <!-- Header Card -->
            <div class="relative bg-gradient-to-r from-blue-100 to-purple-100 dark:from-blue-900/20 dark:to-purple-900/20 rounded-3xl p-8 border border-gray-200 dark:border-white/5 overflow-hidden shadow-lg dark:shadow-none">
                <div class="absolute inset-0 bg-[url('assets/grid.svg')] opacity-10"></div>
                <div class="relative z-10 flex flex-col md:flex-row items-center gap-8">
                    <div class="relative">
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 p-1">
                            <div class="w-full h-full rounded-full bg-white dark:bg-[#0B0E14] flex items-center justify-center text-3xl">👤</div>
                        </div>
                        <div class="absolute bottom-0 right-0 w-8 h-8 bg-white dark:bg-[#151A23] rounded-full flex items-center justify-center border border-gray-200 dark:border-white/10 shadow-sm" title="Tier">
                            <span id="user-tier-icon">🥉</span>
                        </div>
                    </div>
                    <div class="text-center md:text-left flex-1">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Welcome back, <span id="profile-username">User</span></h1>
                        <p id="profile-wallet" class="text-blue-500 dark:text-blue-400 font-mono text-sm bg-blue-50 dark:bg-blue-500/10 px-3 py-1 rounded-full inline-block border border-blue-100 dark:border-blue-500/20 mb-4">Loading...</p>

                        <div class="flex flex-wrap justify-center md:justify-start gap-4 text-sm">
                            <div class="px-4 py-2 bg-white dark:bg-[#151A23] rounded-lg border border-gray-200 dark:border-white/5 shadow-sm">
                                <span class="text-gray-500 block text-xs uppercase">Tier Status</span>
                                <span id="profile-tier" class="text-gray-900 dark:text-white font-bold capitalize">...</span>
                            </div>
                            <div class="px-4 py-2 bg-white dark:bg-[#151A23] rounded-lg border border-gray-200 dark:border-white/5 shadow-sm">
                                <span class="text-gray-500 block text-xs uppercase">Total Spent</span>
                                <span id="profile-spent" class="text-gray-900 dark:text-white font-bold">...</span>
                            </div>
                            <div class="px-4 py-2 bg-white dark:bg-[#151A23] rounded-lg border border-gray-200 dark:border-white/5 shadow-sm">
                                <span class="text-gray-500 block text-xs uppercase">Orders</span>
                                <span id="profile-orders-count" class="text-gray-900 dark:text-white font-bold">...</span>
                            </div>
                        </div>
                    </div>
                    <button onclick="App.logout()" class="px-4 py-2 bg-red-50 dark:bg-red-600/10 hover:bg-red-100 dark:hover:bg-red-600 text-red-500 hover:text-red-700 dark:hover:text-white border border-red-100 dark:border-red-600/20 rounded-lg text-sm font-bold transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Disconnect
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Recent Orders -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2"><svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg> Recent Orders</h2>
                        <a href="orders.php" class="text-xs font-bold text-blue-500 hover:underline">View All</a>
                    </div>
                    <div id="recent-orders-list" class="space-y-4">
                        <!-- JS Injects Orders Here -->
                        <div class="p-8 text-center bg-white dark:bg-[#151A23] rounded-2xl border border-gray-200 dark:border-white/5">
                            <svg class="w-8 h-8 text-gray-400 mx-auto mb-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="text-gray-500 text-xs">Loading orders...</p>
                        </div>
                    </div>
                </div>

                <!-- Settings -->
                <div class="space-y-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Account Settings</h2>
                    <div class="bg-white dark:bg-[#151A23] rounded-2xl p-6 border border-gray-200 dark:border-white/5 space-y-4 shadow-sm dark:shadow-none">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Username</label>
                            <input type="text" id="input-username" class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-lg px-4 py-2 text-gray-900 dark:text-white focus:border-blue-500 outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Email</label>
                            <input type="email" id="input-email" class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-lg px-4 py-2 text-gray-900 dark:text-white focus:border-blue-500 outline-none transition-colors">
                        </div>
                        <button onclick="saveProfile()" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-600/20">Save Changes</button>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-2xl p-6 border border-purple-100 dark:border-white/5">
                        <h3 class="font-bold text-gray-900 dark:text-white mb-2">Referral Program</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Invite friends and earn 5% of their trading fees.</p>
                        <div class="flex gap-2">
                            <input type="text" id="referral-code" readonly class="flex-1 bg-white/50 dark:bg-black/30 border border-purple-200 dark:border-white/10 rounded-lg px-3 py-2 text-sm text-purple-600 dark:text-purple-300 font-mono">
                            <button onclick="navigator.clipboard.writeText(document.getElementById('referral-code').value);notyf.success('Copied!')" class="px-3 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-lg text-sm font-bold shadow-md">Copy</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script src="public/js/pages/profile.js"></script>
<?php require_once 'footer.php'; ?>