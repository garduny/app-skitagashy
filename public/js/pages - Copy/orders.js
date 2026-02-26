App.state.ordersTab = 'my';
App.switchTab = (tab) => {
    App.state.ordersTab = tab;
    document.getElementById('tab-my').classList.toggle('active', tab === 'my');
    document.getElementById('tab-sold').classList.toggle('active', tab === 'sold');
    App.fetchOrders();
};
App.fetchOrders = async () => {
    if (!App.checkAuth()) return;
    const container = document.getElementById('orders-container');
    const empty = document.getElementById('empty-state');
    empty.classList.add('hidden');
    container.classList.remove('hidden');
    container.innerHTML = `<div class="ord-loader"><div class="ord-spinner"></div><p class="ord-loader-text">Loading Orders</p></div>`;
    try {
        const res = await App.post('./api/orders/history.php', { page: 1, limit: 50, tab: App.state.ordersTab });
        container.innerHTML = '';
        if (res.is_seller) document.getElementById('tab-sold').classList.remove('hidden');
        if (res.status && res.data && res.data.length) {
            res.data.forEach(order => {
                const isSoldTab = App.state.ordersTab === 'sold';
                const canEdit = res.can_edit && isSoldTab;
                const safeTx = order.tx_signature ? order.tx_signature.substring(0, 16) + '…' : '—';
                const hasDigital = (order.items || []).some(i => ['digital', 'gift_card', 'mystery_box'].includes(i.type));
                const showReveal = !isSoldTab && order.status === 'completed' && hasDigital;
                const statusSelect = canEdit
                    ? `<select onchange="App.updateStatus(${order.id},this.value)" class="status-select">${['pending', 'processing', 'shipped', 'delivered', 'completed', 'failed', 'refunded'].map(s => `<option value="${s}"${s === order.status ? ' selected' : ''}>${s}</option>`).join('')}</select>`
                    : '';
                const buyerTag = isSoldTab && order.buyer
                    ? `<span style="font-size:.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--accent2)">@${order.buyer}</span>`
                    : '';
                const itemsHtml = (order.items || []).map(item => {
                    let img = 'assets/placeholder.png';
                    try { const a = JSON.parse(item.images || '[]'); if (a[0]) img = a[0]; } catch (e) { }
                    return `<div class="ord-item"><img src="./${img}" class="ord-img" alt=""><div style="flex:1;min-width:0"><p style="font-size:.82rem;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${item.title || ''}</p><p style="font-size:.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);margin-top:2px">${(item.type || '').replace('_', ' ')}</p></div><div style="text-align:right;flex-shrink:0"><p class="mono" style="font-size:.8rem;font-weight:700;color:var(--text)">×${item.quantity || 0}</p><p class="mono" style="font-size:.72rem;color:var(--muted);margin-top:2px">${parseFloat(item.price_at_purchase || 0).toFixed(2)} G</p></div></div>`;
                }).join('');
                const footer = (showReveal || canEdit)
                    ? `<div class="ord-footer">${showReveal ? `<button class="btn-reveal" onclick="App.viewOrderDetails(${order.id})"><svg style="width:14px;height:14px" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>Reveal Codes</button>` : ''}${statusSelect}</div>`
                    : '';
                container.innerHTML += `<div class="ord-card"><div class="ord-header"><div style="display:flex;flex-direction:column;gap:3px"><p class="mono" style="font-size:.7rem;font-weight:700;color:var(--text)">Order #${order.id}</p><p class="mono" style="font-size:.65rem;color:var(--muted)">${safeTx}</p>${buyerTag}</div><div style="display:flex;align-items:center;gap:8px">${getStatusBadge(order.status)}</div></div><div class="ord-body">${itemsHtml}</div>${footer}</div>`;
            });
        } else {
            container.classList.add('hidden');
            empty.classList.remove('hidden');
        }
    } catch (e) {
        console.log(e);
        container.innerHTML = `<div style="text-align:center;color:var(--danger);padding:32px;font-size:.82rem;font-weight:700">Failed to load orders.</div>`;
    }
};
App.updateStatus = async (id, status) => {
    try {
        const res = await App.post('./api/orders/update-status.php', { id, status });
        if (!res.status) notyf.error(res.message || 'Update failed');
        else notyf.success('Status updated');
    } catch { notyf.error('Update failed'); }
};
App.viewOrderDetails = async (oid) => {
    notyf.success('Decrypting codes...');
    try {
        const res = await App.post('./api/orders/detail.php', { id: oid });
        if (!res.status) { notyf.error(res.message); return; }
        if (!res.gift_cards || !res.gift_cards.length) { notyf.error('No digital codes found.'); return; }
        document.getElementById('reveal-order-id').textContent = '#' + oid;
        document.getElementById('reveal-list').innerHTML = res.gift_cards.map(item => `
            <div style="background:var(--surface2);border-radius:10px;padding:14px 16px;border:1px solid var(--border)">
                <p style="font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);margin-bottom:8px">${item.product}</p>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
                    <p class="mono" style="font-size:.88rem;font-weight:700;color:var(--accent);letter-spacing:.05em;word-break:break-all">${item.code}</p>
                    <button onclick="navigator.clipboard.writeText('${item.code}').then(()=>notyf.success('Copied!'))" style="display:inline-flex;align-items:center;gap:5px;background:rgba(0,229,195,.1);color:var(--accent);border:1px solid rgba(0,229,195,.25);border-radius:7px;padding:5px 10px;font-size:.65rem;font-weight:700;cursor:pointer;letter-spacing:.06em;flex-shrink:0"><svg style="width:12px;height:12px" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>COPY</button>
                </div>
                ${item.pin ? `<div style="margin-top:8px;padding-top:8px;border-top:1px solid var(--border);display:flex;align-items:center;gap:8px"><span style="font-size:.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted)">PIN</span><span class="mono" style="font-size:.82rem;color:var(--text)">${item.pin}</span></div>` : ''}
            </div>
        `).join('');
        document.getElementById('reveal-modal').classList.remove('hidden');
    } catch (e) { notyf.error('Decryption failed'); }
};
document.addEventListener('DOMContentLoaded', () => {
    if (App.state.token) App.fetchOrders();
    const modal = document.getElementById('reveal-modal');
    if (modal) modal.addEventListener('click', function (e) { if (e.target === this) this.classList.add('hidden'); });
});
function getStatusBadge(status) {
    const icons = {
        pending: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>`,
        processing: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>`,
        shipped: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/>`,
        delivered: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>`,
        completed: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>`,
        failed: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>`,
        refunded: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>`
    };
    const icon = icons[status] || icons.pending;
    return `<span class="sh-badge ${status}"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">${icon}</svg>${status}</span>`;
}