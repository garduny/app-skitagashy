document.addEventListener('DOMContentLoaded', () => { console.log("NFT Launchpad Loaded"); });
async function mintNFT(dropId, price) {
    if (!App.checkAuth()) return;
    if (!window.solana?.isPhantom) { window.notyf.error('Phantom wallet required'); return; }
    if (!confirm(`Mint this NFT for ${price} GASHY?`)) return;
    window.notyf.success('Preparing payment...');
    try {
        const connection = new solanaWeb3.Connection("https://api.mainnet-beta.solana.com", "confirmed");
        const publicKey = window.solana.publicKey;
        const totalGashy = Number(price || 0);
        if (!totalGashy || totalGashy <= 0) { window.notyf.error('Invalid price'); return; }
        const rawAmount = BigInt(Math.floor(totalGashy * 1e9));
        const mint = new solanaWeb3.PublicKey("DokPYQ33k3T9S7EEesvwvuuAtoQb4pY8NWszukKwXWjv");
        const treasury = new solanaWeb3.PublicKey("GS4tXdRS7CQ5PgePt795Fk2oJe5q34XBhEugNn5AVPb");
        const fromATA = await splToken.getAssociatedTokenAddress(mint, publicKey);
        const toATA = await splToken.getAssociatedTokenAddress(mint, treasury);
        const balanceInfo = await connection.getTokenAccountBalance(fromATA);
        const walletRaw = BigInt(balanceInfo.value.amount);
        if (walletRaw < rawAmount) { window.notyf.error('Insufficient GASHY balance'); return; }
        const tx = new solanaWeb3.Transaction().add(splToken.createTransferInstruction(fromATA, toATA, publicKey, rawAmount));
        tx.feePayer = publicKey;
        tx.recentBlockhash = (await connection.getLatestBlockhash()).blockhash;
        window.notyf.success('Approve transaction in Phantom...');
        const signed = await window.solana.signAndSendTransaction(tx);
        await connection.confirmTransaction(signed.signature, "confirmed");
        window.notyf.success('Processing Mint on Ledger...');
        const res = await App.post('api/nft/mint.php', { drop_id: dropId, tx_signature: signed.signature });
        if (res.status) { window.notyf.success(res.message); setTimeout(() => location.reload(), 2000); } else window.notyf.error(res.message);
    } catch (e) { console.error("Mint Error:", e); window.notyf.error('Mint Transaction Cancelled'); }
}