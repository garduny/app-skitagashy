<?php
require_once 'init.php';
if (get('clear')) {
    execute(" TRUNCATE TABLE activity_log ");
    redirect('logs.php?msg=cleared');
}
$page = max(1, (int)(request('page', 'get') ?? 1));
$limit = 50;
$offset = ($page - 1) * $limit;
$logs = getQuery(" SELECT l.*, u.username FROM activity_log l LEFT JOIN users u ON l.user_id=u.id ORDER BY l.id DESC LIMIT $limit OFFSET $offset ");
$total = countQuery(" SELECT COUNT(*) FROM activity_log ");
$pages = ceil($total / $limit);
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Activity Logs</h1>
            <p class="text-sm text-gray-500">Audit trail of system actions.</p>
        </div>
        <a href="?clear=1" onclick="return confirm('Clear all logs?')" class="px-4 py-2 bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white text-sm font-bold rounded-xl transition-colors"><i class="fa-solid fa-trash"></i> Clear History</a>
    </div>
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/5 text-xs uppercase text-gray-500 font-bold">
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Action</th>
                        <th class="px-6 py-4">IP Address</th>
                        <th class="px-6 py-4 text-right">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">No activity recorded yet.</td>
                        </tr>
                        <?php else: foreach ($logs as $l): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-6 py-3 font-bold text-gray-900 dark:text-white"><?= $l['username'] ?? 'System' ?></td>
                                <td class="px-6 py-3"><span class="px-2 py-1 bg-blue-500/10 text-blue-500 rounded text-xs font-bold uppercase"><?= $l['action'] ?></span> <span class="text-gray-500 ml-2"><?= $l['details'] ?></span></td>
                                <td class="px-6 py-3 font-mono text-xs text-gray-500"><?= $l['ip_address'] ?></td>
                                <td class="px-6 py-3 text-right text-gray-500"><?= date('M d, H:i:s', strtotime($l['created_at'])) ?></td>
                            </tr>
                    <?php endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-white/5 flex justify-center gap-2">
            <?php for ($i = 1; $i <= $pages; $i++): ?><a href="?page=<?= $i ?>" class="px-3 py-1 rounded-lg text-sm font-bold <?= $i == $page ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-white/5 text-gray-500 hover:bg-gray-200' ?>"><?= $i ?></a><?php endfor; ?>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>