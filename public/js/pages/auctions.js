document.addEventListener('DOMContentLoaded', () => { loadAuctions('ending_soon'); });
async function loadAuctions(filter) {
    const container = document.getElementById('auctions-grid');
    const emptyState = document.getElementById('empty-state');
    document.querySelectorAll('.filter-btn').forEach(btn => { if (btn.dataset.filter === filter) { btn.classList.add('bg-red-500/20', 'text-red-400', 'border-red-500/50'); btn.classList.remove('bg-white/5', 'text-gray-400', 'border-white/10'); } else { btn.classList.remove('bg-red-500/20', 'text-red-400', 'border-red-500/50'); btn.classList.add('bg-white/5', 'text-gray-400', 'border-white/10'); } });
    container.innerHTML = `<div class="col-span-full text-center py-12"><svg class="w-10 h-10 text-red-500 mx-auto animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>`;
    emptyState.classList.add('hidden');
    try {
        const res = await App.post('api/auctions/list.php', { filter: filter });
        container.innerHTML = '';
        if (res.status && res.data && res.data.length > 0) {
            res.data.forEach(a => {
                let img = 'assets/placeholder.png';
                try { img = JSON.parse(a.images)[0] || img; } catch (e) { }
                const timeLeft = calculateTimeLeft(a.time_left);
                const html = `<div class="group bg-white dark:bg-[#151A23] rounded-2xl border border-gray-200 dark:border-white/5 overflow-hidden hover:border-red-500/50 transition-all duration-300 flex flex-col hover:-translate-y-1 shadow-lg"><div class="relative h-64 overflow-hidden"><img src="./${img}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"><div class="absolute inset-0 bg-gradient-to-t from-[#151A23] via-transparent to-transparent"></div><div class="absolute top-3 right-3 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded flex items-center gap-1 shadow-lg animate-pulse"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>${timeLeft}</span></div></div><div class="p-5 flex-1 flex flex-col"><h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1 truncate">${a.title}</h3><p class="text-xs text-gray-500 mb-4 line-clamp-2">Current Bid</p><div class="flex items-center justify-between mb-4"><span class="text-2xl font-black text-red-500">${parseFloat(a.current_bid).toFixed(2)} <span class="text-sm text-gray-400">G</span></span></div><div class="mt-auto flex gap-2"><input type="number" id="bid-amount-${a.id}" placeholder="${(parseFloat(a.current_bid) * 1.05).toFixed(2)}" class="flex-1 bg-gray-100 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-lg px-3 text-gray-900 dark:text-white text-sm focus:border-red-500 outline-none"><button onclick="placeBid(${a.id})" class="px-6 py-2 bg-red-600 hover:bg-red-500 text-white font-bold rounded-lg shadow-lg shadow-red-600/20 transition-all">Bid</button></div></div></div>`;
                container.innerHTML += html;
            });
        } else emptyState.classList.remove('hidden');
    } catch (e) { container.innerHTML = `<div class="col-span-full text-center text-red-500">Failed to load auctions</div>`; }
}
function calculateTimeLeft(seconds) {
    if (seconds <= 0) return "Ended";
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    const s = seconds % 60;
    return `${h}h ${m}m ${s}s`;
}
async function placeBid(id) {
    if (!App.checkAuth()) return;
    if (!window.solana?.isPhantom) { window.notyf.error('Phantom wallet required'); return; }
    if (!window.solana.isConnected) { try { await window.solana.connect(); } catch (err) { window.notyf.error('Wallet connection rejected'); return; } }
    const input = document.getElementById(`bid-amount-${id}`);
    const amount = parseFloat(input ? input.value : 0);
    if (!amount || amount <= 0) return window.notyf.error('Invalid amount');
    try {
        window.notyf.success('Preparing payment...');
        const connection = new solanaWeb3.Connection("https://mainnet.helius-rpc.com/?api-key=1fca693b-e96d-42f8-94b7-ab1c5bb0c9ed", "confirmed");
        const publicKey = window.solana.publicKey;
        const mint = new solanaWeb3.PublicKey("DokPYQ33k3T9S7EEesvwvuuAtoQb4pY8NWszukKwXWjv");
        const treasury = new solanaWeb3.PublicKey("GS4tXdRS7CQ5PgePt795Fk2oJe5q34XBhEugNn5AVPb");
        const mintInfo = await connection.getParsedAccountInfo(mint);
        const decimals = mintInfo.value?.data?.parsed?.info?.decimals || 9;
        const rawAmount = BigInt(Math.round(amount * Math.pow(10, decimals)));
        const tokenAccounts = await connection.getParsedTokenAccountsByOwner(publicKey, { mint: mint });
        if (tokenAccounts.value.length === 0) { window.notyf.error('No GASHY tokens found in your wallet!'); return; }
        const fromATA = tokenAccounts.value[0].pubkey;
        const balanceVal = BigInt(tokenAccounts.value[0].account.data.parsed.info.tokenAmount.amount);
        if (balanceVal < rawAmount) { window.notyf.error('Insufficient GASHY balance'); return; }
        const toATA = await splToken.getAssociatedTokenAddress(mint, treasury);
        const tx = new solanaWeb3.Transaction();
        const treasuryInfo = await connection.getAccountInfo(toATA);
        if (!treasuryInfo) tx.add(splToken.createAssociatedTokenAccountInstruction(publicKey, toATA, treasury, mint));
        tx.add(splToken.createTransferInstruction(fromATA, toATA, publicKey, rawAmount));
        tx.feePayer = publicKey;
        tx.recentBlockhash = (await connection.getLatestBlockhash()).blockhash;
        window.notyf.success('Approve transaction in Phantom...');
        const signed = await window.solana.signAndSendTransaction(tx);
        await connection.confirmTransaction(signed.signature, "confirmed");
        const res = await App.post('api/auctions/bid.php', { auction_id: id, amount: amount, tx_signature: signed.signature });
        if (res.status) { window.notyf.success(`Bid placed!`); loadAuctions('ending_soon'); } else window.notyf.error(res.message);
    } catch (e) { window.notyf.error('Bid Cancelled'); }
}