<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
if ($current_page === 'index') $current_page = 'app';

function navClass($target)
{
    global $current_page;
    $base = "flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all group ";
    if ($current_page === $target) {
        return $base . "bg-green-600 text-white shadow-lg shadow-green-600/20";
    } else {
        return $base . "text-gray-600 dark:text-gray-300 hover:text-green-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5";
    }
}

function iconClass($target)
{
    global $current_page;
    $base = "w-8 h-8 rounded-lg flex items-center justify-center transition-colors ";
    if ($current_page === $target) {
        return $base . "bg-white/20 text-white";
    } else {
        return $base . "bg-gray-200 dark:bg-dark-800 text-gray-500 dark:text-gray-400 group-hover:text-green-600 dark:group-hover:text-white group-hover:bg-green-50 dark:group-hover:bg-white/10";
    }
}
?>

<aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-white dark:bg-dark-900 border-r border-gray-200 dark:border-white/5 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 pt-0 lg:pt-16">

    <!-- Mobile Header (Close Button) -->
    <div class="h-16 flex items-center justify-between px-4 border-b border-gray-200 dark:border-white/5 lg:hidden">
        <span class="text-lg font-black tracking-tight text-gray-900 dark:text-white">GASHY<span class="text-green-600">BAZAAR</span></span>
        <button onclick="document.getElementById('sidebar').classList.add('-translate-x-full');document.getElementById('sidebar-overlay').classList.add('hidden')" class="p-2 text-gray-500 hover:text-red-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="h-[calc(100%-4rem)] lg:h-full flex flex-col overflow-y-auto custom-scrollbar">
        <!-- Price Widget -->
        <div class="p-4 mb-2">
            <div class="p-4 rounded-xl bg-gray-100 dark:bg-gradient-to-br dark:from-dark-800 dark:to-dark-700 border border-gray-200 dark:border-white/5 shadow-inner">
                <p class="text-xs text-gray-500 font-medium mb-1">Current Token Price</p>
                <div class="flex items-center justify-between">
                    <span class="text-gray-900 dark:text-white font-bold">$GASHY</span>
                    <span class="text-green-500 dark:text-green-400 font-mono text-sm flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        <span id="sidebar-token-price">$0.045</span>
                    </span>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-3 space-y-1">
            <p class="px-3 text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-4 mb-2">Discover</p>

            <a href="app.php" class="<?= navClass('app') ?>">
                <span class="<?= iconClass('app') ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </span>
                Home
            </a>

            <a href="market.php" class="<?= navClass('market') ?>">
                <span class="<?= iconClass('market') ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </span>
                Marketplace
            </a>

            <a href="auctions.php" class="<?= navClass('auctions') ?>">
                <span class="<?= iconClass('auctions') ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v22m0-2h-5l.982-2.196m0 0L18 9l-7 7-4.018-8.982L13 13" />
                    </svg>
                </span>
                Live Auctions
                <?php if ($current_page !== 'auctions'): ?>
                    <span class="ml-auto px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 dark:bg-red-500/20 text-red-500 dark:text-red-400 animate-pulse">LIVE</span>
                <?php endif; ?>
            </a>

            <a href="mystery-boxes.php" class="<?= navClass('mystery-boxes') ?>">
                <span class="<?= iconClass('mystery-boxes') ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </span>
                Mystery Boxes
            </a>

            <a href="lottery.php" class="<?= navClass('lottery') ?>">
                <span class="<?= iconClass('lottery') ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                Lottery
            </a>

            <p class="px-3 text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-6 mb-2">Account Zone</p>

            <a href="profile.php" class="auth-link hidden <?= navClass('profile') ?>">
                <span class="<?= iconClass('profile') ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </span>
                My Profile
            </a>

            <a href="orders.php" class="auth-link hidden <?= navClass('orders') ?>">
                <span class="<?= iconClass('orders') ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </span>
                My Orders
            </a>

            <a href="seller.php" class="auth-link hidden <?= navClass('seller') ?>">
                <span class="<?= iconClass('seller') ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </span>
                Seller Hub
            </a>
        </nav>

        <!-- Quest -->
        <div class="mt-auto p-4">
            <div class="p-3 rounded-lg bg-gradient-to-r from-primary-600/10 to-accent-600/10 dark:from-primary-900/50 dark:to-accent-900/50 border border-primary-100 dark:border-white/5 text-center">
                <p class="text-[10px] text-gray-500 dark:text-gray-400 mb-1">Weekly Quest</p>
                <p id="sidebar-quest-title" class="text-xs font-bold text-gray-900 dark:text-white mb-2">Burn 500 $GASHY</p>
                <div class="w-full h-1.5 bg-gray-200 dark:bg-dark-900 rounded-full overflow-hidden">
                    <div id="sidebar-quest-bar" class="h-full bg-gradient-to-r from-primary-500 to-accent-500 w-0 transition-all duration-1000"></div>
                </div>
            </div>
        </div>
    </div>
</aside>
<div id="sidebar-overlay" onclick="document.getElementById('sidebar').classList.add('-translate-x-full');document.getElementById('sidebar-overlay').classList.add('hidden')" class="fixed inset-0 bg-black/60 z-30 hidden lg:hidden backdrop-blur-sm transition-opacity"></div>