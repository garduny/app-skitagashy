<?php
require_once 'init.php';
if (get('delete')) {
    $id = request('delete', 'get');
    execute(" DELETE FROM roles WHERE id=$id ");
    redirect('roles.php?msg=deleted');
}
if (post('add_role')) {
    $name = request('name', 'post');
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $check = findQuery(" SELECT id FROM roles WHERE slug='$slug' ");
    if ($check) {
        redirect('roles.php?msg=exists');
    } else {
        execute(" INSERT INTO roles (name,slug) VALUES ('$name','$slug') ");
        redirect('roles.php?msg=added');
    }
}
if (post('edit_role')) {
    $id = request('id', 'post');
    $name = request('name', 'post');
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $check = findQuery(" SELECT id FROM roles WHERE slug='$slug' AND id!=$id ");
    if ($check) {
        redirect('roles.php?msg=exists');
    } else {
        execute(" UPDATE roles SET name='$name', slug='$slug' WHERE id=$id ");
        if (isset($_POST['perms'])) {
            execute(" DELETE FROM permission_role WHERE role_id=$id ");
            $inserts = [];
            foreach ($_POST['perms'] as $pid) {
                $inserts[] = "($id," . (int)$pid . ")";
            }
            if (!empty($inserts)) {
                execute(" INSERT INTO permission_role (role_id,permission_id) VALUES " . implode(',', $inserts));
            }
        }
        redirect('roles.php?msg=updated');
    }
}
$search = request('search', 'get');
$page = max(1, (int)(request('page', 'get') ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;
$where = "WHERE 1=1";
if ($search) {
    $where .= " AND (name LIKE '%$search%' OR slug LIKE '%$search%') ";
}
$roles = getQuery(" SELECT * FROM roles $where ORDER BY id DESC LIMIT $limit OFFSET $offset ");
$total = countQuery(" SELECT 1 FROM roles $where ");
$pages = ceil($total / $limit);
$all_perms = getQuery(" SELECT * FROM permissions ORDER BY group_name,name ");
$grouped_perms = [];
foreach ($all_perms as $p) {
    $grouped_perms[$p['group_name']][] = $p;
}
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Roles & Permissions</h1>
            <p class="text-sm text-gray-500">Manage access levels.</p>
        </div>
        <button onclick="openModal('addModal')" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl shadow-lg shadow-primary-500/20 transition-all flex items-center gap-2"><i class="fa-solid fa-plus"></i> Add Role</button>
    </div>
    <?php if (get('msg') == 'exists'): ?><div class="mb-6 p-4 bg-red-100 dark:bg-red-500/20 border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 rounded-xl font-bold text-center">Role Name/Slug already exists.</div><?php endif; ?>
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200 dark:border-white/5 flex gap-4">
            <form class="flex-1 flex gap-2"><input type="text" name="search" value="<?= $search ?>" placeholder="Search roles..." class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-gray-900 dark:text-white focus:border-primary-500 outline-none"><button type="submit" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 rounded-xl text-gray-600 dark:text-gray-300"><i class="fa-solid fa-search"></i></button></form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/5 text-xs uppercase text-gray-500 font-bold">
                        <th class="px-6 py-4">Role Name</th>
                        <th class="px-6 py-4">Slug</th>
                        <th class="px-6 py-4">Perms</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php foreach ($roles as $r):
                        $rc = (int)$r['id'];
                        $count = countQuery(" SELECT 1 FROM permission_role WHERE role_id=$rc ");
                        $my_perms = getQuery(" SELECT permission_id FROM permission_role WHERE role_id=$rc ");
                        $p_ids = array_column($my_perms, 'permission_id');
                    ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white"><?= $r['name'] ?></td>
                            <td class="px-6 py-4"><span class="px-2 py-1 bg-gray-100 dark:bg-white/10 rounded text-xs font-mono text-gray-500"><?= $r['slug'] ?></span></td>
                            <td class="px-6 py-4"><span class="px-2 py-1 bg-blue-100 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-lg text-xs font-bold"><?= $count ?> Access</span></td>
                            <td class="px-6 py-4 text-right">
                                <button onclick='editRole(<?= json_encode($r) ?>, <?= json_encode($p_ids) ?>)' class="p-2 text-gray-400 hover:text-primary-500 transition-colors"><i class="fa-solid fa-pen-to-square"></i></button>
                                <?php if ($r['slug'] !== 'super-admin'): ?><a href="?delete=<?= $r['id'] ?>" onclick="return confirm('Delete role?')" class="p-2 text-gray-400 hover:text-red-500 transition-colors"><i class="fa-solid fa-trash"></i></a><?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-white/5 flex justify-center gap-2">
            <?php for ($i = 1; $i <= $pages; $i++): ?><a href="?page=<?= $i ?>&search=<?= $search ?>" class="px-3 py-1 rounded-lg text-sm font-bold <?= $i == $page ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-white/5 text-gray-500 hover:bg-gray-200' ?>"><?= $i ?></a><?php endfor; ?>
        </div>
    </div>
</main>
<div id="addModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('addModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white dark:bg-dark-800 rounded-2xl shadow-2xl p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Add Role</h3>
        <form method="POST">
            <div class="space-y-4">
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Role Name</label><input type="text" name="name" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div><button type="submit" name="add_role" value="1" class="w-full py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl">Create Role</button>
            </div>
        </form>
    </div>
</div>
<div id="editModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('editModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl bg-white dark:bg-dark-800 rounded-2xl shadow-2xl p-6 max-h-[90vh] overflow-y-auto custom-scrollbar">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Edit Role & Permissions</h3>
        <form method="POST"><input type="hidden" name="id" id="edit_id">
            <div class="space-y-6">
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Role Name</label><input type="text" name="name" id="edit_name" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                <div class="grid grid-cols-2 gap-6"><?php foreach ($grouped_perms as $grp => $list): ?><div class="space-y-2">
                            <h4 class="text-xs font-black uppercase text-primary-500"><?= $grp ?></h4><?php foreach ($list as $perm): ?><label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 cursor-pointer hover:text-white"><input type="checkbox" name="perms[]" value="<?= $perm['id'] ?>" class="perm-check w-4 h-4 rounded border-gray-600 text-primary-500 focus:ring-0"> <?= $perm['name'] ?></label><?php endforeach; ?>
                        </div><?php endforeach; ?></div><button type="submit" name="edit_role" value="1" class="w-full py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl">Save Changes</button>
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

    function editRole(data, perms) {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_name').value = data.name;
        document.querySelectorAll('.perm-check').forEach(el => el.checked = false);
        perms.forEach(pid => {
            let cb = document.querySelector(`.perm-check[value="${pid}"]`);
            if (cb) cb.checked = true;
        });
        openModal('editModal');
    }
</script>
<?php require_once 'footer.php'; ?>