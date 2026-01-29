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
                            900: '#0B0E14',
                            800: '#151A23',
                            700: '#1E2532'
                        },
                        primary: {
                            500: '#00ffaa',
                            600: '#00d48f'
                        },
                        accent: {
                            500: '#8B5CF6'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0B0E14
        }

        .glass {
            background: rgba(21, 26, 35, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05)
        }

        ::-webkit-scrollbar {
            width: 6px
        }

        ::-webkit-scrollbar-track {
            background: #0B0E14
        }

        ::-webkit-scrollbar-thumb {
            background: #1E2532;
            border-radius: 3px
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #00d48f
        }

        html.dark body {
            background-color: #0B0E14;
            color: #fff
        }

        html:not(.dark) body {
            background-color: #F3F4F6;
            color: #111827
        }

        html:not(.dark) .glass {
            background: rgba(255, 255, 255, 0.95);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05)
        }

        html:not(.dark) .bg-dark-800 {
            background-color: #fff;
            border-color: #e5e7eb
        }

        html:not(.dark) .text-gray-400 {
            color: #6b7280
        }

        html:not(.dark) .border-white\/5 {
            border-color: rgba(0, 0, 0, 0.05)
        }

        html:not(.dark) .bg-white\/5 {
            background-color: rgba(0, 0, 0, 0.05)
        }
    </style>
</head>

<body class="bg-gray-50 dark:bg-dark-900 text-gray-900 dark:text-white antialiased selection:bg-primary-500 selection:text-white transition-colors duration-300">
    <header class="fixed top-0 w-full z-50 glass h-16 transition-all duration-300">
        <div class="flex items-center justify-between h-full px-4 max-w-[1920px] mx-auto">
            <div class="flex items-center gap-2 md:gap-4 shrink-0">
                <button id="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full');document.getElementById('sidebar-overlay').classList.toggle('hidden')" class="p-2 text-gray-500 hover:text-primary-500 lg:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                </button>
                <a href="app.php" class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-primary-600 to-accent-500 flex items-center justify-center shrink-0">
                        <img src="./public/img/logo.png" alt="G" class="w-full h-full object-contain p-1" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                        <svg class="w-5 h-5 text-white hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span class="hidden sm:block text-lg font-black tracking-tight text-gray-900 dark:text-white whitespace-nowrap">
                        GASHY<span class="text-primary-500">BAZAAR</span>
                    </span>
                </a>
            </div>
            <div class="hidden md:flex items-center flex-1 max-w-lg mx-4 lg:mx-8">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" onkeydown="if(event.key==='Enter')window.location='market.php?search='+this.value" placeholder="Search products..." class="block w-full pl-10 pr-3 py-2 bg-gray-100 dark:bg-dark-800 border border-gray-200 dark:border-white/5 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:border-primary-500 transition-all">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="App.toggleTheme()" class="p-2 rounded-lg bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-500 hover:text-primary-500 transition-all">
                    <svg id="theme-sun" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg id="theme-moon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
                <div id="account-info" class="hidden lg:flex flex-col items-end mr-2 opacity-0 transition-opacity duration-300">
                    <span class="text-[10px] text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wider">Balance</span>
                    <span class="text-sm font-bold text-green-600 dark:text-green-400 font-mono" id="account-balance">0.00</span>
                </div>
                <button id="wallet-btn" onclick="App.connectWallet()" class="flex items-center gap-2 px-3 py-2 md:px-4 rounded-lg bg-gray-900 dark:bg-white/10 hover:bg-primary-600 dark:hover:bg-primary-600 text-white font-semibold transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span id="wallet-text" class="hidden sm:inline text-sm">Connect</span>
                </button>
                <button id="logout-btn" onclick="App.logout()" class="hidden flex items-center gap-2 px-3 py-2 md:px-4 rounded-lg bg-red-100 dark:bg-red-500/10 text-red-600 dark:text-red-500 hover:bg-red-200 dark:hover:bg-red-500 hover:text-white transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="hidden sm:inline text-sm">Exit</span>
                </button>
            </div>
        </div>
    </header>
    <script src="public/js/core.js"></script>
    <?php $page = basename($_SERVER['PHP_SELF'], '.php');
    $path = "public/js/pages/{$page}.js";
    if (file_exists($path)) {
        echo "<script src='{$path}'></script>";
    } ?>
</body>

</html>