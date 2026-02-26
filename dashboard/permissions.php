<?php
require_once 'init.php';
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    execute(" DELETE FROM permissions WHERE id=$id ");
    redirect('permissions.php?msg=deleted');
}
if (post('add_permission')) {
    $name = request('name', 'post');
    $group = request('group_name', 'post');
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9.]+/', '.', $name)));
    execute(" INSERT INTO permissions (name,slug,group_name) VALUES ('$name','$slug','$group') ");
    redirect('permissions.php?msg=added');
}
if (post('edit_permission')) {
    $id = (int)$_POST['id'];
    $name = request('name', 'post');
    $group = request('group_name', 'post');
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9.]+/', '.', $name)));
    execute(" UPDATE permissions SET name='$name', slug='$slug', group_name='$group' WHERE id=$id ");
    redirect('permissions.php?msg=updated');
}
$perms = getQuery(" SELECT * FROM permissions ORDER BY group_name ASC, name ASC ");
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">System Permissions</h1>
            <p class="text-sm text-gray-500">Fine-grained access controls for roles.</p>
        </div>
        <button onclick="openModal('addModal')" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl shadow-lg shadow-primary-500/20 transition-all flex items-center gap-2"><i class="fa-solid fa-plus"></i> Add Permission</button>
    </div>
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/5 text-xs uppercase text-gray-500 font-bold">
                        <th class="px-6 py-4">Permission Name</th>
                        <th class="px-6 py-4">Slug (Code)</th>
                        <th class="px-6 py-4">Group</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php foreach ($perms as $p): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white"><?= $p['name'] ?></td>
                            <td class="px-6 py-4"><span class="px-2 py-1 bg-gray-100 dark:bg-white/10 rounded text-xs font-mono text-gray-500"><?= $p['slug'] ?></span></td>
                            <td class="px-6 py-4"><span class="px-2 py-1 bg-blue-100 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-lg text-xs font-bold uppercase"><?= $p['group_name'] ?></span></td>
                            <td class="px-6 py-4 text-right">
                                <button onclick='editPerm(<?= json_encode($p) ?>)' class="p-2 text-gray-400 hover:text-primary-500 transition-colors"><i class="fa-solid fa-pen-to-square"></i></button>
                                <a href="?delete=<?= $p['id'] ?>" onclick="return confirm('Delete this permission?')" class="p-2 text-gray-400 hover:text-red-500 transition-colors"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<div id="addModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeModal('addModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white dark:bg-dark-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-white/10 p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Add Permission</h3>
        <form method="POST">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Name</label>
                    <input type="text" name="name" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Group</label>
                    <input type="text" name="group_name" placeholder="e.g. Products" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 outline-none">
                </div>
                <button type="submit" name="add_permission" value="1" class="w-full py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl transition-all">Create Permission</button>
            </div>
        </form>
    </div>
</div>
<div id="editModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeModal('editModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white dark:bg-dark-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-white/10 p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Edit Permission</h3>
        <form method="POST">
            <input type="hidden" name="id" id="edit_id">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Name</label>
                    <input type="text" name="name" id="edit_name" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Group</label>
                    <input type="text" name="group_name" id="edit_group" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 outline-none">
                </div>
                <button type="submit" name="edit_permission" value="1" class="w-full py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl transition-all">Save Changes</button>
            </div>
        </form>
    </div>
</div>
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function editPerm(data) {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_name').value = data.name;
        document.getElementById('edit_group').value = data.group_name;
        openModal('editModal');
    }
</script>
<?php require_once 'footer.php'; ?>