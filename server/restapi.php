<?php
require __DIR__ . '/./config.php';
header('Access-Control-Allow-Origin:' . $config['api']['access_allow_origin']);
header('Access-Control-Allow-Credentials:' . $config['api']['access_allow_credentials']);
header('Content-Type:' . $config['api']['content_type']);
header('Access-Control-Allow-Methods:' . $config['api']['access_allow_methods']);
header('Access-Control-Allow-Headers:' . $config['api']['access_allow_headers']);
$body = json_decode(file_get_contents('php://input'));
$data = [];
