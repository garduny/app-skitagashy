<?php
require_once 'init.php';
if (get('delete')) {
    $id = (int)request('delete', 'get');
    execute(" DELETE FROM account_quests WHERE quest_id=$id ");
    execute(" DELETE FROM quests WHERE id=$id ");
    redirect('quests.php?msg=deleted');
}
if (get('toggle')) {
    $id = (int)request('toggle', 'get');
    execute(" UPDATE quests SET is_active=IF(is_active=1,0,1) WHERE id=$id ");
    redirect('quests.php?msg=updated');
}
if (post('save_quest')) {
    $id = (int)request('id', 'post');
    $title = secure(request('title', 'post'));
    $type = secure(request('action_type', 'post'));
    $target = (int)request('target_count', 'post');
    $reward = (float)request('reward_gashy', 'post');
    $period = secure(request('reset_period', 'post'));
    if ($id) {
        execute(" UPDATE quests SET title='$title',action_type='$type',target_count=$target,reward_gashy=$reward,reset_period='$period' WHERE id=$id ");
        redirect('quests.php?msg=updated');
    } else {
        execute(" INSERT INTO quests(title,action_type,target_count,reward_gashy,reset_period,is_active) VALUES('$title','$type',$target,$reward,'$period',1) ");
        redirect('quests.php?msg=created');
    }
}
$quests = getQuery(" SELECT q.*,(SELECT COUNT(*) FROM account_quests aq WHERE aq.quest_id=q.id) joined,(SELECT COUNT(*) FROM account_quests aq WHERE aq.quest_id=q.id AND aq.is_claimed=1) claimed FROM quests q ORDER BY q.id DESC ");
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-24 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Quests System</h1>
            <p class="text-sm text-gray-500 mt-1">Manage engagement missions, rewards and progress.</p>
        </div>
        <button onclick="openModal('questModal');resetForm();" class="px-6 py-3 rounded-2xl bg-primary-600 hover:bg-primary-500 text-white font-bold shadow-lg shadow-primary-500/20 transition-all flex items-center gap-2"><i class="fa-solid fa-plus"></i>Add Quest</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="rounded-2xl border border-gray-200 dark:border-white/5 bg-white dark:bg-dark-800 p-5">
            <div class="text-xs uppercase text-gray-500 font-bold">Total Quests</div>
            <div class="mt-2 text-3xl font-black text-gray-900 dark:text-white"><?= count($quests) ?></div>
        </div>
        <div class="rounded-2xl border border-gray-200 dark:border-white/5 bg-white dark:bg-dark-800 p-5">
            <div class="text-xs uppercase text-gray-500 font-bold">Active</div>
            <div class="mt-2 text-3xl font-black text-green-500"><?= count(array_filter($quests, function ($q) {
                                                                        return $q['is_active'] == 1;
                                                                    })) ?></div>
        </div>
        <div class="rounded-2xl border border-gray-200 dark:border-white/5 bg-white dark:bg-dark-800 p-5">
            <div class="text-xs uppercase text-gray-500 font-bold">Players Joined</div>
            <div class="mt-2 text-3xl font-black text-blue-500"><?= array_sum(array_column($quests, 'joined')) ?></div>
        </div>
        <div class="rounded-2xl border border-gray-200 dark:border-white/5 bg-white dark:bg-dark-800 p-5">
            <div class="text-xs uppercase text-gray-500 font-bold">Rewards Claimed</div>
            <div class="mt-2 text-3xl font-black text-purple-500"><?= array_sum(array_column($quests, 'claimed')) ?></div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 dark:border-white/5 bg-white dark:bg-dark-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/5 text-xs uppercase text-gray-500 font-bold">
                        <th class="px-6 py-4">Quest</th>
                        <th class="px-6 py-4">Action</th>
                        <th class="px-6 py-4">Target</th>
                        <th class="px-6 py-4">Reward</th>
                        <th class="px-6 py-4">Period</th>
                        <th class="px-6 py-4">Stats</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php foreach ($quests as $q): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-white"><?= $q['title'] ?></div>
                                <div class="text-xs text-gray-500">#<?= $q['id'] ?></div>
                            </td>
                            <td class="px-6 py-4"><span class="px-2 py-1 rounded-lg text-[10px] uppercase font-bold bg-blue-500/10 text-blue-500"><?= $q['action_type'] ?></span></td>
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white"><?= number_format($q['target_count']) ?></td>
                            <td class="px-6 py-4 font-mono font-bold text-green-500"><?= number_format($q['reward_gashy'], 2) ?> G</td>
                            <td class="px-6 py-4 capitalize text-sm text-gray-500"><?= $q['reset_period'] ?></td>
                            <td class="px-6 py-4 text-sm">
                                <div class="text-gray-900 dark:text-white font-bold"><?= $q['joined'] ?> Joined</div>
                                <div class="text-xs text-gray-500"><?= $q['claimed'] ?> Claimed</div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="?toggle=<?= $q['id'] ?>" class="px-2.5 py-1 rounded-lg text-[10px] uppercase font-bold <?= $q['is_active'] ? 'bg-green-500/10 text-green-500' : 'bg-red-500/10 text-red-500' ?>">
                                    <?= $q['is_active'] ? 'Active' : 'Disabled' ?>
                                </a>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <button onclick='editQuest(<?= json_encode($q) ?>)' class="w-9 h-9 rounded-xl text-gray-400 hover:text-primary-500 hover:bg-primary-500/10 transition-all"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button onclick="openDelete(<?= $q['id'] ?>,'<?= htmlspecialchars($q['title'], ENT_QUOTES) ?>')" class="w-9 h-9 rounded-xl text-gray-400 hover:text-red-500 hover:bg-red-500/10 transition-all"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$quests): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-500">No quests found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<div id="questModal" class="fixed inset-0 z-[70] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('questModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-xl bg-white dark:bg-dark-800 rounded-3xl shadow-2xl p-6">
        <h3 id="modalTitle" class="text-2xl font-black text-gray-900 dark:text-white mb-6">New Quest</h3>
        <form method="POST">
            <input type="hidden" name="id" id="q_id">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Quest Title</label>
                    <input type="text" name="title" id="q_title" required class="w-full rounded-2xl px-4 py-3 bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white outline-none">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Action Type</label>
                        <select name="action_type" id="q_type" class="w-full rounded-2xl px-4 py-3 bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white outline-none">
                            <option value="burn">Burn</option>
                            <option value="buy">Buy Item</option>
                            <option value="login">Login</option>
                            <option value="order">Complete Order</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Target Count</label>
                        <input type="number" min="1" name="target_count" id="q_target" required class="w-full rounded-2xl px-4 py-3 bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Reward GASHY</label>
                        <input type="number" step="0.01" min="0" name="reward_gashy" id="q_reward" required class="w-full rounded-2xl px-4 py-3 bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Reset Period</label>
                        <select name="reset_period" id="q_period" class="w-full rounded-2xl px-4 py-3 bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white outline-none">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="once">One Time</option>
                        </select>
                    </div>
                </div>

                <button type="submit" name="save_quest" value="1" class="w-full py-3 rounded-2xl bg-primary-600 hover:bg-primary-500 text-white font-bold transition-all">Save Quest</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteModal" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('deleteModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white dark:bg-dark-800 rounded-3xl p-6 shadow-2xl">
        <div class="text-xl font-black text-gray-900 dark:text-white mb-2">Delete Quest</div>
        <div class="text-sm text-gray-500 mb-6">Are you sure you want to delete <span id="deleteTitle" class="font-bold"></span> ?</div>
        <div class="grid grid-cols-2 gap-3">
            <button onclick="closeModal('deleteModal')" class="py-3 rounded-2xl bg-gray-100 dark:bg-dark-900 text-gray-700 dark:text-gray-300 font-bold">Cancel</button>
            <a id="deleteBtn" href="#" class="py-3 rounded-2xl bg-red-600 hover:bg-red-500 text-white font-bold text-center">Delete</a>
        </div>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden')
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden')
    }

    function resetForm() {
        q_id.value = '';
        q_title.value = '';
        q_type.value = 'burn';
        q_target.value = '';
        q_reward.value = '';
        q_period.value = 'daily';
        modalTitle.innerText = 'New Quest';
    }

    function editQuest(q) {
        q_id.value = q.id;
        q_title.value = q.title;
        q_type.value = q.action_type;
        q_target.value = q.target_count;
        q_reward.value = q.reward_gashy;
        q_period.value = q.reset_period;
        modalTitle.innerText = 'Edit Quest';
        openModal('questModal');
    }

    function openDelete(id, title) {
        deleteTitle.innerText = title;
        deleteBtn.href = '?delete=' + id;
        openModal('deleteModal');
    }
</script>
<?php require_once 'footer.php'; ?>