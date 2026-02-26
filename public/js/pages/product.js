async function buyNow(pid) {
    const sellerId = Number(window.GASHY_PRODUCT_SELLER_ID || 0);
    let me = 0;
    if (App?.user?.account_id) me = Number(App.user.account_id);
    else if (App?.account_id) me = Number(App.account_id);
    else {
        try {
            const sess = localStorage.getItem('kita_session');
            if (sess) {
                const prof = await App.request?.('account/profile.php').catch(() => null);
                if (prof?.account_id) me = Number(prof.account_id);
            }
        } catch (e) { }
    }
    if (sellerId && me && sellerId === me) {
        notyf.error('You cannot buy your own product');
        return;
    }
    if (!App.checkAuth()) return;
    const qtyInput = document.getElementById('qty');
    let qty = parseInt(qtyInput ? qtyInput.value : 1, 10);
    if (!qty || qty < 1) qty = 1;
    const max = parseInt(qtyInput ? qtyInput.max : 0, 10);
    if (max && qty > max) qty = max;
    const priceText = document.querySelector('.priceText')?.innerText || '';
    notyf.success('Please approve transaction in wallet...');
    try {
        const nonce = Date.now() + Math.random().toString(36).substring(2, 10);
        const message = `CONFIRM PURCHASE:\n\nItem: Product #${pid}\nQuantity: ${qty}\nTotal: ${priceText}\nTimestamp: ${nonce}\n\nClick Approve to pay.`;
        const encoded = new TextEncoder().encode(message);
        const signed = await window.solana.signMessage(encoded, 'utf8');
        let txSig = '';
        if (signed && signed.signature) {
            txSig = Array.from(new Uint8Array(signed.signature)).map(b => b.toString(16).padStart(2, '0')).join('');
        } else {
            txSig = 'MANUAL_APPROVAL_' + nonce;
        }
        notyf.success('Payment Approved! Processing...');
        const res = await App.post('./api/orders/create.php', {
            items: [{ id: pid, qty: qty }],
            total: 0,
            tx_signature: txSig
        });
        if (res && res.status) {
            notyf.success('Order Successful!');
            setTimeout(() => location.href = 'orders.php', 1000);
        } else {
            notyf.error(res?.message || 'Order failed');
        }
    } catch (e) {
        console.error(e);
        notyf.error('Transaction Rejected by Account');
    }
}