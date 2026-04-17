<?php
if (file_exists('../../server/init.php')) require_once '../../server/init.php';
else exit;
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if (!$session) encode(['status' => false, 'message' => 'Unauthorized']);
$uid = (int)$session['account_id'];
$json = file_get_contents('php://input');
$data = json_decode($json, true);
$aid = (int)($data['auction_id'] ?? 0);
$amount = (float)($data['amount'] ?? 0);
if ($aid < 1 || $amount <= 0) encode(['status' => false, 'message' => 'Invalid input']);
$rate = (float)toGashy();
if ($rate <= 0) encode(['status' => false, 'message' => 'Invalid token rate']);
$emails = [];
execute(" START TRANSACTION ");
try {
    $me = findQuery(" SELECT id,accountname,email FROM accounts WHERE id=$uid ");
    $auc = findQuery(" SELECT a.*,p.title,p.seller_id,p.stock,s.store_name,acc.email seller_email,acc.accountname seller_name FROM auctions a LEFT JOIN products p ON p.id=a.product_id LEFT JOIN sellers s ON s.account_id=p.seller_id LEFT JOIN accounts acc ON acc.id=p.seller_id WHERE a.id=$aid FOR UPDATE ");
    if (!$auc) throw new Exception('Auction not found');
    if ($auc['status'] !== 'active') throw new Exception('Auction is not active');
    if ((int)$auc['seller_id'] === $uid) throw new Exception('You cannot bid on your own auction');
    $now = time();
    $endTime = strtotime($auc['end_time']);
    if (!$endTime || $endTime <= $now) throw new Exception('Auction has ended');
    if ((int)($auc['stock'] ?? 1) < 1) throw new Exception('Item out of stock');
    $currentUsd = (float)$auc['current_bid_usd'];
    $startUsd = (float)$auc['start_price_usd'];
    $baseUsd = max($currentUsd, $startUsd);
    $minInc = max(1, round($baseUsd * 0.05, 8));
    $minBidUsd = $baseUsd + $minInc;
    $bidUsd = round($amount * $rate, 8);
    if ($bidUsd < $minBidUsd) {
        $need = $minBidUsd / $rate;
        throw new Exception('Bid too low. Minimum bid is ' . number_format($need, 2) . ' GASHY');
    }
    $oldBidder = (int)$auc['highest_bidder_id'];
    $oldUser = $oldBidder > 0 ? findQuery(" SELECT accountname,email FROM accounts WHERE id=$oldBidder ") : null;
    $extendMsg = '';
    $extendSql = '';
    $newEnd = $auc['end_time'];
    if (($endTime - $now) <= 300) {
        $newEnd = date('Y-m-d H:i:s', $endTime + 300);
        $extendSql = ", end_time='$newEnd'";
        $extendMsg = ' (Time Extended)';
    }
    $txSig = 'BID_' . $aid . '_' . $uid . '_' . time();
    execute(" UPDATE auctions SET current_bid_usd=$bidUsd,highest_bidder_id=$uid $extendSql WHERE id=$aid ");
    execute(" INSERT INTO transactions (account_id,type,amount,tx_signature,reference_id,status,created_at) VALUES ($uid,'auction_bid',-$amount,'$txSig',$aid,'confirmed',NOW()) ");
    if ($oldBidder > 0 && $oldBidder !== $uid) {
        execute(" INSERT INTO transactions (account_id,type,amount,tx_signature,reference_id,status,created_at) VALUES ($oldBidder,'auction_outbid_refund',$amount,'OUTBID_$txSig',$aid,'confirmed',NOW()) ");
    }
    if (function_exists('logActivity')) logActivity('account', $uid, 'auction_bid', 'Auction #' . $aid . ' bid ' . $amount . ' GASHY');
    if (function_exists('updateQuestProgress')) updateQuestProgress($uid, 'bid', $amount);
    execute(" COMMIT ");
    $title = $auc['title'] ?: 'Auction Item';
    $store = $auc['store_name'] ?: 'Seller';
    $myName = $me['accountname'] ?: 'User';
    if (($me['email'] ?? '') && function_exists('mailer')) {
        $subject = "Bid Confirmed - Auction #$aid";
        $body = "<div style='font-family:Arial;padding:20px;color:#222'><h2 style='color:#00d48f'>Bid Confirmed</h2><p>Hello {$myName},</p><p>Your bid is now highest for <strong>{$title}</strong>.</p><p><strong>Bid:</strong> " . number_format($amount, 2) . " GASHY<br><strong>Auction:</strong> #$aid<br><strong>Ends:</strong> {$newEnd}</p><p><a href='https://gashybazaar.com/auctions.php' style='padding:10px 18px;background:#00d48f;color:#fff;text-decoration:none;border-radius:6px'>View Auctions</a></p></div>";
        mailer($subject, $body, 'Gashy Bazaar', $me['email']);
    }
    if (($auc['seller_email'] ?? '') && function_exists('mailer')) {
        $sellerName = $auc['seller_name'] ?: 'Seller';
        $subject = "New Bid On Your Auction #$aid";
        $body = "<div style='font-family:Arial;padding:20px;color:#222'><h2 style='color:#7c3aed'>New Highest Bid</h2><p>Hello {$sellerName},</p><p>Your auction <strong>{$title}</strong> received a new highest bid.</p><p><strong>Bidder:</strong> {$myName}<br><strong>Bid:</strong> " . number_format($amount, 2) . " GASHY<br><strong>Store:</strong> {$store}</p><p><a href='https://gashybazaar.com/dashboard/auctions.php' style='padding:10px 18px;background:#7c3aed;color:#fff;text-decoration:none;border-radius:6px'>Manage Auction</a></p></div>";
        mailer($subject, $body, 'Gashy Bazaar', $auc['seller_email']);
    }
    if (($oldUser['email'] ?? '') && $oldBidder !== $uid && function_exists('mailer')) {
        $oldName = $oldUser['accountname'] ?: 'User';
        $subject = "You Were Outbid - Auction #$aid";
        $body = "<div style='font-family:Arial;padding:20px;color:#222'><h2 style='color:#ef4444'>You Were Outbid</h2><p>Hello {$oldName},</p><p>Another user placed a higher bid on <strong>{$title}</strong>.</p><p><strong>Current Bid:</strong> " . number_format($amount, 2) . " GASHY</p><p><a href='https://gashybazaar.com/auctions.php' style='padding:10px 18px;background:#ef4444;color:#fff;text-decoration:none;border-radius:6px'>Bid Again</a></p></div>";
        mailer($subject, $body, 'Gashy Bazaar', $oldUser['email']);
    }
    encode([
        'status' => true,
        'message' => 'Bid placed successfully' . $extendMsg,
        'auction_id' => $aid,
        'new_bid_gashy' => round($amount, 8),
        'new_bid_usd' => $bidUsd,
        'min_next_bid_gashy' => round(($bidUsd + max(1, $bidUsd * 0.05)) / $rate, 8),
        'end_time' => $newEnd
    ]);
} catch (Exception $e) {
    execute(" ROLLBACK ");
    encode(['status' => false, 'message' => $e->getMessage()]);
}
