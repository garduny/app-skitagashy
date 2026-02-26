<?php
require_once 'init.php';
function formatSize($bytes)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    for ($i = 0; $bytes > 1024; $i++) $bytes /= 1024;
    return number_format($bytes, 2) . ' ' . $units[$i];
}
$db_name = findQuery(" SELECT DATABASE() as db")['db'];
$db_size = findQuery(" SELECT SUM(data_length + index_length) as size FROM information_schema.TABLES WHERE table_schema='$db_name'")['size'];
$server_info = [
    'php_version' => phpversion(),
    'server_software' => $_SERVER['SERVER_SOFTWARE'],
    'db_version' => findQuery(" SELECT VERSION() as v")['v'],
    'disk_free' => disk_free_space("."),
    'disk_total' => disk_total_space("."),
    'memory_limit' => ini_get('memory_limit'),
    'max_upload' => ini_get('upload_max_filesize'),
    'max_ex_time' => ini_get('max_execution_time') . 's'
];
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex items-center gap-4 mb-6">
        <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">System Status</h1>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm">
            <div class="text-xs font-bold text-gray-500 uppercase mb-2">PHP Version</div>
            <div class="text-2xl font-black text-primary-500"><?= $server_info['php_version'] ?></div>
            <div class="text-xs text-gray-400 mt-1">Memory Limit: <?= $server_info['memory_limit'] ?></div>
        </div>
        <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm">
            <div class="text-xs font-bold text-gray-500 uppercase mb-2">Database</div>
            <div class="text-2xl font-black text-blue-500">MySQL 8</div>
            <div class="text-xs text-gray-400 mt-1">Size: <?= formatSize($db_size) ?></div>
        </div>
        <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm">
            <div class="text-xs font-bold text-gray-500 uppercase mb-2">Disk Space</div>
            <div class="text-2xl font-black text-purple-500"><?= formatSize($server_info['disk_free']) ?></div>
            <div class="text-xs text-gray-400 mt-1">Free of <?= formatSize($server_info['disk_total']) ?></div>
        </div>
        <div class="bg-white dark:bg-dark-800 p-6 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm">
            <div class="text-xs font-bold text-gray-500 uppercase mb-2">Web Server</div>
            <div class="text-lg font-bold text-white truncate" title="<?= $server_info['server_software'] ?>">Apache/Nginx</div>
            <div class="text-xs text-gray-400 mt-1">Upload Max: <?= $server_info['max_upload'] ?></div>
        </div>
    </div>
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-200 dark:border-white/5">
            <h3 class="font-bold text-gray-900 dark:text-white">Database Tables</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-gray-500 border-b border-gray-200 dark:border-white/5 bg-gray-50 dark:bg-white/5">
                        <th class="px-6 py-3">Table Name</th>
                        <th class="px-6 py-3">Rows</th>
                        <th class="px-6 py-3">Size</th>
                        <th class="px-6 py-3 text-right">Engine</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php
                    $tables = getQuery(" SELECT table_name, table_rows, data_length, engine FROM information_schema.TABLES WHERE table_schema='$db_name'");
                    foreach ($tables as $t):
                    ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-3 font-bold text-gray-900 dark:text-white"><?= $t['table_name'] ?></td>
                            <td class="px-6 py-3 font-mono"><?= $t['table_rows'] ?></td>
                            <td class="px-6 py-3 text-gray-500"><?= formatSize($t['data_length']) ?></td>
                            <td class="px-6 py-3 text-right text-xs uppercase font-bold text-gray-400"><?= $t['engine'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>