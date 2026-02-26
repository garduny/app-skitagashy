<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="min-h-screen pt-20 lg:pl-64 bg-gray-50 dark:bg-[#0B0E14] text-gray-800 dark:text-gray-200 transition-colors duration-300">
    <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
        <h1 class="text-3xl font-black text-gray-900 dark:text-white mb-8">Terms of Service</h1>

        <div class="prose prose-sm dark:prose-invert max-w-none bg-white dark:bg-[#151A23] p-8 rounded-3xl border border-gray-200 dark:border-white/5">
            <h3>1. Acceptance of Terms</h3>
            <p>By accessing and using Gashy Bazaar, you accept and agree to be bound by the terms and provision of this agreement.</p>

            <h3>2. Cryptocurrency Risks</h3>
            <p>You acknowledge that cryptocurrency assets are subject to high market volatility. We are not responsible for any financial losses.</p>

            <h3>3. Digital Goods</h3>
            <p>All sales of digital gift cards and codes are final once revealed. Please ensure you are purchasing the correct region/type.</p>

            <h3>4. Seller Conduct</h3>
            <p>Sellers must verify ownership of goods. Fraudulent listings will result in an immediate account ban and forfeiture of pending payouts.</p>

            <p class="mt-8 text-xs text-gray-400">Last Updated: January 2026</p>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>