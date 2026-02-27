async function buyTickets(rid) {
    if (!App.checkAuth()) return;
    if (!window.solana?.isPhantom) { notyf.error('Phantom wallet required'); return; }
    if (!window.solana.isConnected) { try { await window.solana.connect(); } catch (e) { notyf.error('Wallet connection rejected'); return; } }
    const qtyInput = document.getElementById('ticket-qty');
    let qty = parseInt(qtyInput ? qtyInput.value : 1, 10);
    if (!qty || qty < 1) return notyf.error('Invalid Quantity');
    try {
        notyf.success('Preparing payment...');
        const connection = new solanaWeb3.Connection("https://mainnet.helius-rpc.com/?api-key=1fca693b-e96d-42f8-94b7-ab1c5bb0c9ed", "confirmed");
        const publicKey = window.solana.publicKey;
        const mint = new solanaWeb3.PublicKey("DokPYQ33k3T9S7EEesvwvuuAtoQb4pY8NWszukKwXWjv");
        const treasury = new solanaWeb3.PublicKey("GS4tXdRS7CQ5PgePt795Fk2oJe5q34XBhEugNn5AVPb");
        const mintInfo = await connection.getParsedAccountInfo(mint);
        const decimals = mintInfo.value?.data?.parsed?.info?.decimals || 9;
        const pricePerTicket = Number(window.GASHY_TICKET_PRICE || 10);
        const totalGashy = pricePerTicket * qty;
        if (!totalGashy || totalGashy <= 0) { notyf.error('Invalid price'); return; }
        const rawAmount = BigInt(Math.round(totalGashy * Math.pow(10, decimals)));
        const buyerTokens = await connection.getParsedTokenAccountsByOwner(publicKey, { mint: mint });
        if (buyerTokens.value.length === 0) { notyf.error('No GASHY tokens found in your wallet!'); return; }
        const fromATA = buyerTokens.value[0].pubkey;
        const balanceVal = BigInt(buyerTokens.value[0].account.data.parsed.info.tokenAmount.amount);
        if (balanceVal < rawAmount) { notyf.error('Insufficient GASHY balance'); return; }
        let toATA;
        const tx = new solanaWeb3.Transaction();
        const treasuryTokens = await connection.getParsedTokenAccountsByOwner(treasury, { mint: mint });
        if (treasuryTokens.value.length > 0) { toATA = treasuryTokens.value[0].pubkey; }
        else {
            toATA = await splToken.getAssociatedTokenAddress(mint, treasury);
            tx.add(splToken.createAssociatedTokenAccountInstruction(publicKey, toATA, treasury, mint));
        }
        tx.add(splToken.createTransferInstruction(fromATA, toATA, publicKey, rawAmount));
        tx.feePayer = publicKey;
        tx.recentBlockhash = (await connection.getLatestBlockhash()).blockhash;
        notyf.success('Approve in Phantom...');
        const signed = await window.solana.signAndSendTransaction(tx);
        await connection.confirmTransaction(signed.signature, "confirmed");
        const res = await App.post('./api/lottery/enter.php', { round_id: rid, tickets: qty, burn_tx: signed.signature });
        if (res.status) { notyf.success(`${qty} Tickets Secured!`); setTimeout(() => location.reload(), 800); } else { notyf.error(res.message); }
    } catch (e) { console.error("Lottery Error:", e); notyf.error('Failed: ' + (e.message || 'Transaction Cancelled')); }
}