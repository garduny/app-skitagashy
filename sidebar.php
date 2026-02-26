<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
if ($current_page === 'index') $current_page = 'app';
function navClass($target)
{
    global $current_page;
    $base = "flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-300 group relative overflow-hidden ";
    if ($current_page === $target) {
        return $base . "nav-active";
    }
    return $base . "nav-inactive";
}
function iconClass($target)
{
    global $current_page;
    $base = "w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-300 shrink-0 ";
    if ($current_page === $target) {
        return $base . "icon-active";
    }
    return $base . "icon-inactive";
}
?>
<style>
    .nav-glow::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(0, 255, 170, 0.08), transparent);
        transition: left 0.5s ease;
    }

    .nav-glow:hover::before {
        left: 100%
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 4px
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #00d48f, #8B5CF6);
        border-radius: 10px;
    }

    .sidebar-gradient {
        background: linear-gradient(180deg, rgba(10, 14, 26, 0.98) 0%, rgba(19, 24, 36, 0.98) 100%);
        backdrop-filter: blur(20px);
    }

    .nav-active {
        background: linear-gradient(135deg, #00d48f, #00ffaa);
        color: #0a0e1a;
        box-shadow: 0 4px 20px rgba(0, 255, 170, 0.25);
    }

    .nav-inactive {
        color: #9ca3af;
    }

    .nav-inactive:hover {
        color: #00ffaa;
        background: rgba(255, 255, 255, 0.04);
    }

    .icon-active {
        background: rgba(255, 255, 255, 0.2);
        color: #0a0e1a;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .icon-inactive {
        background: linear-gradient(135deg, #1a1f2e, #131824);
        color: #6b7280;
    }

    .nav-inactive:hover .icon-inactive {
        background: linear-gradient(135deg, rgba(0, 255, 170, 0.15), rgba(139, 92, 246, 0.15));
        color: #00ffaa;
        box-shadow: 0 0 12px rgba(0, 255, 170, 0.2);
    }

    .price-card {
        background: linear-gradient(135deg, rgba(0, 212, 143, 0.08) 0%, rgba(139, 92, 246, 0.08) 100%);
        border: 1px solid rgba(0, 255, 170, 0.15);
        position: relative;
        overflow: hidden;
    }

    .price-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: conic-gradient(from 0deg, transparent, rgba(0, 255, 170, 0.07), transparent 30%);
        animation: spin 10s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg)
        }

        100% {
            transform: rotate(360deg)
        }
    }

    .price-divider {
        border-color: rgba(255, 255, 255, 0.08)
    }

    .price-label {
        color: #6b7280
    }

    .price-vol {
        color: #00ffaa
    }

    .token-price-text {
        color: #fff
    }

    .quest-card {
        background: linear-gradient(135deg, rgba(0, 212, 143, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
        border: 1px solid rgba(0, 255, 170, 0.2);
        box-shadow: 0 4px 20px rgba(0, 255, 170, 0.08);
    }

    .quest-label {
        color: #6b7280
    }

    .quest-title {
        color: #fff
    }

    .quest-progress-label {
        color: #6b7280
    }

    .quest-progress-bg {
        background: #0a0e1a
    }

    .sidebar-header {
        background: linear-gradient(135deg, rgba(0, 212, 143, 0.1), rgba(139, 92, 246, 0.1));
        border-bottom: 1px solid rgba(0, 255, 170, 0.2);
    }

    .sidebar-logo-text {
        background: linear-gradient(135deg, #fff, #00ffaa);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .sidebar-logo-sub {
        color: #00ffaa
    }

    .sidebar-close-btn {
        color: #9ca3af
    }

    .sidebar-close-btn:hover {
        color: #f87171;
        background: rgba(239, 68, 68, 0.1)
    }

    .section-label {
        color: #6b7280
    }

    .section-divider {
        background: linear-gradient(90deg, rgba(0, 255, 170, 0.4), transparent)
    }

    .section-divider-acc {
        background: linear-gradient(90deg, rgba(139, 92, 246, 0.4), transparent)
    }

    .badge-hot {
        background: rgba(0, 255, 170, 0.15);
        color: #00ffaa
    }

    .badge-live {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171
    }

    .badge-live-dot {
        background: #ef4444
    }

    .badge-new {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444
    }

    .badge-earn {
        background: rgba(139, 92, 246, 0.15);
        color: #a78bfa
    }

    .active-dot {
        background: rgba(255, 255, 255, 0.9)
    }

    .lottery-price {
        color: #6b7280
    }

    html:not(.dark) .sidebar-gradient {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.99) 0%, rgba(248, 250, 252, 0.99) 100%);
        border-right: 1px solid rgba(0, 0, 0, 0.07) !important;
    }

    html:not(.dark) .sidebar-header {
        background: linear-gradient(135deg, rgba(0, 163, 114, 0.08), rgba(139, 92, 246, 0.08));
        border-bottom: 1px solid rgba(0, 163, 114, 0.15);
    }

    html:not(.dark) .sidebar-logo-text {
        background: linear-gradient(135deg, #0f172a, #007a55);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    html:not(.dark) .sidebar-logo-sub {
        color: #007a55
    }

    html:not(.dark) .sidebar-close-btn {
        color: #64748b
    }

    html:not(.dark) .sidebar-close-btn:hover {
        color: #dc2626;
        background: rgba(220, 38, 38, 0.08)
    }

    html:not(.dark) .nav-active {
        background: linear-gradient(135deg, #00a372, #00c896);
        color: #fff;
        box-shadow: 0 4px 20px rgba(0, 163, 114, 0.25);
    }

    html:not(.dark) .nav-inactive {
        color: #475569
    }

    html:not(.dark) .nav-inactive:hover {
        color: #007a55;
        background: rgba(0, 163, 114, 0.06);
    }

    html:not(.dark) .icon-active {
        background: rgba(255, 255, 255, 0.25);
        color: #fff;
    }

    html:not(.dark) .icon-inactive {
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        color: #64748b;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    html:not(.dark) .nav-inactive:hover .icon-inactive {
        background: linear-gradient(135deg, rgba(0, 163, 114, 0.12), rgba(139, 92, 246, 0.12));
        color: #007a55;
        border-color: rgba(0, 163, 114, 0.2);
        box-shadow: 0 0 12px rgba(0, 163, 114, 0.15);
    }

    html:not(.dark) .price-card {
        background: linear-gradient(135deg, rgba(0, 163, 114, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
        border: 1px solid rgba(0, 163, 114, 0.2);
        box-shadow: 0 2px 12px rgba(0, 163, 114, 0.08);
    }

    html:not(.dark) .price-card::before {
        background: conic-gradient(from 0deg, transparent, rgba(0, 163, 114, 0.05), transparent 30%);
    }

    html:not(.dark) .price-divider {
        border-color: rgba(0, 0, 0, 0.07)
    }

    html:not(.dark) .price-label {
        color: #64748b
    }

    html:not(.dark) .price-vol {
        color: #007a55
    }

    html:not(.dark) .token-price-text {
        color: #0f172a
    }

    html:not(.dark) .quest-card {
        background: linear-gradient(135deg, rgba(0, 163, 114, 0.07) 0%, rgba(139, 92, 246, 0.07) 100%);
        border: 1px solid rgba(0, 163, 114, 0.2);
        box-shadow: 0 4px 16px rgba(0, 163, 114, 0.08);
    }

    html:not(.dark) .quest-label {
        color: #64748b
    }

    html:not(.dark) .quest-title {
        color: #0f172a
    }

    html:not(.dark) .quest-progress-label {
        color: #64748b
    }

    html:not(.dark) .quest-progress-bg {
        background: #e2e8f0
    }

    html:not(.dark) .section-label {
        color: #94a3b8
    }

    html:not(.dark) .section-divider {
        background: linear-gradient(90deg, rgba(0, 163, 114, 0.35), transparent)
    }

    html:not(.dark) .section-divider-acc {
        background: linear-gradient(90deg, rgba(139, 92, 246, 0.3), transparent)
    }

    html:not(.dark) .badge-hot {
        background: rgba(0, 163, 114, 0.1);
        color: #007a55
    }

    html:not(.dark) .badge-live {
        background: rgba(220, 38, 38, 0.08);
        color: #dc2626
    }

    html:not(.dark) .badge-live-dot {
        background: #dc2626
    }

    html:not(.dark) .badge-new {
        background: rgba(220, 38, 38, 0.08);
        color: #dc2626
    }

    html:not(.dark) .badge-earn {
        background: rgba(139, 92, 246, 0.1);
        color: #7c3aed
    }

    html:not(.dark) .active-dot {
        background: rgba(255, 255, 255, 0.8)
    }

    html:not(.dark) .lottery-price {
        color: #94a3b8
    }

    html:not(.dark) .nav-glow::before {
        background: linear-gradient(90deg, transparent, rgba(0, 163, 114, 0.06), transparent);
    }

    @media(max-width:1024px) {
        #sidebar {
            width: 280px;
            z-index: 60 !important
        }

        #sidebar-overlay {
            z-index: 55 !important
        }
    }

    @media(min-width:1025px) {
        #sidebar {
            z-index: 30 !important
        }
    }

    @media(max-width:768px) {
        #sidebar {
            width: 100%;
            max-width: 320px
        }

        .sidebar-header {
            height: 64px !important
        }

        .price-card {
            padding: 16px !important
        }

        .quest-card {
            padding: 14px !important
        }

        .nav-glow {
            min-height: 48px
        }
    }

    @media(max-width:640px) {
        #sidebar {
            max-width: 100%
        }

        .sidebar-header {
            height: 60px !important
        }
    }

    #sidebar-overlay {
        transition: opacity 0.3s ease
    }

    #sidebar-overlay.hidden {
        opacity: 0;
        pointer-events: none
    }

    #sidebar-overlay:not(.hidden) {
        opacity: 1
    }

    #sidebar {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1)
    }

    @media(max-width:1024px) {
        #sidebar {
            padding-top: 0 !important
        }
    }

    @media(min-width:1024px) {
        #sidebar {
            padding-top: 80px !important
        }
    }
</style>
<div id="sidebar-overlay" onclick="document.getElementById('sidebar').classList.add('-translate-x-full');document.getElementById('sidebar-overlay').classList.add('hidden');document.getElementById('sidebar-toggle')?.classList.remove('active')" class="fixed inset-0 bg-black/60 z-[45] hidden lg:hidden backdrop-blur-sm"></div>
<aside id="sidebar" class="sidebar-gradient fixed inset-y-0 left-0 z-40 lg:z-30 w-72 border-r border-white/5 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 pt-0 lg:pt-20 shadow-2xl">
    <div class="sidebar-header h-20 flex items-center justify-between px-5 lg:hidden backdrop-blur-xl">
        <div class="flex items-center gap-2">
            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center logo-glow">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div class="flex flex-col">
                <span class="sidebar-logo-text text-base font-black tracking-tighter">GASHY</span>
                <span class="sidebar-logo-sub text-[9px] font-bold tracking-widest -mt-0.5">BAZAAR</span>
            </div>
        </div>
        <button onclick="document.getElementById('sidebar').classList.add('-translate-x-full');document.getElementById('sidebar-overlay').classList.add('hidden');document.getElementById('sidebar-toggle')?.classList.remove('active')" class="sidebar-close-btn p-2 rounded-lg transition-all" aria-label="Close menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    <div class="h-[calc(100%-5rem)] lg:h-full flex flex-col overflow-y-auto custom-scrollbar">
        <div class="p-4 sm:p-5 mb-3">
            <div class="price-card p-4 sm:p-5 rounded-2xl shadow-xl relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <span class="price-label text-xs font-bold uppercase tracking-wider">Token Price</span>
                    </div>
                    <span id="sidebar-token-change" class="px-2 py-1 rounded-lg bg-gray-500/20 text-gray-400 text-[10px] font-bold flex items-center gap-1">
                        ...
                    </span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-xl sm:text-2xl font-black bg-gradient-to-r from-primary-500 to-accent-500 bg-clip-text text-transparent">$GASHY</span>
                    <span class="token-price-text text-lg sm:text-xl font-bold font-mono" id="sidebar-token-price">Loading...</span>
                </div>
                <div class="mt-3 pt-3 border-t border-white/10 flex items-center justify-between text-xs">
                    <span class="price-label text-gray-500">24h Vol</span>
                    <span id="sidebar-token-vol" class="price-vol font-bold text-primary-500">...</span>
                </div>
            </div>
        </div>
        <nav class="flex-1 px-3 sm:px-4 space-y-1.5">
            <div class="flex items-center justify-between px-4 py-2">
                <p class="section-label text-[10px] font-black uppercase tracking-widest">Discover</p>
                <div class="section-divider w-12 h-px"></div>
            </div>
            <a href="app.php" class="nav-glow <?= navClass('app') ?>">
                <span class="<?= iconClass('app') ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </span>
                <span class="flex-1">Dashboard</span>
                <?php if ($current_page === 'app'): ?>
                    <div class="active-dot w-1.5 h-1.5 rounded-full animate-pulse"></div>
                <?php endif; ?>
            </a>
            <a href="market.php" class="nav-glow <?= navClass('market') ?> <?= navClass('product') ?>">
                <span class="<?= iconClass('market') ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </span>
                <span class="flex-1">Marketplace</span>
                <span class="badge-hot px-2 py-0.5 rounded-md text-[9px] font-black">HOT</span>
            </a>
            <a href="auctions.php" class="nav-glow <?= navClass('auctions') ?>">
                <span class="<?= iconClass('auctions') ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                </span>
                <span class="flex-1">Live Auctions</span>
                <?php if ($current_page !== 'auctions'): ?>
                    <span class="badge-live px-2 py-0.5 rounded-md text-[9px] font-black animate-pulse flex items-center gap-1">
                        <div class="badge-live-dot w-1.5 h-1.5 rounded-full animate-ping"></div>
                        LIVE
                    </span>
                <?php endif; ?>
            </a>
            <a href="mystery-boxes.php" class="nav-glow <?= navClass('mystery-boxes') ?>">
                <span class="<?= iconClass('mystery-boxes') ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </span>
                <span class="flex-1">Mystery Boxes</span>
                <svg class="w-4 h-4 text-accent-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
            </a>
            <a href="lottery.php" class="nav-glow <?= navClass('lottery') ?>">
                <span class="<?= iconClass('lottery') ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <span class="flex-1">Lottery</span>
                <span class="lottery-price text-[10px] font-mono">0.1 SOL</span>
            </a>
            <div class="flex items-center justify-between px-4 py-2 mt-6 sm:mt-8">
                <p class="section-label text-[10px] font-black uppercase tracking-widest">Account</p>
                <div class="section-divider-acc w-12 h-px"></div>
            </div>
            <a href="profile.php" class="auth-link hidden nav-glow <?= navClass('profile') ?>">
                <span class="<?= iconClass('profile') ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </span>
                <span class="flex-1">My Profile</span>
            </a>
            <a href="orders.php" class="auth-link hidden nav-glow <?= navClass('orders') ?>">
                <span class="<?= iconClass('orders') ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </span>
                <span class="flex-1">My Orders</span>
            </a>
            <a href="seller.php" class="auth-link hidden nav-glow <?= navClass('seller') ?> <?= navClass('seller-hub') ?>">
                <span class="<?= iconClass('seller') ?> <?= iconClass('seller-hub') ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </span>
                <span class="flex-1">Seller Hub</span>
                <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
            </a>
            <a href="nft-burn.php" class="nav-glow <?= navClass('nft-burn') ?>">
                <span class="<?= iconClass('nft-burn') ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" />
                    </svg>
                </span>
                <span class="flex-1">NFT Burn</span>
                <span class="badge-new px-2 py-0.5 rounded-md text-[9px] font-black">NEW</span>
            </a>
            <a href="nft-drops.php" class="nav-glow <?= navClass('nft-drops') ?>">
                <span class="<?= iconClass('nft-drops') ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </span>
                <span class="flex-1">NFT Launchpad</span>
            </a>
            <a href="quests.php" class="nav-glow <?= navClass('quests') ?>">
                <span class="<?= iconClass('quests') ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </span>
                <span class="flex-1">Quests</span>
                <span class="badge-earn px-2 py-0.5 rounded-md text-[9px] font-black">EARN</span>
            </a>
            <a href="history.php" class="auth-link hidden nav-glow <?= navClass('history') ?>">
                <span class="<?= iconClass('history') ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <span class="flex-1">History Log</span>
            </a>
        </nav>
        <a href="quests.php" class="mt-auto p-4 sm:p-5 block hover:opacity-90 transition-opacity">
            <div class="quest-card p-3 sm:p-4 rounded-2xl backdrop-blur-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-primary-500/20 to-accent-500/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <span class="quest-label text-[10px] font-black uppercase tracking-widest">....</span>
                        <svg class="w-5 h-5 text-accent-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <p id="sidebar-quest-title" class="quest-title text-sm font-black mb-3 leading-tight">....</p>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="quest-progress-label font-medium">...</span>
                            <span class="font-bold text-primary-500">...</span>
                        </div>
                        <div class="quest-progress-bg relative w-full h-2 rounded-full overflow-hidden">
                            <div id="sidebar-quest-bar" class="relative h-full bg-gradient-to-r from-primary-500 via-accent-500 to-primary-500 rounded-full w-0 transition-all duration-1000 shadow-lg shadow-primary-500/30" style="background-size:200% 100%;animation:spin 3s linear infinite"></div>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <span class="quest-progress-label text-[10px] font-medium">...</span>
                            <span class="text-xs font-black text-yellow-500 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                ...
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</aside>