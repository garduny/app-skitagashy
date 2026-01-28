<?php
if (file_exists('../../server/init.php')) {
    require_once '../../server/init.php';
} else {
    exit;
}
$price = 0.045;
$cache_file = '../../server/.cache/price.json';
if (file_exists($cache_file) && (time() - filemtime($cache_file) < 300)) {
    $data = json_decode(file_get_contents($cache_file), true);
    $price = $data['price'] ?? $price;
} else {
    $token_address = "DokPYQ33k3T9S7EEesvwvuuAtoQb4pY8NWszukKwXWjv";
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://public-api.birdeye.so/defi/price?address=$token_address",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_HTTPHEADER => [
            "X-API-KEY: 4d28e7508f7542d9a6042466002d997d",
            "x-chain: solana"
        ]
    ]);
    $response = curl_exec($curl);
    curl_close($curl);
    if ($response) {
        $json = json_decode($response, true);
        if (isset($json['data']['value'])) {
            $price = $json['data']['value'];
            if (!is_dir('../../server/.cache')) {
                mkdir('../../server/.cache', 0755, true);
            }
            file_put_contents($cache_file, json_encode(['price' => $price, 'updated' => time()]));
        }
    }
}
encode([
    'status' => true,
    'token' => 'GASHY',
    'price_usd' => $price,
    'last_updated' => time()
]);
