<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) encode(['status' => false, 'message' => 'Unauthorized']);
$uid = (int)$session['account_id'];
$page = (int)request('page');
$limit = (int)request('limit');
$type = trim((string)request('type'));
if ($page <= 0) $page = 1;
if ($limit <= 0) $limit = 10;
if ($limit > 50) $limit = 50;
$offset = ($page - 1) * $limit;
$where = " WHERE account_id=$uid ";
if ($type !== '') $where .= " AND type='" . secure($type) . "' ";
$total = (int)(findQuery(" SELECT COUNT(id) c FROM transactions $where ")['c'] ?? 0);
$list = getQuery(" SELECT id,type,amount,status,reference_id,created_at,tx_signature FROM transactions $where ORDER BY id DESC LIMIT $offset,$limit ");
$data = [];
if ($list) {
    foreach ($list as $row) {
        $data[] = [
            'id' => (int)$row['id'],
            'type' => $row['type'] ?: '',
            'amount' => number_format((float)$row['amount'], 3, '.', ''),
            'total_gashy' => number_format((float)$row['amount'], 3, '.', ''),
            'status' => strtolower($row['status'] ?: 'pending'),
            'reference_id' => $row['reference_id'] ?: '',
            'created_at' => $row['created_at'],
            'tx_signature' => $row['tx_signature'] ?: ''
        ];
    }
}
encode([
    'status' => true,
    'page' => $page,
    'limit' => $limit,
    'total' => $total,
    'pages' => $limit > 0 ? ceil($total / $limit) : 1,
    'data' => $data
]);
