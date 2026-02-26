async function openBox(id) {
    if (!App.checkAuth()) return;
    if (!window.solana?.isPhantom) { notyf.error('Phantom wallet required'); return; }
    try {
        notyf.success('Preparing burn...');
        const connection = new solanaWeb3.Connection("https://api.mainnet-beta.solana.com", "confirmed");
        const publicKey = window.solana.publicKey;
        const price = Number(window.GASHY_BOX_PRICE || 500);
        if (!price || price <= 0) { notyf.error('Invalid price'); return; }
        const rawAmount = BigInt(Math.floor(price * 1e9));
        const mint = new solanaWeb3.PublicKey("DokPYQ33k3T9S7EEesvwvuuAtoQb4pY8NWszukKwXWjv");
        const burnWallet = new solanaWeb3.PublicKey("1nc1nerator11111111111111111111111111111111");
        const fromATA = await splToken.getAssociatedTokenAddress(mint, publicKey);
        const toATA = await splToken.getAssociatedTokenAddress(mint, burnWallet);
        const balanceInfo = await connection.getTokenAccountBalance(fromATA);
        const walletRaw = BigInt(balanceInfo.value.amount);
        if (walletRaw < rawAmount) { notyf.error('Insufficient GASHY balance'); return; }
        const tx = new solanaWeb3.Transaction().add(splToken.createTransferInstruction(fromATA, toATA, publicKey, rawAmount));
        tx.feePayer = publicKey;
        tx.recentBlockhash = (await connection.getLatestBlockhash()).blockhash;
        notyf.success('Approve transaction in Phantom...');
        const signed = await window.solana.signAndSendTransaction(tx);
        await connection.confirmTransaction(signed.signature, "confirmed");
        notyf.success('Unlocking Box...');
        const res = await App.post('./api/mystery_box/open.php', { box_id: id, tx_signature: signed.signature });
        if (res.status) { alert(`🎉 REWARD: ${res.reward.type.toUpperCase()}\nRarity: ${res.rarity}`); location.reload(); } else { notyf.error(res.message); }
    } catch (e) { console.error(e); notyf.error('Transaction Cancelled'); }
}