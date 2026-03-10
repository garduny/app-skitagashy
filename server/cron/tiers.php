<?php
if (php_sapi_name() !== 'cli') {
    if (!defined('gashy_exec')) define('gashy_exec', true);
}
if (!isset($_SERVER['REQUEST_URI'])) $_SERVER['REQUEST_URI'] = '/cron/tiers';
if (!isset($_SERVER['HTTP_USER_AGENT'])) $_SERVER['HTTP_USER_AGENT'] = 'CronJob';
require_once __DIR__ . '/../init.php';
echo "[" . date('Y-m-d H:i:s') . "] Tier Logic Started...\n";
$tiers = ['diamond' => 100000, 'platinum' => 25000, 'gold' => 5000, 'silver' => 1000, 'bronze' => 0];
$accounts = getQuery(" SELECT id,tier,email FROM accounts ");
foreach ($accounts as $acc) {
    $uid = (int)$acc['id'];
    $spent = findQuery(" SELECT SUM(total_gashy) t FROM orders WHERE account_id=$uid AND status='completed' ")['t'] ?? 0;
    $burned = findQuery(" SELECT SUM(amount) t FROM burn_log WHERE account_id=$uid ")['t'] ?? 0;
    $score = $spent + $burned;
    $new_tier = 'bronze';
    foreach ($tiers as $name => $min) {
        if ($score >= $min) {
            $new_tier = $name;
            break;
        }
    }
    if ($new_tier !== $acc['tier']) {
        execute(" UPDATE accounts SET tier='$new_tier' WHERE id=$uid ");
        echo " -> Upgraded User #$uid to " . strtoupper($new_tier) . " (Score: $score)\n";
        if ($acc['email'] && function_exists('mailer')) mailer("Tier Upgrade!", "You are now a <b>" . strtoupper($new_tier) . "</b> member!", "Gashy Rewards", $acc['email']);
    }
}
echo "Done.\n";
