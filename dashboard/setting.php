<?php
require_once 'init.php';
if (get('del')) {
    execute(" DELETE FROM settings WHERE id='" . request('del', 'get') . "' ");
    redirect('setting?msg=deleted');
}
if (post('update_logo')) {
    $uploadPath = '../server/uploads/setting/';
    $dbPath = '/server/uploads/setting/';
    if (!empty($_FILES['image']['name'])) {
        $newImage = upload('image', $uploadPath);
        if ($newImage) {
            $logo = $dbPath . basename($newImage);
            $exist = findQuery(" SELECT id FROM settings WHERE key_name='site_logo' ");
            if ($exist) execute(" UPDATE settings SET value='$logo' WHERE key_name='site_logo' ");
            else execute(" INSERT INTO settings(key_name,value) VALUES('site_logo','$logo') ");
        }
    }
    redirect('setting?msg=saved');
}
if (post('save')) {
    $id = request('id', 'post');
    $k = request('key_name', 'post');
    $v = request('value', 'post');
    if ($id) execute(" UPDATE settings SET key_name='$k',value='$v' WHERE id='$id' ");
    else execute(" INSERT INTO settings(key_name,value) VALUES('$k','$v') ");
    redirect('setting?msg=saved');
}
$edit_data = null;
if (get('edit')) $edit_data = findQuery(" SELECT * FROM settings WHERE id='" . request('edit', 'get') . "' ");
$settings = getQuery(" SELECT * FROM settings ORDER BY id DESC ");
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">System Settings</h1>
                <p class="text-sm text-gray-500 font-bold uppercase mt-1 tracking-wider">Manage global platform variables</p>
            </div>
            <?php if (get('msg')): ?>
                <div class="px-5 py-3 rounded-2xl bg-gradient-to-r from-primary-600 to-primary-500 text-white font-black shadow-lg shadow-primary-500/20">Operation Successful</div>
            <?php endif; ?>
        </div>
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="space-y-6">
                <div class="bg-white dark:bg-dark-800 rounded-3xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="font-black text-gray-900 dark:text-white">Website Logo</h3>
                        <?php $logo = settings('site_logo'); ?>
                        <?php if ($logo): ?>
                            <img src="<?= '../' . $logo ?>" class="w-12 h-12 rounded-2xl object-cover border border-gray-200 dark:border-white/10">
                        <?php endif; ?>
                    </div>
                    <form method="POST" enctype="multipart/form-data" class="space-y-4">
                        <input type="file" name="image" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-2xl px-4 py-3 text-sm">
                        <button type="submit" name="update_logo" class="w-full py-3 rounded-2xl bg-primary-600 hover:bg-primary-500 text-white font-black transition-all">Update Logo</button>
                    </form>
                </div>
                <form method="POST" class="bg-white dark:bg-dark-800 rounded-3xl border border-gray-200 dark:border-white/5 p-6 shadow-sm sticky top-24">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-3 h-3 rounded-full <?= $edit_data ? 'bg-yellow-400' : 'bg-green-400' ?>"></div>
                        <h3 class="font-black text-gray-900 dark:text-white"><?= $edit_data ? 'Edit Setting' : 'Add New Setting' ?></h3>
                    </div>
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                    <?php endif; ?>
                    <div class="space-y-5">
                        <div>
                            <label class="block text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2">Key Identifier</label>
                            <input type="text" name="key_name" value="<?= $edit_data['key_name'] ?? '' ?>" required placeholder="site_title" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-2xl px-4 py-3 font-mono text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2">Configuration Value</label>
                            <textarea name="value" rows="6" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-2xl px-4 py-3 font-mono text-sm focus:ring-2 focus:ring-primary-500 outline-none"><?= $edit_data['value'] ?? '' ?></textarea>
                        </div>
                        <button type="submit" name="save" value="1" class="w-full py-4 rounded-2xl bg-primary-600 hover:bg-primary-500 text-white font-black shadow-lg shadow-primary-500/20 transition-all"><?= $edit_data ? 'Update Variable' : 'Save Variable' ?></button>
                        <?php if ($edit_data): ?>
                            <a href="setting" class="block text-center text-xs font-black text-gray-500 hover:text-red-500">Cancel Edit</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            <div class="xl:col-span-2">
                <div class="bg-white dark:bg-dark-800 rounded-3xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-gray-100 dark:border-white/5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <h3 class="font-black text-gray-900 dark:text-white">Variables</h3>
                        <div class="text-xs font-black text-gray-400 uppercase tracking-widest"><?= count($settings) ?> Records</div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[760px]">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-white/[0.03]">
                                    <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-widest text-gray-400">Key</th>
                                    <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-widest text-gray-400">Value</th>
                                    <th class="px-6 py-4 text-right text-[11px] font-black uppercase tracking-widest text-gray-400">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                                <?php foreach ($settings as $s): ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-mono text-sm font-black text-primary-600 dark:text-primary-400"><?= $s['key_name'] ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="max-w-[420px] truncate text-sm font-mono text-gray-600 dark:text-gray-400"><?= htmlspecialchars($s['value']) ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex justify-end gap-2">
                                                <a href="setting?edit=<?= $s['id'] ?>" class="w-10 h-10 rounded-xl flex items-center justify-center bg-yellow-500/10 text-yellow-600 hover:bg-yellow-500/20 transition-all">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                                <button onclick="openDeleteModal('setting?del=<?= $s['id'] ?>','<?= htmlspecialchars($s['key_name']) ?>')" class="w-10 h-10 rounded-xl flex items-center justify-center bg-red-500/10 text-red-600 hover:bg-red-500/20 transition-all">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (!$settings): ?>
                                    <tr>
                                        <td colspan="3" class="px-6 py-16 text-center text-gray-400 font-bold">No settings found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="w-full max-w-md bg-white dark:bg-dark-800 rounded-3xl border border-gray-200 dark:border-white/5 p-6">
        <div class="w-14 h-14 rounded-2xl bg-red-500/10 text-red-500 flex items-center justify-center mb-5 mx-auto">
            <i class="fa-solid fa-trash text-xl"></i>
        </div>
        <h3 class="text-xl font-black text-center text-gray-900 dark:text-white mb-2">Delete Setting</h3>
        <p class="text-sm text-center text-gray-500 mb-6">Are you sure you want to delete <span id="delName" class="font-black"></span> ?</p>
        <div class="grid grid-cols-2 gap-3">
            <button onclick="closeDeleteModal()" class="py-3 rounded-2xl bg-gray-100 dark:bg-white/5 font-black">Cancel</button>
            <a id="delLink" href="#" class="py-3 rounded-2xl bg-red-500 hover:bg-red-600 text-white text-center font-black">Delete</a>
        </div>
    </div>
</div>
<script>
    function openDeleteModal(link, name) {
        document.getElementById('delLink').href = link
        document.getElementById('delName').textContent = name
        document.getElementById('deleteModal').classList.remove('hidden')
        document.getElementById('deleteModal').classList.add('flex')
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden')
        document.getElementById('deleteModal').classList.remove('flex')
    }
</script>
<?php require_once 'footer.php'; ?>