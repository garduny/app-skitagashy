<?php
require_once 'init.php';
if (post('save_settings')) {
    $keys = ['site_title', 'treasury_wallet', 'platform_fee', 'burn_address', 'maintenance_mode'];
    foreach ($keys as $k) {
        $val = secure($_POST[$k] ?? '');
        execute(" INSERT INTO settings (key_name,value) VALUES ('$k','$val') ON DUPLICATE KEY UPDATE value='$val' ");
    }
    redirect('setting.php?msg=saved');
}
$set_rows = getQuery(" SELECT * FROM settings ");
$settings = [];
foreach ($set_rows as $r) {
    $settings[$r['key_name']] = $r['value'];
}
function val($key)
{
    global $settings;
    return $settings[$key] ?? '';
}
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-4 mb-6">
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">System Settings</h1>
        </div>
        <?php if (get('msg')): ?><div class="p-4 mb-6 bg-green-100 dark:bg-green-500/20 border border-green-200 dark:border-green-500/30 text-green-600 dark:text-green-400 rounded-xl font-bold text-center">Configuration saved successfully.</div><?php endif; ?>
        <form method="POST" class="space-y-6">
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-white/5 pb-2">General Configuration</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Site Title</label><input type="text" name="site_title" value="<?= val('site_title') ?>" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 outline-none"></div>
                    <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">System Status</label><select name="maintenance_mode" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 outline-none">
                            <option value="0" <?= val('maintenance_mode') == '0' ? 'selected' : '' ?>>Live</option>
                            <option value="1" <?= val('maintenance_mode') == '1' ? 'selected' : '' ?>>Maintenance Mode</option>
                        </select></div>
                </div>
            </div>
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-white/5 pb-2">Financial & Blockchain</h3>
                <div class="space-y-4">
                    <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Treasury Wallet (Fees)</label><input type="text" name="treasury_wallet" value="<?= val('treasury_wallet') ?>" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 outline-none font-mono text-sm"></div>
                    <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Burn Address (Lottery)</label><input type="text" name="burn_address" value="<?= val('burn_address') ?>" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 outline-none font-mono text-sm"></div>
                    <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Platform Fee (%)</label><input type="number" step="0.01" name="platform_fee" value="<?= val('platform_fee') ?>" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 outline-none"></div>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" name="save_settings" value="1" class="px-8 py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl shadow-lg shadow-primary-500/20 transition-all transform hover:-translate-y-1">Save Configuration</button>
            </div>
        </form>
    </div>
</main>
<?php require_once 'footer.php'; ?>