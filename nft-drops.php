<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
$drops = getQuery(" SELECT * FROM nft_drops WHERE status='approved' ORDER BY start_time ASC ");
?>
<main class="ml-0 lg:ml-64 pt-20 p-6 min-h-screen transition-all duration-300 bg-gray-50 dark:bg-[#060709] text-gray-900 dark:text-white">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12">
            <div class="inline-flex px-3 py-1 rounded-full bg-blue-500/10 text-blue-500 text-xs font-bold uppercase mb-4">Launchpad</div>
            <h1 class="text-4xl font-black mb-4">NFT Drops</h1>
            <p class="text-gray-500">Mint exclusive collections directly to your wallet using $GASHY.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($drops as $d): $pct = ($d['minted_count'] / $d['max_supply']) * 100;
                $live = time() >= strtotime($d['start_time']) && time() <= strtotime($d['end_time']);
                $soldout = $d['minted_count'] >= $d['max_supply']; ?>
                <div class="bg-white dark:bg-[#151A23] rounded-3xl border border-gray-200 dark:border-white/5 overflow-hidden shadow-lg flex flex-col">
                    <div class="relative h-64"><img src="<?= $d['image_uri'] ?>" class="w-full h-full object-cover">
                        <div class="absolute top-4 right-4 px-3 py-1 rounded-lg text-xs font-bold uppercase <?= $soldout ? 'bg-red-500 text-white' : ($live ? 'bg-green-500 text-white animate-pulse' : 'bg-gray-800 text-white') ?>"><?= $soldout ? 'Sold Out' : ($live ? 'Live' : 'Upcoming') ?></div>
                    </div>
                    <div class="p-6 flex-1 flex flex-col">
                        <h3 class="text-2xl font-black mb-1"><?= $d['collection_name'] ?> <span class="text-sm text-gray-500 font-mono"><?= $d['symbol'] ?></span></h3>
                        <p class="text-sm text-gray-500 mb-6 line-clamp-2"><?= $d['description'] ?></p>
                        <div class="space-y-2 mb-6">
                            <div class="flex justify-between text-xs font-bold"><span>Mints</span><span><?= $d['minted_count'] ?> / <?= $d['max_supply'] ?></span></div>
                            <div class="w-full h-2 bg-gray-100 dark:bg-black/30 rounded-full">
                                <div class="h-full bg-blue-500" style="width:<?= $pct ?>%"></div>
                            </div>
                        </div>
                        <div class="mt-auto">
                            <div class="flex justify-between items-center mb-4"><span class="text-xs text-gray-500 uppercase font-bold">Price</span><span class="text-xl font-black text-green-500"><?= number_format($d['price_gashy']) ?> G</span></div>
                            <button onclick="mintDrop(<?= $d['id'] ?>,<?= $d['price_gashy'] ?>)" <?= ($soldout || !$live) ? 'disabled' : '' ?> class="w-full py-3 rounded-xl font-bold transition-all <?= $soldout || !$live ? 'bg-gray-200 dark:bg-white/5 text-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-500 text-white shadow-lg shadow-blue-600/20' ?>"><?= $soldout ? 'Sold Out' : ($live ? 'Mint NFT' : 'Starts ' . date('M d', strtotime($d['start_time']))) ?></button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>
<script>
    async function mintDrop(id, price) {
        if (!App.checkAuth()) return;
        window.notyf.success('Approve transaction in wallet...');
        try {
            const nonce = Date.now();
            const msg = `MINT NFT DROP:\n\nDrop ID: #${id}\nCost: ${price} GASHY\nNonce: ${nonce}\n\nClick Approve to mint.`;
            const encoded = new TextEncoder().encode(msg);
            const signed = await window.solana.signMessage(encoded, 'utf8');
            let txSig = '';
            if (signed.signature) {
                const sb = signed.signature || signed;
                txSig = Array.from(new Uint8Array(sb)).map(b => b.toString(16).padStart(2, '0')).join('');
            } else {
                txSig = 'MINT_SIG_' + nonce;
            }
            const res = await App.post('./api/nft/mint.php', {
                drop_id: id,
                tx_signature: txSig
            });
            if (res.status) {
                window.notyf.success(res.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                window.notyf.error(res.message);
            }
        } catch (e) {
            window.notyf.error('Mint Cancelled');
        }
    }
</script>
<script src="public/js/pages/nft-drops.js"></script>
<?php require_once 'footer.php'; ?>