<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
$cat = request('category', 'get');
$search = request('search', 'get');
$sort = request('sort', 'get') ?? 'newest';
$min = request('min', 'get');
$max = request('max', 'get');
$where = " WHERE p.status='active' AND p.stock>0 ";
if ($cat) $where .= " AND c.slug='$cat' ";
if ($search) $where .= " AND (p.title LIKE '%$search%' OR p.description LIKE '%$search%') ";
if ($min) $where .= " AND p.price_usd>=$min ";
if ($max) $where .= " AND p.price_usd<=$max ";
$order = " ORDER BY p.id DESC ";
if ($sort === 'price_asc') $order = " ORDER BY p.price_usd ASC ";
if ($sort === 'price_desc') $order = " ORDER BY p.price_usd DESC ";
if ($sort === 'popular') $order = " ORDER BY p.views DESC ";
$products = getQuery(" SELECT p.id,p.title,p.slug,p.price_usd,p.images,p.type,p.stock,c.name as cat_name,s.store_name,s.is_approved FROM products p JOIN categories c ON p.category_id=c.id JOIN sellers s ON p.seller_id=s.account_id $where $order LIMIT 50 ");
$cats = getQuery(" SELECT name,slug,icon,( SELECT COUNT(*) FROM products WHERE category_id=categories.id AND status='active' ) as count FROM categories WHERE is_active=1 ");
$rate = toGashy();
?>
<style>
    :root {
        --neon: #00ffaa;
        --neon-dim: #00d48f;
        --accent: #8b5cf6;
        --bg: #0a0e1a;
        --panel: rgba(8, 13, 26, 0.92);
        --border: rgba(0, 255, 170, 0.1);
        --text: #e2e8f0;
        --muted: #6b7280;
        --mono: 'JetBrains Mono', monospace;
    }

    .mw {
        min-height: 100vh;
        padding-top: 5rem;
        padding-left: 0;
        background: var(--bg);
        color: var(--text);
        transition: background .3s, color .3s;
    }

    @media(min-width:1024px) {
        .mw {
            padding-left: 18rem
        }
    }

    .mw-bg {
        position: fixed;
        inset: 0;
        pointer-events: none;
        z-index: 0;
        background-image: linear-gradient(rgba(0, 255, 170, .02) 1px, transparent 1px), linear-gradient(90deg, rgba(0, 255, 170, .02) 1px, transparent 1px);
        background-size: 60px 60px;
    }

    .orb1,
    .orb2 {
        position: fixed;
        border-radius: 50%;
        filter: blur(120px);
        pointer-events: none;
        z-index: 0;
    }

    .orb1 {
        width: 500px;
        height: 500px;
        background: #00ffaa;
        opacity: .03;
        top: -80px;
        left: 20%;
    }

    .orb2 {
        width: 400px;
        height: 400px;
        background: #8b5cf6;
        opacity: .03;
        bottom: -80px;
        right: 10%;
    }

    .mc {
        position: relative;
        z-index: 10;
        max-width: 1920px;
        margin: 0 auto;
        padding: 1rem;
    }

    .fp {
        background: var(--panel);
        border: 1px solid var(--border);
        border-radius: .875rem;
        position: sticky;
        top: 6rem;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0, 0, 0, .4);
    }

    .fp::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, rgba(0, 255, 170, .45), transparent);
    }

    .fp-inner {
        padding: 1rem;
        position: relative;
        z-index: 1;
    }

    .fp-head {
        display: flex;
        align-items: center;
        gap: .6rem;
        margin-bottom: .875rem;
    }

    .fp-icon {
        width: 2rem;
        height: 2rem;
        border-radius: .5rem;
        background: linear-gradient(135deg, #00d48f, #8b5cf6);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 3px 10px rgba(0, 212, 143, .3);
    }

    .fp-h {
        font-size: .875rem;
        font-weight: 900;
        line-height: 1;
    }

    .sec-label {
        font-family: var(--mono);
        font-size: .6rem;
        font-weight: 700;
        letter-spacing: .18em;
        color: var(--muted);
        text-transform: uppercase;
        margin-bottom: .5rem;
        display: flex;
        align-items: center;
        gap: .6rem;
    }

    .sec-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: linear-gradient(90deg, rgba(0, 255, 170, .25), transparent);
    }

    .ci {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .45rem .75rem;
        border-radius: .5rem;
        font-size: .78rem;
        font-weight: 600;
        text-decoration: none;
        transition: all .2s;
        position: relative;
        overflow: hidden;
        color: var(--muted);
    }

    .ci::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(180deg, #00d48f, #8b5cf6);
        transform: scaleY(0);
        transition: transform .25s;
        transform-origin: bottom;
    }

    .ci:hover::before,
    .ci.active::before {
        transform: scaleY(1);
    }

    .ci:hover {
        color: #00ffaa;
        background: rgba(0, 255, 170, .04);
    }

    .ci.active {
        background: linear-gradient(135deg, #00d48f, #00ffaa);
        color: #0a0e1a;
        font-weight: 800;
        box-shadow: 0 3px 12px rgba(0, 212, 143, .25);
    }

    .ci.active::before {
        display: none;
    }

    .ci-count {
        font-family: var(--mono);
        font-size: .58rem;
        font-weight: 700;
        padding: .15rem .4rem;
        border-radius: .3rem;
        background: rgba(0, 0, 0, .2);
        opacity: .75;
    }

    .mi {
        width: 100%;
        background: rgba(3, 5, 13, .7);
        border: 1px solid rgba(0, 255, 170, .1);
        border-radius: .5rem;
        padding: .55rem .75rem;
        font-size: .8rem;
        color: var(--text);
        font-weight: 500;
        transition: all .25s;
        outline: none;
        appearance: none;
    }

    .mi::placeholder {
        color: rgba(107, 114, 128, .6);
    }

    .mi:focus {
        border-color: rgba(0, 255, 170, .4);
        background: rgba(3, 5, 13, .9);
        box-shadow: 0 0 0 3px rgba(0, 255, 170, .07);
    }

    .iu {
        position: absolute;
        right: .65rem;
        top: 50%;
        transform: translateY(-50%);
        font-family: var(--mono);
        font-size: .52rem;
        font-weight: 700;
        color: rgba(107, 114, 128, .55);
        pointer-events: none;
        letter-spacing: .05em;
    }

    .ab {
        width: 100%;
        padding: .65rem;
        background: linear-gradient(135deg, #00d48f, #00ffaa);
        color: #0a0e1a;
        border: none;
        border-radius: .5rem;
        font-weight: 800;
        font-size: .8rem;
        letter-spacing: .04em;
        cursor: pointer;
        transition: all .2s;
        box-shadow: 0 3px 12px rgba(0, 212, 143, .3);
        position: relative;
        overflow: hidden;
    }

    .ab::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, transparent, rgba(255, 255, 255, .15), transparent);
        transform: translateX(-100%);
        transition: transform .4s;
    }

    .ab:hover::before {
        transform: translateX(100%);
    }

    .ab:hover {
        box-shadow: 0 5px 20px rgba(0, 212, 143, .4);
        transform: translateY(-1px);
    }

    .mh {
        background: var(--panel);
        border: 1px solid rgba(0, 255, 170, .07);
        border-radius: .875rem;
        padding: .875rem 1.1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: .75rem;
        margin-bottom: 1rem;
        position: relative;
        overflow: hidden;
    }

    .mh::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(0, 255, 170, .28), transparent);
    }

    .mh-l {
        display: flex;
        align-items: center;
        gap: .75rem;
    }

    .mh-bar {
        width: 3px;
        height: 1.75rem;
        background: linear-gradient(180deg, #00ffaa, #8b5cf6);
        border-radius: 999px;
        flex-shrink: 0;
    }

    .mh-title {
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: -.02em;
        background: linear-gradient(135deg, #fff, #00ffaa);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
    }

    .mh-sub {
        font-family: var(--mono);
        font-size: .62rem;
        color: var(--muted);
        display: flex;
        align-items: center;
        gap: .35rem;
        margin-top: .15rem;
    }

    .mh-badge {
        padding: .12rem .45rem;
        background: rgba(0, 255, 170, .1);
        border: 1px solid rgba(0, 255, 170, .2);
        color: #00ffaa;
        border-radius: .3rem;
        font-weight: 700;
    }

    .ss-wrap {
        position: relative;
    }

    .ss {
        appearance: none;
        background: rgba(3, 5, 13, .7);
        border: 1px solid rgba(0, 255, 170, .1);
        border-radius: .5rem;
        padding: .55rem 2.2rem .55rem 1.9rem;
        font-size: .75rem;
        font-weight: 600;
        color: var(--text);
        cursor: pointer;
        outline: none;
        transition: all .2s;
        min-width: 160px;
    }

    .ss:focus {
        border-color: rgba(0, 255, 170, .35);
        box-shadow: 0 0 0 3px rgba(0, 255, 170, .07);
    }

    .ss-il,
    .ss-ir {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: var(--muted);
    }

    .ss-il {
        left: .55rem;
    }

    .ss-ir {
        right: .55rem;
    }

    .pg {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 1rem;
    }

    @media(min-width:480px) {
        .pg {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media(min-width:1280px) {
        .pg {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media(min-width:1600px) {
        .pg {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    .pc {
        background: rgba(8, 13, 26, .88);
        border: 1px solid rgba(255, 255, 255, .05);
        border-radius: .875rem;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: all .3s cubic-bezier(.4, 0, .2, 1);
        position: relative;
    }

    .pc:hover {
        border-color: rgba(0, 255, 170, .25);
        transform: translateY(-4px) scale(1.01);
        box-shadow: 0 12px 40px rgba(0, 255, 170, .09), 0 0 50px rgba(139, 92, 246, .05);
    }

    .pc-img {
        aspect-ratio: 16/10;
        position: relative;
        overflow: hidden;
        background: #0c1120;
    }

    .pc-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .6s ease, opacity .5s ease;
        display: block;
        opacity: 0;
    }

    .pc-img img.loaded {
        opacity: 1;
    }

    .pc:hover .pc-img img {
        transform: scale(1.08);
    }

    .img-skel {
        position: absolute;
        inset: 0;
        z-index: 1;
        background: linear-gradient(90deg, #0c1120 25%, rgba(0, 255, 170, .04) 50%, #0c1120 75%);
        background-size: 200% 100%;
        animation: shimmer 1.6s infinite;
        transition: opacity .4s ease;
    }

    .img-skel.done {
        opacity: 0;
        pointer-events: none;
    }

    @keyframes shimmer {
        0% {
            background-position: 200% 0
        }

        100% {
            background-position: -200% 0
        }
    }

    .img-skel-icon {
        position: absolute;
        inset: 0;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: opacity .4s ease;
    }

    .img-skel-icon.done {
        opacity: 0;
        pointer-events: none;
    }

    .img-skel-icon svg {
        opacity: .12;
    }

    .pc-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, transparent 40%, rgba(3, 5, 13, .8) 100%);
        opacity: 0;
        transition: opacity .3s;
    }

    .pc:hover .pc-overlay {
        opacity: 1;
    }

    .pc-link {
        position: absolute;
        inset: 0;
        z-index: 10;
    }

    .pc-badges {
        position: absolute;
        top: .65rem;
        left: .65rem;
        display: flex;
        flex-wrap: wrap;
        gap: .3rem;
        z-index: 5;
    }

    .b-type {
        padding: .2rem .5rem;
        background: rgba(3, 5, 13, .85);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, .07);
        border-radius: .3rem;
        font-family: var(--mono);
        font-size: .55rem;
        font-weight: 700;
        color: #e2e8f0;
        letter-spacing: .07em;
        text-transform: uppercase;
    }

    .b-ver {
        padding: .2rem .5rem;
        background: rgba(0, 255, 170, .1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(0, 255, 170, .25);
        border-radius: .3rem;
        font-family: var(--mono);
        font-size: .55rem;
        font-weight: 700;
        color: #00ffaa;
        letter-spacing: .05em;
        display: flex;
        align-items: center;
        gap: .25rem;
    }

    .pc-body {
        padding: .875rem 1rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .pc-body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .05), transparent);
    }

    .pc-cat {
        font-family: var(--mono);
        font-size: .58rem;
        font-weight: 700;
        letter-spacing: .14em;
        color: #00d48f;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: .3rem;
        margin-bottom: .35rem;
    }

    .pc-cat-dot {
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: #00d48f;
        flex-shrink: 0;
    }

    .pc-title {
        font-weight: 800;
        font-size: .875rem;
        color: #f1f5f9;
        line-height: 1.35;
        margin-bottom: .35rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-decoration: none;
        transition: color .2s;
    }

    .pc-title:hover {
        color: #00ffaa;
    }

    .pc-seller {
        font-size: .7rem;
        color: var(--muted);
        display: flex;
        align-items: center;
        gap: .35rem;
        margin-bottom: .75rem;
    }

    .pc-sname {
        color: #9ca3af;
        font-weight: 600;
        transition: color .2s;
        cursor: pointer;
    }

    .pc-sname:hover {
        color: #e2e8f0;
    }

    .pc-foot {
        margin-top: auto;
        padding-top: .65rem;
        border-top: 1px solid rgba(255, 255, 255, .05);
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: .4rem;
    }

    .pt {
        display: inline-flex;
        align-items: baseline;
        gap: .3rem;
        padding: .3rem .6rem;
        background: linear-gradient(135deg, rgba(0, 212, 143, .1), rgba(139, 92, 246, .1));
        border: 1px solid rgba(0, 255, 170, .14);
        border-radius: .4rem;
    }

    .pt-v {
        font-size: 1rem;
        font-weight: 900;
        background: linear-gradient(135deg, #00ffaa, #8b5cf6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .pt-u {
        font-family: var(--mono);
        font-size: .55rem;
        font-weight: 700;
        color: var(--muted);
    }

    .pt-usd {
        font-family: var(--mono);
        font-size: .6rem;
        color: var(--muted);
        margin-top: .2rem;
        padding-left: .1rem;
    }

    .sb {
        font-family: var(--mono);
        font-size: .57rem;
        font-weight: 700;
        color: #f87171;
        background: rgba(239, 68, 68, .1);
        border: 1px solid rgba(239, 68, 68, .2);
        padding: .25rem .5rem;
        border-radius: .35rem;
        white-space: nowrap;
    }

    .es {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 4rem 2rem;
        text-align: center;
        border: 2px dashed rgba(0, 255, 170, .1);
        border-radius: 1.25rem;
    }

    .es-icon {
        width: 4.5rem;
        height: 4.5rem;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(0, 255, 170, .07), rgba(139, 92, 246, .07));
        border: 1px solid rgba(0, 255, 170, .14);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .es-t {
        font-size: 1.4rem;
        font-weight: 900;
        color: #f1f5f9;
        margin-bottom: .5rem;
    }

    .es-d {
        font-size: .82rem;
        color: var(--muted);
        max-width: 24rem;
        line-height: 1.65;
        margin-bottom: 1.5rem;
    }

    .es-btn {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .7rem 1.75rem;
        background: linear-gradient(135deg, #00d48f, #00ffaa);
        color: #0a0e1a;
        border-radius: .5rem;
        font-weight: 800;
        font-size: .82rem;
        text-decoration: none;
        transition: all .2s;
        box-shadow: 0 4px 14px rgba(0, 212, 143, .3);
    }

    .es-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 7px 24px rgba(0, 212, 143, .4);
    }

    html:not(.dark) .mw {
        background: #f1f5f9;
        color: #0f172a;
    }

    html:not(.dark) .mw-bg {
        background-image: linear-gradient(rgba(0, 163, 114, .04) 1px, transparent 1px), linear-gradient(90deg, rgba(0, 163, 114, .04) 1px, transparent 1px);
    }

    html:not(.dark) .orb1 {
        background: #00c896;
        opacity: .04;
    }

    html:not(.dark) .orb2 {
        background: #8b5cf6;
        opacity: .03;
    }

    html:not(.dark) .fp {
        background: rgba(255, 255, 255, .97);
        border-color: rgba(0, 163, 114, .12);
        box-shadow: 0 8px 28px rgba(0, 0, 0, .07);
    }

    html:not(.dark) .fp::before {
        background: linear-gradient(90deg, transparent, rgba(0, 163, 114, .3), transparent);
    }

    html:not(.dark) .sec-label {
        color: #64748b;
    }

    html:not(.dark) .sec-label::after {
        background: linear-gradient(90deg, rgba(0, 163, 114, .22), transparent);
    }

    html:not(.dark) .ci {
        color: #475569;
    }

    html:not(.dark) .ci:hover {
        color: #007a55;
        background: rgba(0, 163, 114, .05);
    }

    html:not(.dark) .ci.active {
        background: linear-gradient(135deg, #00a372, #00c896);
        color: #fff;
    }

    html:not(.dark) .mi {
        background: rgba(241, 245, 249, .9);
        border-color: rgba(0, 0, 0, .09);
        color: #0f172a;
    }

    html:not(.dark) .mi:focus {
        background: #fff;
        border-color: rgba(0, 163, 114, .45);
        box-shadow: 0 0 0 3px rgba(0, 163, 114, .09);
    }

    html:not(.dark) .ab {
        background: linear-gradient(135deg, #00a372, #00c896);
    }

    html:not(.dark) .mh {
        background: rgba(255, 255, 255, .97);
        border-color: rgba(0, 163, 114, .09);
        box-shadow: 0 3px 14px rgba(0, 0, 0, .06);
    }

    html:not(.dark) .mh::before {
        background: linear-gradient(90deg, transparent, rgba(0, 163, 114, .22), transparent);
    }

    html:not(.dark) .mh-title {
        background: linear-gradient(135deg, #0f172a, #007a55);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    html:not(.dark) .mh-sub {
        color: #64748b;
    }

    html:not(.dark) .mh-badge {
        background: rgba(0, 163, 114, .1);
        border-color: rgba(0, 163, 114, .2);
        color: #007a55;
    }

    html:not(.dark) .ss {
        background: rgba(241, 245, 249, .9);
        border-color: rgba(0, 0, 0, .09);
        color: #0f172a;
    }

    html:not(.dark) .ss:focus {
        border-color: rgba(0, 163, 114, .4);
        box-shadow: 0 0 0 3px rgba(0, 163, 114, .08);
    }

    html:not(.dark) .pc {
        background: rgba(255, 255, 255, .97);
        border-color: rgba(0, 0, 0, .07);
        box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
    }

    html:not(.dark) .pc:hover {
        border-color: rgba(0, 163, 114, .28);
        box-shadow: 0 12px 40px rgba(0, 163, 114, .09);
    }

    html:not(.dark) .pc-img {
        background: #f1f5f9;
    }

    html:not(.dark) .img-skel {
        background: linear-gradient(90deg, #e8eef5 25%, rgba(0, 163, 114, .06) 50%, #e8eef5 75%);
        background-size: 200% 100%;
    }

    html:not(.dark) .b-type {
        background: rgba(15, 23, 42, .06);
        border-color: rgba(0, 0, 0, .07);
        color: #334155;
    }

    html:not(.dark) .b-ver {
        background: rgba(0, 163, 114, .1);
        border-color: rgba(0, 163, 114, .22);
        color: #007a55;
    }

    html:not(.dark) .pc-cat {
        color: #007a55;
    }

    html:not(.dark) .pc-cat-dot {
        background: #007a55;
    }

    html:not(.dark) .pc-title {
        color: #0f172a;
    }

    html:not(.dark) .pc-title:hover {
        color: #007a55;
    }

    html:not(.dark) .pc-seller {
        color: #94a3b8;
    }

    html:not(.dark) .pc-sname {
        color: #64748b;
    }

    html:not(.dark) .pc-sname:hover {
        color: #0f172a;
    }

    html:not(.dark) .pc-foot {
        border-top-color: rgba(0, 0, 0, .06);
    }

    html:not(.dark) .pt {
        background: linear-gradient(135deg, rgba(0, 163, 114, .08), rgba(139, 92, 246, .07));
        border-color: rgba(0, 163, 114, .18);
    }

    html:not(.dark) .pt-usd {
        color: #94a3b8;
    }

    html:not(.dark) .sb {
        color: #dc2626;
        background: rgba(220, 38, 38, .07);
        border-color: rgba(220, 38, 38, .14);
    }

    html:not(.dark) .es {
        border-color: rgba(0, 163, 114, .14);
        background: rgba(255, 255, 255, .7);
    }

    html:not(.dark) .es-t {
        color: #0f172a;
    }

    html:not(.dark) .es-d {
        color: #64748b;
    }

    html:not(.dark) .es-btn {
        background: linear-gradient(135deg, #00a372, #00c896);
        color: #fff;
    }
</style>
<main class="mw">
    <div class="mw-bg"></div>
    <div class="orb1"></div>
    <div class="orb2"></div>
    <div class="mc">
        <div class="flex flex-col xl:flex-row gap-4">
            <aside class="w-full xl:w-72 flex-shrink-0">
                <div class="fp">
                    <div class="fp-inner">
                        <div class="fp-head">
                            <div class="fp-icon">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0a0e1a" stroke-width="2.5">
                                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
                                </svg>
                            </div>
                            <span class="fp-h">Filters</span>
                        </div>
                        <div class="space-y-0.5 mb-5">
                            <div class="sec-label">Categories</div>
                            <a href="market.php" class="ci <?= !$cat ? 'active' : '' ?>">
                                <span>All Items</span>
                                <span class="ci-count"><?= array_sum(array_column($cats, 'count')) ?></span>
                            </a>
                            <?php foreach ($cats as $c): ?>
                                <a href="market.php?category=<?= htmlspecialchars($c['slug']) ?>" class="ci <?= $cat === $c['slug'] ? 'active' : '' ?>">
                                    <span class="flex items-center gap-1.5">
                                        <?php if ($c['icon']): ?>
                                            <img width="16" src="./<?= htmlspecialchars($c['icon']) ?>" loading="lazy">
                                        <?php endif; ?>
                                        <?= htmlspecialchars($c['name']) ?>
                                    </span>
                                    <span class="ci-count"><?= $c['count'] ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <form action="market.php" method="GET" class="space-y-3">
                            <?php if ($cat): ?><input type="hidden" name="category" value="<?= htmlspecialchars($cat) ?>"><?php endif; ?>
                            <div class="sec-label">Price Range</div>
                            <div class="flex gap-2">
                                <div class="flex-1 relative">
                                    <input type="number" name="min" value="<?= htmlspecialchars((string)$min) ?>" placeholder="Min" class="mi pr-10">
                                    <span class="iu">USD</span>
                                </div>
                                <div class="flex-1 relative">
                                    <input type="number" name="max" value="<?= htmlspecialchars((string)$max) ?>" placeholder="Max" class="mi pr-10">
                                    <span class="iu">USD</span>
                                </div>
                            </div>
                            <button type="submit" class="ab">Apply Filters</button>
                        </form>
                    </div>
                </div>
            </aside>
            <div class="flex-1 min-w-0">
                <div class="mh">
                    <div class="mh-l">
                        <div class="mh-bar"></div>
                        <div>
                            <div class="mh-title">Marketplace</div>
                            <div class="mh-sub">Showing <span class="mh-badge"><?= count($products) ?></span> items</div>
                        </div>
                    </div>
                    <div class="ss-wrap">
                        <svg class="ss-il" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 6h18M7 12h10M11 18h2" />
                        </svg>
                        <select class="ss" onchange="window.location.href='market.php?sort='+this.value+'<?= $cat ? '&category=' . htmlspecialchars($cat) : '' ?>'">
                            <option value="newest" <?= $sort === 'newest'     ? 'selected' : '' ?>>Newest First</option>
                            <option value="popular" <?= $sort === 'popular'    ? 'selected' : '' ?>>Most Popular</option>
                            <option value="price_asc" <?= $sort === 'price_asc'  ? 'selected' : '' ?>>Price: Low → High</option>
                            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High → Low</option>
                        </select>
                        <svg class="ss-ir" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M6 9l6 6 6-6" />
                        </svg>
                    </div>
                </div>
                <?php if (empty($products)): ?>
                    <div class="es">
                        <div class="es-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#00ffaa" stroke-width="1.5">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.35-4.35" />
                            </svg>
                        </div>
                        <h3 class="es-t">No Items Found</h3>
                        <p class="es-d">Try adjusting your filters or browse all available items.</p>
                        <a href="market.php" class="es-btn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M3 12a9 9 0 1 0 18 0 9 9 0 0 0-18 0" />
                                <path d="M3 12h18" />
                            </svg>
                            Clear Filters
                        </a>
                    </div>
                <?php else: ?>
                    <div class="pg">
                        <?php foreach ($products as $p):
                            $img = json_decode($p['images'])[0] ?? 'assets/placeholder.png';
                            $g = $rate > 0 ? $p['price_usd'] / $rate : 0;
                        ?>
                            <div class="pc">
                                <div class="pc-img">
                                    <div class="img-skel"></div>
                                    <div class="img-skel-icon">
                                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#00ffaa" stroke-width="1">
                                            <rect x="3" y="3" width="18" height="18" rx="2" />
                                            <circle cx="8.5" cy="8.5" r="1.5" />
                                            <polyline points="21 15 16 10 5 21" />
                                        </svg>
                                    </div>
                                    <img data-src="./<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['title']) ?>" loading="lazy" class="lazy-img">
                                    <div class="pc-overlay"></div>
                                    <a href="product.php?slug=<?= htmlspecialchars($p['slug']) ?>" class="pc-link"></a>
                                    <div class="pc-badges">
                                        <span class="b-type"><?= htmlspecialchars($p['type']) ?></span>
                                        <?php if ($p['is_approved']): ?>
                                            <span class="b-ver">
                                                <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                                    <polyline points="20 6 9 17 4 12" />
                                                </svg>
                                                VERIFIED
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="pc-body">
                                    <div class="pc-cat"><span class="pc-cat-dot"></span><?= htmlspecialchars($p['cat_name']) ?></div>
                                    <a href="product.php?slug=<?= htmlspecialchars($p['slug']) ?>" class="pc-title"><?= htmlspecialchars($p['title']) ?></a>
                                    <div class="pc-seller">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                            <circle cx="12" cy="7" r="4" />
                                        </svg>
                                        <span class="pc-sname"><?= htmlspecialchars($p['store_name']) ?></span>
                                    </div>
                                    <div class="pc-foot">
                                        <div>
                                            <div class="pt">
                                                <span class="pt-v"><?= number_format($g, 2) ?></span>
                                                <span class="pt-u">$GASHY</span>
                                            </div>
                                            <div class="pt-usd">≈ $<?= number_format($p['price_usd'], 7) ?> USD</div>
                                        </div>
                                        <?php if ($p['stock'] < 5): ?>
                                            <span class="sb">LOW STOCK</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>
<script>
    (function() {
        const imgs = document.querySelectorAll('img.lazy-img');
        if (!imgs.length) return;
        const reveal = (img) => {
            const skel = img.parentElement.querySelector('.img-skel');
            const icon = img.parentElement.querySelector('.img-skel-icon');
            img.src = img.dataset.src;
            img.onload = () => {
                img.classList.add('loaded');
                if (skel) skel.classList.add('done');
                if (icon) icon.classList.add('done');
            };
            img.onerror = () => {
                if (skel) skel.classList.add('done');
                if (icon) icon.classList.add('done');
            };
        };
        if ('IntersectionObserver' in window) {
            const io = new IntersectionObserver((entries, obs) => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        reveal(e.target);
                        obs.unobserve(e.target);
                    }
                });
            }, {
                rootMargin: '200px 0px',
                threshold: 0
            });
            imgs.forEach(img => io.observe(img));
        } else {
            imgs.forEach(reveal);
        }
    })();
</script>
<?php require_once 'footer.php'; ?>