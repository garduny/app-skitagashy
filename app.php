<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
$banners = [];
try {
    $banners = getQuery("SELECT image_path,link_url FROM banners WHERE is_active=1 ORDER BY sort_order ASC LIMIT 5");
} catch (Exception $e) {
}
$flash_deals = getQuery("SELECT p.id,p.title,p.slug,p.price_gashy,p.images,p.type FROM products p WHERE p.status='active' AND p.stock>0 ORDER BY RAND() LIMIT 4");
$top_sellers = getQuery("SELECT store_name,total_sales,rating FROM sellers WHERE is_approved=1 ORDER BY total_sales DESC LIMIT 5");
$new_arrivals = getQuery("SELECT p.id,p.title,p.slug,p.price_gashy,p.images,p.type FROM products p WHERE p.status='active' ORDER BY p.created_at DESC LIMIT 8");
?>
<!-- 1. Removed padding (p-4...) from Main -->
<main class="ml-0 lg:ml-64 pt-20 min-h-screen relative overflow-hidden transition-all duration-300">

    <!-- 2. Added Container Wrapper (Constraints Width & Centers) -->
    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">

        <!-- Ticker -->
        <div class="mb-6 overflow-hidden whitespace-nowrap bg-white dark:bg-white/5 py-2 rounded-lg border border-gray-200 dark:border-white/5 flex items-center gap-8 text-xs font-mono text-gray-500">
            <div class="animate-marquee inline-flex gap-8">
                <span class="text-green-500 font-bold">GASHY $0.045 ▲ 5.2%</span>
                <span class="text-blue-500 font-bold">SOL $145.20 ▲ 2.1%</span>
                <span class="text-purple-500 font-bold">BTC $68,420 ▲ 1.8%</span>
                <span class="text-gray-400">ETH $3,850 ▼ 0.5%</span>
                <span class="text-yellow-500 font-bold">BNB $2,400 ▲ 3.0%</span>
                <span class="text-green-500 font-bold">GASHY $0.045 ▲ 5.2%</span>
            </div>
        </div>

        <!-- Hero Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
            <div class="lg:col-span-3 relative h-64 md:h-96 rounded-2xl overflow-hidden group shadow-xl dark:shadow-none">
                <?php if (!empty($banners)): $main = $banners[0]; ?>
                    <img src="<?= $main['image_path'] ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-8 z-20 max-w-lg">
                        <span class="px-3 py-1 rounded-full bg-blue-600 text-white text-xs font-bold uppercase tracking-widest mb-3 inline-block shadow-lg shadow-blue-600/30">Featured</span>
                        <h1 class="text-4xl md:text-5xl font-black text-white mb-2 leading-tight drop-shadow-lg">Gashy <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-green-400">Marketplace</span></h1>
                        <a href="<?= $main['link_url'] ?>" class="px-6 py-3 bg-white text-gray-900 font-bold rounded-xl hover:bg-gray-100 transition-colors inline-flex items-center gap-2 mt-4">Explore Now <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                <?php else: ?>
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-900 to-purple-900"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <h1 class="text-4xl font-black text-white">Welcome to Gashy Bazaar</h1>
                    </div>
                <?php endif; ?>
            </div>
            <div class="lg:col-span-1 space-y-6">
                <div class="relative h-full rounded-2xl p-6 bg-gradient-to-br from-purple-600 to-blue-600 overflow-hidden text-white flex flex-col justify-between shadow-lg">
                    <div class="absolute top-0 right-0 p-4 opacity-20"><i class="fa-solid fa-box-open text-6xl"></i></div>
                    <div>
                        <h3 class="text-xl font-bold mb-1">Mystery Box</h3>
                        <p class="text-xs opacity-80">Win up to 50,000 GASHY</p>
                    </div>
                    <a href="mystery-boxes.php" class="w-full py-3 bg-white/20 hover:bg-white/30 backdrop-blur rounded-xl text-center font-bold text-sm transition-all">Open Now</a>
                </div>
                <div class="relative h-full rounded-2xl p-6 bg-gradient-to-br from-green-500 to-teal-500 overflow-hidden text-white flex flex-col justify-between shadow-lg">
                    <div class="absolute top-0 right-0 p-4 opacity-20"><i class="fa-solid fa-ticket text-6xl"></i></div>
                    <div>
                        <h3 class="text-xl font-bold mb-1">Lottery Pool</h3>
                        <p class="text-xs opacity-80">Round #42 is Live</p>
                    </div>
                    <a href="lottery.php" class="w-full py-3 bg-white/20 hover:bg-white/30 backdrop-blur rounded-xl text-center font-bold text-sm transition-all">Buy Ticket</a>
                </div>
            </div>
        </div>

        <!-- Categories -->
        <div class="mb-12">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2"><i class="fa-solid fa-layer-group text-blue-500"></i> Categories</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <a href="market.php?category=gift-cards" class="p-4 bg-white dark:bg-white/5 border border-gray-200 dark:border-white/5 rounded-xl hover:border-blue-500 transition-all text-center group">
                    <div class="w-12 h-12 mx-auto rounded-full bg-blue-500/10 text-blue-500 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform"><i class="fa-solid fa-gift text-xl"></i></div><span class="text-sm font-bold text-gray-700 dark:text-gray-300">Gift Cards</span>
                </a>
                <a href="market.php?category=gaming" class="p-4 bg-white dark:bg-white/5 border border-gray-200 dark:border-white/5 rounded-xl hover:border-purple-500 transition-all text-center group">
                    <div class="w-12 h-12 mx-auto rounded-full bg-purple-500/10 text-purple-500 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform"><i class="fa-solid fa-gamepad text-xl"></i></div><span class="text-sm font-bold text-gray-700 dark:text-gray-300">Gaming</span>
                </a>
                <a href="market.php?category=software" class="p-4 bg-white dark:bg-white/5 border border-gray-200 dark:border-white/5 rounded-xl hover:border-green-500 transition-all text-center group">
                    <div class="w-12 h-12 mx-auto rounded-full bg-green-500/10 text-green-500 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform"><i class="fa-brands fa-windows text-xl"></i></div><span class="text-sm font-bold text-gray-700 dark:text-gray-300">Software</span>
                </a>
                <a href="market.php?category=nfts" class="p-4 bg-white dark:bg-white/5 border border-gray-200 dark:border-white/5 rounded-xl hover:border-pink-500 transition-all text-center group">
                    <div class="w-12 h-12 mx-auto rounded-full bg-pink-500/10 text-pink-500 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform"><i class="fa-solid fa-gem text-xl"></i></div><span class="text-sm font-bold text-gray-700 dark:text-gray-300">NFTs</span>
                </a>
                <a href="market.php?category=physical" class="p-4 bg-white dark:bg-white/5 border border-gray-200 dark:border-white/5 rounded-xl hover:border-yellow-500 transition-all text-center group">
                    <div class="w-12 h-12 mx-auto rounded-full bg-yellow-500/10 text-yellow-500 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform"><i class="fa-solid fa-shirt text-xl"></i></div><span class="text-sm font-bold text-gray-700 dark:text-gray-300">Merch</span>
                </a>
                <a href="seller.php" class="p-4 bg-white dark:bg-white/5 border border-gray-200 dark:border-white/5 rounded-xl hover:border-gray-500 transition-all text-center group">
                    <div class="w-12 h-12 mx-auto rounded-full bg-gray-500/10 text-gray-500 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform"><i class="fa-solid fa-store text-xl"></i></div><span class="text-sm font-bold text-gray-700 dark:text-gray-300">Sell Now</span>
                </a>
            </div>
        </div>

        <!-- Flash Deals -->
        <div class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2"><i class="fa-solid fa-bolt text-yellow-500"></i> Flash Deals</h2>
                <div class="flex gap-2 text-xs font-mono bg-red-500/10 text-red-500 px-3 py-1 rounded font-bold">Ends in: 04:22:19</div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($flash_deals as $p): $img = json_decode($p['images'])[0] ?? 'assets/placeholder.png'; ?>
                    <a href="product.php?slug=<?= $p['slug'] ?>" class="group bg-white dark:bg-[#151A23] rounded-xl border border-gray-200 dark:border-white/5 overflow-hidden hover:-translate-y-1 transition-all shadow-md dark:shadow-none">
                        <div class="aspect-square relative bg-gray-100 dark:bg-black/20"><img src="<?= $img ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute top-2 right-2 px-2 py-1 bg-red-500 text-white text-[10px] font-bold rounded shadow">-20%</div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-gray-900 dark:text-white truncate mb-1"><?= $p['title'] ?></h3>
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-bold text-primary-500"><?= number_format($p['price_gashy']) ?> G</div><span class="text-xs text-gray-400 line-through"><?= number_format($p['price_gashy'] * 1.2) ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 2 Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">New Arrivals</h2>
                <div class="space-y-4">
                    <?php foreach ($new_arrivals as $p): $img = json_decode($p['images'])[0] ?? 'assets/placeholder.png'; ?>
                        <a href="product.php?slug=<?= $p['slug'] ?>" class="flex items-center gap-4 p-4 bg-white dark:bg-[#151A23] rounded-xl border border-gray-200 dark:border-white/5 hover:border-blue-500 transition-all">
                            <div class="w-20 h-20 rounded-lg bg-gray-100 dark:bg-white/5 overflow-hidden flex-shrink-0"><img src="<?= $img ?>" class="w-full h-full object-cover"></div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-gray-900 dark:text-white truncate"><?= $p['title'] ?></h3>
                                <div class="text-xs text-gray-500 uppercase mt-1"><?= $p['type'] ?></div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-primary-500"><?= number_format($p['price_gashy']) ?> G</div><button class="mt-2 text-xs bg-gray-100 dark:bg-white/10 px-3 py-1 rounded hover:bg-primary-500 hover:text-white transition-colors">Buy</button>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="lg:col-span-1">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Top Sellers</h2>
                <div class="bg-white dark:bg-[#151A23] rounded-2xl border border-gray-200 dark:border-white/5 p-6 space-y-6">
                    <?php foreach ($top_sellers as $i => $s): ?>
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-white/10 flex items-center justify-center font-bold text-gray-500 text-sm"><?= ($i + 1) ?></div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm"><?= $s['store_name'] ?></h4>
                                <div class="text-xs text-yellow-500"><i class="fa-solid fa-star"></i> <?= $s['rating'] ?></div>
                            </div>
                            <div class="text-xs font-bold text-gray-400"><?= $s['total_sales'] ?> Sold</div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-8 p-6 rounded-2xl bg-gradient-to-br from-blue-900 to-dark-900 border border-blue-500/30 text-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-[url('assets/pattern.svg')] opacity-10"></div>
                    <div class="relative z-10">
                        <h3 class="font-bold text-white mb-2">Become a Seller</h3>
                        <p class="text-xs text-gray-400 mb-4">Start your own crypto store today.</p>
                        <a href="seller.php" class="px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-lg shadow-lg transition-all">Apply Now</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>
<style>
    .animate-marquee {
        animation: marquee 20s linear infinite
    }

    @keyframes marquee {
        0% {
            transform: translateX(0)
        }

        100% {
            transform: translateX(-50%)
        }
    }
</style>
<?php require_once 'footer.php'; ?>