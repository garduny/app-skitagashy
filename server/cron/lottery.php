<?php
if (php_sapi_name() !== 'cli') {
    if (!defined('gashy_exec')) define('gashy_exec', true);
}
if (!isset($_SERVER['REQUEST_URI'])) $_SERVER['REQUEST_URI'] = '/cron/lottery';
if (!isset($_SERVER['HTTP_USER_AGENT'])) $_SERVER['HTTP_USER_AGENT'] = 'CronJob';
require_once __DIR__ . '/../init.php';
execute(" SET time_zone = '+03:00' ");
echo "[" . date('Y-m-d H:i:s') . "] Lottery Logic Started...\n";
while (true) {
    $round = findQuery(" SELECT * FROM lottery_rounds WHERE status='open' AND draw_time<=NOW() ORDER BY draw_time ASC,id ASC LIMIT 1 ");
    if (!$round) {
        echo "No rounds ready to draw.\n";
        break;
    }
    $rid = (int)$round['id'];
    $round_number = (int)$round['round_number'];
    $total_prize = (float)$round['prize_pool'];
    echo "Processing Round #{$round_number} (Pool: {$total_prize} G)...\n";
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
        $log_data = [];
        if (!empty($weightedPool) && !empty($uniqueUsers) && $total_prize > 0) {
            $tiers = [0.50,0.30,0.20];
            $maxWinners = min(count($tiers), count($uniqueUsers));
            $selectedTiers = array_slice($tiers, 0, $maxWinners);
            $tierSum = array_sum($selectedTiers);
            $usedWinners = [];
            foreach ($selectedTiers as $rank => $percent) {
                $availablePool = array_values(array_filter($weightedPool, function ($id) use ($usedWinners) {
                    return !isset($usedWinners[$id]);
                }));
                if (empty($availablePool)) break;
                $key = array_rand($availablePool);
                $winner_id = (int)$availablePool[$key];
                if ($winner_id <= 0) break;
                $usedWinners[$winner_id] = true;
                $amount = $tierSum > 0 ? round(($total_prize * $percent) / $tierSum, 8) : 0;
                if ($rank === count($selectedTiers) - 1) {
                    $distributed = 0;
                    foreach ($log_data as $row) $distributed += (float)$row['amount'];
                    $amount = round($total_prize - $distributed, 8);
                }
                $log_data[] = [
                    'rank' => $rank + 1,
                    'user' => $winner_id,
                    'amount' => $amount
                ];
                echo " -> Rank #" . ($rank + 1) . ": User $winner_id wins $amount G\n";
                execute(" UPDATE lottery_entries SET is_winner=1 WHERE round_id=$rid AND account_id=$winner_id ");
                execute(" INSERT INTO transactions (account_id,type,amount,reference_id,status,created_at) VALUES ($winner_id,'reward',$amount,$rid,'confirmed',NOW()) ");
                $acc = findQuery(" SELECT email,accountname FROM accounts WHERE id=$winner_id ");
                if (!empty($acc['email']) && function_exists('mailer')) {
                    $name = secure($acc['accountname'] ?? 'User');
                    $email = secure($acc['email']);
                    $body = "<h1>Lottery Win!</h1><p>Hi {$name},</p><p>Congratulations! You won <b>Rank #" . ($rank + 1) . "</b> in Round #{$round_number}.</p><p>Prize: <b>" . number_format($amount,2) . " GASHY</b></p>";
                    mailer("Lottery Prize: " . number_format($amount,2) . " G", $body, "Gashy Lottery", $email);
                }
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
            $next_draw = date('Y-m-d H:i:s', strtotime('+7 days'));
            execute(" INSERT INTO lottery_rounds (round_number,prize_pool,draw_time,status) VALUES ($next_num,0,'$next_draw','open') ");
            echo " -> New Round #$next_num Created.\n";
        } else {
            echo " -> Open round already exists. Skipped new round creation.\n";
        }
        execute(" COMMIT ");
    } catch (Throwable $e) {
        execute(" ROLLBACK ");
        echo "Error: " . $e->getMessage() . "\n";
        break;
    }
}
echo "Done.\n";