<?php
if (php_sapi_name() !== 'cli') {
    if (!defined('gashy_exec')) define('gashy_exec', true);
}
if (!isset($_SERVER['REQUEST_URI'])) $_SERVER['REQUEST_URI'] = '/';
if (!isset($_SERVER['HTTP_USER_AGENT'])) $_SERVER['HTTP_USER_AGENT'] = 'CronJob';
require_once __DIR__ . '/../init.php';
echo "[" . date('Y-m-d H:i:s') . "] Cron Started: Oracle Update\n";
$token_address = "DokPYQ33k3T9S7EEesvwvuuAtoQb4pY8NWszukKwXWjv";
$cache_file = __DIR__ . '/../../server/.cache/price.json';
$url = "https://api.dexscreener.com/latest/dex/tokens/$token_address";
$curl = curl_init();
curl_setopt_array($curl, [CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10, CURLOPT_CONNECTTIMEOUT => 5, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_USERAGENT => "KITTA-GASHY-Oracle/1.0"]);
$response = curl_exec($curl);
$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$err = curl_error($curl);
curl_close($curl);
$price = 0;
$marketcap = 0;
$volume24h = 0;
$liquidity = 0;
$change24h = 0;
$source = 'Dexscreener';
if ($response && $http_code === 200) {
    $j = json_decode($response, true);
    $best = null;
    $bestLiq = 0;
    foreach (($j['pairs'] ?? []) as $pair) {
        $liq = (float)($pair['liquidity']['usd'] ?? 0);
        if ($liq > $bestLiq) {
            $bestLiq = $liq;
            $best = $pair;
        }
    }
    if ($best) {
        $price = (float)($best['priceUsd'] ?? 0);
        $marketcap = (float)($best['fdv'] ?? 0);
        $volume24h = (float)($best['volume']['h24'] ?? 0);
        $liquidity = (float)($best['liquidity']['usd'] ?? 0);
        $change24h = (float)($best['priceChange']['h24'] ?? 0);
        if ($price <= 0) $source = 'Simulation (Pair invalid)';
    } else {
        $source = 'Simulation (No valid pair)';
    }
} else {
    $source = 'Simulation (API Error: ' . ($err ?: $http_code) . ')';
}
if ($price <= 0) {
    $prev = 0.045;
    if (file_exists($cache_file)) {
        $old = json_decode(file_get_contents($cache_file), true);
        $prev = $old['price'] ?? 0.045;
    }
    $change = $prev * (rand(-20, 20) / 1000);
    $price = $prev + $change;
    if ($price < 0.0000001) $price = 0.0000001;
    if ($price > 1.00) $price = 0.45;
}
if (!is_dir(dirname($cache_file))) mkdir(dirname($cache_file), 0755, true);
file_put_contents($cache_file, json_encode(['price' => $price, 'marketcap' => $marketcap, 'volume24h' => $volume24h, 'liquidity' => $liquidity, 'change24h' => $change24h, 'source' => $source, 'updated' => time()]));
echo " -> Source: $source\n";
echo " -> Price: $" . number_format($price, 6) . "\n";
echo " -> MCap: $" . number_format($marketcap, 2) . "\n";
echo " -> Vol24h: $" . number_format($volume24h, 2) . "\n";
echo " -> Liquidity: $" . number_format($liquidity, 2) . "\n";
echo "Done.\n";
