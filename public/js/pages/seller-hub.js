let myProducts = []
let gashyRate = 0
let _invProductId = 0
let _invProductTitle = ''
let _invOptionId = 0
let _mysteryProductId = 0
let _mysterySellerProds = []
let _busy = false
let hubDeleteId = 0
document.addEventListener('DOMContentLoaded', async () => {
    if (!App.checkAuth()) return
    initModalEvents()
    hubTypeUI()
    await loadHub()
})
function esc(v) { return String(v || '').replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m])) }
function num(v, d = 2) { return parseFloat(v || 0).toFixed(d) }
function usdToG(v) { return gashyRate > 0 ? (parseFloat(v || 0) / gashyRate) : 0 }
function productImage(p) {
    let img = 'public/img/placeholder.png'
    try {
        const a = JSON.parse(p.images || '[]')
        if (a[0]) img = './' + String(a[0]).replace(/^\/+/, '')
    } catch (e) { }
    return img
}
function typeBadge(type) {
    if (type === 'digital') return '<span class="badge digital">Digital</span>'
    if (type === 'gift_card') return '<span class="badge gift">Gift Card</span>'
    if (type === 'mystery_box') return '<span class="badge mystery">Mystery Box</span>'
    if (type === 'physical') return '<span class="badge physical">Physical</span>'
    return '<span class="badge">' + esc(type || 'item') + '</span>'
}
function closeProductModal() { document.getElementById('product-modal').classList.add('hidden') }
function closeInvModal() { document.getElementById('inv-modal').classList.add('hidden') }
function closeMysteryModal() { document.getElementById('mystery-modal').classList.add('hidden') }
function closeWithdrawModal() { document.getElementById('withdraw-modal').classList.add('hidden') }
function closeDeleteModal() { hubDeleteId = 0; document.getElementById('delete-modal').classList.add('hidden') }
function openWithdrawModal() {
    document.getElementById('withdraw-available').innerText = document.getElementById('stat-available')?.innerText || '0.000'
    document.getElementById('withdraw-amount').value = ''
    document.getElementById('withdraw-modal').classList.remove('hidden')
}
function askDeleteProduct(id) {
    hubDeleteId = parseInt(id || 0)
    document.getElementById('delete-modal').classList.remove('hidden')
}
function initModalEvents() {
    ['product-modal', 'inv-modal', 'mystery-modal', 'withdraw-modal', 'delete-modal'].forEach(id => {
        const el = document.getElementById(id)
        if (!el) return
        el.addEventListener('click', e => { if (e.target === el) el.classList.add('hidden') })
    })
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeProductModal()
            closeInvModal()
            closeMysteryModal()
            closeWithdrawModal()
            closeDeleteModal()
        }
    })
    const inv = document.getElementById('inv-option')
    if (inv) {
        inv.addEventListener('change', async function () {
            _invOptionId = parseInt(this.value || 0)
            await loadInvCodes()
        })
    }
    const search = document.getElementById('product-search')
    if (search) search.addEventListener('input', filterProducts)
    const type = document.getElementById('product-type-filter')
    if (type) type.addEventListener('change', filterProducts)
    const invFilter = document.getElementById('inv-filter')
    if (invFilter) {
        invFilter.addEventListener('input', function () {
            const q = (this.value || '').toLowerCase().trim()
            document.querySelectorAll('#inv-codes-list .code-row').forEach(r => {
                r.style.display = !q || r.innerText.toLowerCase().includes(q) ? '' : 'none'
            })
        })
    }
    const del = document.getElementById('delete-confirm-btn')
    if (del) {
        del.addEventListener('click', async () => {
            if (!hubDeleteId) return
            await deleteProduct(hubDeleteId)
            closeDeleteModal()
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
async function loadHub() {
    try {
        document.getElementById('hub-loader').classList.remove('hidden')
        document.getElementById('hub-content').classList.add('hidden')
        const res = await App.post('./api/seller/dashboard.php', {})
        document.getElementById('hub-loader').classList.add('hidden')
        if (!res.status) {
            console.error(res)
            location.href = 'seller.php'; return 
        }
        document.getElementById('hub-content').classList.remove('hidden')
        myProducts = res.products || []
        gashyRate = parseFloat(res.rate || 0)
        document.getElementById('stat-earnings').innerText = num(res.stats.earnings, 3)
        document.getElementById('stat-available').innerText = num(res.stats.available, 3)
        document.getElementById('stat-sales').innerText = res.stats.total_units || 0
        document.getElementById('stat-products').innerText = res.stats.products || 0
        document.getElementById('stat-rating').innerText = res.stats.rating || '0.0'
        document.getElementById('stat-fee').innerText = 'After ' + (res.stats.fee_rate || '0%') + ' Platform Fee'
        renderTable()
        renderSales(res.sales || [])
        renderWithdrawals(res.withdrawals || [])
    } catch (e) {
        console.error(e)
        notyf.error('Failed to load seller hub')
        document.getElementById('hub-loader').classList.add('hidden')
    }
}
function getActionButtons(p) {
    const t = esc(p.title)
    let extra = `<button type="button" onclick="toggleProductStatus(${p.id})" class="icon-btn green" title="Toggle Status"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></button><button type="button" onclick="duplicateProduct(${p.id})" class="icon-btn purple" title="Duplicate"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg></button>`
    if (p.type === 'digital' || p.type === 'gift_card') {
        extra += `<button type="button" onclick="openInvModal(${p.id},'${t}')" class="icon-btn green" title="Manage Codes"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4"/></svg></button>`
    }
    if (p.type === 'mystery_box') {
        extra += `<button type="button" onclick="openMysteryModal(${p.id},'${t}')" class="icon-btn gold" title="Loot Table"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12v8a2 2 0 01-2 2H6a2 2 0 01-2-2v-8m16 0H4"/></svg></button>`
    }
    extra += `<button type="button" onclick="editProduct(${p.id})" class="icon-btn purple" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button><button type="button" onclick="askDeleteProduct(${p.id})" class="icon-btn red" title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7"/></svg></button>`
    return extra
}
function renderTable() {
    const list = document.getElementById('product-list')
    const mobile = document.getElementById('product-cards-mobile')
    if (!myProducts.length) {
        const empty = `<tr><td colspan="6"><div class="empty"><b>No products yet</b>Create your first seller listing to begin earning.</div></td></tr>`
        const emptyCard = `<div class="empty"><b>No products yet</b>Create your first seller listing to begin earning.</div>`
        list.innerHTML = empty
        if (mobile) mobile.innerHTML = emptyCard
        return
    }
    list.innerHTML = myProducts.map(p => {
        const stock = parseInt(p.stock || 0)
        const stockText = stock > 0 ? `${stock} in stock` : 'Out of stock'
        const stockColor = stock > 0 ? 'color:var(--accent)' : 'color:var(--danger)'
        const cat = esc(p.cat_name || 'Uncategorized')
        const img = productImage(p)
        return `<tr data-title="${esc((p.title || '').toLowerCase())}" data-type="${esc(p.type || '')}"><td data-label="Product"><div class="prod"><img src="${img}" class="thumb" onerror="this.src='public/img/placeholder.png'"><div><div style="font-weight:800">${esc(p.title)}</div><div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:6px">${typeBadge(p.type)}<span class="badge">${cat}</span></div></div></div></td><td data-label="Pricing"><div><div class="mono" style="font-weight:700">$${num(p.price_usd, 2)}</div><div class="mono" style="font-size:.72rem;color:var(--muted)">${num(usdToG(p.price_usd), 3)} G</div></div></td><td data-label="Inventory"><div><div class="mono" style="font-weight:700;${stockColor}">${stock}</div><div style="font-size:.72rem;color:var(--muted)">${stockText}</div></div></td><td data-label="Type">${typeBadge(p.type)}</td><td data-label="Status"><span class="badge ${p.status === 'active' ? 'active' : 'inactive'}">${esc(p.status || 'inactive')}</span></td><td data-label="Actions" style="text-align:right"><div style="display:inline-flex;gap:4px;flex-wrap:wrap">${getActionButtons(p)}</div></td></tr>`
    }).join('')
    if (mobile) {
        mobile.innerHTML = myProducts.map(p => {
            const img = productImage(p)
            const stock = parseInt(p.stock || 0)
            return `<div class="sh-card p-4 product-mobile-card" data-title="${esc((p.title || '').toLowerCase())}" data-type="${esc(p.type || '')}"><div style="display:flex;gap:12px;align-items:flex-start"><img src="${img}" class="thumb" style="width:54px;height:54px" onerror="this.src='public/img/placeholder.png'"><div style="flex:1;min-width:0"><div style="font-weight:800">${esc(p.title)}</div><div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:6px">${typeBadge(p.type)}<span class="badge">${esc(p.cat_name || 'Uncategorized')}</span><span class="badge ${p.status === 'active' ? 'active' : 'inactive'}">${esc(p.status || 'inactive')}</span></div><div style="display:flex;justify-content:space-between;gap:10px;margin-top:10px"><div><div class="mono" style="font-weight:700">$${num(p.price_usd, 2)}</div><div class="mono" style="font-size:.72rem;color:var(--muted)">${num(usdToG(p.price_usd), 3)} G</div></div><div style="text-align:right"><div class="mono" style="font-weight:700;${stock > 0 ? 'color:var(--accent)' : 'color:var(--danger)'}">${stock}</div><div style="font-size:.72rem;color:var(--muted)">Stock</div></div></div><div style="display:flex;justify-content:flex-end;gap:4px;flex-wrap:wrap;margin-top:12px">${getActionButtons(p)}</div></div></div></div>`
        }).join('')
    }
    filterProducts()
}
function filterProducts() {
    const q = (document.getElementById('product-search')?.value || '').toLowerCase().trim()
    const t = document.getElementById('product-type-filter')?.value || ''
    document.querySelectorAll('#product-list tr[data-title]').forEach(r => {
        const title = r.dataset.title || ''
        const type = r.dataset.type || ''
        r.style.display = ((!q || title.includes(q)) && (!t || type === t)) ? '' : 'none'
    })
    document.querySelectorAll('#product-cards-mobile .product-mobile-card').forEach(r => {
        const title = r.dataset.title || ''
        const type = r.dataset.type || ''
        r.style.display = ((!q || title.includes(q)) && (!t || type === t)) ? '' : 'none'
    })
}
function renderSales(rows) {
    const list = document.getElementById('sales-list')
    if (!rows.length) {
        list.innerHTML = `<div class="empty"><b>No sales yet</b>Completed orders will appear here.</div>`
        return
    }
    list.innerHTML = rows.map(s => `<div class="sale-item"><div><div style="font-weight:800">${esc(s.title)}</div><div style="font-size:.72rem;color:var(--muted);margin-top:3px">@${esc(s.accountname || 'Guest')}</div></div><div style="text-align:right"><div class="mono" style="font-weight:700;color:var(--accent)">+${num(s.line_total || ((s.price_at_purchase || 0) * (s.quantity || 0)), 3)} G</div><div style="font-size:.7rem;color:var(--muted);margin-top:3px">${s.created_at ? new Date(s.created_at).toLocaleDateString() : ''}</div></div></div>`).join('')
}
function renderWithdrawals(rows) {
    const list = document.getElementById('withdrawal-list')
    const mobile = document.getElementById('withdrawal-cards-mobile')
    if (!rows.length) {
        const html = `<tr><td colspan="4"><div class="empty"><b>No withdrawals yet</b>Your payout requests will appear here.</div></td></tr>`
        const cards = `<div class="empty"><b>No withdrawals yet</b>Your payout requests will appear here.</div>`
        list.innerHTML = html
        if (mobile) mobile.innerHTML = cards
        return
    }
    list.innerHTML = rows.map(w => `<tr><td data-label="ID" class="mono">#${w.id}</td><td data-label="Amount" class="mono" style="font-weight:700">${num(w.amount, 3)} G</td><td data-label="Status"><span class="badge ${esc(String(w.status || 'pending').toLowerCase())}">${esc(w.status)}</span></td><td data-label="Date" style="text-align:right">${w.created_at ? new Date(w.created_at).toLocaleDateString() : ''}</td></tr>`).join('')
    if (mobile) {
        mobile.innerHTML = rows.map(w => `<div class="sh-card p-4"><div style="display:flex;align-items:center;justify-content:space-between;gap:10px"><div><div class="mono" style="font-weight:700">#${w.id}</div><div class="mono" style="margin-top:5px">${num(w.amount, 3)} G</div></div><div style="text-align:right"><span class="badge ${esc(String(w.status || 'pending').toLowerCase())}">${esc(w.status)}</span><div style="font-size:.72rem;color:var(--muted);margin-top:6px">${w.created_at ? new Date(w.created_at).toLocaleDateString() : ''}</div></div></div></div>`).join('')
    }
}
function openProductModal(edit = false) {
    document.getElementById('product-modal').classList.remove('hidden')
    if (!edit) {
        document.getElementById('product-form').reset()
        document.getElementById('prod-id').value = '0'
        document.getElementById('modal-title').innerText = 'Add Product'
        document.getElementById('prod-preview').src = 'public/img/placeholder.png'
        document.getElementById('prod-attributes').value = ''
        hubTypeUI()
    }
}
function editProduct(id) {
    const p = myProducts.find(x => parseInt(x.id) === parseInt(id))
    if (!p) return
    openProductModal(true)
    document.getElementById('modal-title').innerText = 'Edit Product'
    document.getElementById('prod-id').value = p.id
    document.getElementById('prod-title').value = p.title || ''
    document.getElementById('prod-price').value = p.price_usd || 0
    document.getElementById('prod-stock').value = p.stock || 0
    document.getElementById('prod-type').value = p.type || 'digital'
    document.getElementById('prod-cat').value = p.category_id || 1
    document.getElementById('prod-desc').value = p.description || ''
    document.getElementById('prod-preview').src = productImage(p)
    document.getElementById('prod-attributes').value = p.attributes_json || ''
    hubTypeUI()
}
function hubTypeUI() {
    const t = document.getElementById('prod-type')?.value || 'digital'
    const box = document.getElementById('type-help-box')
    const stock = document.getElementById('prod-stock')
    if (!box || !stock) return
    if (t === 'gift_card') {
        box.innerHTML = 'Gift cards support options, denominations, and secure inventory codes.'
        stock.setAttribute('readonly', 'readonly')
        stock.value = '0'
    } else if (t === 'digital') {
        box.innerHTML = 'Digital products can use redeem codes, serials, or downloadable keys.'
        stock.removeAttribute('readonly')
    } else if (t === 'mystery_box') {
        box.innerHTML = 'Mystery boxes should have balanced loot tables. Total probability must stay under 100%.'
        stock.removeAttribute('readonly')
    } else if (t === 'physical') {
        box.innerHTML = 'Physical items should keep real stock and accurate delivery expectations.'
        stock.removeAttribute('readonly')
    } else {
        box.innerHTML = 'NFT products usually have limited supply and collectible metadata.'
        stock.removeAttribute('readonly')
    }
}
function hubPreviewImage(input) {
    const file = input?.files?.[0]
    if (!file) return
    const reader = new FileReader()
    reader.onload = e => {
        document.getElementById('prod-preview').src = e.target.result
    }
    reader.readAsDataURL(file)
}
function duplicateCurrentProduct() {
    const id = parseInt(document.getElementById('prod-id')?.value || 0)
    if (!id) { notyf.error('Open existing product first'); return }
    duplicateProduct(id)
}
function duplicateProduct(id) {
    const p = myProducts.find(x => parseInt(x.id) === parseInt(id))
    if (!p) return
    openProductModal(true)
    document.getElementById('modal-title').innerText = 'Duplicate Product'
    document.getElementById('prod-id').value = '0'
    document.getElementById('prod-title').value = (p.title || 'Product') + ' Copy'
    document.getElementById('prod-price').value = p.price_usd || 0
    document.getElementById('prod-stock').value = p.stock || 0
    document.getElementById('prod-type').value = p.type || 'digital'
    document.getElementById('prod-cat').value = p.category_id || 1
    document.getElementById('prod-desc').value = p.description || ''
    document.getElementById('prod-preview').src = productImage(p)
    document.getElementById('prod-attributes').value = p.attributes_json || ''
    hubTypeUI()
    notyf.success('Duplicated into new draft')
}
async function saveProduct() {
    if (_busy) return
    _busy = true
    const fd = new FormData()
    fd.append('action', 'save')
    fd.append('id', document.getElementById('prod-id').value)
    fd.append('title', document.getElementById('prod-title').value)
    fd.append('price', document.getElementById('prod-price').value)
    fd.append('stock', document.getElementById('prod-stock').value)
    fd.append('type', document.getElementById('prod-type').value)
    fd.append('category_id', document.getElementById('prod-cat').value)
    fd.append('description', document.getElementById('prod-desc').value)
    fd.append('attributes_json', document.getElementById('prod-attributes')?.value || '')
    const file = document.getElementById('prod-image-file')?.files?.[0]
    if (file) fd.append('image', file)
    const token = localStorage.getItem('gashy_token')
    if (token) fd.append('token', token)
    try {
        const res = await fetch('./api/seller/save.php', { method: 'POST', body: fd })
        const j = await res.json()
        if (j.status) {
            notyf.success(j.message || 'Saved')
            closeProductModal()
            await loadHub()
        } else notyf.error(j.message || 'Failed')
    } catch (e) {
        console.error(e)
        notyf.error('Failed')
    }
    _busy = false
}
async function deleteProduct(id) {
    const r = await App.post('./api/seller/save.php', { action: 'delete', id })
    if (r.status) {
        notyf.success(r.message || 'Deleted')
        await loadHub()
    } else notyf.error(r.message || 'Failed')
}
async function toggleProductStatus(id) {
    const r = await App.post('./api/seller/save.php', { action: 'toggle_status', id })
    if (r.status) {
        notyf.success(r.message || 'Status updated')
        await loadHub()
    } else notyf.error(r.message || 'Failed')
}
async function requestWithdraw() {
    const modal = document.getElementById('withdraw-modal')
    if (modal && !modal.classList.contains('hidden')) {
        const amount = parseFloat(document.getElementById('withdraw-amount').value || 0)
        if (!amount || amount <= 0) { notyf.error('Invalid amount'); return }
        const r = await App.post('./api/seller/withdraw.php', { amount })
        if (r.status) {
            notyf.success(r.message)
            closeWithdrawModal()
            await loadHub()
        } else notyf.error(r.message || 'Failed')
        return
    }
    openWithdrawModal()
}
async function openInvModal(id, title) {
    _invProductId = parseInt(id)
    _invProductTitle = title
    _invOptionId = 0
    document.getElementById('inv-modal').classList.remove('hidden')
    document.getElementById('inv-modal-title').innerText = title
    document.getElementById('inv-codes-input').value = ''
    document.getElementById('inv-filter').value = ''
    await loadInvOptions()
    await loadInvCodes()
}
async function loadInvOptions() {
    const r = await App.post('./api/seller/options.php', { product_id: _invProductId })
    if (!r.status) { notyf.error(r.message || 'Failed'); return }
    const ops = r.options || []
    const sel = document.getElementById('inv-option')
    if (!ops.length) {
        sel.innerHTML = '<option value="0">Default</option>'
        _invOptionId = 0
        document.getElementById('inv-options-grid').innerHTML = `<div class="empty"><b>No options yet</b>Create variants like $10 / $25 / $50.</div>`
        return
    }
    if (!ops.some(o => parseInt(o.id) === parseInt(_invOptionId))) _invOptionId = parseInt(ops[0].id)
    sel.innerHTML = ops.map(o => `<option value="${o.id}">${esc(o.name)} - $${num(o.price_usd)} / ${num(o.price_gashy || usdToG(o.price_usd), 3)} G</option>`).join('')
    sel.value = String(_invOptionId)
    document.getElementById('inv-options-grid').innerHTML = ops.map(o => `<div class="sh-card p-4"><div style="display:flex;align-items:center;justify-content:space-between;gap:10px"><div><div style="font-weight:800">${esc(o.name)}</div><div class="mono" style="font-size:.78rem;color:var(--muted);margin-top:5px">$${num(o.price_usd)} / ${num(o.price_gashy || usdToG(o.price_usd), 3)} G</div><div style="font-size:.72rem;color:var(--muted);margin-top:5px">Stock: ${o.stock || 0}</div></div><div style="display:flex;gap:4px"><button type="button" onclick="invEditOption(${o.id},'${esc(o.name)}',${parseFloat(o.price_usd || 0)})" class="icon-btn purple">✎</button><button type="button" onclick="invDeleteOption(${o.id})" class="icon-btn red">×</button></div></div></div>`).join('')
}
async function invAddOptionModal() {
    const name = window.prompt('Option name')
    if (!name) return
    const price = window.prompt('Price USD')
    if (price === null) return
    const r = await App.post('./api/seller/options.php', { action: 'add', product_id: _invProductId, name, price_usd: price })
    if (r.status) {
        notyf.success(r.message || 'Option added')
        await loadInvOptions()
        await loadInvCodes()
    } else notyf.error(r.message || 'Failed')
}
async function invEditOption(id, name, price) {
    const newName = window.prompt('Option name', name)
    if (!newName) return
    const newPrice = window.prompt('Price USD', price)
    if (newPrice === null) return
    const r = await App.post('./api/seller/options.php', { action: 'edit', product_id: _invProductId, option_id: id, name: newName, price_usd: newPrice })
    if (r.status) {
        notyf.success(r.message || 'Updated')
        await loadInvOptions()
        await loadInvCodes()
    } else notyf.error(r.message || 'Failed')
}
async function invDeleteOption(id) {
    if (!window.confirm('Delete option?')) return
    const r = await App.post('./api/seller/options.php', { action: 'delete', product_id: _invProductId, option_id: id })
    if (r.status) {
        notyf.success(r.message || 'Deleted')
        await loadInvOptions()
        await loadInvCodes()
        await loadHub()
    } else notyf.error(r.message || 'Failed')
}
async function loadInvCodes() {
    const r = await App.post('./api/seller/inventory.php', { product_id: _invProductId, option_id: _invOptionId, action: 'list' })
    if (!r.status) { notyf.error(r.message || 'Failed'); return }
    const codes = r.codes || []
    const sold = codes.filter(x => parseInt(x.is_sold) === 1).length
    document.getElementById('inv-stat-total').innerText = codes.length
    document.getElementById('inv-stat-available').innerText = codes.length - sold
    document.getElementById('inv-stat-sold').innerText = sold
    document.getElementById('inv-codes-count').innerText = codes.length + ' entries'
    document.getElementById('inv-codes-list').innerHTML = !codes.length ? `<div class="empty"><b>No codes yet</b>Add your first inventory batch.</div>` : codes.map(c => `<div class="code-row"><div style="display:flex;align-items:center;gap:8px"><div class="mono">****-****-${esc(c.code_tail)}</div>${c.has_pin ? '<span class="badge gift">PIN</span>' : ''}</div><div style="display:flex;align-items:center;gap:8px">${parseInt(c.is_sold) === 1 ? '<span class="badge inactive">Sold</span>' : `<span class="badge active">Available</span><button type="button" onclick="invDeleteCode(${c.id})" class="icon-btn red">×</button>`}</div></div>`).join('')
}
async function invAddCodes() {
    const raw = document.getElementById('inv-codes-input').value.trim()
    if (!raw) { notyf.error('Enter codes'); return }
    const r = await App.post('./api/seller/inventory.php', { product_id: _invProductId, option_id: _invOptionId, action: 'add', codes: raw })
    if (r.status) {
        notyf.success(r.message || 'Codes added')
        document.getElementById('inv-codes-input').value = ''
        await loadInvCodes()
        await loadInvOptions()
        await loadHub()
    } else notyf.error(r.message || 'Failed')
}
async function invDeleteCode(id) {
    if (!window.confirm('Delete code?')) return
    const r = await App.post('./api/seller/inventory.php', { product_id: _invProductId, option_id: _invOptionId, action: 'delete', code_id: id })
    if (r.status) {
        notyf.success(r.message || 'Deleted')
        await loadInvCodes()
        await loadInvOptions()
        await loadHub()
    } else notyf.error(r.message || 'Failed')
}
async function openMysteryModal(id, title) {
    _mysteryProductId = parseInt(id)
    document.getElementById('mystery-modal').classList.remove('hidden')
    document.getElementById('mystery-modal-title').innerText = title
    await loadMysteryLoot()
}
async function loadMysteryLoot() {
    const r = await App.post('./api/seller/mystery.php', { box_id: _mysteryProductId, action: 'list' })
    if (!r.status) { notyf.error(r.message || 'Failed'); return }
    const loot = r.loot || []
    const prods = r.products || []
    _mysterySellerProds = prods
    document.getElementById('mystery-reward-product').innerHTML = `<option value="">Tokens (GASHY)</option>` + prods.map(p => `<option value="${p.id}">${esc(p.title)}</option>`).join('')
    const total = loot.reduce((s, x) => s + parseFloat(x.probability || 0), 0)
    const probEl = document.getElementById('mystery-total-prob')
    probEl.innerText = total.toFixed(2) + '%'
    probEl.style.color = total > 100 ? 'var(--danger)' : (total > 90 ? 'var(--warn)' : 'var(--muted)')
    document.getElementById('mystery-loot-list').innerHTML = !loot.length ? `<div class="empty"><b>No loot entries</b>Add rewards to activate the box.</div>` : loot.map(l => `<div class="code-row"><div><div style="font-weight:800">${l.reward_product_id ? '📦 ' + esc(l.title || 'Product') : '🪙 Tokens'}</div><div class="mono" style="font-size:.72rem;color:var(--muted);margin-top:4px">${num(l.reward_amount, 3)} / ${num(l.probability, 2)}%</div></div><div style="display:flex;align-items:center;gap:8px"><span class="badge ${esc(l.rarity)}">${esc(l.rarity)}</span><button type="button" onclick="mysteryDeleteLoot(${l.id})" class="icon-btn red">×</button></div></div>`).join('')
}
async function mysteryAddLoot() {
    const payload = { box_id: _mysteryProductId, action: 'add', reward_product_id: document.getElementById('mystery-reward-product').value || null, reward_amount: document.getElementById('mystery-reward-amount').value, rarity: document.getElementById('mystery-rarity').value, probability: document.getElementById('mystery-probability').value }
    if (!parseFloat(payload.probability || 0)) { notyf.error('Enter probability'); return }
    const r = await App.post('./api/seller/mystery.php', payload)
    if (r.status) {
        notyf.success(r.message || 'Added')
        document.getElementById('mystery-reward-amount').value = '0'
        document.getElementById('mystery-probability').value = ''
        await loadMysteryLoot()
    } else notyf.error(r.message || 'Failed')
}
async function mysteryDeleteLoot(id) {
    if (!window.confirm('Delete loot?')) return
    const r = await App.post('./api/seller/mystery.php', { box_id: _mysteryProductId, action: 'delete', loot_id: id })
    if (r.status) {
        notyf.success(r.message || 'Deleted')
        await loadMysteryLoot()
    } else notyf.error(r.message || 'Failed')
}