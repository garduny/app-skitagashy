document.addEventListener('DOMContentLoaded', async () => {
    const guest = document.getElementById('guest-view')
    const auth = document.getElementById('auth-view')
    if (!App.state.token) {
        guest.classList.remove('hidden')
        guest.classList.add('flex')
        auth.classList.add('hidden')
        return
    }
    guest.classList.add('hidden')
    guest.classList.remove('flex')
    auth.classList.remove('hidden')
    await Promise.all([loadAccountData(), loadWithdrawals(), loadRecentOrders()])
})
const $ = id => document.getElementById(id)
const setText = (id, val) => { const el = $(id); if (el) el.innerText = val }
const setVal = (id, val) => { const el = $(id); if (el) el.value = val }
const fmt = n => parseFloat(n || 0).toFixed(3)
const fmt2 = n => parseFloat(n || 0).toFixed(2)
const fmt0 = n => parseFloat(n || 0).toFixed(0)
const shortWallet = v => !v ? '' : (v.length > 18 ? v.substring(0, 8) + '...' + v.slice(-8) : v)
function esc(v) {
    return String(v ?? '').replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]))
}
async function loadAccountData() {
    try {
        const res = await App.post('./api/account/profile.php', {})
        if (!res.status || !res.data) return
        const u = res.data
        setText('profile-accountname', u.accountname || 'Trader')
        setText('profile-wallet', shortWallet(u.wallet_address || ''))
        setText('profile-tier', (u.tier || 'bronze').toUpperCase())
        setText('profile-spent', fmt0(u.stats?.spent) + ' GASHY')
        setText('profile-orders-count', u.stats?.orders || 0)
        setVal('input-accountname', u.accountname || '')
        setVal('input-email', u.email || '')
        setVal('referral-code', 'GASHY-REF-Account' + u.id)
        setText('withdrawable-balance', fmt(u.wallet_stats?.withdrawable) + ' GASHY')
        const tiers = { bronze: '🥉', silver: '🥈', gold: '🥇', platinum: '💎', diamond: '👑' }
        setText('account-tier-icon', tiers[(u.tier || 'bronze').toLowerCase()] || '🥉')
    } catch (e) {
        console.error(e)
        notyf.error('Profile load failed')
    }
}
async function requestWithdraw() {
    const btn = event?.currentTarget
    const amount = parseFloat(($('withdraw-amount').value || 0))
    if (amount <= 0) { notyf.error('Invalid amount'); return }
    if (btn) btn.disabled = true
    try {
        const res = await App.post('./api/account/withdrawal.php', { amount })
        if (res.status) {
            notyf.success(res.message || 'Request sent')
            setText('withdrawable-balance', fmt(res.available) + ' GASHY')
            $('withdraw-amount').value = ''
            loadWithdrawals()
        } else notyf.error(res.message || 'Failed')
    } catch (e) {
        notyf.error('Withdraw failed')
    }
    if (btn) btn.disabled = false
}
async function loadWithdrawals() {
    const box = $('withdrawals-list')
    try {
        const res = await App.post('./api/account/withdrawals.php', {})
        if (res.status && res.data && res.data.length) {
            box.innerHTML = res.data.map(w => `
<div class="stat-line">
<div>
<div class="font-black text-gray-900 dark:text-white">${fmt(w.amount)} GASHY</div>
<div class="subtle-text text-xs">${new Date(w.created_at).toLocaleDateString()}</div>
</div>
<span class="px-3 py-1 rounded-full text-[10px] font-black uppercase ${getWithdrawColor(w.status)}">${esc(w.status)}</span>
</div>
`).join('')
        } else {
            box.innerHTML = `<div class="stat-line"><div><div class="font-bold text-gray-900 dark:text-white text-sm">No withdrawals yet</div><div class="subtle-text text-xs">Your requests will appear here.</div></div></div>`
        }
    } catch (e) {
        box.innerHTML = `<div class="text-center text-red-500 text-sm py-4">Failed to load</div>`
    }
}
function getWithdrawColor(status) {
    status = (status || '').toLowerCase()
    if (status === 'approved') return 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400'
    if (status === 'pending') return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400'
    return 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400'
}
async function loadRecentOrders() {
    const list = $('recent-orders-list')
    try {
        const res = await App.post('./api/orders/history.php', { page: 1, limit: 3 })
        if (res.status && res.data && res.data.length) {
            list.innerHTML = res.data.map(o => `
<div class="order-item">
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
<div class="flex items-center gap-4">
<div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500/15 to-purple-500/15 flex items-center justify-center border border-blue-500/15">
<svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
</div>
<div>
<div class="font-black text-gray-900 dark:text-white">Order #${esc(o.id)}</div>
<div class="subtle-text text-xs">${new Date(o.created_at).toLocaleDateString()}</div>
</div>
</div>
<div class="flex items-center gap-3 justify-between sm:justify-end">
<div class="text-right">
<div class="font-black text-gray-900 dark:text-white">${fmt2(o.total_gashy)} G</div>
<span class="inline-block mt-1 px-3 py-1 rounded-full text-[10px] font-black uppercase ${getStatusColor(o.status)}">${esc(o.status)}</span>
</div>
<a href="orders.php" class="action-btn px-4 py-2 text-xs">View</a>
</div>
</div>
</div>
`).join('')
        } else {
            list.innerHTML = `<div class="empty-state"><div class="space-y-2"><h3 class="text-lg font-black text-gray-900 dark:text-white">No orders found</h3><p class="subtle-text">Browse products and your orders will appear here.</p></div><a href="market.php" class="action-btn px-5 py-3 text-sm">Browse Market</a></div>`
        }
    } catch (e) {
        list.innerHTML = `<div class="text-red-500 text-center text-sm py-4">Failed to load orders</div>`
    }
}
async function saveProfile() {
    const btn = event?.currentTarget
    const accountname = $('input-accountname').value.trim()
    const email = $('input-email').value.trim()
    if (!accountname) { notyf.error('Username required'); return }
    if (btn) btn.disabled = true
    try {
        const res = await App.post('./api/account/update.php', { accountname, email })
        if (res.status) {
            notyf.success(res.message || 'Updated')
            setText('profile-accountname', accountname)
        } else notyf.error(res.message || 'Update failed')
    } catch (e) {
        notyf.error('Update failed')
    }
    if (btn) btn.disabled = false
}
function getStatusColor(status) {
    status = (status || '').toLowerCase()
    if (status === 'completed' || status === 'paid' || status === 'success') return 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400'
    if (status === 'pending' || status === 'processing') return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400'
    return 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400'
}