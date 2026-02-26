<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
?>
<style>
    @keyframes fade-in {
        0% {
            opacity: 0;
            transform: translateY(20px)
        }

        100% {
            opacity: 1;
            transform: translateY(0)
        }
    }

    @keyframes pulse-avatar {

        0%,
        100% {
            box-shadow: 0 0 30px rgba(59, 130, 246, 0.3)
        }

        50% {
            box-shadow: 0 0 50px rgba(139, 92, 246, 0.5)
        }
    }

    @keyframes shimmer {
        0% {
            background-position: 200% center
        }

        100% {
            background-position: -200% center
        }
    }

    .fade-in {
        animation: fade-in 0.6s ease-out
    }

    .profile-header {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(139, 92, 246, 0.1));
        backdrop-filter: blur(20px);
        border: 1px solid rgba(59, 130, 246, 0.15);
        position: relative;
        overflow: hidden
    }

    .profile-header::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(45deg, transparent 30%, rgba(59, 130, 246, 0.05) 50%, transparent 70%);
        transform: translateX(-100%);
        transition: transform 0.6s
    }

    .profile-header:hover::before {
        transform: translateX(100%)
    }

    .avatar-ring {
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        animation: pulse-avatar 3s ease-in-out infinite
    }

    .stat-card {
        background: linear-gradient(135deg, rgba(19, 24, 36, 0.6), rgba(26, 31, 46, 0.6));
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.3s ease
    }

    .stat-card:hover {
        border-color: rgba(59, 130, 246, 0.3);
        box-shadow: 0 8px 30px rgba(59, 130, 246, 0.15);
        transform: translateY(-4px)
    }

    .settings-card {
        background: linear-gradient(135deg, rgba(19, 24, 36, 0.7), rgba(26, 31, 46, 0.7));
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.05)
    }

    .input-field {
        background: rgba(10, 14, 26, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.3s ease
    }

    .input-field:focus {
        border-color: rgba(59, 130, 246, 0.5);
        background: rgba(10, 14, 26, 0.9);
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.1)
    }

    .save-btn {
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden
    }

    .save-btn::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transform: translateX(-100%);
        transition: transform 0.6s
    }

    .save-btn:hover::before {
        transform: translateX(100%)
    }

    .save-btn:hover {
        box-shadow: 0 12px 35px rgba(59, 130, 246, 0.5);
        transform: translateY(-2px)
    }

    .referral-card {
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(236, 72, 153, 0.1));
        border: 2px solid rgba(139, 92, 246, 0.2);
        backdrop-filter: blur(10px)
    }

    html:not(.dark) .profile-header {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.08), rgba(139, 92, 246, 0.08));
        border: 1px solid rgba(59, 130, 246, 0.2)
    }

    html:not(.dark) .stat-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(248, 250, 252, 0.95));
        border: 1px solid rgba(0, 0, 0, 0.08)
    }

    html:not(.dark) .stat-card:hover {
        box-shadow: 0 8px 30px rgba(59, 130, 246, 0.12)
    }

    html:not(.dark) .settings-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(248, 250, 252, 0.95));
        border: 1px solid rgba(0, 0, 0, 0.08)
    }

    html:not(.dark) .input-field {
        background: rgba(248, 250, 252, 0.8);
        border: 1px solid rgba(0, 0, 0, 0.1)
    }

    html:not(.dark) .input-field:focus {
        background: rgba(255, 255, 255, 1)
    }

    html:not(.dark) .referral-card {
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.08), rgba(236, 72, 153, 0.08));
        border: 2px solid rgba(139, 92, 246, 0.15)
    }
</style>
<main class="min-h-screen pt-24 lg:pl-72 bg-gray-50 dark:bg-gradient-to-br dark:from-dark-900 dark:via-dark-800 dark:to-dark-900 text-gray-900 dark:text-white relative overflow-hidden transition-colors duration-300">
    <div class="absolute inset-0 overflow-hidden pointer-events-none dark:block hidden">
        <div class="absolute top-1/4 right-1/4 w-[500px] h-[500px] bg-blue-500/8 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-1/4 left-1/4 w-[500px] h-[500px] bg-purple-500/8 rounded-full blur-[120px]"></div>
    </div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div id="guest-view" class="fade-in flex flex-col items-center justify-center min-h-[70vh] text-center space-y-8">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-purple-500/20 blur-3xl rounded-full animate-pulse"></div>
                <div class="relative w-32 h-32 rounded-3xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-dark-700 dark:to-dark-800 flex items-center justify-center shadow-2xl border-2 border-white dark:border-white/10">
                    <svg class="w-16 h-16 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>
            <div>
                <h1 class="text-4xl md:text-5xl font-black bg-gradient-to-r from-gray-900 via-blue-900 to-gray-900 dark:from-white dark:via-blue-200 dark:to-white bg-clip-text text-transparent mb-4">Connect Your Wallet</h1>
                <p class="text-gray-600 dark:text-gray-400 max-w-xl mx-auto text-lg leading-relaxed">Access your personalized dashboard, track orders, manage assets, and unlock exclusive features on the Solana blockchain.</p>
            </div>
            <button onclick="App.connectWallet()" class="px-10 py-5 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-black text-lg rounded-2xl shadow-2xl shadow-blue-600/30 hover:shadow-blue-600/50 transition-all transform hover:-translate-y-1 flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Connect Phantom Wallet
            </button>
        </div>
        <div id="auth-view" class="hidden space-y-10 fade-in">
            <div class="profile-header rounded-3xl p-8 md:p-10 shadow-2xl">
                <div class="flex flex-col md:flex-row items-center gap-8">
                    <div class="relative">
                        <div class="avatar-ring w-32 h-32 rounded-full p-1.5 shadow-2xl">
                            <div class="w-full h-full rounded-full bg-white dark:bg-dark-900 flex items-center justify-center text-5xl shadow-inner">👤</div>
                        </div>
                        <div class="absolute -bottom-2 -right-2 w-12 h-12 bg-white dark:bg-dark-800 rounded-full flex items-center justify-center border-4 border-white dark:border-dark-900 shadow-xl" title="Tier Badge">
                            <span id="account-tier-icon" class="text-2xl">🥉</span>
                        </div>
                    </div>
                    <div class="text-center md:text-left flex-1">
                        <h1 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-3">Welcome back, <span id="profile-accountname" class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Account</span></h1>
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500/10 dark:bg-blue-500/10 bg-blue-100 rounded-xl border-2 border-blue-500/30 mb-6">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" />
                            </svg>
                            <p id="profile-wallet" class="text-blue-700 dark:text-blue-400 font-mono text-sm font-bold">Loading...</p>
                        </div>
                        <div class="flex flex-wrap justify-center md:justify-start gap-4">
                            <div class="stat-card px-6 py-4 rounded-2xl shadow-xl">
                                <div class="text-xs text-gray-600 dark:text-gray-400 uppercase font-black mb-2">Tier Status</div>
                                <div id="profile-tier" class="text-xl font-black bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">...</div>
                            </div>
                            <div class="stat-card px-6 py-4 rounded-2xl shadow-xl">
                                <div class="text-xs text-gray-600 dark:text-gray-400 uppercase font-black mb-2">Total Spent</div>
                                <div id="profile-spent" class="text-xl font-black text-gray-900 dark:text-white">...</div>
                            </div>
                            <div class="stat-card px-6 py-4 rounded-2xl shadow-xl">
                                <div class="text-xs text-gray-600 dark:text-gray-400 uppercase font-black mb-2">Orders</div>
                                <div id="profile-orders-count" class="text-xl font-black text-gray-900 dark:text-white">...</div>
                            </div>
                        </div>
                    </div>
                    <button onclick="App.logout()" class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-2xl font-black transition-all shadow-xl hover:shadow-2xl flex items-center gap-2 hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Disconnect
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-8">
                    <div class="flex items-center justify-between">
                        <h2 class="text-3xl font-black text-gray-900 dark:text-white flex items-center gap-3">
                            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Recent Orders
                        </h2>
                        <a href="orders.php" class="px-4 py-2 bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-xl font-bold text-sm hover:bg-blue-500/20 transition-all">View All →</a>
                    </div>
                    <div id="recent-orders-list" class="space-y-4">
                        <div class="settings-card p-12 text-center rounded-3xl shadow-2xl">
                            <div class="relative inline-block">
                                <div class="w-20 h-20 rounded-full border-4 border-blue-500/20 border-t-blue-500 animate-spin"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-blue-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 mt-6 text-sm font-bold uppercase tracking-widest">Loading orders...</p>
                        </div>
                    </div>
                </div>
                <div class="space-y-8">
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white">Account Settings</h2>
                    <div class="settings-card rounded-3xl p-8 space-y-6 shadow-2xl">
                        <div>
                            <label class="block text-xs font-black text-gray-600 dark:text-gray-400 uppercase mb-3 tracking-wider">Username</label>
                            <input type="text" id="input-accountname" class="input-field w-full rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none font-medium" placeholder="Enter username">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-600 dark:text-gray-400 uppercase mb-3 tracking-wider">Email Address</label>
                            <input type="email" id="input-email" class="input-field w-full rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none font-medium" placeholder="your@email.com">
                        </div>
                        <button onclick="saveProfile()" class="save-btn w-full py-4 text-white font-black text-lg rounded-2xl shadow-2xl">
                            Save Changes
                        </button>
                    </div>
                    <div class="referral-card rounded-3xl p-8 shadow-2xl">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-black text-gray-900 dark:text-white">Referral Program</h3>
                        </div>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-6 leading-relaxed">Share your unique referral code and earn <span class="font-bold text-purple-600 dark:text-purple-400">5%</span> of your friends' trading fees forever!</p>
                        <div class="flex gap-3">
                            <input type="text" id="referral-code" readonly class="flex-1 bg-white/50 dark:bg-black/30 border-2 border-purple-300 dark:border-purple-500/30 rounded-xl px-4 py-3 text-sm text-purple-700 dark:text-purple-300 font-mono font-bold focus:outline-none">
                            <button onclick="navigator.clipboard.writeText(document.getElementById('referral-code').value);notyf.success('Referral Code Copied!')" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-xl font-black shadow-xl transition-all hover:scale-105">
                                Copy
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script src="./public/js/pages/profile.js"></script>
<?php require_once 'footer.php'; ?>