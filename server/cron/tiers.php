<?php
require_once __DIR__.'/init.php';
echo "[".date('Y-m-d H:i:s')."] Tier Started\n";
if (php_sapi_name() !== 'cli') {
    if (!defined('gashy_exec')) define('gashy_exec', true);
}
if (!isset($_SERVER['REQUEST_URI'])) $_SERVER['REQUEST_URI'] = '/cron/tiers';
if (!isset($_SERVER['HTTP_USER_AGENT'])) $_SERVER['HTTP_USER_AGENT'] = 'CronJob';
require_once __DIR__ . '/../init.php';
echo "[" . date('Y-m-d H:i:s') . "] Tier Logic Started...\n";
$tiers = [
    'diamond' => 100000,
    'platinum' => 25000,
    'gold' => 5000,
    'silver' => 1000,
    'bronze' => 0
];
$accounts = getQuery("
SELECT 
a.id,
a.tier,
a.email,
COALESCE(o.spent,0) spent,
COALESCE(b.burned,0) burned
FROM accounts a
LEFT JOIN (
    SELECT account_id,SUM(total_gashy) spent
    FROM orders
    WHERE status='completed'
    GROUP BY account_id
) o ON o.account_id=a.id
LEFT JOIN (
    SELECT account_id,SUM(amount) burned
    FROM burn_log
    GROUP BY account_id
) b ON b.account_id=a.id
ORDER BY a.id ASC
");
$changed = 0;
foreach ($accounts as $acc) {
    $uid = (int)$acc['id'];
    $spent = (float)$acc['spent'];
    $burned = (float)$acc['burned'];
    $score = $spent + $burned;
    $oldTier = strtolower((string)$acc['tier']);
    $newTier = 'bronze';
    foreach ($tiers as $name => $min) {
        if ($score >= $min) {
            $newTier = $name;
            break;
        }
    }
    if ($newTier !== $oldTier) {
        execute(" UPDATE accounts SET tier='$newTier' WHERE id=$uid ");
        $changed++;
        $type = array_search($newTier, array_keys($tiers)) < array_search($oldTier, array_keys($tiers)) ? 'UPGRADED' : 'CHANGED';
        echo " -> User #{$uid} {$type} {$oldTier} => {$newTier} (Score: " . number_format($score, 3, '.', '') . ")\n";
        if (!empty($acc['email']) && function_exists('mailer')) {
            $subject = "Tier Updated";
            $body = "You are now a <b>" . strtoupper($newTier) . "</b> member.<br>Score: <b>" . number_format($score, 3, '.', '') . "</b>";
            mailer($subject, $body, "Gashy Rewards", $acc['email']);
        }
    }
}
echo " -> Updated Accounts: {$changed}\n";
echo "Done.\n";
