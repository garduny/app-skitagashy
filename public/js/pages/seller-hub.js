let myProducts = []
let gashyRate = 0
let _invProductId = 0
let _invProductTitle = ''
let _invOptionId = 0
let _mysteryProductId = 0
let _mysterySellerProds = []
let _busy = false

document.addEventListener('DOMContentLoaded', async () => {
    if (!App.checkAuth()) return
    initModalEvents()
    await loadHub()
})

function esc(v) { return String(v || '').replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m])) }
function num(v, d = 2) { return parseFloat(v || 0).toFixed(d) }
function usdToG(v) { return gashyRate > 0 ? (parseFloat(v || 0) / gashyRate) : 0 }
function closeProductModal() { document.getElementById('product-modal').classList.add('hidden') }
function closeInvModal() { document.getElementById('inv-modal').classList.add('hidden') }
function closeMysteryModal() { document.getElementById('mystery-modal').classList.add('hidden') }

function initModalEvents() {
    ['product-modal', 'inv-modal', 'mystery-modal'].forEach(id => {
        const el = document.getElementById(id)
        if (!el) return
        el.addEventListener('click', e => { if (e.target === el) el.classList.add('hidden') })
    })
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeProductModal()
            closeInvModal()
            closeMysteryModal()
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
    if (search) {
        search.addEventListener('input', filterProducts)
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
        const res = await App.post('./api/seller/dashboard.php', {})
        document.getElementById('hub-loader').classList.add('hidden')
        if (!res.status) { location.href = 'seller.php'; return }
        document.getElementById('hub-content').classList.remove('hidden')
        myProducts = res.products || []
        gashyRate = parseFloat(res.rate || 0)
        document.getElementById('stat-earnings').innerText = num(res.stats.earnings)
        document.getElementById('stat-available').innerText = num(res.stats.available)
        document.getElementById('stat-sales').innerText = res.stats.total_units || 0
        document.getElementById('stat-products').innerText = res.stats.products || 0
        document.getElementById('stat-rating').innerText = res.stats.rating || '0.0'
        document.getElementById('stat-fee').innerText = 'After ' + (res.stats.fee_rate || '0%') + ' Platform Fee'
        renderTable()
        renderSales(res.sales || [])
        renderWithdrawals(res.withdrawals || [])
    } catch (e) { console.error(e); notyf.error('Failed to load hub') }
}

function getActionButtons(p) {
    const t = esc(p.title)
    let extra = ''
    if (p.type === 'digital' || p.type === 'gift_card') {
        extra = `<button onclick="openInvModal(${p.id},'${t}')" class="btn-icon inv"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4"/></svg></button>`
    } else if (p.type === 'mystery_box') {
        extra = `<button onclick="openMysteryModal(${p.id},'${t}')" class="btn-icon mystery"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12v8a2 2 0 01-2 2H6a2 2 0 01-2-2v-8m16 0H4"/></svg></button>`
    }
    return `${extra}<button onclick="editProduct(${p.id})" class="btn-icon edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button><button onclick="deleteProduct(${p.id})" class="btn-icon del"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7L5 7"/></svg></button>`
}

function renderTable() {
    const list = document.getElementById('product-list')
    if (!myProducts.length) {
        list.innerHTML = `<tr><td colspan="5" style="padding:40px;text-align:center;color:var(--muted)">No products</td></tr>`
        return
    }
    list.innerHTML = myProducts.map(p => {
        let img = 'assets/placeholder.png'
        try {
            const a = JSON.parse(p.images || '[]')
            if (a[0]) img = './' + String(a[0]).replace(/^\/+/, '')
        } catch (e) { }
        return `<tr data-title="${esc((p.title || '').toLowerCase())}"><td><div style="display:flex;gap:10px;align-items:center"><img src="${img}" class="prod-img"><span class="prod-title">${esc(p.title)}</span></div></td><td><div><div class="mono">$${num(p.price_usd)}</div><div class="mono" style="font-size:.72rem;color:var(--muted)">${num(usdToG(p.price_usd))} G</div></div></td><td class="mono">${p.stock || 0}</td><td><span class="sh-badge ${p.status === 'active' ? 'active' : 'inactive'}">${esc(p.status)}</span></td><td style="text-align:right">${getActionButtons(p)}</td></tr>`
    }).join('')
    filterProducts()
}

function filterProducts() {
    const q = (document.getElementById('product-search').value || '').toLowerCase().trim()
    document.querySelectorAll('#product-list tr[data-title]').forEach(r => {
        r.style.display = !q || r.dataset.title.includes(q) ? '' : 'none'
    })
}

function renderSales(rows) {
    const list = document.getElementById('sales-list')
    if (!rows.length) { list.innerHTML = `<div style="padding:30px;text-align:center;color:var(--muted)">No sales</div>`; return }
    list.innerHTML = rows.map(s => `<div class="sale-item"><div><div style="font-weight:700">${esc(s.title)}</div><div style="font-size:.72rem;color:var(--muted)">@${esc(s.accountname)}</div></div><div class="mono" style="color:#00e5c3">+${num((s.price_at_purchase || 0) * (s.quantity || 0))} G</div></div>`).join('')
}

function renderWithdrawals(rows) {
    const list = document.getElementById('withdrawal-list')
    if (!rows.length) { list.innerHTML = `<tr><td colspan="4" style="padding:30px;text-align:center;color:var(--muted)">No withdrawals</td></tr>`; return }
    list.innerHTML = rows.map(w => `<tr><td>#${w.id}</td><td class="mono">${num(w.amount)} G</td><td><span class="sh-badge ${esc(w.status)}">${esc(w.status)}</span></td><td style="text-align:right">${new Date(w.created_at).toLocaleDateString()}</td></tr>`).join('')
}

function openProductModal(edit = false) {
    document.getElementById('product-modal').classList.remove('hidden')
    if (!edit) {
        document.getElementById('product-form').reset()
        document.getElementById('prod-id').value = '0'
        document.getElementById('modal-title').innerText = 'Add Product'
    }
}

function editProduct(id) {
    const p = myProducts.find(x => x.id == id)
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
}

async function saveProduct() {
    if (_busy) return
    _busy = true
    const fd = new FormData()
    fd.append('action', 'save')
        ;['prod-id', 'prod-title', 'prod-price', 'prod-stock', 'prod-type', 'prod-cat', 'prod-desc'].forEach(id => {
            const map = { 'prod-id': 'id', 'prod-title': 'title', 'prod-price': 'price', 'prod-stock': 'stock', 'prod-type': 'type', 'prod-cat': 'category_id', 'prod-desc': 'description' }
            fd.append(map[id], document.getElementById(id).value)
        })
    const file = document.getElementById('prod-image-file').files[0]
    if (file) fd.append('image', file)
    const token = localStorage.getItem('gashy_token')
    if (token) fd.append('token', token)
    try {
        const r = await fetch('./api/seller/save.php', { method: 'POST', body: fd })
        const j = await r.json()
        if (j.status) { notyf.success(j.message || 'Saved'); closeProductModal(); await loadHub() }
        else notyf.error(j.message || 'Failed')
    } catch (e) { notyf.error('Failed') }
    _busy = false
}

async function deleteProduct(id) {
    if (!confirm('Delete product?')) return
    const r = await App.post('./api/seller/save.php', { action: 'delete', id })
    if (r.status) { notyf.success('Deleted'); loadHub() } else notyf.error(r.message || 'Failed')
}

async function requestWithdraw() {
    const val = prompt('Enter amount')
    const amount = parseFloat(val || 0)
    if (amount <= 0) return notyf.error('Invalid amount')
    const r = await App.post('./api/seller/withdraw.php', { amount })
    if (r.status) { notyf.success(r.message); loadHub() } else notyf.error(r.message || 'Failed')
}

async function openInvModal(id, title) {
    _invProductId = id
    _invProductTitle = title
    document.getElementById('inv-modal').classList.remove('hidden')
    document.getElementById('inv-modal-title').innerText = title
    await loadInvOptions()
    await loadInvCodes()
}

async function loadInvOptions() {
    const r = await App.post('./api/seller/options.php', { product_id: _invProductId })
    if (!r.status) return
    const ops = r.options || []
    const sel = document.getElementById('inv-option')
    if (!ops.length) {
        sel.innerHTML = `<option value="0">Default</option>`
        _invOptionId = 0
        document.getElementById('inv-options-grid').innerHTML = ''
        return
    }
    if (!_invOptionId) _invOptionId = parseInt(ops[0].id)
    sel.innerHTML = ops.map(o => `<option value="${o.id}">${esc(o.name)} - $${num(o.price_usd)} / ${num(usdToG(o.price_usd))} G</option>`).join('')
    sel.value = String(_invOptionId)
    document.getElementById('inv-options-grid').innerHTML = ops.map(o => `<div class="code-row"><div>${esc(o.name)}</div><div>$${num(o.price_usd)}</div><div><button onclick="invDeleteOption(${o.id})" class="btn-icon del">×</button></div></div>`).join('')
}

async function invAddOptionModal() {
    const name = prompt('Option name')
    if (!name) return
    const price = prompt('Price USD')
    if (price === null) return
    const r = await App.post('./api/seller/options.php', { action: 'add', product_id: _invProductId, name, price_usd: price })
    if (r.status) { loadInvOptions(); loadInvCodes() } else notyf.error(r.message || 'Failed')
}

async function invDeleteOption(id) {
    if (!confirm('Delete option?')) return
    const r = await App.post('./api/seller/options.php', { action: 'delete', product_id: _invProductId, option_id: id })
    if (r.status) { loadInvOptions(); loadInvCodes() } else notyf.error(r.message || 'Failed')
}

async function loadInvCodes() {
    const r = await App.post('./api/seller/inventory.php', { product_id: _invProductId, option_id: _invOptionId, action: 'list' })
    if (!r.status) return
    const codes = r.codes || []
    const sold = codes.filter(x => parseInt(x.is_sold) === 1).length
    document.getElementById('inv-stat-total').innerText = codes.length
    document.getElementById('inv-stat-available').innerText = codes.length - sold
    document.getElementById('inv-stat-sold').innerText = sold
    document.getElementById('inv-codes-count').innerText = codes.length + ' entries'
    document.getElementById('inv-codes-list').innerHTML = !codes.length ? `<div style="padding:25px;text-align:center;color:var(--muted)">No codes</div>` : codes.map(c => `<div class="code-row"><div>****-****-${esc(c.code_tail)}</div><div>${c.is_sold == 1 ? 'Sold' : `<button onclick="invDeleteCode(${c.id})" class="btn-icon del">×</button>`}</div></div>`).join('')
}

async function invAddCodes() {
    const raw = document.getElementById('inv-codes-input').value.trim()
    if (!raw) return notyf.error('Enter codes')
    const r = await App.post('./api/seller/inventory.php', { product_id: _invProductId, option_id: _invOptionId, action: 'add', codes: raw })
    if (r.status) {
        notyf.success(r.message || 'Added')
        document.getElementById('inv-codes-input').value = ''
        loadHub()
        loadInvCodes()
    } else notyf.error(r.message || 'Failed')
}

async function invDeleteCode(id) {
    if (!confirm('Delete code?')) return
    const r = await App.post('./api/seller/inventory.php', { product_id: _invProductId, option_id: _invOptionId, action: 'delete', code_id: id })
    if (r.status) { loadHub(); loadInvCodes() } else notyf.error(r.message || 'Failed')
}

async function openMysteryModal(id, title) {
    _mysteryProductId = id
    document.getElementById('mystery-modal').classList.remove('hidden')
    document.getElementById('mystery-modal-title').innerText = title
    await loadMysteryLoot()
}

async function loadMysteryLoot() {
    const r = await App.post('./api/seller/mystery.php', { box_id: _mysteryProductId, action: 'list' })
    if (!r.status) return
    const loot = r.loot || []
    const prods = r.products || []
    document.getElementById('mystery-reward-product').innerHTML = `<option value="">Tokens (GASHY)</option>` + prods.map(p => `<option value="${p.id}">${esc(p.title)}</option>`).join('')
    document.getElementById('mystery-total-prob').innerText = loot.reduce((s, x) => s + parseFloat(x.probability || 0), 0).toFixed(2) + '%'
    document.getElementById('mystery-loot-list').innerHTML = !loot.length ? `<div style="padding:25px;text-align:center;color:var(--muted)">No loot</div>` : loot.map(l => `<div class="loot-row"><span>${l.reward_product_id ? '📦' : '🪙'} ${esc(l.title || 'Tokens')}</span><span>${num(l.reward_amount, 3)}</span><span>${esc(l.rarity)}</span><span>${num(l.probability)}%</span><button onclick="mysteryDeleteLoot(${l.id})" class="btn-icon del">×</button></div>`).join('')
}

async function mysteryAddLoot() {
    const p = { box_id: _mysteryProductId, action: 'add', reward_product_id: document.getElementById('mystery-reward-product').value || null, reward_amount: document.getElementById('mystery-reward-amount').value, rarity: document.getElementById('mystery-rarity').value, probability: document.getElementById('mystery-probability').value }
    const r = await App.post('./api/seller/mystery.php', p)
    if (r.status) { loadMysteryLoot(); notyf.success('Added') } else notyf.error(r.message || 'Failed')
}

async function mysteryDeleteLoot(id) {
    if (!confirm('Delete loot?')) return
    const r = await App.post('./api/seller/mystery.php', { box_id: _mysteryProductId, action: 'delete', loot_id: id })
    if (r.status) { loadMysteryLoot() } else notyf.error(r.message || 'Failed')
}