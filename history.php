<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
?>
<style>
    @keyframes slide-in {
        0% {
            opacity: 0;
            transform: translateX(-20px)
        }

        100% {
            opacity: 1;
            transform: translateX(0)
        }
    }

    .history-row {
        animation: slide-in 0.3s ease-out forwards;
        opacity: 0
    }

    .history-row:nth-child(1) {
        animation-delay: 0.05s
    }

    .history-row:nth-child(2) {
        animation-delay: 0.1s
    }

    .history-row:nth-child(3) {
        animation-delay: 0.15s
    }

    .history-row:nth-child(4) {
        animation-delay: 0.2s
    }

    .history-row:nth-child(5) {
        animation-delay: 0.25s
    }

    .table-container {
        background: linear-gradient(135deg, rgba(19, 24, 36, 0.6), rgba(26, 31, 46, 0.6));
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.05)
    }

    .filter-btn {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.3s ease
    }

    .filter-btn.active {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(139, 92, 246, 0.2));
        border-color: rgba(59, 130, 246, 0.4);
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2)
    }

    .filter-btn:not(.active):hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.2)
    }

    html:not(.dark) .table-container {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(248, 250, 252, 0.95));
        border: 1px solid rgba(0, 0, 0, 0.08)
    }

    html:not(.dark) .filter-btn {
        background: rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(0, 0, 0, 0.08)
    }

    html:not(.dark) .filter-btn.active {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(139, 92, 246, 0.15));
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.15)
    }

    html:not(.dark) .filter-btn:not(.active):hover {
        background: rgba(0, 0, 0, 0.05)
    }
</style>
<main class="min-h-screen pt-24 lg:pl-72 bg-gray-50 dark:bg-gradient-to-br dark:from-dark-900 dark:via-dark-800 dark:to-dark-900 text-gray-900 dark:text-white transition-all duration-300 relative overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none dark:block hidden">
        <div class="absolute top-1/4 left-1/4 w-[500px] h-[500px] bg-blue-500/8 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-1/4 right-1/4 w-[500px] h-[500px] bg-purple-500/8 rounded-full blur-[120px]"></div>
    </div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-10">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-black bg-gradient-to-r from-gray-900 via-blue-900 to-gray-900 dark:from-white dark:via-blue-200 dark:to-white bg-clip-text text-transparent tracking-tight">Activity History</h1>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-lg pl-15">Track your bids, rewards, burns, and transactions</p>
            </div>
            <div class="flex flex-wrap gap-3 p-2 rounded-2xl bg-white/50 dark:bg-white/5 backdrop-blur-xl border border-gray-200 dark:border-white/10 shadow-lg">
                <button onclick="setFilter('','all')" id="filter-all" class="filter-btn active px-5 py-3 rounded-xl text-sm font-black transition-all">
                    <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    ALL
                </button>
                <button onclick="setFilter('auction_bid','bid')" id="filter-bid" class="filter-btn px-5 py-3 rounded-xl text-sm font-black text-gray-600 dark:text-gray-400 transition-all">
                    <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                    BIDS
                </button>
                <button onclick="setFilter('reward','reward')" id="filter-reward" class="filter-btn px-5 py-3 rounded-xl text-sm font-black text-gray-600 dark:text-gray-400 transition-all">
                    <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    REWARDS
                </button>
                <button onclick="setFilter('purchase','purchase')" id="filter-purchase" class="filter-btn px-5 py-3 rounded-xl text-sm font-black text-gray-600 dark:text-gray-400 transition-all">
                    <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    BUYS
                </button>
                <button onclick="setFilter('lottery_ticket','lottery_ticket')" id="filter-lottery_ticket" class="filter-btn px-5 py-3 rounded-xl text-sm font-black text-gray-600 dark:text-gray-400 transition-all">
                    <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Lottery
                </button>
            </div>
        </div>
        <div class="table-container rounded-3xl shadow-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-white/5 border-b-2 border-gray-200 dark:border-white/10">
                            <th class="px-6 py-5 text-xs font-black text-gray-600 dark:text-gray-400 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-5 text-xs font-black text-gray-600 dark:text-gray-400 uppercase tracking-wider">Action</th>
                            <th class="px-6 py-5 text-xs font-black text-gray-600 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-5 text-xs font-black text-gray-600 dark:text-gray-400 uppercase tracking-wider">Reference</th>
                            <th class="px-6 py-5 text-xs font-black text-gray-600 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-5 text-xs font-black text-gray-600 dark:text-gray-400 uppercase tracking-wider text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody id="history-list" class="divide-y divide-gray-100 dark:divide-white/5"></tbody>
                </table>
            </div>
            <div id="history-loading" class="p-16 text-center">
                <div class="relative inline-block">
                    <div class="w-20 h-20 rounded-full border-4 border-blue-500/20 border-t-blue-500 animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-gray-600 dark:text-gray-400 mt-6 text-sm font-bold uppercase tracking-widest">Loading History...</p>
            </div>
        </div>
    </div>
</main>
<script>
    let currentFilter = '';
    document.addEventListener('DOMContentLoaded', () => {
        if (App.state.token) loadHistory('');
    });

    function setFilter(type, btnId) {
        currentFilter = type;
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
            btn.classList.add('text-gray-600', 'dark:text-gray-400');
        });
        const activeBtn = document.getElementById('filter-' + btnId);
        if (activeBtn) {
            activeBtn.classList.add('active');
            activeBtn.classList.remove('text-gray-600', 'dark:text-gray-400');
        }
        loadHistory(type);
    }
    async function loadHistory(type) {
        const list = document.getElementById('history-list');
        const loader = document.getElementById('history-loading');
        list.innerHTML = '';
        loader.classList.remove('hidden');
        try {
            const res = await App.post('./api/account/history.php', {
                type: type
            });
            loader.classList.add('hidden');
            if (res.status && res.data.length > 0) {
                list.innerHTML = res.data.map(t => `
<tr class="history-row hover:bg-gray-50 dark:hover:bg-white/5 transition-all duration-200 cursor-pointer">
<td class="px-6 py-5">
<span class="px-3 py-1 rounded-lg bg-gray-100 dark:bg-white/5 font-mono text-xs font-bold text-gray-600 dark:text-gray-400">#${t.id}</span>
</td>
<td class="px-6 py-5">
<span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase ${getTypeColor(t.type)} border ${getTypeBorder(t.type)}">
${getTypeIcon(t.type)}
${t.type.replace('_',' ')}
</span>
</td>
<td class="px-6 py-5">
<span class="font-mono font-black text-lg ${parseFloat(t.amount)>0?'text-green-600 dark:text-green-400':'text-red-600 dark:text-red-400'}">
${parseFloat(t.amount)>0?'+':''}${parseFloat(t.amount).toFixed(2)} G
</span>
</td>
<td class="px-6 py-5">
<div class="flex items-center gap-2">
<code class="text-xs font-mono text-gray-600 dark:text-gray-400 truncate max-w-[150px] block" title="${t.tx_signature}">${t.tx_signature}</code>
<button onclick="navigator.clipboard.writeText('${t.tx_signature}');notyf.success('Copied!')" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
<svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
</svg>
</button>
</div>
</td>
<td class="px-6 py-5">
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-gray-100 dark:bg-white/5 text-xs font-bold text-gray-700 dark:text-gray-300 capitalize">
<div class="w-2 h-2 rounded-full ${t.status==='completed'?'bg-green-500':'bg-yellow-500'} animate-pulse"></div>
${t.status}
</span>
</td>
<td class="px-6 py-5 text-right">
<div class="text-sm font-bold text-gray-700 dark:text-gray-300">${new Date(t.created_at).toLocaleDateString()}</div>
<div class="text-xs text-gray-500 dark:text-gray-500 font-mono">${new Date(t.created_at).toLocaleTimeString()}</div>
</td>
</tr>`).join('');
            } else {
                list.innerHTML = `
<tr>
<td colspan="6" class="p-16 text-center">
<div class="flex flex-col items-center">
<div class="w-24 h-24 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 dark:from-dark-700 dark:to-dark-800 flex items-center justify-center mb-6 shadow-xl">
<svg class="w-12 h-12 text-gray-500 dark:text-gray-600 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
</svg>
</div>
<h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">No Activity Found</h3>
<p class="text-gray-600 dark:text-gray-400">Start trading to see your transaction history here</p>
</div>
</td>
</tr>`;
            }
        } catch (e) {
            console.error(e);
            loader.classList.add('hidden');
            list.innerHTML = `
<tr>
<td colspan="6" class="p-16 text-center">
<div class="text-red-500 text-5xl mb-4">⚠️</div>
<h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Failed to Load History</h3>
<p class="text-gray-600 dark:text-gray-400">Please try again later</p>
</td>
</tr>`;
        }
    }

    function getTypeColor(t) {
        if (t === 'reward') return 'bg-green-500/10 text-green-600 dark:text-green-400';
        if (t === 'purchase') return 'bg-red-500/10 text-red-600 dark:text-red-400';
        if (t === 'auction_bid') return 'bg-blue-500/10 text-blue-600 dark:text-blue-400';
        if (t === 'burn') return 'bg-orange-500/10 text-orange-600 dark:text-orange-400';
        if (t === 'lottery_ticket') return 'bg-orange-500/10 text-orange-600 dark:text-orange-400';
        return 'bg-gray-500/10 text-gray-600 dark:text-gray-400';
    }

    function getTypeBorder(t) {
        if (t === 'reward') return 'border-green-500/30';
        if (t === 'purchase') return 'border-red-500/30';
        if (t === 'auction_bid') return 'border-blue-500/30';
        if (t === 'burn') return 'border-orange-500/30';
        if (t === 'lottery_ticket') return 'border-orange-500/30';
        return 'border-gray-500/30';
    }

    function getTypeIcon(t) {
        if (t === 'reward') return '💰';
        if (t === 'purchase') return '🛒';
        if (t === 'auction_bid') return '📊';
        if (t === 'burn') return '🔥';
        if (t === 'lottery_ticket') return '🔥';
        return '📝';
    }
</script>
<?php require_once 'footer.php'; ?>