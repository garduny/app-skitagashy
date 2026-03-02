<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW()");
if (!$session) encode(['status' => false, 'message' => 'Unauthorized']);
$aid = $session['account_id'];
$list = getQuery(" SELECT id,amount,status,created_at,tx_signature FROM withdrawals WHERE account_id=$aid ORDER BY id DESC LIMIT 50");
encode(['status' => true, 'data' => $list]);
