<?php
require_once 'init.php';
$uploadPath = '../server/uploads/categories/';
$dbPath = '/server/uploads/categories/';
if (get('delete')) {
    $id = (int)request('delete', 'get');
    $check = findQuery(" SELECT id FROM products WHERE category_id=$id LIMIT 1 ");
    if ($check) redirect('categories.php?msg=error_has_products');
    $row = findQuery(" SELECT icon FROM categories WHERE id=$id ");
    if (!empty($row['icon'])) {
        $f = '../' . ltrim($row['icon'], '/');
        if (file_exists($f)) @unlink($f);
    }
    execute(" DELETE FROM categories WHERE id=$id ");
    redirect('categories.php?msg=deleted');
}
if (post('add_cat')) {
    $name = secure($_POST['name']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $check = findQuery(" SELECT id FROM categories WHERE slug='$slug' ");
    if ($check) redirect('categories.php?msg=exists');
    $newImage = upload('image', $uploadPath);
    $icon = $newImage ? $dbPath . $newImage : '';
    execute(" INSERT INTO categories (name,slug,icon,is_active) VALUES ('$name','$slug','$icon',1) ");
    redirect('categories.php?msg=added');
}
if (post('edit_cat')) {
    $id = (int)request('id', 'post');
    $name = secure($_POST['name']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $check = findQuery(" SELECT id FROM categories WHERE slug='$slug' AND id!=$id ");
    if ($check) redirect('categories.php?msg=exists');
    $active = (int)$_POST['is_active'];
    $newImage = upload('image', $uploadPath);
    $old = findQuery(" SELECT icon FROM categories WHERE id=$id ");
    $icon = $newImage ? $dbPath . $newImage : $old['icon'];
    if ($newImage && $old && $old['icon']) {
        $f = '../' . ltrim($old['icon'], '/');
        if (file_exists($f)) @unlink($f);
    }
    execute(" UPDATE categories SET name='$name',slug='$slug',icon='$icon',is_active=$active WHERE id=$id ");
    redirect('categories.php?msg=updated');
}
$cats = getQuery(" SELECT c.*,(SELECT COUNT(*) FROM products WHERE category_id=c.id) prod_count FROM categories c ORDER BY c.id ASC ");
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Categories</h1>
            <p class="text-sm text-gray-500">Organize marketplace items.</p>
        </div>
        <button onclick="openModal('addModal')" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl shadow-lg shadow-primary-500/20 transition-all flex items-center gap-2"><i class="fa-solid fa-plus"></i> Add Category</button>
    </div>
    <?php $msg = request('msg', 'get');
    if ($msg == 'error_has_products'): ?><div class="p-4 mb-6 bg-red-100 dark:bg-red-500/20 border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 rounded-xl font-bold text-center"><i class="fa-solid fa-triangle-exclamation mr-2"></i> Cannot delete: Category has products attached.</div>
    <?php elseif ($msg == 'exists'): ?><div class="p-4 mb-6 bg-red-100 dark:bg-red-500/20 border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 rounded-xl font-bold text-center"><i class="fa-solid fa-triangle-exclamation mr-2"></i> Category name/slug already exists.</div>
    <?php elseif (in_array($msg, ['added', 'updated', 'deleted'])): ?><div class="p-4 mb-6 bg-green-100 dark:bg-green-500/20 border border-green-200 dark:border-green-500/30 text-green-600 dark:text-green-400 rounded-xl font-bold text-center capitalize"><i class="fa-solid fa-check-circle mr-2"></i> Category <?= $msg ?> successfully.</div><?php endif; ?>
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/5 text-xs uppercase text-gray-500 font-bold">
                        <th class="px-6 py-4">Image</th>
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Slug</th>
                        <th class="px-6 py-4">Products</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php foreach ($cats as $c): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4"><img src="../<?= $c['icon'] ?>" class="w-10 h-10 rounded-lg object-cover border border-gray-200 dark:border-white/10"></td>
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white"><?= $c['name'] ?></td>
                            <td class="px-6 py-4"><span class="px-2 py-1 bg-gray-100 dark:bg-white/10 rounded text-xs font-mono text-gray-500"><?= $c['slug'] ?></span></td>
                            <td class="px-6 py-4"><span class="px-2 py-1 bg-blue-100 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-lg text-xs font-bold"><?= $c['prod_count'] ?> Items</span></td>
                            <td class="px-6 py-4"><?= $c['is_active'] ? '<span class="text-green-500 text-xs font-bold bg-green-500/10 px-2 py-1 rounded">Active</span>' : '<span class="text-red-500 text-xs font-bold bg-red-500/10 px-2 py-1 rounded">Hidden</span>' ?></td>
                            <td class="px-6 py-4 text-right">
                                <button onclick='editCat(<?= json_encode($c) ?>)' class="p-2 text-gray-400 hover:text-primary-500 transition-colors"><i class="fa-solid fa-pen-to-square"></i></button>
                                <a href="?delete=<?= $c['id'] ?>" onclick="return confirm('Delete category?')" class="p-2 text-gray-400 hover:text-red-500 transition-colors"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<div id="addModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('addModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white dark:bg-dark-800 rounded-2xl shadow-2xl p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Add Category</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="space-y-4">
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Name</label><input type="text" name="name" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Image</label><input type="file" name="image" required accept="image/*" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                <button type="submit" name="add_cat" value="1" class="w-full py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl">Create</button>
            </div>
        </form>
    </div>
</div>
<div id="editModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('editModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white dark:bg-dark-800 rounded-2xl shadow-2xl p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Edit Category</h3>
        <form method="POST" enctype="multipart/form-data"><input type="hidden" name="id" id="edit_id">
            <div class="space-y-4">
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Name</label><input type="text" name="name" id="edit_name" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Image (optional)</label><input type="file" name="image" accept="image/*" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label><select name="is_active" id="edit_status" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none">
                        <option value="1">Active</option>
                        <option value="0">Hidden</option>
                    </select></div>
                <button type="submit" name="edit_cat" value="1" class="w-full py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl">Save Changes</button>
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

    function editCat(d) {
        edit_id.value = d.id;
        edit_name.value = d.name;
        edit_status.value = d.is_active;
        openModal('editModal')
    }
</script>
<?php require_once 'footer.php'; ?>