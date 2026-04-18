document.addEventListener('DOMContentLoaded', async () => {
    if (!App.checkAuth()) return
    const loading = document.getElementById('seller-loading')
    const formView = document.getElementById('seller-form-view')
    const pendingView = document.getElementById('seller-pending-view')
    const dashboardView = document.getElementById('seller-dashboard-view')
    const nameInput = document.getElementById('store-name')
    const slugInput = document.getElementById('store-slug')
    const slugPreview = document.getElementById('slug-preview')
    if (nameInput) {
        nameInput.addEventListener('input', e => {
            let val = (e.target.value || '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').replace(/-+/g, '-')
            if (slugInput) slugInput.value = val
            if (slugPreview) slugPreview.innerText = val || '...'
        })
    }
    if (slugInput) {
        slugInput.addEventListener('input', e => {
            let val = (e.target.value || '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').replace(/-+/g, '-')
            slugInput.value = val
            if (slugPreview) slugPreview.innerText = val || '...'
        })
    }
    try {
        const res = await App.post('./api/account/seller_status.php', {})
        loading.classList.add('hidden')
        if (res && res.status === true && res.data) {
            if (Number(res.data.is_approved) === 1) {
                window.location.href = 'seller-hub.php'
                return
            }
            pendingView.classList.remove('hidden')
        } else {
            formView.classList.remove('hidden')
        }
    } catch (e) {
        console.error(e)
        loading.classList.add('hidden')
        formView.classList.remove('hidden')
    }
})

let __sellerSubmitting = false

async function applySeller() {
    if (__sellerSubmitting) return
    const name = (document.getElementById('store-name')?.value || '').trim()
    const slug = (document.getElementById('store-slug')?.value || '').trim().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').replace(/-+/g, '-')
    const btn = document.getElementById('apply-seller-btn')
    const txt = document.getElementById('apply-seller-text')
    if (!name || !slug) return notyf.error('Please fill all fields')
    if (slug.length < 3) return notyf.error('Slug too short')
    __sellerSubmitting = true
    if (btn) btn.disabled = true
    if (txt) txt.innerText = 'Submitting...'
    notyf.success('Submitting Application...')
    try {
        const res = await App.post('./api/account/apply_seller.php', { store_name: name, store_slug: slug })
        if (res && res.status) {
            notyf.success('Application Received!')
            setTimeout(() => location.reload(), 1200)
        } else {
            notyf.error(res?.message || 'Failed')
        }
    } catch (e) {
        console.error(e)
        notyf.error('Failed to submit')
    } finally {
        __sellerSubmitting = false
        if (btn) btn.disabled = false
        if (txt) txt.innerText = 'Submit Application'
    }
}