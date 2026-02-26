<?php require_once 'init.php';
if (get('del')) {
    execute(" DELETE FROM settings WHERE id = '" . request('del', 'get') . "' ");
    redirect('setting?msg=deleted');
}
if (post('save')) {
    $id = request('id', 'post');
    $k = request('key_name', 'post');
    $v = request('value', 'post');
    if ($id) {
        execute(" UPDATE settings SET key_name = '$k' , value = '$v' WHERE id = '$id' ");
    } else {
        execute(" INSERT INTO settings (key_name,value) VALUES ('$k','$v') ");
    }
    redirect('setting?msg=saved');
}
$edit_data = null;
if (get('edit')) {
    $edit_data = findQuery(" SELECT * FROM settings WHERE id = '" . request('edit', 'get') . "' ");
}
$settings = getQuery(" SELECT * FROM settings ORDER BY id DESC ");
require_once 'header.php';
require_once 'sidebar.php'; ?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">System Settings</h1>
                <p class="text-sm text-gray-500 font-bold uppercase mt-1">Manage global platform variables</p>
            </div>
            <?php if (get('msg')): ?><div class="px-6 py-3 bg-green-500 text-white rounded-xl font-bold animate-bounce">Operation successful</div><?php endif; ?>
        </div>
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <div class="xl:col-span-1">
                <form method="POST" class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-xl sticky top-24">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2"><?= $edit_data ? '<span class="w-2 h-2 bg-yellow-400 rounded-full"></span> Edit Setting' : '<span class="w-2 h-2 bg-green-400 rounded-full"></span> Add New Setting' ?></h3>
                    <?php if ($edit_data): ?><input type="hidden" name="id" value="<?= $edit_data['id'] ?>"><?php endif; ?>
                    <div class="space-y-5">
                        <div><label class="block text-[10px] font-black text-gray-400 uppercase mb-1 ml-1">Key Identifier</label><input type="text" name="key_name" value="<?= $edit_data['key_name'] ?? '' ?>" required placeholder="site_title" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all font-mono"></div>
                        <div><label class="block text-[10px] font-black text-gray-400 uppercase mb-1 ml-1">Configuration Value</label><textarea name="value" rows="4" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all font-mono"><?= $edit_data['value'] ?? '' ?></textarea></div>
                        <button type="submit" name="save" value="1" class="w-full py-4 bg-primary-600 hover:bg-primary-500 text-white font-black rounded-xl shadow-lg shadow-primary-500/30 transition-all transform hover:-translate-y-1"><?= $edit_data ? 'Update Variable' : 'Save Variable' ?></button>
                        <?php if ($edit_data): ?><a href="setting" class="block text-center text-xs font-bold text-gray-500 hover:text-red-500 mt-2">Cancel Edit</a><?php endif; ?>
                    </div>
                </form>
            </div>
            <div class="xl:col-span-2">
                <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 overflow-hidden shadow-sm">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-white/5">
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Variable Key</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Value</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                            <?php foreach ($settings as $s): ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                    <td class="px-6 py-4 font-mono text-xs font-bold text-primary-600 dark:text-primary-400"><?= $s['key_name'] ?></td>
                                    <td class="px-6 py-4">
                                        <div class="max-w-[300px] truncate font-mono text-xs text-gray-600 dark:text-gray-400"><?= htmlspecialchars($s['value']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="setting?edit=<?= $s['id'] ?>" class="p-2 hover:bg-yellow-500/10 text-yellow-600 rounded-lg transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg></a>
                                            <button onclick="if(confirm('Delete variable?')){window.location.href='setting?del=<?= $s['id'] ?>'}" class="p-2 hover:bg-red-500/10 text-red-600 rounded-lg transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>