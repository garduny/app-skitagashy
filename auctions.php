<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
?>
<style>
    @keyframes pulse-live {

        0%,
        100% {
            opacity: 1;
            transform: scale(1)
        }

        50% {
            opacity: 0.8;
            transform: scale(1.1)
        }
    }

    @keyframes countdown-pulse {

        0%,
        100% {
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.3)
        }

        50% {
            box-shadow: 0 0 25px rgba(239, 68, 68, 0.6)
        }
    }

    @keyframes gavel-swing {

        0%,
        100% {
            transform: rotate(0deg)
        }

        25% {
            transform: rotate(-15deg)
        }

        75% {
            transform: rotate(15deg)
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

    .auction-card {
        background: linear-gradient(135deg, rgba(19, 24, 36, 0.7), rgba(26, 31, 46, 0.7));
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1)
    }

    .auction-card:hover {
        border-color: rgba(239, 68, 68, 0.3);
        box-shadow: 0 20px 60px rgba(239, 68, 68, 0.2), 0 0 80px rgba(239, 68, 68, 0.1);
        transform: translateY(-8px) scale(1.01)
    }

    .auction-img {
        position: relative;
        overflow: hidden
    }

    .auction-img::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.8) 100%);
        opacity: 0;
        transition: opacity 0.3s
    }

    .auction-card:hover .auction-img::after {
        opacity: 1
    }

    .timer-badge {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.95), rgba(220, 38, 38, 0.95));
        backdrop-filter: blur(10px);
        animation: countdown-pulse 2s ease-in-out infinite
    }

    .bid-stats {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.08), rgba(220, 38, 38, 0.08));
        border: 1px solid rgba(239, 68, 68, 0.2)
    }

    .bid-input {
        background: rgba(10, 14, 26, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.3s ease
    }

    .bid-input:focus {
        border-color: rgba(59, 130, 246, 0.5);
        background: rgba(10, 14, 26, 0.9);
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.1)
    }

    .bid-btn {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden
    }

    .bid-btn::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transform: translateX(-100%);
        transition: transform 0.6s ease
    }

    .bid-btn:hover::before {
        transform: translateX(100%)
    }

    .bid-btn:hover {
        box-shadow: 0 12px 35px rgba(59, 130, 246, 0.5);
        transform: translateY(-2px)
    }

    .filter-btn {
        background: rgba(19, 24, 36, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.3s ease
    }

    .filter-btn.active {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(220, 38, 38, 0.2));
        border-color: rgba(239, 68, 68, 0.4);
        color: #ef4444
    }

    .filter-btn:hover {
        border-color: rgba(239, 68, 68, 0.3)
    }

    .live-dot {
        animation: pulse-live 2s ease-in-out infinite
    }

    html:not(.dark) .auction-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(248, 250, 252, 0.95));
        border: 1px solid rgba(0, 0, 0, 0.08)
    }

    html:not(.dark) .auction-card:hover {
        box-shadow: 0 20px 60px rgba(239, 68, 68, 0.15)
    }

    html:not(.dark) .bid-stats {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.05), rgba(220, 38, 38, 0.05));
        border: 1px solid rgba(239, 68, 68, 0.15)
    }

    html:not(.dark) .bid-input {
        background: rgba(248, 250, 252, 0.8);
        border: 1px solid rgba(0, 0, 0, 0.1)
    }

    html:not(.dark) .bid-input:focus {
        background: rgba(255, 255, 255, 1)
    }

    html:not(.dark) .filter-btn {
        background: rgba(248, 250, 252, 0.8);
        border: 1px solid rgba(0, 0, 0, 0.1)
    }

    html:not(.dark) .filter-btn.active {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(220, 38, 38, 0.15))
    }

    html:not(.dark) .timer-badge {
        background: linear-gradient(135deg, #ef4444, #dc2626)
    }
</style>

<main class="min-h-screen pt-24 lg:pl-72 bg-gray-50 dark:bg-gradient-to-br dark:from-dark-900 dark:via-dark-800 dark:to-dark-900 text-gray-900 dark:text-white transition-colors duration-300 relative overflow-hidden">
    <div class="relative z-10 px-4 sm:px-6 lg:px-8 py-8 max-w-[1920px] mx-auto">

        <!-- Header & Tabs -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 mb-10">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="relative">
                        <div class="w-4 h-4 rounded-full bg-red-500 shadow-lg shadow-red-500/50"></div>
                        <div class="absolute inset-0 w-4 h-4 rounded-full bg-red-500 animate-ping"></div>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-black bg-gradient-to-r from-gray-900 via-red-600 to-gray-900 dark:from-white dark:via-red-400 dark:to-white bg-clip-text text-transparent tracking-tight">Live Auctions</h1>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-lg pl-7">Bid on exclusive items with <span class="font-bold text-red-500">$GASHY</span> tokens and win big!</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <button onclick="loadAuctions('ending_soon')" data-filter="ending_soon" class="filter-btn active px-5 py-3 rounded-xl text-sm font-bold bg-white dark:bg-dark-800 shadow-lg flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg> Ending Soon
                </button>
                <button onclick="loadAuctions('my_bids')" data-filter="my_bids" class="filter-btn px-5 py-3 rounded-xl text-sm font-bold bg-white dark:bg-dark-800 text-gray-600 dark:text-gray-400 shadow-lg flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg> My Bids
                </button>
                <button onclick="loadAuctions('hot')" data-filter="hot" class="filter-btn px-5 py-3 rounded-xl text-sm font-bold bg-white dark:bg-dark-800 text-gray-600 dark:text-gray-400 shadow-lg flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                    </svg> Hot Items
                </button>
            </div>
        </div>

        <!-- Content Grid (Filled by JS) -->
        <div id="auctions-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6 lg:gap-8">
            <!-- Loader -->
            <div class="col-span-full py-32 flex justify-center"><svg class="w-12 h-12 text-red-500 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg></div>
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden flex flex-col items-center justify-center py-32 rounded-3xl border-2 border-dashed border-gray-300 dark:border-white/10 relative overflow-hidden">
            <div class="relative z-10 text-center">
                <div class="w-24 h-24 bg-gray-200 dark:bg-dark-800 rounded-full flex items-center justify-center mb-6 mx-auto shadow-2xl">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-3">No Auctions Found</h3>
                <p class="text-gray-600 dark:text-gray-400 text-lg">Check back later or try a different filter.</p>
            </div>
        </div>

    </div>
</main>
<script src="./public/js/pages/auctions.js"></script>
<?php require_once 'footer.php'; ?>