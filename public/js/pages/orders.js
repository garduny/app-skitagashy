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
    container.innerHTML = `<div class="loader"><div class="spin"></div><div class="text-[.68rem] tracking-[.2em] uppercase font-bold text-[var(--muted)]">Loading Orders</div></div>`;
    try {
        const res = await App.post('./api/orders/history.php', { page: 1, limit: 50, tab: App.state.ordersTab });
        container.innerHTML = '';
        if (res.is_seller) document.getElementById('tab-sold').classList.remove('hidden');
        updateMetrics(res.data || []);
        if (res.status && res.data && res.data.length) {
            res.data.forEach(order => {
                const isSoldTab = App.state.ordersTab === 'sold';
                const canEdit = !!(res.can_edit && isSoldTab);
                const safeTx = order.tx_signature ? order.tx_signature.substring(0, 16) + '…' : '—';
                const hasDigital = (order.items || []).some(i => ['digital', 'gift_card', 'mystery_box'].includes((i.type || '').toLowerCase()));
                const showReveal = !isSoldTab && order.status === 'completed' && hasDigital;
                const statusSelect = canEdit ? `<select onchange="App.updateStatus(${order.id},this.value)" class="status-select">${['pending', 'processing', 'shipped', 'delivered', 'completed', 'failed', 'refunded'].map(s => `<option value="${s}"${s === order.status ? ' selected' : ''}>${s}</option>`).join('')}</select>` : '';
                const buyerTag = isSoldTab && order.buyer ? `<span style="font-size:.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--accent2)">@${escapeHtml(order.buyer)}</span>` : '';
                const usdTag = order.total_usd != null ? `<span class="mono" style="font-size:.68rem;color:var(--muted)">$${Number(order.total_usd || 0).toFixed(2)}</span>` : '';
                const qtyTag = order.total_qty != null ? `<span class="mono" style="font-size:.68rem;color:var(--muted)">${Number(order.total_qty || 0)} items</span>` : '';
                const updatedTag = order.updated_at ? `<span style="font-size:.62rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted)">Updated ${formatDate(order.updated_at)}</span>` : '';
                const itemsHtml = (order.items || []).map(item => {
                    const img = item.image || 'public/img/placeholder.png';
                    const attrs = renderAttrs(item.attributes || {});
                    const optionTag = item.option_id ? `<span style="font-size:.58rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--accent2)">Option #${item.option_id}</span>` : '';
                    return `<div class="ord-item"><img src="./${img}" class="ord-img" alt=""><div style="flex:1;min-width:0"><p style="font-size:.82rem;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${escapeHtml(item.title || '')}</p><div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-top:2px"><p style="font-size:.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted)">${escapeHtml((item.type || '').replace('_', ' '))}</p>${optionTag}</div>${attrs}</div><div style="text-align:right;flex-shrink:0"><p class="mono" style="font-size:.8rem;font-weight:700;color:var(--text)">×${Number(item.quantity || 0)}</p><p class="mono" style="font-size:.72rem;color:var(--muted);margin-top:2px">${Number(item.price_at_purchase || 0).toFixed(2)} G</p></div></div>`;
                }).join('');
                const footer = (showReveal || canEdit) ? `<div class="ord-foot"><div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">${qtyTag}${usdTag}</div><div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">${showReveal ? `<button class="btn-reveal" onclick="App.viewOrderDetails(${order.id})"><svg style="width:14px;height:14px" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>Reveal Codes</button>` : ''}${statusSelect}</div></div>` : `<div class="ord-foot"><div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">${qtyTag}${usdTag}${updatedTag}</div></div>`;
                container.innerHTML += `<div class="ord-card"><div class="ord-head"><div style="display:flex;flex-direction:column;gap:4px"><div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap"><p class="mono" style="font-size:.72rem;font-weight:700;color:var(--text)">Order #${order.id}</p>${buyerTag}</div><p class="mono" style="font-size:.65rem;color:var(--muted)">${safeTx}</p><div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">${usdTag}${qtyTag}${updatedTag}</div></div><div style="display:flex;align-items:center;gap:8px">${getStatusBadge(order.status)}</div></div><div class="ord-items">${itemsHtml}</div>${footer}</div>`;
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
        else {
            notyf.success('Status updated');
            App.fetchOrders();
        }
    } catch (e) {
        notyf.error('Update failed');
    }
};
App.viewOrderDetails = async (oid) => {
    notyf.success('Decrypting codes...');
    try {
        const res = await App.post('./api/orders/detail.php', { id: oid });
        if (!res.status) {
            notyf.error(res.message || 'Failed');
            return;
        }
        const list = (res.digital_items && res.digital_items.length ? res.digital_items : (res.gift_cards || []));
        if (!list.length) {
            notyf.error(res.locked ? 'Order not completed yet.' : 'No digital codes found.');
            return;
        }
        document.getElementById('reveal-order-id').textContent = '#' + oid;
        document.getElementById('reveal-list').innerHTML = list.map(item => `
<div style="background:var(--surface2);border-radius:12px;padding:14px 16px;border:1px solid var(--border)">
<p style="font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);margin-bottom:8px">${escapeHtml(item.product || 'Digital Item')}</p>
<div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
<p class="mono" style="font-size:.88rem;font-weight:700;color:var(--accent);letter-spacing:.05em;word-break:break-all">${escapeHtml(item.code || '')}</p>
<button onclick="navigator.clipboard.writeText('${jsEsc(item.code || '')}').then(()=>notyf.success('Copied!'))" style="display:inline-flex;align-items:center;gap:5px;background:rgba(0,229,195,.1);color:var(--accent);border:1px solid rgba(0,229,195,.25);border-radius:7px;padding:5px 10px;font-size:.65rem;font-weight:700;cursor:pointer;letter-spacing:.06em;flex-shrink:0"><svg style="width:12px;height:12px" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>COPY</button>
</div>
${item.pin ? `<div style="margin-top:8px;padding-top:8px;border-top:1px solid var(--border);display:flex;align-items:center;gap:8px;flex-wrap:wrap"><span style="font-size:.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted)">PIN</span><span class="mono" style="font-size:.82rem;color:var(--text)">${escapeHtml(item.pin)}</span></div>` : ''}
${item.expiry_date ? `<div style="margin-top:8px;font-size:.68rem;color:var(--muted)">Expiry: ${escapeHtml(item.expiry_date)}</div>` : ''}
</div>
`).join('');
        document.getElementById('reveal-modal').classList.remove('hidden');
    } catch (e) {
        console.log(e);
        notyf.error('Decryption failed');
    }
};
document.addEventListener('DOMContentLoaded', () => {
    if (App.state.token) App.fetchOrders();
    const modal = document.getElementById('reveal-modal');
    if (modal) modal.addEventListener('click', function (e) { if (e.target === this) this.classList.add('hidden'); });
});
function renderAttrs(attrs) {
    if (!attrs || typeof attrs !== 'object' || Array.isArray(attrs) || !Object.keys(attrs).length) return '';
    return `<div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:6px">${Object.entries(attrs).map(([k, v]) => `<span style="display:inline-flex;align-items:center;gap:4px;background:var(--surface2);border:1px solid var(--border);border-radius:999px;padding:3px 8px;font-size:.58rem;font-weight:700;color:var(--muted);letter-spacing:.06em;text-transform:uppercase"><span>${escapeHtml(String(k).replace(/_/g, ' '))}</span><span style="color:var(--text)">${escapeHtml(String(v))}</span></span>`).join('')}</div>`;
}
function updateMetrics(orders) {
    const totalOrders = orders.length;
    const spent = orders.reduce((a, o) => a + Number(o.total_gashy || 0), 0);
    const completed = orders.filter(o => o.status === 'completed').length;
    const pending = orders.filter(o => ['pending', 'processing', 'shipped'].includes(o.status)).length;
    const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
    set('m-orders', String(totalOrders));
    set('m-spent', spent.toFixed(2) + ' G');
    set('m-completed', String(completed));
    set('m-pending', String(pending));
}
function formatDate(v) {
    try {
        const d = new Date(v.replace(' ', 'T'));
        if (isNaN(d.getTime())) return v;
        return d.toLocaleString([], { month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit' });
    } catch (e) {
        return v;
    }
}
function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));
}
function jsEsc(s) {
    return String(s).replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/\n/g, '\\n').replace(/\r/g, '');
}
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
    return `<span class="badge ${status}"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">${icon}</svg>${status}</span>`;
}