<?php
require_once __DIR__ . '/init.php';
echo "[" . date('Y-m-d H:i:s') . "] Oracle Started\n";
if (php_sapi_name() !== 'cli') {
    if (!defined('gashy_exec')) define('gashy_exec', true);
}
if (!isset($_SERVER['REQUEST_URI'])) $_SERVER['REQUEST_URI'] = '/';
if (!isset($_SERVER['HTTP_USER_AGENT'])) $_SERVER['HTTP_USER_AGENT'] = 'CronJob';
require_once __DIR__ . '/../init.php';
echo "[" . date('Y-m-d H:i:s') . "] Cron Started: Oracle Update\n";
$token_address = "DokPYQ33k3T9S7EEesvwvuuAtoQb4pY8NWszukKwXWjv";
$cache_file = __DIR__ . '/../../server/.cache/price.json';
$tmp_cache_file = $cache_file . '.tmp';
$url = "https://api.dexscreener.com/latest/dex/tokens/$token_address";
$price = 0;
$marketcap = 0;
$volume24h = 0;
$liquidity = 0;
$change24h = 0;
$source = 'Dexscreener';
$fallback_used = false;
$cached = [];
if (file_exists($cache_file)) {
    $cached = json_decode(file_get_contents($cache_file), true);
    if (!is_array($cached)) $cached = [];
}
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
    CURLOPT_USERAGENT => "KITTA-GASHY-Oracle/1.0",
    CURLOPT_HTTPHEADER => ['Accept: application/json']
]);
$res = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);
if ($res && $http === 200) {
    $j = json_decode($res, true);
    $best = null;
    $bestLiq = 0;
    foreach (($j['pairs'] ?? []) as $p) {
        $liq = (float)($p['liquidity']['usd'] ?? 0);
        $pairPrice = (float)($p['priceUsd'] ?? 0);
        if ($liq > $bestLiq && $pairPrice > 0) {
            $bestLiq = $liq;
            $best = $p;
        }
    }
    if ($best) {
        $price = (float)($best['priceUsd'] ?? 0);
        $marketcap = (float)($best['fdv'] ?? 0);
        $volume24h = (float)($best['volume']['h24'] ?? 0);
        $liquidity = (float)($best['liquidity']['usd'] ?? 0);
        $change24h = (float)($best['priceChange']['h24'] ?? 0);
        if ($price <= 0 || $price > 1000000) {
            $price = 0;
            $source = 'Fallback (Invalid live price)';
        }
    } else {
        $source = 'Fallback (No valid pair)';
    }
} else {
    $source = 'Fallback (API Error: ' . ($err ?: $http) . ')';
}
if ($price <= 0) {
    $fallback_used = true;
    $price = (float)($cached['price'] ?? 0);
    $marketcap = (float)($cached['marketcap'] ?? 0);
    $volume24h = (float)($cached['volume24h'] ?? 0);
    $liquidity = (float)($cached['liquidity'] ?? 0);
    $change24h = (float)($cached['change24h'] ?? 0);
    if ($price <= 0) {
        $price = 0.045;
        $marketcap = 0;
        $volume24h = 0;
        $liquidity = 0;
        $change24h = 0;
        $source = 'Fallback (Default bootstrap)';
    } else {
        $source = 'Fallback (Cached last known good)';
    }
}
if (!is_dir(dirname($cache_file))) mkdir(dirname($cache_file), 0755, true);
$payload = [
    'price' => $price,
    'marketcap' => $marketcap,
    'volume24h' => $volume24h,
    'liquidity' => $liquidity,
    'change24h' => $change24h,
    'source' => $source,
    'fallback_used' => $fallback_used ? 1 : 0,
    'updated' => time()
];
file_put_contents($tmp_cache_file, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
rename($tmp_cache_file, $cache_file);
echo " -> Source: $source\n";
echo " -> Price: $" . number_format($price, 6) . "\n";
echo " -> MCap: $" . number_format($marketcap, 2) . "\n";
echo " -> Vol24h: $" . number_format($volume24h, 2) . "\n";
echo " -> Liquidity: $" . number_format($liquidity, 2) . "\n";
echo " -> Fallback Used: " . ($fallback_used ? 'YES' : 'NO') . "\n";
echo "Done.\n";
