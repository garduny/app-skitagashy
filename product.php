<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) require_once 'server/init.php';
require_once 'header.php';
require_once 'sidebar.php';
$slug = request('slug', 'get');
if (!$slug) {
    echo "<script>location='market.php'</script>";
    exit;
}
$p = findQuery(" SELECT p.*,c.name cat_name,c.slug cat_slug,s.store_name,s.rating,s.is_approved FROM products p LEFT JOIN categories c ON c.id=p.category_id LEFT JOIN sellers s ON s.account_id=p.seller_id WHERE p.slug='$slug' AND p.status='active' LIMIT 1 ");
if (!$p) {
    echo "<main class='min-h-screen pt-24 lg:pl-72 flex items-center justify-center px-4'><div class='text-center'><div class='text-4xl font-black mb-4'>Product Not Found</div><a href='market.php' class='px-6 py-3 rounded-2xl bg-primary-500 text-black font-black'>Back To Market</a></div></main>";
    require_once 'footer.php';
    exit;
}
execute(" UPDATE products SET views=views+1 WHERE id={$p['id']} ");
$images = json_decode($p['images'], true) ?: [];
$images = array_values(array_filter($images));
if (!$images) $images = ['assets/placeholder.png'];
$attr = json_decode($p['attributes'], true) ?: [];
$rate = (float)toGashy();
$price_gashy = $rate > 0 ? ((float)$p['price_usd'] / $rate) : 0;
$reserved = (int)(findQuery(" SELECT COALESCE(SUM(oi.quantity),0) q FROM order_items oi INNER JOIN orders o ON o.id=oi.order_id WHERE oi.product_id={$p['id']} AND o.status IN('pending','processing') ")['q'] ?? 0);
$stock = max(0, (int)$p['stock'] - $reserved);
$related = getQuery(" SELECT id,title,slug,price_usd,images,type FROM products WHERE category_id={$p['category_id']} AND id!={$p['id']} AND status='active' ORDER BY id DESC LIMIT 4 ");
$sold = (int)countQuery(" SELECT 1 FROM order_items WHERE product_id={$p['id']} ");
$views = (int)$p['views'];
?>
<style>
    @keyframes luxuryGlow {
        0% {
            background-position: 0% 50%
        }

        100% {
            background-position: 200% 50%
        }
    }

    @keyframes softPulse {

        0%,
        100% {
            opacity: 1;
            transform: translateY(0)
        }

        50% {
            opacity: .72;
            transform: translateY(-2px)
        }
    }

    @keyframes cardFloat {

        0%,
        100% {
            transform: translateY(0)
        }

        50% {
            transform: translateY(-4px)
        }
    }

    .product-page-bg {
        position: relative
    }

    .product-page-bg:before {
        content: '';
        position: absolute;
        top: 6%;
        right: 6%;
        width: 480px;
        height: 480px;
        border-radius: 9999px;
        background: rgba(59, 130, 246, .08);
        filter: blur(120px);
        pointer-events: none
    }

    .product-page-bg:after {
        content: '';
        position: absolute;
        bottom: 6%;
        left: 4%;
        width: 460px;
        height: 460px;
        border-radius: 9999px;
        background: rgba(139, 92, 246, .08);
        filter: blur(120px);
        pointer-events: none
    }

    .lux-card {
        background: linear-gradient(135deg, rgba(15, 23, 42, .82), rgba(17, 24, 39, .92));
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, .06);
        box-shadow: 0 18px 60px rgba(2, 6, 23, .28)
    }

    .lux-line {
        position: relative;
        overflow: hidden
    }

    .lux-line:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #00ffaa, #3b82f6, #8b5cf6, #00ffaa);
        background-size: 200% 100%;
        animation: luxuryGlow 4s linear infinite
    }

    .hero-img {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, rgba(15, 23, 42, .78), rgba(30, 41, 59, .82))
    }

    .hero-img img {
        transition: transform .8s cubic-bezier(.22, 1, .36, 1)
    }

    .hero-img:hover img {
        transform: scale(1.06)
    }

    .hero-img:after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(2, 6, 23, .34), transparent 40%, transparent);
        pointer-events: none
    }

    .thumb {
        border: 2px solid rgba(255, 255, 255, .08);
        transition: all .28s ease;
        background: rgba(255, 255, 255, .03)
    }

    .thumb:hover,
    .thumb.active {
        border-color: #00ffaa;
        box-shadow: 0 0 20px rgba(0, 255, 170, .22);
        transform: translateY(-2px)
    }

    .seller-card {
        background: linear-gradient(135deg, rgba(15, 23, 42, .72), rgba(30, 41, 59, .78));
        border: 1px solid rgba(255, 255, 255, .06);
        backdrop-filter: blur(12px);
        transition: all .28s ease
    }

    .seller-card:hover {
        border-color: rgba(0, 255, 170, .22);
        box-shadow: 0 12px 40px rgba(0, 255, 170, .08)
    }

    .metric-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, .02));
        border: 1px solid rgba(255, 255, 255, .06)
    }

    .desc-card {
        background: linear-gradient(135deg, rgba(15, 23, 42, .74), rgba(17, 24, 39, .88));
        border: 1px solid rgba(255, 255, 255, .06)
    }

    .price-card {
        background: linear-gradient(135deg, rgba(15, 23, 42, .95), rgba(17, 24, 39, .95));
        backdrop-filter: blur(18px);
        border: 1px solid rgba(59, 130, 246, .16);
        position: relative;
        overflow: hidden;
        box-shadow: 0 22px 70px rgba(2, 6, 23, .34)
    }

    .price-card:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6, #00ffaa, #3b82f6);
        background-size: 220% 100%;
        animation: luxuryGlow 4s linear infinite
    }

    .price-chip {
        background: rgba(255, 255, 255, .05);
        border: 1px solid rgba(255, 255, 255, .08)
    }

    .stock-badge {
        animation: softPulse 1.7s ease-in-out infinite
    }

    .attr-box {
        background: rgba(255, 255, 255, .04);
        border: 1px solid rgba(255, 255, 255, .08)
    }

    .attr-select {
        appearance: none;
        background: rgba(2, 6, 23, .44);
        border: 2px solid rgba(255, 255, 255, .08);
        transition: all .28s ease
    }

    .attr-select:focus {
        outline: none;
        border-color: rgba(0, 255, 170, .44);
        box-shadow: 0 0 0 4px rgba(0, 255, 170, .08)
    }

    .qty-input {
        background: rgba(2, 6, 23, .44);
        border: 2px solid rgba(255, 255, 255, .08);
        transition: all .28s ease
    }

    .qty-input:focus {
        outline: none;
        border-color: rgba(59, 130, 246, .46);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, .08)
    }

    .buybtn {
        background: linear-gradient(135deg, #00ffaa, #3b82f6);
        color: #000;
        box-shadow: 0 12px 30px rgba(0, 255, 170, .22);
        transition: all .28s ease;
        position: relative;
        overflow: hidden
    }

    .buybtn:before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .22), transparent);
        transform: translateX(-100%);
        transition: transform .7s ease
    }

    .buybtn:hover:not(:disabled):before {
        transform: translateX(100%)
    }

    .buybtn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 18px 40px rgba(0, 255, 170, .3)
    }

    .sharebtn {
        background: rgba(255, 255, 255, .05);
        border: 1px solid rgba(255, 255, 255, .08);
        transition: all .28s ease
    }

    .sharebtn:hover {
        background: rgba(255, 255, 255, .09);
        transform: translateY(-2px)
    }

    .trust-pill {
        background: rgba(255, 255, 255, .04);
        border: 1px solid rgba(255, 255, 255, .06)
    }

    .related-card {
        background: linear-gradient(135deg, rgba(15, 23, 42, .82), rgba(17, 24, 39, .9));
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, .06);
        transition: all .3s ease
    }

    .related-card:hover {
        transform: translateY(-4px);
        border-color: rgba(0, 255, 170, .22);
        box-shadow: 0 18px 50px rgba(0, 255, 170, .08)
    }

    .related-card img {
        transition: transform .7s cubic-bezier(.22, 1, .36, 1)
    }

    .related-card:hover img {
        transform: scale(1.06)
    }

    .light-title {
        background: linear-gradient(135deg, #111827, #1d4ed8, #111827);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent
    }

    html.dark .light-title {
        background: linear-gradient(135deg, #fff, #bfdbfe, #fff);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent
    }

    html:not(.dark) .product-page-bg:before {
        background: rgba(0, 163, 114, .08)
    }

    html:not(.dark) .product-page-bg:after {
        background: rgba(59, 130, 246, .08)
    }

    html:not(.dark) .lux-card,
    html:not(.dark) .seller-card,
    html:not(.dark) .desc-card,
    html:not(.dark) .price-card,
    html:not(.dark) .related-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, .96), rgba(248, 250, 252, .96));
        border: 1px solid rgba(0, 0, 0, .08);
        box-shadow: 0 18px 50px rgba(15, 23, 42, .08)
    }

    html:not(.dark) .hero-img {
        background: linear-gradient(135deg, rgba(255, 255, 255, .96), rgba(241, 245, 249, .96))
    }

    html:not(.dark) .hero-img:after {
        background: linear-gradient(to top, rgba(241, 245, 249, .18), transparent 40%, transparent)
    }

    html:not(.dark) .thumb {
        border-color: rgba(0, 0, 0, .08);
        background: rgba(255, 255, 255, .9)
    }

    html:not(.dark) .metric-card,
    html:not(.dark) .price-chip,
    html:not(.dark) .attr-box,
    html:not(.dark) .trust-pill,
    html:not(.dark) .sharebtn {
        background: rgba(255, 255, 255, .8);
        border-color: rgba(0, 0, 0, .08)
    }

    html:not(.dark) .attr-select,
    html:not(.dark) .qty-input {
        background: #fff;
        border-color: rgba(0, 0, 0, .12);
        color: #0f172a
    }
</style>

<main class="product-page-bg min-h-screen pt-24 lg:pl-72 bg-gray-50 dark:bg-gradient-to-br dark:from-dark-900 dark:via-dark-800 dark:to-dark-900 text-gray-900 dark:text-white transition-colors duration-300">
    <div class="relative z-10 max-w-[1850px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">

            <div>
                <div class="hero-img lux-line lux-card rounded-3xl overflow-hidden shadow-2xl aspect-square">
                    <img id="main-image" src="./<?= htmlspecialchars($images[0]) ?>" class="w-full h-full object-cover" alt="<?= htmlspecialchars($p['title']) ?>">
                    <div class="absolute top-5 left-5 px-4 py-2 rounded-xl bg-white/95 dark:bg-dark-900/95 text-gray-900 dark:text-white text-xs font-black uppercase tracking-wider shadow-lg"><?= htmlspecialchars($p['type']) ?></div>
                    <?php if ($stock > 0 && $stock < 5): ?>
                        <div class="stock-badge absolute top-5 right-5 px-4 py-2 rounded-xl bg-red-500 text-white text-xs font-black shadow-lg">Only <?= $stock ?> Left</div>
                    <?php endif; ?>
                </div>

                <?php if (count($images) > 1): ?>
                    <div class="flex gap-3 overflow-x-auto mt-4 pb-2">
                        <?php foreach ($images as $k => $img): ?>
                            <button onclick="setMainImg('<?= htmlspecialchars($img, ENT_QUOTES) ?>',this)" class="thumb <?= $k === 0 ? 'active' : '' ?> w-20 h-20 rounded-2xl overflow-hidden flex-shrink-0">
                                <img src="./<?= htmlspecialchars($img) ?>" class="w-full h-full object-cover">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="flex flex-col">
                <div class="flex flex-wrap items-center gap-3 mb-5">
                    <a href="market.php?category=<?= urlencode($p['cat_slug']) ?>" class="px-4 py-2 rounded-xl bg-primary-500/10 text-primary-500 font-black text-xs uppercase tracking-wider"><?= htmlspecialchars($p['cat_name']) ?></a>
                    <span class="px-4 py-2 rounded-xl bg-black/5 dark:bg-white/5 text-xs font-black uppercase">SKU #<?= $p['id'] ?></span>
                    <span class="px-4 py-2 rounded-xl bg-black/5 dark:bg-white/5 text-xs font-black uppercase"><?= htmlspecialchars($p['type']) ?></span>
                </div>

                <h1 class="light-title text-4xl md:text-5xl font-black leading-tight mb-6"><?= htmlspecialchars($p['title']) ?></h1>

                <div class="grid grid-cols-3 gap-3 mb-6">
                    <div class="metric-card rounded-2xl p-4 text-center">
                        <div class="text-xs text-gray-500 mb-1 font-bold uppercase tracking-wider">Views</div>
                        <div class="font-black text-xl"><?= $views ?></div>
                    </div>
                    <div class="metric-card rounded-2xl p-4 text-center">
                        <div class="text-xs text-gray-500 mb-1 font-bold uppercase tracking-wider">Sold</div>
                        <div class="font-black text-xl"><?= $sold ?></div>
                    </div>
                    <div class="metric-card rounded-2xl p-4 text-center">
                        <div class="text-xs text-gray-500 mb-1 font-bold uppercase tracking-wider">Rating</div>
                        <div class="font-black text-xl"><?= number_format((float)$p['rating'], 1) ?></div>
                    </div>
                </div>

                <div class="seller-card rounded-3xl p-5 mb-6 shadow-xl">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-500 via-blue-500 to-purple-500 text-black flex items-center justify-center text-2xl font-black shadow-lg"><?= strtoupper(substr($p['store_name'], 0, 1)) ?></div>
                        <div class="flex-1">
                            <div class="font-black text-lg"><?= htmlspecialchars($p['store_name']) ?></div>
                            <div class="text-sm text-gray-500"><?= $p['is_approved'] ? 'Verified Seller' : 'Seller' ?> • ★ <?= number_format((float)$p['rating'], 2) ?></div>
                        </div>
                        <a href="seller.php?id=<?= $p['seller_id'] ?>" class="px-4 py-2 rounded-xl bg-primary-500 text-black font-black text-sm">Visit Store</a>
                    </div>
                </div>

                <div class="desc-card rounded-3xl p-6 mb-6 shadow-xl">
                    <div class="text-xs text-gray-500 uppercase tracking-wider font-black mb-3">Description</div>
                    <div class="leading-7 text-gray-700 dark:text-gray-300"><?= nl2br(htmlspecialchars($p['description'])) ?></div>
                </div>

                <div class="price-card rounded-3xl p-6 mt-auto shadow-2xl sticky top-24">
                    <div class="relative z-10">
                        <div class="flex flex-wrap items-end justify-between gap-4 mb-6">
                            <div>
                                <div class="text-xs text-gray-500 uppercase tracking-wider font-black mb-2">Current Price</div>
                                <div class="text-5xl font-black text-primary-500"><?= number_format($price_gashy, 2) ?> G</div>
                                <div class="text-sm text-gray-500 mt-1">$GASHY • ≈ $<?= number_format((float)$p['price_usd'], 6) ?> USD</div>
                            </div>
                            <div class="text-right">
                                <?php if ($stock > 0): ?>
                                    <div class="text-green-500 font-black text-lg">In Stock</div>
                                    <div class="text-sm text-gray-500"><?= $stock ?> Available</div>
                                <?php else: ?>
                                    <div class="text-red-500 font-black text-lg">Sold Out</div>
                                <?php endif; ?>
                                <?php if ($reserved > 0): ?>
                                    <div class="text-xs text-amber-500 mt-1 font-bold"><?= $reserved ?> Reserved</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($attr): ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                                <?php foreach ($attr as $key => $vals):
                                    $vals = is_array($vals) ? array_values(array_filter($vals)) : [$vals];
                                    if (!$vals) continue;
                                    $required = count($vals) > 1 ? 1 : 0;
                                ?>
                                    <div class="attr-box rounded-2xl p-3">
                                        <div class="text-[11px] font-extrabold uppercase tracking-[.12em] text-gray-500 mb-2"><?= htmlspecialchars(str_replace('_', ' ', $key)) ?></div>
                                        <div class="relative">
                                            <select class="attr-select w-full rounded-2xl px-4 py-4 pr-10 font-bold" data-attribute-key="<?= htmlspecialchars($key) ?>" data-attribute-required="<?= $required ?>">
                                                <?php foreach ($vals as $v): ?>
                                                    <option value="<?= htmlspecialchars($v) ?>"><?= htmlspecialchars($v) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">▾</div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="flex gap-3">
                            <input id="qty" type="number" min="1" max="<?= $stock ?>" value="<?= $stock > 0 ? 1 : 0 ?>" class="qty-input w-24 rounded-2xl px-4 py-4 text-center font-black text-xl">
                            <button onclick="buyNow(<?= $p['id'] ?>)" <?= $stock < 1 ? 'disabled' : '' ?> class="buybtn flex-1 rounded-2xl py-4 font-black text-lg disabled:opacity-50 disabled:cursor-not-allowed"><?= $stock > 0 ? 'Buy Now' : 'Sold Out' ?></button>
                            <button onclick="shareProduct()" class="sharebtn rounded-2xl px-5 py-4 font-black">Share</button>
                        </div>

                        <div class="grid grid-cols-3 gap-3 mt-4">
                            <div class="trust-pill rounded-2xl p-3 text-center text-xs font-black">Secure Checkout</div>
                            <div class="trust-pill rounded-2xl p-3 text-center text-xs font-black">Instant Delivery</div>
                            <div class="trust-pill rounded-2xl p-3 text-center text-xs font-black">Blockchain Safe</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <?php if ($related): ?>
            <div class="mt-16">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-black">Related Products</h2>
                    <a href="market.php?category=<?= urlencode($p['cat_slug']) ?>" class="text-primary-500 font-black">View More</a>
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
                    <?php foreach ($related as $r):
                        $ri = json_decode($r['images'], true) ?: ['assets/placeholder.png'];
                        $ri = array_values(array_filter($ri));
                        if (!$ri) $ri = ['assets/placeholder.png'];
                        $rg = $rate > 0 ? (float)$r['price_usd'] / $rate : 0;
                    ?>
                        <a href="product.php?slug=<?= urlencode($r['slug']) ?>" class="related-card rounded-3xl overflow-hidden shadow-xl">
                            <div class="aspect-square overflow-hidden bg-black/5 dark:bg-white/5">
                                <img src="./<?= htmlspecialchars($ri[0]) ?>" class="w-full h-full object-cover">
                            </div>
                            <div class="p-4">
                                <div class="font-black line-clamp-1 mb-2"><?= htmlspecialchars($r['title']) ?></div>
                                <div class="text-primary-500 font-black text-lg"><?= number_format($rg, 2) ?> G</div>
                                <div class="text-xs text-gray-500">$<?= number_format((float)$r['price_usd'], 2) ?></div>
                                <?php if (!empty($r['type'])): ?><div class="text-[10px] text-gray-400 uppercase tracking-wider mt-1"><?= htmlspecialchars($r['type']) ?></div><?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</main>

<script>
    window.GASHY_PRICE = <?= json_encode((float)$price_gashy) ?>;
    window.GASHY_PRODUCT_SELLER_ID = <?= json_encode((int)$p['seller_id']) ?>;

    function setMainImg(src, el) {
        document.getElementById('main-image').src = './' + src.replace('./', '');
        document.querySelectorAll('.thumb').forEach(x => x.classList.remove('active'));
        el.classList.add('active');
    }

    function shareProduct() {
        if (navigator.share) {
            navigator.share({
                title: document.title,
                url: location.href
            }).catch(() => {});
        } else if (navigator.clipboard) {
            navigator.clipboard.writeText(location.href);
            if (window.notyf) notyf.success('Link copied');
        }
    }
</script>
<?php require_once 'footer.php'; ?>