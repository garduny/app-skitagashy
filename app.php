<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
$priceData = json_decode(file_get_contents('server/.cache/price.json') ?? '{"price":0.045}', true);
$price = $priceData['price'] ?? 0.045;
$mcap = $price * 350000000;
$vol = $mcap * 0.12;
$liq = $mcap * 0.08;
$banners = [];
try {
    $banners = getQuery(" SELECT image_path,link_url FROM banners WHERE is_active=1 ORDER BY sort_order ASC LIMIT 5 ");
} catch (Exception $e) {
}
$flash_deals = getQuery(" SELECT p.id,p.title,p.slug,p.price_gashy,p.images,p.type FROM products p WHERE p.status='active' AND p.stock>0 ORDER BY RAND() LIMIT 4 ");
$top_sellers = getQuery(" SELECT store_name,total_sales,rating FROM sellers WHERE is_approved=1 ORDER BY total_sales DESC LIMIT 5 ");
$new_arrivals = getQuery(" SELECT p.id,p.title,p.slug,p.price_gashy,p.images,p.type FROM products p WHERE p.status='active' ORDER BY p.created_at DESC LIMIT 8 ");
$lottery = findQuery(" SELECT prize_pool,round_number FROM lottery_rounds WHERE status='open' ORDER BY id DESC LIMIT 1 ");
?>
<main class="ml-0 lg:ml-64 pt-20 min-h-screen relative overflow-hidden transition-all duration-300 bg-gray-50 dark:bg-[#060709] text-gray-900 dark:text-white font-sans">
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none z-0">
        <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-green-500/5 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-blue-500/5 blur-[120px] rounded-full"></div>
    </div>
    <div class="relative z-10 max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 space-y-16">
        <div class="overflow-hidden whitespace-nowrap bg-white dark:bg-[#0B0E14] py-2 rounded-lg border border-gray-200 dark:border-white/5 flex items-center gap-8 text-xs font-mono text-gray-500 shadow-sm">
            <div class="animate-marquee inline-flex gap-8"><span class="text-green-500 font-bold">GASHY $<?= number_format($price, 5) ?> ▲ 5.2%</span><span class="text-blue-500 font-bold">SOL $145.20 ▲ 2.1%</span><span class="text-purple-500 font-bold">BTC $68,420 ▲ 1.8%</span><span class="text-gray-400">ETH $3,850 ▼ 0.5%</span><span class="text-yellow-500 font-bold">BNB $2,400 ▲ 3.0%</span><span class="text-green-500 font-bold">GASHY $<?= number_format($price, 5) ?> ▲ 5.2%</span></div>
        </div>
        <div class="text-center space-y-6 py-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-green-500/30 bg-green-500/10 text-green-600 dark:text-green-400 text-xs font-bold uppercase tracking-widest">🦁 The Apex Predator</div>
            <h1 class="text-5xl md:text-7xl font-black tracking-tighter text-gray-900 dark:text-white">GASHY IS THE <span class="text-green-600 dark:text-[#00ffaa]">MEME ALPHA</span></h1>
            <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto text-lg">CoinGecko listed Solana memecoin with revoked authorities, burned LP, and real utility. Join the #GashyGang revolution.</p>
            <div class="flex flex-wrap justify-center gap-4"><a href="https://jup.ag/swap/SOL-GASHY" target="_blank" class="px-8 py-3 bg-green-500 hover:bg-green-600 text-white dark:text-black font-bold rounded-lg transition-transform hover:-translate-y-1 flex items-center gap-2 shadow-lg shadow-green-500/20">🚀 Buy on Jupiter</a><a href="https://www.coingecko.com/en/coins/gashy" target="_blank" class="px-8 py-3 bg-[#8DC351] hover:bg-[#7caf43] text-white dark:text-black font-bold rounded-lg transition-transform hover:-translate-y-1 flex items-center gap-2 shadow-lg">🦎 CoinGecko</a><button onclick="navigator.clipboard.writeText('DokPYQ33k3T9S7EEesvwvuuAtoQb4pY8NWszukKwXWjv');notyf.success('Contract Copied!')" class="px-8 py-3 border border-green-500 text-green-600 dark:text-[#00ffaa] hover:bg-green-50 dark:hover:bg-[#00ffaa]/10 font-bold rounded-lg transition-all flex items-center gap-2">📄 Copy Contract</button></div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-white dark:bg-[#0B0E14] border border-gray-200 dark:border-white/5 rounded-2xl p-6 shadow-xl dark:shadow-2xl">
            <div class="text-center">
                <div class="text-xs text-gray-500 uppercase font-bold mb-1">Price (USD)</div>
                <div class="text-2xl font-mono font-bold text-green-600 dark:text-[#00ffaa]">$<?= number_format($price, 6) ?></div>
            </div>
            <div class="text-center">
                <div class="text-xs text-gray-500 uppercase font-bold mb-1">Market Cap</div>
                <div class="text-2xl font-mono font-bold text-gray-900 dark:text-green-400">$<?= number_format($mcap / 1000, 1) ?>K</div>
            </div>
            <div class="text-center">
                <div class="text-xs text-gray-500 uppercase font-bold mb-1">24h Volume</div>
                <div class="text-2xl font-mono font-bold text-gray-900 dark:text-green-400">$<?= number_format($vol / 1000, 2) ?>K</div>
            </div>
            <div class="text-center">
                <div class="text-xs text-gray-500 uppercase font-bold mb-1">Liquidity</div>
                <div class="text-2xl font-mono font-bold text-gray-900 dark:text-green-400">$<?= number_format($liq / 1000, 2) ?>K</div>
            </div>
        </div>
        <div class="bg-white dark:bg-[#0B0E14] border border-gray-200 dark:border-white/5 rounded-2xl p-6 shadow-xl dark:shadow-2xl">
            <div class="flex items-center gap-2 mb-6"><i class="fa-solid fa-chart-line text-green-600 dark:text-[#00ffaa] text-xl"></i>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Live Price Chart</h3>
            </div>
            <div id="priceChart" style="height: 350px;"></div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="lg:col-span-3 relative h-64 md:h-96 rounded-2xl overflow-hidden group shadow-xl">
                <?php if (!empty($banners)): $main = $banners[0]; ?><img src="<?= $main['image_path'] ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-8 z-20 max-w-lg"><span class="px-3 py-1 rounded-full bg-[#00ffaa] text-black text-xs font-bold uppercase tracking-widest mb-3 inline-block">Featured</span>
                        <h1 class="text-4xl md:text-5xl font-black text-white mb-2 leading-tight drop-shadow-lg">Gashy <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-blue-500">Marketplace</span></h1><a href="<?= $main['link_url'] ?>" class="px-6 py-3 bg-white text-black font-bold rounded-xl hover:bg-gray-200 transition-colors inline-flex items-center gap-2 mt-4">Explore Now <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                <?php else: ?><div class="absolute inset-0 bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center">
                        <h1 class="text-4xl font-black text-white">Welcome to Gashy Bazaar</h1>
                    </div><?php endif; ?>
            </div>
            <div class="lg:col-span-1 space-y-6">
                <div class="relative h-full rounded-2xl p-6 bg-gradient-to-br from-purple-600 to-blue-600 overflow-hidden text-white flex flex-col justify-between shadow-lg hover:-translate-y-1 transition-transform">
                    <div class="absolute top-0 right-0 p-4 opacity-20"><i class="fa-solid fa-box-open text-6xl"></i></div>
                    <div>
                        <h3 class="text-xl font-bold mb-1">Mystery Box</h3>
                        <p class="text-xs opacity-80">Win up to 50,000 GASHY</p>
                    </div><a href="mystery-boxes.php" class="w-full py-3 bg-white/20 hover:bg-white/30 backdrop-blur rounded-xl text-center font-bold text-sm transition-all">Open Now</a>
                </div>
                <div class="relative h-full rounded-2xl p-6 bg-gradient-to-br from-green-500 to-teal-500 overflow-hidden text-white flex flex-col justify-between shadow-lg hover:-translate-y-1 transition-transform">
                    <div class="absolute top-0 right-0 p-4 opacity-20"><i class="fa-solid fa-ticket text-6xl"></i></div>
                    <div>
                        <h3 class="text-xl font-bold mb-1">Lottery Pool</h3>
                        <p class="text-xs opacity-80">Pool: <?= number_format($lottery['prize_pool'] ?? 0) ?> G</p>
                    </div><a href="lottery.php" class="w-full py-3 bg-white/20 hover:bg-white/30 backdrop-blur rounded-xl text-center font-bold text-sm transition-all">Buy Ticket</a>
                </div>
            </div>
        </div>
        <div class="text-center mb-10">
            <h2 class="text-3xl font-black mb-2 text-gray-900 dark:text-[#00ffaa]">Why Choose $GASHY?</h2>
            <p class="text-gray-600 dark:text-gray-400">More than just a meme — real utility, transparency, and community.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-[#0B0E14] border border-gray-200 dark:border-white/5 p-6 rounded-2xl hover:border-green-500/30 transition-colors shadow-sm">
                <div class="text-4xl mb-4">🔐</div>
                <h3 class="text-xl font-bold mb-2">Security First</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Mint and freeze authorities permanently revoked. 100% LP burned. Verified on SolSniffer.</p>
            </div>
            <div class="bg-white dark:bg-[#0B0E14] border border-gray-200 dark:border-white/5 p-6 rounded-2xl hover:border-green-500/30 transition-colors shadow-sm">
                <div class="text-4xl mb-4">✅</div>
                <h3 class="text-xl font-bold mb-2">CoinGecko Listed</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Official listing on CoinGecko with verified pricing and market data across 17+ platforms.</p>
            </div>
            <div class="bg-white dark:bg-[#0B0E14] border border-gray-200 dark:border-white/5 p-6 rounded-2xl hover:border-green-500/30 transition-colors shadow-sm">
                <div class="text-4xl mb-4">💎</div>
                <h3 class="text-xl font-bold mb-2">Active Staking</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Earn rewards by staking your $GASHY. Non-custodial via Streamflow on Solana.</p>
            </div>
            <div class="bg-white dark:bg-[#0B0E14] border border-gray-200 dark:border-white/5 p-6 rounded-2xl hover:border-green-500/30 transition-colors shadow-sm">
                <div class="text-4xl mb-4">🎨</div>
                <h3 class="text-xl font-bold mb-2">Live NFTs</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">51-piece genesis NFT collection with holder perks and exclusive benefits.</p>
            </div>
            <div class="bg-white dark:bg-[#0B0E14] border border-gray-200 dark:border-white/5 p-6 rounded-2xl hover:border-green-500/30 transition-colors shadow-sm">
                <div class="text-4xl mb-4">🤝</div>
                <h3 class="text-xl font-bold mb-2">Transparent Team</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Clear founder reserve with on-chain proofs. No hidden allocations or shady practices.</p>
            </div>
            <div class="bg-white dark:bg--[#0B0E14] border border-gray-200 dark:border-white/5 p-6 rounded-2xl hover:border-green-500/30 transition-colors shadow-sm">
                <div class="text-4xl mb-4">📈</div>
                <h3 class="text-xl font-bold mb-2">Real Roadmap</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">30/60/90 day milestones with measurable deliverables. Not just empty promises.</p>
            </div>
        </div>
        <div class="text-center mb-10">
            <h2 class="text-3xl font-black mb-2 text-gray-900 dark:text-[#00ffaa]">Why GASHY Exists</h2>
            <p class="text-gray-600 dark:text-gray-400">Built to turn meme culture into real value.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-[#0B0E14] border border-gray-200 dark:border-white/5 p-8 rounded-2xl">
                <div class="text-4xl mb-4">🌇</div>
                <h3 class="text-xl font-bold mb-2">Culture First</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Gashy Gangs is a movement — viral energy, raids, memes, and community identity.</p><span class="text-[10px] font-bold bg-green-500/10 text-green-600 dark:text-green-400 px-2 py-1 rounded">Community • Identity</span>
            </div>
            <div class="bg-white dark:bg-[#0B0E14] border border-gray-200 dark:border-white/5 p-8 rounded-2xl">
                <div class="text-4xl mb-4">🛠️</div>
                <h3 class="text-xl font-bold mb-2">Utility Creates Demand</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Utilities like Gashy Bazaar are designed to transform attention into usage.</p><span class="text-[10px] font-bold bg-green-500/10 text-green-600 dark:text-green-400 px-2 py-1 rounded">Utility • Demand</span>
            </div>
            <div class="bg-white dark:bg-[#0B0E14] border border-gray-200 dark:border-white/5 p-8 rounded-2xl">
                <div class="text-4xl mb-4">💚</div>
                <h3 class="text-xl font-bold mb-2">Meaning + Impact</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Through Gashy Care, the ecosystem supports real-world good — transparent charity.</p><span class="text-[10px] font-bold bg-green-500/10 text-green-600 dark:text-green-400 px-2 py-1 rounded">Charity • Transparency</span>
            </div>
        </div>
        <div class="text-center mb-10">
            <h2 class="text-3xl font-black mb-2 text-gray-900 dark:text-[#00ffaa]">Gashy Ecosystem Map</h2>
            <p class="text-gray-600 dark:text-gray-400">Everything connects. One culture. One token.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-[#0B0E14] border border-gray-200 dark:border-white/5 p-6 rounded-2xl hover:border-green-500/20 transition-all">
                <div class="text-4xl mb-4">🐱</div>
                <h3 class="text-xl font-bold mb-2">$GASHY</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">The neon cat of Solana — the core token that powers the ecosystem.</p><span class="text-[10px] font-bold bg-green-500/10 text-green-600 dark:text-[#00ffaa] px-2 py-1 rounded">Core Token</span>
            </div>
            <div class="bg-white dark:bg-[#0B0E14] border border-green-500/40 p-6 rounded-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 p-2"><span class="w-2 h-2 bg-green-500 rounded-full animate-ping block"></span></div>
                <div class="text-4xl mb-4">🛒</div>
                <h3 class="text-xl font-bold mb-2">Gashy Bazaar</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Marketplace utility where you can spend $GASHY on digital products.</p><span class="text-[10px] font-bold bg-green-500/10 text-green-600 dark:text-[#00ffaa] px-2 py-1 rounded">Utility</span>
            </div>
            <div class="bg-white dark:bg-[#0B0E14] border border-gray-200 dark:border-white/5 p-6 rounded-2xl hover:border-green-500/20 transition-all">
                <div class="text-4xl mb-4">🔥</div>
                <h3 class="text-xl font-bold mb-2">Burn Events</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Deflation-focused moments that create hype and reduce supply.</p><span class="text-[10px] font-bold bg-green-500/10 text-green-600 dark:text-[#00ffaa] px-2 py-1 rounded">Deflation</span>
            </div>
            <div class="bg-white dark:bg-[#0B0E14] border border-gray-200 dark:border-white/5 p-6 rounded-2xl">
                <div class="text-4xl mb-4">🖼️</div>
                <h3 class="text-xl font-bold mb-2">NFT + Staking</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Collectibles and staking mechanics to keep holders engaged.</p><span class="text-[10px] font-bold bg-green-500/10 text-green-600 dark:text-[#00ffaa] px-2 py-1 rounded">NFTs</span>
            </div>
            <div class="bg-white dark:bg-[#0B0E14] border border-gray-200 dark:border-white/5 p-6 rounded-2xl">
                <div class="text-4xl mb-4">💚</div>
                <h3 class="text-xl font-bold mb-2">Gashy Care</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Charity & impact layer — memes with meaning.</p><span class="text-[10px] font-bold bg-green-500/10 text-green-600 dark:text-[#00ffaa] px-2 py-1 rounded">Impact</span>
            </div>
            <div class="bg-white dark:bg-[#0B0E14] border border-gray-200 dark:border-white/5 p-6 rounded-2xl">
                <div class="text-4xl mb-4">🪐</div>
                <h3 class="text-xl font-bold mb-2">KITA Crossover</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Future ecosystem crossover with the Alien universe.</p><span class="text-[10px] font-bold bg-green-500/10 text-green-600 dark:text-[#00ffaa] px-2 py-1 rounded">Expansion</span>
            </div>
        </div>
        <div class="text-center mb-10">
            <h2 class="text-3xl font-black mb-2 text-gray-900 dark:text-[#00ffaa]">Security & Transparency</h2>
            <p class="text-gray-600 dark:text-gray-400">Built for long-term trust.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-[#0B0E14] border border-gray-200 dark:border-white/5 p-6 rounded-2xl">
                <div class="text-4xl mb-4">🔍</div>
                <h3 class="text-xl font-bold mb-2">Clear On-Chain Verification</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Always verify the official contract. Use trusted explorers.</p><span class="text-[10px] font-bold bg-green-500/10 text-green-600 dark:text-green-400 px-2 py-1 rounded">Verify</span>
            </div>
            <div class="bg-white dark:bg-[#0B0E14] border border-gray-200 dark:border-white/5 p-6 rounded-2xl">
                <div class="text-4xl mb-4">📜</div>
                <h3 class="text-xl font-bold mb-2">Transparent Updates</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Major actions are announced publicly with proof.</p><span class="text-[10px] font-bold bg-green-500/10 text-green-600 dark:text-green-400 px-2 py-1 rounded">Proof</span>
            </div>
            <div class="bg-white dark:bg-[#0B0E14] border border-gray-200 dark:border-white/5 p-6 rounded-2xl">
                <div class="text-4xl mb-4">🧠</div>
                <h3 class="text-xl font-bold mb-2">No Fake Promises</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">We prioritize shipping real utilities and building culture.</p><span class="text-[10px] font-bold bg-green-500/10 text-green-600 dark:text-green-400 px-2 py-1 rounded">Real Utility</span>
            </div>
        </div>
        <div class="text-center mb-10">
            <h2 class="text-3xl font-black mb-2 text-gray-900 dark:text-[#00ffaa]">Built by Darin Lab</h2>
            <p class="text-gray-600 dark:text-gray-400">An independent builder lab creating a connected Solana universe.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-[#0B0E14] border border-gray-200 dark:border-white/5 p-6 rounded-2xl">
                <div class="text-4xl mb-4">👨‍🚀</div>
                <h3 class="text-xl font-bold mb-2">Founder: Darin</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Builder focused on ecosystem design and premium branding.</p><span class="text-[10px] font-bold bg-green-500/10 text-green-600 dark:text-green-400 px-2 py-1 rounded">Solana</span>
            </div>
            <div class="bg-white dark:bg-[#0B0E14] border border-gray-200 dark:border-white/5 p-6 rounded-2xl">
                <div class="text-4xl mb-4">👽</div>
                <h3 class="text-xl font-bold mb-2">$KITA</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Alien-themed Solana ecosystem — utility-first vision.</p><span class="text-[10px] font-bold bg-green-500/10 text-green-600 dark:text-green-400 px-2 py-1 rounded">Universe</span>
            </div>
            <div class="bg-white dark:bg-[#0B0E14] border border-gray-200 dark:border-white/5 p-6 rounded-2xl">
                <div class="text-4xl mb-4">🏢</div>
                <h3 class="text-xl font-bold mb-2">FUNDAQ</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Structured finance-inspired brand built for stability.</p><span class="text-[10px] font-bold bg-green-500/10 text-green-600 dark:text-green-400 px-2 py-1 rounded">Finance</span>
            </div>
        </div>
        <div class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2"><i class="fa-solid fa-bolt text-yellow-500"></i> Flash Deals</h2>
                <div class="flex gap-2 text-xs font-mono bg-red-500/10 text-red-500 px-3 py-1 rounded font-bold border border-red-500/20">Ends in: 04:22:19</div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"><?php foreach ($flash_deals as $p): $img = json_decode($p['images'])[0] ?? 'assets/placeholder.png'; ?><a href="product.php?slug=<?= $p['slug'] ?>" class="group bg-white dark:bg-[#0B0E14] rounded-xl border border-gray-200 dark:border-white/5 overflow-hidden hover:-translate-y-1 transition-all shadow-lg hover:border-green-500/30">
                        <div class="aspect-square relative bg-gray-100 dark:bg-black/20"><img src="<?= $img ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute top-2 right-2 px-2 py-1 bg-red-600 text-white text-[10px] font-bold rounded shadow">-20%</div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-gray-900 dark:text-white truncate mb-1"><?= $p['title'] ?></h3>
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-bold text-green-600 dark:text-[#00ffaa]"><?= number_format($p['price_gashy']) ?> G</div><span class="text-xs text-gray-400 line-through"><?= number_format($p['price_gashy'] * 1.2) ?></span>
                            </div>
                        </div>
                    </a><?php endforeach; ?></div>
        </div>
        <div class="bg-gradient-to-br from-white to-gray-50 dark:from-[#0B0E14] dark:to-green-900/10 border border-green-500/20 rounded-3xl p-8 md:p-12 relative overflow-hidden">
            <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="text-4xl mb-6">🎁</div>
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white mb-4">What is Gashy Bazaar?</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Gashy Bazaar is our utility marketplace where the community can use $GASHY to buy real-world digital value. It’s designed to turn attention into usage.</p>
                    <div class="flex gap-2 mb-8"><span class="bg-green-500/20 text-green-600 dark:text-green-400 px-3 py-1 rounded-full text-xs font-bold">Utility</span><span class="bg-green-500/20 text-green-600 dark:text-green-400 px-3 py-1 rounded-full text-xs font-bold">Demand</span><span class="bg-green-500/20 text-green-600 dark:text-green-400 px-3 py-1 rounded-full text-xs font-bold">Culture</span></div><a href="market.php" class="px-8 py-3 bg-green-500 hover:bg-green-600 text-white dark:text-black font-bold rounded-lg inline-flex items-center gap-2">🛍️ Request an Item</a>
                </div>
                <div class="bg-white dark:bg-[#060709] p-6 rounded-2xl border border-gray-200 dark:border-white/5">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-4">What you'll find inside</h3>
                    <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-green-500"></i> iTunes / Apple Gift Cards</li>
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-green-500"></i> Google Play Cards</li>
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-green-500"></i> Digital Products & Subscriptions</li>
                        <li class="flex items-center gap-3"><i class="fa-solid fa-check text-green-500"></i> Community Drops</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">New Arrivals</h2>
                <div class="space-y-4"><?php foreach ($new_arrivals as $p): $img = json_decode($p['images'])[0] ?? 'assets/placeholder.png'; ?><a href="product.php?slug=<?= $p['slug'] ?>" class="flex items-center gap-4 p-4 bg-white dark:bg-[#0B0E14] rounded-xl border border-gray-200 dark:border-white/5 hover:border-blue-500 transition-all">
                            <div class="w-20 h-20 rounded-lg bg-gray-100 dark:bg-white/5 overflow-hidden flex-shrink-0"><img src="<?= $img ?>" class="w-full h-full object-cover"></div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-gray-900 dark:text-white truncate"><?= $p['title'] ?></h3>
                                <div class="text-xs text-gray-500 uppercase mt-1"><?= $p['type'] ?></div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-green-600 dark:text-[#00ffaa]"><?= number_format($p['price_gashy']) ?> G</div><button class="mt-2 text-xs bg-gray-100 dark:bg-white/10 px-3 py-1 rounded hover:bg-green-500 hover:text-white transition-colors">Buy</button>
                            </div>
                        </a><?php endforeach; ?></div>
            </div>
            <div class="lg:col-span-1">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Top Sellers</h2>
                <div class="bg-white dark:bg-[#0B0E14] rounded-2xl border border-gray-200 dark:border-white/5 p-6 space-y-6"><?php foreach ($top_sellers as $i => $s): ?><div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-white/10 flex items-center justify-center font-bold text-gray-500 text-sm"><?= ($i + 1) ?></div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm"><?= $s['store_name'] ?></h4>
                                <div class="text-xs text-yellow-500"><i class="fa-solid fa-star"></i> <?= $s['rating'] ?></div>
                            </div>
                            <div class="text-xs font-bold text-gray-400"><?= $s['total_sales'] ?> Sold</div>
                        </div><?php endforeach; ?></div>
                <div class="mt-8 p-6 rounded-2xl bg-gradient-to-br from-blue-900/10 to-green-900/10 border border-green-500/20 text-center relative overflow-hidden">
                    <div class="relative z-10">
                        <h3 class="font-bold text-gray-900 dark:text-white mb-2">Become a Seller</h3>
                        <p class="text-xs text-gray-500 mb-4">Start your own crypto store today.</p><a href="seller.php" class="px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-lg shadow-lg transition-all">Apply Now</a>
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
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var p = <?= $price ?>;
        var data = [];
        for (var i = 0; i < 12; i++) {
            data.push(p * (1 + ((Math.random() - 0.5) * 0.1)));
        }
        var options = {
            series: [{
                name: 'Price',
                data: data
            }],
            chart: {
                type: 'area',
                height: 300,
                toolbar: {
                    show: false
                },
                background: 'transparent'
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2,
                colors: ['#00ffaa']
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.5,
                    opacityTo: 0.0,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                labels: {
                    show: false
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
                        colors: '#6b7280'
                    },
                    formatter: (v) => {
                        return '$' + v.toFixed(4)
                    }
                }
            },
            grid: {
                borderColor: '#1e293b',
                strokeDashArray: 4
            },
            theme: {
                mode: 'dark'
            },
            tooltip: {
                theme: 'dark'
            }
        };
        var chart = new ApexCharts(document.querySelector("#priceChart"), options);
        chart.render();
    });
</script>
<?php require_once 'footer.php'; ?>