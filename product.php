<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
$slug = request('slug', 'get');
if (!$slug) {
    echo "<script>window.location='market.php';</script>";
    exit;
}
$p = findQuery(" SELECT p.*,c.name as cat_name,c.slug as cat_slug,s.store_name,s.rating,s.is_approved FROM products p JOIN categories c ON p.category_id=c.id JOIN sellers s ON p.seller_id=s.user_id WHERE p.slug='$slug' AND p.status='active' ");
if (!$p) {
    echo "<div class='pt-20 text-center text-gray-500'>Product not found</div>";
    require_once 'footer.php';
    exit;
}
execute(" UPDATE products SET views=views+1 WHERE id={$p['id']} ");
$images = json_decode($p['images']) ?? [];
$mainImg = $images[0] ?? 'assets/placeholder.png';
$related = getQuery(" SELECT title,slug,price_gashy,images,type FROM products WHERE category_id={$p['category_id']} AND id!={$p['id']} AND status='active' LIMIT 4 ");
?>
<main class="min-h-screen pt-20 lg:pl-64 bg-gray-50 dark:bg-[#0B0E14] text-gray-800 dark:text-gray-200 relative transition-colors duration-300">
    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-12">
            <div class="space-y-4">
                <div class="aspect-square rounded-2xl overflow-hidden border border-gray-200 dark:border-white/5 bg-white dark:bg-[#151A23] relative group shadow-lg dark:shadow-none">
                    <img id="main-image" src="<?= $mainImg ?>" class="w-full h-full object-cover">
                    <div class="absolute top-4 left-4 px-3 py-1 bg-white/90 dark:bg-black/60 backdrop-blur rounded-full text-xs font-bold text-gray-800 dark:text-white border border-black/5 dark:border-white/10 uppercase tracking-wider"><?= $p['type'] ?></div>
                </div>
                <?php if (count($images) > 1): ?>
                    <div class="flex gap-4 overflow-x-auto pb-2">
                        <?php foreach ($images as $img): ?>
                            <button onclick="document.getElementById('main-image').src='<?= $img ?>'" class="w-20 h-20 rounded-lg border border-gray-200 dark:border-white/10 overflow-hidden flex-shrink-0 hover:border-blue-500 transition-colors">
                                <img src="<?= $img ?>" class="w-full h-full object-cover">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="flex flex-col">
                <div class="flex items-center gap-2 mb-4">
                    <a href="market.php?category=<?= $p['cat_slug'] ?>" class="text-blue-500 dark:text-blue-400 text-sm font-bold hover:underline uppercase"><?= $p['cat_name'] ?></a>
                    <span class="text-gray-400">•</span>
                    <span class="text-gray-500 dark:text-gray-400 text-sm">SKU: #<?= $p['id'] ?></span>
                </div>
                <h1 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-4 leading-tight"><?= $p['title'] ?></h1>
                <div class="flex items-center gap-4 mb-6 p-4 bg-white dark:bg-[#151A23] rounded-xl border border-gray-200 dark:border-white/5 shadow-sm">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center text-white font-bold text-lg"><?= substr($p['store_name'], 0, 1) ?></div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-gray-900 dark:text-white"><?= $p['store_name'] ?></span>
                            <?php if ($p['is_approved']): ?>
                                <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            <?php endif; ?>
                        </div>
                        <div class="text-xs text-yellow-500 flex items-center gap-1"><span>★</span> <span><?= $p['rating'] ?> Seller Rating</span></div>
                    </div>
                </div>
                <div class="prose prose-sm text-gray-600 dark:text-gray-400 mb-8 max-w-none">
                    <p><?= nl2br($p['description']) ?></p>
                </div>
                <div class="mt-auto bg-white dark:bg-[#151A23] rounded-2xl p-6 border border-gray-200 dark:border-white/5 shadow-lg dark:shadow-none">
                    <div class="flex items-end justify-between mb-6">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Current Price</p>
                            <div class="text-3xl font-black text-gray-900 dark:text-white"><?= number_format($p['price_gashy'], 2) ?> <span class="text-lg text-blue-500">GASHY</span></div>
                            <p class="text-xs text-gray-500">≈ $<?= number_format($p['price_gashy'] * 0.045, 2) ?> USD</p>
                        </div>
                        <div class="text-right">
                            <?php if ($p['stock'] > 0): ?>
                                <div class="text-green-500 dark:text-green-400 font-bold flex items-center gap-1 justify-end"><span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> In Stock</div>
                                <div class="text-xs text-gray-500"><?= $p['stock'] ?> units available</div>
                            <?php else: ?>
                                <div class="text-red-500 font-bold">Out of Stock</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <input type="number" id="qty" value="1" min="1" max="<?= $p['stock'] ?>" class="w-20 bg-gray-100 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl text-center text-gray-900 dark:text-white font-bold focus:border-blue-500 outline-none text-lg">
                        <button onclick="buyNow(<?= $p['id'] ?>)" <?= $p['stock'] < 1 ? 'disabled' : '' ?> class="flex-1 py-4 bg-blue-600 hover:bg-blue-500 disabled:bg-gray-300 dark:disabled:bg-gray-700 disabled:cursor-not-allowed text-white font-bold text-lg rounded-xl shadow-lg shadow-blue-600/20 transition-all">
                            <?= $p['stock'] > 0 ? 'Buy Now' : 'Sold Out' ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($related): ?>
            <div class="border-t border-gray-200 dark:border-white/5 pt-12">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Related Items</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php foreach ($related as $r): $rimg = json_decode($r['images'])[0] ?? 'assets/placeholder.png'; ?>
                        <a href="product.php?slug=<?= $r['slug'] ?>" class="group bg-white dark:bg-[#151A23] rounded-xl border border-gray-200 dark:border-white/5 overflow-hidden hover:border-blue-500/30 transition-all shadow-md dark:shadow-none">
                            <div class="aspect-square relative overflow-hidden">
                                <img src="<?= $rimg ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-gray-900 dark:text-white truncate mb-1 group-hover:text-blue-500 transition-colors"><?= $r['title'] ?></h3>
                                <p class="text-sm font-bold text-gray-500 dark:text-gray-400"><?= number_format($r['price_gashy'], 2) ?> GASHY</p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>
<script src="public/js/pages/product.js"></script>
<?php require_once 'footer.php'; ?>