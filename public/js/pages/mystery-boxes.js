let mysteryBusy = false
document.addEventListener('DOMContentLoaded', () => { })

async function openBox(id, price) {
    if (mysteryBusy) return
    if (!App.checkAuth()) return
    if (!window.solana?.isPhantom) { notyf.error('Phantom wallet required'); return }
    if (!window.solana.isConnected) {
        try { await window.solana.connect() } catch (e) { notyf.error('Wallet connection rejected'); return }
    }
    mysteryBusy = true
    const btns = [...document.querySelectorAll('button[onclick*="openBox(' + id + '"]')]
    btns.forEach(b => { b.disabled = true; b.dataset.old = b.innerHTML; b.innerHTML = 'Opening...' })
    try {
        notyf.success('Preparing burn...')
        const connection = new solanaWeb3.Connection("https://mainnet.helius-rpc.com/?api-key=1fca693b-e96d-42f8-94b7-ab1c5bb0c9ed", "confirmed")
        const publicKey = window.solana.publicKey
        const mint = new solanaWeb3.PublicKey("DokPYQ33k3T9S7EEesvwvuuAtoQb4pY8NWszukKwXWjv")
        const burnWallet = new solanaWeb3.PublicKey("1nc1nerator11111111111111111111111111111111")
        const mintInfo = await connection.getParsedAccountInfo(mint)
        const decimals = mintInfo.value?.data?.parsed?.info?.decimals || 9
        const amount = parseFloat(price || 0)
        if (!amount || amount <= 0) throw new Error('Invalid price')
        const rawAmount = BigInt(Math.round(amount * Math.pow(10, decimals)))
        const buyerTokens = await connection.getParsedTokenAccountsByOwner(publicKey, { mint: mint })
        if (!buyerTokens.value.length) throw new Error('No GASHY tokens found')
        let fromATA = null
        for (const acc of buyerTokens.value) {
            const bal = BigInt(acc.account.data.parsed.info.tokenAmount.amount)
            if (bal >= rawAmount) { fromATA = acc.pubkey; break }
        }
        if (!fromATA) throw new Error('Insufficient GASHY balance')
        let toATA = null
        const tx = new solanaWeb3.Transaction()
        const burnTokens = await connection.getParsedTokenAccountsByOwner(burnWallet, { mint: mint })
        if (burnTokens.value.length) toATA = burnTokens.value[0].pubkey
        else {
            toATA = await splToken.getAssociatedTokenAddress(mint, burnWallet)
            tx.add(splToken.createAssociatedTokenAccountInstruction(publicKey, toATA, burnWallet, mint))
        }
        tx.add(splToken.createTransferInstruction(fromATA, toATA, publicKey, rawAmount))
        tx.feePayer = publicKey
        tx.recentBlockhash = (await connection.getLatestBlockhash()).blockhash
        notyf.success('Approve in Phantom...')
        const signed = await window.solana.signAndSendTransaction(tx)
        notyf.success('Waiting confirmation...')
        await connection.confirmTransaction(signed.signature, "confirmed")
        notyf.success('Opening box...')
        const res = await App.post('./api/mystery_box/open.php', { box_id: id, tx_signature: signed.signature })
        if (!res?.status) throw new Error(res?.message || 'Open failed')
        showRewardModal(res.reward, res.rarity)
        setTimeout(() => location.reload(), 3500)
    } catch (e) {
        console.error(e)
        notyf.error(e.message || 'Transaction Cancelled')
        btns.forEach(b => { b.disabled = false; if (b.dataset.old) b.innerHTML = b.dataset.old })
        mysteryBusy = false
        return
    }
    mysteryBusy = false
}

function showRewardModal(reward, rarity) {
    const old = document.getElementById('mb-reward-modal')
    if (old) old.remove()
    const map = {
        legendary: 'text-yellow-400',
        epic: 'text-purple-400',
        rare: 'text-blue-400',
        common: 'text-green-400'
    }
    let body = ''
    if (reward.type === 'product') {
        body = `<div class="text-2xl font-black mb-2">🎁 PRODUCT WON</div><div class="text-sm opacity-80 mb-3">${escapeHtml(reward.title || 'Reward Product')}</div>`
    } else {
        body = `<div class="text-2xl font-black mb-2">💰 ${Number(reward.amount || 0).toFixed(2)} GASHY</div><div class="text-sm opacity-80 mb-3">${escapeHtml(reward.note || 'Token Reward')}</div>`
    }
    const wrap = document.createElement('div')
    wrap.id = 'mb-reward-modal'
    wrap.className = 'fixed inset-0 z-[9999] bg-black/70 backdrop-blur-sm flex items-center justify-center p-4'
    wrap.innerHTML = `<div class="w-full max-w-md rounded-3xl bg-white dark:bg-[#111827] border border-white/10 p-8 text-center shadow-2xl">
<div class="text-sm uppercase tracking-[0.25em] opacity-60 mb-4">Mystery Box Opened</div>
${body}
<div class="text-lg font-black mb-6 ${map[(rarity || 'common').toLowerCase()] || ''}">${escapeHtml(String(rarity || 'common').toUpperCase())}</div>
<button id="mb-close" class="w-full py-3 rounded-2xl bg-gradient-to-r from-purple-500 to-pink-500 text-white font-black">Awesome</button>
</div>`
    document.body.appendChild(wrap)
    document.getElementById('mb-close').onclick = () => wrap.remove()
    wrap.onclick = e => { if (e.target === wrap) wrap.remove() }
}

function escapeHtml(str) {
    return String(str).replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]))
}