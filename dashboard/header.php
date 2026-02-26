<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Gashy Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="shortcut icon" href="../public/img/logo.png" type="image/x-icon">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            900: '#0a0e1a',
                            800: '#131824',
                            700: '#1a1f2e'
                        },
                        primary: {
                            500: '#00ffaa',
                            600: '#00d48f',
                            400: '#33ffbb'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&display=swap');

        @keyframes glow-pulse {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(0, 255, 170, 0.3)
            }

            50% {
                box-shadow: 0 0 40px rgba(0, 255, 170, 0.6)
            }
        }

        @keyframes slide-down {
            from {
                transform: translateY(-100%);
                opacity: 0
            }

            to {
                transform: translateY(0);
                opacity: 1
            }
        }

        body {
            font-family: 'Inter', sans-serif
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px
        }

        ::-webkit-scrollbar-track {
            background: transparent
        }

        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 10px
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #475569
        }

        html.dark body {
            background-color: #0a0e1a;
            color: #fff
        }

        html:not(.dark) body {
            background-color: #f8fafc;
            color: #111827
        }

        .admin-header {
            background: linear-gradient(135deg, rgba(10, 14, 26, 0.95), rgba(19, 24, 36, 0.95));
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 255, 170, 0.1);
            animation: slide-down 0.3s ease-out
        }

        .logo-glow {
            animation: glow-pulse 3s ease-in-out infinite
        }

        .theme-toggle {
            background: linear-gradient(135deg, rgba(0, 255, 170, 0.1), rgba(139, 92, 246, 0.1));
            border: 1px solid rgba(0, 255, 170, 0.2);
            transition: all 0.3s ease
        }

        .theme-toggle:hover {
            background: linear-gradient(135deg, #00ffaa, #8b5cf6);
            transform: scale(1.1)
        }

        .user-profile {
            background: linear-gradient(135deg, rgba(0, 255, 170, 0.05), rgba(139, 92, 246, 0.05));
            border: 1px solid rgba(0, 255, 170, 0.1);
            transition: all 0.3s ease
        }

        .user-profile:hover {
            border-color: rgba(0, 255, 170, 0.3);
            box-shadow: 0 4px 15px rgba(0, 255, 170, 0.15)
        }

        html:not(.dark) .admin-header {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.98));
            border-bottom: 1px solid rgba(0, 212, 143, 0.15)
        }

        html:not(.dark) .theme-toggle {
            background: linear-gradient(135deg, rgba(0, 212, 143, 0.08), rgba(139, 92, 246, 0.08));
            border: 1px solid rgba(0, 212, 143, 0.2)
        }

        html:not(.dark) .user-profile {
            background: linear-gradient(135deg, rgba(0, 212, 143, 0.03), rgba(139, 92, 246, 0.03));
            border: 1px solid rgba(0, 212, 143, 0.1)
        }
    </style>
    <script>
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.remove('dark');
        }

        function toggleTheme() {
            const html = document.documentElement;
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }
    </script>
</head>

<body class="bg-gray-50 dark:bg-dark-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
    <header class="admin-header fixed top-0 z-50 w-full h-16 px-6 flex items-center justify-between shadow-xl">
        <div class="flex items-center gap-6">
            <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="lg:hidden p-2 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5 transition-all">
                <i class="fa-solid fa-bars text-xl"></i>
            </button>
            <div class="flex items-center gap-3">
                <div class="logo-glow w-10 h-10 rounded-xl bg-gradient-to-br from-[#00ffaa] to-[#00d48f] flex items-center justify-center shadow-lg shadow-[#00ffaa]/30">
                    <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-black tracking-tighter" style="font-family:'Space Mono',monospace">
                        GASHY<span class="text-[#00ffaa]">ADMIN</span>
                    </div>
                    <div class="text-[9px] text-gray-500 dark:text-gray-500 font-bold uppercase tracking-widest -mt-1">Dashboard v2.0</div>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <button onclick="toggleTheme()" class="theme-toggle w-11 h-11 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fa-solid fa-sun hidden dark:block text-[#00ffaa]"></i>
                <i class="fa-solid fa-moon block dark:hidden text-gray-700"></i>
            </button>
            <a href="profile.php" class="user-profile flex items-center gap-4 pl-4 pr-2 py-2 rounded-xl shadow-lg">
                <div class="text-right hidden md:block">
                    <div class="text-sm font-black text-gray-900 dark:text-white"><?= user()['username'] ?></div>
                    <div class="text-[9px] text-[#00ffaa] font-bold uppercase tracking-widest"><?= user()['role_name'] ?></div>
                </div>
                <div class="relative">
                    <img src="<?= user()['avatar'] ?? 'https://ui-avatars.com/api/?name=' . user()['username'] . '&background=00ffaa&color=000' ?>" class="w-10 h-10 rounded-xl border-2 border-[#00ffaa]/30 shadow-lg" alt="Avatar">
                    <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 rounded-full border-2 border-dark-800"></div>
                </div>
            </a>
        </div>
    </header>