<?php
require_once 'init.php';
if (post('new_round')) {
    $last = findQuery(" SELECT MAX(round_number) as r FROM lottery_rounds");
    $next = ($last['r'] ?? 0) + 1;
    $draw = request('draw_time', 'post');
    execute(" INSERT INTO lottery_rounds (round_number,prize_pool,draw_time,status) VALUES ($next,0,'$draw','open') ");
    redirect('lotteries.php?msg=created');
}
$status = request('status', 'get');
$page = max(1, (int)(request('page', 'get') ?: 1));
$limit = 10;
$offset = ($page - 1) * $limit;
$where = "WHERE 1=1";
if ($status) {
    $where .= " AND status='$status' ";
}
$rounds = getQuery(" SELECT * FROM lottery_rounds $where ORDER BY id DESC LIMIT $limit OFFSET $offset ");
$total = countQuery(" SELECT 1 FROM lottery_rounds $where ");
$pages = ceil($total / $limit);
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Lottery Rounds</h1>
            <p class="text-sm text-gray-500">Manage <?= $total ?> rounds.</p>
        </div>
        <button onclick="openModal('addModal')" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl shadow-lg shadow-primary-500/20 transition-all flex items-center gap-2"><i class="fa-solid fa-plus"></i> New Round</button>
    </div>
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200 dark:border-white/5 flex gap-4">
            <div class="flex bg-gray-50 dark:bg-dark-900 p-1 rounded-xl border border-gray-200 dark:border-white/10">
                <a href="lotteries.php" class="px-4 py-2 rounded-lg text-sm font-bold <?= !$status ? 'bg-white dark:bg-white/10 shadow-sm text-gray-900 dark:text-white' : 'text-gray-500' ?>">All</a>
                <a href="?status=open" class="px-4 py-2 rounded-lg text-sm font-bold <?= $status == 'open' ? 'bg-white dark:bg-white/10 shadow-sm text-green-500' : 'text-gray-500' ?>">Open</a>
                <a href="?status=closed" class="px-4 py-2 rounded-lg text-sm font-bold <?= $status == 'closed' ? 'bg-white dark:bg-white/10 shadow-sm text-gray-900 dark:text-white' : 'text-gray-500' ?>">Closed</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/5 text-xs uppercase text-gray-500 font-bold">
                        <th class="px-6 py-4">Round #</th>
                        <th class="px-6 py-4">Prize Pool</th>
                        <th class="px-6 py-4">Draw Date</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php foreach ($rounds as $r): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">#<?= $r['round_number'] ?></td>
                            <td class="px-6 py-4 font-mono font-bold text-green-500"><?= number_format($r['prize_pool']) ?> G</td>
                            <td class="px-6 py-4 text-sm text-gray-500"><?= date('M d, Y H:i', strtotime($r['draw_time'])) ?></td>
                            <td class="px-6 py-4"><span class="px-2 py-1 rounded text-[10px] uppercase font-bold <?= $r['status'] == 'open' ? 'bg-green-500/10 text-green-500' : 'bg-gray-100 dark:bg-white/10 text-gray-500' ?>"><?= $r['status'] ?></span></td>
                            <td class="px-6 py-4 text-right"><a href="lotterydetail.php?id=<?= $r['id'] ?>" class="p-2 text-gray-400 hover:text-primary-500"><i class="fa-solid fa-eye"></i> Manage</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-white/5 flex justify-center gap-2"><?php if ($pages > 1): for ($i = 1; $i <= $pages; $i++): ?><a href="?page=<?= $i ?>&status=<?= $status ?>" class="px-3 py-1 rounded-lg text-sm font-bold <?= $i == $page ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-white/5 text-gray-500 hover:bg-gray-200 dark:hover:bg-white/10' ?>"><?= $i ?></a><?php endfor;
                                                                                                                                                                                                                                                                                                                                                                                                        endif; ?></div>
    </div>
</main>
<div id="addModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('addModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white dark:bg-dark-800 rounded-2xl shadow-2xl p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Start New Round</h3>
        <form method="POST">
            <div class="space-y-4">
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Draw Date</label><input type="datetime-local" name="draw_time" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div><button type="submit" name="new_round" value="1" class="w-full py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl">Create</button>
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
</script>
<?php require_once 'footer.php'; ?>