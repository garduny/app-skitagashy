<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
$boxes = getQuery(" SELECT p.id,p.title,p.price_gashy,p.images,p.description FROM products p WHERE p.type='mystery_box' AND p.status='active' AND p.stock>0 ORDER BY p.price_gashy ASC ");
?>
<main class="min-h-screen pt-20 lg:pl-64 bg-gray-50 dark:bg-[#0B0E14] text-gray-800 dark:text-gray-200 transition-colors duration-300 relative overflow-hidden">
    <div class="relative z-10 p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto">
        <div class="text-center mb-12 relative">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-32 h-32 bg-purple-600/20 blur-[80px] rounded-full mix-blend-multiply dark:mix-blend-normal"></div>
            <h1 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-4 relative z-10">Mystery <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-500 to-pink-500">Boxes</span></h1>
            <p class="text-gray-500 dark:text-gray-400 max-w-xl mx-auto relative z-10">Try your luck. Burn $GASHY tokens to open boxes containing rare NFTs, massive token rewards, or exclusive gift cards.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($boxes as $b): $img = json_decode($b['images'])[0] ?? 'assets/box.png'; ?>
                <div class="group relative perspective-1000">
                    <div class="relative bg-gradient-to-b from-white to-gray-100 dark:from-[#1E2532] dark:to-[#151A23] rounded-3xl p-8 border border-gray-200 dark:border-white/5 hover:border-purple-500/50 transition-all duration-500 transform hover:-translate-y-2 hover:shadow-2xl hover:shadow-purple-500/20 flex flex-col items-center text-center shadow-lg">
                        <div class="absolute inset-x-0 top-0 h-32 bg-gradient-to-b from-purple-500/5 to-transparent rounded-t-3xl"></div>
                        <div class="relative w-48 h-48 mb-6 drop-shadow-2xl filter group-hover:brightness-110 transition-all">
                            <img src="<?= $img ?>" class="w-full h-full object-contain animate-float" style="animation-duration: 3s;">
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2"><?= $b['title'] ?></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6"><?= $b['description'] ?></p>
                        <div class="w-full bg-gray-50 dark:bg-black/30 rounded-xl p-4 border border-gray-200 dark:border-white/5 mb-6">
                            <div class="flex justify-between items-center text-xs mb-2">
                                <span class="text-yellow-500 dark:text-yellow-400 font-bold">Legendary</span>
                                <span class="text-gray-500">1%</span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mb-3">
                                <div class="w-[1%] h-full bg-yellow-400"></div>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-purple-500 dark:text-purple-400 font-bold">Epic</span>
                                <span class="text-gray-500">15%</span>
                            </div>
                        </div>
                        <button onclick="openBox(<?= $b['id'] ?>)" class="w-full py-4 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 text-white font-black text-lg rounded-xl shadow-lg shadow-purple-600/25 transition-all flex items-center justify-center gap-2 group-hover:gap-4">
                            <span>Open for</span>
                            <span><?= number_format($b['price_gashy']) ?> GASHY</span>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>
<style>
    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-15px);
        }
    }

    .animate-float {
        animation: float 4s ease-in-out infinite;
    }
</style>
<script src="public/js/pages/mystery-boxes.js"></script>
<?php require_once 'footer.php'; ?>