<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) require_once 'server/init.php';
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
            opacity: .8
        }
    }

    @keyframes float-up {

        0%,
        100% {
            transform: translateY(0)
        }

        50% {
            transform: translateY(-8px)
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

    .quest-card {
        background: linear-gradient(135deg, rgba(19, 24, 36, .72), rgba(26, 31, 46, .72));
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, .06);
        transition: .3s;
        position: relative;
        overflow: hidden
    }

    .quest-card:hover {
        transform: translateY(-4px);
        border-color: rgba(34, 197, 94, .35);
        box-shadow: 0 15px 40px rgba(34, 197, 94, .12)
    }

    .quest-icon {
        background: linear-gradient(135deg, rgba(34, 197, 94, .15), rgba(16, 185, 129, .15));
        border: 1px solid rgba(34, 197, 94, .28);
        animation: float-up 3s ease-in-out infinite
    }

    .progress-bar {
        background: linear-gradient(90deg, #22c55e, #10b981, #22c55e);
        background-size: 200% 100%;
        animation: shimmer 2s linear infinite
    }

    .claim-btn {
        background: linear-gradient(135deg, #22c55e, #10b981);
        box-shadow: 0 10px 25px rgba(34, 197, 94, .28);
        transition: .3s
    }

    .claim-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 35px rgba(34, 197, 94, .42)
    }

    .stats-card {
        background: linear-gradient(135deg, rgba(34, 197, 94, .12), rgba(16, 185, 129, .08));
        border: 1px solid rgba(34, 197, 94, .2)
    }

    html:not(.dark) .quest-card {
        background: linear-gradient(135deg, #fff, #f8fafc);
        border: 1px solid rgba(0, 0, 0, .08)
    }

    html:not(.dark) .stats-card {
        background: linear-gradient(135deg, rgba(34, 197, 94, .08), rgba(16, 185, 129, .06))
    }
</style>

<main class="min-h-screen pt-24 lg:pl-72 bg-gray-50 dark:bg-gradient-to-br dark:from-dark-900 dark:via-dark-800 dark:to-dark-900 text-gray-900 dark:text-white transition-all overflow-hidden">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-500 flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 12a5 5 0 1110 0 5 5 0 01-10 0z" />
                        </svg>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-black tracking-tight bg-gradient-to-r from-gray-900 via-green-700 to-gray-900 dark:from-white dark:via-green-300 dark:to-white bg-clip-text text-transparent">Quest Board</h1>
                </div>
                <p class="text-lg text-gray-600 dark:text-gray-400">Complete missions and earn free <span class="font-black text-green-500">$GASHY</span></p>
            </div>
            <div class="stats-card rounded-2xl px-6 py-4">
                <div class="text-xs uppercase tracking-widest text-gray-500 font-bold">Total Earned</div>
                <div id="quest-balance" class="text-2xl font-black text-green-500 font-mono mt-1">0.00 G</div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="stats-card rounded-2xl p-5">
                <div class="text-xs uppercase text-gray-500 font-bold">Available Quests</div>
                <div id="stat-total" class="mt-2 text-3xl font-black">0</div>
            </div>
            <div class="stats-card rounded-2xl p-5">
                <div class="text-xs uppercase text-gray-500 font-bold">Completed</div>
                <div id="stat-done" class="mt-2 text-3xl font-black text-green-500">0</div>
            </div>
            <div class="stats-card rounded-2xl p-5">
                <div class="text-xs uppercase text-gray-500 font-bold">Claimed</div>
                <div id="stat-claimed" class="mt-2 text-3xl font-black text-blue-500">0</div>
            </div>
        </div>

        <div id="quests-container" class="space-y-5">
            <div class="text-center py-24">
                <div class="w-20 h-20 mx-auto rounded-full border-4 border-green-500/20 border-t-green-500 animate-spin"></div>
                <div class="mt-6 text-sm uppercase tracking-widest font-bold text-gray-500">Loading Quests...</div>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        setTimeout(() => {
            if (!App.state.token) location.href = 'profile.php'
        }, 1200)
        updateBalanceDisplay()
        await loadQuests()
    })

    function updateBalanceDisplay() {
        let v = 0
        if (App.state.account && App.state.account.wallet_stats) v = parseFloat(App.state.account.wallet_stats.reward_balance || 0)
        document.getElementById('quest-balance').innerText = v.toFixed(2) + ' G'
    }

    function iconByType(type) {
        if (type === 'burn') return '🔥'
        if (type === 'buy') return '🛒'
        if (type === 'login') return '👋'
        if (type === 'order') return '📦'
        return '🎯'
    }

    async function loadQuests() {
        const box = document.getElementById('quests-container')
        try {
            const res = await App.post('./api/account/quests.php', {})
            if (!res.status) {
                box.innerHTML = '<div class="text-center py-16 text-red-500 font-bold">Failed to load quests</div>';
                return
            }
            const rows = res.data || []
            document.getElementById('stat-total').innerText = rows.length
            document.getElementById('stat-done').innerText = rows.filter(x => parseFloat(x.progress) >= parseFloat(x.target_count)).length
            document.getElementById('stat-claimed').innerText = rows.filter(x => parseInt(x.is_claimed) == 1).length

            if (!rows.length) {
                box.innerHTML = '<div class="text-center py-24"><div class="text-6xl mb-4">🎯</div><div class="text-3xl font-black mb-2">No Active Quests</div><div class="text-gray-500">Check back later for new rewards.</div></div>'
                return
            }

            box.innerHTML = rows.map(q => {
                const progress = parseFloat(q.progress || 0)
                const target = parseFloat(q.target_count || 1)
                const pct = Math.min(100, (progress / target) * 100)
                const done = progress >= target
                const claimed = parseInt(q.is_claimed) == 1
                let btn = ''
                if (claimed) {
                    btn = '<button disabled class="px-5 py-3 rounded-xl bg-gray-200 dark:bg-white/5 text-gray-500 text-sm font-bold">Claimed</button>'
                } else if (done) {
                    btn = `<button onclick="claimReward(${q.id})" class="claim-btn px-5 py-3 rounded-xl text-white text-sm font-black">Claim ${parseFloat(q.reward_gashy).toFixed(2)} G</button>`
                } else {
                    btn = `<div class="text-sm font-black text-gray-500">${progress}/${target}</div>`
                }
                return `
<div class="quest-card rounded-2xl p-6">
<div class="flex flex-col md:flex-row gap-5 md:items-center">
<div class="quest-icon w-16 h-16 rounded-2xl flex items-center justify-center text-3xl shrink-0">${iconByType(q.action_type)}</div>
<div class="flex-1">
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">
<div class="text-xl font-black">${q.title}</div>
<div class="text-[10px] uppercase px-3 py-1 rounded-lg bg-green-500/10 text-green-500 font-bold w-fit">${q.reset_period}</div>
</div>
<div class="flex justify-between text-xs font-bold text-gray-500 mb-2">
<span>Progress</span>
<span>${pct.toFixed(0)}%</span>
</div>
<div class="w-full h-3 rounded-full bg-gray-200 dark:bg-dark-900 overflow-hidden">
<div class="progress-bar h-full rounded-full" style="width:${pct}%"></div>
</div>
</div>
<div class="md:ml-3">${btn}</div>
</div>
</div>`
            }).join('')
        } catch (e) {
            box.innerHTML = '<div class="text-center py-16 text-red-500 font-bold">Unexpected error</div>'
        }
    }

    async function claimReward(id) {
        try {
            const r = await App.post('./api/account/claim_quest.php', {
                quest_id: id
            })
            if (r.status) {
                window.notyf.success(r.message || 'Reward claimed')
                await App.fetchProfile()
                updateBalanceDisplay()
                loadQuests()
            } else {
                window.notyf.error(r.message || 'Unable to claim')
            }
        } catch (e) {
            window.notyf.error('Claim failed')
        }
    }
</script>
<?php require_once 'footer.php'; ?>