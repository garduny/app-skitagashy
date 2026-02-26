<?php
require_once 'init.php';
if (get('delete')) {
    $id = request('delete', 'get');
    if ($id !== 1 && $id != (int)user()['id']) {
        execute(" DELETE FROM users WHERE id=$id ");
    }
    redirect('users.php?msg=deleted');
}
if (post('add_user')) {
    $username = request('username', 'post');
    $email = request('email', 'post');
    $role = (int)$_POST['role_id'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $check = findQuery(" SELECT id FROM users WHERE email='$email' OR username='$username' ");
    if ($check) {
        $error = "Duplicate entry.";
    } else {
        execute(" INSERT INTO users (role_id,username,email,password,created_at) VALUES ($role,'$username','$email','$pass',NOW()) ");
        redirect('users.php?msg=added');
    }
}
if (post('edit_user')) {
    $id = (int)$_POST['id'];
    $username = request('username', 'post');
    $email = request('email', 'post');
    $role = (int)$_POST['role_id'];
    $status = (int)$_POST['is_active'];
    $sql_pass = "";
    if (!empty($_POST['password'])) {
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql_pass = ", password='$hash'";
    }
    execute(" UPDATE users SET username='$username', email='$email', role_id=$role, is_active=$status $sql_pass WHERE id=$id ");
    redirect('users.php?msg=updated');
}
$search = request('search', 'get');
$page = max(1, (int)(request('page', 'get') ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;
$where = "WHERE 1=1";
if ($search) {
    $where .= " AND (username LIKE '%$search%' OR email LIKE '%$search%') ";
}
$users = getQuery(" SELECT u.*,r.name as role_name FROM users u JOIN roles r ON u.role_id=r.id $where ORDER BY u.id DESC LIMIT $limit OFFSET $offset ");
$total = countQuery(" SELECT 1 FROM users u $where ");
$pages = ceil($total / $limit);
$roles = getQuery(" SELECT * FROM roles ");
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Admin Users</h1>
            <p class="text-sm text-gray-500">Manage administrators.</p>
        </div>
        <button onclick="openModal('addModal')" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl shadow-lg shadow-primary-500/20 transition-all flex items-center gap-2"><i class="fa-solid fa-plus"></i> Add User</button>
    </div>
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200 dark:border-white/5 flex gap-4">
            <form class="flex-1 flex gap-2"><input type="text" name="search" value="<?= $search ?>" placeholder="Search admins..." class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-gray-900 dark:text-white focus:border-primary-500 outline-none"><button type="submit" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 rounded-xl text-gray-600 dark:text-gray-300"><i class="fa-solid fa-search"></i></button></form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/5 text-xs uppercase text-gray-500 font-bold">
                        <th class="px-6 py-4">Admin</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Created</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php foreach ($users as $u): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3"><img src="https://ui-avatars.com/api/?name=<?= $u['username'] ?>&background=random" class="w-8 h-8 rounded-lg">
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-sm"><?= $u['username'] ?></div>
                                        <div class="text-xs text-gray-500"><?= $u['email'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-blue-100 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400"><?= $u['role_name'] ?></span></td>
                            <td class="px-6 py-4"><?= $u['is_active'] ? '<span class="text-green-500 text-xs font-bold">Active</span>' : '<span class="text-red-500 text-xs font-bold">Inactive</span>' ?></td>
                            <td class="px-6 py-4 text-sm text-gray-500"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                            <td class="px-6 py-4 text-right"><button onclick='editUser(<?= json_encode($u) ?>)' class="p-2 text-gray-400 hover:text-primary-500 transition-colors"><i class="fa-solid fa-pen-to-square"></i></button><?php if ($u['id'] != 1 && $u['id'] != user()['id']): ?><a href="?delete=<?= $u['id'] ?>" onclick="return confirm('Confirm?')" class="p-2 text-gray-400 hover:text-red-500 transition-colors"><i class="fa-solid fa-trash"></i></a><?php endif; ?></td>
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
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Add Admin</h3>
        <form method="POST">
            <div class="space-y-4">
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Username</label><input type="text" name="username" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email</label><input type="email" name="email" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Role</label><select name="role_id" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"><?php foreach ($roles as $r): ?><option value="<?= $r['id'] ?>"><?= $r['name'] ?></option><?php endforeach; ?></select></div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Password</label><input type="password" name="password" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div><button type="submit" name="add_user" value="1" class="w-full py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl">Create</button>
            </div>
        </form>
    </div>
</div>
<div id="editModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('editModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white dark:bg-dark-800 rounded-2xl shadow-2xl p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Edit Admin</h3>
        <form method="POST"><input type="hidden" name="id" id="edit_id">
            <div class="space-y-4">
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Username</label><input type="text" name="username" id="edit_username" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email</label><input type="email" name="email" id="edit_email" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Role</label><select name="role_id" id="edit_role" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"><?php foreach ($roles as $r): ?><option value="<?= $r['id'] ?>"><?= $r['name'] ?></option><?php endforeach; ?></select></div>
                    <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label><select name="is_active" id="edit_status" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select></div>
                </div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">New Password (Optional)</label><input type="password" name="password" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div><button type="submit" name="edit_user" value="1" class="w-full py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl">Save Changes</button>
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

    function editUser(data) {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_username').value = data.username;
        document.getElementById('edit_email').value = data.email;
        document.getElementById('edit_role').value = data.role_id;
        document.getElementById('edit_status').value = data.is_active;
        openModal('editModal');
    }
</script>
<?php require_once 'footer.php'; ?>