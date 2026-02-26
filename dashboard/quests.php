<?php
require_once 'init.php';
if (get('delete')) {
    $id = (int)request('delete', 'get');
    execute(" DELETE FROM quests WHERE id=$id ");
    redirect('quests.php?msg=deleted');
}
if (post('save_quest')) {
    $id = (int)request('id', 'post');
    $title = request('title', 'post');
    $type = request('action_type', 'post');
    $target = (int)request('target_count', 'post');
    $reward = (float)request('reward_gashy', 'post');
    $period = request('reset_period', 'post');
    if ($id) {
        execute(" UPDATE quests SET title='$title',action_type='$type',target_count=$target,reward_gashy=$reward,reset_period='$period' WHERE id=$id ");
        redirect('quests.php?msg=updated');
    } else {
        execute(" INSERT INTO quests (title,action_type,target_count,reward_gashy,reset_period,is_active) VALUES ('$title','$type',$target,$reward,'$period',1) ");
        redirect('quests.php?msg=created');
    }
}
$quests = getQuery(" SELECT * FROM quests ORDER BY id DESC ");
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Quests System</h1>
            <p class="text-sm text-gray-500">Manage user engagement tasks.</p>
        </div>
        <button onclick="openModal('questModal');resetForm();" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl shadow-lg shadow-primary-500/20 transition-all flex items-center gap-2"><i class="fa-solid fa-plus"></i> Add Quest</button>
    </div>
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/5 text-xs uppercase text-gray-500 font-bold">
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Action</th>
                        <th class="px-6 py-4">Target</th>
                        <th class="px-6 py-4">Reward</th>
                        <th class="px-6 py-4">Period</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php foreach ($quests as $q): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white"><?= $q['title'] ?></td>
                            <td class="px-6 py-4"><span class="px-2 py-1 rounded text-[10px] uppercase font-bold bg-blue-500/10 text-blue-500"><?= $q['action_type'] ?></span></td>
                            <td class="px-6 py-4"><?= $q['target_count'] ?></td>
                            <td class="px-6 py-4 font-mono text-green-500 font-bold"><?= number_format($q['reward_gashy']) ?> G</td>
                            <td class="px-6 py-4 capitalize text-sm text-gray-500"><?= $q['reset_period'] ?></td>
                            <td class="px-6 py-4 text-right"><button onclick='editQuest(<?= json_encode($q) ?>)' class="p-2 text-gray-400 hover:text-primary-500 transition-colors"><i class="fa-solid fa-pen-to-square"></i></button><a href="?delete=<?= $q['id'] ?>" onclick="return confirm('Delete?')" class="p-2 text-gray-400 hover:text-red-500 transition-colors"><i class="fa-solid fa-trash"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<div id="questModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('questModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white dark:bg-dark-800 rounded-2xl shadow-2xl p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6" id="modalTitle">New Quest</h3>
        <form method="POST"><input type="hidden" name="id" id="q_id">
            <div class="space-y-4">
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Quest Title</label><input type="text" name="title" id="q_title" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Action Type</label><select name="action_type" id="q_type" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none">
                            <option value="burn">Burn (Spend)</option>
                            <option value="buy">Buy Item</option>
                            <option value="login">Login</option>
                        </select></div>
                    <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Target Amount</label><input type="number" name="target_count" id="q_target" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Reward (GASHY)</label><input type="number" step="0.01" name="reward_gashy" id="q_reward" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                    <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Reset Period</label><select name="reset_period" id="q_period" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="once">One Time</option>
                        </select></div>
                </div>
                <button type="submit" name="save_quest" value="1" class="w-full py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl">Save Quest</button>
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

    function resetForm() {
        document.getElementById('q_id').value = '';
        document.getElementById('modalTitle').innerText = 'New Quest';
    }

    function editQuest(q) {
        document.getElementById('q_id').value = q.id;
        document.getElementById('q_title').value = q.title;
        document.getElementById('q_type').value = q.action_type;
        document.getElementById('q_target').value = q.target_count;
        document.getElementById('q_reward').value = q.reward_gashy;
        document.getElementById('q_period').value = q.reset_period;
        document.getElementById('modalTitle').innerText = 'Edit Quest';
        openModal('questModal');
    }
</script>
<?php require_once 'footer.php'; ?>