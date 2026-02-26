<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
$cat    = request('category', 'get');
$search = request('search', 'get');
$sort   = request('sort', 'get') ?? 'newest';
$min    = request('min', 'get');
$max    = request('max', 'get');
$where  = "WHERE p.status='active' AND p.stock>0";
if ($cat)    $where .= " AND c.slug='$cat' ";
if ($search) $where .= " AND (p.title LIKE '%$search%' OR p.description LIKE '%$search%') ";
if ($min)    $where .= " AND p.price_gashy >= $min ";
if ($max)    $where .= " AND p.price_gashy <= $max ";
$order = "ORDER BY p.id DESC";
if ($sort === 'price_asc')  $order = "ORDER BY p.price_gashy ASC";
if ($sort === 'price_desc') $order = "ORDER BY p.price_gashy DESC";
if ($sort === 'popular')    $order = "ORDER BY p.views DESC";
$products = getQuery(" SELECT p.id,p.title,p.slug,p.price_gashy,p.images,p.type,p.stock,c.name as cat_name,s.store_name,s.is_approved FROM products p JOIN categories c ON p.category_id=c.id JOIN sellers s ON p.seller_id=s.account_id $where $order LIMIT 50");
$cats     = getQuery(" SELECT name,slug,icon,(SELECT COUNT(*) FROM products WHERE category_id=categories.id AND status='active') as count FROM categories WHERE is_active=1");
$oracle=json_decode(@file_get_contents('server/.cache/price.json'),true)?:[];
$gashyUsd=(float)($oracle['price']??0.045);
?>
<style>
    /* ── CSS VARIABLES ── */
    :root {
        --neon: #00ffaa;
        --neon-dim: #00d48f;
        --neon-dark: #007a55;
        --accent: #8b5cf6;
        --panel-dark: rgba(13, 17, 28, 0.92);
        --panel-border-dark: rgba(0, 255, 170, 0.1);
        --text-muted-dark: #6b7280;
        --text-muted-light: #64748b;
        --font-mono: 'JetBrains Mono', monospace;
    }

    /* ── LAYOUT ── */
    .market-wrap {
        min-height: 100vh;
        padding-top: 6rem;
        padding-left: 0;
        background: #0a0e1a;
        color: #e2e8f0;
        transition: background 0.3s, color 0.3s;
    }

    @media(min-width:1024px) {
        .market-wrap {
            padding-left: 18rem
        }
    }

    .market-bg {
        position: fixed;
        inset: 0;
        pointer-events: none;
        z-index: 0;
        background-image:
            linear-gradient(rgba(0, 255, 170, 0.02) 1px, transparent 1px),
            linear-gradient(90deg, rgba(0, 255, 170, 0.02) 1px, transparent 1px);
        background-size: 60px 60px;
    }

    .market-orb-1 {
        position: fixed;
        width: 600px;
        height: 600px;
        background: #00ffaa;
        border-radius: 50%;
        filter: blur(130px);
        opacity: 0.04;
        top: -100px;
        left: 20%;
        pointer-events: none;
        z-index: 0;
    }

    .market-orb-2 {
        position: fixed;
        width: 500px;
        height: 500px;
        background: #8b5cf6;
        border-radius: 50%;
        filter: blur(130px);
        opacity: 0.04;
        bottom: -100px;
        right: 10%;
        pointer-events: none;
        z-index: 0;
    }

    .market-content {
        position: relative;
        z-index: 10;
        max-width: 1920px;
        margin: 0 auto;
        padding: 1.5rem;
    }

    /* ── FILTER PANEL ── */
    .filter-panel {
        background: rgba(8, 13, 26, 0.9);
        border: 1px solid rgba(0, 255, 170, 0.1);
        border-radius: 1rem;
        position: sticky;
        top: 7rem;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        transition: border-color 0.3s;
    }

    .filter-panel::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, rgba(0, 255, 170, 0.5), transparent);
    }

    .filter-panel-inner {
        padding: 1.5rem;
        position: relative;
        z-index: 1;
    }

    .filter-title {
        font-family: var(--font-mono);
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.2em;
        color: var(--text-muted-dark);
        text-transform: uppercase;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .filter-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: linear-gradient(90deg, rgba(0, 255, 170, 0.3), transparent);
    }

    .filter-icon-wrap {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.6rem;
        background: linear-gradient(135deg, #00d48f, #8b5cf6);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(0, 212, 143, 0.3);
    }

    /* ── CATEGORY ITEMS ── */
    .cat-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.7rem 1rem;
        border-radius: 0.6rem;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        position: relative;
        overflow: hidden;
        color: #6b7280;
    }

    .cat-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, #00d48f, #8b5cf6);
        transform: scaleY(0);
        transition: transform 0.25s;
        transform-origin: bottom;
        border-radius: 0 2px 2px 0;
    }

    .cat-item:hover::before,
    .cat-item.active::before {
        transform: scaleY(1)
    }

    .cat-item:hover {
        color: #00ffaa;
        background: rgba(0, 255, 170, 0.04)
    }

    .cat-item.active {
        background: linear-gradient(135deg, #00d48f, #00ffaa);
        color: #0a0e1a;
        font-weight: 700;
        box-shadow: 0 4px 16px rgba(0, 212, 143, 0.25);
    }

    .cat-item.active::before {
        display: none
    }

    .cat-count {
        font-family: var(--font-mono);
        font-size: 0.65rem;
        font-weight: 700;
        padding: 0.2rem 0.5rem;
        border-radius: 0.35rem;
        background: rgba(0, 0, 0, 0.25);
        color: inherit;
        opacity: 0.75;
    }

    .cat-item.active .cat-count {
        background: rgba(0, 0, 0, 0.2);
        color: #0a0e1a
    }

    /* ── INPUTS ── */
    .mkt-input {
        width: 100%;
        background: rgba(3, 5, 13, 0.7);
        border: 1px solid rgba(0, 255, 170, 0.1);
        border-radius: 0.6rem;
        padding: 0.7rem 1rem;
        font-size: 0.85rem;
        color: #e2e8f0;
        font-weight: 500;
        transition: all 0.25s;
        outline: none;
        appearance: none;
    }

    .mkt-input::placeholder {
        color: rgba(107, 114, 128, 0.7)
    }

    .mkt-input:focus {
        border-color: rgba(0, 255, 170, 0.4);
        background: rgba(3, 5, 13, 0.9);
        box-shadow: 0 0 0 3px rgba(0, 255, 170, 0.08), 0 0 16px rgba(0, 255, 170, 0.06);
    }

    .input-unit {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        font-family: var(--font-mono);
        font-size: 0.55rem;
        font-weight: 700;
        color: rgba(107, 114, 128, 0.6);
        letter-spacing: 0.05em;
        pointer-events: none;
    }

    /* ── APPLY BUTTON ── */
    .apply-btn {
        width: 100%;
        padding: 0.8rem;
        background: linear-gradient(135deg, #00d48f, #00ffaa);
        color: #0a0e1a;
        border: none;
        border-radius: 0.6rem;
        font-weight: 800;
        font-size: 0.85rem;
        letter-spacing: 0.05em;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 16px rgba(0, 212, 143, 0.3);
        position: relative;
        overflow: hidden;
    }

    .apply-btn::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, transparent, rgba(255, 255, 255, 0.15), transparent);
        transform: translateX(-100%);
        transition: transform 0.4s;
    }

    .apply-btn:hover::before {
        transform: translateX(100%)
    }

    .apply-btn:hover {
        box-shadow: 0 6px 24px rgba(0, 212, 143, 0.45);
        transform: translateY(-1px)
    }

    /* ── HEADER BAR ── */
    .mkt-header {
        background: rgba(8, 13, 26, 0.9);
        border: 1px solid rgba(0, 255, 170, 0.08);
        border-radius: 1rem;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .mkt-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(0, 255, 170, 0.3), transparent);
    }

    .mkt-header-left {
        display: flex;
        align-items: center;
        gap: 1rem
    }

    .mkt-title-bar {
        width: 3px;
        height: 2rem;
        background: linear-gradient(180deg, #00ffaa, #8b5cf6);
        border-radius: 999px;
        flex-shrink: 0;
    }

    .mkt-title {
        font-size: 1.6rem;
        font-weight: 900;
        letter-spacing: -0.02em;
        background: linear-gradient(135deg, #fff, #00ffaa);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
    }

    .mkt-count {
        font-family: var(--font-mono);
        font-size: 0.7rem;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        margin-top: 0.2rem;
    }

    .mkt-count-badge {
        padding: 0.15rem 0.5rem;
        background: rgba(0, 255, 170, 0.1);
        border: 1px solid rgba(0, 255, 170, 0.2);
        color: #00ffaa;
        border-radius: 0.35rem;
        font-weight: 700;
    }

    .sort-wrap {
        position: relative
    }

    .sort-select {
        appearance: none;
        background: rgba(3, 5, 13, 0.7);
        border: 1px solid rgba(0, 255, 170, 0.1);
        border-radius: 0.6rem;
        padding: 0.65rem 2.5rem 0.65rem 2.25rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: #e2e8f0;
        cursor: pointer;
        outline: none;
        transition: all 0.2s;
        min-width: 180px;
    }

    .sort-select:focus {
        border-color: rgba(0, 255, 170, 0.35);
        box-shadow: 0 0 0 3px rgba(0, 255, 170, 0.08);
    }

    .sort-icon-l,
    .sort-icon-r {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: #6b7280;
    }

    .sort-icon-l {
        left: 0.65rem
    }

    .sort-icon-r {
        right: 0.65rem
    }

    /* ── PRODUCT GRID ── */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 1.25rem;
    }

    @media(min-width:640px) {
        .products-grid {
            grid-template-columns: repeat(2, 1fr)
        }
    }

    @media(min-width:1280px) {
        .products-grid {
            grid-template-columns: repeat(3, 1fr)
        }
    }

    @media(min-width:1600px) {
        .products-grid {
            grid-template-columns: repeat(4, 1fr)
        }
    }

    /* ── PRODUCT CARD ── */
    .pcard {
        background: rgba(8, 13, 26, 0.85);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 1rem;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    .pcard:hover {
        border-color: rgba(0, 255, 170, 0.28);
        transform: translateY(-6px) scale(1.01);
        box-shadow: 0 16px 48px rgba(0, 255, 170, 0.1), 0 0 60px rgba(139, 92, 246, 0.06);
    }

    .pcard-img {
        aspect-ratio: 4/3;
        position: relative;
        overflow: hidden;
        background: #0c1120;
    }

    .pcard-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
        display: block;
    }

    .pcard:hover .pcard-img img {
        transform: scale(1.1)
    }

    .pcard-img-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, transparent 40%, rgba(3, 5, 13, 0.85) 100%);
        opacity: 0;
        transition: opacity 0.3s;
    }

    .pcard:hover .pcard-img-overlay {
        opacity: 1
    }

    .pcard-link {
        position: absolute;
        inset: 0;
        z-index: 10
    }

    .pcard-badges {
        position: absolute;
        top: 0.85rem;
        left: 0.85rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        z-index: 5;
    }

    .badge-type {
        padding: 0.25rem 0.6rem;
        background: rgba(3, 5, 13, 0.88);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 0.35rem;
        font-family: var(--font-mono);
        font-size: 0.6rem;
        font-weight: 700;
        color: #e2e8f0;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .badge-verified {
        padding: 0.25rem 0.6rem;
        background: rgba(0, 255, 170, 0.12);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(0, 255, 170, 0.25);
        border-radius: 0.35rem;
        font-family: var(--font-mono);
        font-size: 0.6rem;
        font-weight: 700;
        color: #00ffaa;
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    .pcard-body {
        padding: 1.1rem 1.25rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        position: relative
    }

    .pcard-body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.06), transparent);
    }

    .pcard-cat {
        font-family: var(--font-mono);
        font-size: 0.6rem;
        font-weight: 700;
        letter-spacing: 0.15em;
        color: #00d48f;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 0.35rem;
        margin-bottom: 0.5rem;
    }

    .pcard-cat-dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: #00d48f;
        flex-shrink: 0
    }

    .pcard-title {
        font-weight: 800;
        font-size: 0.95rem;
        color: #f1f5f9;
        line-height: 1.35;
        margin-bottom: 0.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-overflow: hidden;
        overflow: hidden;
        text-decoration: none;
        transition: color 0.2s;
    }

    .pcard-title:hover {
        color: #00ffaa
    }

    .pcard-seller {
        font-size: 0.75rem;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        margin-bottom: 1rem;
    }

    .pcard-seller-name {
        color: #9ca3af;
        font-weight: 600;
        transition: color 0.2s;
        cursor: pointer
    }

    .pcard-seller-name:hover {
        color: #e2e8f0
    }

    .pcard-footer {
        margin-top: auto;
        padding-top: 0.85rem;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 0.5rem;
    }

    .price-tag {
        display: inline-flex;
        align-items: baseline;
        gap: 0.4rem;
        padding: 0.4rem 0.75rem;
        background: linear-gradient(135deg, rgba(0, 212, 143, 0.1), rgba(139, 92, 246, 0.1));
        border: 1px solid rgba(0, 255, 170, 0.15);
        border-radius: 0.5rem;
        backdrop-filter: blur(10px);
    }

    .price-val {
        font-size: 1.15rem;
        font-weight: 900;
        background: linear-gradient(135deg, #00ffaa, #8b5cf6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .price-unit {
        font-family: var(--font-mono);
        font-size: 0.6rem;
        font-weight: 700;
        color: #6b7280;
    }

    .price-usd {
        font-family: var(--font-mono);
        font-size: 0.65rem;
        color: #6b7280;
        margin-top: 0.25rem;
        padding-left: 0.1rem;
    }

    .stock-badge {
        font-family: var(--font-mono);
        font-size: 0.6rem;
        font-weight: 700;
        color: #f87171;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.2);
        padding: 0.3rem 0.6rem;
        border-radius: 0.4rem;
        white-space: nowrap;
        letter-spacing: 0.05em;
    }

    /* ── EMPTY STATE ── */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 6rem 2rem;
        text-align: center;
        border: 2px dashed rgba(0, 255, 170, 0.1);
        border-radius: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .empty-state::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at center, rgba(0, 255, 170, 0.03) 0%, transparent 70%);
    }

    .empty-icon {
        width: 6rem;
        height: 6rem;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(0, 255, 170, 0.08), rgba(139, 92, 246, 0.08));
        border: 1px solid rgba(0, 255, 170, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        box-shadow: 0 0 40px rgba(0, 255, 170, 0.05);
    }

    .empty-title {
        font-size: 1.8rem;
        font-weight: 900;
        color: #f1f5f9;
        margin-bottom: 0.75rem;
        letter-spacing: -0.02em;
    }

    .empty-desc {
        font-size: 0.9rem;
        color: #6b7280;
        max-width: 28rem;
        line-height: 1.7;
        margin-bottom: 2rem
    }

    .empty-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.85rem 2rem;
        background: linear-gradient(135deg, #00d48f, #00ffaa);
        color: #0a0e1a;
        border-radius: 0.6rem;
        font-weight: 800;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.2s;
        box-shadow: 0 4px 16px rgba(0, 212, 143, 0.3);
    }

    .empty-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(0, 212, 143, 0.4)
    }

    /* ── LOAD MORE ── */
    .load-more-wrap {
        display: flex;
        justify-content: center;
        margin-top: 3rem
    }

    .load-more-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.9rem 2.5rem;
        background: rgba(0, 255, 170, 0.04);
        border: 2px solid rgba(0, 255, 170, 0.15);
        border-radius: 0.75rem;
        color: #9ca3af;
        font-weight: 800;
        font-size: 0.85rem;
        letter-spacing: 0.03em;
        cursor: pointer;
        transition: all 0.25s;
        position: relative;
        overflow: hidden;
    }

    .load-more-btn::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent, rgba(0, 255, 170, 0.08), transparent);
        transform: translateX(-100%);
        transition: transform 0.5s;
    }

    .load-more-btn:hover::before {
        transform: translateX(100%)
    }

    .load-more-btn:hover {
        border-color: rgba(0, 255, 170, 0.4);
        color: #00ffaa;
        box-shadow: 0 0 24px rgba(0, 255, 170, 0.1);
        transform: translateY(-2px);
    }

    /* ══════════════════════════════════════
   LIGHT MODE OVERRIDES
   ══════════════════════════════════════ */
    html:not(.dark) .market-wrap {
        background: #f1f5f9;
        color: #0f172a;
    }

    html:not(.dark) .market-bg {
        background-image:
            linear-gradient(rgba(0, 163, 114, 0.05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(0, 163, 114, 0.05) 1px, transparent 1px);
    }

    html:not(.dark) .market-orb-1 {
        background: #00c896;
        opacity: 0.05
    }

    html:not(.dark) .market-orb-2 {
        background: #8b5cf6;
        opacity: 0.04
    }

    /* Filter panel */
    html:not(.dark) .filter-panel {
        background: rgba(255, 255, 255, 0.97);
        border-color: rgba(0, 163, 114, 0.12);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.07);
    }

    html:not(.dark) .filter-panel::before {
        background: linear-gradient(90deg, transparent, rgba(0, 163, 114, 0.35), transparent);
    }

    html:not(.dark) .filter-title {
        color: var(--text-muted-light)
    }

    html:not(.dark) .filter-title::after {
        background: linear-gradient(90deg, rgba(0, 163, 114, 0.25), transparent)
    }

    /* Cat items */
    html:not(.dark) .cat-item {
        color: #475569
    }

    html:not(.dark) .cat-item:hover {
        color: #007a55;
        background: rgba(0, 163, 114, 0.06)
    }

    html:not(.dark) .cat-item.active {
        background: linear-gradient(135deg, #00a372, #00c896);
        color: #fff;
        box-shadow: 0 4px 16px rgba(0, 163, 114, 0.25);
    }

    html:not(.dark) .cat-item.active .cat-count {
        color: rgba(255, 255, 255, 0.85)
    }

    html:not(.dark) .cat-count {
        background: rgba(0, 0, 0, 0.08);
        color: inherit
    }

    /* Inputs */
    html:not(.dark) .mkt-input {
        background: rgba(241, 245, 249, 0.9);
        border-color: rgba(0, 0, 0, 0.1);
        color: #0f172a;
    }

    html:not(.dark) .mkt-input::placeholder {
        color: rgba(100, 116, 139, 0.6)
    }

    html:not(.dark) .mkt-input:focus {
        background: #fff;
        border-color: rgba(0, 163, 114, 0.5);
        box-shadow: 0 0 0 3px rgba(0, 163, 114, 0.1);
    }

    html:not(.dark) .input-unit {
        color: rgba(100, 116, 139, 0.6)
    }

    /* Apply btn */
    html:not(.dark) .apply-btn {
        background: linear-gradient(135deg, #00a372, #00c896);
        box-shadow: 0 4px 16px rgba(0, 163, 114, 0.3);
    }

    html:not(.dark) .apply-btn:hover {
        box-shadow: 0 6px 24px rgba(0, 163, 114, 0.4)
    }

    /* Header bar */
    html:not(.dark) .mkt-header {
        background: rgba(255, 255, 255, 0.97);
        border-color: rgba(0, 163, 114, 0.1);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
    }

    html:not(.dark) .mkt-header::before {
        background: linear-gradient(90deg, transparent, rgba(0, 163, 114, 0.25), transparent);
    }

    html:not(.dark) .mkt-title {
        background: linear-gradient(135deg, #0f172a, #007a55);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    html:not(.dark) .mkt-count {
        color: #64748b
    }

    html:not(.dark) .mkt-count-badge {
        background: rgba(0, 163, 114, 0.1);
        border-color: rgba(0, 163, 114, 0.2);
        color: #007a55;
    }

    html:not(.dark) .sort-select {
        background: rgba(241, 245, 249, 0.9);
        border-color: rgba(0, 0, 0, 0.1);
        color: #0f172a;
    }

    html:not(.dark) .sort-select:focus {
        border-color: rgba(0, 163, 114, 0.4);
        box-shadow: 0 0 0 3px rgba(0, 163, 114, 0.08);
    }

    html:not(.dark) .sort-icon-l,
    html:not(.dark) .sort-icon-r {
        color: #94a3b8
    }

    /* Product cards */
    html:not(.dark) .pcard {
        background: rgba(255, 255, 255, 0.97);
        border-color: rgba(0, 0, 0, 0.07);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    html:not(.dark) .pcard:hover {
        border-color: rgba(0, 163, 114, 0.3);
        box-shadow: 0 16px 48px rgba(0, 163, 114, 0.1), 0 0 40px rgba(139, 92, 246, 0.05);
    }

    html:not(.dark) .pcard-img {
        background: #f1f5f9
    }

    html:not(.dark) .pcard-body::before {
        background: linear-gradient(90deg, transparent, rgba(0, 0, 0, 0.05), transparent);
    }

    html:not(.dark) .badge-type {
        background: rgba(15, 23, 42, 0.07);
        border-color: rgba(0, 0, 0, 0.08);
        color: #334155;
    }

    html:not(.dark) .badge-verified {
        background: rgba(0, 163, 114, 0.1);
        border-color: rgba(0, 163, 114, 0.25);
        color: #007a55;
    }

    html:not(.dark) .pcard-cat {
        color: #007a55
    }

    html:not(.dark) .pcard-cat-dot {
        background: #007a55
    }

    html:not(.dark) .pcard-title {
        color: #0f172a
    }

    html:not(.dark) .pcard-title:hover {
        color: #007a55
    }

    html:not(.dark) .pcard-seller {
        color: #94a3b8
    }

    html:not(.dark) .pcard-seller-name {
        color: #64748b
    }

    html:not(.dark) .pcard-seller-name:hover {
        color: #0f172a
    }

    html:not(.dark) .pcard-footer {
        border-top-color: rgba(0, 0, 0, 0.06)
    }

    html:not(.dark) .price-tag {
        background: linear-gradient(135deg, rgba(0, 163, 114, 0.08), rgba(139, 92, 246, 0.08));
        border-color: rgba(0, 163, 114, 0.2);
    }

    html:not(.dark) .price-usd {
        color: #94a3b8
    }

    html:not(.dark) .stock-badge {
        color: #dc2626;
        background: rgba(220, 38, 38, 0.08);
        border-color: rgba(220, 38, 38, 0.15);
    }

    /* Empty state */
    html:not(.dark) .empty-state {
        border-color: rgba(0, 163, 114, 0.15);
        background: rgba(255, 255, 255, 0.7);
    }

    html:not(.dark) .empty-state::before {
        background: radial-gradient(circle at center, rgba(0, 163, 114, 0.04) 0%, transparent 70%);
    }

    html:not(.dark) .empty-icon {
        background: linear-gradient(135deg, rgba(0, 163, 114, 0.08), rgba(139, 92, 246, 0.06));
        border-color: rgba(0, 163, 114, 0.15);
    }

    html:not(.dark) .empty-title {
        color: #0f172a
    }

    html:not(.dark) .empty-desc {
        color: #64748b
    }

    html:not(.dark) .empty-btn {
        background: linear-gradient(135deg, #00a372, #00c896);
        color: #fff;
        box-shadow: 0 4px 16px rgba(0, 163, 114, 0.3);
    }

    html:not(.dark) .empty-btn:hover {
        box-shadow: 0 8px 28px rgba(0, 163, 114, 0.4)
    }

    /* Load more */
    html:not(.dark) .load-more-btn {
        background: rgba(0, 163, 114, 0.04);
        border-color: rgba(0, 163, 114, 0.2);
        color: #64748b;
    }

    html:not(.dark) .load-more-btn:hover {
        border-color: rgba(0, 163, 114, 0.45);
        color: #007a55;
        box-shadow: 0 0 24px rgba(0, 163, 114, 0.1);
    }

    html:not(.dark) .load-more-btn::before {
        background: linear-gradient(90deg, transparent, rgba(0, 163, 114, 0.06), transparent);
    }
</style>
<main class="market-wrap">
    <div class="market-bg"></div>
    <div class="market-orb-1"></div>
    <div class="market-orb-2"></div>
    <div class="market-content">
        <div class="flex flex-col xl:flex-row gap-6">
            <aside class="w-full xl:w-80 flex-shrink-0">
                <div class="filter-panel">
                    <div class="filter-panel-inner">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="filter-icon-wrap">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-black bg-gradient-to-r from-white to-primary-500 dark:from-white dark:to-primary-500 bg-clip-text text-transparent" style="background-image:linear-gradient(135deg,#fff,#00ffaa)">Filters</h3>
                        </div>
                        <div class="space-y-1 mb-7">
                            <div class="filter-title">Categories</div>
                            <a href="market.php" class="cat-item <?= !$cat ? 'active' : '' ?>">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                    All Items
                                </span>
                                <span class="cat-count"><?= array_sum(array_column($cats, 'count')) ?></span>
                            </a>
                            <?php foreach ($cats as $c): ?>
                                <a href="market.php?category=<?= htmlspecialchars($c['slug']) ?>" class="cat-item <?= $cat === $c['slug'] ? 'active' : '' ?>">
                                    <span class="flex items-center gap-2">
                                        <?php if ($c['icon']): ?>
                                        <img width="20px" src="./<?= htmlspecialchars($c['icon']) ?>" alt="<?= htmlspecialchars($c['icon']) ?>" loading="lazy">    
                                        <?php endif; ?>
                                        <?= htmlspecialchars($c['name']) ?>
                                    </span>
                                    <span class="cat-count"><?= $c['count'] ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <form action="market.php" method="GET" class="space-y-4">
                            <?php if ($cat): ?><input type="hidden" name="category" value="<?= htmlspecialchars($cat) ?>"><?php endif; ?>
                            <div class="filter-title" style="margin-bottom:0.75rem">Price Range</div>
                            <div class="flex gap-2">
                                <div class="flex-1 relative">
                                    <input type="number" name="min" value="<?= htmlspecialchars((string)$min) ?>" placeholder="Min" class="mkt-input pr-14">
                                    <span class="input-unit">GASHY</span>
                                </div>
                                <div class="flex-1 relative">
                                    <input type="number" name="max" value="<?= htmlspecialchars((string)$max) ?>" placeholder="Max" class="mkt-input pr-14">
                                    <span class="input-unit">GASHY</span>
                                </div>
                            </div>
                            <button type="submit" class="apply-btn">Apply Filters</button>
                        </form>
                    </div>
                </div>
            </aside>
            <div class="flex-1 min-w-0">
                <div class="mkt-header">
                    <div class="mkt-header-left">
                        <div class="mkt-title-bar"></div>
                        <div>
                            <div class="mkt-title">Marketplace</div>
                            <div class="mkt-count">
                                Showing <span class="mkt-count-badge"><?= count($products) ?></span> premium items
                            </div>
                        </div>
                    </div>
                    <div class="sort-wrap">
                        <svg class="sort-icon-l w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                        </svg>
                        <select class="sort-select" onchange="window.location.href='market.php?sort='+this.value+'<?= $cat ? '&category=' . htmlspecialchars($cat) : '' ?>'">
                            <option value="newest" <?= $sort === 'newest'     ? 'selected' : '' ?>>Newest First</option>
                            <option value="popular" <?= $sort === 'popular'    ? 'selected' : '' ?>>Most Popular</option>
                            <option value="price_asc" <?= $sort === 'price_asc'  ? 'selected' : '' ?>>Price: Low → High</option>
                            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High → Low</option>
                        </select>
                        <svg class="sort-icon-r w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
                <?php if (empty($products)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3 class="empty-title">No Items Found</h3>
                        <p class="empty-desc">We couldn't find any products matching your criteria. Try adjusting your filters or search terms.</p>
                        <a href="market.php" class="empty-btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Clear All Filters
                        </a>
                    </div>
                <?php else: ?>
                    <div class="products-grid">
                        <?php foreach ($products as $p):
                            $img = json_decode($p['images'])[0] ?? 'assets/placeholder.png';
                        ?>
                            <div class="pcard">
                                <div class="pcard-img">
                                    <img src="./<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['title']) ?>" loading="lazy">
                                    <div class="pcard-img-overlay"></div>
                                    <a href="product.php?slug=<?= htmlspecialchars($p['slug']) ?>" class="pcard-link"></a>
                                    <div class="pcard-badges">
                                        <span class="badge-type"><?= htmlspecialchars($p['type']) ?></span>
                                        <?php if ($p['is_approved']): ?>
                                            <span class="badge-verified">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                VERIFIED
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="pcard-body">
                                    <div class="pcard-cat">
                                        <span class="pcard-cat-dot"></span>
                                        <?= htmlspecialchars($p['cat_name']) ?>
                                    </div>
                                    <a href="product.php?slug=<?= htmlspecialchars($p['slug']) ?>" class="pcard-title"><?= htmlspecialchars($p['title']) ?></a>
                                    <div class="pcard-seller">
                                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span>by</span>
                                        <span class="pcard-seller-name"><?= htmlspecialchars($p['store_name']) ?></span>
                                    </div>
                                    <div class="pcard-footer">
                                        <div>
                                            <div class="price-tag">
                                                <span class="price-val"><?= number_format($p['price_gashy'], 2) ?></span>
                                                <span class="price-unit">$GASHY</span>
                                            </div>
                                            <div class="price-usd">≈ $<?= number_format($p['price_gashy'] * $gashyUsd, 7) ?> USD</div>
                                        </div>
                                        <?php if ($p['stock'] < 5): ?>
                                            <span class="stock-badge">LOW STOCK</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="load-more-wrap">
                        <button class="load-more-btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Load More Products
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>
<script src="./public/js/pages/market.js"></script>
<?php require_once 'footer.php'; ?>