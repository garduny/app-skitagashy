<style>
    @keyframes pulse-network {

        0%,
        100% {
            opacity: 1;
            transform: scale(1)
        }

        50% {
            opacity: 0.8;
            transform: scale(1.05)
        }
    }

    .footer-gradient {
        background: linear-gradient(180deg, rgba(10, 14, 26, 0.95) 0%, rgba(19, 24, 36, 0.98) 100%);
        backdrop-filter: blur(20px);
        border-top: 1px solid rgba(0, 255, 170, 0.08)
    }

    .footer-link {
        transition: all 0.3s ease;
        position: relative
    }

    .footer-link::before {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: linear-gradient(90deg, #00ffaa, #00d48f);
        transition: width 0.3s ease
    }

    .footer-link:hover::before {
        width: 100%
    }

    .footer-link:hover {
        color: #00ffaa;
        transform: translateX(4px)
    }

    .social-icon {
        background: linear-gradient(135deg, rgba(0, 255, 170, 0.1), rgba(139, 92, 246, 0.1));
        border: 1px solid rgba(0, 255, 170, 0.15);
        transition: all 0.3s ease
    }

    .social-icon:hover {
        background: linear-gradient(135deg, #00ffaa, #00d48f);
        border-color: #00ffaa;
        transform: translateY(-4px) rotate(5deg);
        box-shadow: 0 8px 25px rgba(0, 255, 170, 0.3)
    }

    .network-badge {
        animation: pulse-network 3s ease-in-out infinite
    }

    html:not(.dark) .footer-gradient {
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.98) 0%, rgba(255, 255, 255, 0.98) 100%);
        border-top: 1px solid rgba(0, 212, 143, 0.15)
    }

    html:not(.dark) .social-icon {
        background: linear-gradient(135deg, rgba(0, 212, 143, 0.08), rgba(139, 92, 246, 0.08));
        border: 1px solid rgba(0, 212, 143, 0.2)
    }

    html:not(.dark) .social-icon:hover {
        background: linear-gradient(135deg, #00d48f, #00ffaa)
    }
</style>
<footer class="lg:pl-72 footer-gradient mt-auto transition-colors duration-300 relative overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none opacity-5">
        <div class="absolute top-0 left-0 w-full h-full bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAwIDEwIEwgNDAgMTAgTSAxMCAwIEwgMTAgNDAgTSAwIDIwIEwgNDAgMjAgTSAyMCAwIEwgMjAgNDAgTSAwIDMwIEwgNDAgMzAgTSAzMCAwIEwgMzAgNDAiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzAwZmZhYSIgb3BhY2l0eT0iMC4xIiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')]"></div>
    </div>
    <div class="relative z-10 max-w-[1920px] mx-auto px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
            <div class="col-span-1">
                <a href="app.php" class="inline-flex items-center gap-3 mb-6 group">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#00ffaa] via-[#00d48f] to-[#00ffaa] flex items-center justify-center shadow-xl shadow-[#00ffaa]/30 group-hover:shadow-[#00ffaa]/50 transition-all group-hover:scale-110 group-hover:rotate-3">
                        <img src="./public/img/logo.png" style="border-radius: 1rem;" width="100px" alt="logo">
                    </div>
                    <div>
                        <span class="block text-2xl font-black tracking-tighter text-gray-900 dark:text-white">GASHY</span>
                        <span class="block text-xs font-black tracking-widest text-[#00ffaa] -mt-1">BAZAAR</span>
                    </div>
                </a>
                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed mb-6">The premier decentralized marketplace built on Solana. Trade digital goods, NFTs, and exclusive items with blazing-fast transactions.</p>
                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-500">
                    <svg class="w-4 h-4 text-[#00ffaa]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-bold">Audited & Verified</span>
                </div>
            </div>
            <div>
                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest mb-6">Marketplace</h3>
                <ul class="space-y-4 text-sm">
                    <li><a href="market.php" class="footer-link text-gray-600 dark:text-gray-400 font-medium inline-block">Browse Products</a></li>
                    <li><a href="auctions.php" class="footer-link text-gray-600 dark:text-gray-400 font-medium inline-block">Live Auctions</a></li>
                    <li><a href="mystery-boxes.php" class="footer-link text-gray-600 dark:text-gray-400 font-medium inline-block">Mystery Boxes</a></li>
                    <li><a href="lottery.php" class="footer-link text-gray-600 dark:text-gray-400 font-medium inline-block">Lottery Pool</a></li>
                    <li><a href="quests.php" class="footer-link text-gray-600 dark:text-gray-400 font-medium inline-block">Quest Board</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest mb-6">Resources</h3>
                <ul class="space-y-4 text-sm">
                    <li><a href="faq.php" class="footer-link text-gray-600 dark:text-gray-400 font-medium inline-block">FAQ & Help</a></li>
                    <li><a href="terms.php" class="footer-link text-gray-600 dark:text-gray-400 font-medium inline-block">Terms of Service</a></li>
                    <li><a href="privacy.php" class="footer-link text-gray-600 dark:text-gray-400 font-medium inline-block">Privacy Policy</a></li>
                    <li><a href="seller.php" class="footer-link text-gray-600 dark:text-gray-400 font-medium inline-block">Become a Seller</a></li>
                    <li><a href="https://www.coingecko.com/en/coins/gashy" target="_blank" class="footer-link text-gray-600 dark:text-gray-400 font-medium inline-block flex items-center gap-1">
                            CoinGecko
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest mb-6">Stay Connected</h3>
                <div class="flex gap-3 mb-8">
                    <a href="https://twitter.com/gashytoken" target="_blank" class="social-icon w-12 h-12 rounded-xl flex items-center justify-center text-gray-600 dark:text-gray-400 shadow-lg" title="Twitter">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                        </svg>
                    </a>
                    <a href="https://t.me/gashygang" target="_blank" class="social-icon w-12 h-12 rounded-xl flex items-center justify-center text-gray-600 dark:text-gray-400 shadow-lg" title="Telegram">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.18-.357.295-.6.295-.002 0-.003 0-.005 0l.213-3.054 5.56-5.022c.24-.213-.054-.334-.373-.121l-6.869 4.326-2.96-.924c-.64-.203-.658-.64.135-.954l11.566-4.458c.538-.196 1.006.128.832.941z" />
                        </svg>
                    </a>
                    <a href="https://discord.gg/gashy" target="_blank" class="social-icon w-12 h-12 rounded-xl flex items-center justify-center text-gray-600 dark:text-gray-400 shadow-lg" title="Discord">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.317 4.37a19.791 19.791 0 00-4.885-1.515.074.074 0 00-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 00-5.487 0 12.64 12.64 0 00-.617-1.25.077.077 0 00-.079-.037A19.736 19.736 0 003.677 4.37a.07.07 0 00-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 00.031.057 19.9 19.9 0 005.993 3.03.078.078 0 00.084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 00-.041-.106 13.107 13.107 0 01-1.872-.892.077.077 0 01-.008-.128 10.2 10.2 0 00.372-.292.074.074 0 01.077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 01.078.01c.12.098.246.198.373.292a.077.077 0 01-.006.127 12.299 12.299 0 01-1.873.892.077.077 0 00-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 00.084.028 19.839 19.839 0 006.002-3.03.077.077 0 00.032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 00-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z" />
                        </svg>
                    </a>
                    <a href="https://github.com/gashy" target="_blank" class="social-icon w-12 h-12 rounded-xl flex items-center justify-center text-gray-600 dark:text-gray-400 shadow-lg" title="GitHub">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">Join our community and stay updated with the latest drops, rewards, and announcements.</p>
            </div>
        </div>
        <div class="border-t-2 border-gray-200 dark:border-white/10 pt-8 flex flex-col lg:flex-row justify-between items-center gap-6">
            <div class="flex flex-col sm:flex-row items-center gap-4">
                <p class="text-xs text-gray-600 dark:text-gray-500 font-mono">© 2026 GASHY Project. All rights reserved.</p>
                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-600">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                    </svg>
                    Built on Solana
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-4">
                <div class="network-badge flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-[#00ffaa]/10 to-[#00d48f]/10 dark:from-[#00ffaa]/10 dark:to-[#00d48f]/10 from-[#00d48f]/15 to-[#00ffaa]/15 border-2 border-[#00ffaa]/30 shadow-lg">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#00ffaa] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#00ffaa]"></span>
                    </span>
                    <span class="text-[10px] font-black text-[#00ffaa] tracking-widest">SOLANA MAINNET</span>
                </div>
                <a href="https://solscan.io" target="_blank" class="text-xs text-gray-600 dark:text-gray-400 hover:text-[#00ffaa] transition-colors font-mono flex items-center gap-1">
                    View on Solscan
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</footer>
<script src="./public/js/core.js"></script>
<?php
$page = basename($_SERVER['PHP_SELF'], '.php');
$path = "./public/js/pages/{$page}.js";
if (file_exists($path)) {
    echo "<script src='{$path}'></script>";
}
?>
</body>

</html>