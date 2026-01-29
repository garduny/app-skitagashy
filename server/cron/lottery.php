<?php
require_once 'init.php';
$round = findQuery(" SELECT * FROM lottery_rounds WHERE status='open' AND draw_time<=NOW() ");
if ($round) {
    echo "Drawing Lottery Round #{$round['round_number']}...\n";
    $rid = $round['id'];
    $entries = getQuery(" SELECT account_id,ticket_count FROM lottery_entries WHERE round_id=$rid ");
    if (!empty($entries)) {
        $pool = [];
        foreach ($entries as $e) {
            for ($i = 0; $i < $e['ticket_count']; $i++) {
                $pool[] = $e['account_id'];
            }
        }
        $winner_id = $pool[array_rand($pool)];
        $prize = $round['prize_pool'];
        execute(" UPDATE lottery_rounds SET status='closed', winning_numbers='{\"winner_id\":$winner_id,\"amount\":$prize}' WHERE id=$rid ");
        execute(" INSERT INTO transactions (account_id,type,amount,reference_id,status,created_at) VALUES ($winner_id,'reward',$prize,$rid,'confirmed',NOW()) ");
        echo " -> Winner: $winner_id (Prize: $prize)\n";
    } else {
        execute(" UPDATE lottery_rounds SET status='closed' WHERE id=$rid ");
        echo " -> No Entries.\n";
    }
    $next_num = $round['round_number'] + 1;
    $next_draw = date('Y-m-d H:i:s', strtotime('+7 days'));
    execute(" INSERT INTO lottery_rounds (round_number,prize_pool,draw_time,status) VALUES ($next_num,0,'$next_draw','open') ");
    echo " -> New Round #$next_num Started.\n";
} else {
    echo "No lottery rounds pending.\n";
}
