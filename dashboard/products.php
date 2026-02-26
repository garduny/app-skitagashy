<?php
require_once 'init.php';
$uploadPath = '../server/uploads/products/';
$dbPath = '/server/uploads/products/';
function isInventoryType($type)
{
    return in_array($type, ['digital', 'gift_card']);
}
function isMysteryBox($type)
{
    return $type === 'mystery_box';
}
function productsBaseUrl($extra = [])
{
    $search = request('search', 'get');
    $cat = request('category', 'get');
    $type = request('type', 'get');
    $status = request('status', 'get');
    $sort = request('sort', 'get');
    $limit = request('limit', 'get');
    $params = ['search' => $search, 'category' => $cat, 'type' => $type, 'status' => $status, 'sort' => $sort, 'limit' => $limit];
    $params = array_filter($params, function ($v) {
        return $v !== null && $v !== '';
    });
    $params = array_merge($params, $extra);
    return 'products.php' . ($params ? '?' . http_build_query($params) : '');
}
$cats = getQuery(" SELECT id,name,slug FROM categories ORDER BY name ASC ");
$sellers = getQuery(" SELECT account_id,store_name FROM sellers WHERE is_approved=1 ORDER BY store_name ASC ");
if (get('delete')) {
    $id = (int)request('delete', 'get');
    execute(" UPDATE products SET status='banned' WHERE id=$id ");
    redirect(productsBaseUrl(['msg' => 'banned']));
}
if (get('restore')) {
    $id = (int)request('restore', 'get');
    execute(" UPDATE products SET status='active' WHERE id=$id ");
    redirect(productsBaseUrl(['msg' => 'restored']));
}
if (post('save_product')) {
    $id = (int)request('id', 'post');
    $title = secure(request('title', 'post'));
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $price = (float)request('price_gashy', 'post');
    $stock = (int)request('stock', 'post');
    $type = request('type', 'post');
    $cat = (int)request('category_id', 'post');
    $sid = (int)request('seller_id', 'post');
    $desc = secure(request('description', 'post'));
    $exists = $id ? findQuery(" SELECT id FROM products WHERE (title='$title' OR slug='$slug') AND id!=$id LIMIT 1 ") : findQuery(" SELECT id FROM products WHERE title='$title' OR slug='$slug' LIMIT 1 ");
    if ($exists) redirect(productsBaseUrl(['msg' => 'exists']));
    $newImage = upload('image', $uploadPath);
    if ($id) {
        $old = findQuery(" SELECT images FROM products WHERE id=$id ");
        $oldImg = '';
        if (!empty($old['images'])) {
            $tmp = json_decode($old['images'], true);
            $oldImg = $tmp[0] ?? '';
        }
        $finalImg = $newImage ? $dbPath . $newImage : $oldImg;
        if ($newImage && $oldImg) {
            $f = '../' . ltrim($oldImg, '/');
            if (file_exists($f)) @unlink($f);
        }
        $imgs = json_encode([$finalImg]);
        execute(" UPDATE products SET title='$title',slug='$slug',price_gashy=$price,stock=$stock,type='$type',category_id=$cat,seller_id=$sid,description='$desc',images='$imgs' WHERE id=$id ");
        redirect(productsBaseUrl(['msg' => 'updated']));
    } else {
        $img = $newImage ? $dbPath . $newImage : '';
        $imgs = json_encode([$img]);
        execute(" INSERT INTO products (title,slug,price_gashy,stock,type,category_id,seller_id,description,images,status) VALUES ('$title','$slug',$price,$stock,'$type',$cat,$sid,'$desc','$imgs','active') ");
        redirect(productsBaseUrl(['msg' => 'created']));
    }
}
$search = request('search', 'get');
$cat = request('category', 'get');
$type = request('type', 'get');
$status = request('status', 'get');
$sort = request('sort', 'get') ?: 'newest';
$limit = max(1, (int)(request('limit', 'get') ?: 10));
$page = max(1, (int)(request('page', 'get') ?: 1));
$offset = ($page - 1) * $limit;
$where = " WHERE 1=1 ";
if ($search) {
    $where .= " AND (p.title LIKE '%$search%' OR p.slug LIKE '%$search%') ";
}
if ($cat) {
    $where .= " AND c.slug='$cat' ";
}
if ($type) {
    $where .= " AND p.type='$type' ";
}
if ($status) {
    $where .= " AND p.status='$status' ";
}
$order = " ORDER BY p.id DESC ";
if ($sort === 'oldest') $order = " ORDER BY p.id ASC ";
if ($sort === 'price_high') $order = " ORDER BY p.price_gashy DESC ";
if ($sort === 'price_low') $order = " ORDER BY p.price_gashy ASC ";
if ($sort === 'stock_low') $order = " ORDER BY p.stock ASC ";
$products = getQuery(" SELECT p.*,c.name cat_name,s.store_name FROM products p JOIN categories c ON p.category_id=c.id JOIN sellers s ON p.seller_id=s.account_id $where $order LIMIT $limit OFFSET $offset ");
$total = countQuery(" SELECT 1 FROM products p JOIN categories c ON p.category_id=c.id $where ");
$pages = ceil($total / $limit);
function filterUrl($key, $val)
{
    global $search, $cat, $type, $status, $sort, $limit;
    $params = ['search' => $search, 'category' => $cat, 'type' => $type, 'status' => $status, 'sort' => $sort, 'limit' => $limit];
    $params[$key] = $val;
    return '?' . http_build_query(array_filter($params));
}
require_once 'header.php';
require_once 'sidebar.php';
?>

<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Products Inventory</h1>
            <p class="text-sm text-gray-500">Manage <?= $total ?> listings.</p>
        </div>
        <button onclick="openModal('productModal');resetForm();" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl shadow-lg shadow-primary-500/20 transition-all flex items-center gap-2"><i class="fa-solid fa-plus"></i> Add Product</button>
    </div>
    <?php $msg = request('msg', 'get');
    if ($msg == 'exists'): ?><div class="p-4 mb-6 bg-red-100 dark:bg-red-500/20 border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 rounded-xl font-bold text-center"><i class="fa-solid fa-triangle-exclamation mr-2"></i> Product title/slug already exists.</div><?php elseif (in_array($msg, ['created', 'updated', 'banned', 'restored'])): ?><div class="p-4 mb-6 bg-green-100 dark:bg-green-500/20 border border-green-200 dark:border-green-500/30 text-green-600 dark:text-green-400 rounded-xl font-bold text-center capitalize"><i class="fa-solid fa-check-circle mr-2"></i> Product <?= $msg ?> successfully.</div><?php endif; ?>
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200 dark:border-white/5">
            <form class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <input type="text" name="search" value="<?= $search ?>" placeholder="Search products..." class="bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-gray-900 dark:text-white outline-none">
                <select name="limit" class="bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-gray-900 dark:text-white outline-none">
                    <?php foreach ([10, 25, 50, 100] as $l): ?><option value="<?= $l ?>" <?= $limit == $l ? 'selected' : '' ?>><?= $l ?>/page</option><?php endforeach; ?></select>
                <button type="submit" class="px-4 py-2 bg-gray-100 dark:bg-white/5 rounded-xl"><i class="fa-solid fa-filter"></i></button>
                <a href="products.php" class="px-4 py-2 bg-red-50 dark:bg-red-900/10 text-red-500 rounded-xl flex items-center justify-center"><i class="fa-solid fa-times"></i></a>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/5 text-xs uppercase text-gray-500 font-bold">
                        <th class="px-6 py-4">Product</th>
                        <th class="px-6 py-4">Seller</th>
                        <th class="px-6 py-4">Price</th>
                        <th class="px-6 py-4">Stock</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php foreach ($products as $p): $img = json_decode($p['images'])[0] ?? ''; ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3"><img src="../<?= ltrim($img, '/') ?>" class="w-10 h-10 rounded-lg object-cover bg-gray-100 dark:bg-white/5">
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-sm"><?= $p['title'] ?></div>
                                        <div class="text-xs text-gray-500"><?= $p['cat_name'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300"><?= $p['store_name'] ?></td>
                            <td class="px-6 py-4 font-mono font-bold text-gray-900 dark:text-white"><?= number_format($p['price_gashy'], 2) ?></td>
                            <td class="px-6 py-4"><span class="px-2 py-1 bg-gray-100 dark:bg-white/10 rounded text-xs font-bold <?= $p['stock'] < 5 ? 'text-red-500' : 'text-gray-600 dark:text-gray-300' ?>"><?= $p['stock'] ?></span></td>
                            <td class="px-6 py-4"><span class="uppercase text-[10px] font-bold tracking-wider px-2 py-1 rounded bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400"><?= $p['type'] ?></span></td>
                            <td class="px-6 py-4"><?php if ($p['status'] == 'active'): ?><span class="text-green-500 text-xs font-bold bg-green-500/10 px-2 py-1 rounded">Active</span><?php elseif ($p['status'] == 'banned'): ?><span class="text-red-500 text-xs font-bold bg-red-500/10 px-2 py-1 rounded">Banned</span><?php else: ?><span class="text-gray-500 text-xs font-bold bg-gray-500/10 px-2 py-1 rounded">Inactive</span><?php endif; ?></td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">

                                    <a href="productdetail.php?id=<?= $p['id'] ?>" class="p-2 text-gray-400 hover:text-blue-500">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    <?php if (isInventoryType($p['type'])): ?>
                                        <a href="inventory.php?product_id=<?= $p['id'] ?>" class="p-2 text-gray-400 hover:text-purple-500">
                                            <i class="fa-solid fa-box-open"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if (isMysteryBox($p['type'])): ?>
                                        <a href="mystery-box-detail.php?id=<?= $p['id'] ?>" class="p-2 text-gray-400 hover:text-amber-500">
                                            <i class="fa-solid fa-gift"></i>
                                        </a>
                                    <?php endif; ?>

                                    <button onclick='editProduct(<?= json_encode($p) ?>)' class="p-2 text-gray-400 hover:text-primary-500">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <?php if ($p['status'] == 'banned'): ?>
                                        <a href="?restore=<?= $p['id'] ?>" class="p-2 text-green-500">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="?delete=<?= $p['id'] ?>" class="p-2 text-gray-400 hover:text-red-500">
                                            <i class="fa-solid fa-ban"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-white/5 flex justify-center gap-2">
            <?php if ($pages > 1): for ($i = 1; $i <= $pages; $i++): ?><a href="<?= filterUrl('page', $i) ?>" class="px-3 py-1 rounded-lg text-sm font-bold <?= $i == $page ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-white/5 text-gray-500' ?>"><?= $i ?></a><?php endfor;
                                                                                                                                                                                                                                                                    endif; ?>
        </div>
    </div>
</main>
<div id="productModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('productModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-4xl bg-white dark:bg-dark-800 rounded-2xl shadow-2xl p-0 max-h-[90vh] overflow-hidden flex flex-col">
        <div class="p-6 border-b border-gray-200 dark:border-white/10 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white" id="modalTitle">Add Product</h3><button onclick="closeModal('productModal')" class="text-gray-500 hover:text-red-500"><i class="fa-solid fa-times text-xl"></i></button>
        </div>
        <div class="overflow-y-auto p-6 flex-1 custom-scrollbar">
            <form method="POST" enctype="multipart/form-data" id="productForm"><input type="hidden" name="id" id="prod_id">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-4">
                        <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Title</label><input type="text" name="title" id="prod_title" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                        <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Description</label><textarea name="description" id="prod_desc" rows="5" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></textarea></div>
                        <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Image</label><input type="file" name="image" id="prod_image" accept="image/*" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                    </div>
                    <div class="lg:col-span-1 space-y-4">
                        <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Price (GASHY)</label><input type="number" step="0.000000001" name="price_gashy" id="prod_price" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                        <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Stock</label><input type="number" name="stock" id="prod_stock" required class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"></div>
                        <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Type</label><select name="type" id="prod_type" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none">
                                <option value="digital">Digital</option>
                                <option value="gift_card">Gift Card</option>
                                <option value="physical">Physical</option>
                                <option value="nft">NFT</option>
                                <option value="mystery_box">Mystery Box</option>
                            </select></div>
                        <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Category</label><select name="category_id" id="prod_cat" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"><?php foreach ($cats as $c): ?><option value="<?= $c['id'] ?>"><?= $c['name'] ?></option><?php endforeach; ?></select></div>
                        <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Seller</label><select name="seller_id" id="prod_seller" class="w-full bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5 text-gray-900 dark:text-white outline-none"><?php foreach ($sellers as $s): ?><option value="<?= $s['account_id'] ?>"><?= $s['store_name'] ?></option><?php endforeach; ?></select></div><button type="submit" name="save_product" value="1" class="w-full py-3 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl transition-all shadow-lg shadow-primary-500/20">Save Product</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    window.openModal = function(id) {
        const el = document.getElementById(id);
        if (el) el.classList.remove('hidden')
    }
    window.closeModal = function(id) {
        const el = document.getElementById(id);
        if (el) el.classList.add('hidden')
    }
    window.resetForm = function() {
        const f = document.getElementById('productForm');
        if (f) f.reset();
        const id = document.getElementById('prod_id');
        if (id) id.value = '';
        const t = document.getElementById('modalTitle');
        if (t) t.innerText = 'Add Product'
    }
    window.editProduct = function(p) {
        const map = (k, v) => {
            const el = document.getElementById(k);
            if (el) el.value = v
        };
        map('prod_id', p.id);
        map('prod_title', p.title);
        map('prod_desc', p.description);
        map('prod_price', p.price_gashy);
        map('prod_stock', p.stock);
        map('prod_type', p.type);
        map('prod_cat', p.category_id);
        map('prod_seller', p.seller_id);
        const t = document.getElementById('modalTitle');
        if (t) t.innerText = 'Edit Product';
        openModal('productModal')
    }
</script>
<?php require_once 'footer.php'; ?>