App.fetchOrders = async () => {
    if (!App.checkAuth()) return;
    const container = document.getElementById('orders-container');
    const emptyState = document.getElementById('empty-state');
    container.innerHTML = `<div class="text-center py-12"><svg class="w-12 h-12 text-blue-500 mx-auto animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>`;
    try {
        const res = await App.post('api/orders/history.php', { page: 1, limit: 50 });
        container.innerHTML = '';
        if (res.status && res.data && res.data.length > 0) {
            emptyState.classList.add('hidden');
            res.data.forEach(order => {
                let itemsHtml = order.items.map(item => `
                    <div class="flex items-center gap-4 py-3 border-b border-gray-100 dark:border-white/5 last:border-0">
                        <div class="w-12 h-12 rounded bg-gray-100 dark:bg-white/5 overflow-hidden flex-shrink-0">
                            <img src="${JSON.parse(item.images)[0] || 'assets/placeholder.png'}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900 dark:text-white truncate">${item.title}</p>
                            <p class="text-xs text-gray-500 uppercase">${item.type.replace('_', ' ')}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">x${item.quantity}</p>
                            <p class="text-xs text-gray-500">${parseFloat(item.price_at_purchase).toFixed(2)} G</p>
                        </div>
                    </div>
                `).join('');
                const badge = getStatusBadge(order.status);
                const html = `
                    <div class="bg-white dark:bg-[#151A23] rounded-xl border border-gray-200 dark:border-white/5 overflow-hidden transition-all hover:shadow-lg dark:hover:shadow-blue-900/5">
                        <div class="p-4 border-b border-gray-100 dark:border-white/5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50 dark:bg-white/5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-bold">Order #${order.id}</p>
                                    <p class="text-xs font-mono text-gray-400" title="${order.tx_signature}">${order.tx_signature.substring(0, 16)}...</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <p class="text-sm font-black text-gray-900 dark:text-white">${parseFloat(order.total_gashy).toFixed(2)} G</p>
                                    <p class="text-xs text-gray-500">${new Date(order.created_at).toLocaleDateString()}</p>
                                </div>
                                ${badge}
                            </div>
                        </div>
                        <div class="p-4">${itemsHtml}</div>
                        ${['completed', 'delivered'].includes(order.status) ? `
                        <div class="px-4 py-3 bg-gray-50 dark:bg-black/20 border-t border-gray-100 dark:border-white/5 flex justify-end">
                            <button onclick="App.viewOrderDetails(${order.id})" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-xs font-bold transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Reveal Codes
                            </button>
                        </div>` : ''}
                    </div>
                `;
                container.innerHTML += html;
            });
        } else {
            emptyState.classList.remove('hidden');
        }
    } catch (e) {
        console.error(e);
        container.innerHTML = `<div class="text-center text-red-500 py-4">Failed to load orders.</div>`;
    }
};
function getStatusBadge(status) {
    const config = {
        'pending': { color: 'text-yellow-500', bg: 'bg-yellow-500/10', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>' },
        'processing': { color: 'text-blue-500', bg: 'bg-blue-500/10', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>' },
        'shipped': { color: 'text-purple-500', bg: 'bg-purple-500/10', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>' },
        'delivered': { color: 'text-green-500', bg: 'bg-green-500/10', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>' },
        'completed': { color: 'text-green-500', bg: 'bg-green-500/10', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>' },
        'failed': { color: 'text-red-500', bg: 'bg-red-500/10', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>' },
        'refunded': { color: 'text-red-500', bg: 'bg-red-500/10', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>' }
    };
    const s = config[status] || config['pending'];
    return `<span class="px-3 py-1 rounded-full text-xs font-bold uppercase ${s.color} ${s.bg} flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">${s.icon}</svg>${status}</span>`;
}
App.viewOrderDetails = async (oid) => {
    notyf.success('Authenticating & Decrypting...');
    try {
        const res = await App.post('api/orders/detail.php', { id: oid });
        if (res.status) {
            if (res.gift_cards.length > 0) {
                let msg = "🔐 SECRET CONTENT:\n\n";
                res.gift_cards.forEach(i => msg += `📦 ${i.product}\nCode: ${i.code}\n${i.pin ? 'Pin: ' + i.pin : ''}\n----------------\n`);
                alert(msg);
            } else {
                notyf.error("No digital codes found.");
            }
        } else {
            notyf.error(res.message);
        }
    } catch (e) {
        notyf.error("Decryption Failed");
    }
};
document.addEventListener('DOMContentLoaded', () => {
    if (App.state.token) App.fetchOrders();
});