document.addEventListener('DOMContentLoaded', async () => {
    if (!App.checkAuth()) return;
    const loading = document.getElementById('seller-loading');
    const formView = document.getElementById('seller-form-view');
    const pendingView = document.getElementById('seller-pending-view');
    const nameInput = document.getElementById('store-name');
    const slugInput = document.getElementById('store-slug');
    const slugPreview = document.getElementById('slug-preview');
    if (nameInput) {
        nameInput.addEventListener('input', (e) => {
            const val = e.target.value.toLowerCase().replace(/[^a-z0-9]/g, '-').replace(/-+/g, '-');
            slugInput.value = val;
            slugPreview.innerText = val || '...';
        });
    }
    try {
        const res = await App.post('./api/account/seller_status.php', {});
        loading.classList.add('hidden');
        if (res.status && res.data) {
            if (res.data.is_approved == 1) {
                window.location.href = 'seller-hub.php';
            } else {
                pendingView.classList.remove('hidden');
            }
        } else {
            formView.classList.remove('hidden');
        }
    } catch (e) {
        console.error(e);
        loading.classList.add('hidden');
        formView.classList.remove('hidden');
    }
});
async function applySeller() {
    const name = document.getElementById('store-name').value;
    const slug = document.getElementById('store-slug').value;
    if (!name || !slug) return notyf.error('Please fill all fields');
    notyf.success('Submitting Application...');
    try {
        const res = await App.post('./api/account/apply_seller.php', { store_name: name, store_slug: slug });
        if (res.status) {
            notyf.success('Application Received!');
            setTimeout(() => location.reload(), 1500);
        } else {
            notyf.error(res.message);
        }
    } catch (e) {
        notyf.error('Failed to submit');
    }
}