<?php
if (php_sapi_name() !== 'cli') {
    if (!defined('gashy_exec')) define('gashy_exec', true);
}
if (!isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = '/';
}
if (!isset($_SERVER['HTTP_USER_AGENT'])) {
    $_SERVER['HTTP_USER_AGENT'] = 'CronJob';
}
require_once __DIR__ . '/../init.php';
echo "[" . date('Y-m-d H:i:s') . "] Cron Started: Oracle Update\n";
$token_address = "DokPYQ33k3T9S7EEesvwvuuAtoQb4pY8NWszukKwXWjv";
$cache_file = __DIR__ . '/../../server/.cache/price.json';
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://public-api.birdeye.so/defi/price?address=$token_address",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 5,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HTTPHEADER => [
        "X-API-KEY: 4d28e7508f7542d9a6042466002d997d",
        "x-chain: solana",
        "accept: application/json"
    ]
]);
$response = curl_exec($curl);
$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$err = curl_error($curl);
curl_close($curl);
$price = 0;
$source = 'API';
if ($response && $http_code === 200) {
    $json = json_decode($response, true);
    if (isset($json['data']['value']) && $json['data']['value'] > 0) {
        $price = (float)$json['data']['value'];
    } else {
        $source = 'Simulation (Token not found)';
        $price = 0;
    }
} else {
    $source = 'Simulation (API Error: ' . ($err ?: $http_code) . ')';
    $price = 0;
}
if ($price <= 0) {
    $prev = 0.045;
    if (file_exists($cache_file)) {
        $old_data = json_decode(file_get_contents($cache_file), true);
        $prev = $old_data['price'] ?? 0.045;
    }
    $change = $prev * (rand(-20, 20) / 1000);
    $price = $prev + $change;
    if ($price < 0.01) $price = 0.01;
    if ($price > 1.00) $price = 0.45;
}
if (!is_dir(dirname($cache_file))) mkdir(dirname($cache_file), 0755, true);
file_put_contents($cache_file, json_encode([
    'price' => $price,
    'source' => $source,
    'updated' => time()
]));
echo " -> Source: $source\n";
echo " -> New Price: $" . number_format($price, 6) . "\n";
echo "Done.\n";
