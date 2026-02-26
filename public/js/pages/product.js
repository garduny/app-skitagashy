async function buyNow(pid) {
    const sellerId = Number(window.GASHY_PRODUCT_SELLER_ID || 0);
    let me = 0;
    if (App?.user?.account_id) me = Number(App.user.account_id); else if (App?.account_id) me = Number(App.account_id); else { try { const sess = localStorage.getItem('kita_session'); if (sess) { const prof = await App.request?.('account/profile.php').catch(() => null); if (prof?.account_id) me = Number(prof.account_id); } } catch (e) { } }
    if (sellerId && me && sellerId === me) { notyf.error('You cannot buy your own product'); return; }
    if (!App.checkAuth()) return;
    if (!window.solana?.isPhantom) { notyf.error('Phantom wallet required'); return; }
    const qtyInput = document.getElementById('qty');
    let qty = parseInt(qtyInput ? qtyInput.value : 1, 10);
    if (!qty || qty < 1) qty = 1;
    const max = parseInt(qtyInput ? qtyInput.max : 0, 10);
    if (max && qty > max) qty = max;
    try {
        notyf.success('Preparing payment...');
        const connection = new solanaWeb3.Connection("https://api.mainnet-beta.solana.com", "confirmed");
        const publicKey = window.solana.publicKey;
        const pricePerItem = Number(window.GASHY_PRICE || 0);
        const totalGashy = pricePerItem * qty;
        if (!totalGashy || totalGashy <= 0) { notyf.error('Invalid price'); return; }
        const rawAmount = BigInt(Math.floor(totalGashy * 1e9));
        const mint = new solanaWeb3.PublicKey("DokPYQ33k3T9S7EEesvwvuuAtoQb4pY8NWszukKwXWjv");
        const treasury = new solanaWeb3.PublicKey("GS4tXdRS7CQ5PgePt795Fk2oJe5q34XBhEugNn5AVPb");
        const fromATA = await splToken.getAssociatedTokenAddress(mint, publicKey);
        const toATA = await splToken.getAssociatedTokenAddress(mint, treasury);
        const balanceInfo = await connection.getTokenAccountBalance(fromATA);
        const walletRaw = BigInt(balanceInfo.value.amount);
        if (walletRaw < rawAmount) { notyf.error('Insufficient GASHY balance'); return; }
        const tx = new solanaWeb3.Transaction().add(splToken.createTransferInstruction(fromATA, toATA, publicKey, rawAmount));
        tx.feePayer = publicKey;
        tx.recentBlockhash = (await connection.getLatestBlockhash()).blockhash;
        notyf.success('Approve transaction in Phantom...');
        const signed = await window.solana.signAndSendTransaction(tx);
        await connection.confirmTransaction(signed.signature, "confirmed");
        notyf.success('Payment confirmed!');
        const res = await App.post('./api/orders/create.php', { items: [{ id: pid, qty: qty }], total: totalGashy, tx_signature: signed.signature });
        if (res && res.status) { notyf.success('Order Successful!'); setTimeout(() => location.href = 'orders.php', 800); } else { notyf.error(res?.message || 'Order failed'); }
    } catch (e) { console.error(e); notyf.error('Transaction failed'); }
}