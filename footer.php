<style>
    @keyframes pulse-network {

        0%,
        100% {
            opacity: 1;
            transform: scale(1)
        }

        50% {
            opacity: .85;
            transform: scale(1.04)
        }
    }

    .footer-shell {
        background: linear-gradient(180deg, rgba(10, 14, 26, .94), rgba(19, 24, 36, .98));
        backdrop-filter: blur(20px);
        border-top: 1px solid rgba(0, 255, 170, .08)
    }

    .footer-link {
        position: relative;
        transition: .25s
    }

    .footer-link:after {
        content: '';
        position: absolute;
        left: 0;
        bottom: -2px;
        width: 0;
        height: 2px;
        background: linear-gradient(90deg, #00ffaa, #00d48f);
        transition: .25s
    }

    .footer-link:hover {
        color: #00ffaa;
        transform: translateX(4px)
    }

    .footer-link:hover:after {
        width: 100%
    }

    .social-btn {
        background: linear-gradient(135deg, rgba(0, 255, 170, .08), rgba(139, 92, 246, .08));
        border: 1px solid rgba(0, 255, 170, .12);
        transition: .25s
    }

    .social-btn:hover {
        transform: translateY(-4px);
        background: linear-gradient(135deg, #00ffaa, #00d48f);
        color: #071019;
        box-shadow: 0 10px 24px rgba(0, 255, 170, .22)
    }

    .network-badge {
        animation: pulse-network 3s ease-in-out infinite
    }

    html:not(.dark) .footer-shell {
        background: linear-gradient(180deg, rgba(248, 250, 252, .98), rgba(255, 255, 255, .98));
        border-top: 1px solid rgba(0, 163, 114, .12)
    }

    html:not(.dark) .social-btn {
        background: linear-gradient(135deg, rgba(0, 163, 114, .06), rgba(139, 92, 246, .06));
        border-color: rgba(0, 163, 114, .16)
    }

    html:not(.dark) .social-btn:hover {
        color: #fff
    }
</style>

<footer class="lg:pl-[280px] footer-shell mt-auto transition-colors duration-300">
    <div class="max-w-[1900px] mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-8">

            <div>
                <a href="app.php" class="inline-flex items-center gap-3 mb-5 group">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center shadow-xl shadow-primary-500/20 group-hover:scale-105 transition-all overflow-hidden">
                        <img src="./public/img/logo.png" class="w-full h-full object-cover" alt="logo">
                    </div>
                    <div class="leading-none">
                        <div class="text-2xl font-black tracking-tight text-gray-900 dark:text-white">GASHY</div>
                        <div class="text-[10px] tracking-[0.35em] font-black text-primary-500 mt-1">BAZAAR</div>
                    </div>
                </a>
                <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-400 max-w-sm">Next generation decentralized marketplace for digital goods, collectibles, rewards and premium blockchain commerce.</p>
                <div class="mt-4 flex items-center gap-2 text-xs text-gray-500">
                    <span class="w-2 h-2 rounded-full bg-primary-500"></span>
                    <span class="font-bold">Audited & Verified</span>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-black uppercase tracking-[0.25em] text-gray-900 dark:text-white mb-5">Marketplace</h3>
                <ul class="space-y-3 text-sm">
                    <li><a href="market.php" class="footer-link text-gray-600 dark:text-gray-400 inline-block">Browse Products</a></li>
                    <li><a href="auctions.php" class="footer-link text-gray-600 dark:text-gray-400 inline-block">Live Auctions</a></li>
                    <li><a href="mystery-boxes.php" class="footer-link text-gray-600 dark:text-gray-400 inline-block">Mystery Boxes</a></li>
                    <li><a href="lottery.php" class="footer-link text-gray-600 dark:text-gray-400 inline-block">Lottery Pool</a></li>
                    <li><a href="quests.php" class="footer-link text-gray-600 dark:text-gray-400 inline-block">Quest Board</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-black uppercase tracking-[0.25em] text-gray-900 dark:text-white mb-5">Resources</h3>
                <ul class="space-y-3 text-sm">
                    <li><a href="faq.php" class="footer-link text-gray-600 dark:text-gray-400 inline-block">FAQ & Help</a></li>
                    <li><a href="terms.php" class="footer-link text-gray-600 dark:text-gray-400 inline-block">Terms of Service</a></li>
                    <li><a href="privacy.php" class="footer-link text-gray-600 dark:text-gray-400 inline-block">Privacy Policy</a></li>
                    <li><a href="seller.php" class="footer-link text-gray-600 dark:text-gray-400 inline-block">Become a Seller</a></li>
                    <li><a href="https://www.coingecko.com/en/coins/gashy" target="_blank" class="footer-link text-gray-600 dark:text-gray-400 inline-block">CoinGecko</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-black uppercase tracking-[0.25em] text-gray-900 dark:text-white mb-5">Stay Connected</h3>
                <div class="flex gap-3 mb-5 flex-wrap">
                    <a href="https://twitter.com/gashytoken" target="_blank" class="social-btn w-11 h-11 rounded-xl flex items-center justify-center text-gray-600 dark:text-gray-400">X</a>
                    <a href="https://t.me/gashygang" target="_blank" class="social-btn w-11 h-11 rounded-xl flex items-center justify-center text-gray-600 dark:text-gray-400">TG</a>
                    <a href="https://discord.gg/gashy" target="_blank" class="social-btn w-11 h-11 rounded-xl flex items-center justify-center text-gray-600 dark:text-gray-400">DC</a>
                    <a href="https://github.com/gashy" target="_blank" class="social-btn w-11 h-11 rounded-xl flex items-center justify-center text-gray-600 dark:text-gray-400">GH</a>
                </div>
                <p class="text-xs leading-relaxed text-gray-600 dark:text-gray-400">Join the community for drops, rewards, governance and major announcements.</p>
            </div>

        </div>

        <div class="mt-10 pt-6 border-t border-gray-200 dark:border-white/10 flex flex-col xl:flex-row gap-5 xl:items-center xl:justify-between">

            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <p class="text-xs text-gray-600 dark:text-gray-500 font-mono">© 2026 GASHY Project. All rights reserved.</p>
                <div class="text-xs text-gray-500 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-accent-500"></span>
                    Built on Solana
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <div class="network-badge px-4 py-2 rounded-xl border border-primary-500/20 bg-primary-500/10 text-primary-500 text-[10px] font-black tracking-[0.25em]">
                    SOLANA MAINNET
                </div>
                <a href="https://solscan.io" target="_blank" class="text-xs text-gray-600 dark:text-gray-400 hover:text-primary-500 transition-colors font-mono">View on Solscan</a>
            </div>

        </div>
    </div>
</footer>

<script src="./public/js/core.js"></script>
<?php
$page = basename($_SERVER['PHP_SELF'], '.php');
$path = "./public/js/pages/{$page}.js";
if (file_exists($path)) echo "<script src='{$path}'></script>";
?>
</body>

</html>