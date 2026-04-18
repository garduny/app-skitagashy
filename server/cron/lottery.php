<?php
require_once __DIR__.'/init.php';
echo "[".date('Y-m-d H:i:s')."] Lottery Started\n";
if (php_sapi_name() !== 'cli') {
    if (!defined('gashy_exec')) define('gashy_exec', true);
}
if (!isset($_SERVER['REQUEST_URI'])) $_SERVER['REQUEST_URI'] = '/cron/lottery';
if (!isset($_SERVER['HTTP_USER_AGENT'])) $_SERVER['HTTP_USER_AGENT'] = 'CronJob';
require_once __DIR__ . '/../init.php';
execute(" SET time_zone = '+03:00' ");
echo "[" . date('Y-m-d H:i:s') . "] Lottery Logic Started...\n";
$maxLoops = 20;
$loop = 0;
while ($loop < $maxLoops) {
    $loop++;
    $candidate = findQuery(" SELECT id FROM lottery_rounds WHERE status='open' AND draw_time<=NOW() ORDER BY draw_time ASC,id ASC LIMIT 1 ");
    if (!$candidate) {
        echo "No rounds ready to draw.\n";
        break;
    }
    $rid = (int)$candidate['id'];
    execute(" START TRANSACTION ");
    try {
        $round = findQuery(" SELECT * FROM lottery_rounds WHERE id=$rid AND status='open' AND draw_time<=NOW() FOR UPDATE ");
        if (!$round) {
            execute(" ROLLBACK ");
            echo " -> Skipped locked/already processed round.\n";
            continue;
        }
        $round_number = (int)$round['round_number'];
        $total_prize = (float)$round['prize_pool'];
        echo "Processing Round #{$round_number} (Pool: {$total_prize} G)...\n";
        $entries = getQuery(" SELECT account_id,ticket_count FROM lottery_entries WHERE round_id=$rid AND ticket_count>0 ");
        execute(" UPDATE lottery_entries SET is_winner=0 WHERE round_id=$rid ");
        $weights = [];
        $totalTickets = 0;
        foreach ($entries as $e) {
            $account_id = (int)$e['account_id'];
            $ticket_count = (int)$e['ticket_count'];
            if ($account_id <= 0 || $ticket_count <= 0) continue;
            if (!isset($weights[$account_id])) $weights[$account_id] = 0;
            $weights[$account_id] += $ticket_count;
            $totalTickets += $ticket_count;
        }
        $log_data = [];
        $mailQueue = [];
        if (!empty($weights) && $totalTickets > 0 && $total_prize > 0) {
            $tiers = [0.50, 0.30, 0.20];
            $selectedTiers = array_slice($tiers, 0, min(count($tiers), count($weights)));
            $tierSum = array_sum($selectedTiers);
            $usedWinners = [];
            $distributed = 0;
            foreach ($selectedTiers as $rank => $percent) {
                $availableWeights = array_diff_key($weights, $usedWinners);
                if (empty($availableWeights)) break;
                $poolSum = array_sum($availableWeights);
                if ($poolSum <= 0) break;
                $rand = mt_rand(1, $poolSum);
                $cursor = 0;
                $winner_id = 0;
                foreach ($availableWeights as $account_id => $ticket_count) {
                    $cursor += (int)$ticket_count;
                    if ($rand <= $cursor) {
                        $winner_id = (int)$account_id;
                        break;
                    }
                }
                if ($winner_id <= 0) break;
                $usedWinners[$winner_id] = true;
                $amount = $tierSum > 0 ? round(($total_prize * $percent) / $tierSum, 8) : 0;
                if ($rank === count($selectedTiers) - 1) $amount = round($total_prize - $distributed, 8);
                if ($amount < 0) $amount = 0;
                $distributed += $amount;
                $alreadyRewarded = findQuery(" SELECT id FROM transactions WHERE account_id=$winner_id AND type='reward' AND reference_id=$rid AND status='confirmed' LIMIT 1 ");
                if (!$alreadyRewarded && $amount > 0) {
                    execute(" INSERT INTO transactions (account_id,type,amount,reference_id,status,created_at) VALUES ($winner_id,'reward',$amount,$rid,'confirmed',NOW()) ");
                }
                execute(" UPDATE lottery_entries SET is_winner=1 WHERE round_id=$rid AND account_id=$winner_id ");
                $log_data[] = ['rank' => $rank + 1, 'user' => $winner_id, 'amount' => $amount];
                $acc = findQuery(" SELECT email,accountname FROM accounts WHERE id=$winner_id ");
                if (!empty($acc['email'])) {
                    $mailQueue[] = [
                        'email' => $acc['email'],
                        'name' => $acc['accountname'] ?: 'User',
                        'rank' => $rank + 1,
                        'amount' => $amount
                    ];
                }
                echo " -> Rank #" . ($rank + 1) . ": User $winner_id wins $amount G\n";
            }
            $win_json = json_encode($log_data, JSON_UNESCAPED_UNICODE);
            execute(" UPDATE lottery_rounds SET status='closed',winning_numbers='$win_json' WHERE id=$rid ");
        } else {
            echo " -> No valid entries or no prize. Closing without winners.\n";
            execute(" UPDATE lottery_rounds SET status='closed',winning_numbers=NULL WHERE id=$rid ");
        }
        $openExists = findQuery(" SELECT id FROM lottery_rounds WHERE status='open' ORDER BY id DESC LIMIT 1 ");
        if (!$openExists) {
            $lastNum = findQuery(" SELECT MAX(round_number) as max_round FROM lottery_rounds ");
            $next_num = ((int)($lastNum['max_round'] ?? 0)) + 1;
            $baseDraw = !empty($round['draw_time']) ? strtotime($round['draw_time']) : time();
            $next_draw = date('Y-m-d H:i:s', strtotime('+7 days', $baseDraw));
            execute(" INSERT INTO lottery_rounds (round_number,prize_pool,draw_time,status) VALUES ($next_num,0,'$next_draw','open') ");
            echo " -> New Round #$next_num Created.\n";
        } else {
            echo " -> Open round already exists. Skipped new round creation.\n";
        }
        execute(" COMMIT ");
        if (!empty($mailQueue) && function_exists('mailer')) {
            foreach ($mailQueue as $m) {
                $name = secure($m['name']);
                $email = secure($m['email']);
                $amountText = number_format((float)$m['amount'], 2);
                $body = "<h1>Lottery Win!</h1><p>Hi {$name},</p><p>Congratulations! You won <b>Rank #{$m['rank']}</b> in Round #{$round_number}.</p><p>Prize: <b>{$amountText} GASHY</b></p>";
                mailer("Lottery Prize: {$amountText} G", $body, "Gashy Lottery", $email);
            }
        }
    } catch (Throwable $e) {
        execute(" ROLLBACK ");
        echo "Error: " . $e->getMessage() . "\n";
        break;
    }
}
if ($loop >= $maxLoops) echo "Stopped after max loop protection.\n";
echo "Done.\n";
