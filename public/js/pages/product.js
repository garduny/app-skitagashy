async function buyNow(pid) {
    const sellerId = Number(window.GASHY_PRODUCT_SELLER_ID || 0);
    let me = 0;
    if (App?.user?.account_id) me = Number(App.user.account_id);
    else if (App?.account_id) me = Number(App.account_id);
    else { try { const sess = localStorage.getItem('kita_session'); if (sess) { const prof = await App.request?.('account/profile.php').catch(() => null); if (prof?.account_id) me = Number(prof.account_id); } } catch (e) { } }
    if (sellerId && me && sellerId === me) { notyf.error('You cannot buy your own product'); return; }
    if (!App.checkAuth()) return;
    if (!window.solana?.isPhantom) { notyf.error('Phantom wallet required'); return; }
    if (!window.solana.isConnected) {
        try { await window.solana.connect(); } catch (err) { notyf.error('Wallet connection rejected'); return; }
    }
    try {
        const qtyInput = document.getElementById('qty');
        let qty = parseInt(qtyInput ? qtyInput.value : 1, 10);
        if (!qty || qty < 1) qty = 1;
        notyf.success('Preparing payment...');
        const connection = new solanaWeb3.Connection("https://mainnet.helius-rpc.com/?api-key=1fca693b-e96d-42f8-94b7-ab1c5bb0c9ed", "confirmed");
        const publicKey = window.solana.publicKey;
        const mint = new solanaWeb3.PublicKey("DokPYQ33k3T9S7EEesvwvuuAtoQb4pY8NWszukKwXWjv");
        const treasury = new solanaWeb3.PublicKey("GS4tXdRS7CQ5PgePt795Fk2oJe5q34XBhEugNn5AVPb");
        const mintInfo = await connection.getParsedAccountInfo(mint);
        const decimals = mintInfo.value?.data?.parsed?.info?.decimals || 9;
        const pricePerItem = Number(window.GASHY_PRICE || 0);
        const totalGashy = pricePerItem * qty;
        if (!totalGashy || totalGashy <= 0) { notyf.error('Invalid price'); return; }
        const rawAmount = BigInt(Math.round(totalGashy * Math.pow(10, decimals)));
        console.log(`Sending ${totalGashy} GASHY (${rawAmount})`);
        const buyerTokens = await connection.getParsedTokenAccountsByOwner(publicKey, { mint: mint });
        if (buyerTokens.value.length === 0) {
            notyf.error('No GASHY tokens found in your wallet!');
            return;
        }
        const fromATA = buyerTokens.value[0].pubkey;
        const balanceVal = BigInt(buyerTokens.value[0].account.data.parsed.info.tokenAmount.amount);
        if (balanceVal < rawAmount) {
            notyf.error('Insufficient GASHY balance');
            return;
        }
        let toATA;
        const tx = new solanaWeb3.Transaction();
        const treasuryTokens = await connection.getParsedTokenAccountsByOwner(treasury, { mint: mint });
        if (treasuryTokens.value.length > 0) {
            console.log("Found existing Treasury token account.");
            toATA = treasuryTokens.value[0].pubkey;
        } else {
            console.log("No Treasury account found. Creating new ATA...");
            toATA = await splToken.getAssociatedTokenAddress(mint, treasury);
            tx.add(
                splToken.createAssociatedTokenAccountInstruction(
                    publicKey,
                    toATA,
                    treasury,
                    mint
                )
            );
        }
        tx.add(splToken.createTransferInstruction(fromATA, toATA, publicKey, rawAmount));
        tx.feePayer = publicKey;
        tx.recentBlockhash = (await connection.getLatestBlockhash()).blockhash;
        notyf.success('Approve in Phantom...');
        const signed = await window.solana.signAndSendTransaction(tx);
        console.log("TX:", signed.signature);
        notyf.success('Processing...');
        await connection.confirmTransaction(signed.signature, "confirmed");
        notyf.success('Payment confirmed!');
        const res = await App.post('./api/orders/create.php', {
            items: [{ id: pid, qty: qty }],
            total: totalGashy,
            tx_signature: signed.signature
        });
        if (res && res.status) {
            notyf.success('Order Successful!');
            setTimeout(() => location.href = 'orders.php', 800);
        } else {
            notyf.error(res?.message || 'Order failed');
        }
    } catch (e) {
        console.error("Transaction Error:", e);
        if (e.message && e.message.includes("0x1")) {
            notyf.error('Insufficient SOL for gas fees');
        } else {
            notyf.error('Transaction failed');
        }
    }
}