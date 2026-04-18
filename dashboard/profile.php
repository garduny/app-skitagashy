<?php
require_once 'init.php';
$u = user();
if (post('update_info')) {
    $username = trim(request('username', 'post'));
    $email = trim(request('email', 'post'));
    $avatar = $u['avatar'];
    $check = findQuery(" SELECT id FROM users WHERE (email='$email' OR username='$username') AND id!={$u['id']} ");
    if ($check) {
        $error = "Username or Email already taken.";
    } else {
        $uploadPath = '../server/uploads/users/';
        $dbPath = '/server/uploads/users/';
        if (!empty($_FILES['image']['name'])) {
            $newImage = upload('image', $uploadPath);
            if ($newImage) $avatar = $dbPath . basename($newImage);
        }
        execute(" UPDATE users SET username='$username',email='$email',avatar='$avatar' WHERE id={$u['id']} ");
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
    } elseif (strlen($new) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        execute(" UPDATE users SET password='$hash' WHERE id={$u['id']} ");
        redirect('profile.php?msg=pass_updated');
    }
}
require_once 'header.php';
require_once 'sidebar.php';
$avatar = $u['avatar'] ? '../' . $u['avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode($u['username']) . '&background=00ffaa&color=000';
$msg = get('msg');
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="max-w-6xl mx-auto space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.25em] font-black text-primary-600 mb-2">Account Center</p>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">My Profile</h1>
            </div>
            <div class="hidden sm:flex items-center gap-2 px-4 py-2 rounded-2xl bg-white dark:bg-dark-800 border border-gray-200 dark:border-white/5 shadow-sm">
                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Secure Session</span>
            </div>
        </div>
        <?php if (isset($error)): ?>
            <div class="p-4 rounded-2xl border border-red-200 dark:border-red-500/20 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 font-bold"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($msg): ?>
            <div class="p-4 rounded-2xl border border-green-200 dark:border-green-500/20 bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-400 font-bold"><?= $msg === 'pass_updated' ? 'Password updated successfully.' : 'Profile updated successfully.' ?></div>
        <?php endif; ?>
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="space-y-6">
                <div class="bg-white dark:bg-dark-800 rounded-3xl border border-gray-200 dark:border-white/5 p-6 shadow-sm overflow-hidden relative">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-primary-500 via-cyan-400 to-purple-500"></div>
                    <img src="<?= $avatar ?>" class="w-28 h-28 rounded-full mx-auto object-cover border-4 border-gray-100 dark:border-white/10 shadow-lg">
                    <div class="text-center mt-5">
                        <h2 class="text-xl font-black text-gray-900 dark:text-white"><?= $u['username'] ?></h2>
                        <p class="text-xs font-bold uppercase tracking-[0.25em] text-gray-500 mt-2"><?= $u['role_name'] ?></p>
                        <p class="text-sm text-gray-400 mt-4">Member since <?= date('M Y', strtotime($u['created_at'])) ?></p>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mt-6">
                        <div class="rounded-2xl bg-gray-50 dark:bg-dark-900 p-4 text-center border border-gray-100 dark:border-white/5">
                            <p class="text-xs font-bold uppercase text-gray-400">Status</p>
                            <p class="text-sm font-black text-green-500 mt-1">Active</p>
                        </div>
                        <div class="rounded-2xl bg-gray-50 dark:bg-dark-900 p-4 text-center border border-gray-100 dark:border-white/5">
                            <p class="text-xs font-bold uppercase text-gray-400">Role</p>
                            <p class="text-sm font-black text-primary-600 mt-1"><?= $u['role_name'] ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="xl:col-span-2 space-y-6">
                <div class="bg-white dark:bg-dark-800 rounded-3xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-3 mb-6 pb-4 border-b border-gray-100 dark:border-white/5">
                        <div>
                            <h3 class="text-xl font-black text-gray-900 dark:text-white">Personal Information</h3>
                            <p class="text-sm text-gray-500 mt-1">Manage your public account details.</p>
                        </div>
                    </div>
                    <form method="POST" enctype="multipart/form-data" class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-black uppercase tracking-[0.18em] text-gray-500 mb-2">Username</label>
                                <input type="text" name="username" value="<?= $u['username'] ?>" required class="w-full rounded-2xl px-4 py-3 bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-xs font-black uppercase tracking-[0.18em] text-gray-500 mb-2">Email</label>
                                <input type="email" name="email" value="<?= $u['email'] ?>" required class="w-full rounded-2xl px-4 py-3 bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase tracking-[0.18em] text-gray-500 mb-2">Avatar</label>
                            <input type="file" name="image" accept="image/*" class="w-full rounded-2xl px-4 py-3 bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white">
                        </div>
                        <button type="submit" name="update_info" value="1" class="px-6 py-3 rounded-2xl bg-primary-600 hover:bg-primary-500 text-white font-black transition-all shadow-lg shadow-primary-500/20">Update Profile</button>
                    </form>
                </div>
                <div class="bg-white dark:bg-dark-800 rounded-3xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-3 mb-6 pb-4 border-b border-gray-100 dark:border-white/5">
                        <div>
                            <h3 class="text-xl font-black text-gray-900 dark:text-white">Security</h3>
                            <p class="text-sm text-gray-500 mt-1">Update your password and keep your account safe.</p>
                        </div>
                    </div>
                    <form method="POST" class="space-y-5">
                        <div>
                            <label class="block text-xs font-black uppercase tracking-[0.18em] text-gray-500 mb-2">Current Password</label>
                            <input type="password" name="current_password" required class="w-full rounded-2xl px-4 py-3 bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-primary-500/30">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-black uppercase tracking-[0.18em] text-gray-500 mb-2">New Password</label>
                                <input type="password" name="new_password" required class="w-full rounded-2xl px-4 py-3 bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-primary-500/30">
                            </div>
                            <div>
                                <label class="block text-xs font-black uppercase tracking-[0.18em] text-gray-500 mb-2">Confirm Password</label>
                                <input type="password" name="confirm_password" required class="w-full rounded-2xl px-4 py-3 bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-primary-500/30">
                            </div>
                        </div>
                        <button type="submit" name="update_pass" value="1" class="px-6 py-3 rounded-2xl bg-gray-900 hover:bg-black dark:bg-white dark:hover:bg-gray-100 text-white dark:text-gray-900 font-black transition-all">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>