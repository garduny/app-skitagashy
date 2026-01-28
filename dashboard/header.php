<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Gashy Admin</title>
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
                            900: '#0B0E14',
                            800: '#151A23',
                            700: '#1E2532'
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0B0E14
        }

        ::-webkit-scrollbar {
            width: 5px
        }

        ::-webkit-scrollbar-track {
            background: transparent
        }

        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 10px
        }

        html.dark body {
            background-color: #0B0E14;
            color: #fff
        }

        html:not(.dark) body {
            background-color: #F3F4F6;
            color: #111827
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
    <header class="fixed top-0 z-50 w-full bg-white dark:bg-dark-800 border-b border-gray-200 dark:border-white/5 h-16 px-4 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-4">
            <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="lg:hidden p-2 text-gray-500 hover:text-primary-500"><i class="fa-solid fa-bars text-xl"></i></button>
            <div class="text-xl font-black tracking-tighter">GASHY<span class="text-primary-500">ADMIN</span></div>
        </div>
        <div class="flex items-center gap-4">
            <button onclick="toggleTheme()" class="w-10 h-10 rounded-full bg-gray-100 dark:bg-white/5 flex items-center justify-center text-gray-500 hover:text-primary-500 transition-colors">
                <i class="fa-solid fa-sun hidden dark:block"></i>
                <i class="fa-solid fa-moon block dark:hidden"></i>
            </button>
            <a href="profile.php" class="flex items-center gap-3 pl-4 border-l border-gray-200 dark:border-white/10 hover:opacity-80 transition-opacity">
                <div class="text-right hidden md:block">
                    <div class="text-sm font-bold"><?= user()['username'] ?></div>
                    <div class="text-[10px] text-gray-500 uppercase tracking-wider"><?= user()['role_name'] ?></div>
                </div>
                <img src="<?= user()['avatar'] ?? 'https://ui-avatars.com/api/?name=' . user()['username'] . '&background=00ffaa&color=000' ?>" class="w-9 h-9 rounded-lg bg-gray-200">
            </a>
        </div>
    </header>