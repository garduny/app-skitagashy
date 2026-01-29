<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>System Maintenance | Gashy Bazaar</title>
    <link rel="shortcut icon" href="public/img/logo.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
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
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0B0E14
        }

        .glass {
            background: rgba(21, 26, 35, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05)
        }
    </style>
</head>

<body class="bg-dark-900 text-white flex items-center justify-center min-h-screen relative overflow-hidden">
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-blue-600/10 blur-[150px] rounded-full"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-purple-600/10 blur-[150px] rounded-full"></div>
    </div>
    <div class="relative z-10 glass p-12 rounded-3xl text-center max-w-lg mx-4 shadow-2xl">
        <div class="w-24 h-24 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-8 animate-pulse"><svg class="w-12 h-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg></div>
        <h1 class="text-4xl font-black mb-4 tracking-tight">System Maintenance</h1>
        <p class="text-gray-400 mb-8 leading-relaxed">Gashy Bazaar is currently undergoing scheduled upgrades to improve your experience. Funds are safe. Please check back shortly.</p>
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 text-xs font-mono text-gray-500"><span class="w-2 h-2 rounded-full bg-yellow-500 animate-ping"></span> Status: Upgrading Database</div>
    </div>
</body>

</html>