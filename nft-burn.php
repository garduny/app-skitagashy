<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300 bg-gray-50 dark:bg-[#060709] text-gray-900 dark:text-white">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-black mb-4">NFT <span class="text-red-500">Incinerator</span></h1>
            <p class="text-gray-500">Burn your minted NFTs to reclaim 50% of the GASHY value.</p>
        </div>
        <div id="burn-loader" class="text-center py-20"><svg class="w-12 h-12 text-red-500 mx-auto animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg></div>
        <div id="burn-grid" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"></div>
        <div id="burn-empty" class="hidden text-center py-20 bg-white dark:bg-[#151A23] rounded-3xl border border-gray-200 dark:border-white/5">
            <p class="text-gray-500">You do not own any active NFTs.</p>
        </div>
    </div>
</main>
<script>
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
<button onclick="burnMyNft('${n.mint_address}')" class="w-full py-2 bg-red-600 hover:bg-red-500 text-white font-bold rounded-lg transition-colors">🔥 Burn for ${(parseFloat(n.mint_price)*0.5).toFixed(2)} G</button>
</div>`).join('');
            } else {
                document.getElementById('burn-empty').classList.remove('hidden');
            }
        } catch (e) {}
    }
    async function burnMyNft(mint) {
        if (!confirm('Burn this NFT forever?')) return;
        window.notyf.success('Approve burn transaction...');
        try {
            const nonce = Date.now();
            const msg = `BURN NFT:\n\nMint: ${mint}\nNonce: ${nonce}\n\nApprove to destroy and claim GASHY.`;
            const encoded = new TextEncoder().encode(msg);
            const signed = await window.solana.signMessage(encoded, 'utf8');
            let txSig = '';
            if (signed.signature) {
                const sb = signed.signature || signed;
                txSig = Array.from(new Uint8Array(sb)).map(b => b.toString(16).padStart(2, '0')).join('');
            } else {
                txSig = 'BURN_SIG_' + nonce;
            }
            const res = await App.post('./api/nft/burn.php', {
                nft_mint: mint,
                tx_signature: txSig
            });
            if (res.status) {
                window.notyf.success(res.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                window.notyf.error(res.message);
            }
        } catch (e) {
            window.notyf.error('Burn Cancelled');
        }
    }
</script>
<?php require_once 'footer.php'; ?>