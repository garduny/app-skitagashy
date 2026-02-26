<?php
require_once 'init.php';
$uploadPath = '../server/uploads/banners/';
$dbPath = '/server/uploads/banners/';
if (get('delete')) {
    $id = (int)request('delete', 'get');
    $row = findQuery(" SELECT image_path FROM banners WHERE id=$id ");
    if (!empty($row['image_path'])) {
        $oldFile = '../' . ltrim($row['image_path'], '/');
        if (file_exists($oldFile)) @unlink($oldFile);
    }
    execute(" DELETE FROM banners WHERE id=$id ");
    redirect('banners.php?msg=deleted');
}
if (post('save_banner')) {
    $id = (int)request('id', 'post');
    $link = secure(request('link_url', 'post'));
    $sort = (int)request('sort_order', 'post');
    $active = (int)request('is_active', 'post');
    $newImage = upload('image', $uploadPath);
    if ($id) {
        $old = findQuery(" SELECT image_path FROM banners WHERE id=$id ");
        $img = $newImage ? $dbPath . $newImage : $old['image_path'];
        if ($newImage && $old && $old['image_path']) {
            $oldFile = '../' . ltrim($old['image_path'], '/');
            if (file_exists($oldFile)) @unlink($oldFile);
        }
        execute(" UPDATE banners SET image_path='$img',link_url='$link',sort_order=$sort,is_active=$active WHERE id=$id ");
        redirect('banners.php?msg=updated');
    } else {
        $img = $newImage ? $dbPath . $newImage : '';
        execute(" INSERT INTO banners (image_path,link_url,sort_order,is_active) VALUES ('$img','$link',$sort,$active) ");
        redirect('banners.php?msg=created');
    }
}
$banners = getQuery(" SELECT * FROM banners ORDER BY sort_order ASC ");
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Banner Management</h1>
            <p class="text-sm text-gray-500">Manage homepage slider images.</p>
        </div>
        <button onclick="openModal('bannerModal');resetForm();" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl shadow-lg shadow-primary-500/20 transition-all flex items-center gap-2"><i class="fa-solid fa-plus"></i> Add Banner</button>
    </div>
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/5 text-xs uppercase text-gray-500 font-bold">
                        <th class="px-6 py-4">Sort</th>
                        <th class="px-6 py-4">Preview</th>
                        <th class="px-6 py-4">Link / URL</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php if (empty($banners)): ?>
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">No banners found.</td>
                        </tr>
                        <?php else: foreach ($banners as $b): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">#<?= $b['sort_order'] ?></td>
                                <td class="px-6 py-4"><img src="../<?= $b['image_path'] ?>" class="w-32 h-16 object-cover rounded-lg border border-gray-200 dark:border-white/10"></td>
                                <td class="px-6 py-4">
                                    <div class="text-xs font-mono text-primary-500 truncate max-w-[200px]"><?= $b['link_url'] ?></div>
                                </td>
                                <td class="px-6 py-4"><?= $b['is_active'] ? '<span class="text-green-500 text-xs font-bold bg-green-500/10 px-2 py-1 rounded">Active</span>' : '<span class="text-gray-500 text-xs font-bold bg-gray-500/10 px-2 py-1 rounded">Hidden</span>' ?></td>
                                <td class="px-6 py-4 text-right">
                                    <button onclick='editBanner(<?= json_encode($b) ?>)' class="p-2 text-gray-400 hover:text-primary-500 transition-colors"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <a href="?delete=<?= $b['id'] ?>" onclick="return confirm('Delete banner?')" class="p-2 text-gray-400 hover:text-red-500 transition-colors"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                    <?php endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<div id="bannerModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('bannerModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white dark:bg-dark-800 rounded-2xl shadow-2xl p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6" id="modalTitle">Add Banner</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" id="ban_id">
            <div class="space-y-4">
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Image</label><input type="file" name="image" id="ban_img" accept="image/*" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Link URL (Optional)</label><input type="text" name="link_url" id="ban_link" placeholder="market.php?cat=nft" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sort Order</label><input type="number" name="sort_order" id="ban_sort" value="0" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                    <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label><select name="is_active" id="ban_status" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none">
                            <option value="1">Active</option>
                            <option value="0">Hidden</option>
                        </select></div>
                </div>
                <button type="submit" name="save_banner" value="1" class="w-full py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl shadow-lg">Save Banner</button>
            </div>
        </form>
    </div>
</div>
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden')
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden')
    }

    function resetForm() {
        ban_id.value = '';
        ban_img.value = '';
        ban_link.value = '';
        ban_sort.value = '0';
        ban_status.value = '1';
        modalTitle.innerText = 'Add Banner'
    }

    function editBanner(b) {
        ban_id.value = b.id;
        ban_link.value = b.link_url;
        ban_sort.value = b.sort_order;
        ban_status.value = b.is_active;
        modalTitle.innerText = 'Edit Banner';
        openModal('bannerModal')
    }
</script>
<?php require_once 'footer.php'; ?>