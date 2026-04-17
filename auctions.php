<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) require_once 'server/init.php';
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
            opacity: .8;
            transform: scale(1.1)
        }
    }

    @keyframes countdown-pulse {

        0%,
        100% {
            box-shadow: 0 0 15px rgba(239, 68, 68, .25)
        }

        50% {
            box-shadow: 0 0 25px rgba(239, 68, 68, .45)
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
        background: linear-gradient(135deg, rgba(19, 24, 36, .72), rgba(26, 31, 46, .72));
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, .05);
        transition: all .35s ease;
        overflow: hidden;
        position: relative
    }

    .auction-card:hover {
        border-color: rgba(239, 68, 68, .28);
        box-shadow: 0 20px 60px rgba(239, 68, 68, .16);
        transform: translateY(-6px)
    }

    .auction-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #ef4444, #f59e0b, #ef4444);
        background-size: 200% 100%;
        animation: shimmer 4s linear infinite
    }

    .auction-img {
        position: relative;
        aspect-ratio: 1/1;
        overflow: hidden;
        background: #0f172a
    }

    .auction-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .6s ease
    }

    .auction-card:hover .auction-img img {
        transform: scale(1.08)
    }

    .auction-img:after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, transparent 20%, rgba(0, 0, 0, .72));
        pointer-events: none
    }

    .timer-badge {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        animation: countdown-pulse 2s ease-in-out infinite
    }

    .bid-stats {
        background: linear-gradient(135deg, rgba(239, 68, 68, .08), rgba(220, 38, 38, .08));
        border: 1px solid rgba(239, 68, 68, .18)
    }

    .bid-input {
        background: rgba(10, 14, 26, .6);
        border: 1px solid rgba(255, 255, 255, .06);
        transition: .25s
    }

    .bid-input:focus {
        border-color: rgba(59, 130, 246, .5);
        box-shadow: 0 0 18px rgba(59, 130, 246, .14)
    }

    .bid-btn {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        box-shadow: 0 8px 25px rgba(59, 130, 246, .25);
        transition: .25s
    }

    .bid-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 35px rgba(59, 130, 246, .38)
    }

    .filter-btn {
        background: rgba(19, 24, 36, .6);
        border: 1px solid rgba(255, 255, 255, .06);
        transition: .25s
    }

    .filter-btn.active {
        background: linear-gradient(135deg, rgba(239, 68, 68, .18), rgba(220, 38, 38, .18));
        border-color: rgba(239, 68, 68, .35);
        color: #ef4444
    }

    .filter-btn:hover {
        border-color: rgba(239, 68, 68, .25)
    }

    .live-dot {
        animation: pulse-live 2s ease-in-out infinite
    }

    .reserve-ok {
        color: #10b981
    }

    .reserve-no {
        color: #f59e0b
    }

    html:not(.dark) .auction-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, .96), rgba(248, 250, 252, .96));
        border: 1px solid rgba(0, 0, 0, .08)
    }

    html:not(.dark) .auction-card:hover {
        box-shadow: 0 20px 60px rgba(239, 68, 68, .12)
    }

    html:not(.dark) .filter-btn {
        background: #fff;
        border: 1px solid rgba(0, 0, 0, .08)
    }

    html:not(.dark) .bid-input {
        background: #fff;
        border: 1px solid rgba(0, 0, 0, .08)
    }
</style>
<main class="min-h-screen pt-24 lg:pl-72 bg-gray-50 dark:bg-gradient-to-br dark:from-dark-900 dark:via-dark-800 dark:to-dark-900 text-gray-900 dark:text-white transition-colors duration-300">
    <div class="px-4 sm:px-6 lg:px-8 py-8 max-w-[1920px] mx-auto">
        <div class="flex flex-col xl:flex-row items-start xl:items-center justify-between gap-6 mb-10">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="relative">
                        <div class="w-4 h-4 rounded-full bg-red-500 shadow-lg shadow-red-500/50"></div>
                        <div class="absolute inset-0 w-4 h-4 rounded-full bg-red-500 animate-ping"></div>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-black bg-gradient-to-r from-gray-900 via-red-600 to-gray-900 dark:from-white dark:via-red-400 dark:to-white bg-clip-text text-transparent">Live Auctions</h1>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-lg pl-7">Bid with <span class="font-bold text-red-500">$GASHY</span> and win exclusive marketplace items.</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 w-full xl:w-auto">
                <button onclick="loadAuctions('ending_soon')" data-filter="ending_soon" class="filter-btn active px-5 py-3 rounded-xl text-sm font-bold flex items-center justify-center gap-2">Ending Soon</button>
                <button onclick="loadAuctions('hot')" data-filter="hot" class="filter-btn px-5 py-3 rounded-xl text-sm font-bold flex items-center justify-center gap-2">Hot</button>
                <button onclick="loadAuctions('lowest')" data-filter="lowest" class="filter-btn px-5 py-3 rounded-xl text-sm font-bold flex items-center justify-center gap-2">Lowest</button>
                <button onclick="loadAuctions('my_bids')" data-filter="my_bids" class="filter-btn px-5 py-3 rounded-xl text-sm font-bold flex items-center justify-center gap-2">My Bids</button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="rounded-2xl p-5 bg-white dark:bg-dark-800 border border-gray-200 dark:border-white/5 shadow-sm">
                <div class="text-xs uppercase font-bold text-gray-500 mb-2">Live Auctions</div>
                <div id="stat-live" class="text-3xl font-black">0</div>
            </div>
            <div class="rounded-2xl p-5 bg-white dark:bg-dark-800 border border-gray-200 dark:border-white/5 shadow-sm">
                <div class="text-xs uppercase font-bold text-gray-500 mb-2">Total Bids</div>
                <div id="stat-bids" class="text-3xl font-black">0</div>
            </div>
            <div class="rounded-2xl p-5 bg-white dark:bg-dark-800 border border-gray-200 dark:border-white/5 shadow-sm">
                <div class="text-xs uppercase font-bold text-gray-500 mb-2">Reserve Met</div>
                <div id="stat-reserve" class="text-3xl font-black text-green-500">0</div>
            </div>
            <div class="rounded-2xl p-5 bg-white dark:bg-dark-800 border border-gray-200 dark:border-white/5 shadow-sm">
                <div class="text-xs uppercase font-bold text-gray-500 mb-2">Ending < 1h</div>
                        <div id="stat-ending" class="text-3xl font-black text-red-500">0</div>
                </div>
            </div>
            <div id="auctions-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6 lg:gap-8">
                <div class="col-span-full py-32 flex justify-center">
                    <svg class="w-12 h-12 text-red-500 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>
            </div>
            <div id="empty-state" class="hidden flex flex-col items-center justify-center py-32 rounded-3xl border-2 border-dashed border-gray-300 dark:border-white/10 mt-6">
                <div class="w-24 h-24 bg-gray-200 dark:bg-dark-800 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-3xl font-black mb-3">No Auctions Found</h3>
                <p class="text-gray-600 dark:text-gray-400 text-lg">Try another filter or check back later.</p>
            </div>
        </div>
</main>
<?php require_once 'footer.php'; ?>