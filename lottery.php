<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
$round = findQuery(" SELECT * FROM lottery_rounds WHERE status='open' ORDER BY id DESC LIMIT 1 ");
$pool = $round['prize_pool'] ?? 0;
$rid = $round['id'] ?? 0;
$my_tickets = 0;
$token = request('token') ?? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$session = findQuery(" SELECT account_id FROM account_sessions WHERE token='$token' AND expires_at>NOW() ");
if ($session && $rid) {
    $uid = $session['account_id'];
    $ticket_query = findQuery(" SELECT SUM(ticket_count) as total FROM lottery_entries WHERE round_id=$rid AND account_id=$uid ");
    $my_tickets = $ticket_query['total'] ?? 0;
}
$closed_rounds = getQuery(" SELECT winning_numbers, draw_time FROM lottery_rounds WHERE status='closed' AND winning_numbers IS NOT NULL ORDER BY id DESC LIMIT 3 ");
$real_winners = [];
foreach ($closed_rounds as $cr) {
    $winners_array = json_decode($cr['winning_numbers'], true) ?? [];
    foreach ($winners_array as $w) {
        $u = findQuery(" SELECT wallet_address FROM accounts WHERE id=" . (int)$w['user']);
        if ($u) {
            $real_winners[] = [
                'wallet' => substr($u['wallet_address'], 0, 4) . '...' . substr($u['wallet_address'], -4),
                'amount' => $w['amount'],
                'date' => $cr['draw_time']
            ];
        }
    }
}
?>
<style>
    @keyframes coin-flip {
        0% {
            transform: rotateY(0deg)
        }

        100% {
            transform: rotateY(360deg)
        }
    }

    @keyframes pulse-ring {

        0%,
        100% {
            transform: scale(1);
            opacity: 1
        }

        50% {
            transform: scale(1.1);
            opacity: 0.5
        }
    }

    @keyframes number-glow {

        0%,
        100% {
            text-shadow: 0 0 10px rgba(34, 197, 94, 0.5), 0 0 20px rgba(34, 197, 94, 0.3)
        }

        50% {
            text-shadow: 0 0 20px rgba(34, 197, 94, 0.8), 0 0 40px rgba(34, 197, 94, 0.5)
        }
    }

    @keyframes gradient-shift {

        0%,
        100% {
            background-position: 0% 50%
        }

        50% {
            background-position: 100% 50%
        }
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px)
        }

        50% {
            transform: translateY(-10px)
        }
    }

    .prize-card {
        background: linear-gradient(135deg, rgba(19, 24, 36, 0.9), rgba(26, 31, 46, 0.9));
        backdrop-filter: blur(20px);
        border: 1px solid rgba(34, 197, 94, 0.15);
        position: relative;
        overflow: hidden
    }

    .prize-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #22c55e, #10b981, #22c55e);
        background-size: 200% 100%;
        animation: gradient-shift 3s ease infinite
    }

    .prize-card::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 50% 0%, rgba(34, 197, 94, 0.1), transparent 70%);
        pointer-events: none
    }

    .ticket-input {
        background: rgba(10, 14, 26, 0.8);
        border: 2px solid rgba(34, 197, 94, 0.2);
        transition: all 0.3s ease
    }

    .ticket-input:focus {
        border-color: rgba(34, 197, 94, 0.5);
        box-shadow: 0 0 20px rgba(34, 197, 94, 0.2);
        background: rgba(10, 14, 26, 1)
    }

    .buy-btn {
        background: linear-gradient(135deg, #22c55e 0%, #10b981 100%);
        box-shadow: 0 10px 30px rgba(34, 197, 94, 0.3);
        transition: all 0.3s ease
    }

    .buy-btn:hover {
        box-shadow: 0 15px 40px rgba(34, 197, 94, 0.5);
        transform: translateY(-2px)
    }

    .info-card {
        background: linear-gradient(135deg, rgba(19, 24, 36, 0.6), rgba(26, 31, 46, 0.6));
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.3s ease
    }

    .info-card:hover {
        border-color: rgba(34, 197, 94, 0.2);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2)
    }

    .step-number {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.15), rgba(16, 185, 129, 0.15));
        border: 2px solid rgba(34, 197, 94, 0.3)
    }

    .winner-card {
        background: linear-gradient(90deg, rgba(34, 197, 94, 0.05), rgba(16, 185, 129, 0.05));
        border: 1px solid rgba(34, 197, 94, 0.15);
        transition: all 0.3s ease
    }

    .winner-card:hover {
        background: linear-gradient(90deg, rgba(34, 197, 94, 0.1), rgba(16, 185, 129, 0.1));
        border-color: rgba(34, 197, 94, 0.3);
        transform: translateX(4px)
    }

    .badge-live {
        animation: pulse-ring 2s ease-in-out infinite
    }

    html:not(.dark) .prize-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(248, 250, 252, 0.95));
        border: 1px solid rgba(34, 197, 94, 0.2)
    }

    html:not(.dark) .info-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(248, 250, 252, 0.9));
        border: 1px solid rgba(0, 0, 0, 0.08)
    }

    html:not(.dark) .ticket-input {
        background: rgba(255, 255, 255, 0.9);
        border: 2px solid rgba(34, 197, 94, 0.25)
    }

    html:not(.dark) .ticket-input:focus {
        background: rgba(255, 255, 255, 1)
    }

    html:not(.dark) .step-number {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(16, 185, 129, 0.1));
        border: 2px solid rgba(34, 197, 94, 0.25)
    }

    html:not(.dark) .winner-card {
        background: linear-gradient(90deg, rgba(34, 197, 94, 0.03), rgba(16, 185, 129, 0.03));
        border: 1px solid rgba(34, 197, 94, 0.2)
    }

    html:not(.dark) .winner-card:hover {
        background: linear-gradient(90deg, rgba(34, 197, 94, 0.08), rgba(16, 185, 129, 0.08))
    }
</style>
<main class="min-h-screen pt-24 lg:pl-72 bg-gray-50 dark:bg-gradient-to-br dark:from-dark-900 dark:via-dark-800 dark:to-dark-900 text-gray-900 dark:text-white transition-colors duration-300 relative overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none dark:block hidden">
        <div class="absolute top-1/4 left-1/4 w-[500px] h-[500px] bg-green-500/8 rounded-full blur-[120px] animate-float"></div>
        <div class="absolute bottom-1/4 right-1/4 w-[500px] h-[500px] bg-emerald-500/8 rounded-full blur-[120px] animate-float" style="animation-delay:-2s"></div>
    </div>
    <div class="relative z-10 px-4 sm:px-6 lg:px-8 py-8 max-w-6xl mx-auto">
        <div class="prize-card rounded-3xl p-8 md:p-12 text-center mb-10 shadow-2xl relative">
            <div class="absolute inset-0 opacity-5 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAwIDEwIEwgNDAgMTAgTSAxMCAwIEwgMTAgNDAgTSAwIDIwIEwgNDAgMjAgTSAyMCAwIEwgMjAgNDAgTSAwIDMwIEwgNDAgMzAgTSAzMCAwIEwgMzAgNDAiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzAwMCIgb3BhY2l0eT0iMC4xIiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')]"></div>
            <div class="relative z-10">
                <div class="badge-live inline-flex items-center gap-2 py-2 px-4 rounded-full bg-green-500/10 dark:bg-green-500/10 bg-green-100 text-green-600 dark:text-green-400 text-xs font-black uppercase tracking-widest border-2 border-green-500/30 mb-8 shadow-lg">
                    <div class="relative">
                        <div class="w-2 h-2 rounded-full bg-green-500 animate-ping absolute"></div>
                        <div class="w-2 h-2 rounded-full bg-green-500"></div>
                    </div>
                    Round #<?= $round['round_number'] ?? 1 ?> Live
                </div>
                <div class="mb-6 relative inline-block">
                    <div class="absolute inset-0 bg-gradient-to-r from-green-500 to-emerald-500 blur-3xl opacity-30 animate-pulse"></div>
                    <h1 class="relative text-6xl md:text-8xl font-black tracking-tighter mb-2" style="animation:number-glow 2s ease-in-out infinite">
                        <span class="bg-gradient-to-r from-green-400 via-emerald-400 to-green-400 bg-clip-text text-transparent dark:from-green-400 dark:via-emerald-400 dark:to-green-400 from-green-600 via-emerald-600 to-green-600" style="background-size:200% auto"><?= number_format($pool) ?></span>
                    </h1>
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-8 h-8 text-green-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-3xl md:text-4xl font-black text-green-500">GASHY</span>
                    </div>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-lg font-medium mb-10">Current Prize Pool • Draws Every Week</p>
                <div class="max-w-lg mx-auto bg-white/50 dark:bg-black/30 rounded-2xl p-8 border-2 border-green-500/20 dark:border-green-500/20 border-green-200 backdrop-blur-xl shadow-2xl">
                    <div class="flex items-center justify-between mb-6 pb-6 border-b-2 border-gray-200 dark:border-white/10">
                        <div class="text-left">
                            <span class="block text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider mb-1">Ticket Price</span>
                            <span class="text-2xl font-black bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">10 GASHY</span>
                        </div>
                        <div class="text-right">
                            <span class="block text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider mb-1">Your Tickets</span>
                            <span class="text-2xl font-black text-gray-900 dark:text-white"><?= $my_tickets ?></span>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="relative">
                            <input type="number" id="ticket-qty" value="1" min="1" class="ticket-input w-24 rounded-xl text-center text-xl font-black text-gray-900 dark:text-white focus:outline-none py-4">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 flex flex-col gap-1">
                                <button onclick="document.getElementById('ticket-qty').stepUp()" class="w-5 h-5 flex items-center justify-center rounded bg-green-500/20 hover:bg-green-500/30 text-green-600 transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7" />
                                    </svg>
                                </button>
                                <button onclick="document.getElementById('ticket-qty').stepDown()" class="w-5 h-5 flex items-center justify-center rounded bg-green-500/20 hover:bg-green-500/30 text-green-600 transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <button onclick="buyTickets(<?= $rid ?>)" class="buy-btn flex-1 text-white font-black text-lg rounded-xl py-4 flex items-center justify-center gap-2 relative overflow-hidden group">
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                            <svg class="w-6 h-6 transform group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                            Buy Tickets
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
            <div class="info-card rounded-2xl p-8 shadow-xl">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-500 flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black bg-gradient-to-r from-gray-900 to-green-600 dark:from-white dark:to-green-400 bg-clip-text text-transparent">How It Works</h3>
                </div>
                <ul class="space-y-6">
                    <li class="flex gap-5">
                        <div class="step-number w-12 h-12 rounded-xl flex items-center justify-center font-black text-xl text-green-600 dark:text-green-400 shrink-0 shadow-lg">1</div>
                        <div class="pt-2">
                            <h4 class="font-bold text-gray-900 dark:text-white mb-1">Purchase Tickets</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">Buy tickets using $GASHY tokens. 100% of all ticket sales go directly into the prize pool.</p>
                        </div>
                    </li>
                    <li class="flex gap-5">
                        <div class="step-number w-12 h-12 rounded-xl flex items-center justify-center font-black text-xl text-green-600 dark:text-green-400 shrink-0 shadow-lg">2</div>
                        <div class="pt-2">
                            <h4 class="font-bold text-gray-900 dark:text-white mb-1">Wait for the Draw</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">Winners are selected fairly and transparently using Chainlink VRF (Verifiable Random Function).</p>
                        </div>
                    </li>
                    <li class="flex gap-5">
                        <div class="step-number w-12 h-12 rounded-xl flex items-center justify-center font-black text-xl text-green-600 dark:text-green-400 shrink-0 shadow-lg">3</div>
                        <div class="pt-2">
                            <h4 class="font-bold text-gray-900 dark:text-white mb-1">Claim Your Prize</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">Prizes are automatically airdropped to winners' wallets instantly after the draw.</p>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="info-card rounded-2xl p-8 shadow-xl">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-yellow-500 to-orange-500 flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black bg-gradient-to-r from-gray-900 to-yellow-600 dark:from-white dark:to-yellow-400 bg-clip-text text-transparent">Recent Winners</h3>
                </div>
                <div class="space-y-4">
                    <?php if (empty($real_winners)): ?>
                        <div class="text-center py-8 text-gray-500">No winners drawn yet.</div>
                    <?php else: ?>
                        <?php foreach ($real_winners as $i => $w):
                            $colors = [
                                'from-yellow-400 via-yellow-500 to-yellow-600',
                                'from-gray-300 via-gray-400 to-gray-500',
                                'from-orange-400 via-orange-500 to-orange-600'
                            ];
                            $c = $colors[$i % 3];
                        ?>
                            <div class="winner-card flex items-center justify-between p-5 rounded-xl">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br <?= $c ?> shadow-lg flex items-center justify-center font-black text-white">#<?= $i + 1 ?></div>
                                    <div>
                                        <span class="block text-sm font-black text-gray-900 dark:text-white mb-1"><?= $w['wallet'] ?></span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 font-medium"><?= date('M d, Y', strtotime($w['date'])) ?></span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="block text-lg font-black bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">+<?= number_format($w['amount']) ?></span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 font-bold">GASHY</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>
<script>
    window.GASHY_TICKET_PRICE = 10;
</script>
<script src="./public/js/pages/lottery.js"></script>
<?php require_once 'footer.php'; ?>