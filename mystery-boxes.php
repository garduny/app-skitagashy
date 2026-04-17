<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) require_once 'server/init.php';
require_once 'header.php';
require_once 'sidebar.php';
$rate = (float)toGashy();
$boxes = getQuery(" SELECT p.id,p.title,p.slug,p.price_usd,p.images,p.description,p.stock,p.views FROM products p WHERE p.type='mystery_box' AND p.status='active' AND p.stock>0 ORDER BY p.price_usd ASC,p.id DESC ");
foreach ($boxes as &$b) {
    $b['price_usd'] = (float)$b['price_usd'];
    $b['price_gashy'] = $rate > 0 ? ($b['price_usd'] / $rate) : 0;
    $bid = (int)$b['id'];
    $b['loot_count'] = countQuery(" SELECT COUNT(1) FROM mystery_box_loot WHERE box_product_id=$bid AND is_active=1 ");
}
?>
<style>
    @keyframes float {

        0%,
        100% {
            transform: translateY(0) rotate(0)
        }

        50% {
            transform: translateY(-18px) rotate(2deg)
        }
    }

    @keyframes pulse-glow {

        0%,
        100% {
            box-shadow: 0 0 30px rgba(139, 92, 246, .28), 0 0 60px rgba(236, 72, 153, .15)
        }

        50% {
            box-shadow: 0 0 55px rgba(139, 92, 246, .45), 0 0 110px rgba(236, 72, 153, .22)
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

    @keyframes spin-slow {
        from {
            transform: rotate(0)
        }

        to {
            transform: rotate(360deg)
        }
    }

    @keyframes gradient-shift {

        0%,
        100% {
            background-position: 0 50%
        }

        50% {
            background-position: 100% 50%
        }
    }

    .animate-float {
        animation: float 6s ease-in-out infinite
    }

    .box-card {
        background: linear-gradient(135deg, rgba(19, 24, 36, .82), rgba(26, 31, 46, .82));
        backdrop-filter: blur(20px);
        border: 1px solid rgba(139, 92, 246, .16);
        transition: all .45s cubic-bezier(.4, 0, .2, 1);
        position: relative;
        overflow: hidden
    }

    .box-card:before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: conic-gradient(from 0deg at 50% 50%, transparent 0deg, rgba(139, 92, 246, .12) 60deg, transparent 120deg);
        animation: spin-slow 8s linear infinite;
        opacity: 0;
        transition: opacity .4s
    }

    .box-card:hover:before {
        opacity: 1
    }

    .box-card:hover {
        border-color: rgba(139, 92, 246, .45);
        box-shadow: 0 20px 60px rgba(139, 92, 246, .28), 0 0 100px rgba(236, 72, 153, .18);
        transform: translateY(-10px) scale(1.015)
    }

    .open-btn {
        background: linear-gradient(135deg, #8B5CF6 0%, #EC4899 100%);
        background-size: 200% 200%;
        transition: all .35s
    }

    .open-btn:hover {
        background-position: 100% 0;
        box-shadow: 0 12px 40px rgba(139, 92, 246, .38);
        transform: scale(1.03)
    }

    .glow-ring {
        position: absolute;
        inset: -2px;
        background: linear-gradient(90deg, #8B5CF6, #EC4899, #8B5CF6);
        border-radius: inherit;
        opacity: 0;
        transition: opacity .45s;
        z-index: -1;
        filter: blur(18px)
    }

    .group:hover .glow-ring {
        opacity: .55
    }

    .stat-chip {
        padding: .45rem .7rem;
        border-radius: .8rem;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .03em
    }

    html:not(.dark) .box-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, .95), rgba(248, 250, 252, .95));
        border: 1px solid rgba(139, 92, 246, .18)
    }

    html:not(.dark) .box-card:hover {
        box-shadow: 0 20px 60px rgba(139, 92, 246, .18)
    }
</style>
<main class="min-h-screen pt-24 lg:pl-72 bg-gray-50 dark:bg-gradient-to-br dark:from-dark-900 dark:via-dark-800 dark:to-dark-900 text-gray-900 dark:text-white transition-colors duration-300 relative overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none dark:block hidden">
        <div class="absolute top-1/4 left-1/3 w-[500px] h-[500px] bg-purple-500/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-1/4 right-1/3 w-[500px] h-[500px] bg-pink-500/10 rounded-full blur-[120px]"></div>
    </div>
    <div class="relative z-10 px-4 sm:px-6 lg:px-8 py-12 max-w-[1920px] mx-auto">
        <div class="text-center mb-16 relative">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-gradient-to-r from-purple-500/20 to-pink-500/20 blur-[100px] rounded-full animate-pulse"></div>
            <div class="relative z-10 inline-block mb-6">
                <h1 class="text-5xl md:text-7xl font-black mb-2">
                    <span class="bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600 dark:from-purple-400 dark:via-pink-400 dark:to-purple-400 bg-clip-text text-transparent" style="background-size:200% auto;animation:gradient-shift 4s ease infinite">Mystery Boxes</span>
                </h1>
            </div>
            <p class="text-gray-500 dark:text-gray-400 max-w-3xl mx-auto text-lg leading-relaxed">Burn <span class="font-black text-purple-500">$GASHY</span> to unlock random rewards including digital codes, rare items and token bonuses. Every open is instant and exciting.</p>
        </div>

        <?php if ($boxes): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-8">
                <?php foreach ($boxes as $b):
                    $imgs = json_decode($b['images'], true);
                    $img = is_array($imgs) && !empty($imgs[0]) ? $imgs[0] : 'assets/box.png';
                ?>
                    <div class="group relative">
                        <div class="glow-ring rounded-3xl"></div>
                        <div class="box-card rounded-3xl p-8 flex flex-col items-center text-center shadow-2xl relative z-10 h-full">
                            <div class="absolute inset-x-0 top-0 h-36 bg-gradient-to-b from-purple-500/10 via-pink-500/5 to-transparent rounded-t-3xl"></div>
                            <div class="absolute top-4 left-4 stat-chip bg-purple-500/10 text-purple-500 border border-purple-500/25"><?= (int)$b['stock'] ?> Left</div>
                            <div class="absolute top-4 right-4 stat-chip bg-pink-500/10 text-pink-500 border border-pink-500/25"><?= (int)$b['loot_count'] ?> Rewards</div>
                            <div class="relative w-56 h-56 mb-8 animate-float">
                                <div class="absolute inset-0 bg-gradient-to-br from-purple-500/20 to-pink-500/20 rounded-full blur-3xl"></div>
                                <img src="./<?= $img ?>" class="relative w-full h-full object-contain group-hover:scale-110 transition-transform duration-500" alt="<?= htmlspecialchars($b['title']) ?>">
                            </div>
                            <div class="w-full relative z-10 flex flex-col flex-1">
                                <h3 class="text-3xl font-black bg-gradient-to-r from-gray-900 via-purple-600 to-gray-900 dark:from-white dark:via-purple-200 dark:to-white bg-clip-text text-transparent mb-2"><?= htmlspecialchars($b['title']) ?></h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-5 line-clamp-3 min-h-[60px]"><?= htmlspecialchars($b['description']) ?></p>
                                <div class="grid grid-cols-2 gap-3 mb-6">
                                    <div class="rounded-2xl p-3 bg-gray-100 dark:bg-white/5">
                                        <div class="text-xs text-gray-500 mb-1">USD</div>
                                        <div class="font-black">$<?= number_format($b['price_usd'], 2) ?></div>
                                    </div>
                                    <div class="rounded-2xl p-3 bg-gray-100 dark:bg-white/5">
                                        <div class="text-xs text-gray-500 mb-1">GASHY</div>
                                        <div class="font-black text-purple-500"><?= number_format($b['price_gashy'], 2) ?></div>
                                    </div>
                                </div>
                                <button onclick="openBox(<?= (int)$b['id'] ?>,<?= (float)$b['price_gashy'] ?>)" class="open-btn mt-auto w-full py-4 text-white font-black text-lg rounded-2xl shadow-2xl flex items-center justify-center gap-3 relative overflow-hidden">
                                    <span>Open Now</span>
                                    <span class="font-mono"><?= number_format($b['price_gashy'], 2) ?></span>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="max-w-2xl mx-auto rounded-3xl border border-dashed border-gray-300 dark:border-white/10 p-14 text-center">
                <div class="text-3xl font-black mb-3">No Mystery Boxes</div>
                <div class="text-gray-500">New drops are coming soon.</div>
            </div>
        <?php endif; ?>

        <div class="mt-20 text-center">
            <div class="inline-block bg-purple-50 dark:bg-gradient-to-r dark:from-purple-500/10 dark:via-pink-500/10 dark:to-purple-500/10 border border-purple-200 dark:border-purple-500/20 rounded-2xl p-8 max-w-2xl">
                <h3 class="text-2xl font-black mb-3 bg-gradient-to-r from-purple-500 to-pink-500 bg-clip-text text-transparent">Pro Tip</h3>
                <p class="text-gray-600 dark:text-gray-400">Higher priced boxes usually contain stronger reward pools. Open wisely.</p>
            </div>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>