<!DOCTYPE html>
<html lang="en" class="dark">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<title>GASHY BAZAAR | Next Gen Crypto Market</title>
	<link rel="shortcut icon" href="public/img/logo.png" type="image/x-icon">
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
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
			background: rgba(21, 26, 35, 0.85);
			backdrop-filter: blur(12px);
			border-bottom: 1px solid rgba(255, 255, 255, 0.05)
		}

		.neon-text {
			text-shadow: 0 0 10px rgba(59, 130, 246, 0.5)
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
			background: #3B82F6
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
			background: rgba(255, 255, 255, 0.9);
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

<body>
	<main class="min-h-screen pt-20 bg-gray-50 dark:bg-[#0B0E14] text-gray-800 dark:text-gray-200 transition-colors duration-300 flex items-center justify-center">
		<div class="text-center p-8">
			<div class="relative w-40 h-40 mx-auto mb-6">
				<div class="absolute inset-0 bg-blue-500/20 blur-2xl rounded-full"></div>
				<div class="relative w-full h-full bg-white dark:bg-[#151A23] rounded-full flex items-center justify-center border-4 border-gray-100 dark:border-white/5 text-6xl shadow-2xl">
					😵
				</div>
			</div>
			<h1 class="text-6xl font-black text-gray-900 dark:text-white mb-2 tracking-tighter">404</h1>
			<p class="text-xl text-gray-500 dark:text-gray-400 font-medium mb-8">Lost in the Blockchain?</p>
			<a href="app" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition-all inline-flex items-center gap-2 hover:-translate-y-1">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
				</svg>
				Back Home
			</a>
		</div>
	</main>

</body>

</html>