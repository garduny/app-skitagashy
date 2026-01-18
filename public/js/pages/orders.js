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
                let statusColor = 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20';
                if (order.status === 'completed') statusColor = 'bg-green-500/10 text-green-500 border-green-500/20';
                if (order.status === 'failed') statusColor = 'bg-red-500/10 text-red-500 border-red-500/20';
                let itemsHtml = order.items.map(item => `
                    <div class="flex items-center gap-4 py-2 border-b border-gray-100 dark:border-white/5 last:border-0">
                        <div class="w-12 h-12 rounded bg-gray-100 dark:bg-white/5 overflow-hidden flex-shrink-0">
                            <img src="${JSON.parse(item.images)[0] || 'assets/placeholder.png'}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900 dark:text-white truncate">${item.title}</p>
                            <p class="text-xs text-gray-500">${item.type.replace('_', ' ').toUpperCase()}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">x${item.quantity}</p>
                            <p class="text-xs text-gray-500">${parseFloat(item.price_at_purchase).toFixed(2)} G</p>
                        </div>
                    </div>
                `).join('');
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
                                    <p class="text-sm font-black text-gray-900 dark:text-white">${parseFloat(order.total_gashy).toFixed(2)} GASHY</p>
                                    <p class="text-xs text-gray-500">${new Date(order.created_at).toLocaleDateString()}</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-bold border ${statusColor} capitalize">${order.status}</span>
                            </div>
                        </div>
                        <div class="p-4">
                            ${itemsHtml}
                        </div>
                        ${order.status === 'completed' ? `
                        <div class="px-4 py-3 bg-gray-50 dark:bg-black/20 border-t border-gray-100 dark:border-white/5 flex justify-end">
                            <button onclick="App.viewOrderDetails(${order.id})" class="text-xs font-bold text-blue-500 hover:text-blue-400 flex items-center gap-1 transition-colors">
                                View Gift Codes / Secrets 
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                        ` : ''}
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
App.viewOrderDetails = async (oid) => {
    notyf.success('Fetching secure data...');
    try {
        const res = await App.post('api/orders/detail.php', { id: oid });
        if (res.status && res.gift_cards.length > 0) {
            let codes = res.gift_cards.map(gc => `Product: ${gc.title}\nCode: ${gc.code_enc}\nPIN: ${gc.pin_enc || 'N/A'}`).join('\n\n');
            alert("🔒 SECRET DATA REVEALED:\n\n" + codes);
        } else {
            alert("No digital codes attached to this order.");
        }
    } catch (e) {
        notyf.error("Failed to fetch details");
    }
};
document.addEventListener('DOMContentLoaded', () => {
    if (App.state.token) App.fetchOrders();
});