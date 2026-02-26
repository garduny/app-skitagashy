<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
$boxes = getQuery(" SELECT p.id,p.title,p.price_gashy,p.images,p.description FROM products p WHERE p.type='mystery_box' AND p.status='active' AND p.stock>0 ORDER BY p.price_gashy ASC ");
?>
<style>
    @keyframes float {

        0%,
        100% {
            transform: translateY(0px) rotate(0deg)
        }

        50% {
            transform: translateY(-20px) rotate(3deg)
        }
    }

    @keyframes pulse-glow {

        0%,
        100% {
            box-shadow: 0 0 30px rgba(139, 92, 246, 0.3), 0 0 60px rgba(236, 72, 153, 0.2)
        }

        50% {
            box-shadow: 0 0 50px rgba(139, 92, 246, 0.5), 0 0 100px rgba(236, 72, 153, 0.3)
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
            transform: rotate(0deg)
        }

        to {
            transform: rotate(360deg)
        }
    }

    @keyframes gradient-shift {

        0%,
        100% {
            background-position: 0% 50%
        }

        50% {
            background-position: 100% 50%
        }
    }

    .animate-float {
        animation: float 6s ease-in-out infinite
    }

    .box-card {
        background: linear-gradient(135deg, rgba(19, 24, 36, 0.8), rgba(26, 31, 46, 0.8));
        backdrop-filter: blur(20px);
        border: 1px solid rgba(139, 92, 246, 0.15);
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden
    }

    .box-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: conic-gradient(from 0deg at 50% 50%, transparent 0deg, rgba(139, 92, 246, 0.15) 60deg, transparent 120deg);
        animation: spin-slow 8s linear infinite;
        opacity: 0;
        transition: opacity 0.5s
    }

    .box-card:hover::before {
        opacity: 1
    }

    .box-card:hover {
        border-color: rgba(139, 92, 246, 0.5);
        box-shadow: 0 20px 60px rgba(139, 92, 246, 0.3), 0 0 100px rgba(236, 72, 153, 0.2);
        transform: translateY(-12px) scale(1.02)
    }

    .rarity-bar {
        background: linear-gradient(90deg, rgba(139, 92, 246, 0.2), rgba(236, 72, 153, 0.2));
        border: 1px solid rgba(139, 92, 246, 0.2);
        backdrop-filter: blur(10px)
    }

    .open-btn {
        background: linear-gradient(135deg, #8B5CF6 0%, #EC4899 100%);
        background-size: 200% 200%;
        transition: all 0.4s ease
    }

    .open-btn:hover {
        background-position: 100% 0;
        box-shadow: 0 10px 40px rgba(139, 92, 246, 0.4), 0 0 60px rgba(236, 72, 153, 0.3);
        transform: scale(1.05)
    }

    .glow-ring {
        position: absolute;
        inset: -2px;
        background: linear-gradient(90deg, #8B5CF6, #EC4899, #8B5CF6);
        border-radius: inherit;
        opacity: 0;
        transition: opacity 0.5s;
        z-index: -1;
        filter: blur(20px)
    }

    .box-card:hover .glow-ring {
        opacity: 0.6
    }

    html:not(.dark) .box-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(248, 250, 252, 0.95));
        border: 1px solid rgba(139, 92, 246, 0.2)
    }

    html:not(.dark) .box-card:hover {
        box-shadow: 0 20px 60px rgba(139, 92, 246, 0.2)
    }

    html:not(.dark) .rarity-bar {
        background: linear-gradient(90deg, rgba(139, 92, 246, 0.1), rgba(236, 72, 153, 0.1));
        border: 1px solid rgba(139, 92, 246, 0.25)
    }
</style>
<main class="min-h-screen pt-24 lg:pl-72 bg-gray-50 dark:bg-gradient-to-br dark:from-dark-900 dark:via-dark-800 dark:to-dark-900 text-gray-900 dark:text-white transition-colors duration-300 relative overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none dark:block hidden">
        <div class="absolute top-1/4 left-1/3 w-[500px] h-[500px] bg-purple-500/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-1/4 right-1/3 w-[500px] h-[500px] bg-pink-500/10 rounded-full blur-[120px]"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-accent-500/5 rounded-full blur-[150px]"></div>
    </div>
    <div class="relative z-10 px-4 sm:px-6 lg:px-8 py-12 max-w-[1920px] mx-auto">
        <div class="text-center mb-16 relative">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-gradient-to-r from-purple-500/20 to-pink-500/20 blur-[100px] rounded-full animate-pulse"></div>
            <div class="relative z-10 inline-block mb-6">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-500 to-pink-500 blur-2xl opacity-30 animate-pulse"></div>
                <h1 class="relative text-5xl md:text-7xl font-black mb-2">
                    <span class="bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600 dark:from-purple-400 dark:via-pink-400 dark:to-purple-400 bg-clip-text text-transparent" style="background-size:200% auto;animation:gradient-shift 4s ease infinite">Mystery Boxes</span>
                </h1>
            </div>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto text-lg leading-relaxed relative z-10">Try your luck and burn <span class="font-bold text-purple-500">$GASHY</span> tokens to unlock mystery boxes containing <span class="font-semibold text-pink-500">rare NFTs</span>, <span class="font-semibold text-yellow-500">massive token rewards</span>, or <span class="font-semibold text-primary-500">exclusive gift cards</span>. The rarer the item, the bigger the prize!</p>
            <div class="mt-8 flex items-center justify-center gap-8 text-sm relative z-10">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse shadow-lg shadow-yellow-400/50"></div>
                    <span class="text-gray-600 dark:text-gray-400">Legendary <span class="font-bold text-yellow-500">1%</span></span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-purple-400 animate-pulse shadow-lg shadow-purple-400/50"></div>
                    <span class="text-gray-600 dark:text-gray-400">Epic <span class="font-bold text-purple-500">15%</span></span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-blue-400 animate-pulse shadow-lg shadow-blue-400/50"></div>
                    <span class="text-gray-600 dark:text-gray-400">Rare <span class="font-bold text-blue-500">30%</span></span>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-8">
            <?php foreach ($boxes as $b): $img = json_decode($b['images'])[0] ?? 'assets/box.png'; ?>
                <div class="group relative">
                    <div class="glow-ring"></div>
                    <div class="box-card rounded-3xl p-8 flex flex-col items-center text-center shadow-2xl relative z-10">
                        <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-purple-500/10 via-pink-500/5 to-transparent rounded-t-3xl pointer-events-none"></div>
                        <div class="absolute top-4 right-4 px-3 py-1.5 bg-gradient-to-r from-purple-500/20 to-pink-500/20 backdrop-blur-xl rounded-xl border border-purple-500/30 text-[10px] font-black text-purple-400 uppercase tracking-wider">Limited</div>
                        <div class="relative w-56 h-56 mb-8 animate-float filter drop-shadow-2xl">
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-500/20 to-pink-500/20 rounded-full blur-3xl"></div>
                            <img src="./<?= $img ?>" class="relative w-full h-full object-contain transform group-hover:scale-110 transition-transform duration-500" alt="<?= $b['title'] ?>">
                        </div>
                        <div class="relative z-10 w-full">
                            <h3 class="text-3xl font-black bg-gradient-to-r from-gray-900 via-purple-600 to-gray-900 dark:from-white dark:via-purple-200 dark:to-white bg-clip-text text-transparent mb-3"><?= $b['title'] ?></h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-8 leading-relaxed"><?= $b['description'] ?></p>
                            <div class="rarity-bar rounded-2xl p-5 mb-8">
                                <div class="flex justify-between items-center mb-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-yellow-400 shadow-lg shadow-yellow-400/50"></div>
                                        <span class="text-yellow-500 dark:text-yellow-400 font-black text-xs uppercase tracking-wider">Legendary</span>
                                    </div>
                                    <span class="text-gray-500 dark:text-gray-400 font-bold text-xs">1%</span>
                                </div>
                                <div class="w-full h-2 bg-gray-200 dark:bg-gray-800 rounded-full overflow-hidden mb-4 shadow-inner">
                                    <div class="w-[1%] h-full bg-gradient-to-r from-yellow-400 to-yellow-500 shadow-lg shadow-yellow-400/50"></div>
                                </div>
                                <div class="flex justify-between items-center mb-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-purple-400 shadow-lg shadow-purple-400/50"></div>
                                        <span class="text-purple-500 dark:text-purple-400 font-black text-xs uppercase tracking-wider">Epic</span>
                                    </div>
                                    <span class="text-gray-500 dark:text-gray-400 font-bold text-xs">15%</span>
                                </div>
                                <div class="w-full h-2 bg-gray-200 dark:bg-gray-800 rounded-full overflow-hidden mb-4 shadow-inner">
                                    <div class="w-[15%] h-full bg-gradient-to-r from-purple-400 to-purple-500 shadow-lg shadow-purple-400/50"></div>
                                </div>
                                <div class="flex justify-between items-center mb-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-blue-400 shadow-lg shadow-blue-400/50"></div>
                                        <span class="text-blue-500 dark:text-blue-400 font-black text-xs uppercase tracking-wider">Rare</span>
                                    </div>
                                    <span class="text-gray-500 dark:text-gray-400 font-bold text-xs">30%</span>
                                </div>
                                <div class="w-full h-2 bg-gray-200 dark:bg-gray-800 rounded-full overflow-hidden shadow-inner">
                                    <div class="w-[30%] h-full bg-gradient-to-r from-blue-400 to-blue-500 shadow-lg shadow-blue-400/50"></div>
                                </div>
                            </div>
                            <button onclick="openBox(<?= $b['id'] ?>)" class="open-btn w-full py-4 text-white font-black text-lg rounded-2xl shadow-2xl flex items-center justify-center gap-3 relative overflow-hidden group">
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                                <svg class="w-6 h-6 transform group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <span>Open for</span>
                                <span class="font-mono"><?= number_format($b['price_gashy']) ?> $GASHY</span>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-20 text-center relative z-10">
            <div class="inline-block bg-purple-50 dark:bg-gradient-to-r dark:from-purple-500/10 dark:via-pink-500/10 dark:to-purple-500/10 backdrop-blur-xl border border-purple-200 dark:border-purple-500/20 rounded-2xl p-8 max-w-2xl">
                <div class="flex items-center justify-center gap-3 mb-4">
                    <svg class="w-8 h-8 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.476.859h4.002z" />
                    </svg>
                    <h3 class="text-2xl font-black bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">Pro Tip</h3>
                </div>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Higher priced boxes have better odds for legendary items! All rewards are instantly transferred to your wallet upon opening.</p>
            </div>
        </div>
    </div>
</main>
<script src="./public/js/pages/mystery-boxes.js"></script>
<?php require_once 'footer.php'; ?>