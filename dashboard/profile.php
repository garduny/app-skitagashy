<?php
require_once 'init.php';
$u = user();
if (post('update_info')) {
    $username = request('username', 'post');
    $email = request('email', 'post');
    $avatar = request('avatar', 'post');
    $check = findQuery(" SELECT id FROM users WHERE (email='$email' OR username='$username') AND id!={$u['id']} ");
    if ($check) {
        $error = "Username or Email already taken.";
    } else {
        execute(" UPDATE users SET username='$username', email='$email', avatar='$avatar' WHERE id={$u['id']} ");
        redirect('profile.php?msg=updated');
    }
}
if (post('update_pass')) {
    $curr = request('current_password', 'post');
    $new = request('new_password', 'post');
    $conf = request('confirm_password', 'post');
    if (!password_verify($curr, $u['password'])) {
        $error = "Current password is incorrect.";
    } elseif ($new !== $conf) {
        $error = "New passwords do not match.";
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        execute(" UPDATE users SET password='$hash' WHERE id={$u['id']} ");
        redirect('profile.php?msg=pass_updated');
    }
}
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight mb-6">My Profile</h1>
        <?php if (isset($error)): ?><div class="p-4 mb-6 bg-red-100 dark:bg-red-500/20 border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 rounded-xl font-bold"><?= $error ?></div><?php endif; ?>
        <?php if (get('msg')): ?><div class="p-4 mb-6 bg-green-100 dark:bg-green-500/20 border border-green-200 dark:border-green-500/30 text-green-600 dark:text-green-400 rounded-xl font-bold">Profile updated successfully.</div><?php endif; ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-1 space-y-6">
                <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 text-center shadow-sm">
                    <img src="<?= $u['avatar'] ?? 'https://ui-avatars.com/api/?name=' . $u['username'] . '&background=00ffaa&color=000' ?>" class="w-32 h-32 rounded-full mx-auto mb-4 border-4 border-gray-100 dark:border-white/5">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white"><?= $u['username'] ?></h2>
                    <p class="text-sm text-gray-500 uppercase tracking-wider font-bold mb-4"><?= $u['role_name'] ?></p>
                    <div class="text-xs text-gray-400">Member since <?= date('M Y', strtotime($u['created_at'])) ?></div>
                </div>
            </div>
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 border-b border-gray-100 dark:border-white/5 pb-4">Personal Information</h3>
                    <form method="POST">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Username</label>
                                <input type="text" name="username" value="<?= $u['username'] ?>" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email</label>
                                <input type="email" name="email" value="<?= $u['email'] ?>" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 outline-none">
                            </div>
                        </div>
                        <div class="mb-6">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Avatar URL</label>
                            <input type="text" name="avatar" value="<?= $u['avatar'] ?>" placeholder="https://..." class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 outline-none">
                        </div>
                        <button type="submit" name="update_info" value="1" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl transition-all">Update Profile</button>
                    </form>
                </div>
                <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 border-b border-gray-100 dark:border-white/5 pb-4">Security</h3>
                    <form method="POST">
                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Current Password</label>
                                <input type="password" name="current_password" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 outline-none">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">New Password</label>
                                    <input type="password" name="new_password" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Confirm Password</label>
                                    <input type="password" name="confirm_password" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:border-primary-500 outline-none">
                                </div>
                            </div>
                        </div>
                        <button type="submit" name="update_pass" value="1" class="px-6 py-2.5 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-200 text-white dark:text-gray-900 font-bold rounded-xl transition-all">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>