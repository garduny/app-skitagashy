async function openBox(id, price) {
    if (!App.checkAuth()) return;
    if (!window.solana?.isPhantom) { notyf.error('Phantom wallet required'); return; }
    if (!window.solana.isConnected) { try { await window.solana.connect(); } catch (e) { notyf.error('Wallet connection rejected'); return; } }
    try {
        notyf.success('Preparing burn...');
        const connection = new solanaWeb3.Connection("https://mainnet.helius-rpc.com/?api-key=1fca693b-e96d-42f8-94b7-ab1c5bb0c9ed", "confirmed");
        const publicKey = window.solana.publicKey;
        const mint = new solanaWeb3.PublicKey("DokPYQ33k3T9S7EEesvwvuuAtoQb4pY8NWszukKwXWjv");
        const burnWallet = new solanaWeb3.PublicKey("1nc1nerator11111111111111111111111111111111");
        const mintInfo = await connection.getParsedAccountInfo(mint);
        const decimals = mintInfo.value?.data?.parsed?.info?.decimals || 9;
        const nprice = Number(price);
        if (!nprice || nprice <= 0) { notyf.error('Invalid price'); return; }
        const rawAmount = BigInt(Math.round(nprice * Math.pow(10, decimals)));
        const buyerTokens = await connection.getParsedTokenAccountsByOwner(publicKey, { mint: mint });
        if (buyerTokens.value.length === 0) { notyf.error('No GASHY tokens found in your wallet!'); return; }
        const fromATA = buyerTokens.value[0].pubkey;
        const balanceVal = BigInt(buyerTokens.value[0].account.data.parsed.info.tokenAmount.amount);
        if (balanceVal < rawAmount) { notyf.error('Insufficient GASHY balance'); return; }
        let toATA;
        const tx = new solanaWeb3.Transaction();
        const burnTokens = await connection.getParsedTokenAccountsByOwner(burnWallet, { mint: mint });
        if (burnTokens.value.length > 0) { toATA = burnTokens.value[0].pubkey; }
        else {
            toATA = await splToken.getAssociatedTokenAddress(mint, burnWallet);
            tx.add(splToken.createAssociatedTokenAccountInstruction(publicKey, toATA, burnWallet, mint));
        }
        tx.add(splToken.createTransferInstruction(fromATA, toATA, publicKey, rawAmount));
        tx.feePayer = publicKey;
        tx.recentBlockhash = (await connection.getLatestBlockhash()).blockhash;
        notyf.success('Approve in Phantom...');
        const signed = await window.solana.signAndSendTransaction(tx);
        await connection.confirmTransaction(signed.signature, "confirmed");
        notyf.success('Unlocking Box...');
        const res = await App.post('./api/mystery_box/open.php', { box_id: id, tx_signature: signed.signature });
        if (res.status) { alert(`🎉 REWARD: ${res.reward.type.toUpperCase()}\nRarity: ${res.rarity}`); location.reload(); } else { notyf.error(res.message); }
    } catch (e) { console.error(e); notyf.error('Transaction Cancelled'); }
}