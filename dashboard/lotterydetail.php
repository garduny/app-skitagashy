<?php
require_once 'init.php';
$id = (int)request('id', 'get');
$r = findQuery(" SELECT * FROM lottery_rounds WHERE id=$id ");
if (!$r) redirect('lotteries.php');
if (post('draw_winner') && $r['status'] === 'open' && strtotime($r['draw_time']) <= time()) {
    $r = findQuery(" SELECT * FROM lottery_rounds WHERE id=$id ");
    if ($r['status'] !== 'open') redirect("lotterydetail.php?id=$id");
    $rid = (int)$r['id'];
    $total_prize = (float)$r['prize_pool'];
    execute(" START TRANSACTION ");
    try {
        $entries = getQuery(" SELECT account_id,ticket_count FROM lottery_entries WHERE round_id=$rid AND ticket_count>0 ");
        $weightedPool = [];
        $uniqueUsers = [];
        foreach ($entries as $e) {
            $account_id = (int)$e['account_id'];
            $ticket_count = (int)$e['ticket_count'];
            if ($account_id <= 0 || $ticket_count <= 0) continue;
            $uniqueUsers[$account_id] = true;
            for ($i = 0; $i < $ticket_count; $i++) {
                $weightedPool[] = $account_id;
            }
        }
        execute(" UPDATE lottery_entries SET is_winner=0 WHERE round_id=$rid ");
        $winners = [];
        if (!empty($weightedPool) && !empty($uniqueUsers) && $total_prize > 0) {
            $tiers = [0.50, 0.30, 0.20];
            $maxWinners = min(count($tiers), count($uniqueUsers));
            $selectedTiers = array_slice($tiers, 0, $maxWinners);
            $tierSum = array_sum($selectedTiers);
            $used = [];
            foreach ($selectedTiers as $rank => $percent) {
                $availablePool = array_values(array_filter($weightedPool, function ($uid) use ($used) {
                    return !isset($used[$uid]);
                }));
                if (empty($availablePool)) break;
                $winner_id = (int)$availablePool[array_rand($availablePool)];
                if ($winner_id <= 0) break;
                $used[$winner_id] = true;
                $amount = $tierSum > 0 ? round(($total_prize * $percent) / $tierSum, 8) : 0;
                if ($rank === count($selectedTiers) - 1) {
                    $distributed = 0;
                    foreach ($winners as $wx) $distributed += (float)$wx['amount'];
                    $amount = round($total_prize - $distributed, 8);
                }
                $winners[] = [
                    'rank' => $rank + 1,
                    'user' => $winner_id,
                    'amount' => $amount
                ];
                execute(" UPDATE lottery_entries SET is_winner=1 WHERE round_id=$rid AND account_id=$winner_id ");
                execute(" INSERT INTO transactions (account_id,type,amount,reference_id,status,created_at) VALUES ($winner_id,'reward',$amount,$rid,'confirmed',NOW()) ");
            }
            $win_json = json_encode($winners, JSON_UNESCAPED_UNICODE);
            execute(" UPDATE lottery_rounds SET status='closed',winning_numbers='$win_json' WHERE id=$rid ");
        } else {
            execute(" UPDATE lottery_rounds SET status='closed',winning_numbers=NULL WHERE id=$rid ");
        }
        $openExists = findQuery(" SELECT id FROM lottery_rounds WHERE status='open' ORDER BY id DESC LIMIT 1 ");
        if (!$openExists) {
            $lastNum = findQuery(" SELECT MAX(round_number) max_round FROM lottery_rounds ");
            $next_num = ((int)($lastNum['max_round'] ?? 0)) + 1;
            $next_draw = date('Y-m-d H:i:s', strtotime('+7 days'));
            execute(" INSERT INTO lottery_rounds (round_number,prize_pool,draw_time,status) VALUES ($next_num,0,'$next_draw','open') ");
        }
        execute(" COMMIT ");
        redirect("lotterydetail.php?id=$id&msg=drawn");
    } catch (Throwable $e) {
        execute(" ROLLBACK ");
        redirect("lotterydetail.php?id=$id&msg=error");
    }
}
$entries = getQuery(" SELECT le.*,acc.accountname FROM lottery_entries le JOIN accounts acc ON le.account_id=acc.id WHERE le.round_id=$id ORDER BY le.ticket_count DESC,le.id DESC ");
$total_tickets = 0;
foreach ($entries as $e) $total_tickets += (int)$e['ticket_count'];
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex items-center gap-4 mb-6">
        <a href="lotteries.php" class="p-2 rounded-lg bg-white dark:bg-white/5 text-gray-500 hover:text-white transition-colors"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Round #<?= $r['round_number'] ?></h1>
    </div>
    <?php if (request('msg', 'get') === 'drawn'): ?>
        <div class="mb-6 p-4 rounded-xl bg-green-500/10 text-green-500 font-bold">Round drawn successfully.</div>
    <?php endif; ?>
    <?php if (request('msg', 'get') === 'error'): ?>
        <div class="mb-6 p-4 rounded-xl bg-red-500/10 text-red-500 font-bold">Unexpected error while drawing round.</div>
    <?php endif; ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm text-center">
                <div class="text-xs text-gray-500 uppercase font-bold mb-2">Prize Pool</div>
                <div class="text-4xl font-black text-green-500 mb-6"><?= number_format($r['prize_pool'], 2) ?> GASHY</div>
                <div class="grid grid-cols-2 gap-4 border-t border-gray-100 dark:border-white/5 pt-4">
                    <div>
                        <div class="text-xs text-gray-500 uppercase">Tickets</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white"><?= $total_tickets ?></div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase">Participants</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white"><?= count($entries) ?></div>
                    </div>
                </div>
            </div>
            <?php if ($r['status'] === 'open' && strtotime($r['draw_time']) <= time()): ?>
                <form method="POST">
                    <button type="submit" name="draw_winner" value="1" onclick="return confirm('Close round?')" class="w-full py-4 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-2xl shadow-lg shadow-primary-500/20 text-lg">🎲 Draw Winner</button>
                </form>
            <?php elseif ($r['status'] === 'open'): ?>
                <div class="p-4 bg-yellow-500/10 text-yellow-500 text-center rounded-xl font-bold">Waiting for draw time</div>
                <?php else:
                $winners = json_decode($r['winning_numbers'], true) ?? [];
                if (isset($winners['account_id'])) $winners = [['rank' => 1, 'user' => $winners['account_id'], 'amount' => $winners['amount']]];
                if (!empty($winners)): ?>
                    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm">
                        <h3 class="font-bold text-gray-900 dark:text-white mb-4">Winners Podium</h3>
                        <div class="space-y-3">
                            <?php foreach ($winners as $w):
                                $uid = (int)($w['user'] ?? 0);
                                $u = $uid ? findQuery(" SELECT accountname FROM accounts WHERE id=$uid ") : [];
                                $rankColor = $w['rank'] == 1 ? 'text-yellow-500' : ($w['rank'] == 2 ? 'text-gray-400' : 'text-orange-500');
                            ?>
                                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/5">
                                    <div class="flex items-center gap-3">
                                        <div class="font-black text-lg <?= $rankColor ?>">#<?= $w['rank'] ?></div>
                                        <div class="text-sm font-bold text-gray-900 dark:text-white"><?= $u['accountname'] ?? 'Unknown' ?></div>
                                    </div>
                                    <div class="font-mono font-bold text-green-500">+<?= number_format($w['amount'], 2) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="p-4 bg-red-500/10 text-red-500 text-center rounded-xl font-bold">No Winners</div>
            <?php endif;
            endif; ?>
        </div>
        <div class="lg:col-span-2 bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 p-6 shadow-sm overflow-hidden">
            <h3 class="font-bold text-gray-900 dark:text-white mb-4">Entries</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-gray-500 border-b border-gray-200 dark:border-white/5">
                            <th class="pb-2">Account</th>
                            <th class="pb-2">Tickets</th>
                            <th class="pb-2">Win Chance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        <?php foreach ($entries as $e):
                            $chance = $total_tickets > 0 ? round(($e['ticket_count'] / $total_tickets) * 100, 2) : 0;
                        ?>
                            <tr class="<?= ($e['is_winner'] === 'yes' || $e['is_winner'] == 1) ? 'bg-green-500/10' : '' ?>">
                                <td class="py-3 font-bold text-gray-900 dark:text-white"><?= $e['accountname'] ?></td>
                                <td class="py-3"><?= $e['ticket_count'] ?></td>
                                <td class="py-3 text-primary-500 font-bold"><?= $chance ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>