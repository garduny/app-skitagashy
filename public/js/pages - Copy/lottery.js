async function buyTickets(rid) {
    if (!App.checkAuth()) return;

    const qtyInput = document.getElementById('ticket-qty');
    const qty = qtyInput ? qtyInput.value : 1;

    if (qty < 1) return notyf.error('Invalid Quantity');

    notyf.success('Please approve ticket purchase...');

    try {
        const nonce = Date.now();
        const message = `CONFIRM LOTTERY ENTRY:\n\nRound ID: ${rid}\nTickets: ${qty}\nCost: ${qty * 10} GASHY\nTimestamp: ${nonce}\n\nClick Approve to join pool.`;
        const encoded = new TextEncoder().encode(message);
        const signed = await window.solana.signMessage(encoded, 'utf8');
        const signatureBytes = signed.signature || signed;
        const txSig = Array.from(new Uint8Array(signatureBytes)).map(b => b.toString(16).padStart(2, '0')).join('');
        const res = await App.post('./api/lottery/enter.php', {
            round_id: rid,
            tickets: qty,
            burn_tx: txSig
        });
        if (res.status) {
            notyf.success(`${qty} Tickets Secured!`);
            setTimeout(() => location.reload(), 1000);
        } else {
            notyf.error(res.message);
        }
    } catch (e) {
        console.error("Lottery Error:", e);
        notyf.error('Failed: ' + (e.message || 'Transaction Cancelled'));
    }
}