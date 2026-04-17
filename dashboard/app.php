<?php
require_once 'init.php';
require_once 'header.php';
require_once 'sidebar.php';
$gashyRate = (float)toGashy();
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));
$statsRow = findQuery(" SELECT
(SELECT COUNT(*) FROM accounts) users,
(SELECT COUNT(*) FROM accounts WHERE is_banned=1) banned_users,
(SELECT COUNT(*) FROM accounts WHERE COALESCE(is_verified,0)=1) verified_users,
(SELECT COUNT(*) FROM sellers WHERE is_approved=1) active_sellers,
(SELECT COUNT(*) FROM sellers WHERE is_approved=0) pending_sellers,
(SELECT COUNT(*) FROM products) total_products,
(SELECT COUNT(*) FROM products WHERE status='active') active_products,
(SELECT COUNT(*) FROM products WHERE stock<=0) out_of_stock_products,
(SELECT COUNT(*) FROM orders) orders_total,
(SELECT COUNT(*) FROM orders WHERE status='completed') completed_orders,
(SELECT COUNT(*) FROM orders WHERE status='pending') pending_orders,
(SELECT COUNT(*) FROM orders WHERE status='cancelled') cancelled_orders,
(SELECT COALESCE(SUM(total_usd),0) FROM orders WHERE status='completed') sales_total,
(SELECT COALESCE(SUM(total_usd),0) FROM orders WHERE status='completed' AND DATE(created_at)='$today') sales_today,
(SELECT COALESCE(SUM(total_usd),0) FROM orders WHERE status='completed' AND DATE(created_at)='$yesterday') sales_yesterday,
(SELECT COUNT(*) FROM orders WHERE DATE(created_at)='$today') orders_today,
(SELECT COUNT(*) FROM orders WHERE DATE(created_at)='$yesterday') orders_yesterday,
(SELECT COALESCE(AVG(total_usd),0) FROM orders WHERE status='completed') avg_order,
(SELECT COALESCE(SUM(amount),0) FROM burn_log) burned_total,
(SELECT COALESCE(SUM(amount),0) FROM burn_log WHERE DATE(created_at)='$today') burned_today,
(SELECT COUNT(*) FROM auctions WHERE status='active') active_auctions,
(SELECT COUNT(*) FROM auctions WHERE status='active' AND end_time<=DATE_ADD(NOW(),INTERVAL 24 HOUR)) auctions_ending_soon,
(SELECT COUNT(*) FROM products WHERE type='mystery_box' AND stock<=5) low_inventory_boxes,
(SELECT COUNT(*) FROM gift_cards WHERE is_sold=0 AND (expiry_date IS NULL OR expiry_date>=CURDATE())) gift_card_codes_available ") ?: [];
$salesToday = (float)($statsRow['sales_today'] ?? 0);
$salesYesterday = (float)($statsRow['sales_yesterday'] ?? 0);
$salesGrowth = $salesYesterday > 0 ? (($salesToday - $salesYesterday) / $salesYesterday) * 100 : ($salesToday > 0 ? 100 : 0);
$ordersToday = (int)($statsRow['orders_today'] ?? 0);
$ordersYesterday = (int)($statsRow['orders_yesterday'] ?? 0);
$ordersGrowth = $ordersYesterday > 0 ? (($ordersToday - $ordersYesterday) / $ordersYesterday) * 100 : ($ordersToday > 0 ? 100 : 0);
$verifiedUsers = (int)($statsRow['verified_users'] ?? 0);
$totalUsers = max(1, (int)($statsRow['users'] ?? 0));
$verifyRate = ($verifiedUsers / $totalUsers) * 100;
$stats = [
    'users' => (int)($statsRow['users'] ?? 0),
    'banned_users' => (int)($statsRow['banned_users'] ?? 0),
    'verified_users' => $verifiedUsers,
    'verify_rate' => round($verifyRate, 1),
    'active_sellers' => (int)($statsRow['active_sellers'] ?? 0),
    'pending_sellers' => (int)($statsRow['pending_sellers'] ?? 0),
    'total_products' => (int)($statsRow['total_products'] ?? 0),
    'active_products' => (int)($statsRow['active_products'] ?? 0),
    'out_of_stock_products' => (int)($statsRow['out_of_stock_products'] ?? 0),
    'orders_total' => (int)($statsRow['orders_total'] ?? 0),
    'completed_orders' => (int)($statsRow['completed_orders'] ?? 0),
    'pending_orders' => (int)($statsRow['pending_orders'] ?? 0),
    'cancelled_orders' => (int)($statsRow['cancelled_orders'] ?? 0),
    'sales_total' => (float)($statsRow['sales_total'] ?? 0),
    'sales_today' => $salesToday,
    'sales_growth' => round($salesGrowth, 1),
    'orders_today' => $ordersToday,
    'orders_growth' => round($ordersGrowth, 1),
    'avg_order' => (float)($statsRow['avg_order'] ?? 0),
    'burned_total' => (float)($statsRow['burned_total'] ?? 0),
    'burned_today' => (float)($statsRow['burned_today'] ?? 0),
    'active_auctions' => (int)($statsRow['active_auctions'] ?? 0),
    'auctions_ending_soon' => (int)($statsRow['auctions_ending_soon'] ?? 0),
    'low_inventory_boxes' => (int)($statsRow['low_inventory_boxes'] ?? 0),
    'gift_card_codes_available' => (int)($statsRow['gift_card_codes_available'] ?? 0)
];
$recent = getQuery(" SELECT o.id,o.total_usd,o.total_gashy,o.status,o.created_at,a.accountname FROM orders o LEFT JOIN accounts a ON a.id=o.account_id ORDER BY o.id DESC LIMIT 6 ");
$top_products = getQuery(" SELECT p.id,p.title,p.slug,p.images,p.price_usd,COALESCE(SUM(oi.quantity),0) sold FROM products p LEFT JOIN order_items oi ON oi.product_id=p.id GROUP BY p.id ORDER BY sold DESC,p.id DESC LIMIT 5 ");
$top_sellers = getQuery(" SELECT s.store_name,s.store_slug,s.total_sales,s.rating,s.account_id FROM sellers s WHERE s.is_approved=1 ORDER BY s.total_sales DESC,s.rating DESC,s.account_id DESC LIMIT 5 ");
$chart_data = getQuery(" SELECT DATE(created_at) d,COALESCE(SUM(total_usd),0) t FROM orders WHERE status='completed' AND created_at>=DATE_SUB(NOW(),INTERVAL 7 DAY) GROUP BY DATE(created_at) ORDER BY d ASC ");
$dates = [];
$vols = [];
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $dates[] = date('M d', strtotime($d));
    $found = false;
    foreach ($chart_data as $row) {
        if ($row['d'] == $d) {
            $vols[] = (float)$row['t'];
            $found = true;
            break;
        }
    }
    if (!$found) $vols[] = 0;
}
$cat_stats = getQuery(" SELECT c.name,COUNT(p.id) count FROM categories c LEFT JOIN products p ON p.category_id=c.id GROUP BY c.id,c.name ORDER BY count DESC,c.name ASC LIMIT 6 ");
$cat_labels = array_column($cat_stats, 'name');
$cat_counts = array_map('intval', array_column($cat_stats, 'count'));
$alerts = [];
if ($stats['pending_sellers'] > 0) $alerts[] = ['icon' => 'fa-user-clock', 'color' => 'yellow', 'text' => $stats['pending_sellers'] . ' seller approvals waiting'];
if ($stats['pending_orders'] > 0) $alerts[] = ['icon' => 'fa-receipt', 'color' => 'orange', 'text' => $stats['pending_orders'] . ' pending orders need review'];
if ($stats['out_of_stock_products'] > 0) $alerts[] = ['icon' => 'fa-box-open', 'color' => 'red', 'text' => $stats['out_of_stock_products'] . ' products are out of stock'];
if ($stats['auctions_ending_soon'] > 0) $alerts[] = ['icon' => 'fa-gavel', 'color' => 'blue', 'text' => $stats['auctions_ending_soon'] . ' auctions ending within 24h'];
if ($stats['low_inventory_boxes'] > 0) $alerts[] = ['icon' => 'fa-gift', 'color' => 'purple', 'text' => $stats['low_inventory_boxes'] . ' mystery box inventory rows are low'];
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white">GASHY Command Center</h1>
            <p class="text-sm text-gray-500">Marketplace health, sales, users, sellers and inventory in one view.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <div class="px-4 py-2 rounded-xl bg-white dark:bg-dark-800 border border-gray-200 dark:border-white/5 shadow-sm">
                <div class="text-[10px] uppercase font-bold text-gray-400">Rate</div>
                <div class="text-sm font-black text-primary-500"><?= number_format($gashyRate, 6) ?></div>
            </div>
            <div class="px-4 py-2 rounded-xl bg-white dark:bg-dark-800 border border-gray-200 dark:border-white/5 shadow-sm">
                <div class="text-[10px] uppercase font-bold text-gray-400">Live Auctions</div>
                <div class="text-sm font-black text-gray-900 dark:text-white"><?= number_format($stats['active_auctions']) ?></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm relative overflow-hidden group hover:border-primary-500/50 transition-colors">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity"><i class="fa-solid fa-wallet text-4xl text-primary-500"></i></div>
            <div class="text-gray-500 text-xs font-bold uppercase mb-2">Total Volume</div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">$<?= number_format($stats['sales_total'], 2) ?></div>
            <div class="mt-1 text-xs text-gray-400 font-bold"><?= $gashyRate > 0 ? number_format($stats['sales_total'] / $gashyRate, 2) : '0.00' ?> G</div>
            <div class="mt-2 text-xs font-bold flex items-center gap-1 <?= $stats['sales_growth'] >= 0 ? 'text-green-500' : 'text-red-500' ?>"><i class="fa-solid fa-<?= $stats['sales_growth'] >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i><?= $stats['sales_growth'] ?>% vs yesterday</div>
        </div>

        <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm relative overflow-hidden group hover:border-blue-500/50 transition-colors">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity"><i class="fa-solid fa-cart-shopping text-4xl text-blue-500"></i></div>
            <div class="text-gray-500 text-xs font-bold uppercase mb-2">Total Orders</div>
            <div class="text-3xl font-black text-gray-900 dark:text-white"><?= number_format($stats['orders_total']) ?></div>
            <div class="mt-1 text-xs text-gray-400 font-bold"><?= number_format($stats['completed_orders']) ?> completed / <?= number_format($stats['pending_orders']) ?> pending</div>
            <div class="mt-2 text-xs font-bold flex items-center gap-1 <?= $stats['orders_growth'] >= 0 ? 'text-green-500' : 'text-red-500' ?>"><i class="fa-solid fa-<?= $stats['orders_growth'] >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i><?= $stats['orders_growth'] ?>% vs yesterday</div>
        </div>

        <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm relative overflow-hidden group hover:border-red-500/50 transition-colors">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity"><i class="fa-solid fa-fire text-4xl text-red-500"></i></div>
            <div class="text-gray-500 text-xs font-bold uppercase mb-2">Total Burned</div>
            <div class="text-3xl font-black text-gray-900 dark:text-white"><?= number_format($stats['burned_total'], 2) ?><span class="text-sm text-gray-400"> G</span></div>
            <div class="mt-1 text-xs text-gray-400 font-bold"><?= number_format($stats['burned_today'], 2) ?> G today</div>
            <div class="mt-2 text-xs text-red-500 font-bold">Deflationary Asset</div>
        </div>

        <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm relative overflow-hidden group hover:border-purple-500/50 transition-colors">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity"><i class="fa-solid fa-chart-line text-4xl text-purple-500"></i></div>
            <div class="text-gray-500 text-xs font-bold uppercase mb-2">Avg Order Value</div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">$<?= number_format($stats['avg_order'], 2) ?></div>
            <div class="mt-1 text-xs text-gray-400 font-bold"><?= $gashyRate > 0 ? number_format($stats['avg_order'] / $gashyRate, 2) : '0.00' ?> G</div>
            <div class="mt-2 text-xs text-gray-400 font-bold"><?= number_format($stats['active_sellers']) ?> active sellers</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-dark-800 p-5 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs uppercase font-bold text-gray-500">Users</div>
                <i class="fa-solid fa-users text-blue-500"></i>
            </div>
            <div class="text-2xl font-black text-gray-900 dark:text-white"><?= number_format($stats['users']) ?></div>
            <div class="mt-2 text-xs text-gray-400"><?= number_format($stats['verified_users']) ?> verified · <?= number_format($stats['banned_users']) ?> banned</div>
            <div class="mt-2 h-2 rounded-full bg-gray-100 dark:bg-white/5 overflow-hidden">
                <div class="h-full bg-blue-500 rounded-full" style="width: <?= $stats['verify_rate'] ?>%"></div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 p-5 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs uppercase font-bold text-gray-500">Sellers</div>
                <i class="fa-solid fa-store text-emerald-500"></i>
            </div>
            <div class="text-2xl font-black text-gray-900 dark:text-white"><?= number_format($stats['active_sellers']) ?></div>
            <div class="mt-2 text-xs text-gray-400"><?= number_format($stats['pending_sellers']) ?> waiting approval</div>
            <div class="mt-2 text-xs font-bold <?= $stats['pending_sellers'] > 0 ? 'text-yellow-500' : 'text-green-500' ?>"><?= $stats['pending_sellers'] > 0 ? 'Needs action' : 'Healthy' ?></div>
        </div>

        <div class="bg-white dark:bg-dark-800 p-5 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs uppercase font-bold text-gray-500">Products</div>
                <i class="fa-solid fa-box text-purple-500"></i>
            </div>
            <div class="text-2xl font-black text-gray-900 dark:text-white"><?= number_format($stats['total_products']) ?></div>
            <div class="mt-2 text-xs text-gray-400"><?= number_format($stats['active_products']) ?> active · <?= number_format($stats['out_of_stock_products']) ?> out of stock</div>
            <div class="mt-2 text-xs font-bold <?= $stats['out_of_stock_products'] > 0 ? 'text-red-500' : 'text-green-500' ?>"><?= $stats['out_of_stock_products'] > 0 ? 'Restock needed' : 'Inventory stable' ?></div>
        </div>

        <div class="bg-white dark:bg-dark-800 p-5 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs uppercase font-bold text-gray-500">Gift Cards / Boxes</div>
                <i class="fa-solid fa-gift text-pink-500"></i>
            </div>
            <div class="text-2xl font-black text-gray-900 dark:text-white"><?= number_format($stats['gift_card_codes_available']) ?></div>
            <div class="mt-2 text-xs text-gray-400">Available gift card codes</div>
            <div class="mt-2 text-xs font-bold <?= $stats['low_inventory_boxes'] > 0 ? 'text-yellow-500' : 'text-green-500' ?>"><?= number_format($stats['low_inventory_boxes']) ?> low mystery-box rows</div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
        <div class="xl:col-span-2 bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-gray-900 dark:text-white">Revenue Analytics</h3>
                <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-primary-500 animate-pulse"></span><span class="text-xs font-bold text-primary-500">Last 7 Days</span></div>
            </div>
            <div id="revenueChart" style="min-height:320px;"></div>
        </div>

        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm p-6">
            <h3 class="font-bold text-gray-900 dark:text-white mb-6">Inventory Distribution</h3>
            <div id="catChart" style="min-height:320px;"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6 mb-8">
        <div class="xl:col-span-1 bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm p-6">
            <h3 class="font-bold text-gray-900 dark:text-white mb-5">Alerts</h3>
            <div class="space-y-3">
                <?php foreach ($alerts as $alert): ?>
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/5">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-<?= $alert['color'] ?>-500/10 text-<?= $alert['color'] ?>-500"><i class="fa-solid <?= $alert['icon'] ?>"></i></div>
                        <div class="text-sm text-gray-700 dark:text-gray-200"><?= $alert['text'] ?></div>
                    </div>
                <?php endforeach; ?>
                <?php if (!$alerts): ?>
                    <div class="p-4 rounded-xl bg-green-500/10 text-green-500 text-sm font-bold">No urgent alerts right now.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="xl:col-span-3 bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/5">
                    <div class="text-xs uppercase font-bold text-gray-500 mb-1">Sales Today</div>
                    <div class="text-xl font-black text-gray-900 dark:text-white">$<?= number_format($stats['sales_today'], 2) ?></div>
                </div>
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/5">
                    <div class="text-xs uppercase font-bold text-gray-500 mb-1">Orders Today</div>
                    <div class="text-xl font-black text-gray-900 dark:text-white"><?= number_format($stats['orders_today']) ?></div>
                </div>
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/5">
                    <div class="text-xs uppercase font-bold text-gray-500 mb-1">Cancelled Orders</div>
                    <div class="text-xl font-black text-gray-900 dark:text-white"><?= number_format($stats['cancelled_orders']) ?></div>
                </div>
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/5">
                    <div class="text-xs uppercase font-bold text-gray-500 mb-1">Auctions Ending Soon</div>
                    <div class="text-xl font-black text-gray-900 dark:text-white"><?= number_format($stats['auctions_ending_soon']) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-gray-900 dark:text-white">Recent Orders</h3>
                <a href="orders.php" class="text-xs font-bold text-primary-500 hover:underline">View All</a>
            </div>
            <div class="space-y-4">
                <?php foreach ($recent as $r): ?>
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-white/5 last:border-0 last:pb-0">
                        <div class="min-w-0">
                            <div class="font-bold text-sm text-gray-900 dark:text-white truncate">#<?= $r['id'] ?> - <?= htmlspecialchars($r['accountname'] ?: 'Unknown') ?></div>
                            <div class="text-xs text-gray-500"><?= date('M d, H:i', strtotime($r['created_at'])) ?></div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-sm text-primary-500">$<?= number_format((float)$r['total_usd'], 2) ?></div>
                            <div class="text-[10px] text-gray-400"><?= number_format((float)$r['total_gashy'], 2) ?> G</div>
                            <div class="text-[10px] uppercase font-bold <?= $r['status'] == 'completed' ? 'text-green-500' : ($r['status'] == 'cancelled' ? 'text-red-500' : 'text-yellow-500') ?>"><?= $r['status'] ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (!$recent): ?>
                    <div class="text-sm text-gray-400">No recent orders.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-gray-900 dark:text-white">Top Products</h3>
                <a href="products.php" class="text-xs font-bold text-primary-500 hover:underline">View All</a>
            </div>
            <div class="space-y-4">
                <?php foreach ($top_products as $tp):
                    $images = json_decode($tp['images'], true) ?: [];
                    $img = $images[0] ?? '';
                ?>
                    <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-white/5 last:border-0 last:pb-0">
                        <img src="../<?= $img ?>" class="w-10 h-10 rounded bg-gray-100 dark:bg-white/5 object-cover">
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-sm text-gray-900 dark:text-white truncate"><?= $tp['title'] ?></div>
                            <div class="text-xs text-gray-500">$<?= number_format((float)$tp['price_usd'], 2) ?></div>
                            <div class="text-[10px] text-gray-400"><?= $gashyRate > 0 ? number_format((float)$tp['price_usd'] / $gashyRate, 2) . ' G' : '0.00 G' ?></div>
                        </div>
                        <div class="text-right">
                            <div class="text-primary-500 font-bold text-sm"><?= number_format((float)$tp['sold']) ?></div>
                            <div class="text-[10px] text-gray-400">Sold</div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (!$top_products): ?>
                    <div class="text-sm text-gray-400">No products yet.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-gray-900 dark:text-white">Top Sellers</h3>
                <a href="sellers.php" class="text-xs font-bold text-primary-500 hover:underline">View All</a>
            </div>
            <div class="space-y-4">
                <?php foreach ($top_sellers as $i => $ts):
                    $sellerTotal = sellerStats($ts['account_id'])['total_sale'] ?? $ts['total_sales'];
                ?>
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-white/5 last:border-0 last:pb-0">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-white/10 flex items-center justify-center font-bold text-gray-500 text-xs"><?= $i + 1 ?></div>
                            <div class="min-w-0">
                                <div class="font-bold text-sm text-gray-900 dark:text-white truncate"><?= $ts['store_name'] ?></div>
                                <div class="text-[10px] text-gray-400">@<?= $ts['store_slug'] ?></div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-bold text-gray-900 dark:text-white"><?= number_format((float)$sellerTotal, 2) ?> Sales</div>
                            <div class="text-[10px] text-yellow-500"><i class="fa-solid fa-star"></i> <?= number_format((float)$ts['rating'], 2) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (!$top_sellers): ?>
                    <div class="text-sm text-gray-400">No sellers yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>
<script>
    var revOpts = {
        series: [{
            name: 'Revenue',
            data: <?= json_encode($vols) ?>
        }],
        chart: {
            height: 320,
            type: 'area',
            toolbar: {
                show: false
            },
            fontFamily: 'Inter, sans-serif',
            background: 'transparent'
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3,
            colors: ['#00ffaa']
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.35,
                opacityTo: 0.04,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: <?= json_encode($dates) ?>,
            labels: {
                style: {
                    colors: '#9ca3af',
                    fontSize: '10px'
                }
            },
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            }
        },
        yaxis: {
            labels: {
                formatter: function(val) {
                    return '$' + Number(val).toFixed(0);
                },
                style: {
                    colors: '#9ca3af',
                    fontSize: '10px'
                }
            }
        },
        grid: {
            borderColor: '#334155',
            strokeDashArray: 4,
            xaxis: {
                lines: {
                    show: false
                }
            }
        },
        tooltip: {
            theme: 'dark',
            y: {
                formatter: function(val) {
                    return '$' + Number(val).toFixed(2);
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#revenueChart"), revOpts).render();
    var catOpts = {
        series: <?= json_encode($cat_counts) ?>,
        labels: <?= json_encode($cat_labels) ?>,
        chart: {
            type: 'donut',
            height: 320,
            fontFamily: 'Inter, sans-serif',
            background: 'transparent'
        },
        colors: ['#00ffaa', '#3b82f6', '#8b5cf6', '#ef4444', '#f59e0b', '#14b8a6'],
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        name: {
                            fontSize: '12px',
                            color: '#9ca3af'
                        },
                        value: {
                            fontSize: '24px',
                            color: '#fff',
                            fontWeight: 900
                        }
                    }
                }
            }
        },
        stroke: {
            show: true,
            colors: ['#1e293b'],
            width: 2
        },
        dataLabels: {
            enabled: false
        },
        legend: {
            position: 'bottom',
            labels: {
                colors: '#9ca3af'
            }
        },
        tooltip: {
            theme: 'dark'
        }
    };
    new ApexCharts(document.querySelector("#catChart"), catOpts).render();
</script>
<?php require_once 'footer.php'; ?>