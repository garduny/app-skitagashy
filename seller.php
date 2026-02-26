<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
$is_seller = false;
$seller_data = [];
$uid = 0;
?>
<style>
    @keyframes float-gentle {

        0%,
        100% {
            transform: translateY(0)
        }

        50% {
            transform: translateY(-15px)
        }
    }

    @keyframes shimmer-border {
        0% {
            background-position: 200% center
        }

        100% {
            background-position: -200% center
        }
    }

    .seller-form-card {
        background: linear-gradient(135deg, rgba(19, 24, 36, 0.8), rgba(26, 31, 46, 0.8));
        backdrop-filter: blur(20px);
        border: 1px solid rgba(59, 130, 246, 0.15);
        position: relative;
        overflow: hidden
    }

    .seller-form-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6, #3b82f6);
        background-size: 200% 100%;
        animation: shimmer-border 3s linear infinite
    }

    .input-seller {
        background: rgba(10, 14, 26, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.3s ease
    }

    .input-seller:focus {
        border-color: rgba(59, 130, 246, 0.5);
        background: rgba(10, 14, 26, 0.9);
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.15)
    }

    .submit-btn {
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden
    }

    .submit-btn::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transform: translateX(-100%);
        transition: transform 0.6s
    }

    .submit-btn:hover::before {
        transform: translateX(100%)
    }

    .submit-btn:hover {
        box-shadow: 0 12px 35px rgba(59, 130, 246, 0.5);
        transform: translateY(-2px)
    }

    .feature-box {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(139, 92, 246, 0.1));
        border: 2px solid rgba(59, 130, 246, 0.2);
        transition: all 0.3s ease
    }

    .feature-box:hover {
        border-color: rgba(59, 130, 246, 0.4);
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.2)
    }

    .illustration-wrapper {
        animation: float-gentle 6s ease-in-out infinite
    }

    html:not(.dark) .seller-form-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.98));
        border: 1px solid rgba(59, 130, 246, 0.2)
    }

    html:not(.dark) .input-seller {
        background: rgba(248, 250, 252, 0.8);
        border: 1px solid rgba(0, 0, 0, 0.1)
    }

    html:not(.dark) .input-seller:focus {
        background: rgba(255, 255, 255, 1)
    }

    html:not(.dark) .feature-box {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.08), rgba(139, 92, 246, 0.08));
        border: 2px solid rgba(59, 130, 246, 0.15)
    }
</style>
<main class="min-h-screen pt-24 lg:pl-72 bg-gray-50 dark:bg-gradient-to-br dark:from-dark-900 dark:via-dark-800 dark:to-dark-900 text-gray-900 dark:text-white relative overflow-hidden transition-colors duration-300">
    <div class="absolute inset-0 overflow-hidden pointer-events-none dark:block hidden">
        <div class="absolute top-1/4 right-1/4 w-[600px] h-[600px] bg-blue-500/8 rounded-full blur-[150px]"></div>
        <div class="absolute bottom-1/4 left-1/4 w-[600px] h-[600px] bg-purple-500/8 rounded-full blur-[150px]"></div>
    </div>
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-16">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-500/10 dark:bg-blue-500/10 bg-blue-100 border-2 border-blue-500/30 mb-6">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span class="text-sm font-black text-blue-700 dark:text-blue-400 uppercase tracking-wider">Seller Program</span>
            </div>
            <h1 class="text-5xl md:text-6xl font-black bg-gradient-to-r from-gray-900 via-blue-900 to-gray-900 dark:from-white dark:via-blue-200 dark:to-white bg-clip-text text-transparent mb-6">Seller <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Center</span></h1>
            <p class="text-gray-600 dark:text-gray-400 max-w-3xl mx-auto text-lg leading-relaxed">Join the decentralized economy. List your digital assets, gift cards, and exclusive codes on Gashy Bazaar and get paid in <span class="font-bold text-blue-600 dark:text-blue-400">crypto instantly</span>.</p>
        </div>
        <div id="seller-loading" class="text-center py-32">
            <div class="relative inline-block">
                <div class="w-24 h-24 rounded-full border-4 border-blue-500/20 border-t-blue-500 animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-10 h-10 text-blue-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
            <p class="text-gray-600 dark:text-gray-400 mt-8 text-sm font-bold uppercase tracking-widest">Loading Seller Status...</p>
        </div>
        <div id="seller-form-view" class="hidden seller-form-card rounded-3xl p-8 md:p-12 shadow-2xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="order-2 lg:order-1">
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white mb-8 flex items-center gap-3">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Open Your Store
                    </h2>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs font-black text-gray-600 dark:text-gray-400 uppercase mb-3 tracking-wider">Store Name</label>
                            <input type="text" id="store-name" placeholder="e.g. CyberTech Digital" class="input-seller w-full rounded-xl px-4 py-4 text-gray-900 dark:text-white focus:outline-none font-medium text-lg">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-600 dark:text-gray-400 uppercase mb-3 tracking-wider">Store Handle</label>
                            <input type="text" id="store-slug" placeholder="e.g. cybertech" class="input-seller w-full rounded-xl px-4 py-4 text-gray-900 dark:text-white focus:outline-none font-medium text-lg">
                            <div class="mt-3 px-4 py-2 bg-blue-500/10 dark:bg-blue-500/10 bg-blue-100 rounded-lg border border-blue-500/30">
                                <p class="text-xs text-gray-700 dark:text-gray-300">Your Store URL:</p>
                                <p class="text-sm font-mono text-blue-700 dark:text-blue-400 font-bold">gashybazaar.com/shop/<span id="slug-preview" class="text-purple-600 dark:text-purple-400">...</span></p>
                            </div>
                        </div>
                        <button onclick="applySeller()" class="submit-btn w-full py-5 text-white font-black text-lg rounded-2xl shadow-2xl flex items-center justify-center gap-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Submit Application
                        </button>
                    </div>
                </div>
                <div class="order-1 lg:order-2">
                    <div class="relative mb-8">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-purple-500/20 blur-3xl rounded-full"></div>
                        <div class="illustration-wrapper relative z-10">
                            <img src="assets/seller-illustration.svg" onerror="this.src='https://cdn-icons-png.flaticon.com/512/2921/2921226.png'" class="w-full max-w-sm mx-auto drop-shadow-2xl" alt="Seller Illustration">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="feature-box p-6 rounded-2xl text-center shadow-xl">
                            <div class="text-4xl font-black bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">5%</div>
                            <div class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Flat Fee</div>
                        </div>
                        <div class="feature-box p-6 rounded-2xl text-center shadow-xl">
                            <div class="text-4xl font-black bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent mb-2">Instant</div>
                            <div class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Payouts</div>
                        </div>
                        <div class="feature-box p-6 rounded-2xl text-center shadow-xl">
                            <div class="text-4xl font-black bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-2">24/7</div>
                            <div class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Support</div>
                        </div>
                        <div class="feature-box p-6 rounded-2xl text-center shadow-xl">
                            <div class="text-4xl font-black bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent mb-2">Global</div>
                            <div class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Reach</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="seller-pending-view" class="hidden text-center py-32">
            <div class="relative inline-block mb-8">
                <div class="absolute inset-0 bg-yellow-500/20 blur-3xl rounded-full"></div>
                <div class="relative w-32 h-32 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center shadow-2xl">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <h2 class="text-4xl font-black text-gray-900 dark:text-white mb-4">Application Pending</h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg max-w-md mx-auto mb-8">Our team is reviewing your store details. You'll receive a notification once approved!</p>
            <div class="inline-flex items-center gap-2 px-6 py-3 bg-yellow-500/10 rounded-xl border-2 border-yellow-500/30">
                <div class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse"></div>
                <span class="text-sm font-bold text-yellow-600 dark:text-yellow-400">Under Review</span>
            </div>
        </div>
        <div id="seller-dashboard-view" class="hidden">
            <div class="relative overflow-hidden bg-gradient-to-br from-green-500 to-emerald-600 rounded-3xl p-12 text-center shadow-2xl">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAwIDEwIEwgNDAgMTAgTSAxMCAwIEwgMTAgNDAgTSAwIDIwIEwgNDAgMjAgTSAyMCAwIEwgMjAgNDAgTSAwIDMwIEwgNDAgMzAgTSAzMCAwIEwgMzAgNDAiIGZpbGw9Im5vbmUiIHN0cm9rZT0iI2ZmZiIgb3BhY2l0eT0iMC4xIiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-20"></div>
                <div class="relative z-10">
                    <div class="w-32 h-32 bg-white/20 backdrop-blur-xl rounded-full flex items-center justify-center mx-auto mb-8 shadow-2xl">
                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-4xl font-black text-white mb-4">You're a Verified Seller! 🎉</h2>
                    <p class="text-white/90 text-lg mb-10 max-w-lg mx-auto">Access your inventory, manage products, and track sales in your dedicated seller dashboard.</p>
                    <a href="/dashboard" class="inline-flex items-center gap-3 px-10 py-5 bg-white hover:bg-gray-100 text-green-600 font-black text-lg rounded-2xl shadow-2xl transition-all hover:scale-105">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                        </svg>
                        Go to Dashboard
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>
<script src="./public/js/pages/seller.js"></script>
<?php require_once 'footer.php'; ?>