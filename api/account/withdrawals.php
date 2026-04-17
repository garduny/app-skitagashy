<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) encode(['status' => false, 'message' => 'Unauthorized']);
$aid = (int)$session['account_id'];
$limit = (int)request('limit');
if ($limit <= 0) $limit = 50;
if ($limit > 100) $limit = 100;
$list = getQuery(" SELECT id,amount,status,created_at,tx_signature FROM withdrawals WHERE account_id=$aid ORDER BY id DESC LIMIT $limit ");
$data = [];
if ($list) {
    foreach ($list as $row) {
        $data[] = [
            'id' => (int)$row['id'],
            'amount' => number_format((float)$row['amount'], 3, '.', ''),
            'status' => strtolower($row['status'] ?: 'pending'),
            'created_at' => $row['created_at'],
            'tx_signature' => $row['tx_signature'] ?: ''
        ];
    }
}
encode([
    'status' => true,
    'count' => count($data),
    'data' => $data
]);
