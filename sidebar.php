<?php
defined('gashy_exec') or die();
$current_page = basename($_SERVER['PHP_SELF'], '.php');
if ($current_page === 'index') $current_page = 'app';
function navClass($pages)
{
    global $current_page;
    $list = is_array($pages) ? $pages : explode('|', $pages);
    $active = in_array($current_page, $list, true);
    $base = "flex items-center gap-3 px-3 py-2.5 rounded-xl text-[15px] font-semibold transition-all duration-200 group relative overflow-hidden nav-glow ";
    return $base . ($active ? 'nav-active' : 'nav-inactive');
}
function iconClass($pages)
{
    global $current_page;
    $list = is_array($pages) ? $pages : explode('|', $pages);
    $active = in_array($current_page, $list, true);
    $base = "w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-200 shrink-0 text-base ";
    return $base . ($active ? 'icon-active' : 'icon-inactive');
}
function isActive($pages)
{
    global $current_page;
    $list = is_array($pages) ? $pages : explode('|', $pages);
    return in_array($current_page, $list, true);
}
?>
<style>
    #sidebar {
        transition: transform .28s cubic-bezier(.4, 0, .2, 1);
        will-change: transform
    }

    #sidebar-overlay {
        transition: opacity .25s ease
    }

    #sidebar-overlay.hidden {
        opacity: 0;
        pointer-events: none
    }

    #sidebar-overlay:not(.hidden) {
        opacity: 1
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 4px
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #00d48f, #8b5cf6);
        border-radius: 999px
    }

    .sidebar-gradient {
        background: linear-gradient(180deg, rgba(10, 14, 26, .98), rgba(16, 21, 34, .98));
        backdrop-filter: blur(18px)
    }

    .nav-glow:before {
        content: '';
        position: absolute;
        inset: 0;
        transform: translateX(-120%);
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .06), transparent);
        transition: .45s
    }

    .nav-glow:hover:before {
        transform: translateX(120%)
    }

    .nav-active {
        background: linear-gradient(135deg, #00d48f, #00ffaa);
        color: #071019;
        box-shadow: 0 10px 25px rgba(0, 255, 170, .18)
    }

    .nav-inactive {
        color: #9ca3af
    }

    .nav-inactive:hover {
        color: #fff;
        background: rgba(255, 255, 255, .04);
        transform: translateX(3px)
    }

    .icon-active {
        background: rgba(255, 255, 255, .22);
        color: #071019
    }

    .icon-inactive {
        background: linear-gradient(135deg, #1a1f2e, #111827);
        color: #6b7280
    }

    .nav-inactive:hover .icon-inactive {
        color: #00ffaa;
        background: linear-gradient(135deg, rgba(0, 255, 170, .14), rgba(139, 92, 246, .14))
    }

    .section-label {
        color: #6b7280
    }

    .section-divider {
        height: 1px;
        background: linear-gradient(90deg, rgba(0, 255, 170, .4), transparent)
    }

    .section-divider-acc {
        height: 1px;
        background: linear-gradient(90deg, rgba(139, 92, 246, .4), transparent)
    }

    .badge-hot {
        background: rgba(0, 255, 170, .14);
        color: #00ffaa
    }

    .badge-live {
        background: rgba(239, 68, 68, .14);
        color: #f87171
    }

    .badge-new {
        background: rgba(59, 130, 246, .14);
        color: #60a5fa
    }

    .badge-earn {
        background: rgba(139, 92, 246, .14);
        color: #c4b5fd
    }

    .price-card {
        background: linear-gradient(135deg, rgba(0, 212, 143, .08), rgba(139, 92, 246, .08));
        border: 1px solid rgba(0, 255, 170, .12)
    }

    .quest-card {
        background: linear-gradient(135deg, rgba(0, 212, 143, .08), rgba(139, 92, 246, .08));
        border: 1px solid rgba(0, 255, 170, .12)
    }

    @media(max-width:1024px) {
        #sidebar {
            width: 290px;
            z-index: 60 !important;
            padding-top: 0 !important
        }

        #sidebar-overlay {
            z-index: 55 !important
        }
    }

    @media(min-width:1025px) {
        #sidebar {
            width: 280px;
            z-index: 30 !important;
            padding-top: 72px !important
        }
    }

    @media(max-width:640px) {
        #sidebar {
            width: 100%;
            max-width: 320px
        }
    }

    html:not(.dark) .sidebar-gradient {
        background: linear-gradient(180deg, rgba(255, 255, 255, .98), rgba(248, 250, 252, .98));
        border-right: 1px solid rgba(15, 23, 42, .08)
    }

    html:not(.dark) .nav-active {
        background: linear-gradient(135deg, #00a372, #00c896);
        color: #fff
    }

    html:not(.dark) .nav-inactive {
        color: #475569
    }

    html:not(.dark) .nav-inactive:hover {
        color: #0f172a;
        background: rgba(15, 23, 42, .04)
    }

    html:not(.dark) .icon-active {
        background: rgba(255, 255, 255, .25);
        color: #fff
    }

    html:not(.dark) .icon-inactive {
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        color: #64748b;
        border: 1px solid rgba(15, 23, 42, .05)
    }

    html:not(.dark) .price-card,
    html:not(.dark) .quest-card {
        background: linear-gradient(135deg, rgba(0, 163, 114, .05), rgba(139, 92, 246, .05));
        border-color: rgba(0, 163, 114, .14)
    }
</style>
<div id="sidebar-overlay" class="fixed inset-0 bg-black/60 hidden lg:hidden backdrop-blur-sm" onclick="closeSidebar()"></div>
<aside id="sidebar" class="sidebar-gradient fixed inset-y-0 left-0 border-r border-white/5 transform -translate-x-full lg:translate-x-0 shadow-2xl">
    <div class="h-16 px-4 flex items-center justify-between lg:hidden border-b border-gray-200 dark:border-white/5 bg-white/95 dark:bg-transparent backdrop-blur-xl">
        <div class="flex items-center gap-2">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center shadow-lg shadow-primary-500/20">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div class="leading-none">
                <div class="text-sm font-black tracking-tight text-gray-900 dark:text-white">GASHY</div>
                <div class="text-[9px] tracking-[0.25em] text-primary-600 dark:text-primary-400 mt-0.5">BAZAAR</div>
            </div>
        </div>
        <button onclick="closeSidebar()" class="p-2 rounded-xl text-gray-500 dark:text-gray-400 hover:text-red-500 hover:bg-red-500/10 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    <div class="h-full flex flex-col overflow-y-auto custom-scrollbar">
        <div class="p-4">
            <div class="price-card rounded-2xl p-4 border border-primary-500/10">
                <div class="flex items-center justify-between mb-3">
                    <div class="text-[15px] uppercase font-black tracking-[0.2em] text-gray-500 dark:text-gray-500">Token Price</div>
                    <span id="sidebar-token-change" class="px-2 py-1 rounded-lg text-[15px] bg-primary-500/10 text-primary-600 dark:text-primary-300 font-black">...</span>
                </div>
                <div class="flex items-end gap-2">
                    <div class="text-lg font-black bg-gradient-to-r from-primary-500 to-accent-500 bg-clip-text text-transparent">$GASHY</div>
                    <div id="sidebar-token-price" class="text-sm font-bold text-gray-900 dark:text-white">Loading...</div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-white/5 flex justify-between text-[15px]">
                    <span class="text-gray-500 dark:text-gray-500">24h Vol</span>
                    <span id="sidebar-token-vol" class="text-primary-600 dark:text-primary-400 font-bold">...</span>
                </div>
            </div>
        </div>
        <nav class="px-3 pb-4 space-y-1">
            <div class="flex items-center justify-between px-2 pt-1 pb-2">
                <div class="section-label text-[15px] uppercase font-black tracking-[0.25em]">Discover</div>
                <div class="section-divider w-10"></div>
            </div>
            <a href="app.php" class="<?= navClass('app') ?>">
                <span class="<?= iconClass('app') ?>">🏠</span>
                <span class="flex-1 leading-none">Dashboard</span>
                <?php if (isActive('app')): ?><span class="w-2 h-2 rounded-full bg-white animate-pulse"></span><?php endif; ?>
            </a>
            <a href="market.php" class="<?= navClass('market|product') ?>">
                <span class="<?= iconClass('market|product') ?>">🛒</span>
                <span class="flex-1 leading-none">Marketplace</span>
                <span class="badge-hot px-2 py-1 rounded-lg text-[9px] font-black">HOT</span>
            </a>
            <a href="auctions.php" class="<?= navClass('auctions') ?>">
                <span class="<?= iconClass('auctions') ?>">📈</span>
                <span class="flex-1 leading-none">Live Auctions</span>
                <span class="badge-live px-2 py-1 rounded-lg text-[9px] font-black">LIVE</span>
            </a>
            <a href="mystery-boxes.php" class="<?= navClass('mystery-boxes') ?>">
                <span class="<?= iconClass('mystery-boxes') ?>">🎁</span>
                <span class="flex-1 leading-none">Mystery Boxes</span>
            </a>
            <a href="lottery.php" class="<?= navClass('lottery') ?>">
                <span class="<?= iconClass('lottery') ?>">🎟️</span>
                <span class="flex-1 leading-none">Lottery</span>
                <span class="text-[15px] text-gray-500">0.1 SOL</span>
            </a>
            <div class="flex items-center justify-between px-2 pt-5 pb-2">
                <div class="section-label text-[15px] uppercase font-black tracking-[0.25em]">Account</div>
                <div class="section-divider-acc w-10"></div>
            </div>
            <a href="profile.php" class="auth-link hidden <?= navClass('profile') ?>">
                <span class="<?= iconClass('profile') ?>">👤</span>
                <span class="flex-1 leading-none">My Profile</span>
            </a>
            <a href="orders.php" class="auth-link hidden <?= navClass('orders') ?>">
                <span class="<?= iconClass('orders') ?>">📦</span>
                <span class="flex-1 leading-none">My Orders</span>
            </a>
            <a href="seller-hub.php" class="auth-link hidden <?= navClass('seller|seller-hub') ?>">
                <span class="<?= iconClass('seller|seller-hub') ?>">🏪</span>
                <span class="flex-1 leading-none">Seller Hub</span>
            </a>
            <a href="history.php" class="auth-link hidden <?= navClass('history') ?>">
                <span class="<?= iconClass('history') ?>">🕒</span>
                <span class="flex-1 leading-none">History Log</span>
            </a>
            <a href="quests.php" class="<?= navClass('quests') ?>">
                <span class="<?= iconClass('quests') ?>">⭐</span>
                <span class="flex-1 leading-none">Quests</span>
                <span class="badge-earn px-2 py-1 rounded-lg text-[9px] font-black">EARN</span>
            </a>
        </nav>
        <a href="quests.php" class="mt-auto p-4 block">
            <div class="quest-card rounded-2xl p-4 border border-primary-500/10">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-[15px] uppercase tracking-[0.2em] font-black text-gray-500 dark:text-gray-500">Daily Quest</div>
                    <div class="text-lg">✨</div>
                </div>
                <div id="sidebar-quest-title" class="text-sm font-black text-gray-900 dark:text-white mb-3">Loading...</div>
                <div class="w-full h-2 rounded-full bg-gray-200 dark:bg-black/20 overflow-hidden">
                    <div id="sidebar-quest-bar" class="h-full w-0 bg-gradient-to-r from-primary-500 to-accent-500 transition-all duration-700"></div>
                </div>
                <div class="mt-2 text-[15px] text-gray-500 dark:text-gray-500">Complete tasks to earn rewards</div>
            </div>
        </a>
    </div>
</aside>
<script>
    function closeSidebar() {
        document.getElementById('sidebar').classList.add('-translate-x-full')
        document.getElementById('sidebar-overlay').classList.add('hidden')
        document.getElementById('sidebar-toggle')?.classList.remove('active')
    }
</script>