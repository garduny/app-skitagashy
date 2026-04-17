let __gashyBuying = false;

async function buyNow(pid) {
    if (__gashyBuying) return;
    const btn = document.querySelector('.buy-btn');
    const qtyInput = document.getElementById('qty');
    const attrInputs = document.querySelectorAll('[data-attribute-required="1"],select[data-attribute-required="1"],input[data-attribute-required="1"]');
    for (const el of attrInputs) {
        if (!String(el.value || '').trim()) {
            notyf.error('Please select all required options');
            el.focus();
            return;
        }
    }
    const sellerId = Number(window.GASHY_PRODUCT_SELLER_ID || 0);
    let me = 0;
    try {
        if (App?.user?.account_id) me = Number(App.user.account_id);
        else if (App?.account_id) me = Number(App.account_id);
        else {
            const sess = localStorage.getItem('kita_session');
            if (sess) {
                const prof = await App.request?.('account/profile.php').catch(() => null);
                if (prof?.account_id) me = Number(prof.account_id);
            }
        }
    } catch (e) { }
    if (sellerId && me && sellerId === me) {
        notyf.error('You cannot buy your own product');
        return;
    }
    if (!App.checkAuth()) return;
    if (!window.solana?.isPhantom) {
        notyf.error('Phantom wallet required');
        return;
    }
    let maxQty = parseInt(qtyInput?.max || 1, 10) || 1;
    let qty = parseInt(qtyInput?.value || 1, 10) || 1;
    if (qty < 1) qty = 1;
    if (qty > maxQty) qty = maxQty;
    if (qtyInput) qtyInput.value = qty;
    const attrs = {};
    document.querySelectorAll('[data-attribute-key]').forEach(el => {
        attrs[String(el.dataset.attributeKey)] = String(el.value || '').trim();
    });
    try {
        __gashyBuying = true;
        if (btn) {
            btn.disabled = true;
            btn.dataset.old = btn.innerHTML;
            btn.innerHTML = 'Processing...';
        }
        if (!window.solana.isConnected) {
            try {
                await window.solana.connect();
            } catch (err) {
                throw new Error('Wallet connection rejected');
            }
        }
        notyf.success('Preparing payment...');
        const connection = new solanaWeb3.Connection("https://mainnet.helius-rpc.com/?api-key=1fca693b-e96d-42f8-94b7-ab1c5bb0c9ed", "confirmed");
        const publicKey = window.solana.publicKey;
        const mint = new solanaWeb3.PublicKey("DokPYQ33k3T9S7EEesvwvuuAtoQb4pY8NWszukKwXWjv");
        const treasury = new solanaWeb3.PublicKey("GS4tXdRS7CQ5PgePt795fK2oJe5q34XBhEugNNn5AVPb");
        const mintInfo = await connection.getParsedAccountInfo(mint);
        const decimals = mintInfo.value?.data?.parsed?.info?.decimals || 9;
        const pricePerItem = Number(window.GASHY_PRICE || 0);
        const totalGashy = pricePerItem * qty;
        if (!totalGashy || totalGashy <= 0) throw new Error('Invalid price');
        const rawAmount = BigInt(Math.floor(totalGashy * Math.pow(10, decimals)));
        const buyerTokens = await connection.getParsedTokenAccountsByOwner(publicKey, { mint: mint });
        if (!buyerTokens.value.length) throw new Error('No GASHY tokens found in your wallet');
        let fromATA = null;
        for (const acc of buyerTokens.value) {
            const bal = BigInt(acc.account.data.parsed.info.tokenAmount.amount);
            if (bal >= rawAmount) {
                fromATA = acc.pubkey;
                break;
            }
        }
        if (!fromATA) throw new Error('Insufficient GASHY balance');
        let toATA;
        const tx = new solanaWeb3.Transaction();
        const treasuryTokens = await connection.getParsedTokenAccountsByOwner(treasury, { mint: mint });
        if (treasuryTokens.value.length) {
            toATA = treasuryTokens.value[0].pubkey;
        } else {
            toATA = await splToken.getAssociatedTokenAddress(mint, treasury);
            tx.add(splToken.createAssociatedTokenAccountInstruction(publicKey, toATA, treasury, mint));
        }
        tx.add(splToken.createTransferInstruction(fromATA, toATA, publicKey, rawAmount));
        tx.feePayer = publicKey;
        tx.recentBlockhash = (await connection.getLatestBlockhash()).blockhash;
        notyf.success('Approve in Phantom...');
        const signed = await window.solana.signAndSendTransaction(tx);
        if (!signed?.signature) throw new Error('Transaction cancelled');
        notyf.success('Confirming payment...');
        await connection.confirmTransaction(signed.signature, "confirmed");
        const payload = {
            items: [{ id: pid, qty: qty, attributes: attrs }],
            tx_signature: signed.signature
        };
        const res = await App.post('./api/orders/create.php', payload);
        if (res && res.status) {
            notyf.success('Order Successful!');
            setTimeout(() => location.href = 'orders.php', 800);
        } else {
            throw new Error(res?.message || 'Order failed');
        }
    } catch (e) {
        console.error(e);
        if (String(e.message).includes("0x1")) notyf.error('Insufficient SOL for gas fees');
        else notyf.error(e.message || 'Transaction failed');
    } finally {
        __gashyBuying = false;
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = btn.dataset.old || 'Buy Now';
        }
    }
}

async function shareProduct() {
    try {
        const url = location.href;
        const title = document.title || 'GASHY Product';
        if (navigator.share) {
            await navigator.share({ title: title, url: url });
        } else {
            await navigator.clipboard.writeText(url);
            notyf.success('Product link copied');
        }
    } catch (e) { }
}