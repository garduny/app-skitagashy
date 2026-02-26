<?php
require_once 'init.php';
require_once 'header.php';
require_once 'sidebar.php';
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));
$sales_today = findQuery(" SELECT SUM(total_gashy) as t FROM orders WHERE status='completed' AND DATE(created_at)='$today' ")['t'] ?? 0;
$sales_yesterday = findQuery(" SELECT SUM(total_gashy) as t FROM orders WHERE status='completed' AND DATE(created_at)='$yesterday' ")['t'] ?? 1;
$sales_growth = $sales_yesterday > 0 ? (($sales_today - $sales_yesterday) / $sales_yesterday) * 100 : 0;
$orders_today = countQuery(" SELECT 1 FROM orders WHERE DATE(created_at)='$today' ");
$orders_yesterday = countQuery(" SELECT 1 FROM orders WHERE DATE(created_at)='$yesterday' ");
$orders_growth = $orders_yesterday > 0 ? (($orders_today - $orders_yesterday) / $orders_yesterday) * 100 : 0;
$stats = [
    'users' => countQuery(" SELECT 1 FROM accounts "),
    'sales_total' => findQuery(" SELECT SUM(total_gashy) as t FROM orders WHERE status='completed' ")['t'] ?? 0,
    'sales_today' => $sales_today,
    'sales_growth' => round($sales_growth, 1),
    'orders_total' => countQuery(" SELECT 1 FROM orders "),
    'orders_today' => $orders_today,
    'orders_growth' => round($orders_growth, 1),
    'avg_order' => findQuery(" SELECT AVG(total_gashy) as t FROM orders WHERE status='completed' ")['t'] ?? 0,
    'burned' => findQuery(" SELECT SUM(amount) as t FROM burn_log ")['t'] ?? 0,
    'sellers' => countQuery(" SELECT 1 FROM sellers WHERE is_approved=1 ")
];
$recent = getQuery(" SELECT o.id,o.total_gashy,o.status,o.created_at,a.accountname FROM orders o JOIN accounts a ON o.account_id=a.id ORDER BY o.id DESC LIMIT 6 ");
$top_products = getQuery(" SELECT p.title,SUM(oi.quantity) as sold,p.images,p.price_gashy FROM order_items oi JOIN products p ON oi.product_id=p.id GROUP BY oi.product_id ORDER BY sold DESC LIMIT 5 ");
$top_sellers = getQuery(" SELECT store_name,total_sales,rating,account_id FROM sellers WHERE is_approved=1 ORDER BY total_sales DESC LIMIT 5 ");
$chart_data = getQuery(" SELECT DATE(created_at) as d, SUM(total_gashy) as t FROM orders WHERE status='completed' AND created_at>=DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY DATE(created_at) ORDER BY d ASC ");
$dates = [];
$vols = [];
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $dates[] = $d;
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
$cat_stats = getQuery(" SELECT c.name,COUNT(p.id) as count FROM categories c LEFT JOIN products p ON c.id=p.category_id GROUP BY c.id ");
$cat_labels = array_column($cat_stats, 'name');
$cat_counts = array_column($cat_stats, 'count');
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm relative overflow-hidden group hover:border-primary-500/50 transition-colors">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity"><i class="fa-solid fa-wallet text-4xl text-primary-500"></i></div>
            <div class="text-gray-500 text-xs font-bold uppercase mb-2">Total Volume</div>
            <div class="text-3xl font-black text-gray-900 dark:text-white"><?= number_format($stats['sales_total']) ?> <span class="text-sm text-gray-400">G</span></div>
            <div class="mt-2 text-xs font-bold flex items-center gap-1 <?= $stats['sales_growth'] >= 0 ? 'text-green-500' : 'text-red-500' ?>">
                <i class="fa-solid fa-<?= $stats['sales_growth'] >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i> <?= $stats['sales_growth'] ?>% (24h)
            </div>
        </div>
        <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm relative overflow-hidden group hover:border-blue-500/50 transition-colors">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity"><i class="fa-solid fa-cart-shopping text-4xl text-blue-500"></i></div>
            <div class="text-gray-500 text-xs font-bold uppercase mb-2">Total Orders</div>
            <div class="text-3xl font-black text-gray-900 dark:text-white"><?= number_format($stats['orders_total']) ?></div>
            <div class="mt-2 text-xs font-bold flex items-center gap-1 <?= $stats['orders_growth'] >= 0 ? 'text-green-500' : 'text-red-500' ?>">
                <i class="fa-solid fa-<?= $stats['orders_growth'] >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i> <?= $stats['orders_growth'] ?>% (24h)
            </div>
        </div>
        <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm relative overflow-hidden group hover:border-red-500/50 transition-colors">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity"><i class="fa-solid fa-fire text-4xl text-red-500"></i></div>
            <div class="text-gray-500 text-xs font-bold uppercase mb-2">Total Burned</div>
            <div class="text-3xl font-black text-gray-900 dark:text-white"><?= number_format($stats['burned']) ?> <span class="text-sm text-gray-400">G</span></div>
            <div class="mt-2 text-xs text-red-500 font-bold">Deflationary Asset</div>
        </div>
        <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm relative overflow-hidden group hover:border-purple-500/50 transition-colors">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity"><i class="fa-solid fa-chart-line text-4xl text-purple-500"></i></div>
            <div class="text-gray-500 text-xs font-bold uppercase mb-2">Avg Order Value</div>
            <div class="text-3xl font-black text-gray-900 dark:text-white"><?= number_format($stats['avg_order'], 1) ?> <span class="text-sm text-gray-400">G</span></div>
            <div class="mt-2 text-xs text-gray-400 font-bold"><?= number_format($stats['sellers']) ?> Active Sellers</div>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-gray-900 dark:text-white">Revenue Analytics</h3>
                <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-primary-500 animate-pulse"></span><span class="text-xs font-bold text-primary-500">Live Data</span></div>
            </div>
            <div id="revenueChart" style="min-height:300px;"></div>
        </div>
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm p-6">
            <h3 class="font-bold text-gray-900 dark:text-white mb-6">Inventory Distribution</h3>
            <div id="catChart" style="min-height:300px;"></div>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-gray-900 dark:text-white">Recent Orders</h3><a href="orders.php" class="text-xs font-bold text-primary-500 hover:underline">View All</a>
            </div>
            <div class="space-y-4">
                <?php foreach ($recent as $r): ?>
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-white/5 last:border-0 last:pb-0">
                        <div>
                            <div class="font-bold text-sm text-gray-900 dark:text-white">#<?= $r['id'] ?> - <?= $r['accountname'] ?></div>
                            <div class="text-xs text-gray-500"><?= date('M d, H:i', strtotime($r['created_at'])) ?></div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-sm text-primary-500"><?= number_format($r['total_gashy']) ?></div>
                            <div class="text-[10px] uppercase font-bold text-gray-400"><?= $r['status'] ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm p-6">
            <h3 class="font-bold text-gray-900 dark:text-white mb-6">Top Products</h3>
            <div class="space-y-4">
                <?php foreach ($top_products as $i => $tp): $img = json_decode($tp['images'])[0] ?? ''; ?>
                    <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-white/5 last:border-0 last:pb-0">
                        <img src="../<?= $img ?>" class="w-10 h-10 rounded bg-gray-100 dark:bg-white/5 object-cover">
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-sm text-gray-900 dark:text-white truncate"><?= $tp['title'] ?></div>
                            <div class="text-xs text-gray-500"><?= number_format($tp['price_gashy']) ?> G</div>
                        </div>
                        <div class="text-right">
                            <div class="text-primary-500 font-bold text-sm"><?= $tp['sold'] ?></div>
                            <div class="text-[10px] text-gray-400">Sold</div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm p-6">
            <h3 class="font-bold text-gray-900 dark:text-white mb-6">Top Sellers</h3>
            <div class="space-y-4">
                <?php foreach ($top_sellers as $i => $ts): ?>
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-white/5 last:border-0 last:pb-0">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-white/10 flex items-center justify-center font-bold text-gray-500 text-xs"><?= ($i + 1) ?></div>
                            <div class="font-bold text-sm text-gray-900 dark:text-white"><?= $ts['store_name'] ?></div>
                        </div>
                        <div class="text-right">
                            <?= $ts['account_id'] ?>
                            <div class="text-xs font-bold text-gray-900 dark:text-white"><?= sellerStats($ts['account_id'])['total_sale'] ?> Sales</div>
                            <div class="text-[10px] text-yellow-500"><i class="fa-solid fa-star"></i> <?= $ts['rating'] ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>
<script>
    var revOpts = {
        series: [{
            name: 'Volume',
            data: <?= json_encode($vols) ?>
        }],
        chart: {
            height: 300,
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
                opacityFrom: 0.4,
                opacityTo: 0.05,
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
                    return val + " G"
                }
            }
        }
    };
    var revChart = new ApexCharts(document.querySelector("#revenueChart"), revOpts);
    revChart.render();
    var catOpts = {
        series: <?= json_encode($cat_counts) ?>,
        labels: <?= json_encode($cat_labels) ?>,
        chart: {
            type: 'donut',
            height: 300,
            fontFamily: 'Inter, sans-serif',
            background: 'transparent'
        },
        colors: ['#00ffaa', '#3b82f6', '#8b5cf6', '#ef4444', '#f59e0b'],
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
    var catChart = new ApexCharts(document.querySelector("#catChart"), catOpts);
    catChart.render();
</script>
<?php require_once 'footer.php'; ?>