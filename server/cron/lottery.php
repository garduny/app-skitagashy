<?php
if (php_sapi_name() !== 'cli') {
    if (!defined('gashy_exec')) define('gashy_exec', true);
}
if (!isset($_SERVER['REQUEST_URI'])) $_SERVER['REQUEST_URI'] = '/cron/lottery';
if (!isset($_SERVER['HTTP_USER_AGENT'])) $_SERVER['HTTP_USER_AGENT'] = 'CronJob';
require_once __DIR__ . '/../init.php';
echo "[" . date('Y-m-d H:i:s') . "] Lottery Logic Started...\n";
$round = findQuery(" SELECT * FROM lottery_rounds WHERE status='open' AND draw_time<=NOW() ");
if ($round) {
    echo "Processing Round #{$round['round_number']} (Pool: {$round['prize_pool']} G)...\n";
    $rid = $round['id'];
    $total_prize = $round['prize_pool'];
    $entries = getQuery(" SELECT account_id,ticket_count FROM lottery_entries WHERE round_id=$rid ");
    $pool = [];
    foreach ($entries as $e) {
        for ($i = 0; $i < $e['ticket_count']; $i++) {
            $pool[] = $e['account_id'];
        }
    }
    if (!empty($pool)) {
        $tiers = [0.50, 0.30, 0.20];
        $winners = [];
        $log_data = [];
        foreach ($tiers as $rank => $percent) {
            if (empty($pool)) break;
            $key = array_rand($pool);
            $winner_id = $pool[$key];
            $amount = $total_prize * $percent;
            $winners[$rank + 1] = ['id' => $winner_id, 'amount' => $amount];
            $log_data[] = ['rank' => $rank + 1, 'user' => $winner_id, 'amount' => $amount];
            echo " -> Rank #" . ($rank + 1) . ": User $winner_id wins $amount G\n";
            execute(" INSERT INTO transactions (account_id,type,amount,reference_id,status,created_at) VALUES ($winner_id,'reward',$amount,$rid,'confirmed',NOW()) ");
            $acc = findQuery(" SELECT email,accountname FROM accounts WHERE id=$winner_id ");
            if ($acc['email'] && function_exists('mailer')) {
                $body = "<h1>Lottery Win!</h1><p>Hi {$acc['accountname']},</p><p>Congratulations! You won <b>Rank #" . ($rank + 1) . "</b> in Round #{$round['round_number']}.</p><p>Prize: <b>" . number_format($amount) . " GASHY</b></p>";
                mailer("Lottery Prize: " . number_format($amount) . " G", $body, "Gashy Lottery", $acc['email']);
            }
            $pool = array_filter($pool, fn($id) => $id !== $winner_id);
        }
        $win_json = json_encode($log_data);
        execute(" UPDATE lottery_rounds SET status='closed',winning_numbers='$win_json' WHERE id=$rid ");
    } else {
        echo " -> No Entries. Closing without winners.\n";
        execute(" UPDATE lottery_rounds SET status='closed' WHERE id=$rid ");
    }
    $next_num = $round['round_number'] + 1;
    $next_draw = date('Y-m-d H:i:s', strtotime('+7 days'));
    execute(" INSERT INTO lottery_rounds (round_number,prize_pool,draw_time,status) VALUES ($next_num,0,'$next_draw','open') ");
    echo " -> New Round #$next_num Created.\n";
} else {
    echo "No rounds ready to draw.\n";
}
echo "Done.\n";
