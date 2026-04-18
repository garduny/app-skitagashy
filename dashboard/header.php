<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Gashy Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="shortcut icon" href="<?= '../' . settings('site_logo') ?: 'https://ui-avatars.com/api/?name=GB&background=00ffaa&color=000' ?>" type="image/x-icon">
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

        @keyframes glowPulse {

            0%,
            100% {
                box-shadow: 0 0 18px rgba(0, 255, 170, .22)
            }

            50% {
                box-shadow: 0 0 32px rgba(0, 255, 170, .45)
            }
        }

        @keyframes slideDown {
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
            background: #0a0e1a;
            color: #fff
        }

        html:not(.dark) body {
            background: #f8fafc;
            color: #111827
        }

        .admin-header {
            height: 64px;
            background: rgba(10, 14, 26, .88);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(255, 255, 255, .05);
            animation: slideDown .28s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .18)
        }

        html:not(.dark) .admin-header {
            background: rgba(255, 255, 255, .92);
            border-bottom: 1px solid rgba(15, 23, 42, .06)
        }

        .logo-glow {
            animation: glowPulse 3s ease-in-out infinite
        }

        .top-btn {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: .22s ease
        }

        .theme-toggle {
            background: linear-gradient(135deg, rgba(0, 255, 170, .08), rgba(124, 58, 237, .08));
            border: 1px solid rgba(0, 255, 170, .14)
        }

        .theme-toggle:hover {
            transform: translateY(-1px) scale(1.03)
        }

        .user-profile {
            height: 44px;
            padding: 4px 6px 4px 14px;
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(0, 255, 170, .05), rgba(124, 58, 237, .05));
            border: 1px solid rgba(0, 255, 170, .12);
            transition: .22s ease
        }

        .user-profile:hover {
            border-color: rgba(0, 255, 170, .28);
            box-shadow: 0 10px 24px rgba(0, 255, 170, .08)
        }

        .mobile-menu:hover {
            background: rgba(148, 163, 184, .12)
        }

        @media(max-width:640px) {
            .admin-header {
                padding-left: 14px !important;
                padding-right: 14px !important
            }

            .brand-text {
                display: none
            }

            .user-meta {
                display: none
            }
        }
    </style>
    <script>
        if (localStorage.getItem('theme') === 'light') document.documentElement.classList.remove('dark');

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
    <header class="admin-header fixed top-0 left-0 right-0 z-50 px-4 sm:px-6 flex items-center justify-between">
        <div class="flex items-center gap-3 sm:gap-5">
            <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="top-btn mobile-menu lg:hidden text-gray-600 dark:text-gray-300">
                <i class="fa-solid fa-bars text-lg"></i>
            </button>
            <a href="app" class="flex items-center gap-3 min-w-0">
                <div class="logo-glow w-10 h-10 rounded-2xl overflow-hidden bg-gradient-to-br from-[#00ffaa] to-[#00d48f] flex items-center justify-center">
                    <img src="<?= '../' . settings('site_logo') ?: 'https://ui-avatars.com/api/?name=GB&background=00ffaa&color=000' ?>" class="w-full h-full object-cover" alt="Logo">
                </div>
                <div class="brand-text leading-tight">
                    <div class="text-[22px] font-black tracking-[-0.06em]" style="font-family:'Space Mono',monospace">GASHY<span class="text-[#00ffaa]">ADMIN</span></div>
                    <div class="text-[9px] uppercase tracking-[0.28em] text-gray-500 font-bold -mt-1">Dashboard v1.0</div>
                </div>
            </a>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="toggleTheme()" class="top-btn theme-toggle shadow-sm">
                <i class="fa-solid fa-sun hidden dark:block text-[#00ffaa]"></i>
                <i class="fa-solid fa-moon block dark:hidden text-gray-700"></i>
            </button>

            <a href="profile.php" class="user-profile flex items-center gap-3">
                <div class="user-meta text-right hidden md:block leading-tight">
                    <div class="text-sm font-black text-gray-900 dark:text-white"><?= user()['username'] ?></div>
                    <div class="text-[9px] uppercase tracking-[0.24em] text-[#00ffaa] font-bold"><?= user()['role_name'] ?></div>
                </div>
                <div class="relative shrink-0">
                    <img src="<?= '../' . user()['avatar'] ?: 'https://ui-avatars.com/api/?name=' . user()['username'] . '&background=00ffaa&color=000' ?>" class="w-9 h-9 rounded-xl object-cover border border-[#00ffaa]/25" alt="Avatar">
                    <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-green-500 border-2 border-white dark:border-dark-800"></span>
                </div>
            </a>
        </div>
    </header>