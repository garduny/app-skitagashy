<?php $p = basename($_SERVER['PHP_SELF'], '.php');
function active($n)
{
    global $p;
    return $p == $n ? 'bg-gradient-to-r from-[#00ffaa]/15 to-[#00d48f]/15 text-[#00ffaa] border-r-3 border-[#00ffaa] font-black shadow-lg shadow-[#00ffaa]/10' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-white font-medium';
}
?>
<style>
    @keyframes slide-right {
        from {
            transform: translateX(-100%);
            opacity: 0
        }

        to {
            transform: translateX(0);
            opacity: 1
        }
    }

    .admin-sidebar {
        background: linear-gradient(180deg, rgba(10, 14, 26, 0.98), rgba(19, 24, 36, 0.98));
        backdrop-filter: blur(20px);
        border-right: 1px solid rgba(0, 255, 170, 0.08);
        animation: slide-right 0.3s ease-out
    }

    .nav-item {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden
    }

    .nav-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, #00ffaa, #00d48f);
        transform: scaleY(0);
        transition: transform 0.3s ease
    }

    .nav-item:hover::before,
    .nav-item.active::before {
        transform: scaleY(1)
    }

    .section-label {
        color: #6b7280;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        margin: 1.5rem 0 0.5rem 0;
        padding: 0 1rem;
        position: relative
    }

    .section-label::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        width: 8px;
        height: 2px;
        background: linear-gradient(90deg, #00ffaa, transparent)
    }

    .logout-btn {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.1));
        border: 1px solid rgba(239, 68, 68, 0.2);
        transition: all 0.3s ease
    }

    .logout-btn:hover {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        border-color: #ef4444;
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3)
    }

    html:not(.dark) .admin-sidebar {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.98));
        border-right: 1px solid rgba(0, 212, 143, 0.12)
    }
</style>
<aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-white dark:bg-dark-800 border-r border-gray-200 dark:border-white/5 pt-16 transform -translate-x-full lg:translate-x-0 transition-transform duration-300">
    <div class="h-full overflow-y-auto p-4 space-y-1 custom-scrollbar">
        <div class="section-label">MAIN</div>
        <a href="app" class="nav-item flex items-center gap-4 px-4 py-3 text-sm rounded-xl transition-all <?= active('app') ?>">
            <i class="fa-solid fa-chart-pie w-5 text-center text-lg"></i>
            <span>Dashboard</span>
        </a>
        <a href="profile" class="nav-item flex items-center gap-4 px-4 py-3 text-sm rounded-xl transition-all <?= active('profile') ?>">
            <i class="fa-solid fa-user-circle w-5 text-center text-lg"></i>
            <span>My Profile</span>
        </a>
        <div class="section-label">MANAGEMENT</div>
        <a href="accounts" class="nav-item flex items-center gap-4 px-4 py-3 text-sm rounded-xl transition-all <?= active('accounts') ?> <?= active('accountdetail') ?>">
            <i class="fa-solid fa-users w-5 text-center text-lg"></i>
            <span>Accounts</span>
        </a>
        <a href="sellers" class="nav-item flex items-center gap-4 px-4 py-3 text-sm rounded-xl transition-all <?= active('sellers') ?> <?= active('sellerdetail') ?>">
            <i class="fa-solid fa-store w-5 text-center text-lg"></i>
            <span>Sellers</span>
        </a>
        <a href="payouts.php" class="nav-item flex items-center gap-4 px-4 py-3 text-sm rounded-xl transition-all <?= active('payouts') ?>">
            <i class="fa-solid fa-money-bill-transfer w-5 text-center text-lg"></i>
            <span>Payouts</span>
        </a>
        <a href="categories" class="nav-item flex items-center gap-4 px-4 py-3 text-sm rounded-xl transition-all <?= active('categories') ?>">
            <i class="fa-solid fa-tags w-5 text-center text-lg"></i>
            <span>Categories</span>
        </a>
        <a href="products" class="nav-item flex items-center gap-4 px-4 py-3 text-sm rounded-xl transition-all <?= active('products') ?> <?= active('productdetail') ?> <?= active('inventory') ?>">
            <i class="fa-solid fa-box w-5 text-center text-lg"></i>
            <span>Products</span>
        </a>
        <a href="orders" class="nav-item flex items-center gap-4 px-4 py-3 text-sm rounded-xl transition-all <?= active('orders') ?> <?= active('orderdetail') ?>">
            <i class="fa-solid fa-receipt w-5 text-center text-lg"></i>
            <span>Orders</span>
        </a>
        <div class="section-label">FEATURES</div>
        <a href="auctions" class="nav-item flex items-center gap-4 px-4 py-3 text-sm rounded-xl transition-all <?= active('auctions') ?> <?= active('auctiondetail') ?>">
            <i class="fa-solid fa-gavel w-5 text-center text-lg"></i>
            <span>Auctions</span>
        </a>
        <a href="lotteries" class="nav-item flex items-center gap-4 px-4 py-3 text-sm rounded-xl transition-all <?= active('lotteries') ?> <?= active('lotterydetail') ?>">
            <i class="fa-solid fa-ticket w-5 text-center text-lg"></i>
            <span>Lotteries</span>
        </a>
        <a href="mystery-boxes" class="nav-item flex items-center gap-4 px-4 py-3 text-sm rounded-xl transition-all <?= active('mystery-boxes') ?> <?= active('mystery-box-detail') ?>">
            <i class="fa-solid fa-cube w-5 text-center text-lg"></i>
            <span>Mystery Boxes</span>
        </a>
        <a href="quests.php" class="nav-item flex items-center gap-4 px-4 py-3 text-sm rounded-xl transition-all <?= active('quests') ?>">
            <i class="fa-solid fa-bullseye w-5 text-center text-lg"></i>
            <span>Quests</span>
        </a>


        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-6 px-3">NFT Engine</div>

        <a href="nft-drops.php" class="nav-item flex items-center gap-4 px-4 py-3 text-sm rounded-xl transition-all <?= active('nft-drops') ?>">
            <i class="fa-solid fa-rocket w-5 text-center text-lg"></i>
            <span>Launchpad</span>
        </a>

        <a href="nft-campaigns.php" class="nav-item flex items-center gap-4 px-4 py-3 text-sm rounded-xl transition-all <?= active('nft-campaigns') ?>">
            <i class="fa-solid fa-fire w-5 text-center text-lg"></i>
            <span>Burn Campaigns</span>
        </a>

        <a href="nft-logs.php" class="nav-item flex items-center gap-4 px-4 py-3 text-sm rounded-xl transition-all <?= active('nft-logs') ?>">
            <i class="fa-solid fa-list-check w-5 text-center text-lg"></i>
            <span>Burn Logs</span>
        </a>


        <div class="section-label">SYSTEM</div>
        <a href="banners.php" class="nav-item flex items-center gap-4 px-4 py-3 text-sm rounded-xl transition-all <?= active('banners') ?>">
            <i class="fa-solid fa-images w-5 text-center text-lg"></i>
            <span>Banners</span>
        </a>
        <a href="users.php" class="nav-item flex items-center gap-4 px-4 py-3 text-sm rounded-xl transition-all <?= active('users') ?>">
            <i class="fa-solid fa-user-shield w-5 text-center text-lg"></i>
            <span>Admins</span>
        </a>
        <a href="roles.php" class="nav-item flex items-center gap-4 px-4 py-3 text-sm rounded-xl transition-all <?= active('roles') ?>">
            <i class="fa-solid fa-user-tag w-5 text-center text-lg"></i>
            <span>Roles</span>
        </a>
        <a href="permissions.php" class="nav-item flex items-center gap-4 px-4 py-3 text-sm rounded-xl transition-all <?= active('permissions') ?>">
            <i class="fa-solid fa-key w-5 text-center text-lg"></i>
            <span>Permissions</span>
        </a>
        <a href="logs.php" class="nav-item flex items-center gap-4 px-4 py-3 text-sm rounded-xl transition-all <?= active('logs') ?>">
            <i class="fa-solid fa-list-ul w-5 text-center text-lg"></i>
            <span>Activity Logs</span>
        </a>
        <a href="system.php" class="nav-item flex items-center gap-4 px-4 py-3 text-sm rounded-xl transition-all <?= active('system') ?>">
            <i class="fa-solid fa-server w-5 text-center text-lg"></i>
            <span>System Info</span>
        </a>
        <a href="setting.php" class="nav-item flex items-center gap-4 px-4 py-3 text-sm rounded-xl transition-all <?= active('setting') ?>">
            <i class="fa-solid fa-gear w-5 text-center text-lg"></i>
            <span>Settings</span>
        </a>
        <div class="mt-8 pt-4 border-t-2 border-gray-200 dark:border-white/10">
            <a href="?logout=1" class="logout-btn nav-item flex items-center gap-4 px-4 py-3 text-sm rounded-xl transition-all">
                <i class="fa-solid fa-right-from-bracket w-5 text-center text-lg"></i>
                <span class="font-black">Logout</span>
            </a>
        </div>
        <div class="h-4"></div>
    </div>
</aside>