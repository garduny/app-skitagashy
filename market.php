<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
$cat = request('category', 'get');
$search = request('search', 'get');
$sort = request('sort', 'get') ?? 'newest';
$min = request('min', 'get');
$max = request('max', 'get');
$where = "WHERE p.status='active' AND p.stock>0";
if ($cat) {
    $where .= " AND c.slug='$cat' ";
}
if ($search) {
    $where .= " AND (p.title LIKE '%$search%' OR p.description LIKE '%$search%') ";
}
if ($min) {
    $where .= " AND p.price_gashy >= $min ";
}
if ($max) {
    $where .= " AND p.price_gashy <= $max ";
}
$order = "ORDER BY p.id DESC";
if ($sort === 'price_asc') {
    $order = "ORDER BY p.price_gashy ASC";
}
if ($sort === 'price_desc') {
    $order = "ORDER BY p.price_gashy DESC";
}
if ($sort === 'popular') {
    $order = "ORDER BY p.views DESC";
}
$products = getQuery(" SELECT p.id,p.title,p.slug,p.price_gashy,p.images,p.type,p.stock,c.name as cat_name,s.store_name,s.is_approved FROM products p JOIN categories c ON p.category_id=c.id JOIN sellers s ON p.seller_id=s.user_id $where $order LIMIT 50 ");
$cats = getQuery(" SELECT name,slug,icon,(SELECT COUNT(*) FROM products WHERE category_id=categories.id AND status='active') as count FROM categories WHERE is_active=1 ");
?>
<main class="min-h-screen pt-20 lg:pl-64 bg-gray-50 dark:bg-[#0B0E14] text-gray-800 dark:text-gray-200 relative transition-colors duration-300">
    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col lg:flex-row gap-8">

            <!-- Filters Sidebar -->
            <div class="w-full lg:w-64 flex-shrink-0 space-y-6">
                <div class="bg-white dark:bg-[#151A23] rounded-2xl border border-gray-200 dark:border-white/5 p-5 sticky top-24 shadow-lg dark:shadow-xl dark:shadow-black/20 transition-colors">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2 text-lg">
                        <span class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-600/10 text-blue-600 dark:text-blue-500 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                        </span>
                        Filters
                    </h3>

                    <!-- Categories -->
                    <div class="space-y-1 mb-8">
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-500 uppercase tracking-wider mb-3 px-1">Categories</p>
                        <a href="market.php" class="flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-all <?= !$cat ? 'bg-blue-600 text-white font-bold shadow-lg shadow-blue-500/30' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5' ?>">
                            <span>All Items</span>
                        </a>
                        <?php foreach ($cats as $c): ?>
                            <a href="market.php?category=<?= $c['slug'] ?>" class="flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-all <?= $cat === $c['slug'] ? 'bg-blue-600 text-white font-bold shadow-lg shadow-blue-500/30' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5' ?>">
                                <span class="flex items-center gap-2">
                                    <?php if ($c['icon']): ?><i class="<?= $c['icon'] ?>"></i><?php endif; ?>
                                    <?= $c['name'] ?>
                                </span>
                                <span class="text-xs opacity-50 bg-black/10 dark:bg-black/20 px-1.5 py-0.5 rounded"><?= $c['count'] ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <!-- Price Range -->
                    <form action="market.php" method="GET" class="space-y-4">
                        <?php if ($cat): ?><input type="hidden" name="category" value="<?= $cat ?>"><?php endif; ?>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-500 uppercase tracking-wider px-1">Price Range</p>
                        <div class="flex gap-2">
                            <input type="number" name="min" value="<?= $min ?>" placeholder="Min" class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-lg px-3 py-2 text-xs text-gray-900 dark:text-white focus:border-blue-500 outline-none transition-colors">
                            <input type="number" name="max" value="<?= $max ?>" placeholder="Max" class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-lg px-3 py-2 text-xs text-gray-900 dark:text-white focus:border-blue-500 outline-none transition-colors">
                        </div>
                        <button type="submit" class="w-full py-2 bg-gray-100 dark:bg-white/5 hover:bg-blue-600 dark:hover:bg-blue-600 text-gray-600 dark:text-gray-300 hover:text-white rounded-lg text-xs font-bold transition-all border border-gray-200 dark:border-white/5">Apply Filter</button>
                    </form>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8 bg-white dark:bg-[#151A23] p-4 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm dark:shadow-none transition-colors">
                    <div>
                        <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Marketplace</h1>
                        <p class="text-xs text-gray-500 mt-1">Showing <?= count($products) ?> results</p>
                    </div>
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <div class="relative group">
                            <select onchange="window.location.href='market.php?sort='+this.value+'<?= $cat ? "&category=$cat" : "" ?>'" class="appearance-none bg-gray-50 dark:bg-[#0B0E14] border border-gray-200 dark:border-white/10 text-sm text-gray-700 dark:text-gray-300 rounded-lg pl-4 pr-10 py-2.5 outline-none focus:border-blue-500 cursor-pointer min-w-[160px] transition-colors">
                                <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>Newest Arrivals</option>
                                <option value="popular" <?= $sort == 'popular' ? 'selected' : '' ?>>Most Popular</option>
                                <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                                <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Grid -->
                <?php if (empty($products)): ?>
                    <div class="flex flex-col items-center justify-center py-32 text-center bg-white dark:bg-[#151A23] rounded-3xl border border-gray-200 dark:border-white/5 border-dashed shadow-sm dark:shadow-none">
                        <div class="w-24 h-24 rounded-full bg-gray-100 dark:bg-gradient-to-tr dark:from-gray-800 dark:to-gray-700 flex items-center justify-center mb-6 shadow-inner">
                            <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">No items found</h3>
                        <p class="text-gray-500 max-w-sm mx-auto mb-6">We couldn't find any items matching your filters. Try different keywords or adjust your price range.</p>
                        <a href="market.php" class="px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-bold transition-colors shadow-lg shadow-blue-600/20">Clear Filters</a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <?php foreach ($products as $p): $img = json_decode($p['images'])[0] ?? 'assets/placeholder.png'; ?>
                            <div class="group bg-white dark:bg-[#151A23] rounded-2xl border border-gray-200 dark:border-white/5 overflow-hidden hover:border-blue-500/50 dark:hover:border-blue-500/30 transition-all duration-300 flex flex-col h-full shadow-md dark:shadow-lg dark:shadow-black/20 hover:-translate-y-1">

                                <!-- Image -->
                                <div class="aspect-[4/3] relative overflow-hidden bg-gray-100 dark:bg-black/20">
                                    <img src="<?= $img ?>" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500" loading="lazy">
                                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900/10 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                    <a href="product.php?slug=<?= $p['slug'] ?>" class="absolute inset-0 z-10"></a>

                                    <div class="absolute top-3 left-3 flex gap-2">
                                        <span class="px-2.5 py-1 bg-white/90 dark:bg-black/60 backdrop-blur-md rounded-lg text-[10px] font-bold text-gray-800 dark:text-white border border-black/5 dark:border-white/10 uppercase tracking-wide shadow-sm"><?= $p['type'] ?></span>
                                        <?php if ($p['is_approved']): ?>
                                            <span class="px-2 py-1 bg-blue-500/20 backdrop-blur-md rounded-lg text-[10px] font-bold text-blue-600 dark:text-blue-400 border border-blue-500/20 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                Verified
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <button onclick="addToCart(<?= $p['id'] ?>)" class="absolute bottom-3 right-3 z-20 translate-y-12 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300 w-10 h-10 bg-blue-600 hover:bg-blue-500 text-white rounded-xl shadow-lg flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Content -->
                                <div class="p-5 flex-1 flex flex-col">
                                    <div class="mb-1 text-[10px] text-blue-500 dark:text-blue-400 font-bold uppercase tracking-wider"><?= $p['cat_name'] ?></div>
                                    <a href="product.php?slug=<?= $p['slug'] ?>" class="block text-gray-900 dark:text-white font-bold text-lg mb-1 truncate hover:text-blue-600 dark:hover:text-blue-400 transition-colors"><?= $p['title'] ?></a>
                                    <p class="text-xs text-gray-500 dark:text-gray-500 mb-4 flex items-center gap-1">Sold by <span class="text-gray-700 dark:text-gray-400 font-medium hover:text-black dark:hover:text-white cursor-pointer"><?= $p['store_name'] ?></span></p>

                                    <div class="mt-auto pt-4 border-t border-gray-100 dark:border-white/5 flex items-center justify-between">
                                        <div>
                                            <div class="text-lg font-black text-gray-900 dark:text-white"><?= number_format($p['price_gashy'], 2) ?> <span class="text-xs text-gray-500 font-normal">GASHY</span></div>
                                            <div class="text-[10px] text-gray-400 font-medium">≈ $<?= number_format($p['price_gashy'] * 0.045, 2); ?></div>
                                        </div>
                                        <?php if ($p['stock'] < 5): ?>
                                            <span class="text-[10px] font-bold text-red-500 bg-red-100 dark:bg-red-500/10 px-2 py-1 rounded">Low Stock</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-16 flex justify-center">
                        <button class="px-8 py-3 bg-white dark:bg-[#151A23] border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:border-blue-500/50 hover:bg-gray-50 dark:hover:bg-white/5 rounded-xl transition-all text-sm font-bold shadow-sm dark:shadow-lg">Load More Products</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>
<script src="public/js/pages/market.js"></script>
<?php require_once 'footer.php'; ?>