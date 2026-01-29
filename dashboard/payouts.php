<?php
require_once 'init.php';
if (post('approve')) {
    $wid = (int)request('id', 'post');
    $tx = request('tx_signature', 'post') ?: 'PAYOUT_' . uniqid();
    execute(" UPDATE withdrawals SET status='approved', tx_signature='$tx' WHERE id=$wid ");
    $w = findQuery(" SELECT w.*,a.email,a.accountname FROM withdrawals w JOIN accounts a ON w.account_id=a.id WHERE w.id=$wid ");
    if ($w['email']) {
        $body = "<h1>Payout Approved</h1><p>Hi {$w['accountname']},</p><p>Your withdrawal of {$w['amount']} GASHY has been sent.</p><p>TX: $tx</p>";
        mailer('Payout Approved', $body, 'Gashy Finance', $w['email']);
    }
    redirect('payouts.php?msg=approved');
}
if (get('reject')) {
    $wid = (int)request('reject', 'get');
    execute(" UPDATE withdrawals SET status='rejected' WHERE id=$wid ");
    redirect('payouts.php?msg=rejected');
}
$status = request('status', 'get');
$where = "WHERE 1=1";
if ($status) {
    $where .= " AND w.status='$status' ";
}
$payouts = getQuery(" SELECT w.*,a.accountname,a.wallet_address,s.store_name FROM withdrawals w JOIN accounts a ON w.account_id=a.id LEFT JOIN sellers s ON a.id=s.account_id $where ORDER BY w.id DESC ");
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Payout Requests</h1>
            <p class="text-sm text-gray-500">Manage seller withdrawals.</p>
        </div>
        <div class="flex bg-white dark:bg-dark-800 p-1 rounded-xl border border-gray-200 dark:border-white/10">
            <a href="payouts.php" class="px-3 py-1.5 rounded-lg text-xs font-bold <?= !$status ? 'bg-primary-500 text-white' : 'text-gray-500' ?>">All</a>
            <a href="?status=pending" class="px-3 py-1.5 rounded-lg text-xs font-bold <?= $status == 'pending' ? 'bg-primary-500 text-white' : 'text-gray-500' ?>">Pending</a>
            <a href="?status=approved" class="px-3 py-1.5 rounded-lg text-xs font-bold <?= $status == 'approved' ? 'bg-primary-500 text-white' : 'text-gray-500' ?>">Paid</a>
        </div>
    </div>
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/5 text-xs uppercase text-gray-500 font-bold">
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Seller</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Wallet</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php foreach ($payouts as $p): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">#<?= $p['id'] ?></td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-white text-sm"><?= $p['store_name'] ?></div>
                                <div class="text-xs text-gray-500"><?= $p['accountname'] ?></div>
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-primary-500"><?= number_format($p['amount'], 2) ?></td>
                            <td class="px-6 py-4 text-xs font-mono text-gray-500"><?= substr($p['wallet_address'], 0, 6) ?>...</td>
                            <td class="px-6 py-4"><span class="px-2 py-1 rounded text-[10px] uppercase font-bold <?= $p['status'] == 'approved' ? 'bg-green-500/10 text-green-500' : ($p['status'] == 'pending' ? 'bg-yellow-500/10 text-yellow-500' : 'bg-red-500/10 text-red-500') ?>"><?= $p['status'] ?></span></td>
                            <td class="px-6 py-4 text-sm text-gray-500"><?= date('M d, H:i', strtotime($p['created_at'])) ?></td>
                            <td class="px-6 py-4 text-right">
                                <?php if ($p['status'] == 'pending'): ?>
                                    <button onclick='approvePayout(<?= $p['id'] ?>,"<?= $p['amount'] ?>")' class="p-2 text-green-500 hover:bg-green-500/10 rounded"><i class="fa-solid fa-check"></i></button>
                                    <a href="?reject=<?= $p['id'] ?>" onclick="return confirm('Reject request?')" class="p-2 text-red-500 hover:bg-red-500/10 rounded"><i class="fa-solid fa-times"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<div id="payoutModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('payoutModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white dark:bg-dark-800 rounded-2xl shadow-2xl p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Confirm Payout</h3>
        <form method="POST"><input type="hidden" name="id" id="pay_id">
            <div class="space-y-4">
                <p class="text-sm text-gray-500">Send <strong class="text-white" id="pay_amount"></strong> GASHY to seller?</p>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">TX Signature (Optional)</label><input type="text" name="tx_signature" placeholder="Paste Solana TX Hash" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div><button type="submit" name="approve" value="1" class="w-full py-3 bg-green-600 hover:bg-green-500 text-white font-bold rounded-xl">Confirm Transfer</button>
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

    function approvePayout(id, amt) {
        document.getElementById('pay_id').value = id;
        document.getElementById('pay_amount').innerText = amt;
        openModal('payoutModal');
    }
</script>
<?php require_once 'footer.php'; ?>