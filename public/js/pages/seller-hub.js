function getActionButtons(p) {
    const titleEsc = p.title.replace(/'/g, "\\'")
    let extra = ''
    if (p.type === 'digital' || p.type === 'gift_card') {
        extra = `<button onclick="openInvModal(${p.id},'${titleEsc}')" class="btn-icon inv" title="Manage Codes"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4"/></svg></button>`
    } else if (p.type === 'mystery_box') {
        extra = `<button onclick="openMysteryModal(${p.id},'${titleEsc}')" class="btn-icon mystery" title="Loot Table"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12v8a2 2 0 01-2 2H6a2 2 0 01-2-2v-8m16 0H4m16 0a2 2 0 00-2-2h-3m-6 0H6a2 2 0 00-2 2"/></svg></button>`
    }
    return `${extra}<button onclick="editProduct(${p.id})" class="btn-icon edit" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button><button onclick="deleteProduct(${p.id})" class="btn-icon del" title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>`
}
document.addEventListener('DOMContentLoaded', async () => {
    if (!App.checkAuth()) return
    initModalEvents()
    await loadHub()
})
let myProducts = []
let gashyRate = 0
let _invProductId = null
let _invProductTitle = ''
let _invOptionId = 0
let _mysteryProductId = null
let _mysterySellerProds = []
function initModalEvents() {
    ['product-modal', 'inv-modal', 'mystery-modal'].forEach(id => {
        const el = document.getElementById(id)
        if (!el) return
        el.addEventListener('click', function (e) {
            if (e.target === this) this.classList.add('hidden')
        })
    })
    const invOption = document.getElementById('inv-option')
    if (invOption) {
        invOption.addEventListener('change', async function () {
            _invOptionId = parseInt(this.value || 0)
            await loadInvCodes()
        })
    }
}
function toggleTab(t) {
    ['products', 'withdrawals'].forEach(x => {
        document.getElementById('view-' + x).classList.add('hidden')
        document.getElementById('tab-' + x).classList.remove('active')
    })
    document.getElementById('view-' + t).classList.remove('hidden')
    document.getElementById('tab-' + t).classList.add('active')
}
function closeProductModal() { document.getElementById('product-modal').classList.add('hidden') }
function closeInvModal() { document.getElementById('inv-modal').classList.add('hidden') }
function closeMysteryModal() { document.getElementById('mystery-modal').classList.add('hidden') }
async function loadHub() {
    try {
        const res = await App.post('./api/seller/dashboard.php', {})
        document.getElementById('hub-loader').classList.add('hidden')
        if (res.status) {
            document.getElementById('hub-content').classList.remove('hidden')
            document.getElementById('stat-earnings').innerText = parseFloat(res.stats.earnings).toFixed(2)
            document.getElementById('stat-available').innerText = parseFloat(res.stats.available).toFixed(2)
            document.getElementById('stat-sales').innerText = res.stats.total_units || 0
            document.getElementById('stat-products').innerText = res.stats.products
            document.getElementById('stat-rating').innerText = res.stats.rating
            if (document.getElementById('stat-fee')) document.getElementById('stat-fee').innerText = `After ${res.stats.fee_rate} Platform Fee`
            myProducts = res.products || []
            gashyRate = parseFloat(res.rate || 0)
            renderTable()
            renderSales(res.sales)
            renderWithdrawals(res.withdrawals)
        } else window.location.href = 'seller.php'
    } catch (e) { console.error(e) }
}
function renderTable() {
    const list = document.getElementById('product-list')
    if (!myProducts || myProducts.length === 0) {
        list.innerHTML = `<tr><td colspan="5" style="padding:40px;text-align:center;color:var(--muted);font-size:.8rem;letter-spacing:.08em;text-transform:uppercase">No products listed yet</td></tr>`
        return
    }
    list.innerHTML = myProducts.map(p => {
        let img = 'assets/placeholder.png'
        try {
            const parsed = JSON.parse(p.images || '[]')
            if (parsed[0]) img = './' + parsed[0].replace(/^\/+/, '')
        } catch (e) { }
        const statusClass = p.status === 'active' ? 'active' : 'inactive'
        const usd = parseFloat(p.price_usd || 0)
        const gashy = gashyRate > 0 ? usd / gashyRate : 0
        return `<tr><td><div style="display:flex;align-items:center;gap:10px"><img src="${img}" class="prod-img"><span class="prod-title">${p.title}</span></div></td><td><div style="display:flex;flex-direction:column"><span class="mono" style="font-size:.8rem;font-weight:700">$${usd.toFixed(2)}</span><span class="mono" style="font-size:.7rem;color:var(--muted)">${gashy.toFixed(2)} G</span></div></td><td><span class="mono" style="font-size:.8rem">${p.stock}</span></td><td><span class="sh-badge ${statusClass}">${p.status}</span></td><td style="text-align:right"><div style="display:inline-flex;align-items:center;gap:2px">${getActionButtons(p)}</div></td></tr>`
    }).join('')
}
function renderSales(sales) {
    const list = document.getElementById('sales-list')
    if (!sales || sales.length === 0) {
        list.innerHTML = `<div style="padding:40px;text-align:center;color:var(--muted);font-size:.75rem;letter-spacing:.1em;text-transform:uppercase">No sales yet</div>`
        return
    }
    list.innerHTML = sales.map(s => `<div class="sale-item"><div><div style="font-weight:700;font-size:.82rem;color:var(--text);max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${s.title}</div><div style="font-size:.7rem;color:var(--muted);margin-top:2px">@${s.accountname}</div></div><div style="text-align:right"><div class="mono" style="font-weight:700;font-size:.82rem;color:#00e5c3">+${parseFloat((s.price_at_purchase || 0) * (s.quantity || 0)).toFixed(2)} G</div><div style="font-size:.65rem;color:var(--muted);margin-top:2px">${new Date(s.created_at).toLocaleDateString()}</div></div></div>`).join('')
}
function renderWithdrawals(rows) {
    const list = document.getElementById('withdrawal-list')
    if (!rows || rows.length === 0) {
        list.innerHTML = `<tr><td colspan="4" style="padding:40px;text-align:center;color:var(--muted);font-size:.8rem;letter-spacing:.08em;text-transform:uppercase">No withdrawals yet</td></tr>`
        return
    }
    list.innerHTML = rows.map(w => {
        let sc = 'pending'
        if (w.status === 'approved') sc = 'approved'
        if (w.status === 'rejected') sc = 'rejected'
        return `<tr><td class="mono" style="font-size:.75rem;color:var(--muted)">#${w.id}</td><td class="mono" style="font-weight:700">${parseFloat(w.amount).toFixed(2)} G</td><td><span class="sh-badge ${sc}">${w.status}</span></td><td style="text-align:right;font-size:.78rem;color:var(--muted)">${new Date(w.created_at).toLocaleDateString()}</td></tr>`
    }).join('')
}
function openProductModal(isEdit = false) {
    document.getElementById('product-modal').classList.remove('hidden')
    if (!isEdit) {
        document.getElementById('modal-title').innerText = 'Add Product'
        document.getElementById('product-form').reset()
        document.getElementById('prod-id').value = '0'
    }
}
function editProduct(id) {
    const p = myProducts.find(x => x.id == id)
    if (!p) return
    document.getElementById('modal-title').innerText = 'Edit Product'
    document.getElementById('prod-id').value = p.id
    document.getElementById('prod-title').value = p.title
    document.getElementById('prod-price').value = p.price_usd
    document.getElementById('prod-stock').value = p.stock
    document.getElementById('prod-type').value = p.type
    document.getElementById('prod-cat').value = p.category_id || 1
    document.getElementById('prod-desc').value = p.description
    openProductModal(true)
}
async function saveProduct() {
    const fd = new FormData()
    fd.append('action', 'save')
    fd.append('id', document.getElementById('prod-id').value)
    fd.append('title', document.getElementById('prod-title').value)
    fd.append('price', document.getElementById('prod-price').value)
    fd.append('stock', document.getElementById('prod-stock').value)
    fd.append('type', document.getElementById('prod-type').value)
    fd.append('category_id', document.getElementById('prod-cat').value)
    fd.append('description', document.getElementById('prod-desc').value)
    const file = document.getElementById('prod-image-file')?.files?.[0]
    if (file) fd.append('image', file)
    const token = localStorage.getItem('gashy_token')
    if (token) fd.append('token', token)
    const res = await fetch('./api/seller/save.php', { method: 'POST', body: fd })
    const j = await res.json()
    if (j.status) {
        notyf.success(j.message)
        closeProductModal()
        loadHub()
    } else notyf.error(j.message)
}
async function deleteProduct(id) {
    if (!confirm('Remove this product?')) return
    const res = await App.post('./api/seller/save.php', { action: 'delete', id })
    if (res.status) {
        notyf.success('Product deleted')
        loadHub()
    } else notyf.error(res.message)
}
async function requestWithdraw() {
    const val = prompt('Enter amount to withdraw:')
    const amount = parseFloat(val)
    if (!amount || amount <= 0) {
        notyf.error('Invalid amount')
        return
    }
    notyf.success('Processing...')
    const res = await App.post('./api/seller/withdraw.php', { amount })
    if (res.status) {
        notyf.success(res.message)
        loadHub()
    } else notyf.error(res.message)
}
async function openInvModal(productId, title) {
    _invProductId = productId
    _invProductTitle = title
    _invOptionId = 0
    document.getElementById('inv-modal-title').textContent = title
    document.getElementById('inv-codes-input').value = ''
    document.getElementById('inv-modal').classList.remove('hidden')
    await loadInvOptions()
    await loadInvCodes()
}
async function loadInvOptions() {
    const res = await App.post('./api/seller/options.php', { product_id: _invProductId })
    if (!res.status) return
    const options = res.options || []
    renderInvOptions(options)
    const sel = document.getElementById('inv-option')
    if (options.length === 0) {
        _invOptionId = 0
        sel.innerHTML = `<option value="0">Default</option>`
        sel.value = '0'
        return
    }
    if (!options.some(o => parseInt(o.id) === parseInt(_invOptionId))) _invOptionId = parseInt(options[0].id)
    sel.innerHTML = options.map(o => {
        const usd = parseFloat(o.price_usd || 0)
        const gashy = gashyRate > 0 ? usd / gashyRate : 0
        return `<option value="${o.id}">${o.name} - $${usd.toFixed(2)} / ${gashy.toFixed(2)} G</option>`
    }).join('')
    sel.value = String(_invOptionId)
}
function renderInvOptions(options) {
    const box = document.getElementById('inv-options-grid')
    if (!box) return
    if (!options || !options.length) {
        box.innerHTML = `<div style="padding:20px;color:var(--muted);font-size:.75rem;text-transform:uppercase">No options yet</div>`
        return
    }
    box.innerHTML = options.map(o => {
        const usd = parseFloat(o.price_usd || 0)
        const g = gashyRate > 0 ? usd / gashyRate : 0
        return `<div class="opt-card"><div>${o.name}</div><div>$${usd.toFixed(2)} / ${g.toFixed(2)} G</div><div class="flex gap-2"><button onclick="invEditOption(${o.id},'${o.name.replace(/'/g, "\\'")}',${usd})">Edit</button><button onclick="invDeleteOption(${o.id})">Delete</button></div></div>`
    }).join('')
}
async function invAddOptionModal() {
    const name = prompt('Option name')
    if (!name) return
    const price = prompt('Price USD')
    if (!price) return
    const res = await App.post('./api/seller/options.php', { action: 'add', product_id: _invProductId, name, price_usd: price })
    if (res.status) {
        await loadInvOptions()
        await loadInvCodes()
    } else notyf.error(res.message)
}
async function invEditOption(id, name, price) {
    const n = prompt('Edit name', name)
    if (!n) return
    const p = prompt('Edit price', price)
    if (!p) return
    const res = await App.post('./api/seller/options.php', { action: 'edit', product_id: _invProductId, option_id: id, name: n, price_usd: p })
    if (res.status) {
        await loadInvOptions()
        await loadInvCodes()
    } else notyf.error(res.message)
}
async function invDeleteOption(id) {
    if (!confirm('Delete option?')) return
    const res = await App.post('./api/seller/options.php', { action: 'delete', product_id: _invProductId, option_id: id })
    if (res.status) {
        await loadInvOptions()
        await loadInvCodes()
    } else notyf.error(res.message)
}
async function loadInvCodes() {
    document.getElementById('inv-codes-list').innerHTML = `<div style="padding:24px;text-align:center;color:var(--muted);font-size:.75rem">Loading...</div>`
    const res = await App.post('./api/seller/inventory.php', { product_id: _invProductId, option_id: _invOptionId, action: 'list' })
    if (!res.status) {
        notyf.error(res.message || 'Failed to load')
        return
    }
    if (typeof res.selected_option !== 'undefined') _invOptionId = parseInt(res.selected_option || 0)
    const sel = document.getElementById('inv-option')
    if (sel) sel.value = String(_invOptionId)
    const codes = res.codes || []
    const total = codes.length
    const sold = codes.filter(c => parseInt(c.is_sold) === 1).length
    document.getElementById('inv-stat-total').textContent = total
    document.getElementById('inv-stat-available').textContent = total - sold
    document.getElementById('inv-stat-sold').textContent = sold
    document.getElementById('inv-codes-count').textContent = total + ' entries'
    if (total === 0) {
        document.getElementById('inv-codes-list').innerHTML = `<div style="padding:28px;text-align:center;color:var(--muted);font-size:.75rem;letter-spacing:.08em;text-transform:uppercase">No codes yet</div>`
        return
    }
    document.getElementById('inv-codes-list').innerHTML = codes.map(c => `<div class="code-row"><div style="display:flex;align-items:center;gap:10px"><span style="font-family:'Space Mono',monospace;font-size:.75rem;color:var(--text)">****-****-${c.code_tail}</span>${c.has_pin ? `<span class="sh-badge" style="color:var(--accent2);background:rgba(124,109,255,.08);border-color:var(--accent2)">PIN</span>` : ''}</div><div style="display:flex;align-items:center;gap:10px"><span class="sh-badge ${c.is_sold ? 'inactive' : 'active'}">${c.is_sold ? 'Sold' : 'Available'}</span>${c.is_sold ? `` : `<button onclick="invDeleteCode(${c.id})" class="btn-icon del"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>`}</div></div>`).join('')
}
async function invAddCodes() {
    const raw = document.getElementById('inv-codes-input').value.trim()
    if (!raw) {
        notyf.error('Enter at least one code')
        return
    }
    const opt = parseInt(document.getElementById('inv-option')?.value || 0)
    _invOptionId = opt
    const res = await App.post('./api/seller/inventory.php', { product_id: _invProductId, action: 'add', codes: raw, option_id: opt })
    if (res.status) {
        notyf.success(res.message || 'Codes imported')
        document.getElementById('inv-codes-input').value = ''
        loadHub()
        loadInvCodes()
    } else notyf.error(res.message || 'Failed')
}
async function invDeleteCode(cid) {
    if (!confirm('Delete this code?')) return
    const res = await App.post('./api/seller/inventory.php', { product_id: _invProductId, option_id: _invOptionId, action: 'delete', code_id: cid })
    if (res.status) {
        notyf.success('Code removed')
        loadHub()
        loadInvCodes()
    } else notyf.error(res.message || 'Failed')
}
async function openMysteryModal(productId, title) {
    _mysteryProductId = productId
    document.getElementById('mystery-modal-title').textContent = title
    document.getElementById('mystery-modal').classList.remove('hidden')
    await loadMysteryLoot()
}
async function loadMysteryLoot() {
    document.getElementById('mystery-loot-list').innerHTML = `<div style="padding:24px;text-align:center;color:var(--muted);font-size:.75rem">Loading...</div>`
    const res = await App.post('./api/seller/mystery.php', { box_id: _mysteryProductId, action: 'list' })
    if (!res.status) {
        notyf.error(res.message || 'Failed')
        return
    }
    _mysterySellerProds = res.products || []
    const sel = document.getElementById('mystery-reward-product')
    sel.innerHTML = `<option value="">Tokens (GASHY)</option>` + _mysterySellerProds.map(p => `<option value="${p.id}">${p.title}</option>`).join('')
    const loot = res.loot || []
    const totalProb = loot.reduce((s, l) => s + parseFloat(l.probability), 0)
    document.getElementById('mystery-total-prob').textContent = totalProb.toFixed(2) + '% total'
    if (loot.length === 0) {
        document.getElementById('mystery-loot-list').innerHTML = `<div style="padding:28px;text-align:center;color:var(--muted);font-size:.75rem;letter-spacing:.08em;text-transform:uppercase">No loot entries yet</div>`
        return
    }
    document.getElementById('mystery-loot-list').innerHTML = loot.map(l => `<div class="loot-row"><span style="font-size:.8rem;font-weight:700;color:var(--text)">${l.reward_product_id ? '📦 ' + (l.title || 'Product') : '🪙 Tokens'}</span><span style="font-family:'Space Mono',monospace;font-size:.75rem">${parseFloat(l.reward_amount).toFixed(3)}</span><span class="sh-badge">${l.rarity}</span><div><div style="font-family:'Space Mono',monospace;font-size:.72rem;margin-bottom:4px">${l.probability}%</div><div class="prob-bar-wrap"><div class="prob-bar-fill" style="width:${Math.min(l.probability, 100)}%"></div></div></div><button onclick="mysteryDeleteLoot(${l.id})" class="btn-icon del"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></div>`).join('')
}
async function mysteryAddLoot() {
    const payload = { box_id: _mysteryProductId, action: 'add', reward_product_id: document.getElementById('mystery-reward-product').value || null, reward_amount: parseFloat(document.getElementById('mystery-reward-amount').value) || 0, rarity: document.getElementById('mystery-rarity').value, probability: parseFloat(document.getElementById('mystery-probability').value) || 0 }
    if (!payload.probability) {
        notyf.error('Enter a probability')
        return
    }
    const res = await App.post('./api/seller/mystery.php', payload)
    if (res.status) {
        notyf.success('Loot added')
        loadMysteryLoot()
    } else notyf.error(res.message || 'Failed')
}
async function mysteryDeleteLoot(lid) {
    if (!confirm('Remove this loot entry?')) return
    const res = await App.post('./api/seller/mystery.php', { box_id: _mysteryProductId, action: 'delete', loot_id: lid })
    if (res.status) {
        notyf.success('Removed')
        loadMysteryLoot()
    } else notyf.error(res.message || 'Failed')
}