window.campaigns = [];
document.addEventListener('DOMContentLoaded', async () => {
    const checkAuth = setInterval(async () => {
        if (App.state.token && !App.state.account) return;
        clearInterval(checkAuth);
        if (!App.state.token) { document.getElementById('wallet-connect-block').classList.remove('hidden'); }
        else { document.getElementById('burn-interface').classList.remove('hidden'); await loadCampaigns(); await scanNFTs(); }
    }, 100);
});
async function loadCampaigns() {
    try { const res = await App.post('./api/nft/get_campaigns.php', {}); if (res.status) window.campaigns = res.data; } catch (e) { }
}
async function scanNFTs() {
    if (!App.state.account || !App.state.account.wallet_address) return;
    const grid = document.getElementById('nft-grid');
    const loader = document.getElementById('nft-loader');
    const none = document.getElementById('no-nfts');
    grid.innerHTML = '';
    loader.classList.remove('hidden');
    none.classList.add('hidden');
    let found = 0;
    try {
        const connection = new solanaWeb3.Connection("https://mainnet.helius-rpc.com/?api-key=1fca693b-e96d-42f8-94b7-ab1c5bb0c9ed", "confirmed");
        const pubkey = new solanaWeb3.PublicKey(App.state.account.wallet_address);
        const tokens = await connection.getParsedTokenAccountsByOwner(pubkey, { programId: new solanaWeb3.PublicKey("TokenkegQfeZyiNwAJbNbGKPFXCWuBvf9Ss623VQ5DA") });
        for (let t of tokens.value) {
            const amount = t.account.data.parsed.info.tokenAmount.uiAmount;
            const decimals = t.account.data.parsed.info.tokenAmount.decimals;
            const mint = t.account.data.parsed.info.mint;
            if (amount === 1 && decimals === 0 && window.campaigns.length > 0) {
                const camp = window.campaigns[0];
                found++;
                grid.innerHTML += renderCard(mint, camp.id, camp.reward_amount, false);
            }
        }
    } catch (e) { console.warn("RPC Error:", e); }
    if (found === 0 && window.campaigns.length > 0) {
        const camp = window.campaigns[0];
        const mockMint = "MOCK_NFT_" + Date.now();
        grid.innerHTML += renderCard(mockMint, camp.id, camp.reward_amount, true);
        found++;
    }
    loader.classList.add('hidden');
    if (found === 0) none.classList.remove('hidden');
}
function renderCard(mint, campId, reward, isMock) {
    const displayMint = isMock ? "Simulated Item" : "NFT ..." + mint.substring(0, 4);
    const badge = isMock ? '<div class="absolute top-0 right-0 bg-yellow-500 text-black text-[9px] font-bold px-2 py-0.5">TEST MODE</div>' : '';
    const icon = isMock ? '🧪' : '🖼️';
    return `<div class="bg-white dark:bg-[#151A23] rounded-2xl border border-gray-200 dark:border-white/5 p-4 shadow-sm hover:border-red-500/50 transition-all relative overflow-hidden">${badge}<div class="aspect-square bg-gray-100 dark:bg-black/20 rounded-xl mb-4 flex items-center justify-center text-4xl">${icon}</div><h4 class="font-bold text-gray-900 dark:text-white truncate mb-1">Target Asset</h4><div class="text-xs text-gray-500 mb-3 truncate">${displayMint}</div><div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-white/5"><span class="text-xs font-bold text-green-500">+${parseFloat(reward).toFixed(0)} G</span><button onclick="burnNFT('${mint}',${campId})" class="px-3 py-1.5 bg-red-600 hover:bg-red-500 text-white text-xs font-bold rounded-lg transition-colors">🔥 Burn</button></div></div>`;
}
async function burnNFT(mint, campId) {
    if (!confirm('WARNING: This action is irreversible. Burn NFT?')) return;
    if (!window.solana?.isPhantom) { window.notyf.error('Phantom wallet required'); return; }
    if (!window.solana.isConnected) { try { await window.solana.connect(); } catch (e) { window.notyf.error('Wallet connection rejected'); return; } }
    window.notyf.success('Constructing Burn Transaction...');
    try {
        const connection = new solanaWeb3.Connection("https://mainnet.helius-rpc.com/?api-key=1fca693b-e96d-42f8-94b7-ab1c5bb0c9ed", "confirmed");
        const publicKey = window.solana.publicKey;
        const mintKey = new solanaWeb3.PublicKey(mint);
        const ownerATA = await splToken.getAssociatedTokenAddress(mintKey, publicKey);
        const data = new Uint8Array(9);
        const view = new DataView(data.buffer);
        view.setUint8(0, 8);
        view.setBigUint64(1, BigInt(1), true);
        const burnIx = new solanaWeb3.TransactionInstruction({ keys: [{ pubkey: ownerATA, isSigner: false, isWritable: true }, { pubkey: mintKey, isSigner: false, isWritable: true }, { pubkey: publicKey, isSigner: true, isWritable: false }], programId: TOKEN_PROGRAM_ID, data: data });
        const tx = new solanaWeb3.Transaction().add(burnIx);
        tx.feePayer = publicKey;
        tx.recentBlockhash = (await connection.getLatestBlockhash()).blockhash;
        const signed = await window.solana.signAndSendTransaction(tx);
        await connection.confirmTransaction(signed.signature, "confirmed");
        const res = await App.post('./api/nft/process_burn.php', { campaign_id: campId, nft_mint: mint, tx_signature: signed.signature });
        if (res.status) { window.notyf.success(res.message); scanNFTs(); } else window.notyf.error(res.message);
    } catch (e) { console.error(e); window.notyf.error('Burn Cancelled'); }
}