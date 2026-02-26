<style>
    @keyframes shimmer-footer {
        0% {
            background-position: 200% center
        }

        100% {
            background-position: -200% center
        }
    }

    .admin-footer {
        background: linear-gradient(135deg, rgba(10, 14, 26, 0.95), rgba(19, 24, 36, 0.95));
        backdrop-filter: blur(20px);
        border-top: 2px solid rgba(0, 255, 170, 0.1);
        position: relative;
        overflow: hidden
    }

    .admin-footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, #00ffaa, transparent);
        background-size: 200% 100%;
        animation: shimmer-footer 3s linear infinite
    }

    .live-site-btn {
        background: linear-gradient(135deg, rgba(0, 255, 170, 0.1), rgba(139, 92, 246, 0.1));
        border: 2px solid rgba(0, 255, 170, 0.2);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden
    }

    .live-site-btn::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transform: translateX(-100%);
        transition: transform 0.6s
    }

    .live-site-btn:hover::before {
        transform: translateX(100%)
    }

    .live-site-btn:hover {
        background: linear-gradient(135deg, #00ffaa, #00d48f);
        color: #000;
        border-color: #00ffaa;
        box-shadow: 0 8px 25px rgba(0, 255, 170, 0.3);
        transform: translateY(-2px)
    }

    html:not(.dark) .admin-footer {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.98));
        border-top: 2px solid rgba(0, 212, 143, 0.15)
    }

    html:not(.dark) .live-site-btn {
        background: linear-gradient(135deg, rgba(0, 212, 143, 0.08), rgba(139, 92, 246, 0.08));
        border: 2px solid rgba(0, 212, 143, 0.25)
    }

    html:not(.dark) .live-site-btn:hover {
        color: #000
    }
</style>
<footer class="admin-footer lg:pl-72 mt-auto py-8 transition-colors duration-300 shadow-2xl">
    <div class="flex flex-col md:flex-row justify-between items-center px-6 gap-6">
        <div class="flex flex-col sm:flex-row items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#00ffaa] to-[#00d48f] flex items-center justify-center shadow-lg">
                    <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-black text-gray-700 dark:text-gray-300" style="font-family:'Space Mono',monospace">&copy; <?= date('Y') ?> GASHY BAZAAR</p>
                    <p class="text-[9px] text-gray-500 dark:text-gray-500 font-bold uppercase tracking-widest -mt-0.5">Admin System v2.0.0</p>
                </div>
            </div>
            <div class="hidden sm:block w-px h-8 bg-gray-300 dark:bg-white/10"></div>
            <div class="flex items-center gap-2 text-[10px] text-gray-500 dark:text-gray-500 font-mono">
                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                <span class="font-bold">SYSTEM ONLINE</span>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="hidden lg:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                <svg class="w-3 h-3 text-[#00ffaa]" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="text-[10px] font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Secured</span>
            </div>
            <a href="../" target="_blank" class="live-site-btn flex items-center gap-3 px-6 py-3 rounded-xl font-black text-sm shadow-xl">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                </svg>
                <span>VIEW LIVE SITE</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </a>
        </div>
    </div>
</footer>
</body>

</html>