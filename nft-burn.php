<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Syne:wght@700;800;900&display=swap');
    .burn-bg {
        background-image: linear-gradient(rgba(239, 68, 68, .03) 1px, transparent 1px), linear-gradient(90deg, rgba(239, 68, 68, .03) 1px, transparent 1px);
        background-size: 40px 40px;
    }
    html:not(.dark) .burn-bg {
        background-image: linear-gradient(rgba(220, 38, 38, .04) 1px, transparent 1px), linear-gradient(90deg, rgba(220, 38, 38, .04) 1px, transparent 1px);
    }
    @keyframes ember {
        0% {
            transform: translateY(0) translateX(0) scale(1);
            opacity: .8;
        }
        100% {
            transform: translateY(-60px) translateX(var(--dx)) scale(0);
            opacity: 0;
        }
    }
    .ember {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
        animation: ember linear forwards;
    }
    .nft-card {
        position: relative;
        overflow: hidden;
        transition: transform .3s ease, box-shadow .3s ease;
        cursor: pointer;
    }
    .nft-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(239, 68, 68, .08) 0%, transparent 60%);
        opacity: 0;
        transition: opacity .3s;
        pointer-events: none;
        z-index: 1;
    }
    .nft-card:hover {
        transform: translateY(-5px);
    }
    .dark .nft-card:hover {
        box-shadow: 0 0 40px rgba(239, 68, 68, .2), 0 20px 50px rgba(0, 0, 0, .5);
    }
    html:not(.dark) .nft-card:hover {
        box-shadow: 0 0 24px rgba(220, 38, 38, .1), 0 16px 40px rgba(0, 0, 0, .07);
    }
    .nft-card:hover::before {
        opacity: 1;
    }
    .nft-card.selected {
        box-shadow: 0 0 0 2px #ef4444, 0 0 30px rgba(239, 68, 68, .3);
    }
    .nft-card.selected .sel-ring {
        opacity: 1;
    }
    .sel-ring {
        opacity: 0;
        transition: opacity .2s;
    }
    .btn-burn {
        background: linear-gradient(135deg, #ef4444, #f97316);
        position: relative;
        overflow: hidden;
        transition: opacity .2s, transform .1s;
    }
    .btn-burn::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, .15), transparent);
    }
    .btn-burn:hover:not(:disabled) {
        opacity: .9;
        transform: scale(1.02);
    }
    .btn-burn:active:not(:disabled) {
        transform: scale(.98);
    }
    .btn-burn:disabled {
        opacity: .35;
        cursor: not-allowed;
    }
    .val-bar {
        height: 3px;
        border-radius: 99px;
        background: linear-gradient(90deg, #ef4444, #f97316);
    }
    @keyframes spin-ring {
        to {
            transform: rotate(360deg);
        }
    }
    .spin-ring {
        animation: spin-ring .9s linear infinite;
    }
    .img-ov {
        background: linear-gradient(to top, rgba(10, 14, 26, 1) 0%, rgba(10, 14, 26, .25) 50%, transparent 100%);
    }
    html:not(.dark) .img-ov {
        background: linear-gradient(to top, rgba(241, 245, 249, .97) 0%, rgba(241, 245, 249, .15) 50%, transparent 100%);
    }
    .mono {
        font-family: 'Share Tech Mono', monospace;
    }
    .burn-tag {
        font-family: 'Share Tech Mono', monospace;
        letter-spacing: .1em;
        border: 1px solid rgba(239, 68, 68, .3);
        color: #ef4444;
        background: rgba(239, 68, 68, .07);
    }
    html:not(.dark) .burn-tag {
        border-color: rgba(220, 38, 38, .3);
        color: #dc2626;
        background: rgba(220, 38, 38, .06);
    }
    .dc {
        background: #131824;
        border: 1px solid rgba(255, 255, 255, .06);
    }
    html:not(.dark) .dc {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, .07);
    }
    .dc-mini {
        background: rgba(255, 255, 255, .04);
        border: 1px solid rgba(255, 255, 255, .06);
    }
    html:not(.dark) .dc-mini {
        background: rgba(0, 0, 0, .03);
        border: 1px solid rgba(0, 0, 0, .06);
    }
    .dc-mini-red {
        background: rgba(239, 68, 68, .06);
        border: 1px solid rgba(239, 68, 68, .15);
    }
    html:not(.dark) .dc-mini-red {
        background: rgba(220, 38, 38, .04);
        border: 1px solid rgba(220, 38, 38, .12);
    }
    .dc-div {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .08), transparent);
    }
    html:not(.dark) .dc-div {
        background: linear-gradient(90deg, transparent, rgba(0, 0, 0, .08), transparent);
    }
    .info-strip {
        border: 1px solid rgba(255, 255, 255, .06);
        background: rgba(255, 255, 255, .02);
    }
    html:not(.dark) .info-strip {
        border-color: rgba(0, 0, 0, .07);
        background: rgba(0, 0, 0, .02);
    }
    .info-sep {
        width: 1px;
        height: 16px;
        background: rgba(255, 255, 255, .08);
    }
    html:not(.dark) .info-sep {
        background: rgba(0, 0, 0, .08);
    }
    .burn-bar-bg {
        background: rgba(19, 24, 36, .97);
        border: 1px solid rgba(239, 68, 68, .3);
    }
    html:not(.dark) .burn-bar-bg {
        background: rgba(255, 255, 255, .97);
        border: 1px solid rgba(220, 38, 38, .25);
    }
    .burn-icon-wrap {
        background: rgba(239, 68, 68, .12);
        border: 1px solid rgba(239, 68, 68, .3);
    }
    html:not(.dark) .burn-icon-wrap {
        background: rgba(220, 38, 38, .08);
        border: 1px solid rgba(220, 38, 38, .2);
    }
    .bar-clear-btn {
        border: 1px solid rgba(255, 255, 255, .1);
        color: rgba(255, 255, 255, .4);
    }
    html:not(.dark) .bar-clear-btn {
        border-color: rgba(0, 0, 0, .12);
        color: #64748b;
    }
    .bar-clear-btn:hover {
        border-color: rgba(255, 255, 255, .2);
        color: rgba(255, 255, 255, .7);
    }
    html:not(.dark) .bar-clear-btn:hover {
        border-color: rgba(0, 0, 0, .2);
        color: #0f172a;
    }
    .txt-h {
        color: #f8fafc;
    }
    html:not(.dark) .txt-h {
        color: #0f172a;
    }
    .txt-m {
        color: #64748b;
    }
    .empty-box {
        border: 1px solid rgba(255, 255, 255, .05);
        background: #131824;
    }
    html:not(.dark) .empty-box {
        border-color: rgba(0, 0, 0, .07);
        background: #fff;
    }
    .empty-icon {
        background: rgba(239, 68, 68, .1);
        border: 1px solid rgba(239, 68, 68, .2);
    }
    html:not(.dark) .empty-icon {
        background: rgba(220, 38, 38, .07);
        border: 1px solid rgba(220, 38, 38, .15);
    }
</style>
<main class="ml-0 lg:ml-64 pt-20 min-h-screen transition-all duration-300 bg-[#0a0e1a] text-gray-100 dark:bg-[#0a0e1a] dark:text-gray-100">
    <style>
        html:not(.dark) main {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }
    </style>
    <div class="burn-bg min-h-screen px-6 py-10">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-14">
                <span class="burn-tag inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                    Incinerator · Irreversible
                </span>
                <h1 class="text-5xl md:text-7xl font-black mb-4 tracking-tight" style="font-family:'Syne',sans-serif;">
                    <span class="txt-h">NFT</span>
                    <span style="background:linear-gradient(135deg,#ef4444,#f97316);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;"> BURN</span>
                </h1>
                <p class="mono text-sm txt-m mt-2 tracking-widest uppercase">
                <div class="mt-8 inline-flex flex-wrap justify-center items-center gap-6 px-8 py-3 rounded-2xl info-strip backdrop-blur-sm">
                    <div class="flex items-center gap-2 text-[11px] mono txt-m"><span style="color:#ef4444;">▲</span>50% value returned as $GASHY</div>
                    <div class="info-sep"></div>
                    <div class="flex items-center gap-2 text-[11px] mono txt-m"><span style="color:#f97316;">■</span>NFT is permanently destroyed</div>
                    <div class="info-sep"></div>
                    <div class="flex items-center gap-2 text-[11px] mono txt-m"><span style="color:#8B5CF6;">◆</span>Select multiple to batch burn</div>
                </div>
            </div>
            <div id="burn-bar" class="hidden sticky top-20 z-50 mb-8 rounded-2xl burn-bar-bg backdrop-blur-md px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-lg" style="box-shadow:0 8px 30px rgba(239,68,68,.1);">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl burn-icon-wrap flex items-center justify-center">
                        <svg width="18" height="18" fill="#ef4444" viewBox="0 0 24 24">
                            <path d="M12 2C9 6 5 8.5 5 13a7 7 0 0 0 14 0c0-4.5-4-7-7-11zm0 17a5 5 0 0 1-5-5c0-3 2.5-5.5 5-8 2.5 2.5 5 5 5 8a5 5 0 0 1-5 5z" />
                        </svg>
                    </div>
                    <div>
                        <div class="txt-h font-black text-sm" style="font-family:'Syne',sans-serif;"><span id="bar-count">0</span> NFT<span id="bar-plural">s</span> selected</div>
                        <div class="mono text-xs txt-m">You will receive <span id="bar-refund" style="color:#00d48f;">0 G</span> back</div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="clearSelection()" class="bar-clear-btn px-5 py-2.5 rounded-xl text-sm mono uppercase tracking-wider transition-all">Clear</button>
                    <button id="confirm-burn-btn" onclick="confirmBurn()" class="btn-burn px-6 py-2.5 rounded-xl font-black text-sm text-white mono uppercase tracking-widest"><span class="relative z-10 flex items-center gap-2"><svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C9 6 5 8.5 5 13a7 7 0 0 0 14 0c0-4.5-4-7-7-11z" />
                            </svg>Burn Selected</span></button>
                </div>
            </div>
            <style>
                @keyframes bcs-pulse {
                    0%,
                    100% {
                        opacity: .5;
                        transform: scale(1);
                    }
                    50% {
                        opacity: 1;
                        transform: scale(1.08);
                    }
                }
                @keyframes bcs-scan {
                    0% {
                        transform: translateY(-100%);
                    }
                    100% {
                        transform: translateY(400%);
                    }
                }
                @keyframes bcs-blink {
                    0%,
                    100% {
                        opacity: 1;
                    }
                    50% {
                        opacity: 0;
                    }
                }
                @keyframes bcs-progress {
                    0% {
                        width: 0%;
                    }
                    70% {
                        width: 58%;
                    }
                    100% {
                        width: 58%;
                    }
                }
                .bcs-wrap {
                    position: relative;
                    overflow: hidden;
                    border: 1px solid rgba(239, 68, 68, .15);
                    background: rgba(19, 24, 36, .6);
                    backdrop-filter: blur(12px);
                }
                html:not(.dark) .bcs-wrap {
                    border-color: rgba(220, 38, 38, .15);
                    background: rgba(255, 255, 255, .7);
                }
                .bcs-scan {
                    position: absolute;
                    left: 0;
                    right: 0;
                    height: 2px;
                    background: linear-gradient(90deg, transparent, rgba(239, 68, 68, .4), transparent);
                    animation: bcs-scan 3s linear infinite;
                    pointer-events: none;
                }
                html:not(.dark) .bcs-scan {
                    background: linear-gradient(90deg, transparent, rgba(220, 38, 38, .3), transparent);
                }
                .bcs-ring {
                    border: 1px solid rgba(239, 68, 68, .2);
                }
                .bcs-ring2 {
                    border: 1px solid rgba(249, 115, 22, .15);
                }
                .bcs-icon {
                    background: linear-gradient(135deg, rgba(239, 68, 68, .15), rgba(249, 115, 22, .15));
                    border: 1px solid rgba(239, 68, 68, .25);
                }
                html:not(.dark) .bcs-icon {
                    background: linear-gradient(135deg, rgba(220, 38, 38, .1), rgba(234, 88, 12, .1));
                    border-color: rgba(220, 38, 38, .25);
                }
                .bcs-bar-track {
                    background: rgba(255, 255, 255, .06);
                    border-radius: 99px;
                    overflow: hidden;
                    height: 6px;
                }
                html:not(.dark) .bcs-bar-track {
                    background: rgba(0, 0, 0, .07);
                }
                .bcs-bar-fill {
                    height: 100%;
                    border-radius: 99px;
                    background: linear-gradient(90deg, #ef4444, #f97316);
                    animation: bcs-progress 2.5s ease-out forwards;
                }
                .bcs-blink {
                    animation: bcs-blink 1.2s step-end infinite;
                }
                .bcs-feat {
                    border: 1px solid rgba(255, 255, 255, .07);
                    background: rgba(255, 255, 255, .03);
                }
                html:not(.dark) .bcs-feat {
                    border-color: rgba(0, 0, 0, .07);
                    background: rgba(0, 0, 0, .02);
                }
            </style>
            <div id="burn-loader" class="hidden"></div>
            <div id="burn-grid" class="hidden"></div>
            <div id="burn-empty" class="hidden"></div>
            <div class="bcs-wrap rounded-3xl p-10 md:p-16 text-center relative">
                <div class="bcs-scan"></div>
                <div class="relative inline-flex items-center justify-center mb-8">
                    <div class="bcs-ring absolute w-32 h-32 rounded-full animate-ping" style="animation-duration:2.5s;animation-timing-function:ease-out;"></div>
                    <div class="bcs-ring2 absolute w-24 h-24 rounded-full" style="animation:bcs-pulse 3s ease-in-out infinite;"></div>
                    <div class="bcs-icon relative w-20 h-20 rounded-2xl flex items-center justify-center" style="box-shadow:0 0 40px rgba(239,68,68,.15);">
                        <svg width="36" height="36" fill="none" viewBox="0 0 24 24">
                            <defs>
                                <linearGradient id="bg1" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#ef4444" />
                                    <stop offset="100%" stop-color="#f97316" />
                                </linearGradient>
                            </defs>
                            <path fill="url(#bg1)" d="M12 2C9 6 5 8.5 5 13a7 7 0 0 0 14 0c0-4.5-4-7-7-11zm0 17a5 5 0 0 1-5-5c0-3 2.5-5.5 5-8 2.5 2.5 5 5 5 8a5 5 0 0 1-5 5z" />
                        </svg>
                    </div>
                </div>
                <div class="mono text-xs uppercase tracking-widest mb-3" style="color:#ef4444;">
                <h2 class="text-4xl md:text-5xl font-black mb-3 tracking-tight txt-h" style="font-family:'Syne',sans-serif;">Coming <span style="background:linear-gradient(135deg,#ef4444,#f97316);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Soon</span></h2>
                <p class="txt-m text-sm max-w-md mx-auto leading-relaxed mb-8">The NFT Incinerator is being forged. Burn your NFTs, reclaim 50% of your $GASHY, and free up your wallet.</p>
                <div class="max-w-xs mx-auto mb-3">
                    <div class="flex justify-between items-center mb-2">
                        <span class="mono text-[10px] txt-m uppercase tracking-wider">Build Progress</span>
                        <span class="mono text-[10px]" style="color:#f97316;">58%<span class="bcs-blink">_</span></span>
                    </div>
                    <div class="bcs-bar-track">
                        <div class="bcs-bar-fill"></div>
                    </div>
                </div>
                <div class="flex flex-wrap justify-center gap-3 mt-8">
                    <div class="bcs-feat mono text-[11px] txt-m px-4 py-2 rounded-xl uppercase tracking-wider">🔥 Batch Burn</div>
                    <div class="bcs-feat mono text-[11px] txt-m px-4 py-2 rounded-xl uppercase tracking-wider">🔥 50% Refund</div>
                    <div class="bcs-feat mono text-[11px] txt-m px-4 py-2 rounded-xl uppercase tracking-wider">🔥 On-chain Destroy</div>
                    <div class="bcs-feat mono text-[11px] txt-m px-4 py-2 rounded-xl uppercase tracking-wider">🔥 Wallet Sync</div>
                </div>
            </div>
        </div>
    </div>
</main>
<script>
    const selected = new Set();
    const priceMap = {};
    function renderCards(nfts) {
        const loader = document.getElementById('burn-loader');
        const grid = document.getElementById('burn-grid');
        const empty = document.getElementById('burn-empty');
        loader.classList.add('hidden');
        if (!nfts || nfts.length === 0) {
            empty.classList.remove('hidden');
            return;
        }
        grid.classList.remove('hidden');
        grid.innerHTML = nfts.map(nft => {
            priceMap[nft.id] = nft.price_gashy;
            return `<div id="card-${nft.id}" class="nft-card dc rounded-2xl flex flex-col" onclick="toggleSelect(${nft.id})">
<div class="relative h-52 overflow-hidden rounded-t-2xl">
<img src="${nft.image_uri}" class="w-full h-full object-cover transition-transform duration-700" loading="lazy">
<div class="img-ov absolute inset-0"></div>
<div class="sel-ring absolute inset-0 rounded-t-2xl flex items-center justify-center" style="border:2px solid #ef4444;background:rgba(239,68,68,.1);">
<div class="w-8 h-8 rounded-full flex items-center justify-center" style="background:#ef4444;box-shadow:0 0 16px rgba(239,68,68,.4);"><svg width="14" height="14" fill="white" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg></div>
</div>
<div class="absolute top-3 left-3 mono text-[10px] z-10" style="color:rgba(255,255,255,.3);">#${String(nft.id).padStart(4,'0')}</div>
</div>
<div class="p-4 flex-1 flex flex-col gap-3">
<div>
<h3 class="txt-h font-black text-base leading-tight" style="font-family:'Syne',sans-serif;">${nft.collection_name}</h3>
<span class="mono text-[10px] opacity-70" style="color:#ef4444;">$${nft.symbol}</span>
</div>
<div class="dc-div"></div>
<div class="grid grid-cols-2 gap-2">
<div class="dc-mini rounded-xl p-2.5">
<div class="mono text-[9px] txt-m uppercase tracking-wider mb-1">Mint Price</div>
<div class="mono text-xs" style="color:rgba(248,250,252,.6);">${Number(nft.price_gashy).toLocaleString()} G</div>
</div>
<div class="dc-mini-red rounded-xl p-2.5">
<div class="mono text-[9px] txt-m uppercase tracking-wider mb-1">Refund</div>
<div class="mono text-xs font-bold" style="color:#00d48f;">${Math.floor(nft.price_gashy*0.5).toLocaleString()} G</div>
</div>
</div>
<div>
<div class="val-bar" style="width:50%;"></div>
<div class="mono text-[9px] txt-m mt-1">50% recovery rate</div>
</div>
</div>
</div>`;
        }).join('');
    }
    function toggleSelect(id) {
        const card = document.getElementById('card-' + id);
        if (selected.has(id)) {
            selected.delete(id);
            card.classList.remove('selected');
        } else {
            selected.add(id);
            card.classList.add('selected');
            spawnEmbers(card);
        }
        updateBurnBar();
    }
    function updateBurnBar() {
        const bar = document.getElementById('burn-bar');
        if (selected.size === 0) {
            bar.classList.add('hidden');
            return;
        }
        bar.classList.remove('hidden');
        document.getElementById('bar-count').textContent = selected.size;
        document.getElementById('bar-plural').textContent = selected.size === 1 ? '' : 's';
        let total = 0;
        selected.forEach(id => {
            total += Math.floor((priceMap[id] || 0) * 0.5);
        });
        document.getElementById('bar-refund').textContent = total.toLocaleString() + ' G';
    }
    function clearSelection() {
        selected.forEach(id => {
            document.getElementById('card-' + id)?.classList.remove('selected');
        });
        selected.clear();
        updateBurnBar();
    }
    function confirmBurn() {
        if (selected.size === 0) return;
        if (typeof burnNFTs === 'function') burnNFTs([...selected]);
    }
    function spawnEmbers(card) {
        for (let i = 0; i < 8; i++) {
            const e = document.createElement('div');
            e.className = 'ember';
            const size = 3 + Math.random() * 5;
            const colors = ['#ef4444', '#f97316', '#fbbf24'];
            e.style.cssText = `width:${size}px;height:${size}px;background:${colors[Math.floor(Math.random()*colors.length)]};bottom:${20+Math.random()*30}%;left:${10+Math.random()*80}%;--dx:${(Math.random()-.5)*40}px;animation-duration:${0.8+Math.random()*0.8}s;animation-delay:${Math.random()*0.3}s;z-index:20;`;
            card.appendChild(e);
            setTimeout(() => e.remove(), 1600);
        }
    }
    window.renderBurnCards = renderCards;
</script>
<?php require_once 'footer.php'; ?>