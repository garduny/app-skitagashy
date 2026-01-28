async function placeBid(id) {
    if (!App.checkAuth()) return;
    const input = document.getElementById(`bid-amount-${id}`);
    const amount = input ? input.value : 0;
    if (!amount || amount <= 0) return notyf.error('Invalid amount');
    notyf.success('Please sign the bid transaction...');
    try {
        const message = `CONFIRM BID:\n\nAction: Place Bid\nAmount: ${amount} GASHY\nAuction ID: ${id}\n\nClick Approve to confirm.`;
        const encoded = new TextEncoder().encode(message);
        const signed = await window.solana.signMessage(encoded, 'utf8');
        let txSig = '';
        if (signed.signature) {
            txSig = Array.from(new Uint8Array(signed.signature)).map(b => b.toString(16).padStart(2, '0')).join('');
        } else {
            txSig = 'MANUAL_BID_' + Date.now();
        }
        const res = await App.post('api/auctions/bid.php', { auction_id: id, amount: amount });
        if (res.status) {
            notyf.success(`Bid of ${amount} GASHY placed!`);
            setTimeout(() => location.reload(), 1000);
        } else {
            notyf.error(res.message);
        }
    } catch (e) {
        console.error(e);
        notyf.error('Bid Rejected by Account');
    }
}