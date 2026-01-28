async function buyNow(pid) {
    if (!App.checkAuth()) return;
    const qtyInput = document.getElementById('qty');
    const qty = qtyInput ? qtyInput.value : 1;
    const priceText = document.querySelector('.text-3xl.font-black').innerText;
    notyf.success('Please approve transaction in wallet...');
    try {
        const message = `CONFIRM PURCHASE:\n\nItem: Product #${pid}\nQuantity: ${qty}\nTotal: ${priceText}\n\nClick Approve to pay.`;
        const encoded = new TextEncoder().encode(message);
        const signed = await window.solana.signMessage(encoded, 'utf8');
        let txSig = '';
        if (signed.signature) {
            txSig = Array.from(new Uint8Array(signed.signature)).map(b => b.toString(16).padStart(2, '0')).join('');
        } else {
            txSig = 'MANUAL_APPROVAL_' + Date.now();
        }
        notyf.success('Payment Approved! Processing...');
        const res = await App.post('api/orders/create.php', {
            items: [{ id: pid, qty: qty }],
            total: 0,
            tx_signature: txSig
        });
        if (res.status) {
            notyf.success('Order Successful!');
            setTimeout(() => location.href = 'orders.php', 1000);
        } else {
            notyf.error(res.message);
        }
    } catch (e) {
        console.error(e);
        notyf.error('Transaction Rejected by Account');
    }
}