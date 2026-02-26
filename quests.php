<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
?>
<style>
    @keyframes pulse-ring {

        0%,
        100% {
            transform: scale(1);
            opacity: 1
        }

        50% {
            transform: scale(1.05);
            opacity: 0.8
        }
    }

    @keyframes float-up {

        0%,
        100% {
            transform: translateY(0)
        }

        50% {
            transform: translateY(-10px)
        }
    }

    @keyframes shimmer {
        0% {
            background-position: 200% center
        }

        100% {
            background-position: -200% center
        }
    }

    @keyframes glow-pulse {

        0%,
        100% {
            box-shadow: 0 0 20px rgba(34, 197, 94, 0.3)
        }

        50% {
            box-shadow: 0 0 40px rgba(34, 197, 94, 0.6)
        }
    }

    .quest-card {
        background: linear-gradient(135deg, rgba(19, 24, 36, 0.7), rgba(26, 31, 46, 0.7));
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden
    }

    .quest-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(34, 197, 94, 0.1), transparent);
        transition: left 0.6s
    }

    .quest-card:hover::before {
        left: 100%
    }

    .quest-card:hover {
        border-color: rgba(34, 197, 94, 0.3);
        box-shadow: 0 8px 30px rgba(34, 197, 94, 0.15);
        transform: translateY(-4px)
    }

    .quest-icon {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.15), rgba(16, 185, 129, 0.15));
        border: 2px solid rgba(34, 197, 94, 0.3);
        animation: float-up 3s ease-in-out infinite
    }

    .progress-bar {
        background: linear-gradient(90deg, #22c55e, #10b981);
        background-size: 200% 100%;
        animation: shimmer 2s linear infinite;
        box-shadow: 0 0 20px rgba(34, 197, 94, 0.4)
    }

    .balance-badge {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.15), rgba(16, 185, 129, 0.15));
        border: 2px solid rgba(34, 197, 94, 0.3);
        animation: glow-pulse 3s ease-in-out infinite
    }

    .claim-btn {
        background: linear-gradient(135deg, #22c55e, #10b981);
        box-shadow: 0 8px 25px rgba(34, 197, 94, 0.3);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden
    }

    .claim-btn::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transform: translateX(-100%);
        transition: transform 0.6s
    }

    .claim-btn:hover::before {
        transform: translateX(100%)
    }

    .claim-btn:hover {
        box-shadow: 0 12px 35px rgba(34, 197, 94, 0.5);
        transform: translateY(-2px)
    }

    html:not(.dark) .quest-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(248, 250, 252, 0.95));
        border: 1px solid rgba(0, 0, 0, 0.08)
    }

    html:not(.dark) .quest-card:hover {
        box-shadow: 0 8px 30px rgba(34, 197, 94, 0.12)
    }

    html:not(.dark) .quest-icon {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(16, 185, 129, 0.1));
        border: 2px solid rgba(34, 197, 94, 0.25)
    }

    html:not(.dark) .balance-badge {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(16, 185, 129, 0.1));
        border: 2px solid rgba(34, 197, 94, 0.25)
    }
</style>
<main class="min-h-screen pt-24 lg:pl-72 bg-gray-50 dark:bg-gradient-to-br dark:from-dark-900 dark:via-dark-800 dark:to-dark-900 text-gray-900 dark:text-white transition-all duration-300 relative overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none dark:block hidden">
        <div class="absolute top-1/4 right-1/4 w-[500px] h-[500px] bg-green-500/8 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-1/4 left-1/4 w-[500px] h-[500px] bg-emerald-500/8 rounded-full blur-[120px]"></div>
    </div>
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-500 flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-black bg-gradient-to-r from-gray-900 via-green-900 to-gray-900 dark:from-white dark:via-green-200 dark:to-white bg-clip-text text-transparent tracking-tight">Quest Board</h1>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-lg pl-15">Complete daily tasks and earn free <span class="font-bold text-green-500">$GASHY</span> rewards</p>
            </div>
            <div class="balance-badge px-6 py-4 rounded-2xl backdrop-blur-xl">
                <div class="text-xs text-gray-600 dark:text-gray-400 font-bold uppercase tracking-wider mb-1">Total Earned</div>
                <div id="quest-balance" class="text-2xl font-black bg-gradient-to-r from-green-600 to-emerald-600 dark:from-green-400 dark:to-emerald-400 bg-clip-text text-transparent font-mono">0.00 G</div>
            </div>
        </div>
        <div id="quests-container" class="space-y-6">
            <div class="flex flex-col items-center justify-center py-32">
                <div class="relative">
                    <div class="w-24 h-24 rounded-full border-4 border-green-500/20 border-t-green-500 animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-10 h-10 text-green-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                </div>
                <p class="text-gray-600 dark:text-gray-400 mt-8 text-sm font-bold uppercase tracking-widest">Loading Quests...</p>
            </div>
        </div>
    </div>
</main>
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        if (!App.state.token) {
            window.location.href = 'app.php';
            return;
        }
        updateBalanceDisplay();
        await loadQuests();
    });

    function updateBalanceDisplay() {
        if (App.state.account && App.state.account.tier_progress) {
            document.getElementById('quest-balance').innerText = parseFloat(App.state.account.tier_progress.current).toFixed(2) + ' G';
        }
    }
    async function loadQuests() {
        const container = document.getElementById('quests-container');
        try {
            const res = await App.post('api/account/quests.php', {});
            if (res.status && res.data) {
                if (res.data.length === 0) {
                    container.innerHTML = `
<div class="flex flex-col items-center justify-center py-32 text-center">
<div class="w-32 h-32 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 dark:from-dark-700 dark:to-dark-800 flex items-center justify-center mb-8 shadow-2xl">
<svg class="w-16 h-16 text-gray-500 dark:text-gray-600 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
</svg>
</div>
<h3 class="text-3xl font-black text-gray-900 dark:text-white mb-4">No Active Quests</h3>
<p class="text-gray-600 dark:text-gray-400 text-lg">Check back later for new challenges and rewards!</p>
</div>`;
                    return;
                }
                container.innerHTML = res.data.map(q => {
                    const pct = Math.min(100, (q.progress / q.target_count) * 100);
                    const isDone = q.progress >= q.target_count;
                    const isClaimed = q.is_claimed == 1;
                    let actionBtn = '';
                    if (isClaimed) {
                        actionBtn = `<button disabled class="px-6 py-3 rounded-xl bg-gray-200 dark:bg-white/5 text-gray-500 dark:text-gray-400 text-sm font-bold uppercase cursor-not-allowed flex items-center gap-2">
<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
</svg>
Claimed
</button>`;
                    } else if (isDone) {
                        actionBtn = `<button onclick="claimReward(${q.id})" class="claim-btn px-6 py-3 rounded-xl text-white text-sm font-black uppercase shadow-2xl flex items-center gap-2">
<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
</svg>
Claim ${q.reward_gashy} G
</button>`;
                    } else {
                        actionBtn = `<div class="text-sm font-mono text-gray-600 dark:text-gray-400 font-bold">${q.progress} / ${q.target_count}</div>`;
                    }
                    return `
<div class="quest-card rounded-2xl p-6 md:p-8 shadow-2xl">
<div class="flex flex-col md:flex-row items-start md:items-center gap-6">
<div class="quest-icon w-16 h-16 rounded-2xl flex items-center justify-center text-4xl shrink-0 shadow-lg">${getIcon(q.action_type)}</div>
<div class="flex-1 w-full">
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
<h3 class="text-xl font-black text-gray-900 dark:text-white">${q.title}</h3>
<span class="text-[10px] font-black uppercase px-3 py-1.5 rounded-lg bg-gradient-to-r from-green-500/20 to-emerald-500/20 text-green-600 dark:text-green-400 border border-green-500/30 w-fit">${q.reset_period}</span>
</div>
<div class="space-y-2">
<div class="flex items-center justify-between text-xs font-bold text-gray-600 dark:text-gray-400">
<span>Progress</span>
<span class="text-green-600 dark:text-green-400">${pct.toFixed(0)}%</span>
</div>
<div class="w-full h-3 bg-gray-200 dark:bg-dark-800 rounded-full overflow-hidden shadow-inner">
<div class="progress-bar h-full transition-all duration-1000 rounded-full" style="width:${pct}%"></div>
</div>
</div>
</div>
<div class="w-full md:w-auto">${actionBtn}</div>
</div>
</div>`;
                }).join('');
            }
        } catch (e) {
            console.error(e);
            container.innerHTML = `
<div class="text-center py-20">
<div class="text-red-500 text-6xl mb-4">⚠️</div>
<h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">Failed to Load Quests</h3>
<p class="text-gray-600 dark:text-gray-400">Please try again later</p>
</div>`;
        }
    }

    function getIcon(type) {
        if (type === 'burn') return '🔥';
        if (type === 'buy') return '🛒';
        if (type === 'login') return '👋';
        if (type === 'trade') return '💱';
        if (type === 'stake') return '💎';
        return '🎯';
    }
    async function claimReward(qid) {
        try {
            const res = await App.post('api/account/claim_quest.php', {
                quest_id: qid
            });
            if (res.status) {
                window.notyf.success(res.message);
                await App.fetchProfile();
                updateBalanceDisplay();
                loadQuests();
            } else {
                window.notyf.error(res.message);
            }
        } catch (e) {
            window.notyf.error('Claim failed');
        }
    }
</script>
<?php require_once 'footer.php'; ?>