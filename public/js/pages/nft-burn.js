document.addEventListener('DOMContentLoaded', async () => {
    const checkAuth = setInterval(async () => {
        if (App.state.token && !App.state.account) return;
        clearInterval(checkAuth);
        if (!App.state.token) {
            window.location.href = 'app.php';
            return;
        }
        loadOwnedNFTs();
    }, 100);
});
async function loadOwnedNFTs() {
    try {
        const res = await App.post('./api/nft/get_my_mints.php', {});
        document.getElementById('burn-loader').classList.add('hidden');
        if (res.status && res.data.length > 0) {
            document.getElementById('burn-grid').classList.remove('hidden');
            document.getElementById('burn-grid').innerHTML = res.data.map(n => `
<div class="bg-white dark:bg-[#151A23] p-4 rounded-2xl border border-gray-200 dark:border-white/5 shadow-sm text-center">
<div class="aspect-square bg-gray-100 dark:bg-black/20 rounded-xl mb-4"><img src="${n.image_uri}" class="w-full h-full object-cover rounded-xl"></div>
<h4 class="font-bold text-gray-900 dark:text-white truncate">${n.collection_name} #${n.mint_number}</h4>
<div class="text-xs text-gray-500 mb-4 font-mono truncate">${n.mint_address}</div>
<button onclick="burnMyNft('${n.mint_address}')" class="w-full py-2 bg-red-600 hover:bg-red-500 text-white font-bold rounded-lg transition-colors">🔥 Burn for ${(parseFloat(n.mint_price) * 0.5).toFixed(2)} G</button>
</div>`).join('');
        } else document.getElementById('burn-empty').classList.remove('hidden');
    } catch (e) { }
}
async function burnMyNft(mint) {
    if (!confirm('Burn this NFT forever?')) return;
    if (!window.solana?.isPhantom) {
        window.notyf.error('Phantom wallet required');
        return;
    }
    if (!window.solana.isConnected) {
        try {
            await window.solana.connect();
        } catch (e) {
            window.notyf.error('Wallet connection rejected');
            return;
        }
    }
    window.notyf.success('Constructing burn...');
    try {
        const connection = new solanaWeb3.Connection("https://mainnet.helius-rpc.com/?api-key=1fca693b-e96d-42f8-94b7-ab1c5bb0c9ed", "confirmed");
        const publicKey = window.solana.publicKey;
        const mintKey = new solanaWeb3.PublicKey(mint);
        const ownerATA = await splToken.getAssociatedTokenAddress(mintKey, publicKey);
        const data = new Uint8Array(9);
        const view = new DataView(data.buffer);
        view.setUint8(0, 8);
        view.setBigUint64(1, BigInt(1), true);
        const burnIx = new solanaWeb3.TransactionInstruction({
            keys: [{
                pubkey: ownerATA,
                isSigner: false,
                isWritable: true
            }, {
                pubkey: mintKey,
                isSigner: false,
                isWritable: true
            }, {
                pubkey: publicKey,
                isSigner: true,
                isWritable: false
            }],
            programId: TOKEN_PROGRAM_ID,
            data: data
        });
        const tx = new solanaWeb3.Transaction().add(burnIx);
        tx.feePayer = publicKey;
        tx.recentBlockhash = (await connection.getLatestBlockhash()).blockhash;
        window.notyf.success('Approve in Phantom...');
        const signed = await window.solana.signAndSendTransaction(tx);
        await connection.confirmTransaction(signed.signature, "confirmed");
        const res = await App.post('./api/nft/burn.php', {
            nft_mint: mint,
            tx_signature: signed.signature
        });
        if (res.status) {
            window.notyf.success(res.message);
            setTimeout(() => location.reload(), 1500);
        } else window.notyf.error(res.message);
    } catch (e) {
        window.notyf.error('Burn Cancelled');
    }
}