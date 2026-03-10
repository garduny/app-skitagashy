<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
$priceData = json_decode(@file_get_contents('server/.cache/price.json'), true) ?: [];
$price = $priceData['price'] ?? 0.045;
$mcap = $priceData['marketcap'] ?? 0;
$vol = $priceData['volume24h'] ?? 0;
$liq = $priceData['liquidity'] ?? 0;
$banners = [];
try {
    $banners = getQuery(" SELECT image_path,link_url FROM banners WHERE is_active=1 ORDER BY sort_order ASC LIMIT 5 ");
} catch (Exception $e) {
}
$flash_deals = getQuery(" SELECT p.id,p.title,p.slug,p.price_usd,p.images,p.type FROM products p WHERE p.status='active' AND p.stock>0 ORDER BY RAND() LIMIT 4 ");
$top_sellers = getQuery(" SELECT store_name,total_sales,rating FROM sellers WHERE is_approved=1 ORDER BY total_sales DESC LIMIT 5 ");
$new_arrivals = getQuery(" SELECT p.id,p.title,p.slug,p.price_usd,p.images,p.type FROM products p WHERE p.status='active' ORDER BY p.created_at DESC LIMIT 8 ");
$lottery = findQuery(" SELECT prize_pool,round_number,draw_time FROM lottery_rounds WHERE status='open' ORDER BY id DESC LIMIT 1 ");
$mystery_box = findQuery(" SELECT p.id,p.title,p.slug,p.price_usd,p.images FROM products p INNER JOIN mystery_box_loot m ON m.box_product_id=p.id WHERE p.status='active' AND p.stock>0 GROUP BY p.id ORDER BY p.created_at DESC LIMIT 1 ");
function usdToGashy($usd, $rate)
{
    return $rate > 0 ? ($usd / $rate) : 0;
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        display: ['Orbitron', 'sans-serif'],
                        body: ['Rajdhani', 'sans-serif'],
                    },
                    colors: {
                        neon: '#00ffaa',
                        plasma: '#ff2d78',
                        void: '#03050d',
                    },
                    animation: {
                        ticker: 'ticker 30s linear infinite',
                        pulse2: 'pulse 2s ease-in-out infinite',
                        slideUp: 'slideUp 0.6s ease-out both',
                    },
                    keyframes: {
                        ticker: {
                            '0%': {
                                transform: 'translateX(0)'
                            },
                            '100%': {
                                transform: 'translateX(-50%)'
                            }
                        },
                        slideUp: {
                            from: {
                                opacity: '0',
                                transform: 'translateY(30px)'
                            },
                            to: {
                                opacity: '1',
                                transform: 'translateY(0)'
                            }
                        },
                    },
                    backgroundImage: {
                        grid: "linear-gradient(rgba(0,255,170,.02) 1px,transparent 1px),linear-gradient(90deg,rgba(0,255,170,.02) 1px,transparent 1px)",
                    },
                    backgroundSize: {
                        grid: '60px 60px',
                    },
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --neon: #00ffaa;
            --void: #03050d;
        }

        body {
            font-family: 'Rajdhani', sans-serif;
            background: var(--void);
        }

        .ticker-track {
            animation: ticker 30s linear infinite;
        }

        @keyframes ticker {
            0% {
                transform: translateX(0)
            }

            100% {
                transform: translateX(-50%)
            }
        }

        .glow-dot {
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .5
            }
        }

        .slide-up {
            animation: slideUp .6s ease-out both;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(28px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        .grad-text {
            background: linear-gradient(135deg, #00ffaa, #00c8ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .banner-img {
            transition: transform .5s ease;
        }

        .banner-wrap:hover .banner-img {
            transform: scale(1.05);
        }

        .prod-img {
            transition: transform .5s ease;
        }

        .prod-card:hover .prod-img {
            transform: scale(1.08);
        }

        html:not(.dark) body {
            background: #f1f5f9;
            color: #0f172a;
        }

        html:not(.dark) .grad-text {
            background: linear-gradient(135deg, #007a55, #0ea5e9);
            -webkit-background-clip: text;
            background-clip: text;
        }

        html:not(.dark) .ticker-bar-bg {
            background: rgba(255, 255, 255, .97) !important;
            border-color: rgba(0, 163, 114, .25) !important;
        }

        html:not(.dark) .ticker-price {
            color: #475569 !important;
        }

        html:not(.dark) .hero-badge {
            border-color: rgba(0, 163, 114, .35);
            background: rgba(0, 163, 114, .07);
            color: #007a55;
        }

        html:not(.dark) .hero-badge .glow-dot {
            background: #007a55;
            box-shadow: 0 0 8px rgba(0, 163, 114, .5);
        }

        html:not(.dark) .hero-title {
            color: #0f172a;
        }

        html:not(.dark) .hero-accent {
            color: #007a55 !important;
        }

        html:not(.dark) .panel-bg {
            background: rgba(255, 255, 255, .97) !important;
            border-color: rgba(0, 163, 114, .15) !important;
        }

        html:not(.dark) .panel-bg:hover {
            border-color: rgba(0, 163, 114, .3) !important;
        }

        html:not(.dark) .stat-grid-wrap {
            background: rgba(0, 163, 114, .1);
            border-color: rgba(0, 163, 114, .2);
        }

        html:not(.dark) .stat-cell {
            background: rgba(255, 255, 255, .97) !important;
        }

        html:not(.dark) .stat-val-neon {
            color: #007a55 !important;
        }

        html:not(.dark) .stat-val-white {
            color: #0f172a !important;
        }

        html:not(.dark) .stat-label {
            color: #64748b !important;
        }

        html:not(.dark) .sec-title {
            color: #0f172a !important;
        }

        html:not(.dark) .sec-sub {
            color: #64748b !important;
        }

        html:not(.dark) .feat-title {
            color: #0f172a !important;
        }

        html:not(.dark) .feat-desc {
            color: #475569 !important;
        }

        html:not(.dark) .feat-icon {
            background: rgba(0, 163, 114, .08) !important;
            border-color: rgba(0, 163, 114, .2) !important;
        }

        html:not(.dark) .prod-title {
            color: #0f172a !important;
        }

        html:not(.dark) .prod-thumb-bg {
            background: #f1f5f9 !important;
            border-color: rgba(0, 0, 0, .06) !important;
        }

        html:not(.dark) .prod-type {
            color: #94a3b8 !important;
        }

        html:not(.dark) .neon-price {
            color: #007a55 !important;
        }

        html:not(.dark) .neon-pill {
            color: #007a55 !important;
            background: rgba(0, 163, 114, .08) !important;
            border-color: rgba(0, 163, 114, .2) !important;
        }

        html:not(.dark) .old-price {
            color: #94a3b8 !important;
        }

        html:not(.dark) .seller-name {
            color: #0f172a !important;
        }

        html:not(.dark) .seller-sales {
            color: #94a3b8 !important;
        }

        html:not(.dark) .seller-divider {
            border-color: rgba(0, 0, 0, .06) !important;
        }

        html:not(.dark) .chart-title {
            color: #0f172a !important;
        }

        html:not(.dark) .neon-border-subtle {
            border-color: rgba(0, 163, 114, .2) !important;
        }
    </style>
</head>

<body class="text-slate-800 dark:text-slate-200 overflow-x-hidden">
    <div class="fixed inset-0 pointer-events-none z-0 bg-grid bg-[length:60px_60px]" style="background-image:linear-gradient(rgba(0,255,170,.02) 1px,transparent 1px),linear-gradient(90deg,rgba(0,255,170,.02) 1px,transparent 1px)"></div>
    <div class="fixed -top-48 left-[10%] w-[600px] h-[600px] rounded-full bg-[#00ffaa] blur-[100px] opacity-[.04] pointer-events-none z-0"></div>
    <div class="fixed -bottom-24 right-[5%]  w-[600px] h-[600px] rounded-full bg-blue-500  blur-[100px] opacity-[.04] pointer-events-none z-0"></div>
    <main class="relative z-10 pt-24 lg:pl-72 min-h-screen">
        <div class="sticky top-20 z-50 overflow-hidden border-y border-[rgba(0,255,170,.2)] bg-[rgba(3,5,13,.95)] backdrop-blur-md ticker-bar-bg">
            <div class="ticker-track inline-flex gap-12 py-3 px-4 whitespace-nowrap">
                <?php
                $tickers = [
                    ['GASHY', '#00ffaa', '$' . number_format($price, 5), '▲ 5.2%', '#00ffaa'],
                    ['SOL',   '#60a5fa', '$145.20',   '▲ 2.1%', '#60a5fa'],
                    ['BTC',   '#f97316', '$68,420',   '▲ 1.8%', '#f97316'],
                    ['ETH',   '#a78bfa', '$3,850',    '▼ 0.5%', '#f87171'],
                    ['BNB',   '#fbbf24', '$2,400',    '▲ 3.0%', '#fbbf24'],
                ];
                for ($r = 0; $r < 2; $r++) foreach ($tickers as $t): ?>
                    <span class="inline-flex items-center gap-2 font-body text-xs font-bold tracking-wide">
                        <span class="glow-dot w-1.5 h-1.5 rounded-full flex-shrink-0" style="background:<?= $t[1] ?>"></span>
                        <span style="color:<?= $t[1] ?>"><?= $t[0] ?></span>
                        <span class="ticker-price text-slate-400"><?= $t[2] ?></span>
                        <span style="color:<?= $t[4] ?>"><?= $t[3] ?></span>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="relative z-10 max-w-[1800px] mx-auto px-6 pb-20">
            <section class="slide-up text-center py-16">
                <div class="hero-badge inline-flex items-center gap-2 px-5 py-2 rounded-full border border-[rgba(0,255,170,.3)] bg-[rgba(0,255,170,.05)] text-[#00ffaa] text-[.7rem] font-bold tracking-widest uppercase mb-8">
                    <span class="glow-dot w-1.5 h-1.5 rounded-full bg-[#00ffaa]"></span>
                    THE APEX PREDATOR OF SOLANA MEMECOINS
                </div>
                <h1 class="hero-title font-display font-black text-slate-900 dark:text-white leading-none tracking-tight" style="font-size:clamp(2.5rem,8vw,6rem)">GASHY IS THE</h1>
                <span class="grad-text font-display font-black leading-none tracking-tight block mb-8" style="font-size:clamp(3rem,9vw,7rem)">MEME ALPHA</span>
                <p class="font-body text-lg font-medium text-slate-500 dark:text-slate-400 max-w-2xl mx-auto leading-relaxed mb-12">
                    CoinGecko listed Solana memecoin with <span class="hero-accent text-[#00ffaa] font-bold">revoked authorities</span>,
                    <span class="hero-accent text-[#00ffaa] font-bold">burned LP</span>, and
                    <span class="hero-accent text-[#00ffaa] font-bold">real utility</span>. Join the #GashyGang revolution.
                </p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="https://jup.ag/swap/SOL-GASHY" target="_blank"
                        class="inline-flex items-center gap-2 px-8 py-4 bg-[#00ffaa] text-black font-display font-bold text-sm tracking-wide rounded-lg hover:bg-[#00ffcc] hover:shadow-[0_4px_24px_rgba(0,255,170,.35)] hover:-translate-y-0.5 transition-all duration-200">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M4.5 19.5l15-15M4.5 4.5h15v15" />
                        </svg>
                        BUY ON JUPITER
                    </a>
                    <a href="https://www.coingecko.com/en/coins/gashy" target="_blank"
                        class="inline-flex items-center gap-2 px-8 py-4 bg-[#8DC351] text-white font-display font-bold text-sm tracking-wide rounded-lg hover:bg-[#7caf43] hover:-translate-y-0.5 transition-all duration-200">
                        🦎 COINGECKO
                    </a>
                    <button onclick="navigator.clipboard.writeText('DokPYQ33k3T9S7EEesvwvuuAtoQb4pY8NWszukKwXWjv');notyf.success('Contract Copied!')"
                        class="inline-flex items-center gap-2 px-8 py-4 bg-transparent text-[#00ffaa] border border-[rgba(0,255,170,.5)] font-display font-bold text-sm tracking-wide rounded-lg hover:bg-[rgba(0,255,170,.08)] hover:-translate-y-0.5 transition-all duration-200">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="9" y="9" width="13" height="13" rx="2" />
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                        </svg>
                        COPY CONTRACT
                    </button>
                </div>
            </section>
            <div class="slide-up stat-grid-wrap grid grid-cols-2 md:grid-cols-4 gap-px bg-[rgba(0,255,170,.1)] border border-[rgba(0,255,170,.1)] rounded-2xl overflow-hidden mb-20">
                <?php
                $stats = [
                    ['PRICE (USD)',  '$' . number_format($price, 6), 'neon'],
                    ['MARKET CAP',  '$' . number_format($mcap / 1000, 1) . 'K', 'white'],
                    ['24H VOLUME',  '$' . number_format($vol / 1000, 2) . 'K', 'white'],
                    ['LIQUIDITY',   '$' . number_format($liq / 1000, 2) . 'K', 'white'],
                ];
                foreach ($stats as $s): ?>
                    <div class="stat-cell bg-[rgba(8,13,26,.85)] hover:bg-[rgba(0,255,170,.04)] transition-colors duration-300 px-6 py-8 text-center">
                        <div class="stat-label font-body text-[.65rem] font-bold tracking-[.15em] text-slate-500 uppercase mb-3"><?= $s[0] ?></div>
                        <div class="font-display font-black leading-none <?= $s[2] === 'neon' ? 'stat-val-neon text-[#00ffaa]' : 'stat-val-white text-slate-900 dark:text-white' ?>" style="font-size:clamp(1.4rem,4vw,2.4rem)"><?= $s[1] ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="slide-up panel-bg bg-[rgba(8,13,26,.85)] border border-[rgba(0,255,170,.12)] rounded-2xl p-8 mb-20 hover:border-[rgba(0,255,170,.25)] hover:shadow-[0_10px_40px_rgba(0,255,170,.05)] transition-all duration-300">
                <div class="flex items-center gap-3 mb-8">
                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="#00ffaa" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                    </svg>
                    <span class="chart-title font-display font-bold text-slate-900 dark:text-white tracking-wide">GASHY / USD — LIVE PRICE</span>
                    <span class="ml-auto font-body text-[.65rem] text-slate-600 tracking-widest">SOLANA</span>
                </div>
                <div id="priceChart" style="height:360px"></div>
            </div>
            <div class="slide-up grid grid-cols-1 lg:grid-cols-[3fr_1fr] gap-6 mb-20">
                <div class="banner-wrap relative h-[400px] md:h-[480px] rounded-2xl overflow-hidden border border-[rgba(0,255,170,.1)]">
                    <?php if (!empty($banners)): ?>
                        <?php foreach ($banners as $bi => $b): ?>
                            <div class="banner-slide absolute inset-0 transition-opacity duration-700" data-index="<?= $bi ?>" style="opacity:<?= $bi === 0 ? '1' : '0' ?>;z-index:<?= $bi === 0 ? 2 : 1 ?>">
                                <img class="banner-img w-full h-full object-cover" src="./<?= htmlspecialchars($b['image_path']) ?>" alt="Banner <?= $bi + 1 ?>">
                                <div class="absolute inset-0 bg-gradient-to-t from-[rgba(3,5,13,.97)] via-[rgba(3,5,13,.45)] to-transparent"></div>
                                <div class="absolute bottom-0 left-0 p-8 z-10">
                                    <span class="inline-block px-3 py-1 bg-[#00ffaa] text-black font-body font-bold text-[.6rem] tracking-widest uppercase rounded mb-4">FEATURED DROP</span>
                                    <h2 class="font-display font-black text-white leading-none tracking-wide mb-6" style="font-size:clamp(1.8rem,5vw,3.5rem)">GASHY <span class="text-[#00ffaa]">MARKETPLACE</span></h2>
                                    <a href="<?= htmlspecialchars($b['link_url']) ?>"
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-[#00ffaa] text-black font-display font-bold text-sm rounded-lg hover:bg-[#00ffcc] transition-colors">
                                        EXPLORE NOW
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <path d="M5 12h14M12 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="absolute bottom-5 right-6 z-10 flex gap-2">
                            <?php foreach ($banners as $bi => $b): ?>
                                <button onclick="goSlide(<?= $bi ?>)" id="dot-<?= $bi ?>"
                                    class="w-2 h-2 rounded-full border-none cursor-pointer transition-all duration-300"
                                    style="background:<?= $bi === 0 ? '#00ffaa' : 'rgba(255,255,255,.35)' ?>;<?= $bi === 0 ? 'box-shadow:0 0 8px #00ffaa' : '' ?>"></button>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="w-full h-full bg-gradient-to-br from-[#1e3a5f] to-[#312e81] flex items-center justify-center">
                            <h2 class="font-display font-black text-white text-center text-4xl px-8">WELCOME TO<br><span class="text-[#00ffaa]">GASHY BAZAAR</span></h2>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="flex flex-col gap-6">
                    <a href="mystery-boxes.php<?= $mystery_box ? '?id=' . $mystery_box['id'] : '' ?>"
                        class="flex-1 rounded-2xl p-7 flex flex-col justify-between min-h-[200px] relative overflow-hidden hover:-translate-y-1 transition-transform duration-300 no-underline text-white"
                        style="background:linear-gradient(135deg,#7c3aed,#ec4899)">
                        <div>
                            <div class="font-display font-black text-xl tracking-wide mb-1">MYSTERY BOX</div>
                            <div class="font-body text-xs opacity-80"><?= $mystery_box ? htmlspecialchars(strtoupper($mystery_box['title'])) : 'No Box Added' ?></div>
                            <?php if ($mystery_box): ?>
                                <div class="font-body text-[.65rem] text-white/50 mt-1"><?= number_format($mystery_box['price_gashy']) ?> G / OPEN</div>
                            <?php endif; ?>
                        </div>
                        <div class="mt-4 py-3 bg-white/15 border border-white/20 rounded-lg text-center font-display font-bold text-xs tracking-widest hover:bg-white/25 transition-colors">OPEN NOW →</div>
                    </a>
                    <a href="lottery.php"
                        class="flex-1 rounded-2xl p-7 flex flex-col justify-between min-h-[200px] relative overflow-hidden hover:-translate-y-1 transition-transform duration-300 no-underline text-white"
                        style="background:linear-gradient(135deg,#059669,#0891b2)">
                        <div>
                            <div class="font-display font-black text-xl tracking-wide mb-1">LOTTERY #<?= htmlspecialchars($lottery['round_number'] ?? '—') ?></div>
                            <div class="font-body text-xs opacity-80">POOL: <?= number_format($lottery['prize_pool'] ?? 0) ?> G</div>
                            <?php if (!empty($lottery['draw_time'])): ?>
                                <div class="font-body text-[.65rem] text-white/50 mt-1">DRAW: <?= date('d M H:i', strtotime($lottery['draw_time'])) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mt-4 py-3 bg-white/15 border border-white/20 rounded-lg text-center font-display font-bold text-xs tracking-widest hover:bg-white/25 transition-colors">BUY TICKET →</div>
                    </a>
                </div>
            </div>
            <div class="slide-up mb-20">
                <div class="text-center mb-12">
                    <h2 class="sec-title font-display font-black text-slate-900 dark:text-white mb-2" style="font-size:clamp(1.5rem,4vw,2.5rem)">WHY CHOOSE <span class="text-[#00ffaa]">$GASHY?</span></h2>
                    <p class="sec-sub font-body text-slate-500 dark:text-slate-400 text-base">More than just a meme — real utility, transparency, and community.</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php
                    $features = [
                        ['🔐', 'SECURITY FIRST', 'Mint and freeze authorities permanently revoked. 100% LP burned. Verified on SolSniffer.'],
                        ['✅', 'COINGECKO LISTED', 'Official listing on CoinGecko with verified pricing and market data across 17+ platforms.'],
                        ['💎', 'ACTIVE STAKING', 'Earn rewards by staking your $GASHY. Non-custodial via Streamflow on Solana.'],
                        ['🎨', 'LIVE NFTS', '51-piece genesis NFT collection with holder perks and exclusive benefits.'],
                        ['🤝', 'TRANSPARENT TEAM', 'Clear founder reserve with on-chain proofs. No hidden allocations or shady practices.'],
                        ['📈', 'REAL ROADMAP', '30/60/90 day milestones with measurable deliverables. Not just empty promises.'],
                    ];
                    foreach ($features as $f): ?>
                        <div class="panel-bg bg-[rgba(8,13,26,.85)] border border-[rgba(0,255,170,.12)] rounded-2xl p-8 hover:border-[rgba(0,255,170,.3)] hover:-translate-y-1 hover:shadow-[0_10px_40px_rgba(0,255,170,.08)] transition-all duration-300">
                            <div class="feat-icon w-14 h-14 rounded-xl bg-[rgba(0,255,170,.08)] border border-[rgba(0,255,170,.2)] flex items-center justify-center text-2xl mb-6"><?= $f[0] ?></div>
                            <div class="feat-title font-display font-bold text-slate-900 dark:text-white tracking-wide text-sm mb-3"><?= $f[1] ?></div>
                            <div class="feat-desc font-body text-slate-500 dark:text-slate-400 text-sm leading-relaxed font-medium"><?= $f[2] ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="slide-up mb-20">
                <div class="flex items-center justify-between flex-wrap gap-4 mb-8">
                    <h2 class="sec-title font-display font-black text-slate-900 dark:text-white" style="font-size:clamp(1.5rem,4vw,2.2rem)">
                        <span class="text-yellow-400">⚡</span> FLASH DEALS
                    </h2>
                    <div class="hidden flex items-center gap-2 px-4 py-2 bg-red-500/10 border border-red-500/30 rounded-lg font-body text-xs font-bold text-red-400">
                        <span class="glow-dot w-1.5 h-1.5 rounded-full bg-red-400 flex-shrink-0"></span>
                        ENDS: <span id="countdown" class="tracking-wide">04:22:19</span>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php $rate = $price > 0 ? 1 / $price : 0;
                    foreach ($flash_deals as $p): $img = json_decode($p['images'])[0] ?? 'assets/placeholder.png';
                        $g = $p['price_usd'] * $rate; ?>
                        <a href="product.php?slug=<?= htmlspecialchars($p['slug']) ?>" class="prod-card panel-bg bg-[rgba(8,13,26,.85)] border border-[rgba(0,255,170,.12)] rounded-2xl overflow-hidden hover:border-[rgba(0,255,170,.3)] hover:-translate-y-1 hover:shadow-[0_10px_40px_rgba(0,255,170,.08)] transition-all duration-300 no-underline block">
                            <div class="aspect-square overflow-hidden relative prod-thumb-bg bg-[#0c1120]">
                                <img class="prod-img w-full h-full object-cover" src="./<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['title']) ?>" loading="lazy">
                                <span class="absolute top-3 right-3 px-2 py-1 bg-red-600 text-white font-body font-bold text-[.6rem] tracking-wide rounded">-20%</span>
                            </div>
                            <div class="p-5">
                                <div class="prod-title font-display font-bold text-slate-900 dark:text-white text-sm tracking-wide mb-3 truncate"><?= htmlspecialchars($p['title']) ?></div>
                                <div class="flex items-center justify-between">
                                    <div class="flex flex-col">
                                        <span class="font-body font-bold text-slate-900 dark:text-white text-sm">$<?= number_format($p['price_usd'], 2, '.', '') ?></span>
                                        <span class="neon-price font-body font-bold text-[#00ffaa] text-xs"><?= number_format($g, 2, '.', '') ?> G</span>
                                    </div>
                                    <span class="old-price font-body text-slate-400 dark:text-slate-500 text-xs line-through">$<?= number_format($p['price_usd'] * 1.2, 2, '.', '') ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-[2fr_1fr] gap-8 mb-20">
                <div class="slide-up">
                    <h2 class="sec-title font-display font-black text-slate-900 dark:text-white mb-8" style="font-size:clamp(1.5rem,4vw,2rem)">NEW ARRIVALS</h2>
                    <div class="flex flex-col gap-3">
                        <?php $rate = $price > 0 ? 1 / $price : 0;
                        foreach ($new_arrivals as $p): $img = json_decode($p['images'])[0] ?? 'assets/placeholder.png';
                            $g = $p['price_usd'] * $rate; ?>
                            <a href="product.php?slug=<?= htmlspecialchars($p['slug']) ?>" class="flex items-center gap-5 p-5 panel-bg bg-[rgba(8,13,26,.85)] border border-[rgba(0,255,170,.1)] rounded-xl hover:border-[rgba(0,255,170,.25)] hover:bg-[rgba(0,255,170,.02)] hover:translate-x-1 transition-all duration-300 no-underline">
                                <div class="prod-thumb-bg w-20 h-20 rounded-lg overflow-hidden flex-shrink-0 border border-white/5 bg-[#0c1120]">
                                    <img src="./<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['title']) ?>" loading="lazy" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="prod-title font-display font-bold text-slate-900 dark:text-white text-sm tracking-wide mb-1 truncate"><?= htmlspecialchars($p['title']) ?></div>
                                    <div class="prod-type font-body text-[.6rem] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest"><?= htmlspecialchars($p['type']) ?></div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <div class="font-body font-bold text-slate-900 dark:text-white text-xs">$<?= number_format($p['price_usd'], 2, '.', '') ?></div>
                                    <div class="neon-price font-body font-bold text-[#00ffaa] text-base mb-1"><?= number_format($g, 2, '.', '') ?> G</div>
                                    <span class="neon-pill inline-block px-3 py-1 font-body font-bold text-[.6rem] tracking-widest text-[#00ffaa] bg-[rgba(0,255,170,.08)] border border-[rgba(0,255,170,.2)] rounded-full hover:bg-[rgba(0,255,170,.15)] transition-colors">BUY NOW</span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="slide-up">
                    <h2 class="sec-title font-display font-black text-slate-900 dark:text-white mb-8" style="font-size:clamp(1.5rem,4vw,2rem)">TOP SELLERS</h2>
                    <div class="panel-bg bg-[rgba(8,13,26,.85)] border border-[rgba(0,255,170,.12)] rounded-2xl p-6">
                        <?php
                        $rankStyles = [
                            'background:linear-gradient(135deg,#f59e0b,#ef4444);color:#000',
                            'background:linear-gradient(135deg,#6b7280,#9ca3af);color:#000',
                            'background:linear-gradient(135deg,#92400e,#b45309);color:#fff',
                        ];
                        foreach ($top_sellers as $i => $s): ?>
                            <div class="seller-divider flex items-center gap-4 py-4 border-b border-black/[.06] dark:border-white/[.04] last:border-0">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center font-display font-black text-base flex-shrink-0"
                                    style="<?= $rankStyles[$i] ?? 'background:rgba(0,255,170,.08);border:1px solid rgba(0,255,170,.2);color:#00ffaa' ?>"><?= $i + 1 ?></div>
                                <div class="flex-1 min-w-0">
                                    <div class="seller-name font-display font-bold text-slate-900 dark:text-white text-sm tracking-wide truncate"><?= htmlspecialchars($s['store_name']) ?></div>
                                    <div class="flex items-center gap-1 font-body text-xs text-yellow-500 dark:text-yellow-400 mt-0.5">
                                        <svg class="w-2.5 h-2.5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <?= htmlspecialchars($s['rating']) ?>
                                    </div>
                                </div>
                                <div class="seller-sales font-body text-xs font-bold text-slate-400 dark:text-slate-500 flex-shrink-0"><?= htmlspecialchars($s['total_sales']) ?> SOLD</div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-6 rounded-2xl p-7 text-center" style="background:linear-gradient(135deg,#1e40af,#7c3aed)">
                        <div class="font-display font-black text-white text-xl tracking-wide mb-2">BECOME A SELLER</div>
                        <div class="font-body text-xs text-white/70 mb-6">Launch your own crypto store today</div>
                        <a href="seller.php"
                            class="flex items-center justify-center gap-2 w-full py-3.5 bg-[#00ffaa] text-black font-display font-bold text-sm rounded-lg hover:bg-[#00ffcc] hover:shadow-[0_4px_20px_rgba(0,255,170,.3)] transition-all duration-200">
                            APPLY NOW
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var basePrice = <?= $price ?>;
            var points = 48,
                cats = [],
                data = [],
                last = basePrice;
            var now = Date.now();

            function nextPrice(p) {
                var d = (Math.random() - .48) * .035;
                var s = Math.random() < .08 ? (Math.random() - .5) * .12 : 0;
                return parseFloat(Math.max(p * (1 + d + s), p * .001).toFixed(8));
            }
            for (var i = points - 1; i >= 0; i--) {
                last = nextPrice(last);
                data.push(last);
                cats.push(new Date(now - i * 1800000).toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                }));
            }

            function isDark() {
                return document.documentElement.classList.contains('dark');
            }

            function neonColor() {
                return isDark() ? '#00ffaa' : '#007a55';
            }

            function labelColor() {
                return isDark() ? 'rgba(148,163,184,.45)' : '#64748b';
            }

            function gridColor() {
                return isDark() ? 'rgba(255,255,255,.05)' : 'rgba(0,0,0,.06)';
            }
            var chart = new ApexCharts(document.querySelector('#priceChart'), {
                series: [{
                    name: 'GASHY',
                    data: data
                }],
                chart: {
                    type: 'area',
                    height: 360,
                    background: 'transparent',
                    toolbar: {
                        show: false
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 600
                    },
                    zoom: {
                        enabled: false
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 2,
                    colors: [neonColor()]
                },
                colors: [neonColor()],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: .22,
                        opacityTo: 0,
                        stops: [0, 100]
                    }
                },
                dataLabels: {
                    enabled: false
                },
                markers: {
                    size: 0,
                    hover: {
                        size: 5
                    }
                },
                xaxis: {
                    categories: cats,
                    tickAmount: 6,
                    labels: {
                        style: {
                            colors: labelColor(),
                            fontFamily: 'Rajdhani,sans-serif',
                            fontSize: '11px'
                        }
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
                            colors: labelColor(),
                            fontFamily: 'Rajdhani,sans-serif',
                            fontSize: '11px'
                        },
                        formatter: function(v) {
                            return v < .001 ? '$' + v.toFixed(6) : v < 1 ? '$' + v.toFixed(4) : '$' + v.toFixed(2);
                        }
                    }
                },
                grid: {
                    borderColor: gridColor(),
                    strokeDashArray: 4,
                    padding: {
                        left: 10,
                        right: 20
                    }
                },
                tooltip: {
                    theme: isDark() ? 'dark' : 'light',
                    style: {
                        fontFamily: 'Rajdhani,sans-serif',
                        fontSize: '12px'
                    },
                    y: {
                        formatter: function(v) {
                            return '$' + v.toFixed(6);
                        }
                    }
                },
                theme: {
                    mode: isDark() ? 'dark' : 'light'
                }
            });
            chart.render();
            setInterval(function() {
                last = nextPrice(last);
                data.push(last);
                data.shift();
                var t = new Date().toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });
                cats.push(t);
                cats.shift();
                chart.updateSeries([{
                    data: data
                }], true);
                chart.updateOptions({
                    xaxis: {
                        categories: cats
                    }
                }, false, false);
            }, 15000);
            new MutationObserver(function() {
                chart.updateOptions({
                    stroke: {
                        colors: [neonColor()]
                    },
                    colors: [neonColor()],
                    xaxis: {
                        labels: {
                            style: {
                                colors: labelColor()
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: labelColor()
                            }
                        }
                    },
                    grid: {
                        borderColor: gridColor()
                    },
                    theme: {
                        mode: isDark() ? 'dark' : 'light'
                    },
                    tooltip: {
                        theme: isDark() ? 'dark' : 'light'
                    }
                }, false, true);
            }).observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class']
            });
            (function() {
                var el = document.getElementById('countdown');
                var s = 4 * 3600 + 22 * 60 + 19;
                setInterval(function() {
                    if (s <= 0) return;
                    s--;
                    var h = Math.floor(s / 3600),
                        m = Math.floor((s % 3600) / 60),
                        ss = s % 60;
                    el.textContent = [h, m, ss].map(function(x) {
                        return x < 10 ? '0' + x : x;
                    }).join(':');
                }, 1000);
            })();
        });
    </script>
    <script>
        (function() {
            var slides = document.querySelectorAll('.banner-slide');
            var dots = document.querySelectorAll('[id^="dot-"]');
            var cur = 0,
                total = slides.length;
            if (total < 2) return;
            window.goSlide = function(n) {
                slides[cur].style.opacity = '0';
                slides[cur].style.zIndex = '1';
                dots[cur].style.background = 'rgba(255,255,255,.35)';
                dots[cur].style.boxShadow = '';
                cur = ((n % total) + total) % total;
                slides[cur].style.opacity = '1';
                slides[cur].style.zIndex = '2';
                dots[cur].style.background = '#00ffaa';
                dots[cur].style.boxShadow = '0 0 8px #00ffaa';
            };
            setInterval(function() {
                goSlide(cur + 1);
            }, 5000);
        })();
    </script>
    <?php require_once 'footer.php'; ?>
</body>

</html>