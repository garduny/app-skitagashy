let _lotteryBuying = false

async function loadLotteryInfo() {
    try {
        const res = await App.post('./api/lottery/info.php', {})
        if (!res.status || !res.round) return

        const r = res.round

        const totalEl = document.getElementById('totalTickets')
        const myEl = document.getElementById('myTickets')
        const poolEl = document.getElementById('prizePool')
        const timeEl = document.getElementById('timeLeft')

        if (totalEl) totalEl.innerText = r.total_tickets || 0
        if (myEl) myEl.innerText = res.account_entries || 0
        if (poolEl) poolEl.innerText = (r.prize_pool || 0)
        if (timeEl) timeEl.innerText = formatTime(r.time_left || 0)

    } catch (e) {
        console.error(e)
    }
}

function formatTime(sec) {
    sec = parseInt(sec || 0)
    if (sec <= 0) return '0s'
    const d = Math.floor(sec / 86400)
    const h = Math.floor((sec % 86400) / 3600)
    const m = Math.floor((sec % 3600) / 60)
    const s = sec % 60
    return `${d}d ${h}h ${m}m ${s}s`
}

async function buyTickets(rid) {
    if (_lotteryBuying) return
    if (!App.checkAuth()) return

    if (!window.solana?.isPhantom) {
        notyf.error('Phantom wallet required')
        return
    }

    const qtyInput = document.getElementById('ticket-qty')
    const buyBtn = document.querySelector(`[onclick="buyTickets(${rid})"]`) || document.getElementById('buy-btn')

    try {

        const info = await App.post('./api/lottery/info.php', {})

        if (!info.status || !info.round || !info.round.id) {
            notyf.error('No active lottery round')
            return
        }

        if (parseInt(info.round.id) !== parseInt(rid)) {
            notyf.error('Lottery round changed, refresh page')
            setTimeout(() => location.reload(), 800)
            return
        }

        if ((parseInt(info.round.time_left) || 0) <= 10) {
            notyf.error('Lottery closing, wait next round')
            return
        }

        let qty = parseInt(qtyInput ? qtyInput.value : 1, 10)

        if (!qty || qty < 1) {
            notyf.error('Invalid Quantity')
            return
        }

        _lotteryBuying = true
        if (buyBtn) buyBtn.disabled = true
        if (qtyInput) qtyInput.disabled = true

        if (!window.solana.isConnected) {
            try {
                await window.solana.connect()
            } catch (e) {
                notyf.error('Wallet connection rejected')
                return
            }
        }

        notyf.success('Preparing payment...')

        const connection = new solanaWeb3.Connection("https://mainnet.helius-rpc.com/?api-key=1fca693b-e96d-42f8-94b7-ab1c5bb0c9ed", "confirmed")
        const publicKey = window.solana.publicKey

        const mint = new solanaWeb3.PublicKey("DokPYQ33k3T9S7EEesvwvuuAtoQb4pY8NWszukKwXWjv")
        const treasury = new solanaWeb3.PublicKey("GS4tXdRS7CQ5PgePt795fK2oJe5q34XBhEugNNn5AVPb")

        const mintInfo = await connection.getParsedAccountInfo(mint)
        const decimals = mintInfo.value?.data?.parsed?.info?.decimals || 9

        const pricePerTicket = Number(window.GASHY_TICKET_PRICE || 10)
        const totalGashy = pricePerTicket * qty

        if (!totalGashy || totalGashy <= 0) {
            notyf.error('Invalid price')
            return
        }

        const rawAmount = BigInt(Math.round(totalGashy * Math.pow(10, decimals)))

        const buyerTokens = await connection.getParsedTokenAccountsByOwner(publicKey, { mint: mint })

        if (buyerTokens.value.length === 0) {
            notyf.error('No GASHY tokens found')
            return
        }

        const fromATA = buyerTokens.value[0].pubkey
        const balanceVal = BigInt(buyerTokens.value[0].account.data.parsed.info.tokenAmount.amount)

        if (balanceVal < rawAmount) {
            notyf.error('Insufficient GASHY balance')
            return
        }

        let toATA
        const tx = new solanaWeb3.Transaction()

        const treasuryTokens = await connection.getParsedTokenAccountsByOwner(treasury, { mint: mint })

        if (treasuryTokens.value.length > 0) toATA = treasuryTokens.value[0].pubkey
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

        const res = await App.post('./api/lottery/enter.php', {
            round_id: rid,
            tickets: qty,
            burn_tx: signed.signature
        })

        if (res.status) {
            notyf.success(`${qty} Tickets Secured!`)
            await loadLotteryInfo()
        } else {
            notyf.error(res.message || 'Failed to enter lottery')
        }

    } catch (e) {
        console.error(e)
        notyf.error(e?.message || 'Transaction failed')
    } finally {
        _lotteryBuying = false
        if (buyBtn) buyBtn.disabled = false
        if (qtyInput) qtyInput.disabled = false
    }
}

document.addEventListener('DOMContentLoaded', () => {
    loadLotteryInfo()
    setInterval(loadLotteryInfo, 10000)
})