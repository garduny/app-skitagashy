<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
	require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="min-h-screen pt-20 lg:pl-64 bg-gray-50 dark:bg-[#0B0E14] text-gray-800 dark:text-gray-200 transition-colors duration-300 flex items-center justify-center">
	<div class="text-center p-8">
		<div class="relative w-40 h-40 mx-auto mb-6">
			<div class="absolute inset-0 bg-blue-500/20 blur-2xl rounded-full"></div>
			<div class="relative w-full h-full bg-white dark:bg-[#151A23] rounded-full flex items-center justify-center border-4 border-gray-100 dark:border-white/5 text-6xl shadow-2xl">
				😵
			</div>
		</div>
		<h1 class="text-6xl font-black text-gray-900 dark:text-white mb-2 tracking-tighter">404</h1>
		<p class="text-xl text-gray-500 dark:text-gray-400 font-medium mb-8">Lost in the Blockchain?</p>
		<a href="app.php" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition-all inline-flex items-center gap-2 hover:-translate-y-1">
			<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
			</svg>
			Back Home
		</a>
	</div>
</main>
<?php require_once 'footer.php'; ?>