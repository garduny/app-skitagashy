document.addEventListener('DOMContentLoaded', () => { window.currentAuctionFilter = 'ending_soon'; loadAuctions('ending_soon') })
async function loadAuctions(filter) {
    window.currentAuctionFilter = filter || window.currentAuctionFilter || 'ending_soon'
    const container = document.getElementById('auctions-grid')
    const empty = document.getElementById('empty-state')
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.toggle('active', b.dataset.filter === window.currentAuctionFilter))
    container.innerHTML = `<div class="col-span-full py-24 flex justify-center"><svg class="w-12 h-12 text-red-500 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg></div>`
    empty.classList.add('hidden')
    try {
        const res = await App.post('./api/auctions/list.php', { filter: window.currentAuctionFilter })
        container.innerHTML = ''
        if (!res.status || !res.data || !res.data.length) { setStats([]); empty.classList.remove('hidden'); return }
        setStats(res.data)
        res.data.forEach(a => {
            console.log(a.image)
            let img = a.image || 'public/img/placeholder.png'
            console.log(img)
            const bid = num(a.current_bid)
            const nextBid = Math.max(bid * 1.05, bid + 1).toFixed(2)
            const timeLeft = renderTime(a.time_left)
            const reserveMet = Number(a.current_bid) >= Number(a.reserve_price || 0)
            container.innerHTML += `
<div class="auction-card rounded-2xl flex flex-col">
<a href="auctiondetail.php?id=${a.id}" class="auction-img block">
<img src=".${img}" alt="${escapeHtml(a.title)}">
<div class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-bold bg-black/55 text-white flex items-center gap-2"><span class="live-dot w-2 h-2 rounded-full bg-red-500"></span>LIVE</div>
<div class="absolute top-3 right-3 timer-badge text-white text-xs font-bold px-3 py-1 rounded-full">${timeLeft}</div>
<div class="absolute bottom-3 left-3 right-3 text-white">
<div class="text-lg font-black line-clamp-1">${escapeHtml(a.title)}</div>
<div class="text-xs text-white/70 mt-1">${escapeHtml(a.high_bidder || 'No bidder yet')}</div>
</div>
</a>
<div class="p-5 flex-1 flex flex-col">
<div class="bid-stats rounded-2xl p-4 mb-4">
<div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-2"><span>Current Bid</span><span class="${reserveMet ? 'reserve-ok' : 'reserve-no'} font-bold">${reserveMet ? 'RESERVE MET' : 'RESERVE'}</span></div>
<div class="text-3xl font-black text-red-500">${bid.toFixed(2)} <span class="text-sm text-gray-400">GASHY</span></div>
<div class="mt-2 text-xs text-gray-500">Min next: ${nextBid} GASHY</div>
</div>
<div class="mt-auto flex gap-2">
<input type="number" step="0.01" min="${nextBid}" id="bid-amount-${a.id}" placeholder="${nextBid}" class="bid-input flex-1 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white outline-none">
<button onclick="placeBid(${a.id})" class="bid-btn px-5 py-3 rounded-xl text-white font-bold">Bid</button>
</div>
</div>
</div>`
        })
    } catch (e) {
        container.innerHTML = `<div class="col-span-full text-center text-red-500 py-20 font-bold">Failed to load auctions</div>`
    }
}
function setStats(rows) {
    let ending = 0, bids = 0, reserve = 0
    rows.forEach(r => {
        if (Number(r.time_left) <= 3600) ending++
        if (Number(r.bid_count || 0) > 0) bids += Number(r.bid_count)
        if (Number(r.current_bid) >= Number(r.reserve_price || 0)) reserve++
    })
    setText('stat-live', rows.length)
    setText('stat-bids', bids)
    setText('stat-reserve', reserve)
    setText('stat-ending', ending)
}
function setText(id, val) { const el = document.getElementById(id); if (el) el.textContent = val }
function renderTime(sec) {
    sec = parseInt(sec || 0, 10)
    if (sec <= 0) return 'Ended'
    const d = Math.floor(sec / 86400)
    const h = Math.floor((sec % 86400) / 3600)
    const m = Math.floor((sec % 3600) / 60)
    const s = sec % 60
    if (d > 0) return `${d}d ${h}h`
    if (h > 0) return `${h}h ${m}m`
    return `${m}m ${s}s`
}
function num(v) { v = parseFloat(v); return isNaN(v) ? 0 : v }
function escapeHtml(str) {
    return String(str || '').replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]))
}
async function placeBid(id) {
    if (!App.checkAuth()) return
    if (!window.solana?.isPhantom) { notyf.error('Phantom wallet required'); return }
    if (!window.solana.isConnected) { try { await window.solana.connect() } catch (e) { notyf.error('Wallet connection rejected'); return } }
    const input = document.getElementById(`bid-amount-${id}`)
    const amount = parseFloat(input?.value || 0)
    if (!amount || amount <= 0) { notyf.error('Invalid amount'); return }
    try {
        notyf.success('Preparing payment...')
        const connection = new solanaWeb3.Connection("https://mainnet.helius-rpc.com/?api-key=1fca693b-e96d-42f8-94b7-ab1c5bb0c9ed", "confirmed")
        const publicKey = window.solana.publicKey
        const mint = new solanaWeb3.PublicKey("DokPYQ33k3T9S7EEesvwvuuAtoQb4pY8NWszukKwXWjv")
        const treasury = new solanaWeb3.PublicKey("GS4tXdRS7CQ5PgePt795fK2oJe5q34XBhEugNNn5AVPb")
        const mintInfo = await connection.getParsedAccountInfo(mint)
        const decimals = mintInfo.value?.data?.parsed?.info?.decimals || 9
        const rawAmount = BigInt(Math.floor(amount * Math.pow(10, decimals)))
        const buyerTokens = await connection.getParsedTokenAccountsByOwner(publicKey, { mint: mint })
        if (!buyerTokens.value.length) { notyf.error('No GASHY token account'); return }
        let fromATA = null
        for (const acc of buyerTokens.value) {
            const bal = BigInt(acc.account.data.parsed.info.tokenAmount.amount)
            if (bal >= rawAmount) { fromATA = acc.pubkey; break }
        }
        if (!fromATA) { notyf.error('Insufficient GASHY balance'); return }
        const tx = new solanaWeb3.Transaction()
        let toATA
        const treasuryTokens = await connection.getParsedTokenAccountsByOwner(treasury, { mint: mint })
        if (treasuryTokens.value.length) toATA = treasuryTokens.value[0].pubkey
        else {
            toATA = await splToken.getAssociatedTokenAddress(mint, treasury)
            tx.add(splToken.createAssociatedTokenAccountInstruction(publicKey, toATA, treasury, mint))
        }
        tx.add(splToken.createTransferInstruction(fromATA, toATA, publicKey, rawAmount))
        tx.feePayer = publicKey
        tx.recentBlockhash = (await connection.getLatestBlockhash()).blockhash
        notyf.success('Approve in Phantom...')
        const signed = await window.solana.signAndSendTransaction(tx)
        await connection.confirmTransaction(signed.signature, "confirmed")
        const res = await App.post('./api/auctions/bid.php', { auction_id: id, amount: amount, tx_signature: signed.signature })
        if (res.status) { notyf.success('Bid placed successfully'); loadAuctions(window.currentAuctionFilter) }
        else notyf.error(res.message || 'Bid failed')
    } catch (e) { notyf.error('Transaction cancelled') }
}