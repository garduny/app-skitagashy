<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
$cats = getQuery(" SELECT * FROM categories WHERE is_active=1 ORDER BY name ASC ");
?>
<main class="min-h-screen pt-20 lg:pl-64 bg-gray-50 dark:bg-[#0B0E14] text-gray-800 dark:text-gray-200 relative transition-colors duration-300">
    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
        <div id="hub-loader" class="text-center py-20"><svg class="w-12 h-12 text-blue-500 mx-auto animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg></div>
        <div id="hub-content" class="hidden space-y-8 animate-fade-in">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Seller Hub</h1>
                    <p class="text-sm text-gray-500">Manage your store inventory and sales.</p>
                </div>
                <button onclick="openProductModal()" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition-all flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg> Add Product</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-[#151A23] p-6 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm">
                    <div class="text-gray-500 text-xs font-bold uppercase mb-2">Total Earnings</div>
                    <div class="text-3xl font-black text-green-500"><span id="stat-earnings">0</span> <span class="text-sm text-gray-400">G</span></div>
                </div>
                <div class="bg-white dark:bg-[#151A23] p-6 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm">
                    <div class="text-gray-500 text-xs font-bold uppercase mb-2">Total Sales</div>
                    <div class="text-3xl font-black text-gray-900 dark:text-white" id="stat-sales">0</div>
                </div>
                <div class="bg-white dark:bg-[#151A23] p-6 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm">
                    <div class="text-gray-500 text-xs font-bold uppercase mb-2">Active Products</div>
                    <div class="text-3xl font-black text-gray-900 dark:text-white" id="stat-products">0</div>
                </div>
                <div class="bg-white dark:bg-[#151A23] p-6 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm">
                    <div class="text-gray-500 text-xs font-bold uppercase mb-2">Rating</div>
                    <div class="text-3xl font-black text-yellow-500 flex items-center gap-2"><span id="stat-rating">0.0</span> <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg></div>
                </div>
            </div>
            <div class="bg-white dark:bg-[#151A23] rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-white/5">
                    <h3 class="font-bold text-gray-900 dark:text-white">Your Inventory</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-gray-500 border-b border-gray-200 dark:border-white/5">
                                <th class="px-6 py-4">Product</th>
                                <th class="px-6 py-4">Price</th>
                                <th class="px-6 py-4">Stock</th>
                                <th class="px-6 py-4">Category</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="product-list" class="divide-y divide-gray-100 dark:divide-white/5"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
<div id="product-modal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeProductModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl bg-white dark:bg-[#151A23] rounded-2xl shadow-2xl p-6 max-h-[90vh] overflow-y-auto custom-scrollbar">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6" id="modal-title">Add Product</h3>
        <form id="product-form" onsubmit="event.preventDefault();saveProduct();">
            <input type="hidden" id="prod-id" value="0">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Title</label><input type="text" id="prod-title" required class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:border-blue-500 outline-none"></div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Price (GASHY)</label><input type="number" step="0.01" id="prod-price" required class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:border-blue-500 outline-none"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Stock</label><input type="number" id="prod-stock" required class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:border-blue-500 outline-none"></div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Type</label><select id="prod-type" class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:border-blue-500 outline-none">
                        <option value="digital">Digital</option>
                        <option value="gift_card">Gift Card</option>
                        <option value="nft">NFT</option>
                        <option value="physical">Physical</option>
                    </select></div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Category</label><select id="prod-cat" class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:border-blue-500 outline-none"><?php foreach ($cats as $c): ?><option value="<?= $c['id'] ?>"><?= $c['name'] ?></option><?php endforeach; ?></select></div>
            </div>
            <div class="mb-4"><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Description</label><textarea id="prod-desc" rows="3" class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:border-blue-500 outline-none"></textarea></div>
            <div class="mb-6"><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Images (One URL per line)</label><textarea id="prod-images" rows="3" class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:border-blue-500 outline-none font-mono text-xs"></textarea></div>
            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl transition-all">Save Product</button>
        </form>
    </div>
</div>
<script src="public/js/pages/seller-hub.js"></script>
<?php require_once 'footer.php'; ?>