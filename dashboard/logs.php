<?php
require_once 'init.php';
if (get('clear')) {
    execute(" TRUNCATE TABLE activity_log ");
    redirect('logs.php?msg=cleared');
}
$type = request('type', 'get');
$search = request('search', 'get');
$page = max(1, (int)(request('page', 'get') ?? 1));
$limit = 50;
$offset = ($page - 1) * $limit;
$where = "WHERE 1=1";
if ($type) {
    $where .= " AND l.user_type='$type' ";
}
if ($search) {
    $where .= " AND (l.action LIKE '%$search%' OR l.details LIKE '%$search%') ";
}
$sql = " SELECT l.*, 
       CASE 
         WHEN l.user_type = 'admin' THEN u.username 
         WHEN l.user_type = 'account' THEN a.accountname 
         ELSE 'System' 
       END as actor_name
       FROM activity_log l 
       LEFT JOIN users u ON (l.user_id = u.id AND l.user_type = 'admin') 
       LEFT JOIN accounts a ON (l.user_id = a.id AND l.user_type = 'account') 
       $where 
       ORDER BY l.id DESC LIMIT $limit OFFSET $offset ";
$logs = getQuery($sql);
$total = countQuery(" SELECT 1 FROM activity_log l $where ");
$pages = ceil($total / $limit);
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">System Logs</h1>
            <p class="text-sm text-gray-500">Monitor Admin and Customer activity.</p>
        </div>
        <div class="flex gap-2">
            <a href="logs.php" class="px-3 py-1.5 rounded-lg text-xs font-bold <?= !$type ? 'bg-primary-500 text-white' : 'bg-white dark:bg-white/5 text-gray-500' ?>">All</a>
            <a href="?type=admin" class="px-3 py-1.5 rounded-lg text-xs font-bold <?= $type == 'admin' ? 'bg-purple-500 text-white' : 'bg-white dark:bg-white/5 text-gray-500' ?>">Admins</a>
            <a href="?type=account" class="px-3 py-1.5 rounded-lg text-xs font-bold <?= $type == 'account' ? 'bg-blue-500 text-white' : 'bg-white dark:bg-white/5 text-gray-500' ?>">Accounts</a>
            <a href="?clear=1" onclick="return confirm('Clear all logs?')" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition-colors"><i class="fa-solid fa-trash"></i></a>
        </div>
    </div>
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/5 text-xs uppercase text-gray-500 font-bold">
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Actor</th>
                        <th class="px-6 py-4">Action</th>
                        <th class="px-6 py-4">IP Address</th>
                        <th class="px-6 py-4 text-right">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php if (empty($logs)): ?><tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No activity recorded yet.</td>
                        </tr><?php else: foreach ($logs as $l): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-6 py-3">
                                    <?php if ($l['user_type'] == 'admin'): ?><span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-purple-500/10 text-purple-500">Admin</span>
                                    <?php elseif ($l['user_type'] == 'account'): ?><span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-500/10 text-blue-500">Client</span>
                                    <?php else: ?><span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-gray-500/10 text-gray-500">System</span><?php endif; ?>
                                </td>
                                <td class="px-6 py-3 font-bold text-gray-900 dark:text-white"><?= $l['actor_name'] ?? 'Unknown' ?> <span class="text-xs text-gray-400 font-normal">#<?= $l['user_id'] ?></span></td>
                                <td class="px-6 py-3"><span class="font-bold"><?= $l['action'] ?></span> <span class="text-gray-500 ml-2"><?= $l['details'] ?></span></td>
                                <td class="px-6 py-3 font-mono text-xs text-gray-500"><?= $l['ip_address'] ?></td>
                                <td class="px-6 py-3 text-right text-gray-500"><?= date('M d, H:i:s', strtotime($l['created_at'])) ?></td>
                            </tr>
                    <?php endforeach;
                            endif; ?>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-white/5 flex justify-center gap-2"><?php for ($i = 1; $i <= $pages; $i++): ?><a href="?page=<?= $i ?>&type=<?= $type ?>" class="px-3 py-1 rounded-lg text-sm font-bold <?= $i == $page ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-white/5 text-gray-500 hover:bg-gray-200' ?>"><?= $i ?></a><?php endfor; ?></div>
    </div>
</main>
<?php require_once 'footer.php'; ?>