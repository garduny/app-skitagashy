document.addEventListener('DOMContentLoaded', async () => {
    if (!App.checkAuth()) return;
    await loadHub();
});
let myProducts = [];
async function loadHub() {
    try {
        const res = await App.post('api/seller/dashboard.php', {});
        document.getElementById('hub-loader').classList.add('hidden');
        if (res.status) {
            document.getElementById('hub-content').classList.remove('hidden');
            document.getElementById('stat-earnings').innerText = parseFloat(res.stats.earnings).toFixed(2);
            document.getElementById('stat-sales').innerText = res.stats.total_sales;
            document.getElementById('stat-products').innerText = res.stats.products;
            document.getElementById('stat-rating').innerText = res.stats.rating;
            myProducts = res.products;
            renderTable();
            renderSales(res.sales);
        } else {
            window.location.href = 'seller.php';
        }
    } catch (e) {
        console.error(e);
    }
}
function renderTable() {
    const list = document.getElementById('product-list');
    if (myProducts.length === 0) {
        list.innerHTML = `<tr><td colspan="6" class="p-8 text-center text-gray-500">No products listed.</td></tr>`;
        return;
    }
    list.innerHTML = myProducts.map(p => {
        let img = 'assets/placeholder.png';
        try { img = JSON.parse(p.images)[0] || img; } catch (e) { }
        return `<tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors"><td class="px-6 py-4"><div class="flex items-center gap-3"><img src="${img}" class="w-10 h-10 rounded-lg object-cover bg-gray-100 dark:bg-white/5"><div class="font-bold text-gray-900 dark:text-white truncate max-w-[200px]">${p.title}</div></div></td><td class="px-6 py-4 font-mono font-bold text-gray-900 dark:text-white">${parseFloat(p.price_gashy).toFixed(2)}</td><td class="px-6 py-4">${p.stock}</td><td class="px-6 py-4"><span class="px-2 py-1 rounded text-[10px] uppercase font-bold ${p.status === 'active' ? 'text-green-500 bg-green-500/10' : 'text-red-500 bg-red-500/10'}">${p.status}</span></td><td class="px-6 py-4 text-right"><button onclick="editProduct(${p.id})" class="p-2 text-blue-500 hover:bg-blue-500/10 rounded"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button><button onclick="deleteProduct(${p.id})" class="p-2 text-red-500 hover:bg-red-500/10 rounded"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></td></tr>`;
    }).join('');
}
function renderSales(sales) {
    const list = document.getElementById('sales-list');
    if (!sales || sales.length === 0) {
        list.innerHTML = `<tr><td colspan="2" class="p-4 text-center text-xs text-gray-500">No sales yet.</td></tr>`;
        return;
    }
    list.innerHTML = sales.map(s => {
        return `<tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors"><td class="px-4 py-3"><div class="font-bold text-gray-900 dark:text-white text-sm truncate max-w-[150px]">${s.title}</div><div class="text-xs text-gray-500">Buyer: ${s.accountname}</div></td><td class="px-4 py-3 text-right"><div class="font-bold text-green-500 text-sm">+${parseFloat(s.price_at_purchase * s.quantity).toFixed(2)}</div><div class="text-[10px] text-gray-500">${new Date(s.created_at).toLocaleDateString()}</div></td></tr>`;
    }).join('');
}
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
    try { document.getElementById('prod-images').value = JSON.parse(p.images).join('\n'); } catch (e) { }
    openProductModal(true);
}
async function saveProduct() {
    const data = {
        action: 'save',
        id: document.getElementById('prod-id').value,
        title: document.getElementById('prod-title').value,
        price: document.getElementById('prod-price').value,
        stock: document.getElementById('prod-stock').value,
        type: document.getElementById('prod-type').value,
        category_id: document.getElementById('prod-cat').value,
        description: document.getElementById('prod-desc').value,
        images: document.getElementById('prod-images').value
    };
    try {
        const res = await App.post('api/seller/save.php', data);
        if (res.status) {
            notyf.success(res.message);
            closeProductModal();
            loadHub();
        } else {
            notyf.error(res.message);
        }
    } catch (e) {
        notyf.error('Save failed');
    }
}
async function deleteProduct(id) {
    if (!confirm('Remove this product?')) return;
    try {
        const res = await App.post('api/seller/save.php', {
            action: 'delete',
            id: id
        });
        if (res.status) {
            notyf.success('Deleted');
            loadHub();
        } else {
            notyf.error(res.message);
        }
    } catch (e) {
        notyf.error('Delete failed');
    }
}