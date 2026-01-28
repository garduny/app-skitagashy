<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
$banners = [];
try {
    $banners = getQuery(" SELECT image_path,link_url FROM banners WHERE is_active=1 ORDER BY sort_order ASC LIMIT 3");
} catch (Exception $e) {
}
$trending = [];
try {
    $trending = getQuery(" SELECT p.id,p.title,p.slug,p.price_gashy,p.images,p.type,s.store_name,s.is_approved FROM products p JOIN sellers s ON p.seller_id=s.account_id WHERE p.status='active' AND p.stock>0 ORDER BY p.views DESC LIMIT 4");
} catch (Exception $e) {
}
?>
<main class="min-h-screen pt-20 lg:pl-64 bg-gray-50 dark:bg-[#0B0E14] text-gray-800 dark:text-gray-200 relative overflow-hidden transition-colors duration-300">
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-500/10 dark:bg-blue-600/5 blur-[120px] rounded-full mix-blend-screen animate-pulse"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-purple-500/10 dark:bg-purple-600/5 blur-[120px] rounded-full mix-blend-screen animate-pulse" style="animation-delay:2s"></div>
    </div>
    <div class="relative z-10 p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 relative h-64 md:h-80 rounded-2xl overflow-hidden group shadow-xl dark:shadow-2xl shadow-blue-900/5 dark:shadow-blue-900/10 ring-1 ring-black/5 dark:ring-white/10">
                <?php if (!empty($banners)): $main = $banners[0]; ?>
                    <div class="absolute inset-0 bg-gradient-to-r from-gray-900 via-transparent to-transparent z-10"></div>
                    <img src="<?= $main['image_path'] ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    <a href="<?= $main['link_url'] ?>" class="absolute inset-0 z-20"></a>
                    <div class="absolute bottom-0 left-0 p-8 z-20 max-w-lg">
                        <h1 class="text-4xl font-black text-white mb-2 leading-tight drop-shadow-lg">Featured <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500">Collection</span></h1>
                        <a href="<?= $main['link_url'] ?>" class="px-6 py-3 bg-white text-gray-900 font-bold rounded-lg hover:bg-gray-100 transition-colors inline-flex items-center gap-2">View Now &rarr;</a>
                    </div>
                <?php else: ?>
                    <div class="absolute inset-0 bg-gradient-to-r from-gray-900/90 via-gray-900/40 to-transparent z-10"></div>
                    <img src="https://images.unsplash.com/photo-1639762681485-074b7f938ba0?q=80&w=2832" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Hero">
                    <div class="absolute bottom-0 left-0 p-8 z-20 max-w-lg">
                        <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-bold uppercase tracking-widest backdrop-blur-md border border-blue-500/30 mb-3 inline-block">New Arrival</span>
                        <h1 class="text-4xl font-black text-white mb-2 leading-tight drop-shadow-lg">Cyberpunk <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500">Collection</span></h1>
                        <p class="text-gray-200 text-sm mb-6 line-clamp-2 drop-shadow-md">Exclusive NFTs and digital assets verified on Solana. Limited edition run ending soon.</p>
                        <a href="market" class="px-6 py-3 bg-white text-gray-900 font-bold rounded-lg hover:bg-gray-100 transition-colors inline-flex items-center gap-2 shadow-lg">Explore Market &rarr;</a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="grid grid-rows-2 gap-6">
                <div class="relative rounded-2xl p-6 overflow-hidden bg-white dark:bg-gradient-to-br dark:from-[#151A23] dark:to-[#1E2532] border border-gray-200 dark:border-white/5 group hover:border-purple-500/30 transition-all shadow-lg dark:shadow-none">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Mystery Boxes</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Win rare NFTs & Tokens</p>
                    <a href="mystery-boxes" class="text-xs font-bold text-purple-600 dark:text-purple-400 hover:text-purple-500 dark:hover:text-purple-300 absolute bottom-6">Open Now &rarr;</a>
                    <div class="absolute bottom-4 right-4 text-purple-500/10 dark:text-purple-500/20"><svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                        </svg></div>
                </div>
                <div class="relative rounded-2xl p-6 overflow-hidden bg-white dark:bg-gradient-to-br dark:from-[#151A23] dark:to-[#1E2532] border border-gray-200 dark:border-white/5 group hover:border-green-500/30 transition-all shadow-lg dark:shadow-none">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Lottery Pool</h3>
                    <div class="mt-auto pt-4">
                        <span class="text-2xl font-black text-green-500 dark:text-green-400">Live</span> <span class="text-xs font-bold text-gray-500">Draws Weekly</span>
                    </div>
                    <a href="lottery" class="absolute inset-0"></a>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2"><span class="w-1.5 h-6 bg-blue-500 rounded-full"></span> Trending Now</h2>
        </div>
        <?php if (empty($trending)): ?>
            <div class="text-center py-12 bg-white dark:bg-[#151A23] rounded-xl border border-gray-200 dark:border-white/5">
                <p class="text-gray-500">No active products found. <a href="seller" class="text-blue-500 dark:text-blue-400 hover:underline">List an item?</a></p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($trending as $p): $img = json_decode($p['images'])[0] ?? 'assets/placeholder.png'; ?>
                    <a href="product?slug=<?= $p['slug'] ?>" class="group relative bg-white dark:bg-[#151A23] rounded-xl border border-gray-200 dark:border-white/5 overflow-hidden hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-500/10 transition-all duration-300">
                        <div class="aspect-square relative overflow-hidden bg-gray-100 dark:bg-black/20">
                            <img src="<?= $img ?>" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute top-2 right-2 px-2 py-1 bg-white/90 dark:bg-black/60 backdrop-blur rounded text-[10px] font-bold text-gray-800 dark:text-white border border-black/5 dark:border-white/10 uppercase"><?= $p['type'] ?></div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-gray-900 dark:text-gray-200 truncate group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors"><?= $p['title'] ?></h3>
                            <p class="text-xs text-gray-500 mb-3">by <?= $p['store_name'] ?></p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-gray-900 dark:text-white"><?= number_format($p['price_gashy'], 2) ?> GASHY</span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php require_once 'footer.php'; ?>