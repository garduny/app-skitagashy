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
$p = findQuery(" SELECT p.*,c.name as cat_name,c.slug as cat_slug,s.store_name,s.rating,s.is_approved FROM products p JOIN categories c ON p.category_id=c.id JOIN sellers s ON p.seller_id=s.account_id WHERE p.slug='$slug' AND p.status='active' ");
if (!$p) {
    echo "<div class='pt-20 text-center text-gray-500'>Product not found</div>";
    require_once 'footer.php';
    exit;
}
execute(" UPDATE products SET views=views+1 WHERE id={$p['id']} ");
$images = json_decode($p['images']) ?? [];
$mainImg = $images[0] ?? 'assets/placeholder.png';
$related = getQuery(" SELECT title,slug,price_usd,images,type FROM products WHERE category_id={$p['category_id']} AND id!={$p['id']} AND status='active' LIMIT 4 ");
$rate = toGashy();
$price_gashy = $rate > 0 ? $p['price_usd'] / $rate : 0;
?>
<style>
    @keyframes zoom-in {
        0% {
            transform: scale(1)
        }

        100% {
            transform: scale(1.1)
        }
    }

    @keyframes badge-pulse {

        0%,
        100% {
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.3)
        }

        50% {
            box-shadow: 0 0 25px rgba(59, 130, 246, 0.6)
        }
    }

    @keyframes stock-pulse {

        0%,
        100% {
            opacity: 1
        }

        50% {
            opacity: 0.6
        }
    }

    @keyframes shimmer {
        0% {
            background-position: 200% center
        }

        100% {
            background-position: -200% center
        }
    }

    .product-img {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, rgba(19, 24, 36, 0.3), rgba(26, 31, 46, 0.3));
        border: 1px solid rgba(255, 255, 255, 0.05)
    }

    .product-img:hover img {
        transform: scale(1.05)
    }

    .product-img img {
        transition: transform 0.7s cubic-bezier(0.4, 0, 0.2, 1)
    }

    .thumb-btn {
        border: 2px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease
    }

    .thumb-btn:hover,
    .thumb-btn.active {
        border-color: rgba(59, 130, 246, 0.5);
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.2)
    }

    .seller-card {
        background: linear-gradient(135deg, rgba(19, 24, 36, 0.5), rgba(26, 31, 46, 0.5));
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.3s ease
    }

    .seller-card:hover {
        border-color: rgba(59, 130, 246, 0.2);
        box-shadow: 0 8px 30px rgba(59, 130, 246, 0.1)
    }

    .price-card {
        background: linear-gradient(135deg, rgba(19, 24, 36, 0.8), rgba(26, 31, 46, 0.8));
        backdrop-filter: blur(20px);
        border: 1px solid rgba(59, 130, 246, 0.15);
        position: relative;
        overflow: hidden
    }

    .price-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6, #3b82f6);
        background-size: 200% 100%;
        animation: shimmer 3s linear infinite
    }

    .qty-input {
        background: rgba(10, 14, 26, 0.6);
        border: 2px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease
    }

    .qty-input:focus {
        border-color: rgba(59, 130, 246, 0.5);
        background: rgba(10, 14, 26, 0.9);
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.15)
    }

    .buy-btn {
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden
    }

    .buy-btn::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transform: translateX(-100%);
        transition: transform 0.6s ease
    }

    .buy-btn:hover::before {
        transform: translateX(100%)
    }

    .buy-btn:hover:not(:disabled) {
        box-shadow: 0 15px 40px rgba(59, 130, 246, 0.5);
        transform: translateY(-2px)
    }

    .related-card {
        background: linear-gradient(135deg, rgba(19, 24, 36, 0.6), rgba(26, 31, 46, 0.6));
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.3s ease
    }

    .related-card:hover {
        border-color: rgba(59, 130, 246, 0.3);
        box-shadow: 0 12px 40px rgba(59, 130, 246, 0.15);
        transform: translateY(-4px)
    }

    html:not(.dark) .product-img {
        background: linear-gradient(135deg, rgba(248, 250, 252, 0.5), rgba(241, 245, 249, 0.5));
        border: 1px solid rgba(0, 0, 0, 0.08)
    }

    html:not(.dark) .thumb-btn {
        border: 2px solid rgba(0, 0, 0, 0.1)
    }

    html:not(.dark) .thumb-btn:hover,
    .thumb-btn.active {
        border-color: rgba(59, 130, 246, 0.5)
    }

    html:not(.dark) .seller-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(248, 250, 252, 0.9));
        border: 1px solid rgba(0, 0, 0, 0.08)
    }

    html:not(.dark) .price-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(248, 250, 252, 0.95));
        border: 1px solid rgba(59, 130, 246, 0.2)
    }

    html:not(.dark) .qty-input {
        background: rgba(248, 250, 252, 0.8);
        border: 2px solid rgba(0, 0, 0, 0.1)
    }

    html:not(.dark) .qty-input:focus {
        background: rgba(255, 255, 255, 1)
    }

    html:not(.dark) .related-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(248, 250, 252, 0.95));
        border: 1px solid rgba(0, 0, 0, 0.08)
    }

    html:not(.dark) .related-card:hover {
        box-shadow: 0 12px 40px rgba(59, 130, 246, 0.12)
    }
</style>
<main class="min-h-screen pt-24 lg:pl-72 bg-gray-50 dark:bg-gradient-to-br dark:from-dark-900 dark:via-dark-800 dark:to-dark-900 text-gray-900 dark:text-white relative transition-colors duration-300">
    <div class="absolute inset-0 overflow-hidden pointer-events-none dark:block hidden">
        <div class="absolute top-1/3 right-1/4 w-[500px] h-[500px] bg-blue-500/8 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-1/3 left-1/4 w-[500px] h-[500px] bg-purple-500/8 rounded-full blur-[120px]"></div>
    </div>
    <div class="relative z-10 max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 mb-16">
            <div class="space-y-6">
                <div class="product-img aspect-square rounded-3xl overflow-hidden shadow-2xl relative group">
                    <img id="main-image" src="./<?= $mainImg ?>" class="w-full h-full object-cover" alt="<?= $p['title'] ?>">
                    <div class="absolute inset-0 bg-gradient-to-t from-dark-900/30 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="absolute top-6 left-6 px-4 py-2 bg-white/95 dark:bg-dark-900/95 backdrop-blur-xl rounded-xl text-xs font-black text-gray-900 dark:text-white border border-gray-200 dark:border-white/10 uppercase tracking-wider shadow-lg"><?= $p['type'] ?></div>
                    <?php if ($p['stock'] < 5 && $p['stock'] > 0): ?>
                        <div class="absolute top-6 right-6 px-4 py-2 bg-red-500/95 backdrop-blur-xl rounded-xl text-xs font-black text-white shadow-lg animate-pulse">Only <?= $p['stock'] ?> Left!</div>
                    <?php endif; ?>
                </div>
                <?php if (count($images) > 1): ?>
                    <div class="flex gap-4 overflow-x-auto pb-2 custom-scrollbar">
                        <?php foreach ($images as $idx => $img): ?>
                            <button onclick="document.getElementById('main-image').src='<?= $img ?>';document.querySelectorAll('.thumb-btn').forEach(b=>b.classList.remove('active'));this.classList.add('active')" class="thumb-btn <?= $idx === 0 ? 'active' : '' ?> w-24 h-24 rounded-2xl overflow-hidden flex-shrink-0 hover:scale-105 transition-transform">
                                <img src="<?= $img ?>" class="w-full h-full object-cover" alt="Thumbnail">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="flex flex-col">
                <div class="flex items-center gap-3 mb-6">
                    <a href="market.php?category=<?= $p['cat_slug'] ?>" class="px-4 py-2 bg-blue-500/10 dark:bg-blue-500/10 bg-blue-100 text-blue-600 dark:text-blue-400 text-sm font-black uppercase tracking-wider rounded-xl border border-blue-500/30 hover:bg-blue-500/20 transition-all"><?= $p['cat_name'] ?></a>
                    <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400 text-sm"><span class="font-mono">SKU: #<?= $p['id'] ?></span></div>
                </div>
                <h1 class="text-4xl md:text-5xl font-black bg-gradient-to-r from-gray-900 via-blue-900 to-gray-900 dark:from-white dark:via-blue-200 dark:to-white bg-clip-text text-transparent mb-8 leading-tight"><?= $p['title'] ?></h1>
                <div class="seller-card flex items-center gap-5 p-6 rounded-2xl mb-8 shadow-xl">
                    <div class="relative">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 flex items-center justify-center text-white font-black text-2xl shadow-lg"><?= strtoupper(substr($p['store_name'], 0, 1)) ?></div>
                        <?php if ($p['is_approved']): ?>
                            <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center border-2 border-white dark:border-dark-800 shadow-lg"></div>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2"><span class="text-lg font-black text-gray-900 dark:text-white"><?= $p['store_name'] ?></span></div>
                        <div class="text-sm font-bold text-gray-600 dark:text-gray-400"><?= $p['rating'] ?> Rating</div>
                    </div>
                </div>
                <div class="prose prose-lg max-w-none text-gray-700 dark:text-gray-300 mb-10 leading-relaxed">
                    <p><?= nl2br($p['description']) ?></p>
                </div>
                <div class="price-card mt-auto rounded-3xl p-8 shadow-2xl relative">
                    <div class="relative z-10">
                        <div class="flex items-end justify-between mb-8">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider mb-3">Current Price</p>
                                <div class="flex items-baseline gap-3 mb-2">
                                    <span class="text-5xl font-black bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400 bg-clip-text text-transparent priceText"><?= number_format($price_gashy, 2) ?></span>
                                    <span class="text-2xl font-black text-blue-500">$GASHY</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 font-mono">≈ $<?= number_format($p['price_usd'], 7) ?> USD</p>
                            </div>
                            <div class="text-right">
                                <?php if ($p['stock'] > 0): ?>
                                    <div class="flex items-center gap-2 justify-end text-green-500 dark:text-green-400 font-black text-lg mb-2">In Stock</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400 font-bold"><?= $p['stock'] ?> units available</div>
                                <?php else: ?>
                                    <div class="text-red-500 dark:text-red-400 font-black text-lg flex items-center gap-2 justify-end">Out of Stock</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <input type="number" id="qty" value="1" min="1" max="<?= $p['stock'] ?>" class="qty-input w-28 rounded-2xl text-center text-2xl font-black text-gray-900 dark:text-white focus:outline-none py-5">
                            <button onclick="buyNow(<?= $p['id'] ?>)" <?= $p['stock'] < 1 ? 'disabled' : '' ?> class="buy-btn flex-1 py-5 text-white font-black text-xl rounded-2xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-3 shadow-2xl"><?= $p['stock'] > 0 ? 'Buy Now' : 'Sold Out' ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($related): ?>
            <div class="border-t-2 border-gray-200 dark:border-white/5 pt-16 mt-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php foreach ($related as $r): $rimg = json_decode($r['images'])[0] ?? 'assets/placeholder.png';
                        $rg = $rate > 0 ? $r['price_usd'] / $rate : 0; ?>
                        <a href="product.php?slug=<?= $r['slug'] ?>" class="related-card group rounded-2xl overflow-hidden shadow-xl">
                            <div class="aspect-square relative overflow-hidden bg-gray-200 dark:bg-dark-800">
                                <img src="./<?= $rimg ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="<?= $r['title'] ?>">
                            </div>
                            <div class="p-5">
                                <h3 class="font-black text-gray-900 dark:text-white mb-2 line-clamp-1 group-hover:text-blue-500 transition-colors"><?= $r['title'] ?></h3>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-lg font-black bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400 bg-clip-text text-transparent"><?= number_format($rg, 2) ?></span>
                                    <span class="text-xs font-bold text-gray-500">GASHY</span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>
<script>
    window.GASHY_PRICE = <?= (float)$price_gashy ?>;
    window.GASHY_PRODUCT_SELLER_ID = <?= (int)$p['seller_id'] ?>;
</script>
<?php require_once 'footer.php'; ?>