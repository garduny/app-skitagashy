document.addEventListener('DOMContentLoaded', () => {
    // UI Initialization if needed
    console.log("NFT Launchpad Loaded");
});

async function mintNFT(dropId, price) {
    // 1. Check Auth
    if (!App.checkAuth()) return;

    // 2. Confirm Action
    if (!confirm(`Mint this NFT for ${price} GASHY?`)) return;

    window.notyf.success('Please sign the transaction in your wallet...');

    try {
        // 3. Create Unique Signature Request (Proof of Ownership)
        const nonce = Date.now() + Math.random().toString(36).substring(2, 10);
        const msg = `MINT NFT DROP:\n\nDrop ID: #${dropId}\nPrice: ${price} GASHY\nTimestamp: ${nonce}\n\nClick Approve to confirm mint.`;

        const encoded = new TextEncoder().encode(msg);
        const signed = await window.solana.signMessage(encoded, 'utf8');

        // 4. Handle Hex Conversion securely
        let txSig = '';
        if (signed.signature) {
            const signatureBytes = signed.signature || signed;
            txSig = Array.from(new Uint8Array(signatureBytes)).map(b => b.toString(16).padStart(2, '0')).join('');
        } else {
            txSig = 'MINT_SIG_' + nonce; // Fallback for simulated wallets
        }

        window.notyf.success('Processing Mint on Ledger...');

        // 5. Send to API
        const res = await App.post('api/nft/mint.php', {
            drop_id: dropId,
            tx_signature: txSig
        });

        // 6. Handle Response
        if (res.status) {
            window.notyf.success(res.message);
            // Reload page after 2 seconds to update supply bars
            setTimeout(() => location.reload(), 2000);
        } else {
            window.notyf.error(res.message);
        }

    } catch (e) {
        console.error("Mint Error:", e);
        window.notyf.error('Mint Transaction Cancelled');
    }
}