<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$price = 0.045;
$change24h = 0;
$vol24h = 0;
$cache_file = '../../server/.cache/price.json';
if (file_exists($cache_file)) {
    $data = json_decode(file_get_contents($cache_file), true);
    $price = $data['price'] ?? $price;
    $change24h = $data['change24h'] ?? $change24h;
    $vol24h = $data['volume24h'] ?? $vol24h;
}
$formatted_vol = '$0';
if ($vol24h >= 1000000) $formatted_vol = '$' . number_format($vol24h / 1000000, 1) . 'M';
elseif ($vol24h >= 1000) $formatted_vol = '$' . number_format($vol24h / 1000, 1) . 'K';
else $formatted_vol = '$' . number_format($vol24h, 2);
encode([
    'status' => true,
    'token' => 'GASHY',
    'price_usd' => $price,
    'change_24h' => round($change24h, 2),
    'volume_formatted' => $formatted_vol,
    'last_updated' => time()
]);
