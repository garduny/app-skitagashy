async function openBox(id) {
    if (!App.checkAuth()) return;
    notyf.success('Please sign transaction to open box...');
    try {
        const nonce = Date.now() + Math.random().toString(36).substring(2, 10);
        const message = `CONFIRM MYSTERY BOX:\n\nAction: Open Box #${id}\nCost: Standard Rate (e.g. 500 GASHY)\nTimestamp: ${nonce}\n\nTokens will be burned. Click Approve.`;
        const encoded = new TextEncoder().encode(message);
        const signed = await window.solana.signMessage(encoded, 'utf8');
        let txSig = '';
        if (signed.signature) {
            txSig = Array.from(new Uint8Array(signed.signature)).map(b => b.toString(16).padStart(2, '0')).join('');
        } else {
            txSig = 'MANUAL_BOX_' + nonce;
        }
        notyf.success('Unlocking Box...');
        const res = await App.post('./api/mystery_box/open.php', { box_id: id, tx_signature: txSig });
        if (res.status) {
            alert(`🎉 REWARD: ${res.reward.type.toUpperCase()}\nRarity: ${res.rarity}`);
            location.reload();
        } else {
            notyf.error(res.message);
        }
    } catch (e) {
        console.error(e);
        notyf.error('Transaction Cancelled');
    }
}