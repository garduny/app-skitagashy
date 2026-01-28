<?php $p = basename($_SERVER['PHP_SELF'], '.php');
function active($n)
{
    global $p;
    return $p == $n ? 'bg-primary-500/10 text-primary-500 border-r-2 border-primary-500' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-white';
}
?>
<aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-white dark:bg-dark-800 border-r border-gray-200 dark:border-white/5 pt-16 transform -translate-x-full lg:translate-x-0 transition-transform duration-300">
    <div class="h-full overflow-y-auto p-4 space-y-1">
        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-2 px-3">Main</div>
        <a href="app" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= active('app') ?>"><i class="fa-solid fa-chart-pie w-5 text-center"></i> Dashboard</a>
        <a href="profile" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= active('profile') ?>"><i class="fa-solid fa-user-circle w-5 text-center"></i> My Profile</a>
        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-6 px-3">Management</div>
        <a href="accounts" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= active('accounts') ?>"><i class="fa-solid fa-users w-5 text-center"></i> Accounts</a>
        <a href="sellers" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= active('sellers') ?>"><i class="fa-solid fa-store w-5 text-center"></i> Sellers</a>
        <a href="categories" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= active('categories') ?>"><i class="fa-solid fa-tags w-5 text-center"></i> Categories</a>
        <a href="products" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= active('products') ?>"><i class="fa-solid fa-box w-5 text-center"></i> Products</a>
        <a href="orders" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= active('orders') ?>"><i class="fa-solid fa-receipt w-5 text-center"></i> Orders</a>
        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-6 px-3">Features</div>
        <a href="auctions" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= active('auctions') ?>"><i class="fa-solid fa-gavel w-5 text-center"></i> Auctions</a>
        <a href="lotteries" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= active('lotteries') ?>"><i class="fa-solid fa-ticket w-5 text-center"></i> Lotteries</a>
        <a href="mystery-boxes" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= active('mystery-boxes') ?>"><i class="fa-solid fa-cube w-5 text-center"></i> Mystery Boxes</a>
        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-6 px-3">System</div>
        <a href="users" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= active('users') ?>"><i class="fa-solid fa-user-shield w-5 text-center"></i> Admins</a>
        <a href="roles" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= active('roles') ?>"><i class="fa-solid fa-user-tag w-5 text-center"></i> Roles</a>
        <a href="permissions" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= active('permissions') ?>"><i class="fa-solid fa-key w-5 text-center"></i> Permissions</a>
        <a href="setting" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?= active('setting') ?>"><i class="fa-solid fa-gear w-5 text-center"></i> Settings</a>
        <div class="mt-8 pt-4 border-t border-gray-200 dark:border-white/5">
            <a href="?logout=1" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-red-500 hover:bg-red-50 dark:hover:bg-red-900/10 rounded-lg transition-colors"><i class="fa-solid fa-right-from-bracket w-5 text-center"></i> Logout</a>
        </div>
    </div>
</aside>