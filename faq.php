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
        <div class="text-center mb-12">
            <h1 class="text-3xl font-black text-gray-900 dark:text-white mb-4">Frequently Asked Questions</h1>
            <p class="text-gray-500 dark:text-gray-400">Everything you need to know about Gashy Bazaar.</p>
        </div>

        <div class="space-y-4">
            <!-- Q1 -->
            <div class="bg-white dark:bg-[#151A23] rounded-2xl border border-gray-200 dark:border-white/5 overflow-hidden">
                <details class="group">
                    <summary class="flex items-center justify-between p-6 cursor-pointer list-none">
                        <span class="font-bold text-gray-900 dark:text-white">How do I buy items?</span>
                        <span class="transition group-open:rotate-180">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </summary>
                    <div class="text-gray-500 dark:text-gray-400 p-6 pt-0 leading-relaxed text-sm">
                        Connect your Phantom wallet, ensure you have $GASHY tokens, and click "Buy Now" on any product. You will be asked to sign a transaction to transfer the tokens. Once confirmed on the blockchain (usually <2 seconds), the item is yours.
                            </div>
                </details>
            </div>

            <!-- Q2 -->
            <div class="bg-white dark:bg-[#151A23] rounded-2xl border border-gray-200 dark:border-white/5 overflow-hidden">
                <details class="group">
                    <summary class="flex items-center justify-between p-6 cursor-pointer list-none">
                        <span class="font-bold text-gray-900 dark:text-white">Are Mystery Boxes fair?</span>
                        <span class="transition group-open:rotate-180">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </summary>
                    <div class="text-gray-500 dark:text-gray-400 p-6 pt-0 leading-relaxed text-sm">
                        Yes. We use a Provably Fair RNG system. The probabilities for each item rarity are displayed publicly on the box page. The result is generated server-side and recorded in the database instantly.
                    </div>
                </details>
            </div>

            <!-- Q3 -->
            <div class="bg-white dark:bg-[#151A23] rounded-2xl border border-gray-200 dark:border-white/5 overflow-hidden">
                <details class="group">
                    <summary class="flex items-center justify-between p-6 cursor-pointer list-none">
                        <span class="font-bold text-gray-900 dark:text-white">How do I get my Gift Card code?</span>
                        <span class="transition group-open:rotate-180">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </summary>
                    <div class="text-gray-500 dark:text-gray-400 p-6 pt-0 leading-relaxed text-sm">
                        Go to "My Orders" in the sidebar. Find your completed order and click "View Gift Codes". The encrypted code will be decrypted and shown to you securely.
                    </div>
                </details>
            </div>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>