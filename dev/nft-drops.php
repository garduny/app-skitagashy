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
                <p class="mono text-sm txt-m mt-3 tracking-widest uppercase">// mint exclusive collections · pay with $GASHY</p>
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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($drops as $i => $d):
                    $pct     = $d['max_supply'] > 0 ? ($d['minted_count'] / $d['max_supply']) * 100 : 0;
                    $live    = time() >= strtotime($d['start_time']) && time() <= strtotime($d['end_time']);
                    $soldout = $d['minted_count'] >= $d['max_supply'];
                    $upcoming = !$live && !$soldout;
                ?>
                    <div class="drop-card dc rounded-2xl flex flex-col group">
                        <div class="relative h-60 overflow-hidden rounded-t-2xl">
                            <img src="<?= htmlspecialchars($d['image_uri']) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
                            <div class="img-ov absolute inset-0"></div>
                            <div class="absolute top-4 left-4 z-10">
                                <?php if ($soldout): ?>
                                    <span class="mono badge-sold px-3 py-1 rounded-lg text-xs font-bold uppercase">✕ Sold Out</span>
                                <?php elseif ($live): ?>
                                    <span class="badge-live mono badge-on px-3 py-1 rounded-lg text-xs font-bold uppercase inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background:#00d48f;"></span>Live</span>
                                <?php else: ?>
                                    <span class="mono badge-up px-3 py-1 rounded-lg text-xs font-bold uppercase">◷ Upcoming</span>
                                <?php endif; ?>
                            </div>
                            <div class="absolute top-4 right-4 z-10 mono text-xs" style="color:rgba(255,255,255,.2);">#<?= str_pad($i + 1, 3, '0', STR_PAD_LEFT) ?></div>
                            <div class="absolute bottom-0 left-0 right-0 p-4 z-10">
                                <h3 class="text-xl font-black text-white leading-tight" style="font-family:'Syne',sans-serif;"><?= htmlspecialchars($d['collection_name']) ?><span class="mono text-xs ml-2 font-normal opacity-70" style="color:#00d48f;">$<?= htmlspecialchars($d['symbol']) ?></span></h3>
                            </div>
                        </div>
                        <div class="p-5 flex-1 flex flex-col gap-4">
                            <p class="text-sm txt-m line-clamp-2 leading-relaxed"><?= htmlspecialchars($d['description']) ?></p>
                            <div class="dc-div"></div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="dc-mini rounded-xl p-3">
                                    <div class="text-[10px] txt-m mono uppercase tracking-wider mb-1">Minted</div>
                                    <div class="mono txt-h font-bold text-sm"><?= number_format($d['minted_count']) ?><span class="txt-m font-normal"> / <?= number_format($d['max_supply']) ?></span></div>
                                </div>
                                <div class="dc-mini rounded-xl p-3">
                                    <div class="text-[10px] txt-m mono uppercase tracking-wider mb-1">Price</div>
                                    <div class="mono font-bold text-sm" style="color:#00d48f;"><?= number_format($d['price_gashy']) ?><span class="txt-m font-normal"> G</span></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-[10px] mono txt-m uppercase tracking-wider">Supply Minted</span>
                                    <span class="text-[10px] mono" style="color:#8B5CF6;"><?= round($pct) ?>%</span>
                                </div>
                                <div class="prog-bar">
                                    <div class="prog-fill" style="width:<?= $pct ?>%;"></div>
                                </div>
                            </div>
                            <?php if ($upcoming): ?>
                                <div class="mono text-[10px] txt-m flex items-center gap-2"><span style="color:#8B5CF6;">▶</span>Starts <?= date('M d, Y · H:i', strtotime($d['start_time'])) ?> UTC</div>
                            <?php elseif ($live): ?>
                                <div class="mono text-[10px] txt-m flex items-center gap-2"><span style="color:#00d48f;">■</span>Ends <?= date('M d, Y · H:i', strtotime($d['end_time'])) ?> UTC</div>
                            <?php endif; ?>
                            <div class="mt-auto">
                                <?php if ($soldout): ?>
                                    <button disabled class="btn-mint w-full py-3.5 rounded-xl font-bold text-sm mono uppercase tracking-widest cursor-not-allowed" style="border:1px solid rgba(239,68,68,.2);color:rgba(248,113,113,.35);">✕ Sold Out</button>
                                <?php elseif (!$live): ?>
                                    <button disabled class="btn-mint w-full py-3.5 rounded-xl font-bold text-sm mono uppercase tracking-widest cursor-not-allowed" style="border:1px solid rgba(255,255,255,.08);color:rgba(255,255,255,.2);">◷ <?= date('M d', strtotime($d['start_time'])) ?></button>
                                <?php else: ?>
                                    <button onclick="mintDrop(<?= $d['id'] ?>,<?= $d['price_gashy'] ?>)" class="btn-mint w-full py-3.5 rounded-xl font-black text-sm text-white mono uppercase tracking-widest" style="box-shadow:0 4px 20px rgba(139,92,246,.25);"><span class="relative z-10 flex items-center justify-center gap-2"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                                            </svg>Mint NFT</span></button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($drops)): ?>
                    <div class="col-span-3 text-center py-24">
                        <div class="mono txt-m text-sm tracking-widest uppercase">// No approved drops found</div>
                        <div class="mono text-xs mt-2" style="color:#2a3444;">Check back soon for upcoming collections</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>
<?php require_once 'footer.php'; ?>