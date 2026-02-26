document.addEventListener('DOMContentLoaded', async () => {
    if (!App.state.token) {
        document.getElementById('guest-view').classList.remove('hidden');
        document.getElementById('auth-view').classList.add('hidden');
        return;
    }
    document.getElementById('guest-view').classList.add('hidden');
    document.getElementById('auth-view').classList.remove('hidden');
    await loadAccountData();
    await loadRecentOrders();
});
async function loadAccountData() {
    try {
        const res = await App.post('./api/account/profile.php', {});
        if (res.status && res.data) {
            const u = res.data;
            document.getElementById('profile-accountname').innerText = u.accountname || 'Trader';
            document.getElementById('profile-wallet').innerText = u.wallet_address;
            document.getElementById('profile-tier').innerText = u.tier;
            document.getElementById('profile-spent').innerText = parseFloat(u.stats.spent || 0).toFixed(0) + ' GASHY';
            document.getElementById('profile-orders-count').innerText = u.stats.orders || 0;
            document.getElementById('input-accountname').value = u.accountname || '';
            document.getElementById('input-email').value = u.email || '';
            document.getElementById('referral-code').value = 'GASHY-REF-Account' + u.id;
            const tiers = { 'bronze': '🥉', 'silver': '🥈', 'gold': '🥇', 'platinum': '💎', 'diamond': '👑' };
            document.getElementById('account-tier-icon').innerText = tiers[u.tier] || '🥉';
        }
    } catch (e) {
        console.error("Profile Load Error", e);
    }
}
async function loadRecentOrders() {
    const list = document.getElementById('recent-orders-list');
    try {
        const res = await App.post('./api/orders/history.php', { page: 1, limit: 3 });
        if (res.status && res.data && res.data.length > 0) {
            list.innerHTML = res.data.map(o => `
                <div class="bg-white dark:bg-[#151A23] rounded-xl p-4 border border-gray-200 dark:border-white/5 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm dark:shadow-none hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                    <div class="flex items-center gap-4 w-full sm:w-auto">
                        <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-white/5 flex items-center justify-center text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <div>
                            <div class="text-gray-900 dark:text-white font-bold">Order #${o.id}</div>
                            <div class="text-xs text-gray-500">${new Date(o.created_at).toLocaleDateString()}</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between w-full sm:w-auto gap-4">
                        <div class="text-right">
                            <div class="text-gray-900 dark:text-white font-bold">${parseFloat(o.total_gashy).toFixed(2)} G</div>
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase ${getStatusColor(o.status)}">${o.status}</span>
                        </div>
                        <button onclick="location.href='orders.php'" class="px-3 py-1.5 bg-gray-100 dark:bg-white/10 hover:bg-gray-200 rounded-lg text-xs text-gray-600 dark:text-gray-300">View</button>
                    </div>
                </div>
            `).join('');
        } else {
            list.innerHTML = `
                <div class="p-8 text-center bg-white dark:bg-[#151A23] rounded-2xl border border-gray-200 dark:border-white/5">
                    <p class="text-gray-500">No orders found.</p>
                    <a href="market.php" class="text-blue-500 hover:underline mt-2 inline-block text-sm">Browse Market</a>
                </div>
            `;
        }
    } catch (e) {
        list.innerHTML = `<div class="text-red-500 text-center text-sm">Failed to load orders</div>`;
    }
}
async function saveProfile() {
    const accountname = document.getElementById('input-accountname').value;
    const email = document.getElementById('input-email').value;
    notyf.success('Updating Profile...');
    try {
        const res = await App.post('./api/account/update.php', { accountname, email });
        if (res.status) {
            notyf.success(res.message);
            document.getElementById('profile-accountname').innerText = accountname || 'Account';
        } else {
            notyf.error(res.message);
        }
    } catch (e) {
        notyf.error('Update Failed');
    }
}
function getStatusColor(status) {
    if (status === 'completed') return 'bg-green-100 text-green-600 dark:bg-green-500/20 dark:text-green-400';
    if (status === 'pending') return 'bg-yellow-100 text-yellow-600 dark:bg-yellow-500/20 dark:text-yellow-400';
    return 'bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-400';
}