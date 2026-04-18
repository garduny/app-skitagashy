<?php
$p = basename($_SERVER['PHP_SELF'], '.php');
function active($n)
{
    global $p;
    return $p == $n ? 'active-item' : 'idle-item';
}
?>
<style>
    @keyframes slideRight {
        from {
            transform: translateX(-100%);
            opacity: 0
        }

        to {
            transform: translateX(0);
            opacity: 1
        }
    }

    #sidebar {
        animation: slideRight .28s ease;
        width: 16rem !important;
        max-width: 16rem !important;
        min-width: 16rem !important
    }

    @media (max-width:1023px) {
        #sidebar {
            max-width: 88vw !important;
            min-width: auto !important
        }
    }

    .sidebar-shell {
        background: rgba(255, 255, 255, .92);
        backdrop-filter: blur(18px);
        border-right: 1px solid rgba(15, 23, 42, .06)
    }

    html.dark .sidebar-shell {
        background: rgba(10, 14, 26, .92);
        border-right: 1px solid rgba(255, 255, 255, .05)
    }

    .nav-item {
        position: relative;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 14px;
        font-size: .9rem;
        transition: .22s ease;
        overflow: hidden
    }

    .nav-item i {
        width: 18px;
        text-align: center;
        font-size: .95rem
    }

    .nav-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 9px;
        bottom: 9px;
        width: 3px;
        border-radius: 99px;
        background: linear-gradient(180deg, #00ffaa, #00d48f);
        opacity: 0;
        transform: scaleY(.3);
        transition: .22s ease
    }

    .idle-item {
        color: #4b5563
    }

    .idle-item:hover {
        background: rgba(15, 23, 42, .05);
        color: #111827;
        transform: translateX(3px)
    }

    html.dark .idle-item {
        color: #94a3b8
    }

    html.dark .idle-item:hover {
        background: rgba(255, 255, 255, .05);
        color: #fff
    }

    .active-item {
        color: #00e89f;
        background: linear-gradient(90deg, rgba(0, 255, 170, .14), rgba(0, 255, 170, .04));
        font-weight: 900;
        box-shadow: 0 10px 25px rgba(0, 255, 170, .08)
    }

    .active-item::before {
        opacity: 1;
        transform: scaleY(1)
    }

    .section-label {
        font-size: .62rem;
        font-weight: 900;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: #9ca3af;
        padding: 0 10px;
        margin: 14px 0 6px
    }

    .brand-box {
        border: 1px solid rgba(0, 255, 170, .14);
        background: linear-gradient(135deg, rgba(0, 255, 170, .10), rgba(124, 58, 237, .08));
        border-radius: 16px;
        padding: 12px
    }

    .logout-btn {
        background: linear-gradient(135deg, rgba(239, 68, 68, .10), rgba(220, 38, 38, .08));
        border: 1px solid rgba(239, 68, 68, .14)
    }

    .logout-btn:hover {
        background: #ef4444;
        color: #fff;
        transform: translateY(-1px)
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 5px
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, .35);
        border-radius: 99px
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent
    }
</style>
<aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-72 max-w-[88vw] -translate-x-full lg:translate-x-0 transition-transform duration-300 pt-16 sidebar-shell">
    <div class="h-full overflow-y-auto custom-scrollbar px-4 pb-6">
        <div class="brand-box p-4 mb-4 mt-2">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#00ffaa] to-[#00d48f] text-black flex items-center justify-center font-black text-lg">G</div>
                <div>
                    <div class="text-sm font-black text-gray-900 dark:text-white">GASHY Admin</div>
                    <div class="text-[11px] uppercase tracking-[0.22em] text-gray-500">Control Panel</div>
                </div>
            </div>
        </div>
        <div class="section-label">Main</div>
        <a href="app" class="nav-item <?= active('app') ?>"><i class="fa-solid fa-chart-pie"></i><span>Dashboard</span></a>
        <a href="profile" class="nav-item <?= active('profile') ?>"><i class="fa-solid fa-user-circle"></i><span>My Profile</span></a>
        <div class="section-label">Management</div>
        <a href="accounts" class="nav-item <?= active('accounts') ?> <?= active('accountdetail') ?>"><i class="fa-solid fa-users"></i><span>Accounts</span></a>
        <a href="sellers" class="nav-item <?= active('sellers') ?> <?= active('sellerdetail') ?>"><i class="fa-solid fa-store"></i><span>Sellers</span></a>
        <a href="payouts.php" class="nav-item <?= active('payouts') ?>"><i class="fa-solid fa-money-bill-transfer"></i><span>Payouts</span></a>
        <a href="categories" class="nav-item <?= active('categories') ?>"><i class="fa-solid fa-tags"></i><span>Categories</span></a>
        <a href="products" class="nav-item <?= active('products') ?> <?= active('productdetail') ?> <?= active('inventory') ?>"><i class="fa-solid fa-box"></i><span>Products</span></a>
        <a href="orders" class="nav-item <?= active('orders') ?> <?= active('orderdetail') ?>"><i class="fa-solid fa-receipt"></i><span>Orders</span></a>
        <div class="section-label">Features</div>
        <a href="auctions" class="nav-item <?= active('auctions') ?> <?= active('auctiondetail') ?>"><i class="fa-solid fa-gavel"></i><span>Auctions</span></a>
        <a href="lotteries" class="nav-item <?= active('lotteries') ?> <?= active('lotterydetail') ?>"><i class="fa-solid fa-ticket"></i><span>Lotteries</span></a>
        <a href="mystery-boxes" class="nav-item <?= active('mystery-boxes') ?> <?= active('mystery-box-detail') ?>"><i class="fa-solid fa-cube"></i><span>Mystery Boxes</span></a>
        <a href="quests.php" class="nav-item <?= active('quests') ?>"><i class="fa-solid fa-bullseye"></i><span>Quests</span></a>
        <div class="section-label">NFT Engine</div>
        <a href="nft-drops.php" class="nav-item <?= active('nft-drops') ?>"><i class="fa-solid fa-rocket"></i><span>Launchpad</span></a>
        <a href="nft-campaigns.php" class="nav-item <?= active('nft-campaigns') ?>"><i class="fa-solid fa-fire"></i><span>Burn Campaigns</span></a>
        <a href="nft-logs.php" class="nav-item <?= active('nft-logs') ?>"><i class="fa-solid fa-list-check"></i><span>Burn Logs</span></a>
        <div class="section-label">System</div>
        <a href="banners.php" class="nav-item <?= active('banners') ?>"><i class="fa-solid fa-images"></i><span>Banners</span></a>
        <a href="users.php" class="nav-item <?= active('users') ?>"><i class="fa-solid fa-user-shield"></i><span>Admins</span></a>
        <a href="roles.php" class="nav-item <?= active('roles') ?>"><i class="fa-solid fa-user-tag"></i><span>Roles</span></a>
        <a href="permissions.php" class="nav-item <?= active('permissions') ?>"><i class="fa-solid fa-key"></i><span>Permissions</span></a>
        <a href="logs.php" class="nav-item <?= active('logs') ?>"><i class="fa-solid fa-list-ul"></i><span>Activity Logs</span></a>
        <a href="system.php" class="nav-item <?= active('system') ?>"><i class="fa-solid fa-server"></i><span>System Info</span></a>
        <a href="setting.php" class="nav-item <?= active('setting') ?>"><i class="fa-solid fa-gear"></i><span>Settings</span></a>
        <div class="pt-5 mt-5 border-t border-gray-200 dark:border-white/10">
            <a href="?logout=1" class="nav-item logout-btn text-red-500"><i class="fa-solid fa-right-from-bracket"></i><span class="font-black">Logout</span></a>
        </div>
        <div class="h-5"></div>
    </div>
</aside>