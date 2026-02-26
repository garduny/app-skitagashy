<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>GASHY BAZAAR | Next Gen Crypto Market</title>
    <link rel="shortcut icon" href="./public/img/logo.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script src="https://unpkg.com/@solana/web3.js@latest/lib/index.iife.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            900: '#0a0e1a',
                            800: '#131824',
                            700: '#1a1f2e',
                            600: '#222938'
                        },
                        primary: {
                            500: '#00ffaa',
                            600: '#00d48f',
                            700: '#00b377'
                        },
                        accent: {
                            500: '#8B5CF6',
                            600: '#7C3AED'
                        },
                        glow: {
                            cyan: '#00ffff',
                            purple: '#a855f7'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    },
                    animation: {
                        'gradient': 'gradient 8s linear infinite',
                        'pulse-glow': 'pulse-glow 2s ease-in-out infinite',
                        'slide-down': 'slide-down 0.3s ease-out'
                    },
                    keyframes: {
                        gradient: {
                            '0%,100%': {
                                backgroundPosition: '0% 50%'
                            },
                            '50%': {
                                backgroundPosition: '100% 50%'
                            }
                        },
                        'pulse-glow': {
                            '0%,100%': {
                                boxShadow: '0 0 20px rgba(0,255,170,0.3)'
                            },
                            '50%': {
                                boxShadow: '0 0 40px rgba(0,255,170,0.6)'
                            }
                        },
                        'slide-down': {
                            '0%': {
                                transform: 'translateY(-100%)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateY(0)',
                                opacity: '1'
                            }
                        }
                    },
                    boxShadow: {
                        glow: '0 0 30px rgba(0,255,170,0.2), 0 0 60px rgba(139,92,246,0.1)',
                        'glow-lg': '0 0 40px rgba(0,255,170,0.4), 0 0 80px rgba(139,92,246,0.2)',
                        'inner-glow': 'inset 0 0 20px rgba(0,255,170,0.1)'
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #0a0e1a;
            overflow-x: hidden
        }

        /* ── DARK MODE HEADER ── */
        .glass-header {
            background: rgba(13, 17, 28, 0.9);
            backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid rgba(0, 255, 170, 0.08);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3), 0 0 20px rgba(0, 255, 170, 0.05);
            height: 80px;
        }

        .glass-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(0, 255, 170, 0.3) 50%, transparent);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {

            0%,
            100% {
                opacity: 0
            }

            50% {
                opacity: 1
            }
        }

        .logo-glow {
            filter: drop-shadow(0 0 10px rgba(0, 255, 170, 0.4)) drop-shadow(0 0 20px rgba(139, 92, 246, 0.2))
        }

        /* ── SEARCH ── */
        .search-input {
            background: rgba(26, 31, 46, 0.6);
            border: 1px solid rgba(0, 255, 170, 0.15);
            color: #fff;
            transition: all 0.3s ease;
        }

        .search-input::placeholder {
            color: rgba(148, 163, 184, 0.5)
        }

        .search-input:focus {
            background: rgba(26, 31, 46, 0.9);
            border-color: rgba(0, 255, 170, 0.5);
            box-shadow: 0 0 20px rgba(0, 255, 170, 0.1), inset 0 0 15px rgba(0, 255, 170, 0.05);
            outline: none;
        }

        /* ── BUTTONS ── */
        .btn-glow {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease
        }

        .btn-glow::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(0, 255, 170, 0.1), transparent);
            transform: rotate(45deg);
            transition: all 0.6s ease;
        }

        .btn-glow:hover::before {
            animation: shine 0.8s ease-in-out
        }

        @keyframes shine {
            0% {
                left: -50%
            }

            100% {
                left: 150%
            }
        }

        .btn-primary {
            background: linear-gradient(135deg, #00d48f 0%, #00ffaa 100%);
            box-shadow: 0 4px 15px rgba(0, 255, 170, 0.3);
            color: #0a0e1a;
        }

        .btn-primary:hover {
            box-shadow: 0 6px 25px rgba(0, 255, 170, 0.5);
            transform: translateY(-2px)
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(156, 163, 175, 1);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(0, 255, 170, 0.3);
            color: #00ffaa;
        }

        /* ── BALANCE CARD ── */
        .balance-card {
            background: linear-gradient(135deg, rgba(0, 212, 143, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
            border: 1px solid rgba(0, 255, 170, 0.2);
            border-radius: 12px;
            padding: 8px 16px;
        }

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px
        }

        ::-webkit-scrollbar-track {
            background: rgba(26, 31, 46, 0.3)
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #00d48f, #8B5CF6);
            border-radius: 10px
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #00ffaa, #a855f7)
        }

        /* ══════════════════════════════════════
   LIGHT MODE OVERRIDES — ALL EXPLICIT
   ══════════════════════════════════════ */
        html:not(.dark) body {
            background: #f1f5f9;
            color: #0f172a;
        }

        html:not(.dark) .glass-header {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        html:not(.dark) .glass-header::before {
            background: linear-gradient(90deg, transparent, rgba(0, 180, 120, 0.2) 50%, transparent);
        }

        /* Logo text */
        html:not(.dark) .logo-text-main {
            background: linear-gradient(135deg, #0f172a, #00a372) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
        }

        html:not(.dark) .logo-text-sub {
            color: #00a372 !important
        }

        /* Search */
        html:not(.dark) .search-input {
            background: rgba(241, 245, 249, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.12);
            color: #0f172a;
        }

        html:not(.dark) .search-input::placeholder {
            color: rgba(100, 116, 139, 0.7)
        }

        html:not(.dark) .search-input:focus {
            background: #fff;
            border-color: rgba(0, 163, 114, 0.6);
            box-shadow: 0 0 0 3px rgba(0, 163, 114, 0.12);
            color: #0f172a;
        }

        /* Icon colors in light */
        html:not(.dark) .header-icon {
            color: #475569
        }

        html:not(.dark) .header-icon:hover {
            color: #00a372
        }

        /* Buttons */
        html:not(.dark) .btn-secondary {
            background: rgba(15, 23, 42, 0.06);
            border: 1px solid rgba(15, 23, 42, 0.12);
            color: #374151;
        }

        html:not(.dark) .btn-secondary:hover {
            background: rgba(15, 23, 42, 0.1);
            border-color: rgba(0, 163, 114, 0.4);
            color: #00a372;
        }

        /* Balance card */
        html:not(.dark) .balance-card {
            background: linear-gradient(135deg, rgba(0, 163, 114, 0.08) 0%, rgba(139, 92, 246, 0.08) 100%);
            border: 1px solid rgba(0, 163, 114, 0.2);
        }

        html:not(.dark) .balance-label {
            color: #64748b !important
        }

        html:not(.dark) #account-balance {
            background: linear-gradient(135deg, #059669, #00a372) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
        }

        /* Mobile menu hamburger */
        html:not(.dark) .mobile-menu-btn span {
            background: #374151
        }

        html:not(.dark) .mobile-menu-btn:hover span {
            background: #00a372
        }

        /* kbd hint */
        html:not(.dark) .search-kbd {
            background: #e2e8f0;
            border-color: rgba(0, 0, 0, 0.12);
            color: #475569;
        }

        /* Mobile search modal */
        html:not(.dark) .mobile-search-modal {
            background: rgba(248, 250, 252, 0.99);
        }

        html:not(.dark) .mobile-search-modal h3 {
            color: #0f172a
        }

        /* Sidebar toggle active bars in light */
        html:not(.dark) .mobile-menu-btn.active span {
            background: #00a372
        }

        /* ── RESPONSIVE ── */
        .mobile-menu-btn {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center
        }

        .mobile-menu-btn>div {
            position: relative;
            transition: all 0.3s ease
        }

        .mobile-menu-btn span {
            display: block;
            width: 100%;
            height: 2px;
            background: currentColor;
            border-radius: 2px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1)
        }

        .mobile-menu-btn.active>div span:nth-child(1) {
            transform: translateY(7px) rotate(45deg)
        }

        .mobile-menu-btn.active>div span:nth-child(2) {
            opacity: 0;
            transform: scaleX(0)
        }

        .mobile-menu-btn.active>div span:nth-child(3) {
            transform: translateY(-7px) rotate(-45deg)
        }

        @media(max-width:1024px) {
            .balance-card {
                display: none !important
            }
        }

        @media(max-width:768px) {
            .glass-header {
                height: 64px !important;
                backdrop-filter: blur(15px)
            }

            .balance-card {
                padding: 6px 12px
            }
        }

        @media(max-width:640px) {
            .glass-header {
                height: 60px !important
            }

            .search-input {
                font-size: 14px
            }

            .logo-glow {
                width: 36px !important;
                height: 36px !important
            }
        }

        .mobile-search-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(10, 14, 26, 0.98);
            backdrop-filter: blur(20px);
            z-index: 100;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .mobile-search-modal.active {
            display: flex;
            opacity: 1
        }

        @media(max-width:768px) {

            .mobile-menu-btn,
            .btn-secondary,
            #wallet-btn,
            #logout-btn {
                min-width: 44px;
                min-height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }
    </style>
</head>

<body class="antialiased selection:bg-primary-500 selection:text-dark-900 transition-colors duration-300">
    <div id="mobile-search-modal" class="mobile-search-modal flex-col items-center justify-start pt-6 px-4">
        <div class="w-full max-w-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold dark:text-white text-gray-900">Search</h3>
                <button onclick="document.getElementById('mobile-search-modal').classList.remove('active')" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-500/10 rounded-lg transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="relative w-full group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-primary-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" id="mobile-search-input" onkeydown="if(event.key==='Enter'){window.location='market.php?search='+this.value}" placeholder="Search crypto products, NFTs, services..." class="search-input block w-full pl-12 pr-4 py-4 rounded-xl text-base placeholder-gray-500 focus:outline-none font-medium" autofocus>
            </div>
        </div>
    </div>
    <header class="glass-header fixed top-0 left-0 right-0 z-50 animate-slide-down">
        <div class="relative flex items-center justify-between h-full px-3 sm:px-4 md:px-6 lg:px-8 max-w-[2000px] mx-auto">
            <div class="flex items-center gap-2 sm:gap-3 md:gap-6 z-10 flex-shrink-0">
                <button id="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full');document.getElementById('sidebar-overlay').classList.toggle('hidden');this.classList.toggle('active')" class="mobile-menu-btn lg:hidden p-2.5 text-gray-500 dark:text-gray-400 hover:text-primary-500 transition-colors" aria-label="Toggle menu">
                    <div class="w-5 h-4 flex flex-col justify-between">
                        <span></span><span></span><span></span>
                    </div>
                </button>
                <a href="app.php" class="flex items-center gap-2 sm:gap-3 group">
                    <div class="relative w-9 h-9 sm:w-10 sm:h-10 md:w-12 md:h-12 rounded-xl bg-gradient-to-br from-primary-500 via-primary-600 to-accent-500 flex items-center justify-center logo-glow transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">
                        <img src="./public/img/logo.png" alt="GASHY" class="w-full h-full object-contain p-2" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 text-white hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <div class="absolute inset-0 rounded-xl bg-gradient-to-br from-primary-500 to-accent-500 opacity-0 group-hover:opacity-20 blur-xl transition-opacity"></div>
                    </div>
                    <div class="hidden sm:flex flex-col">
                        <span class="logo-text-main text-lg sm:text-xl md:text-2xl font-black tracking-tighter bg-gradient-to-r from-white via-primary-500 to-accent-500 bg-clip-text text-transparent">GASHY</span>
                        <span class="logo-text-sub text-[9px] sm:text-[10px] md:text-xs font-bold tracking-widest text-primary-500 -mt-1">BAZAAR</span>
                    </div>
                </a>
            </div>
            <div class="hidden md:flex items-center flex-1 max-w-2xl mx-4 lg:mx-12">
                <div class="relative w-full group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 group-focus-within:text-primary-500 transition-colors header-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" onkeydown="if(event.key==='Enter')window.location='market.php?search='+this.value" placeholder="Search crypto products, NFTs, services..." class="search-input block w-full pl-12 pr-12 py-3 rounded-xl text-sm md:text-base placeholder-gray-500 focus:outline-none font-medium">
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none opacity-0 group-focus-within:opacity-100 transition-opacity">
                        <kbd class="search-kbd px-2 py-1 text-xs font-semibold text-gray-400 dark:text-gray-500 bg-dark-700 dark:bg-dark-700 bg-gray-200 border border-white/10 dark:border-white/10 border-gray-300 rounded">Enter</kbd>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-1.5 sm:gap-2 md:gap-3 z-10 flex-shrink-0">
                <button onclick="document.getElementById('mobile-search-modal').classList.add('active');setTimeout(()=>document.getElementById('mobile-search-input').focus(),100)" class="md:hidden btn-secondary btn-glow p-2.5 rounded-xl transition-all header-icon" aria-label="Search">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
                <button onclick="App.toggleTheme()" class="btn-secondary btn-glow p-2.5 md:p-3 rounded-xl transition-all header-icon" aria-label="Toggle theme">
                    <svg id="theme-sun" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg id="theme-moon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
                <div id="account-info" class="balance-card hidden lg:flex flex-col items-end opacity-0 transition-all duration-300 hover:shadow-glow">
                    <span class="balance-label text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Balance</span>
                    <span class="text-base font-black bg-gradient-to-r from-green-400 to-primary-500 bg-clip-text text-transparent font-mono" id="account-balance">0.00 SOL</span>
                </div>
                <button id="wallet-btn" onclick="App.connectWallet()" class="btn-primary btn-glow flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 md:px-6 py-2.5 md:py-3 rounded-xl font-bold text-sm md:text-base shadow-lg hover:shadow-glow-lg transition-all">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span id="wallet-text" class="hidden sm:inline">Connect</span>
                </button>
                <button id="logout-btn" onclick="App.logout()" class="hidden btn-glow items-center gap-1.5 sm:gap-2 px-3 sm:px-4 md:px-6 py-2.5 md:py-3 rounded-xl bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold text-sm md:text-base shadow-lg transition-all">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </div>
        </div>
    </header>
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