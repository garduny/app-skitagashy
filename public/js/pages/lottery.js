async function buyTickets(rid) {
    if (!App.checkAuth()) return;
    if (!window.solana?.isPhantom) { notyf.error('Phantom wallet required'); return; }
    const qtyInput = document.getElementById('ticket-qty');
    let qty = parseInt(qtyInput ? qtyInput.value : 1, 10);
    if (!qty || qty < 1) return notyf.error('Invalid Quantity');
    try {
        notyf.success('Preparing payment...');
        const connection = new solanaWeb3.Connection("https://api.mainnet-beta.solana.com", "confirmed");
        const publicKey = window.solana.publicKey;
        const pricePerTicket = Number(window.GASHY_TICKET_PRICE || 10);
        const totalGashy = pricePerTicket * qty;
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
        const res = await App.post('./api/lottery/enter.php', { round_id: rid, tickets: qty, burn_tx: signed.signature });
        if (res.status) { notyf.success(`${qty} Tickets Secured!`); setTimeout(() => location.reload(), 800); } else { notyf.error(res.message); }
    } catch (e) { console.error("Lottery Error:", e); notyf.error('Failed: ' + (e.message || 'Transaction Cancelled')); }
}