<?php
require_once 'init.php';
if (post('approve')) {
    $wid = (int)request('id', 'post');
    $tx = trim((string)request('tx_signature', 'post'));
    if ($tx === '') $tx = 'PAYOUT_' . uniqid();
    $exists = findQuery(" SELECT id,status FROM withdrawals WHERE id=$wid LIMIT 1 ");
    if ($exists && strtolower($exists['status']) === 'pending') {
        execute(" UPDATE withdrawals SET status='approved',tx_signature='$tx' WHERE id=$wid LIMIT 1 ");
        $w = findQuery(" SELECT w.*,a.email,a.accountname FROM withdrawals w JOIN accounts a ON w.account_id=a.id WHERE w.id=$wid LIMIT 1 ");
        if (!empty($w['email'])) {
            $body = "<h1>Payout Approved</h1><p>Hi {$w['accountname']},</p><p>Your withdrawal of {$w['amount']} GASHY has been sent.</p><p>TX: {$tx}</p>";
            mailer('Payout Approved', $body, 'Gashy Finance', $w['email']);
        }
    }
    redirect('payouts.php?msg=approved');
}
if (get('reject')) {
    $wid = (int)request('reject', 'get');
    $exists = findQuery(" SELECT id,status FROM withdrawals WHERE id=$wid LIMIT 1 ");
    if ($exists && strtolower($exists['status']) === 'pending') {
        execute(" UPDATE withdrawals SET status='rejected' WHERE id=$wid LIMIT 1 ");
    }
    redirect('payouts.php?msg=rejected');
}
$status = trim((string)request('status', 'get'));
$where = " WHERE 1=1 ";
if (in_array($status, ['pending', 'approved', 'rejected'])) $where .= " AND LOWER(w.status)='$status' ";
$payouts = getQuery(" SELECT w.*,a.accountname,a.wallet_address,s.store_name FROM withdrawals w JOIN accounts a ON w.account_id=a.id LEFT JOIN sellers s ON a.id=s.account_id $where ORDER BY w.id DESC ");
$counts = findQuery(" SELECT COUNT(*) allc,COALESCE(SUM(CASE WHEN LOWER(status)='pending' THEN 1 ELSE 0 END),0) pendingc,COALESCE(SUM(CASE WHEN LOWER(status)='approved' THEN 1 ELSE 0 END),0) approvedc,COALESCE(SUM(CASE WHEN LOWER(status)='rejected' THEN 1 ELSE 0 END),0) rejectedc,COALESCE(SUM(CASE WHEN LOWER(status)='pending' THEN amount ELSE 0 END),0) pendingamt FROM withdrawals ");
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-gray-900 dark:text-white">Payout Requests</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage withdrawal requests and seller payouts.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="payouts.php" class="px-4 py-2 rounded-xl text-xs font-black <?= $status === '' ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/20' : 'bg-white dark:bg-dark-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-white/10' ?>">All</a>
                <a href="?status=pending" class="px-4 py-2 rounded-xl text-xs font-black <?= $status === 'pending' ? 'bg-yellow-500 text-white shadow-lg shadow-yellow-500/20' : 'bg-white dark:bg-dark-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-white/10' ?>">Pending</a>
                <a href="?status=approved" class="px-4 py-2 rounded-xl text-xs font-black <?= $status === 'approved' ? 'bg-green-600 text-white shadow-lg shadow-green-600/20' : 'bg-white dark:bg-dark-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-white/10' ?>">Paid</a>
                <a href="?status=rejected" class="px-4 py-2 rounded-xl text-xs font-black <?= $status === 'rejected' ? 'bg-red-600 text-white shadow-lg shadow-red-600/20' : 'bg-white dark:bg-dark-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-white/10' ?>">Rejected</a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">
            <div class="rounded-2xl bg-white dark:bg-dark-800 border border-gray-200 dark:border-white/5 p-5">
                <div class="text-xs uppercase font-black text-gray-500">Total Requests</div>
                <div class="mt-2 text-3xl font-black text-gray-900 dark:text-white"><?= number_format($counts['allc']) ?></div>
            </div>
            <div class="rounded-2xl bg-white dark:bg-dark-800 border border-gray-200 dark:border-white/5 p-5">
                <div class="text-xs uppercase font-black text-gray-500">Pending</div>
                <div class="mt-2 text-3xl font-black text-yellow-500"><?= number_format($counts['pendingc']) ?></div>
            </div>
            <div class="rounded-2xl bg-white dark:bg-dark-800 border border-gray-200 dark:border-white/5 p-5">
                <div class="text-xs uppercase font-black text-gray-500">Approved</div>
                <div class="mt-2 text-3xl font-black text-green-500"><?= number_format($counts['approvedc']) ?></div>
            </div>
            <div class="rounded-2xl bg-white dark:bg-dark-800 border border-gray-200 dark:border-white/5 p-5">
                <div class="text-xs uppercase font-black text-gray-500">Rejected</div>
                <div class="mt-2 text-3xl font-black text-red-500"><?= number_format($counts['rejectedc']) ?></div>
            </div>
            <div class="rounded-2xl bg-white dark:bg-dark-800 border border-gray-200 dark:border-white/5 p-5">
                <div class="text-xs uppercase font-black text-gray-500">Pending Amount</div>
                <div class="mt-2 text-3xl font-black text-primary-500"><?= number_format($counts['pendingamt'], 2) ?></div>
            </div>
        </div>

        <div class="rounded-2xl bg-white dark:bg-dark-800 border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden">
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-white/5 bg-gray-50 dark:bg-white/5 text-xs uppercase text-gray-500">
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
                        <?php if ($payouts): foreach ($payouts as $p): ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                    <td class="px-6 py-4 font-black text-gray-900 dark:text-white">#<?= $p['id'] ?></td>
                                    <td class="px-6 py-4">
                                        <div class="font-black text-gray-900 dark:text-white"><?= $p['store_name'] ?: 'No Store' ?></div>
                                        <div class="text-xs text-gray-500"><?= $p['accountname'] ?></div>
                                    </td>
                                    <td class="px-6 py-4 font-black text-primary-500"><?= number_format($p['amount'], 3) ?></td>
                                    <td class="px-6 py-4 text-xs font-mono text-gray-500"><?= substr($p['wallet_address'], 0, 6) ?>...<?= substr($p['wallet_address'], -4) ?></td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-[10px] uppercase font-black <?= $p['status'] == 'approved' ? 'bg-green-500/10 text-green-500' : ($p['status'] == 'pending' ? 'bg-yellow-500/10 text-yellow-500' : 'bg-red-500/10 text-red-500') ?>"><?= $p['status'] ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500"><?= date('M d, Y H:i', strtotime($p['created_at'])) ?></td>
                                    <td class="px-6 py-4 text-right">
                                        <?php if ($p['status'] == 'pending'): ?>
                                            <button onclick="approvePayout('<?= $p['id'] ?>','<?= $p['amount'] ?>')" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-green-500 hover:bg-green-500/10"><i class="fa-solid fa-check"></i></button>
                                            <button onclick="rejectPayout('<?= $p['id'] ?>')" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-red-500 hover:bg-red-500/10"><i class="fa-solid fa-times"></i></button>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400">Done</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">No payout requests found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="lg:hidden p-4 space-y-4">
                <?php if ($payouts): foreach ($payouts as $p): ?>
                        <div class="rounded-2xl border border-gray-200 dark:border-white/5 p-4 bg-gray-50 dark:bg-white/5">
                            <div class="flex items-center justify-between gap-3">
                                <div class="font-black text-gray-900 dark:text-white">#<?= $p['id'] ?></div>
                                <span class="px-3 py-1 rounded-full text-[10px] uppercase font-black <?= $p['status'] == 'approved' ? 'bg-green-500/10 text-green-500' : ($p['status'] == 'pending' ? 'bg-yellow-500/10 text-yellow-500' : 'bg-red-500/10 text-red-500') ?>"><?= $p['status'] ?></span>
                            </div>
                            <div class="mt-3 text-sm font-black text-gray-900 dark:text-white"><?= $p['store_name'] ?: 'No Store' ?></div>
                            <div class="text-xs text-gray-500"><?= $p['accountname'] ?></div>
                            <div class="mt-3 text-primary-500 font-black"><?= number_format($p['amount'], 3) ?> GASHY</div>
                            <div class="text-xs text-gray-500 mt-1"><?= date('M d, Y H:i', strtotime($p['created_at'])) ?></div>
                            <?php if ($p['status'] == 'pending'): ?>
                                <div class="grid grid-cols-2 gap-2 mt-4">
                                    <button onclick="approvePayout('<?= $p['id'] ?>','<?= $p['amount'] ?>')" class="py-2 rounded-xl bg-green-600 text-white font-black text-sm">Approve</button>
                                    <button onclick="rejectPayout('<?= $p['id'] ?>')" class="py-2 rounded-xl bg-red-600 text-white font-black text-sm">Reject</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach;
                else: ?>
                    <div class="py-12 text-center text-gray-500">No payout requests found.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<div id="payoutModal" class="fixed inset-0 z-[70] hidden">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeModal('payoutModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md px-4">
        <div class="rounded-3xl bg-white dark:bg-dark-800 border border-gray-200 dark:border-white/10 shadow-2xl p-6">
            <div class="text-2xl font-black text-gray-900 dark:text-white">Confirm Payout</div>
            <p class="text-sm text-gray-500 mt-2">Send <span id="pay_amount" class="font-black text-primary-500"></span> GASHY to seller wallet.</p>
            <form method="POST" class="mt-6 space-y-4">
                <input type="hidden" name="id" id="pay_id">
                <div>
                    <label class="block text-xs uppercase font-black text-gray-500 mb-2">TX Signature</label>
                    <input type="text" name="tx_signature" placeholder="Paste Solana TX Hash" class="w-full rounded-2xl px-4 py-3 bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white outline-none">
                </div>
                <button type="submit" name="approve" value="1" class="w-full py-3 rounded-2xl bg-green-600 hover:bg-green-500 text-white font-black">Confirm Transfer</button>
            </form>
        </div>
    </div>
</div>

<div id="rejectModal" class="fixed inset-0 z-[70] hidden">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeModal('rejectModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-sm px-4">
        <div class="rounded-3xl bg-white dark:bg-dark-800 border border-gray-200 dark:border-white/10 shadow-2xl p-6">
            <div class="text-xl font-black text-gray-900 dark:text-white">Reject Request?</div>
            <p class="text-sm text-gray-500 mt-2">This payout request will be marked rejected.</p>
            <div class="grid grid-cols-2 gap-3 mt-6">
                <button onclick="closeModal('rejectModal')" class="py-3 rounded-2xl bg-gray-100 dark:bg-dark-900 text-gray-700 dark:text-white font-black">Cancel</button>
                <a href="#" id="rejectLink" class="py-3 rounded-2xl bg-red-600 text-white text-center font-black">Reject</a>
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

    function approvePayout(id, amt) {
        document.getElementById('pay_id').value = id
        document.getElementById('pay_amount').innerText = amt
        openModal('payoutModal')
    }

    function rejectPayout(id) {
        document.getElementById('rejectLink').href = '?reject=' + id
        openModal('rejectModal')
    }
</script>
<?php require_once 'footer.php'; ?>