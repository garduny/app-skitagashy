<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
$drops = getQuery(" SELECT * FROM nft_drops WHERE status='approved' ORDER BY start_time ASC ");
?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Syne:wght@700;800;900&display=swap');
    .drops-bg {
        background-image: linear-gradient(rgba(0, 212, 143, .03) 1px, transparent 1px), linear-gradient(90deg, rgba(0, 212, 143, .03) 1px, transparent 1px);
        background-size: 40px 40px;
    }
    html:not(.dark) .drops-bg {
        background-image: linear-gradient(rgba(0, 163, 114, .04) 1px, transparent 1px), linear-gradient(90deg, rgba(0, 163, 114, .04) 1px, transparent 1px);
    }
    .drop-card {
        position: relative;
        overflow: hidden;
        transition: transform .3s ease, box-shadow .3s ease;
    }
    .drop-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(0, 212, 143, .07) 0%, transparent 60%);
        opacity: 0;
        transition: opacity .3s;
        pointer-events: none;
        z-index: 1;
    }
    .drop-card:hover {
        transform: translateY(-4px);
    }
    .dark .drop-card:hover {
        box-shadow: 0 0 40px rgba(0, 212, 143, .15), 0 20px 60px rgba(0, 0, 0, .5);
    }
    html:not(.dark) .drop-card:hover {
        box-shadow: 0 0 24px rgba(0, 163, 114, .1), 0 16px 40px rgba(0, 0, 0, .07);
    }
    .drop-card:hover::before {
        opacity: 1;
    }
    .prog-bar {
        position: relative;
        height: 4px;
        border-radius: 99px;
        overflow: hidden;
        background: rgba(255, 255, 255, .06);
    }
    html:not(.dark) .prog-bar {
        background: rgba(0, 0, 0, .08);
    }
    .prog-fill {
        height: 100%;
        border-radius: 99px;
        background: linear-gradient(90deg, #8B5CF6, #00d48f);
        position: relative;
    }
    .prog-fill::after {
        content: '';
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 0 8px #00d48f;
    }
    @keyframes pulse-live {
        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(0, 212, 143, .5);
        }
        50% {
            box-shadow: 0 0 0 6px rgba(0, 212, 143, 0);
        }
    }
    .badge-live {
        animation: pulse-live 2s infinite;
    }
    .btn-mint {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #8B5CF6, #00d48f);
        transition: opacity .2s, transform .1s;
    }
    .btn-mint:hover:not(:disabled) {
        opacity: .9;
        transform: scale(1.02);
    }
    .btn-mint:active:not(:disabled) {
        transform: scale(.98);
    }
    .btn-mint::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, .15), transparent);
    }
    .btn-mint:disabled {
        background: transparent;
        cursor: not-allowed;
    }
    .mono {
        font-family: 'Share Tech Mono', monospace;
    }
    .img-ov {
        background: linear-gradient(to top, rgba(10, 14, 26, 1) 0%, rgba(10, 14, 26, .25) 50%, transparent 100%);
    }
    html:not(.dark) .img-ov {
        background: linear-gradient(to top, rgba(241, 245, 249, .97) 0%, rgba(241, 245, 249, .15) 50%, transparent 100%);
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
    .dc-div {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .08), transparent);
    }
    html:not(.dark) .dc-div {
        background: linear-gradient(90deg, transparent, rgba(0, 0, 0, .08), transparent);
    }
    .stat-strip {
        border: 1px solid rgba(255, 255, 255, .06);
        background: rgba(255, 255, 255, .02);
    }
    html:not(.dark) .stat-strip {
        border-color: rgba(0, 0, 0, .07);
        background: rgba(0, 0, 0, .02);
    }
    .stat-sep {
        width: 1px;
        height: 32px;
        background: rgba(255, 255, 255, .08);
    }
    html:not(.dark) .stat-sep {
        background: rgba(0, 0, 0, .08);
    }
    .drops-tag {
        font-family: 'Share Tech Mono', monospace;
        letter-spacing: .1em;
        border: 1px solid rgba(0, 212, 143, .3);
        color: #00d48f;
        background: rgba(0, 212, 143, .07);
    }
    html:not(.dark) .drops-tag {
        border-color: rgba(0, 163, 114, .3);
        color: #059669;
        background: rgba(0, 163, 114, .07);
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
    .badge-sold {
        background: rgba(239, 68, 68, .12);
        border: 1px solid rgba(239, 68, 68, .25);
        color: #f87171;
    }
    .badge-up {
        background: rgba(255, 255, 255, .07);
        border: 1px solid rgba(255, 255, 255, .1);
        color: #94a3b8;
    }
    html:not(.dark) .badge-up {
        background: rgba(0, 0, 0, .05);
        border: 1px solid rgba(0, 0, 0, .09);
        color: #64748b;
    }
    .badge-on {
        background: rgba(0, 212, 143, .1);
        border: 1px solid rgba(0, 212, 143, .35);
        color: #00d48f;
    }
</style>
<main class="ml-0 lg:ml-64 pt-20 min-h-screen transition-all duration-300 bg-[#0a0e1a] text-gray-100 dark:bg-[#0a0e1a] dark:text-gray-100">
    <style>
        html:not(.dark) main {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }
    </style>
    <div class="drops-bg min-h-screen px-6 py-10">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <span class="drops-tag inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6">
                    <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background:#00d48f;"></span>
                    Launchpad
                </span>
                <h1 class="text-5xl md:text-7xl font-black mb-4 tracking-tight leading-none" style="font-family:'Syne',sans-serif;">
                    <span class="txt-h">NFT</span>
                    <span style="background:linear-gradient(135deg,#8B5CF6,#00ffaa);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;"> DROPS</span>
                </h1>
                <p class="mono text-sm txt-m mt-3 tracking-widest uppercase">
                <div class="mt-10 inline-flex items-center gap-8 px-8 py-3 rounded-2xl stat-strip backdrop-blur-sm">
                    <div class="text-center">
                        <div class="mono text-xl font-bold" style="color:#00d48f;"><?= count($drops) ?></div>
                        <div class="text-[10px] txt-m uppercase tracking-wider">Active Drops</div>
                    </div>
                    <div class="stat-sep"></div>
                    <div class="text-center">
                        <div class="mono text-xl font-bold" style="color:#8B5CF6;"><?= array_sum(array_column($drops, 'minted_count')) ?></div>
                        <div class="text-[10px] txt-m uppercase tracking-wider">Total Minted</div>
                    </div>
                    <div class="stat-sep"></div>
                    <div class="text-center">
                        <div class="mono text-xl font-bold" style="color:#f87171;"><?= array_sum(array_column($drops, 'max_supply')) ?></div>
                        <div class="text-[10px] txt-m uppercase tracking-wider">Max Supply</div>
                    </div>
                </div>
            </div>
            <style>
                @keyframes cs-pulse {
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
                @keyframes cs-scan {
                    0% {
                        transform: translateY(-100%);
                    }
                    100% {
                        transform: translateY(400%);
                    }
                }
                @keyframes cs-blink {
                    0%,
                    100% {
                        opacity: 1;
                    }
                    50% {
                        opacity: 0;
                    }
                }
                .cs-wrap {
                    position: relative;
                    overflow: hidden;
                    border: 1px solid rgba(0, 212, 143, .15);
                    background: rgba(19, 24, 36, .6);
                    backdrop-filter: blur(12px);
                }
                html:not(.dark) .cs-wrap {
                    border-color: rgba(0, 163, 114, .15);
                    background: rgba(255, 255, 255, .7);
                }
                .cs-scan-line {
                    position: absolute;
                    left: 0;
                    right: 0;
                    height: 2px;
                    background: linear-gradient(90deg, transparent, rgba(0, 212, 143, .4), transparent);
                    animation: cs-scan 3s linear infinite;
                    pointer-events: none;
                }
                html:not(.dark) .cs-scan-line {
                    background: linear-gradient(90deg, transparent, rgba(0, 163, 114, .3), transparent);
                }
                .cs-ring {
                    border: 1px solid rgba(0, 212, 143, .2);
                }
                html:not(.dark) .cs-ring {
                    border-color: rgba(0, 163, 114, .2);
                }
                .cs-ring-2 {
                    border: 1px solid rgba(139, 92, 246, .15);
                }
                .cs-icon-bg {
                    background: linear-gradient(135deg, rgba(0, 212, 143, .15), rgba(139, 92, 246, .15));
                    border: 1px solid rgba(0, 212, 143, .25);
                }
                html:not(.dark) .cs-icon-bg {
                    background: linear-gradient(135deg, rgba(0, 163, 114, .12), rgba(139, 92, 246, .1));
                    border-color: rgba(0, 163, 114, .25);
                }
                .cs-bar-track {
                    background: rgba(255, 255, 255, .06);
                    border-radius: 99px;
                    overflow: hidden;
                    height: 6px;
                }
                html:not(.dark) .cs-bar-track {
                    background: rgba(0, 0, 0, .07);
                }
                @keyframes cs-progress {
                    0% {
                        width: 0%;
                    }
                    70% {
                        width: 72%;
                    }
                    100% {
                        width: 72%;
                    }
                }
                .cs-bar-fill {
                    height: 100%;
                    border-radius: 99px;
                    background: linear-gradient(90deg, #8B5CF6, #00d48f);
                    animation: cs-progress 2.5s ease-out forwards;
                }
                .cs-blink {
                    animation: cs-blink 1.2s step-end infinite;
                }
                .cs-feat {
                    border: 1px solid rgba(255, 255, 255, .07);
                    background: rgba(255, 255, 255, .03);
                }
                html:not(.dark) .cs-feat {
                    border-color: rgba(0, 0, 0, .07);
                    background: rgba(0, 0, 0, .02);
                }
            </style>
            <div class="cs-wrap rounded-3xl p-10 md:p-16 text-center relative">
                <div class="cs-scan-line"></div>
                <div class="relative inline-flex items-center justify-center mb-8">
                    <div class="cs-ring absolute w-32 h-32 rounded-full animate-ping" style="animation-duration:2.5s;animation-timing-function:ease-out;"></div>
                    <div class="cs-ring-2 absolute w-24 h-24 rounded-full" style="animation:cs-pulse 3s ease-in-out infinite;"></div>
                    <div class="cs-icon-bg relative w-20 h-20 rounded-2xl flex items-center justify-center" style="box-shadow:0 0 40px rgba(0,212,143,.15);">
                        <svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                            <defs>
                                <linearGradient id="ig1" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#8B5CF6" />
                                    <stop offset="100%" stop-color="#00d48f" />
                                </linearGradient>
                            </defs>
                            <path stroke="url(#ig1)" stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
                <div class="mono text-xs uppercase tracking-widest mb-3" style="color:#00d48f;">
                <h2 class="text-4xl md:text-5xl font-black mb-3 tracking-tight txt-h" style="font-family:'Syne',sans-serif;">Coming <span style="background:linear-gradient(135deg,#8B5CF6,#00ffaa);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Soon</span></h2>
                <p class="txt-m text-sm max-w-md mx-auto leading-relaxed mb-8">The NFT Launchpad is being built. Exclusive drops, on-chain minting, and $GASHY payments are on the way.</p>
                <div class="max-w-xs mx-auto mb-3">
                    <div class="flex justify-between items-center mb-2">
                        <span class="mono text-[10px] txt-m uppercase tracking-wider">Build Progress</span>
                        <span class="mono text-[10px]" style="color:#8B5CF6;">72%<span class="cs-blink">_</span></span>
                    </div>
                    <div class="cs-bar-track">
                        <div class="cs-bar-fill"></div>
                    </div>
                </div>
                <div class="flex flex-wrap justify-center gap-3 mt-8">
                    <div class="cs-feat mono text-[11px] txt-m px-4 py-2 rounded-xl uppercase tracking-wider">◆ On-chain Minting</div>
                    <div class="cs-feat mono text-[11px] txt-m px-4 py-2 rounded-xl uppercase tracking-wider">◆ $GASHY Payments</div>
                    <div class="cs-feat mono text-[11px] txt-m px-4 py-2 rounded-xl uppercase tracking-wider">◆ Exclusive Collections</div>
                    <div class="cs-feat mono text-[11px] txt-m px-4 py-2 rounded-xl uppercase tracking-wider">◆ Live Drop Tracker</div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>