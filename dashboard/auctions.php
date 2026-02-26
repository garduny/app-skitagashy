<?php
require_once 'init.php';
$products = getQuery(" SELECT id,title FROM products WHERE status='active' AND stock>0 ORDER BY title ASC ");
if (get('delete')) {
    $id = (int)request('delete', 'get');
    execute(" DELETE FROM auctions WHERE id=$id ");
    redirect('auctions.php?msg=deleted');
}
if (post('save_auction')) {
    $id = (int)request('id', 'post');
    $pid = (int)request('product_id', 'post');
    $start = request('start_time', 'post');
    $end = request('end_time', 'post');
    $price = (float)request('start_price', 'post');
    $reserve = (float)request('reserve_price', 'post');
    $status = request('status', 'post') ?? 'active';
    if ($id) {
        execute(" UPDATE auctions SET start_time='$start',end_time='$end',status='$status',reserve_price=$reserve WHERE id=$id ");
        redirect('auctions.php?msg=updated');
    } else {
        execute(" INSERT INTO auctions (product_id,start_time,end_time,start_price,current_bid,status,reserve_price) VALUES ($pid,'$start','$end',$price,$price,'active',$reserve) ");
        redirect('auctions.php?msg=created');
    }
}
$search = request('search', 'get');
$status = request('status', 'get');
$page = max(1, (int)(request('page', 'get') ?: 1));
$limit = 10;
$offset = ($page - 1) * $limit;
$where = "WHERE 1=1";
if ($search) {
    $where .= " AND p.title LIKE '%$search%' ";
}
if ($status) {
    $where .= " AND a.status='$status' ";
}
$auctions = getQuery(" SELECT a.*,p.title,p.images,acc.accountname as bidder_name FROM auctions a JOIN products p ON a.product_id=p.id LEFT JOIN accounts acc ON a.highest_bidder_id=acc.id $where ORDER BY a.status ASC, a.end_time ASC LIMIT $limit OFFSET $offset ");
$total = countQuery(" SELECT 1 FROM auctions a JOIN products p ON a.product_id=p.id $where ");
$pages = ceil($total / $limit);
function filterUrl($key, $val)
{
    global $search, $status;
    $p = ['search' => $search, 'status' => $status];
    $p[$key] = $val;
    return '?' . http_build_query(array_filter($p));
}
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Live Auctions</h1>
            <p class="text-sm text-gray-500">Manage <?= $total ?> bidding events.</p>
        </div>
        <button onclick="openModal('auctionModal');resetForm();" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl shadow-lg shadow-primary-500/20 transition-all flex items-center gap-2"><i class="fa-solid fa-plus"></i> Create Auction</button>
    </div>
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200 dark:border-white/5 flex flex-col md:flex-row gap-4">
            <form class="flex-1 flex gap-2">
                <select name="status" class="bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-gray-900 dark:text-white outline-none">
                    <option value="">All Status</option>
                    <option value="active" <?= $status == 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="ended" <?= $status == 'ended' ? 'selected' : '' ?>>Ended</option>
                    <option value="cancelled" <?= $status == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
                <input type="text" name="search" value="<?= $search ?>" placeholder="Search item..." class="flex-1 bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-gray-900 dark:text-white focus:border-primary-500 outline-none">
                <button type="submit" class="px-4 py-2 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 rounded-xl text-gray-600 dark:text-gray-300"><i class="fa-solid fa-search"></i></button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/5 text-xs uppercase text-gray-500 font-bold">
                        <th class="px-6 py-4">Item</th>
                        <th class="px-6 py-4">Current Bid</th>
                        <th class="px-6 py-4">Reserve</th>
                        <th class="px-6 py-4">Top Bidder</th>
                        <th class="px-6 py-4">Countdown</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php foreach ($auctions as $a): $img = json_decode($a['images'])[0] ?? '';
                        $time = strtotime($a['end_time']);
                        $diff = $time - time();
                        $h = floor($diff / 3600);
                        $m = floor(($diff % 3600) / 60); ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3"><img src="../<?= $img ?>" class="w-10 h-10 rounded-lg object-cover bg-gray-100 dark:bg-white/5">
                                    <div class="font-bold text-gray-900 dark:text-white truncate max-w-[200px]"><?= $a['title'] ?></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-primary-500"><?= number_format($a['current_bid'], 2) ?></td>
                            <td class="px-6 py-4 font-mono text-xs text-gray-500"><?= number_format($a['reserve_price'] ?? 0, 2) ?></td>
                            <td class="px-6 py-4 text-sm"><?= $a['bidder_name'] ?? '<span class="text-gray-400 italic">No Bids</span>' ?></td>
                            <td class="px-6 py-4"><span class="text-sm font-mono <?= $diff > 0 && $diff < 3600 ? 'text-red-500 font-bold' : '' ?>"><?= $diff > 0 ? "{$h}h {$m}m" : 'Ended' ?></span></td>
                            <td class="px-6 py-4"><span class="px-2 py-1 rounded text-[10px] uppercase font-bold <?= $a['status'] == 'active' ? 'bg-green-500/10 text-green-500' : ($a['status'] == 'ended' ? 'bg-blue-500/10 text-blue-500' : 'bg-red-500/10 text-red-500') ?>"><?= $a['status'] ?></span></td>
                            <td class="px-6 py-4 text-right"><a href="auctiondetail.php?id=<?= $a['id'] ?>" class="p-2 text-gray-400 hover:text-blue-500 transition-colors"><i class="fa-solid fa-eye"></i></a><button onclick='editAuction(<?= json_encode($a) ?>)' class="p-2 text-gray-400 hover:text-primary-500 transition-colors"><i class="fa-solid fa-pen-to-square"></i></button><a href="?delete=<?= $a['id'] ?>" onclick="return confirm('Delete auction?')" class="p-2 text-gray-400 hover:text-red-500 transition-colors"><i class="fa-solid fa-trash"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-white/5 flex flex-wrap justify-center gap-2"><?php if ($pages > 1): for ($i = 1; $i <= $pages; $i++): ?><a href="<?= filterUrl('page', $i) ?>" class="px-3 py-1 rounded-lg text-sm font-bold <?= $i == $page ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-white/5 text-gray-500 hover:bg-gray-200 dark:hover:bg-white/10' ?>"><?= $i ?></a><?php endfor;
                                                                                                                                                                                                                                                                                                                                                                                                        endif; ?></div>
    </div>
</main>
<div id="auctionModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('auctionModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white dark:bg-dark-800 rounded-2xl shadow-2xl p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6" id="modalTitle">New Auction</h3>
        <form method="POST"><input type="hidden" name="id" id="auc_id">
            <div class="space-y-4">
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Product</label><select name="product_id" id="auc_prod" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"><?php foreach ($products as $p): ?><option value="<?= $p['id'] ?>"><?= $p['title'] ?></option><?php endforeach; ?></select></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Start Time</label><input type="datetime-local" name="start_time" id="auc_start" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                    <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">End Time</label><input type="datetime-local" name="end_time" id="auc_end" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Start Price</label><input type="number" step="0.01" name="start_price" id="auc_price" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                    <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Reserve Price</label><input type="number" step="0.01" name="reserve_price" id="auc_reserve" value="0" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                </div>
                <div id="status_field" class="hidden"><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label><select name="status" id="auc_status" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none">
                        <option value="active">Active</option>
                        <option value="ended">Ended</option>
                        <option value="cancelled">Cancelled</option>
                    </select></div>
                <button type="submit" name="save_auction" value="1" class="w-full py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl">Save</button>
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
        document.getElementById('auc_id').value = '';
        document.getElementById('modalTitle').innerText = 'New Auction';
        document.getElementById('status_field').classList.add('hidden');
    }

    function editAuction(a) {
        document.getElementById('auc_id').value = a.id;
        document.getElementById('auc_prod').value = a.product_id;
        document.getElementById('auc_start').value = a.start_time.replace(' ', 'T');
        document.getElementById('auc_end').value = a.end_time.replace(' ', 'T');
        document.getElementById('auc_price').value = a.start_price;
        document.getElementById('auc_reserve').value = a.reserve_price;
        document.getElementById('auc_status').value = a.status;
        document.getElementById('status_field').classList.remove('hidden');
        document.getElementById('modalTitle').innerText = 'Edit Auction';
        openModal('auctionModal');
    }
</script>
<?php require_once 'footer.php'; ?>