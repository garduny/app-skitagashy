<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
$priceData = json_decode(@file_get_contents('server/.cache/price.json'), true) ?: [];
$price = $priceData['price'] ?? 0.045;
$mcap = $priceData['marketcap'] ?? 0;
$vol = $priceData['volume24h'] ?? 0;
$liq = $priceData['liquidity'] ?? 0;
$banners = [];
try {
    $banners = getQuery(" SELECT image_path,link_url FROM banners WHERE is_active=1 ORDER BY sort_order ASC LIMIT 5 ");
} catch (Exception $e) {
}
$flash_deals = getQuery(" SELECT p.id,p.title,p.slug,p.price_gashy,p.images,p.type FROM products p WHERE p.status='active' AND p.stock>0 ORDER BY RAND() LIMIT 4 ");
$top_sellers = getQuery(" SELECT store_name,total_sales,rating FROM sellers WHERE is_approved=1 ORDER BY total_sales DESC LIMIT 5 ");
$new_arrivals = getQuery(" SELECT p.id,p.title,p.slug,p.price_gashy,p.images,p.type FROM products p WHERE p.status='active' ORDER BY p.created_at DESC LIMIT 8 ");
$lottery = findQuery(" SELECT prize_pool,round_number,draw_time FROM lottery_rounds WHERE status='open' ORDER BY id DESC LIMIT 1 ");
$mystery_box = findQuery(" SELECT p.id,p.title,p.slug,p.price_gashy,p.images FROM products p INNER JOIN mystery_box_loot m ON m.box_product_id=p.id WHERE p.status='active' AND p.stock>0 GROUP BY p.id ORDER BY p.created_at DESC LIMIT 1 ");
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Rajdhani:wght@500;600;700&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
<style>
    :root {
        --neon: #00ffaa;
        --neon-dim: #00d48f;
        --plasma: #ff2d78;
        --volt: #ffe500;
        --void: #03050d;
        --void-2: #070b14;
        --void-3: #0c1120;
        --panel: rgba(8, 13, 26, 0.9);
        --pb: rgba(0, 255, 170, 0.1);
        --pbh: rgba(0, 255, 170, 0.28);
        --td: rgba(148, 163, 184, 0.75);
        --fd: 'Orbitron', monospace;
        --fb: 'Rajdhani', sans-serif;
        --fm: 'JetBrains Mono', monospace
    }

    *,
    *::before,
    *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0
    }

    html {
        scroll-behavior: smooth
    }

    body {
        background: var(--void);
        color: #e2e8f0;
        font-family: var(--fb);
        overflow-x: hidden
    }

    @keyframes ticker {
        0% {
            transform: translateX(0)
        }

        100% {
            transform: translateX(-50%)
        }
    }

    @keyframes pulse-dot {

        0%,
        100% {
            opacity: 1;
            box-shadow: 0 0 4px currentColor
        }

        50% {
            opacity: .6;
            box-shadow: 0 0 10px currentColor
        }
    }

    @keyframes neon-text {

        0%,
        100% {
            text-shadow: 0 0 8px var(--neon), 0 0 20px var(--neon)
        }

        50% {
            text-shadow: 0 0 4px var(--neon), 0 0 40px var(--neon), 0 0 70px var(--neon)
        }
    }

    @keyframes shimmer-bg {
        0% {
            background-position: -200% center
        }

        100% {
            background-position: 200% center
        }
    }

    @keyframes fade-up {
        from {
            opacity: 0;
            transform: translateY(28px)
        }

        to {
            opacity: 1;
            transform: translateY(0)
        }
    }

    @keyframes glitch-a {

        0%,
        85%,
        100% {
            clip-path: inset(0 0 100% 0);
            transform: translateX(0)
        }

        87% {
            clip-path: inset(20% 0 50% 0);
            transform: translateX(-3px)
        }

        90% {
            clip-path: inset(55% 0 25% 0);
            transform: translateX(3px)
        }

        93% {
            clip-path: inset(70% 0 10% 0);
            transform: translateX(-2px)
        }
    }

    @keyframes glitch-b {

        0%,
        85%,
        100% {
            clip-path: inset(0 0 100% 0);
            transform: translateX(0)
        }

        88% {
            clip-path: inset(50% 0 30% 0);
            transform: translateX(2px)
        }

        91% {
            clip-path: inset(25% 0 60% 0);
            transform: translateX(-2px)
        }

        94% {
            clip-path: inset(80% 0 5% 0);
            transform: translateX(1px)
        }
    }

    .reveal {
        opacity: 0;
        animation: fade-up .7s cubic-bezier(.2, 1, .3, 1) forwards
    }

    .d1 {
        animation-delay: .05s
    }

    .d2 {
        animation-delay: .12s
    }

    .d3 {
        animation-delay: .2s
    }

    .d4 {
        animation-delay: .28s
    }

    .d5 {
        animation-delay: .36s
    }

    .d6 {
        animation-delay: .44s
    }

    .main-wrap {
        padding-top: 6rem;
        min-height: 100vh;
        contain: layout
    }

    @media(min-width:1024px) {
        .main-wrap {
            padding-left: 18rem
        }
    }

    .bg-grid {
        position: fixed;
        inset: 0;
        pointer-events: none;
        z-index: 0;
        background-image: linear-gradient(rgba(0, 255, 170, .018) 1px, transparent 1px), linear-gradient(90deg, rgba(0, 255, 170, .018) 1px, transparent 1px);
        background-size: 64px 64px
    }

    .bg-orb {
        position: fixed;
        border-radius: 50%;
        pointer-events: none;
        z-index: 0
    }

    .orb-1 {
        width: 600px;
        height: 600px;
        top: -180px;
        left: 8%;
        background: radial-gradient(circle, rgba(0, 255, 170, .06) 0%, transparent 70%)
    }

    .orb-2 {
        width: 500px;
        height: 500px;
        bottom: -80px;
        right: 4%;
        background: radial-gradient(circle, rgba(59, 130, 246, .05) 0%, transparent 70%)
    }

    .content {
        position: relative;
        z-index: 10;
        max-width: 1800px;
        margin: 0 auto;
        padding: 0 1.25rem 5rem
    }

    .ticker-bar {
        position: sticky;
        top: 5rem;
        z-index: 50;
        overflow: hidden;
        border-top: 1px solid rgba(0, 255, 170, .25);
        border-bottom: 1px solid rgba(0, 255, 170, .25);
        background: rgba(3, 5, 13, .97);
        backdrop-filter: blur(12px)
    }

    .ticker-bar::before,
    .ticker-bar::after {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        width: 80px;
        z-index: 2;
        pointer-events: none
    }

    .ticker-bar::before {
        left: 0;
        background: linear-gradient(90deg, rgba(3, 5, 13, 1), transparent)
    }

    .ticker-bar::after {
        right: 0;
        background: linear-gradient(90deg, transparent, rgba(3, 5, 13, 1))
    }

    .ticker-inner {
        display: inline-flex;
        gap: 2.5rem;
        padding: .6rem 1rem;
        animation: ticker 30s linear infinite;
        will-change: transform;
        white-space: nowrap
    }

    .ticker-item {
        font-family: var(--fm);
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .05em;
        display: flex;
        align-items: center;
        gap: .35rem
    }

    .ticker-dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        flex-shrink: 0;
        animation: pulse-dot 2s ease-in-out infinite
    }

    .hero-section {
        padding: 4.5rem 0 2.5rem;
        text-align: center;
        position: relative
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .35rem 1.1rem;
        border: 1px solid rgba(0, 255, 170, .35);
        background: rgba(0, 255, 170, .05);
        border-radius: 999px;
        font-family: var(--fm);
        font-size: .62rem;
        font-weight: 700;
        letter-spacing: .15em;
        color: var(--neon);
        text-transform: uppercase;
        margin-bottom: 1.75rem
    }

    .hero-badge-dot {
        width: 6px;
        height: 6px;
        background: var(--neon);
        border-radius: 50%;
        animation: pulse-dot 2s ease-in-out infinite
    }

    .hero-title {
        font-family: var(--fd);
        font-size: clamp(2.8rem, 9vw, 7.5rem);
        font-weight: 900;
        line-height: .95;
        letter-spacing: -.02em;
        color: #fff;
        margin-bottom: .4rem
    }

    .hero-accent {
        font-family: var(--fd);
        font-size: clamp(3.2rem, 10.5vw, 9rem);
        font-weight: 900;
        line-height: .9;
        letter-spacing: -.02em;
        background: linear-gradient(120deg, var(--neon), #00c8ff, var(--neon));
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: shimmer-bg 4s linear infinite, neon-text 3s ease-in-out infinite;
        display: block;
        margin-bottom: 1.75rem;
        position: relative
    }

    .hero-accent::before {
        content: attr(data-text);
        position: absolute;
        inset: 0;
        font-family: var(--fd);
        font-size: inherit;
        font-weight: 900;
        letter-spacing: inherit;
        -webkit-text-fill-color: rgba(255, 45, 120, .4);
        background: none;
        animation: glitch-a 8s 1s infinite linear
    }

    .hero-accent::after {
        content: attr(data-text);
        position: absolute;
        inset: 0;
        font-family: var(--fd);
        font-size: inherit;
        font-weight: 900;
        letter-spacing: inherit;
        -webkit-text-fill-color: rgba(0, 200, 255, .35);
        background: none;
        animation: glitch-b 8s 1.1s infinite linear
    }

    .hero-desc {
        font-family: var(--fb);
        font-size: 1.15rem;
        font-weight: 500;
        color: var(--td);
        max-width: 660px;
        margin: 0 auto 2.5rem;
        line-height: 1.7
    }

    .hero-desc span {
        color: var(--neon);
        font-weight: 700
    }

    .hero-btns {
        display: flex;
        flex-wrap: wrap;
        gap: .9rem;
        justify-content: center
    }

    .btn-p {
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        padding: .9rem 2.2rem;
        background: var(--neon);
        color: #000;
        font-family: var(--fd);
        font-size: .8rem;
        font-weight: 700;
        letter-spacing: .08em;
        border-radius: .5rem;
        border: none;
        cursor: pointer;
        transition: transform .15s, box-shadow .15s, background .15s;
        text-decoration: none
    }

    .btn-p:hover {
        background: #00ffcc;
        box-shadow: 0 0 24px rgba(0, 255, 170, .45), 0 6px 18px rgba(0, 255, 170, .18);
        transform: translateY(-2px)
    }

    .btn-s {
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        padding: .9rem 2.2rem;
        background: transparent;
        color: var(--neon);
        font-family: var(--fd);
        font-size: .8rem;
        font-weight: 700;
        letter-spacing: .08em;
        border-radius: .5rem;
        border: 1px solid rgba(0, 255, 170, .45);
        cursor: pointer;
        transition: transform .15s, box-shadow .15s, background .15s;
        text-decoration: none
    }

    .btn-s:hover {
        background: rgba(0, 255, 170, .07);
        border-color: var(--neon);
        box-shadow: 0 0 16px rgba(0, 255, 170, .18);
        transform: translateY(-2px)
    }

    .btn-cg {
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        padding: .9rem 2.2rem;
        background: #8DC351;
        color: #fff;
        font-family: var(--fd);
        font-size: .8rem;
        font-weight: 700;
        letter-spacing: .08em;
        border-radius: .5rem;
        border: none;
        cursor: pointer;
        transition: transform .15s, box-shadow .15s, background .15s;
        text-decoration: none
    }

    .btn-cg:hover {
        background: #7caf43;
        box-shadow: 0 6px 18px rgba(141, 195, 81, .28);
        transform: translateY(-2px)
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1px;
        background: rgba(0, 255, 170, .08);
        border: 1px solid rgba(0, 255, 170, .08);
        border-radius: 1rem;
        overflow: hidden;
        margin: 3.5rem 0
    }

    @media(min-width:640px) {
        .stat-grid {
            grid-template-columns: repeat(4, 1fr)
        }
    }

    .stat-cell {
        background: var(--panel);
        padding: 1.75rem 1.25rem;
        text-align: center;
        position: relative;
        transition: background .2s
    }

    .stat-cell:hover {
        background: rgba(0, 255, 170, .035)
    }

    .stat-label {
        font-family: var(--fm);
        font-size: .58rem;
        font-weight: 700;
        letter-spacing: .18em;
        color: rgba(148, 163, 184, .55);
        text-transform: uppercase;
        margin-bottom: .65rem
    }

    .stat-val {
        font-family: var(--fd);
        font-size: clamp(1.4rem, 3.5vw, 2.3rem);
        font-weight: 900;
        color: var(--neon);
        line-height: 1
    }

    .stat-val.white {
        color: #fff
    }

    .panel {
        background: var(--panel);
        border: 1px solid var(--pb);
        border-radius: 1rem;
        position: relative;
        overflow: hidden;
        transition: border-color .25s, box-shadow .25s
    }

    .panel::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(0, 255, 170, .35), transparent)
    }

    .panel:hover {
        border-color: var(--pbh);
        box-shadow: 0 0 30px rgba(0, 255, 170, .04), 0 16px 48px rgba(0, 0, 0, .35)
    }

    .chart-panel {
        padding: 1.75rem
    }

    .chart-header {
        display: flex;
        align-items: center;
        gap: .7rem;
        margin-bottom: 1.75rem
    }

    .chart-title {
        font-family: var(--fd);
        font-size: 1rem;
        font-weight: 700;
        letter-spacing: .05em;
        color: #fff
    }

    .section-title {
        font-family: var(--fd);
        font-size: clamp(1.4rem, 3.5vw, 2.2rem);
        font-weight: 900;
        letter-spacing: .03em;
        color: #fff;
        margin-bottom: .4rem
    }

    .section-title .accent {
        color: var(--neon)
    }

    .section-sub {
        font-family: var(--fb);
        font-size: .95rem;
        color: var(--td);
        margin-bottom: 2.5rem
    }

    .feature-grid {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 1.25rem
    }

    @media(min-width:640px) {
        .feature-grid {
            grid-template-columns: repeat(2, 1fr)
        }
    }

    @media(min-width:1024px) {
        .feature-grid {
            grid-template-columns: repeat(3, 1fr)
        }
    }

    .feature-card {
        background: var(--panel);
        border: 1px solid var(--pb);
        border-radius: 1rem;
        padding: 1.75rem;
        position: relative;
        overflow: hidden;
        transition: border-color .2s, transform .2s, box-shadow .2s
    }

    .feature-card:hover {
        border-color: var(--pbh);
        transform: translateY(-4px);
        box-shadow: 0 16px 48px rgba(0, 255, 170, .07)
    }

    .feature-icon {
        width: 3rem;
        height: 3rem;
        border-radius: .65rem;
        background: rgba(0, 255, 170, .07);
        border: 1px solid rgba(0, 255, 170, .18);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        margin-bottom: 1.25rem;
        transition: background .2s, box-shadow .2s
    }

    .feature-card:hover .feature-icon {
        background: rgba(0, 255, 170, .13);
        box-shadow: 0 0 16px rgba(0, 255, 170, .18)
    }

    .feature-title {
        font-family: var(--fd);
        font-size: .88rem;
        font-weight: 700;
        letter-spacing: .05em;
        color: #fff;
        margin-bottom: .6rem
    }

    .feature-desc {
        font-family: var(--fb);
        font-size: .88rem;
        color: var(--td);
        line-height: 1.6;
        font-weight: 500
    }

    .hero-banner-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.25rem;
        margin-bottom: 4.5rem
    }

    @media(min-width:1024px) {
        .hero-banner-grid {
            grid-template-columns: 3fr 1fr
        }
    }

    .main-banner {
        position: relative;
        height: 440px;
        border-radius: 1.25rem;
        overflow: hidden;
        border: 1px solid rgba(0, 255, 170, .08)
    }

    .main-banner img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .6s ease
    }

    .main-banner:hover img {
        transform: scale(1.04)
    }

    .main-banner-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(3, 5, 13, .96) 0%, rgba(3, 5, 13, .45) 55%, transparent 100%)
    }

    .main-banner-content {
        position: absolute;
        bottom: 0;
        left: 0;
        padding: 2.25rem;
        z-index: 10
    }

    .banner-tag {
        display: inline-block;
        padding: .28rem .7rem;
        background: var(--neon);
        color: #000;
        font-family: var(--fm);
        font-size: .58rem;
        font-weight: 700;
        letter-spacing: .15em;
        border-radius: .25rem;
        text-transform: uppercase;
        margin-bottom: .9rem
    }

    .banner-title {
        font-family: var(--fd);
        font-size: clamp(1.8rem, 4.5vw, 3.2rem);
        font-weight: 900;
        color: #fff;
        line-height: 1;
        letter-spacing: .02em;
        margin-bottom: 1.25rem
    }

    .banner-title .hl {
        color: var(--neon);
        text-shadow: 0 0 18px var(--neon)
    }

    .side-promos {
        display: flex;
        flex-direction: column;
        gap: 1.25rem
    }

    .promo-card {
        flex: 1;
        border-radius: 1.25rem;
        padding: 1.75rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 200px;
        position: relative;
        overflow: hidden;
        transition: transform .2s, box-shadow .2s;
        text-decoration: none;
        color: #fff
    }

    .promo-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, .35)
    }

    .promo-bg-1 {
        background: linear-gradient(135deg, #7c3aed, #ec4899)
    }

    .promo-bg-2 {
        background: linear-gradient(135deg, #059669, #0891b2)
    }

    .promo-label {
        font-family: var(--fd);
        font-size: 1.3rem;
        font-weight: 900;
        letter-spacing: .04em;
        margin-bottom: .35rem
    }

    .promo-sub {
        font-family: var(--fm);
        font-size: .68rem;
        opacity: .8
    }

    .promo-btn {
        padding: .65rem;
        background: rgba(255, 255, 255, .14);
        border: 1px solid rgba(255, 255, 255, .2);
        border-radius: .5rem;
        text-align: center;
        font-family: var(--fd);
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .08em;
        transition: background .2s;
        backdrop-filter: blur(8px)
    }

    .promo-btn:hover {
        background: rgba(255, 255, 255, .22)
    }

    .promo-deco {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 4.5rem;
        height: 4.5rem;
        opacity: .09
    }

    .deals-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.75rem
    }

    .timer-badge {
        display: flex;
        align-items: center;
        gap: .45rem;
        padding: .45rem .9rem;
        background: rgba(239, 68, 68, .1);
        border: 1px solid rgba(239, 68, 68, .28);
        border-radius: .5rem;
        font-family: var(--fm);
        font-size: .72rem;
        font-weight: 700;
        color: #f87171
    }

    .timer-dot {
        width: 5px;
        height: 5px;
        background: #f87171;
        border-radius: 50%;
        animation: pulse-dot 1s ease-in-out infinite
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.25rem
    }

    @media(min-width:1024px) {
        .products-grid {
            grid-template-columns: repeat(4, 1fr)
        }
    }

    .product-card {
        background: var(--panel);
        border: 1px solid var(--pb);
        border-radius: 1rem;
        overflow: hidden;
        transition: border-color .2s, transform .2s, box-shadow .2s;
        text-decoration: none;
        display: block;
        position: relative
    }

    .product-card:hover {
        border-color: var(--pbh);
        transform: translateY(-4px);
        box-shadow: 0 16px 40px rgba(0, 255, 170, .07)
    }

    .product-img {
        aspect-ratio: 1;
        overflow: hidden;
        position: relative;
        background: var(--void-3)
    }

    .product-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .4s ease
    }

    .product-card:hover .product-img img {
        transform: scale(1.06)
    }

    .product-badge {
        position: absolute;
        top: .65rem;
        right: .65rem;
        padding: .25rem .55rem;
        background: #dc2626;
        color: #fff;
        font-family: var(--fm);
        font-size: .58rem;
        font-weight: 700;
        border-radius: .25rem;
        letter-spacing: .05em
    }

    .product-info {
        padding: 1.1rem
    }

    .product-name {
        font-family: var(--fd);
        font-size: .8rem;
        font-weight: 700;
        color: #fff;
        letter-spacing: .03em;
        margin-bottom: .65rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis
    }

    .product-price-row {
        display: flex;
        align-items: center;
        justify-content: space-between
    }

    .product-price {
        font-family: var(--fm);
        font-size: .95rem;
        font-weight: 700;
        color: var(--neon)
    }

    .product-price-old {
        font-family: var(--fm);
        font-size: .72rem;
        color: rgba(148, 163, 184, .45);
        text-decoration: line-through
    }

    .arrivals-sellers-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem
    }

    @media(min-width:1024px) {
        .arrivals-sellers-grid {
            grid-template-columns: 2fr 1fr
        }
    }

    .arrival-item {
        display: flex;
        align-items: center;
        gap: 1.1rem;
        padding: 1.1rem;
        background: var(--panel);
        border: 1px solid var(--pb);
        border-radius: .75rem;
        transition: border-color .2s, transform .15s;
        text-decoration: none;
        position: relative;
        overflow: hidden
    }

    .arrival-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: var(--neon);
        transform: scaleY(0);
        transition: transform .25s;
        transform-origin: bottom
    }

    .arrival-item:hover::before {
        transform: scaleY(1)
    }

    .arrival-item:hover {
        border-color: rgba(0, 255, 170, .22);
        transform: translateX(3px)
    }

    .arrival-thumb {
        width: 5rem;
        height: 5rem;
        border-radius: .5rem;
        overflow: hidden;
        flex-shrink: 0;
        border: 1px solid rgba(255, 255, 255, .04);
        background: var(--void-3)
    }

    .arrival-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover
    }

    .arrival-info {
        flex: 1;
        min-width: 0
    }

    .arrival-name {
        font-family: var(--fd);
        font-size: .85rem;
        font-weight: 700;
        color: #fff;
        letter-spacing: .03em;
        margin-bottom: .2rem
    }

    .arrival-type {
        font-family: var(--fm);
        font-size: .58rem;
        font-weight: 700;
        letter-spacing: .15em;
        color: rgba(148, 163, 184, .45);
        text-transform: uppercase
    }

    .arrival-right {
        text-align: right;
        flex-shrink: 0
    }

    .arrival-price {
        font-family: var(--fm);
        font-size: 1rem;
        font-weight: 700;
        color: var(--neon)
    }

    .buy-pill {
        margin-top: .35rem;
        padding: .28rem .7rem;
        font-family: var(--fm);
        font-size: .58rem;
        font-weight: 700;
        letter-spacing: .1em;
        background: rgba(0, 255, 170, .07);
        border: 1px solid rgba(0, 255, 170, .18);
        border-radius: 999px;
        color: var(--neon);
        cursor: pointer;
        transition: background .15s, border-color .15s;
        display: inline-block;
        text-decoration: none
    }

    .buy-pill:hover {
        background: rgba(0, 255, 170, .14);
        border-color: rgba(0, 255, 170, .4)
    }

    .sellers-panel {
        background: var(--panel);
        border: 1px solid var(--pb);
        border-radius: 1rem;
        padding: 1.5rem
    }

    .seller-row {
        display: flex;
        align-items: center;
        gap: .9rem;
        padding: .9rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, .035)
    }

    .seller-row:last-child {
        border-bottom: none
    }

    .seller-rank {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: .45rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: var(--fd);
        font-size: .85rem;
        font-weight: 900;
        flex-shrink: 0
    }

    .rank-1 {
        background: linear-gradient(135deg, #f59e0b, #ef4444);
        color: #000
    }

    .rank-2 {
        background: linear-gradient(135deg, #6b7280, #9ca3af);
        color: #000
    }

    .rank-3 {
        background: linear-gradient(135deg, #92400e, #b45309);
        color: #fff
    }

    .rank-other {
        background: rgba(0, 255, 170, .07);
        border: 1px solid rgba(0, 255, 170, .18);
        color: var(--neon)
    }

    .seller-name {
        font-family: var(--fd);
        font-size: .82rem;
        font-weight: 700;
        color: #fff;
        letter-spacing: .03em
    }

    .seller-rating {
        font-family: var(--fm);
        font-size: .62rem;
        color: #fbbf24;
        display: flex;
        align-items: center;
        gap: .22rem
    }

    .seller-sales {
        font-family: var(--fm);
        font-size: .62rem;
        font-weight: 700;
        color: rgba(148, 163, 184, .45);
        margin-left: auto;
        flex-shrink: 0
    }

    .become-seller {
        border-radius: 1rem;
        padding: 1.75rem;
        text-align: center;
        background: linear-gradient(135deg, #1e40af, #7c3aed);
        position: relative;
        overflow: hidden;
        margin-top: 1.25rem
    }

    .become-title {
        font-family: var(--fd);
        font-size: 1.2rem;
        font-weight: 900;
        color: #fff;
        letter-spacing: .05em;
        margin-bottom: .4rem
    }

    .become-sub {
        font-family: var(--fm);
        font-size: .68rem;
        color: rgba(255, 255, 255, .65);
        margin-bottom: 1.25rem
    }

    .spacer {
        margin-bottom: 4.5rem
    }

    html:not(.dark) body {
        background: #f1f5f9;
        color: #0f172a
    }

    html:not(.dark) .bg-grid {
        background-image: linear-gradient(rgba(0, 163, 114, .05) 1px, transparent 1px), linear-gradient(90deg, rgba(0, 163, 114, .05) 1px, transparent 1px)
    }

    html:not(.dark) .orb-1 {
        background: radial-gradient(circle, rgba(0, 200, 150, .07) 0%, transparent 70%)
    }

    html:not(.dark) .orb-2 {
        background: radial-gradient(circle, rgba(59, 130, 246, .05) 0%, transparent 70%)
    }

    html:not(.dark) .ticker-bar {
        background: rgba(255, 255, 255, .97);
        border-top: 1px solid rgba(0, 163, 114, .22);
        border-bottom: 1px solid rgba(0, 163, 114, .22)
    }

    html:not(.dark) .ticker-bar::before {
        background: linear-gradient(90deg, rgba(255, 255, 255, 1), transparent)
    }

    html:not(.dark) .ticker-bar::after {
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 1))
    }

    html:not(.dark) .hero-badge {
        border-color: rgba(0, 163, 114, .35);
        background: rgba(0, 163, 114, .07);
        color: #007a55
    }

    html:not(.dark) .hero-badge-dot {
        background: #007a55
    }

    html:not(.dark) .hero-title {
        color: #0f172a
    }

    html:not(.dark) .hero-accent {
        background: linear-gradient(120deg, #007a55, #0ea5e9, #007a55);
        background-size: 200% auto;
        -webkit-background-clip: text;
        background-clip: text
    }

    html:not(.dark) .hero-desc {
        color: #475569
    }

    html:not(.dark) .hero-desc span {
        color: #007a55
    }

    html:not(.dark) .btn-p {
        background: linear-gradient(135deg, #00a372, #00c896);
        color: #fff;
        box-shadow: 0 4px 14px rgba(0, 163, 114, .28)
    }

    html:not(.dark) .btn-p:hover {
        background: linear-gradient(135deg, #007a55, #00a372);
        box-shadow: 0 6px 22px rgba(0, 163, 114, .38)
    }

    html:not(.dark) .btn-s {
        border-color: rgba(0, 163, 114, .45);
        color: #007a55
    }

    html:not(.dark) .btn-s:hover {
        background: rgba(0, 163, 114, .07);
        border-color: #00a372
    }

    html:not(.dark) .stat-grid {
        background: rgba(0, 163, 114, .07);
        border-color: rgba(0, 163, 114, .12)
    }

    html:not(.dark) .stat-cell {
        background: rgba(255, 255, 255, .97)
    }

    html:not(.dark) .stat-val {
        color: #007a55
    }

    html:not(.dark) .stat-val.white {
        color: #0f172a
    }

    html:not(.dark) .panel {
        background: rgba(255, 255, 255, .97);
        border-color: rgba(0, 163, 114, .1);
        box-shadow: 0 4px 18px rgba(0, 0, 0, .05)
    }

    html:not(.dark) .panel::before {
        background: linear-gradient(90deg, transparent, rgba(0, 163, 114, .22), transparent)
    }

    html:not(.dark) .section-title {
        color: #0f172a
    }

    html:not(.dark) .section-title .accent {
        color: #007a55
    }

    html:not(.dark) .section-sub {
        color: #64748b
    }

    html:not(.dark) .feature-card {
        background: rgba(255, 255, 255, .97);
        border-color: rgba(0, 163, 114, .1)
    }

    html:not(.dark) .feature-card:hover {
        border-color: rgba(0, 163, 114, .28)
    }

    html:not(.dark) .feature-icon {
        background: rgba(0, 163, 114, .07);
        border-color: rgba(0, 163, 114, .18)
    }

    html:not(.dark) .feature-title {
        color: #0f172a
    }

    html:not(.dark) .feature-desc {
        color: #475569
    }

    html:not(.dark) .main-banner {
        border-color: rgba(0, 163, 114, .12)
    }

    html:not(.dark) .product-card {
        background: rgba(255, 255, 255, .97);
        border-color: rgba(0, 0, 0, .06)
    }

    html:not(.dark) .product-card:hover {
        border-color: rgba(0, 163, 114, .28)
    }

    html:not(.dark) .product-img {
        background: #f1f5f9
    }

    html:not(.dark) .product-name {
        color: #0f172a
    }

    html:not(.dark) .product-price {
        color: #007a55
    }

    html:not(.dark) .product-price-old {
        color: #94a3b8
    }

    html:not(.dark) .arrival-item {
        background: rgba(255, 255, 255, .97);
        border-color: rgba(0, 0, 0, .06)
    }

    html:not(.dark) .arrival-item::before {
        background: #007a55
    }

    html:not(.dark) .arrival-item:hover {
        border-color: rgba(0, 163, 114, .22)
    }

    html:not(.dark) .arrival-name {
        color: #0f172a
    }

    html:not(.dark) .arrival-type {
        color: #94a3b8
    }

    html:not(.dark) .arrival-price {
        color: #007a55
    }

    html:not(.dark) .buy-pill {
        background: rgba(0, 163, 114, .07);
        border-color: rgba(0, 163, 114, .18);
        color: #007a55
    }

    html:not(.dark) .buy-pill:hover {
        background: rgba(0, 163, 114, .13)
    }

    html:not(.dark) .sellers-panel {
        background: rgba(255, 255, 255, .97);
        border-color: rgba(0, 163, 114, .1)
    }

    html:not(.dark) .seller-row {
        border-bottom-color: rgba(0, 0, 0, .045)
    }

    html:not(.dark) .seller-name {
        color: #0f172a
    }

    html:not(.dark) .seller-sales {
        color: #94a3b8
    }

    html:not(.dark) .rank-other {
        background: rgba(0, 163, 114, .09);
        border-color: rgba(0, 163, 114, .18);
        color: #007a55
    }

    @media(max-width:480px) {
        .hero-btns {
            gap: .6rem
        }

        .btn-p,
        .btn-s,
        .btn-cg {
            padding: .75rem 1.4rem;
            font-size: .72rem
        }

        .stat-val {
            font-size: 1.3rem
        }

        .products-grid {
            grid-template-columns: repeat(2, 1fr)
        }

        .arrivals-sellers-grid {
            gap: 1.5rem
        }
    }
</style>
<div class="bg-grid"></div>
<div class="bg-orb orb-1"></div>
<div class="bg-orb orb-2"></div>
<main class="main-wrap">
    <div class="ticker-bar">
        <div class="ticker-inner">
            <span class="ticker-item"><span class="ticker-dot" style="color:#00ffaa;background:#00ffaa"></span><span style="color:#00ffaa">GASHY</span>&nbsp;<span style="color:rgba(148,163,184,.7)">$<?= number_format($price, 5) ?></span>&nbsp;<span style="color:#00ffaa">▲ 5.2%</span></span>
            <span class="ticker-item"><span class="ticker-dot" style="color:#60a5fa;background:#60a5fa"></span><span style="color:#60a5fa">SOL</span>&nbsp;<span style="color:rgba(148,163,184,.7)">$145.20</span>&nbsp;<span style="color:#60a5fa">▲ 2.1%</span></span>
            <span class="ticker-item"><span class="ticker-dot" style="color:#f97316;background:#f97316"></span><span style="color:#f97316">BTC</span>&nbsp;<span style="color:rgba(148,163,184,.7)">$68,420</span>&nbsp;<span style="color:#f97316">▲ 1.8%</span></span>
            <span class="ticker-item"><span class="ticker-dot" style="color:#a78bfa;background:#a78bfa"></span><span style="color:#a78bfa">ETH</span>&nbsp;<span style="color:rgba(148,163,184,.7)">$3,850</span>&nbsp;<span style="color:#f87171">▼ 0.5%</span></span>
            <span class="ticker-item"><span class="ticker-dot" style="color:#fbbf24;background:#fbbf24"></span><span style="color:#fbbf24">BNB</span>&nbsp;<span style="color:rgba(148,163,184,.7)">$2,400</span>&nbsp;<span style="color:#fbbf24">▲ 3.0%</span></span>
            <span style="color:rgba(148,163,184,.3);font-family:var(--fm);font-size:.5rem">◆</span>
            <span class="ticker-item"><span class="ticker-dot" style="color:#00ffaa;background:#00ffaa"></span><span style="color:#00ffaa">GASHY</span>&nbsp;<span style="color:rgba(148,163,184,.7)">$<?= number_format($price, 5) ?></span>&nbsp;<span style="color:#00ffaa">▲ 5.2%</span></span>
            <span class="ticker-item"><span class="ticker-dot" style="color:#60a5fa;background:#60a5fa"></span><span style="color:#60a5fa">SOL</span>&nbsp;<span style="color:rgba(148,163,184,.7)">$145.20</span>&nbsp;<span style="color:#60a5fa">▲ 2.1%</span></span>
            <span class="ticker-item"><span class="ticker-dot" style="color:#f97316;background:#f97316"></span><span style="color:#f97316">BTC</span>&nbsp;<span style="color:rgba(148,163,184,.7)">$68,420</span>&nbsp;<span style="color:#f97316">▲ 1.8%</span></span>
            <span class="ticker-item"><span class="ticker-dot" style="color:#a78bfa;background:#a78bfa"></span><span style="color:#a78bfa">ETH</span>&nbsp;<span style="color:rgba(148,163,184,.7)">$3,850</span>&nbsp;<span style="color:#f87171">▼ 0.5%</span></span>
            <span class="ticker-item"><span class="ticker-dot" style="color:#fbbf24;background:#fbbf24"></span><span style="color:#fbbf24">BNB</span>&nbsp;<span style="color:rgba(148,163,184,.7)">$2,400</span>&nbsp;<span style="color:#fbbf24">▲ 3.0%</span></span>
        </div>
    </div>
    <div class="content">
        <section class="hero-section reveal d1">
            <div class="hero-badge"><span class="hero-badge-dot"></span>THE APEX PREDATOR OF SOLANA MEMECOINS</div>
            <h1 class="hero-title">GASHY IS THE</h1>
            <span class="hero-accent" data-text="MEME ALPHA">MEME ALPHA</span>
            <p class="hero-desc">CoinGecko listed Solana memecoin with <span>revoked authorities</span>, <span>burned LP</span>, and <span>real utility</span>. Join the #GashyGang revolution.</p>
            <div class="hero-btns">
                <a href="https://jup.ag/swap/SOL-GASHY" target="_blank" class="btn-p"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M4.5 19.5l15-15M4.5 4.5h15v15" />
                    </svg>BUY ON JUPITER</a>
                <a href="https://www.coingecko.com/en/coins/gashy" target="_blank" class="btn-cg">🦎 COINGECKO</a>
                <button onclick="navigator.clipboard.writeText('DokPYQ33k3T9S7EEesvwvuuAtoQb4pY8NWszukKwXWjv');notyf.success('Contract Copied!')" class="btn-s"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>COPY CONTRACT</button>
            </div>
        </section>
        <div class="stat-grid reveal d2">
            <div class="stat-cell">
                <div class="stat-label">PRICE (USD)</div>
                <div class="stat-val">$<?= number_format($price, 6) ?></div>
            </div>
            <div class="stat-cell">
                <div class="stat-label">MARKET CAP</div>
                <div class="stat-val white">$<?= number_format($mcap / 1000, 1) ?>K</div>
            </div>
            <div class="stat-cell">
                <div class="stat-label">24H VOLUME</div>
                <div class="stat-val white">$<?= number_format($vol / 1000, 2) ?>K</div>
            </div>
            <div class="stat-cell">
                <div class="stat-label">LIQUIDITY</div>
                <div class="stat-val white">$<?= number_format($liq / 1000, 2) ?>K</div>
            </div>
        </div>
        <div class="panel chart-panel spacer reveal d3">
            <div class="chart-header"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#00ffaa" stroke-width="2">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                </svg><span class="chart-title">GASHY / USD — LIVE PRICE</span><span style="margin-left:auto;font-family:var(--fm);font-size:.62rem;color:rgba(148,163,184,.35);letter-spacing:.1em">SOLANA NETWORK</span></div>
            <div id="priceChart" style="height:340px"></div>
        </div>
        <div class="hero-banner-grid reveal d4">
            <div class="main-banner" id="bannerSlider" style="position:relative;overflow:hidden;">
                <?php if (!empty($banners)): ?>
                    <?php foreach ($banners as $bi => $b): ?>
                        <div class="banner-slide" data-index="<?= $bi ?>" style="position:absolute;inset:0;opacity:<?= $bi === 0 ? '1' : '0' ?>;transition:opacity .7s ease;z-index:<?= $bi === 0 ? 2 : 1 ?>">
                            <img src="./<?= htmlspecialchars($b['image_path']) ?>" alt="Banner <?= $bi + 1 ?>" style="width:100%;height:100%;object-fit:cover;">
                            <div class="main-banner-overlay"></div>
                            <div class="main-banner-content">
                                <span class="banner-tag">FEATURED DROP</span>
                                <h2 class="banner-title">GASHY <span class="hl">MARKETPLACE</span></h2>
                                <a href="<?= htmlspecialchars($b['link_url']) ?>" class="btn-p">EXPLORE NOW <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <path d="M5 12h14M12 5l7 7-7 7" />
                                    </svg></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div style="position:absolute;bottom:1rem;right:1.25rem;z-index:10;display:flex;gap:.45rem;">
                        <?php foreach ($banners as $bi => $b): ?>
                            <button onclick="goSlide(<?= $bi ?>)" id="dot-<?= $bi ?>" style="width:7px;height:7px;border-radius:50%;border:none;cursor:pointer;transition:all .25s;background:<?= $bi === 0 ? 'var(--neon)' : 'rgba(255,255,255,.3)' ?>;box-shadow:<?= $bi === 0 ? '0 0 7px var(--neon)' : '' ?>"></button>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="width:100%;height:100%;background:linear-gradient(135deg,#1e3a5f,#312e81);display:flex;align-items:center;justify-content:center;">
                        <h2 style="font-family:var(--fd);font-size:2.2rem;font-weight:900;color:#fff;text-align:center;padding:2rem;">WELCOME TO<br><span style="color:var(--neon)">GASHY BAZAAR</span></h2>
                    </div>
                <?php endif; ?>
            </div>
            <div class="side-promos">
                <a href="mystery-boxes.php<?= $mystery_box ? '?id=' . $mystery_box['id'] : '' ?>" class="promo-card promo-bg-1">
                    <svg class="promo-deco" viewBox="0 0 64 64" fill="white">
                        <path d="M32 4L4 18v28l28 14 28-14V18L32 4z" />
                    </svg>
                    <div>
                        <div class="promo-label">MYSTERY BOX</div>
                        <div class="promo-sub"><?= $mystery_box ? htmlspecialchars(strtoupper($mystery_box['title'])) : 'No Box Added' ?></div><?php if ($mystery_box): ?><div style="font-family:var(--fm);font-size:.62rem;color:rgba(255,255,255,.5);margin-top:.2rem"><?= number_format($mystery_box['price_gashy']) ?> G / OPEN</div><?php endif; ?>
                    </div>
                    <div class="promo-btn">OPEN NOW →</div>
                </a>
                <a href="lottery.php" class="promo-card promo-bg-2">
                    <svg class="promo-deco" viewBox="0 0 64 64" fill="white">
                        <circle cx="32" cy="32" r="28" /><text x="32" y="40" text-anchor="middle" font-size="20" fill="rgba(0,0,0,0.3)" font-weight="bold">G</text>
                    </svg>
                    <div>
                        <div class="promo-label">LOTTERY #<?= htmlspecialchars($lottery['round_number'] ?? '—') ?></div>
                        <div class="promo-sub">POOL: <?= number_format($lottery['prize_pool'] ?? 0) ?> G</div><?php if (!empty($lottery['draw_time'])): ?><div style="font-family:var(--fm);font-size:.62rem;color:rgba(255,255,255,.5);margin-top:.2rem">DRAW: <?= date('d M H:i', strtotime($lottery['draw_time'])) ?></div><?php endif; ?>
                    </div>
                    <div class="promo-btn">BUY TICKET →</div>
                </a>
            </div>
        </div>
        <div class="spacer reveal d5">
            <div style="text-align:center;margin-bottom:2.5rem">
                <h2 class="section-title">WHY CHOOSE <span class="accent">$GASHY?</span></h2>
                <p class="section-sub">More than just a meme — real utility, transparency, and community.</p>
            </div>
            <div class="feature-grid">
                <?php $features = [['🔐', 'SECURITY FIRST', 'Mint and freeze authorities permanently revoked. 100% LP burned. Verified on SolSniffer.'], ['✅', 'COINGECKO LISTED', 'Official listing on CoinGecko with verified pricing and market data across 17+ platforms.'], ['💎', 'ACTIVE STAKING', 'Earn rewards by staking your $GASHY. Non-custodial via Streamflow on Solana.'], ['🎨', 'LIVE NFTS', '51-piece genesis NFT collection with holder perks and exclusive benefits.'], ['🤝', 'TRANSPARENT TEAM', 'Clear founder reserve with on-chain proofs. No hidden allocations or shady practices.'], ['📈', 'REAL ROADMAP', '30/60/90 day milestones with measurable deliverables. Not just empty promises.']];
                foreach ($features as $f): ?>
                    <div class="feature-card">
                        <div class="feature-icon"><?= $f[0] ?></div>
                        <div class="feature-title"><?= $f[1] ?></div>
                        <div class="feature-desc"><?= $f[2] ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="spacer reveal d6">
            <div class="deals-header">
                <h2 class="section-title" style="margin-bottom:0"><span style="color:#fbbf24">⚡</span> FLASH DEALS</h2>
                <div class="timer-badge"><span class="timer-dot"></span>ENDS:&nbsp;<span id="countdown">04:22:19</span></div>
            </div>
            <div class="products-grid">
                <?php foreach ($flash_deals as $p): $img = json_decode($p['images'])[0] ?? 'assets/placeholder.png'; ?>
                    <a href="product.php?slug=<?= htmlspecialchars($p['slug']) ?>" class="product-card">
                        <div class="product-img"><img src="./<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['title']) ?>" loading="lazy"><span class="product-badge">-20%</span></div>
                        <div class="product-info">
                            <div class="product-name"><?= htmlspecialchars($p['title']) ?></div>
                            <div class="product-price-row"><span class="product-price"><?= number_format($p['price_gashy']) ?> G</span><span class="product-price-old"><?= number_format($p['price_gashy'] * 1.2) ?></span></div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="arrivals-sellers-grid spacer">
            <div class="reveal d1">
                <h2 class="section-title" style="margin-bottom:1.75rem">NEW ARRIVALS</h2>
                <div style="display:flex;flex-direction:column;gap:.9rem">
                    <?php foreach ($new_arrivals as $p): $img = json_decode($p['images'])[0] ?? 'assets/placeholder.png'; ?>
                        <a href="product.php?slug=<?= htmlspecialchars($p['slug']) ?>" class="arrival-item">
                            <div class="arrival-thumb"><img src="./<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['title']) ?>" loading="lazy"></div>
                            <div class="arrival-info">
                                <div class="arrival-name"><?= htmlspecialchars($p['title']) ?></div>
                                <div class="arrival-type"><?= htmlspecialchars($p['type']) ?></div>
                            </div>
                            <div class="arrival-right">
                                <div class="arrival-price"><?= number_format($p['price_gashy']) ?> G</div><span class="buy-pill">BUY NOW</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="reveal d2">
                <h2 class="section-title" style="margin-bottom:1.75rem">TOP SELLERS</h2>
                <div class="sellers-panel">
                    <?php foreach ($top_sellers as $i => $s): ?>
                        <div class="seller-row">
                            <div class="seller-rank <?= match ($i) {
                                                        0 => 'rank-1',
                                                        1 => 'rank-2',
                                                        2 => 'rank-3',
                                                        default => 'rank-other'
                                                    } ?>"><?= $i + 1 ?></div>
                            <div>
                                <div class="seller-name"><?= htmlspecialchars($s['store_name']) ?></div>
                                <div class="seller-rating"><svg width="9" height="9" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg><?= htmlspecialchars($s['rating']) ?></div>
                            </div>
                            <div class="seller-sales"><?= htmlspecialchars($s['total_sales']) ?> SOLD</div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="become-seller">
                    <div class="become-title">BECOME A SELLER</div>
                    <div class="become-sub">Launch your own crypto store today</div><a href="seller.php" class="btn-p" style="width:100%;justify-content:center">APPLY NOW</a>
                </div>
            </div>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var basePrice = <?= $price ?>;
        var pts = 48,
            cats = [],
            data = [],
            now = Date.now(),
            last = basePrice;

        function nxt(p) {
            var d = (Math.random() - .48) * .03,
                s = (Math.random() < .07) ? (Math.random() - .5) * .1 : 0,
                v = p * (1 + d + s);
            return v <= 0 ? p * 1.002 : parseFloat(v.toFixed(8))
        }
        for (var i = pts - 1; i >= 0; i--) {
            last = nxt(last);
            data.push(last);
            cats.push(new Date(now - i * 1800000).toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            }))
        }
        var dk = document.documentElement.classList.contains('dark');
        var nc = dk ? '#00ffaa' : '#007a55',
            lc = dk ? 'rgba(148,163,184,.4)' : '#64748b',
            gc = dk ? 'rgba(255,255,255,.04)' : 'rgba(0,0,0,.05)';
        var chart = new ApexCharts(document.querySelector('#priceChart'), {
            series: [{
                name: 'GASHY',
                data: data
            }],
            chart: {
                type: 'area',
                height: 340,
                background: 'transparent',
                toolbar: {
                    show: false
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                    dynamicAnimation: {
                        speed: 500
                    }
                },
                zoom: {
                    enabled: false
                }
            },
            stroke: {
                curve: 'smooth',
                width: 2,
                colors: [nc]
            },
            colors: [nc],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: dk ? .24 : .14,
                    opacityTo: 0,
                    stops: [0, 100]
                }
            },
            dataLabels: {
                enabled: false
            },
            markers: {
                size: 0,
                hover: {
                    size: 4
                }
            },
            xaxis: {
                categories: cats,
                tickAmount: 6,
                labels: {
                    style: {
                        colors: lc,
                        fontFamily: 'JetBrains Mono,monospace',
                        fontSize: '10px'
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: lc,
                        fontFamily: 'JetBrains Mono,monospace',
                        fontSize: '10px'
                    },
                    formatter: function(v) {
                        if (v < .001) return '$' + v.toFixed(6);
                        if (v < 1) return '$' + v.toFixed(4);
                        return '$' + v.toFixed(2)
                    }
                }
            },
            grid: {
                borderColor: gc,
                strokeDashArray: 4,
                padding: {
                    left: 8,
                    right: 16
                }
            },
            tooltip: {
                theme: dk ? 'dark' : 'light',
                style: {
                    fontFamily: 'JetBrains Mono,monospace',
                    fontSize: '11px'
                },
                y: {
                    formatter: function(v) {
                        return '$' + v.toFixed(6)
                    }
                }
            },
            theme: {
                mode: dk ? 'dark' : 'light'
            }
        });
        chart.render();
        setInterval(function() {
            last = nxt(last);
            data.push(last);
            data.shift();
            cats.push(new Date().toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            }));
            cats.shift();
            chart.updateSeries([{
                data: data
            }], true);
            chart.updateOptions({
                xaxis: {
                    categories: cats
                }
            }, false, false)
        }, 15000);
        new MutationObserver(function() {
            var d = document.documentElement.classList.contains('dark'),
                n = d ? '#00ffaa' : '#007a55',
                l = d ? 'rgba(148,163,184,.4)' : '#64748b',
                g = d ? 'rgba(255,255,255,.04)' : 'rgba(0,0,0,.05)';
            chart.updateOptions({
                stroke: {
                    colors: [n]
                },
                colors: [n],
                fill: {
                    gradient: {
                        opacityFrom: d ? .24 : .14
                    }
                },
                xaxis: {
                    labels: {
                        style: {
                            colors: l
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: l
                        }
                    }
                },
                grid: {
                    borderColor: g
                },
                theme: {
                    mode: d ? 'dark' : 'light'
                },
                tooltip: {
                    theme: d ? 'dark' : 'light'
                }
            }, false, true)
        }).observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class']
        });
    });
</script>
<script>
    (function() {
        var slides = document.querySelectorAll('.banner-slide'),
            dots = document.querySelectorAll('[id^="dot-"]'),
            cur = 0,
            total = slides.length;
        if (total < 2) return;

        function goSlide(n) {
            slides[cur].style.opacity = '0';
            slides[cur].style.zIndex = '1';
            dots[cur].style.background = 'rgba(255,255,255,.3)';
            dots[cur].style.boxShadow = '';
            cur = (n + total) % total;
            slides[cur].style.opacity = '1';
            slides[cur].style.zIndex = '2';
            dots[cur].style.background = 'var(--neon)';
            dots[cur].style.boxShadow = '0 0 7px var(--neon)'
        }
        window.goSlide = goSlide;
        setInterval(function() {
            goSlide(cur + 1)
        }, 5000);
    })();
</script>
<?php require_once 'footer.php'; ?>