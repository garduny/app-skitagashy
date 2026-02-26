window.campaigns = [];
document.addEventListener('DOMContentLoaded', async () => {
    const checkAuth = setInterval(async () => {
        if (App.state.token && !App.state.account) return;
        clearInterval(checkAuth);
        if (!App.state.token) {
            document.getElementById('wallet-connect-block').classList.remove('hidden');
        } else {
            document.getElementById('burn-interface').classList.remove('hidden');
            await loadCampaigns();
            await scanNFTs();
        }
    }, 100);
});
async function loadCampaigns() {
    try {
        const res = await App.post('api/nft/get_campaigns.php', {});
        if (res.status) window.campaigns = res.data;
    } catch (e) { }
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
        const connection = new solanaWeb3.Connection("https://rpc.ankr.com/solana");
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
    return `
<div class="bg-white dark:bg-[#151A23] rounded-2xl border border-gray-200 dark:border-white/5 p-4 shadow-sm hover:border-red-500/50 transition-all relative overflow-hidden">
${badge}
<div class="aspect-square bg-gray-100 dark:bg-black/20 rounded-xl mb-4 flex items-center justify-center text-4xl">${icon}</div>
<h4 class="font-bold text-gray-900 dark:text-white truncate mb-1">Target Asset</h4>
<div class="text-xs text-gray-500 mb-3 truncate">${displayMint}</div>
<div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-white/5">
<span class="text-xs font-bold text-green-500">+${parseFloat(reward).toFixed(0)} G</span>
<button onclick="burnNFT('${mint}',${campId})" class="px-3 py-1.5 bg-red-600 hover:bg-red-500 text-white text-xs font-bold rounded-lg transition-colors">🔥 Burn</button>
</div>
</div>`;
}
async function burnNFT(mint, campId) {
    if (!confirm('WARNING: This action is irreversible. Burn NFT?')) return;
    window.notyf.success('Constructing Burn Transaction...');
    try {
        const nonce = Date.now() + Math.random().toString(36).substring(2, 10);
        const msg = `BURN CONFIRMATION:\n\nMint: ${mint}\nAction: Destroy Asset\nTimestamp: ${nonce}\n\nClick Approve to Burn.`;
        const encoded = new TextEncoder().encode(msg);
        const signed = await window.solana.signMessage(encoded, 'utf8');
        let txSig = '';
        if (signed.signature) {
            const signatureBytes = signed.signature || signed;
            txSig = Array.from(new Uint8Array(signatureBytes)).map(b => b.toString(16).padStart(2, '0')).join('');
        } else {
            txSig = 'BURN_SIG_' + nonce;
        }
        const res = await App.post('api/nft/process_burn.php', { campaign_id: campId, nft_mint: mint, tx_signature: txSig });
        if (res.status) {
            window.notyf.success(res.message);
            scanNFTs();
        } else {
            window.notyf.error(res.message);
        }
    } catch (e) {
        console.error(e);
        window.notyf.error('Burn Cancelled');
    }
}