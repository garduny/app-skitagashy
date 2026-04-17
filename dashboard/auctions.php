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
    $price = (float)request('start_price_usd', 'post');
    $reserve = (float)request('reserve_price_usd', 'post');
    $state = request('status', 'post') ?: 'active';
    if ($id) {
        execute(" UPDATE auctions SET product_id=$pid,start_time='$start',end_time='$end',reserve_price_usd=$reserve,status='$state' WHERE id=$id ");
        redirect('auctions.php?msg=updated');
    } else {
        execute(" INSERT INTO auctions (product_id,option_id,start_time,end_time,start_price_usd,reserve_price_usd,current_bid_usd,highest_bidder_id,status) VALUES ($pid,NULL,'$start','$end',$price,$reserve,$price,NULL,'active') ");
        redirect('auctions.php?msg=created');
    }
}
$search = trim((string)request('search', 'get'));
$status = trim((string)request('status', 'get'));
$page = max(1, (int)(request('page', 'get') ?: 1));
$limit = 10;
$offset = ($page - 1) * $limit;
$where = " WHERE 1=1 ";
if ($search) $where .= " AND p.title LIKE '%$search%' ";
if ($status) $where .= " AND a.status='$status' ";
$auctions = getQuery(" SELECT a.*,p.title,p.images,acc.accountname bidder_name FROM auctions a JOIN products p ON p.id=a.product_id LEFT JOIN accounts acc ON acc.id=a.highest_bidder_id $where ORDER BY FIELD(a.status,'active','ended','cancelled'),a.end_time ASC LIMIT $limit OFFSET $offset ");
$total = countQuery(" SELECT 1 FROM auctions a JOIN products p ON p.id=a.product_id $where ");
$pages = max(1, ceil($total / $limit));
function filterUrl($key, $val)
{
    global $search, $status;
    $p = ['search' => $search, 'status' => $status, 'page' => 1];
    $p[$key] = $val;
    return '?' . http_build_query(array_filter($p, function ($v) {
        return $v !== '' && $v !== null;
    }));
}
$gashyRate = toGashy();
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white">Live Auctions</h1>
            <p class="text-sm text-gray-500">Manage <?= number_format($total) ?> bidding events.</p>
        </div>
        <button onclick="resetForm();openModal('auctionModal')" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl shadow-lg shadow-primary-500/20 flex items-center gap-2"><i class="fa-solid fa-plus"></i>Create Auction</button>
    </div>
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-white/5">
            <form class="grid md:grid-cols-[180px_1fr_50px] gap-3">
                <select name="status" class="bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-gray-900 dark:text-white outline-none">
                    <option value="">All Status</option>
                    <option value="active" <?= $status == 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="ended" <?= $status == 'ended' ? 'selected' : '' ?>>Ended</option>
                    <option value="cancelled" <?= $status == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search item..." class="bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-gray-900 dark:text-white outline-none">
                <button class="bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 rounded-xl text-gray-600 dark:text-gray-300"><i class="fa-solid fa-search"></i></button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
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
                    <?php foreach ($auctions as $a):
                        $imgs = json_decode($a['images'], true);
                        $img = $imgs[0] ?? '';
                        $diff = strtotime($a['end_time']) - time();
                        $h = floor(max(0, $diff) / 3600);
                        $m = floor((max(0, $diff) % 3600) / 60);
                        $usd = (float)$a['current_bid_usd'];
                        $gashy = $gashyRate ? ($usd / $gashyRate) : 0;
                        $resusd = (float)$a['reserve_price_usd'];
                        $resgashy = $gashyRate ? ($resusd / $gashyRate) : 0;
                    ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3 min-w-[250px]">
                                    <img src="../<?= $img ?>" class="w-10 h-10 rounded-lg object-cover bg-gray-100 dark:bg-white/5">
                                    <div class="font-bold text-gray-900 dark:text-white"><?= $a['title'] ?></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-primary-500">$<?= number_format($usd, 2) ?><div class="text-xs"><?= number_format($gashy, 2) ?> GASHY</div>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500">$<?= number_format($resusd, 2) ?><div><?= number_format($resgashy, 2) ?> GASHY</div>
                            </td>
                            <td class="px-6 py-4"><?= $a['bidder_name'] ?: '<span class="text-gray-400 italic">No Bids</span>' ?></td>
                            <td class="px-6 py-4"><span class="font-mono <?= $diff > 0 && $diff < 3600 ? 'text-red-500 font-bold' : '' ?>"><?= $diff > 0 ? $h . 'h ' . $m . 'm' : 'Ended' ?></span></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-[10px] uppercase font-bold <?= $a['status'] == 'active' ? 'bg-green-500/10 text-green-500' : ($a['status'] == 'ended' ? 'bg-blue-500/10 text-blue-500' : 'bg-red-500/10 text-red-500') ?>"><?= $a['status'] ?></span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="auctiondetail.php?id=<?= $a['id'] ?>" class="p-2 text-gray-400 hover:text-blue-500"><i class="fa-solid fa-eye"></i></a>
                                <button onclick='editAuction(<?= json_encode($a, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="p-2 text-gray-400 hover:text-primary-500"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button onclick="confirmDelete(<?= $a['id'] ?>)" class="p-2 text-gray-400 hover:text-red-500"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$auctions): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400">No auctions found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($pages > 1): ?>
            <div class="p-4 border-t border-gray-200 dark:border-white/5 flex flex-wrap justify-center gap-2">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <a href="<?= filterUrl('page', $i) ?>" class="px-3 py-1 rounded-lg text-sm font-bold <?= $i == $page ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-white/5 text-gray-500' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<div id="auctionModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('auctionModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-xl p-4">
        <div class="bg-white dark:bg-dark-800 rounded-2xl shadow-2xl p-6">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-900 dark:text-white mb-6">New Auction</h3>
            <form method="POST">
                <input type="hidden" name="id" id="auc_id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Product</label>
                        <select name="product_id" id="auc_prod" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none">
                            <?php foreach ($products as $p): ?><option value="<?= $p['id'] ?>"><?= $p['title'] ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Start Time</label><input type="datetime-local" name="start_time" id="auc_start" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white"></div>
                        <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">End Time</label><input type="datetime-local" name="end_time" id="auc_end" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white"></div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Start Price USD</label><input type="number" step="0.01" min="0" name="start_price_usd" id="auc_price" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white"></div>
                        <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Reserve Price USD</label><input type="number" step="0.01" min="0" name="reserve_price_usd" id="auc_reserve" value="0" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white"></div>
                    </div>
                    <div id="statusWrap" class="hidden">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label>
                        <select name="status" id="auc_status" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white">
                            <option value="active">Active</option>
                            <option value="ended">Ended</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" name="save_auction" value="1" class="w-full py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl">Save Auction</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="deleteModal" class="fixed inset-0 z-[70] hidden">
    <div class="absolute inset-0 bg-black/60" onclick="closeModal('deleteModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-sm p-4">
        <div class="bg-white dark:bg-dark-800 rounded-2xl p-6">
            <div class="text-lg font-bold text-gray-900 dark:text-white mb-2">Delete Auction?</div>
            <div class="text-sm text-gray-500 mb-6">This action cannot be undone.</div>
            <div class="grid grid-cols-2 gap-3">
                <button onclick="closeModal('deleteModal')" class="py-2 rounded-xl bg-gray-100 dark:bg-white/5">Cancel</button>
                <a id="deleteLink" href="#" class="py-2 rounded-xl bg-red-500 text-white text-center">Delete</a>
            </div>
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
        auc_id.value = ''
        modalTitle.innerText = 'New Auction'
        statusWrap.classList.add('hidden')
        auc_price.disabled = false
        auc_prod.disabled = false
    }

    function editAuction(a) {
        openModal('auctionModal')
        auc_id.value = a.id
        auc_prod.value = a.product_id
        auc_start.value = a.start_time.replace(' ', 'T')
        auc_end.value = a.end_time.replace(' ', 'T')
        auc_price.value = a.start_price_usd
        auc_reserve.value = a.reserve_price_usd
        auc_status.value = a.status
        modalTitle.innerText = 'Edit Auction'
        statusWrap.classList.remove('hidden')
        auc_price.disabled = true
        auc_prod.disabled = false
    }

    function confirmDelete(id) {
        deleteLink.href = '?delete=' + id
        openModal('deleteModal')
    }
</script>
<?php require_once 'footer.php'; ?>