<style>
    @keyframes footerline {
        0% {
            background-position: 200% center
        }

        100% {
            background-position: -200% center
        }
    }

    @keyframes footerpulse {

        0%,
        100% {
            transform: scale(1);
            opacity: 1
        }

        50% {
            transform: scale(1.15);
            opacity: .7
        }
    }

    .admin-footer {
        background: linear-gradient(135deg, rgba(10, 14, 26, .96), rgba(19, 24, 36, .96));
        backdrop-filter: blur(18px);
        border-top: 1px solid rgba(0, 255, 170, .12);
        position: relative;
        overflow: hidden
    }

    .admin-footer:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, #00ffaa, transparent);
        background-size: 200% 100%;
        animation: footerline 3s linear infinite
    }

    .footer-btn {
        background: linear-gradient(135deg, rgba(0, 255, 170, .08), rgba(139, 92, 246, .08));
        border: 1px solid rgba(0, 255, 170, .2);
        transition: .25s ease;
        position: relative;
        overflow: hidden
    }

    .footer-btn:before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .08), transparent);
        transform: translateX(-120%);
        transition: transform .6s
    }

    .footer-btn:hover:before {
        transform: translateX(120%)
    }

    .footer-btn:hover {
        background: linear-gradient(135deg, #00ffaa, #00d48f);
        color: #000;
        border-color: #00ffaa;
        box-shadow: 0 12px 30px rgba(0, 255, 170, .22);
        transform: translateY(-2px)
    }

    .footer-dot {
        animation: footerpulse 1.6s infinite
    }

    html:not(.dark) .admin-footer {
        background: linear-gradient(135deg, rgba(255, 255, 255, .98), rgba(248, 250, 252, .98));
        border-top: 1px solid rgba(0, 212, 143, .12)
    }

    html:not(.dark) .footer-btn {
        background: linear-gradient(135deg, rgba(0, 212, 143, .08), rgba(139, 92, 246, .06));
        border-color: rgba(0, 212, 143, .18)
    }

    html:not(.dark) .footer-btn:hover {
        color: #000
    }
</style>

<footer class="admin-footer lg:pl-72 mt-auto py-6 transition-colors duration-300">
    <div class="px-6 flex flex-col xl:flex-row items-center justify-between gap-5">
        <div class="flex flex-col md:flex-row items-center gap-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#00ffaa] to-[#00d48f] p-[2px] shadow-lg">
                    <img src="../public/img/logo.png" alt="GASHY" class="w-full h-full rounded-[10px] object-cover">
                </div>
                <div>
                    <div class="text-xs font-black tracking-wider text-gray-800 dark:text-gray-200">&copy; <?= date('Y') ?> GASHY BAZAAR</div>
                    <div class="text-[10px] uppercase font-bold tracking-[.22em] text-gray-500">Admin Control Panel</div>
                </div>
            </div>

            <div class="hidden md:block w-px h-8 bg-gray-300 dark:bg-white/10"></div>

            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                <div class="w-2 h-2 rounded-full bg-green-500 footer-dot"></div>
                <div class="text-[10px] font-black tracking-widest text-gray-700 dark:text-gray-300">SYSTEM ONLINE</div>
            </div>

            <div class="hidden xl:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                <i class="fa-solid fa-shield-halved text-[#00ffaa] text-xs"></i>
                <div class="text-[10px] font-black tracking-widest text-gray-700 dark:text-gray-300">SECURED</div>
            </div>
        </div>

        <div class="flex items-center gap-3 flex-wrap justify-center">
            <a href="app.php" class="footer-btn px-4 py-2 rounded-xl text-xs font-black text-gray-700 dark:text-gray-200 flex items-center gap-2">
                <i class="fa-solid fa-chart-line text-[11px]"></i>
                <span>DASHBOARD</span>
            </a>

            <a href="../" target="_blank" class="footer-btn px-5 py-2.5 rounded-xl text-sm font-black text-gray-700 dark:text-gray-200 flex items-center gap-3 shadow-xl">
                <i class="fa-solid fa-globe"></i>
                <span>VIEW LIVE SITE</span>
                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
            </a>
        </div>
    </div>
</footer>
</body>

</html>