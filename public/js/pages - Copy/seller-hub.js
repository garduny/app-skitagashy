/* ─── Seller Hub JS ─── */

function getActionButtons(p) {
    const titleEsc = p.title.replace(/'/g, "\\'");
    let extra = '';
    if (p.type === 'digital' || p.type === 'gift_card') {
        extra = `<button onclick="openInvModal(${p.id}, '${titleEsc}')" class="btn-icon inv" title="Manage Codes">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4"/></svg>
        </button>`;
    } else if (p.type === 'mystery_box') {
        extra = `<button onclick="openMysteryModal(${p.id}, '${titleEsc}')" class="btn-icon mystery" title="Loot Table">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12v8a2 2 0 01-2 2H6a2 2 0 01-2-2v-8m16 0H4m16 0a2 2 0 00-2-2h-3m-6 0H6a2 2 0 00-2 2"/></svg>
        </button>`;
    }
    return `
        ${extra}
        <button onclick="editProduct(${p.id})" class="btn-icon edit" title="Edit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </button>
        <button onclick="deleteProduct(${p.id})" class="btn-icon del" title="Delete">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>
    `;
}

document.addEventListener('DOMContentLoaded', async () => {
    if (!App.checkAuth()) return;
    await loadHub();
});

let myProducts = [];

async function loadHub() {
    try {
        const res = await App.post('./api/seller/dashboard.php', {});
        document.getElementById('hub-loader').classList.add('hidden');
        if (res.status) {
            document.getElementById('hub-content').classList.remove('hidden');
            document.getElementById('stat-earnings').innerText = parseFloat(res.stats.earnings).toFixed(2);
            document.getElementById('stat-available').innerText = parseFloat(res.stats.available).toFixed(2);
            document.getElementById('stat-sales').innerText = res.stats.total_units || 0;
            document.getElementById('stat-products').innerText = res.stats.products;
            document.getElementById('stat-rating').innerText = res.stats.rating;
            if (document.getElementById('stat-fee'))
                document.getElementById('stat-fee').innerText = `After ${res.stats.fee_rate} Platform Fee`;
            myProducts = res.products;
            renderTable();
            renderSales(res.sales);
            renderWithdrawals(res.withdrawals);
        } else {
            window.location.href = 'seller.php';
        }
    } catch (e) {
        console.error(e);
    }
}

function renderTable() {
    const list = document.getElementById('product-list');
    if (!myProducts || myProducts.length === 0) {
        list.innerHTML = `<tr><td colspan="5" style="padding:40px;text-align:center;color:var(--muted);font-size:.8rem;letter-spacing:.08em;text-transform:uppercase">No products listed yet</td></tr>`;
        return;
    }
    list.innerHTML = myProducts.map(p => {
        let img = 'assets/placeholder.png';
        try { img = './' + JSON.parse(p.images)[0] || img; } catch (e) { }
        const statusClass = p.status === 'active' ? 'active' : 'inactive';
        return `<tr>
            <td>
                <div style="display:flex;align-items:center;gap:10px">
                    <img src="${img}" class="prod-img" alt="">
                    <span class="prod-title">${p.title}</span>
                </div>
            </td>
            <td><span class="mono" style="font-size:.8rem;font-weight:700">${parseFloat(p.price_gashy).toFixed(2)} G</span></td>
            <td><span class="mono" style="font-size:.8rem">${p.stock}</span></td>
            <td><span class="sh-badge ${statusClass}">${p.status}</span></td>
            <td style="text-align:right">
                <div style="display:inline-flex;align-items:center;gap:2px">${getActionButtons(p)}</div>
            </td>
        </tr>`;
    }).join('');
}

function renderSales(sales) {
    const list = document.getElementById('sales-list');
    if (!sales || sales.length === 0) {
        list.innerHTML = `<div style="padding:40px;text-align:center;color:var(--muted);font-size:.75rem;letter-spacing:.1em;text-transform:uppercase">No sales yet</div>`;
        return;
    }
    list.innerHTML = sales.map(s => `
        <div class="sale-item">
            <div>
                <div style="font-weight:700;font-size:.82rem;color:var(--text);max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${s.title}</div>
                <div style="font-size:.7rem;color:var(--muted);margin-top:2px">@${s.accountname}</div>
            </div>
            <div style="text-align:right">
                <div class="mono" style="font-weight:700;font-size:.82rem;color:#00e5c3">+${parseFloat(s.price_at_purchase * s.quantity).toFixed(2)} G</div>
                <div style="font-size:.65rem;color:var(--muted);margin-top:2px">${new Date(s.created_at).toLocaleDateString()}</div>
            </div>
        </div>
    `).join('');
}

function renderWithdrawals(withdrawals) {
    const list = document.getElementById('withdrawal-list');
    if (!withdrawals || withdrawals.length === 0) {
        list.innerHTML = `<tr><td colspan="4" style="padding:40px;text-align:center;color:var(--muted);font-size:.8rem;letter-spacing:.08em;text-transform:uppercase">No withdrawals yet</td></tr>`;
        return;
    }
    list.innerHTML = withdrawals.map(w => {
        let sc = 'pending';
        if (w.status === 'approved') sc = 'approved';
        if (w.status === 'rejected') sc = 'rejected';
        return `<tr>
            <td class="mono" style="font-size:.75rem;color:var(--muted)">#${w.id}</td>
            <td class="mono" style="font-weight:700">${parseFloat(w.amount).toFixed(2)} G</td>
            <td><span class="sh-badge ${sc}">${w.status}</span></td>
            <td style="text-align:right;font-size:.78rem;color:var(--muted)">${new Date(w.created_at).toLocaleDateString()}</td>
        </tr>`;
    }).join('');
}

/* ─── Modal ─── */
function openProductModal(isEdit = false) {
    document.getElementById('product-modal').classList.remove('hidden');
    if (!isEdit) {
        document.getElementById('modal-title').innerText = 'Add Product';
        document.getElementById('product-form').reset();
        document.getElementById('prod-id').value = '0';
    }
}

function closeProductModal() {
    document.getElementById('product-modal').classList.add('hidden');
}

function editProduct(id) {
    const p = myProducts.find(x => x.id == id);
    if (!p) return;
    document.getElementById('modal-title').innerText = 'Edit Product';
    document.getElementById('prod-id').value = p.id;
    document.getElementById('prod-title').value = p.title;
    document.getElementById('prod-price').value = p.price_gashy;
    document.getElementById('prod-stock').value = p.stock;
    document.getElementById('prod-type').value = p.type;
    document.getElementById('prod-cat').value = p.category_id || 1;
    document.getElementById('prod-desc').value = p.description;
    openProductModal(true);
}

async function saveProduct() {
    const fd = new FormData();
    fd.append('action', 'save');
    fd.append('id', document.getElementById('prod-id').value);
    fd.append('title', document.getElementById('prod-title').value);
    fd.append('price', document.getElementById('prod-price').value);
    fd.append('stock', document.getElementById('prod-stock').value);
    fd.append('type', document.getElementById('prod-type').value);
    fd.append('category_id', document.getElementById('prod-cat').value);
    fd.append('description', document.getElementById('prod-desc').value);
    const file = document.getElementById('prod-image-file')?.files?.[0];
    if (file) fd.append('image', file);
    const token = localStorage.getItem('gashy_token');
    if (token) fd.append('token', token);
    try {
        const res = await fetch('./api/seller/save.php', { method: 'POST', body: fd });
        const j = await res.json();
        if (j.status) {
            notyf.success(j.message);
            closeProductModal();
            loadHub();
        } else notyf.error(j.message);
    } catch (e) {
        console.log(e);
        notyf.error('Save failed');
    }
}

async function deleteProduct(id) {
    if (!confirm('Remove this product?')) return;
    try {
        const res = await App.post('./api/seller/save.php', { action: 'delete', id });
        if (res.status) {
            notyf.success('Product deleted');
            loadHub();
        } else notyf.error(res.message);
    } catch (e) { notyf.error('Delete failed'); }
}

async function requestWithdraw() {
    const val = prompt('Enter amount to withdraw:');
    const amount = parseFloat(val);
    if (!amount || amount <= 0) { notyf.error('Invalid amount'); return; }
    notyf.success('Processing...');
    try {
        const res = await App.post('./api/seller/withdraw.php', { amount });
        if (res.status) {
            notyf.success(res.message);
            loadHub();
        } else notyf.error(res.message);
    } catch (e) { notyf.error('Request failed'); }
}