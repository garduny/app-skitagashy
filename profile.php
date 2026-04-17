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
            transform: translateY(20px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pulse-glow {

        0%,
        100% {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
        }

        50% {
            box-shadow: 0 0 40px rgba(139, 92, 246, 0.5);
        }
    }

    @keyframes shimmer {
        0% {
            background-position: -200% 0;
        }

        100% {
            background-position: 200% 0;
        }
    }

    .fade-in {
        animation: fade-in 0.5s ease-out;
    }

    .profile-header {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(139, 92, 246, 0.1));
        backdrop-filter: blur(10px);
        border: 1px solid rgba(59, 130, 246, 0.2);
        transition: all 0.3s ease;
    }

    html:not(.dark) .profile-header {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(139, 92, 246, 0.05));
        border: 1px solid rgba(59, 130, 246, 0.15);
    }

    .avatar-ring {
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        animation: pulse-glow 3s ease-in-out infinite;
    }

    .stat-card {
        background: rgba(19, 24, 36, 0.5);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.3s ease;
    }

    html:not(.dark) .stat-card {
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(0, 0, 0, 0.08);
    }

    .stat-card:hover {
        border-color: rgba(59, 130, 246, 0.3);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.2);
    }

    .content-card {
        background: rgba(19, 24, 36, 0.6);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    html:not(.dark) .content-card {
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(0, 0, 0, 0.08);
    }

    .input-field {
        background: rgba(10, 14, 26, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.3s ease;
    }

    html:not(.dark) .input-field {
        background: rgba(248, 250, 252, 0.8);
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .input-field:focus {
        border-color: rgba(59, 130, 246, 0.5);
        background: rgba(10, 14, 26, 0.8);
        box-shadow: 0 0 15px rgba(59, 130, 246, 0.15);
        outline: none;
    }

    html:not(.dark) .input-field:focus {
        background: rgba(255, 255, 255, 1);
    }

    .action-btn {
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .action-btn::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transform: translateX(-100%);
    }

    .action-btn:hover::before {
        animation: shimmer 0.6s;
    }

    .action-btn:hover {
        box-shadow: 0 6px 25px rgba(59, 130, 246, 0.5);
        transform: translateY(-2px);
    }

    .referral-card {
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(236, 72, 153, 0.1));
        border: 2px solid rgba(139, 92, 246, 0.2);
        backdrop-filter: blur(8px);
    }

    html:not(.dark) .referral-card {
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.08), rgba(236, 72, 153, 0.08));
        border: 2px solid rgba(139, 92, 246, 0.15);
    }

    .order-item {
        background: rgba(19, 24, 36, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.3s ease;
    }

    html:not(.dark) .order-item {
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(0, 0, 0, 0.06);
    }

    .order-item:hover {
        border-color: rgba(59, 130, 246, 0.3);
        transform: translateX(4px);
    }

    .disconnect-btn {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        transition: all 0.3s ease;
    }

    .disconnect-btn:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        box-shadow: 0 4px 20px rgba(239, 68, 68, 0.4);
        transform: scale(1.05);
    }
</style>
<main class="min-h-screen pt-20 lg:pl-72 bg-gray-50 dark:bg-gradient-to-br dark:from-dark-900 dark:via-dark-800 dark:to-dark-900 text-gray-900 dark:text-white transition-colors duration-300">
    <div class="absolute inset-0 overflow-hidden pointer-events-none dark:block hidden">
        <div class="absolute top-1/4 right-1/4 w-96 h-96 bg-blue-500/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 left-1/4 w-96 h-96 bg-purple-500/5 rounded-full blur-3xl"></div>
    </div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div id="guest-view" class="hidden fade-in flex-col items-center justify-center min-h-[80vh] text-center space-y-6">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-purple-500/20 blur-2xl rounded-full animate-pulse"></div>
                <div class="relative w-24 h-24 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-dark-700 dark:to-dark-800 flex items-center justify-center shadow-xl border-2 border-white dark:border-white/10">
                    <svg class="w-12 h-12 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>
            <div>
                <h1 class="text-3xl md:text-4xl font-black bg-gradient-to-r from-gray-900 via-blue-900 to-gray-900 dark:from-white dark:via-blue-200 dark:to-white bg-clip-text text-transparent mb-3">Connect Your Wallet</h1>
                <p class="text-gray-600 dark:text-gray-400 max-w-xl mx-auto text-base leading-relaxed">Access your dashboard, track orders, and unlock exclusive features on Solana blockchain.</p>
            </div>
            <button onclick="App.connectWallet()" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold text-base rounded-xl shadow-lg shadow-blue-600/30 hover:shadow-blue-600/50 transition-all transform hover:-translate-y-1 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Connect Phantom Wallet
            </button>
        </div>
        <div id="auth-view" class="block space-y-6 fade-in">
            <div class="profile-header rounded-2xl p-6 shadow-lg">
                <div class="flex flex-col md:flex-row items-center gap-6">
                    <div class="relative">
                        <div class="avatar-ring w-20 h-20 rounded-full p-1 shadow-lg">
                            <div class="w-full h-full rounded-full bg-white dark:bg-dark-900 flex items-center justify-center text-3xl">👤</div>
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-8 h-8 bg-white dark:bg-dark-800 rounded-full flex items-center justify-center border-3 border-white dark:border-dark-900 shadow-lg" title="Tier Badge">
                            <span id="account-tier-icon" class="text-lg">🥇</span>
                        </div>
                    </div>
                    <div class="text-center md:text-left flex-1">
                        <h1 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-2">Welcome, <span id="profile-accountname" class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">gardunydev</span></h1>
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-500/10 rounded-lg border border-blue-500/30 mb-3">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" />
                            </svg>
                            <p id="profile-wallet" class="text-blue-700 dark:text-blue-400 font-mono text-xs font-semibold">6dygwo6jHPrExGKrohykhYoC1DkAA6CyPp9qDbhMe1JT</p>
                        </div>
                        <div class="flex flex-wrap justify-center md:justify-start gap-3">
                            <div class="stat-card px-4 py-2 rounded-lg">
                                <div class="text-xs text-gray-600 dark:text-gray-400 font-semibold mb-1">Tier</div>
                                <div id="profile-tier" class="text-base font-black bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">gold</div>
                            </div>
                            <div class="stat-card px-4 py-2 rounded-lg">
                                <div class="text-xs text-gray-600 dark:text-gray-400 font-semibold mb-1">Spent</div>
                                <div id="profile-spent" class="text-base font-black text-gray-900 dark:text-white">0 GASHY</div>
                            </div>
                            <div class="stat-card px-4 py-2 rounded-lg">
                                <div class="text-xs text-gray-600 dark:text-gray-400 font-semibold mb-1">Orders</div>
                                <div id="profile-orders-count" class="text-base font-black text-gray-900 dark:text-white">0</div>
                            </div>
                        </div>
                    </div>
                    <button onclick="App.logout()" class="disconnect-btn px-5 py-2.5 text-white rounded-lg font-bold transition-all shadow-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Disconnect
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div class="lg:col-span-7 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-black text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Recent Orders
                        </h2>
                        <a href="orders.php" class="px-3 py-1.5 bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-lg font-semibold text-sm hover:bg-blue-500/20 transition-all">View All →</a>
                    </div>
                    <div id="recent-orders-list" class="space-y-3 max-h-96 overflow-y-auto">
                        <div class="content-card p-12 text-center rounded-xl flex flex-col items-center justify-center min-h-[300px]">
                            <p class="text-gray-500 dark:text-gray-400 text-sm mb-2">No orders found.</p>
                            <a href="market.php" class="text-blue-500 hover:text-blue-400 text-sm">Browse Market</a>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-5 space-y-4">
                    <h2 class="text-xl font-black text-gray-900 dark:text-white">Account Settings</h2>
                    <div class="content-card rounded-xl p-5 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 uppercase mb-2">Username</label>
                            <input type="text" id="input-accountname" value="gardunydev" class="input-field w-full rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-white font-medium" placeholder="Enter username">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 uppercase mb-2">Email Address</label>
                            <input type="email" id="input-email" value="gardunydeveloper@gmail.com" class="input-field w-full rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-white font-medium" placeholder="your@email.com">
                        </div>
                        <button onclick="saveProfile()" class="action-btn w-full py-3 text-white font-bold rounded-lg">
                            Save Changes
                        </button>
                    </div>
                    <div class="referral-card rounded-xl p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h3 class="text-base font-black text-gray-900 dark:text-white">Referral Program</h3>
                        </div>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">Share your code and earn <span class="font-bold text-purple-600 dark:text-purple-400">5%</span> of trading fees!</p>
                        <div class="flex gap-2">
                            <input type="text" id="referral-code" readonly value="GASHY-REF-Account1" class="flex-1 bg-white/50 dark:bg-black/30 border-2 border-purple-300 dark:border-purple-500/30 rounded-lg px-3 py-2 text-xs text-purple-700 dark:text-purple-300 font-mono font-bold">
                            <button onclick="navigator.clipboard.writeText(document.getElementById('referral-code').value);notyf.success('Referral Code Copied!')" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-lg font-bold text-sm transition-all">
                                Copy
                            </button>
                        </div>
                    </div>
                    <div class="content-card rounded-xl p-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">Balance</div>
                            <div id="withdrawable-balance" class="text-xl font-black text-green-500">5.000 GASHY</div>
                        </div>
                        <input type="number" id="withdraw-amount" placeholder="Enter amount" class="input-field w-full rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-white">
                        <button onclick="requestWithdraw()" class="action-btn w-full py-3 text-white font-bold rounded-lg">Request Withdrawal</button>
                    </div>
                    <div class="content-card rounded-xl p-5 space-y-3">
                        <h3 class="text-base font-black text-gray-900 dark:text-white">Withdrawal History</h3>
                        <div id="withdrawals-list" class="space-y-2 max-h-48 overflow-y-auto flex items-center justify-center min-h-[100px]">
                            <div class="text-gray-400 dark:text-gray-500 text-sm">No withdrawals yet</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script src="./public/js/pages/profile.js"></script>
<?php require_once 'footer.php'; ?>