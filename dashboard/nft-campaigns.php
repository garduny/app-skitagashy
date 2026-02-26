<?php
require_once 'init.php';
if (get('delete')) {
    $id = (int)request('delete', 'get');
    execute(" DELETE FROM nft_burn_campaigns WHERE id=$id ");
    redirect('nft-campaigns.php?msg=deleted');
}
if (post('save_campaign')) {
    $id = (int)request('id', 'post');
    $name = secure(request('name', 'post'));
    $addr = secure(request('collection_address', 'post'));
    $reward = (float)request('reward_amount', 'post');
    $active = (int)request('is_active', 'post');
    if ($id) {
        execute(" UPDATE nft_burn_campaigns SET name='$name',collection_address='$addr',reward_amount=$reward,is_active=$active WHERE id=$id ");
        redirect('nft-campaigns.php?msg=updated');
    } else {
        execute(" INSERT INTO nft_burn_campaigns (name,collection_address,reward_amount,is_active) VALUES ('$name','$addr',$reward,$active) ");
        redirect('nft-campaigns.php?msg=created');
    }
}
$campaigns = getQuery(" SELECT * FROM nft_burn_campaigns ORDER BY id DESC ");
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">NFT Burn Campaigns</h1>
            <p class="text-sm text-gray-500">Manage burnable collections and rewards.</p>
        </div>
        <button onclick="openModal('campModal');resetForm();" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl shadow-lg shadow-primary-500/20 transition-all flex items-center gap-2"><i class="fa-solid fa-fire"></i> Add Campaign</button>
    </div>
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/5 text-xs uppercase text-gray-500 font-bold">
                        <th class="px-6 py-4">Campaign Name</th>
                        <th class="px-6 py-4">Collection Address</th>
                        <th class="px-6 py-4">Reward</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php foreach ($campaigns as $c): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white"><?= $c['name'] ?></td>
                            <td class="px-6 py-4 font-mono text-xs text-gray-500"><?= $c['collection_address'] ?></td>
                            <td class="px-6 py-4 font-mono font-bold text-green-500"><?= number_format($c['reward_amount']) ?> G</td>
                            <td class="px-6 py-4"><?= $c['is_active'] ? '<span class="text-green-500 text-xs font-bold bg-green-500/10 px-2 py-1 rounded">Active</span>' : '<span class="text-gray-500 text-xs font-bold bg-gray-500/10 px-2 py-1 rounded">Inactive</span>' ?></td>
                            <td class="px-6 py-4 text-right"><button onclick='editCamp(<?= json_encode($c) ?>)' class="p-2 text-gray-400 hover:text-primary-500 transition-colors"><i class="fa-solid fa-pen-to-square"></i></button><a href="?delete=<?= $c['id'] ?>" onclick="return confirm('Delete campaign?')" class="p-2 text-gray-400 hover:text-red-500 transition-colors"><i class="fa-solid fa-trash"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<div id="campModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('campModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white dark:bg-dark-800 rounded-2xl shadow-2xl p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6" id="modalTitle">Add Campaign</h3>
        <form method="POST"><input type="hidden" name="id" id="c_id">
            <div class="space-y-4">
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Name</label><input type="text" name="name" id="c_name" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Collection Address</label><input type="text" name="collection_address" id="c_addr" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none font-mono text-xs"></div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Reward (GASHY)</label><input type="number" step="0.01" name="reward_amount" id="c_reward" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label><select name="is_active" id="c_status" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select></div>
                <button type="submit" name="save_campaign" value="1" class="w-full py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl">Save</button>
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
        document.getElementById('c_id').value = '';
        document.getElementById('c_name').value = '';
        document.getElementById('c_addr').value = '';
        document.getElementById('c_reward').value = '';
        document.getElementById('c_status').value = '1';
        document.getElementById('modalTitle').innerText = 'New Campaign';
    }

    function editCamp(c) {
        document.getElementById('c_id').value = c.id;
        document.getElementById('c_name').value = c.name;
        document.getElementById('c_addr').value = c.collection_address;
        document.getElementById('c_reward').value = c.reward_amount;
        document.getElementById('c_status').value = c.is_active;
        document.getElementById('modalTitle').innerText = 'Edit Campaign';
        openModal('campModal');
    }
</script>
<?php require_once 'footer.php'; ?>