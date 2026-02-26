<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
$cats = getQuery(" SELECT * FROM categories WHERE is_active=1 ORDER BY name ASC ");
?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400&family=Syne:wght@400;600;700;800&display=swap');

    :root {
        --accent: #00e5c3;
        --accent2: #7c6dff;
        --danger: #ff4d6a;
        --warn: #f5a623;
        --bg: #f4f5f7;
        --surface: #ffffff;
        --surface2: #f0f1f4;
        --border: rgba(0, 0, 0, 0.07);
        --text: #0d0f1a;
        --muted: #6b7280;
        --card-shadow: 0 2px 16px rgba(0, 0, 0, 0.07);
        --glow: rgba(0, 229, 195, 0.15);
    }

    html.dark {
        --bg: #080b12;
        --surface: #0e1320;
        --surface2: #131929;
        --border: rgba(255, 255, 255, 0.06);
        --text: #e8ecf4;
        --muted: #5a6478;
        --card-shadow: 0 2px 24px rgba(0, 0, 0, 0.4);
        --glow: rgba(0, 229, 195, 0.08);
    }

    #hub-content,
    #hub-content * {
        font-family: 'Syne', sans-serif;
    }

    #hub-content .mono {
        font-family: 'Space Mono', monospace;
    }

    #product-modal,
    #product-modal * {
        font-family: 'Syne', sans-serif;
    }

    #product-modal .mono {
        font-family: 'Space Mono', monospace;
    }

    /* ── Layout ─────────────────────────────── */
    .sh-wrap {
        background: var(--bg);
        min-height: 100vh;
        color: var(--text);
        transition: background .3s, color .3s;
    }

    /* ── Stat Cards ──────────────────────────── */
    .sh-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: var(--card-shadow);
        transition: transform .2s, box-shadow .2s, border-color .2s;
        position: relative;
        overflow: hidden;
    }

    .sh-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 32px rgba(0, 229, 195, .12);
        border-color: rgba(0, 229, 195, .25);
    }

    .sh-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, var(--accent), var(--accent2));
        opacity: 0;
        transition: opacity .25s;
    }

    .sh-card:hover::before {
        opacity: 1;
    }

    /* accent top bar variants */
    .sh-card.accent-green::before {
        background: linear-gradient(90deg, #00e5c3, #00bfa5);
        opacity: 1;
    }

    .sh-card.accent-blue::before {
        background: linear-gradient(90deg, #7c6dff, #5b8af5);
        opacity: 1;
    }

    .sh-card.accent-pink::before {
        background: linear-gradient(90deg, #ff4d6a, #ff8c42);
        opacity: 1;
    }

    .sh-card.accent-gold::before {
        background: linear-gradient(90deg, #f5a623, #f7d060);
        opacity: 1;
    }

    /* ── Stat number ─────────────────────────── */
    .stat-val {
        font-family: 'Space Mono', monospace;
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
        letter-spacing: -.02em;
    }

    .stat-label {
        font-size: .65rem;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--muted);
    }

    /* ── Pill badge ──────────────────────────── */
    .sh-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: .6rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        padding: 3px 9px;
        border-radius: 99px;
        border: 1px solid currentColor;
    }

    .sh-badge.active {
        color: #00e5c3;
        background: rgba(0, 229, 195, .08);
    }

    .sh-badge.inactive {
        color: var(--danger);
        background: rgba(255, 77, 106, .08);
    }

    .sh-badge.pending {
        color: var(--warn);
        background: rgba(245, 166, 35, .08);
    }

    .sh-badge.approved {
        color: #00e5c3;
        background: rgba(0, 229, 195, .08);
    }

    .sh-badge.rejected {
        color: var(--danger);
        background: rgba(255, 77, 106, .08);
    }

    /* ── Buttons ─────────────────────────────── */
    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--accent);
        color: #080b12;
        font-weight: 700;
        font-size: .85rem;
        letter-spacing: .04em;
        padding: 10px 22px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        transition: all .2s;
        box-shadow: 0 4px 18px rgba(0, 229, 195, .25);
    }

    .btn-primary:hover {
        background: #00ffd5;
        box-shadow: 0 6px 28px rgba(0, 229, 195, .4);
        transform: translateY(-1px);
    }

    .btn-ghost {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: transparent;
        color: var(--accent);
        font-weight: 700;
        font-size: .8rem;
        letter-spacing: .05em;
        padding: 9px 20px;
        border-radius: 10px;
        border: 1.5px solid rgba(0, 229, 195, .35);
        cursor: pointer;
        transition: all .2s;
    }

    .btn-ghost:hover {
        background: rgba(0, 229, 195, .08);
        border-color: var(--accent);
        box-shadow: 0 4px 18px rgba(0, 229, 195, .15);
    }

    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: all .15s;
        background: transparent;
    }

    .btn-icon.edit {
        color: var(--accent2);
    }

    .btn-icon.edit:hover {
        background: rgba(124, 109, 255, .12);
    }

    .btn-icon.del {
        color: var(--danger);
    }

    .btn-icon.del:hover {
        background: rgba(255, 77, 106, .12);
    }

    .btn-icon.inv {
        color: var(--accent);
    }

    .btn-icon.inv:hover {
        background: rgba(0, 229, 195, .12);
    }

    .btn-icon.mystery {
        color: var(--warn);
    }

    .btn-icon.mystery:hover {
        background: rgba(245, 166, 35, .12);
    }

    /* ── Tab bar ─────────────────────────────── */
    .tab-bar {
        display: flex;
        gap: 4px;
        padding: 4px;
        background: var(--surface2);
        border-radius: 10px;
        width: fit-content;
    }

    .tab-pill {
        padding: 7px 18px;
        border-radius: 7px;
        font-size: .75rem;
        font-weight: 700;
        letter-spacing: .07em;
        text-transform: uppercase;
        cursor: pointer;
        border: none;
        background: transparent;
        color: var(--muted);
        transition: all .2s;
    }

    .tab-pill.active {
        background: var(--surface);
        color: var(--text);
        box-shadow: 0 2px 8px rgba(0, 0, 0, .1);
    }

    html.dark .tab-pill.active {
        box-shadow: 0 2px 8px rgba(0, 0, 0, .3);
    }

    /* ── Table ───────────────────────────────── */
    .sh-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .82rem;
    }

    .sh-table th {
        font-size: .6rem;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--muted);
        padding: 10px 16px;
        border-bottom: 1px solid var(--border);
        text-align: left;
        white-space: nowrap;
    }

    .sh-table th:last-child {
        text-align: right;
    }

    .sh-table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
        color: var(--text);
        vertical-align: middle;
    }

    .sh-table tbody tr {
        transition: background .15s;
    }

    .sh-table tbody tr:hover {
        background: var(--glow);
    }

    .sh-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* ── Product title cell ──────────────────── */
    .prod-img {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        object-fit: cover;
        background: var(--surface2);
        flex-shrink: 0;
    }

    .prod-title {
        font-weight: 700;
        font-size: .82rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 180px;
    }

    /* ── Modal ───────────────────────────────── */
    .sh-modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 70;
        background: rgba(0, 0, 0, .7);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .sh-modal-overlay.hidden {
        display: none !important;
    }

    .sh-modal {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 20px;
        width: 100%;
        max-width: 680px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 24px 80px rgba(0, 0, 0, .35);
        padding: 32px;
        position: relative;
    }

    .sh-modal::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, var(--accent), var(--accent2));
        border-radius: 20px 20px 0 0;
    }

    /* ── Inputs ──────────────────────────────── */
    .sh-label {
        display: block;
        font-size: .65rem;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--muted);
        margin-bottom: 7px;
    }

    .sh-input {
        width: 100%;
        box-sizing: border-box;
        background: var(--surface2);
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 11px 14px;
        font-size: .85rem;
        color: var(--text);
        font-family: 'Syne', sans-serif;
        transition: border-color .2s, box-shadow .2s;
        outline: none;
    }

    .sh-input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(0, 229, 195, .12);
        background: var(--surface);
    }

    .sh-input::placeholder {
        color: var(--muted);
    }

    select.sh-input {
        appearance: none;
        cursor: pointer;
    }

    textarea.sh-input {
        resize: vertical;
        min-height: 90px;
    }

    /* ── Section title ───────────────────────── */
    .section-title {
        font-size: .65rem;
        font-weight: 700;
        letter-spacing: .15em;
        text-transform: uppercase;
        color: var(--muted);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-title::before {
        content: '';
        display: block;
        width: 12px;
        height: 2px;
        background: var(--accent);
        border-radius: 2px;
    }

    /* ── Icon dot ────────────────────────────── */
    .icon-dot {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .icon-dot.green {
        background: rgba(0, 229, 195, .1);
        color: #00e5c3;
    }

    .icon-dot.blue {
        background: rgba(124, 109, 255, .1);
        color: #7c6dff;
    }

    .icon-dot.pink {
        background: rgba(255, 77, 106, .1);
        color: #ff4d6a;
    }

    .icon-dot.gold {
        background: rgba(245, 166, 35, .1);
        color: #f5a623;
    }

    /* ── Sale item ───────────────────────────── */
    .sale-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
    }

    .sale-item:last-child {
        border-bottom: none;
    }

    .sale-item:hover {
        background: var(--glow);
    }

    /* ── Scrollbar ───────────────────────────── */
    .sh-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .sh-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    .sh-scroll::-webkit-scrollbar-thumb {
        background: var(--border);
        border-radius: 4px;
    }

    /* ── Loader ──────────────────────────────── */
    .sh-loader {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 60vh;
        gap: 16px;
    }

    .sh-spinner {
        width: 44px;
        height: 44px;
        border: 2px solid var(--border);
        border-top-color: var(--accent);
        border-radius: 50%;
        animation: spin .7s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    #hub-loader.hidden {
        display: none !important;
    }

    #hub-content.hidden {
        display: none !important;
    }

    .sh-loader-text {
        font-size: .65rem;
        font-weight: 700;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: var(--muted);
    }

    /* ── Fade in ─────────────────────────────── */
    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(12px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-up {
        animation: fadeUp .4s ease both;
    }

    .fade-up-1 {
        animation-delay: .05s;
    }

    .fade-up-2 {
        animation-delay: .1s;
    }

    .fade-up-3 {
        animation-delay: .15s;
    }

    .fade-up-4 {
        animation-delay: .2s;
    }

    /* ── Wide modal variant ──────────────────── */
    .sh-modal.wide {
        max-width: 900px;
    }

    /* ── Rarity badges ───────────────────────── */
    .rarity-common {
        color: #9ca3af;
        background: rgba(156, 163, 175, .1);
        border-color: #9ca3af;
    }

    .rarity-rare {
        color: #3b82f6;
        background: rgba(59, 130, 246, .1);
        border-color: #3b82f6;
    }

    .rarity-epic {
        color: #a855f7;
        background: rgba(168, 85, 247, .1);
        border-color: #a855f7;
    }

    .rarity-legendary {
        color: #f5a623;
        background: rgba(245, 166, 35, .1);
        border-color: #f5a623;
    }

    /* ── Inventory stat mini cards ───────────── */
    .inv-stat {
        background: var(--surface2);
        border-radius: 10px;
        padding: 14px 18px;
        text-align: center;
        flex: 1;
    }

    .inv-stat-val {
        font-family: 'Space Mono', monospace;
        font-size: 1.4rem;
        font-weight: 700;
        line-height: 1;
    }

    .inv-stat-label {
        font-size: .6rem;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--muted);
        margin-top: 4px;
    }

    /* ── Code row ────────────────────────────── */
    .code-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 16px;
        border-bottom: 1px solid var(--border);
        transition: background .15s;
    }

    .code-row:last-child {
        border-bottom: none;
    }

    .code-row:hover {
        background: var(--glow);
    }

    /* ── Loot table row ──────────────────────── */
    .loot-row {
        display: grid;
        grid-template-columns: 1fr auto auto auto auto;
        align-items: center;
        gap: 12px;
        padding: 11px 16px;
        border-bottom: 1px solid var(--border);
        transition: background .15s;
    }

    .loot-row:last-child {
        border-bottom: none;
    }

    .loot-row:hover {
        background: var(--glow);
    }

    /* ── Probability bar ─────────────────────── */
    .prob-bar-wrap {
        background: var(--surface2);
        border-radius: 99px;
        height: 4px;
        width: 80px;
        overflow: hidden;
    }

    .prob-bar-fill {
        height: 100%;
        border-radius: 99px;
        background: var(--accent);
        transition: width .4s;
    }

    /* ── Modal panel split ───────────────────── */
    .modal-split {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 24px;
    }

    @media (max-width: 700px) {
        .modal-split {
            grid-template-columns: 1fr;
        }
    }

    /* ── Responsive ──────────────────────────── */
    @media (max-width: 640px) {
        .stat-val {
            font-size: 1.5rem;
        }

        .sh-modal {
            padding: 20px;
        }

        .stat-cards-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    /* ── Panel hidden fix ────────────────────── */
    .sh-modal-overlay.hidden {
        display: none !important;
    }

    #inv-modal.hidden,
    #mystery-modal.hidden {
        display: none !important;
    }
</style>

<main class="sh-wrap pt-24 lg:pl-72 transition-all duration-300">
    <div class="max-w-[1680px] mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Loader -->
        <div id="hub-loader" class="sh-loader">
            <div class="sh-spinner"></div>
            <p class="sh-loader-text">Loading Seller Hub</p>
        </div>

        <!-- Content -->
        <div id="hub-content" class="hidden space-y-8">

            <!-- ── Header ── -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 fade-up">
                <div>
                    <p class="section-title mb-2">Kitta Gashy — Seller Terminal</p>
                    <h1 style="font-family:'Space Mono',monospace; font-size:1.9rem; font-weight:700; line-height:1; color:var(--text);">
                        Seller Hub
                    </h1>
                </div>
                <button onclick="openProductModal()" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                    </svg>
                    New Product
                </button>
            </div>

            <!-- ── Stat Cards ── -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 stat-cards-grid fade-up fade-up-1">

                <!-- Available Balance -->
                <div class="sh-card accent-green p-6 flex flex-col gap-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="stat-label mb-3">Available</p>
                            <p class="stat-val" style="color:#00e5c3"><span id="stat-available">0.00</span><span style="font-size:.8rem; opacity:.5; margin-left:4px;">G</span></p>
                        </div>
                        <div class="icon-dot green">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <button onclick="requestWithdraw()" class="btn-ghost" style="width:100%; justify-content:center; font-size:.7rem;">
                        Withdraw
                    </button>
                </div>

                <!-- Lifetime Earnings -->
                <div class="sh-card accent-blue p-6 flex flex-col gap-2">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="stat-label mb-3">Lifetime Earnings</p>
                            <p class="stat-val" style="color:var(--text)"><span id="stat-earnings">0.00</span><span style="font-size:.8rem; opacity:.5; margin-left:4px;">G</span></p>
                        </div>
                        <div class="icon-dot blue">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                    <p class="stat-label" id="stat-fee" style="margin-top:auto; padding-top:12px; border-top:1px solid var(--border);">After Platform Fee</p>
                </div>

                <!-- Units Sold -->
                <div class="sh-card accent-pink p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="stat-label mb-3">Units Sold</p>
                            <p class="stat-val" style="color:var(--text)" id="stat-sales">0</p>
                        </div>
                        <div class="icon-dot pink">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Rating -->
                <div class="sh-card accent-gold p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="stat-label mb-3">Store Rating</p>
                            <p class="stat-val" style="color:#f5a623" id="stat-rating">0.0</p>
                        </div>
                        <div class="icon-dot gold">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ── Main Grid ── -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 fade-up fade-up-2">

                <!-- Left: Inventory + Withdrawals -->
                <div class="xl:col-span-2 sh-card overflow-hidden">

                    <!-- Tab bar -->
                    <div class="flex items-center justify-between p-5 border-b" style="border-color:var(--border)">
                        <div class="tab-bar">
                            <button onclick="toggleTab('products')" id="tab-products" class="tab-pill active">
                                Inventory&nbsp;<span id="stat-products" style="opacity:.6">0</span>
                            </button>
                            <button onclick="toggleTab('withdrawals')" id="tab-withdrawals" class="tab-pill">
                                Withdrawals
                            </button>
                        </div>
                    </div>

                    <!-- Products table -->
                    <div id="view-products" class="overflow-x-auto">
                        <table class="sh-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th style="text-align:right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="product-list"></tbody>
                        </table>
                    </div>

                    <!-- Withdrawals table -->
                    <div id="view-withdrawals" class="overflow-x-auto hidden">
                        <table class="sh-table">
                            <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th style="text-align:right">Date</th>
                                </tr>
                            </thead>
                            <tbody id="withdrawal-list"></tbody>
                        </table>
                    </div>

                </div>

                <!-- Right: Recent Sales -->
                <div class="xl:col-span-1 sh-card overflow-hidden">
                    <div class="flex items-center gap-2 p-5 border-b" style="border-color:var(--border)">
                        <div class="icon-dot green" style="width:28px;height:28px;border-radius:7px">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <span style="font-size:.75rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--text)">Recent Sales</span>
                    </div>
                    <div class="overflow-y-auto sh-scroll" style="max-height:420px">
                        <div id="sales-list"></div>
                    </div>
                </div>

            </div>
        </div><!-- /hub-content -->

    </div>
</main>

<!-- ══════════════════ MODAL ══════════════════ -->
<div id="product-modal" class="sh-modal-overlay hidden">
    <div class="sh-modal sh-scroll">

        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <p class="section-title mb-1">Seller Terminal</p>
                <h2 id="modal-title" style="font-family:'Space Mono',monospace; font-size:1.25rem; font-weight:700; color:var(--text)">Add Product</h2>
            </div>
            <button onclick="closeProductModal()" class="btn-icon" style="width:36px;height:36px;border:1.5px solid var(--border);border-radius:9px;color:var(--muted)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Form -->
        <form id="product-form" onsubmit="event.preventDefault(); saveProduct();">
            <input type="hidden" id="prod-id" value="0">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="sh-label">Product Title</label>
                    <input type="text" id="prod-title" required class="sh-input" placeholder="e.g. Premium Steam Key">
                </div>
                <div>
                    <label class="sh-label">Price (GASHY)</label>
                    <input type="number" step="0.01" id="prod-price" required class="sh-input mono" placeholder="0.00">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-5">
                <div>
                    <label class="sh-label">Stock Qty</label>
                    <input type="number" id="prod-stock" required class="sh-input mono" placeholder="0">
                </div>
                <div>
                    <label class="sh-label">Product Type</label>
                    <select id="prod-type" class="sh-input">
                        <option value="digital">Digital</option>
                        <option value="gift_card">Gift Card</option>
                        <option value="mystery_box">Mystery Box</option>
                        <option value="nft">NFT</option>
                        <option value="physical">Physical</option>
                    </select>
                </div>
                <div>
                    <label class="sh-label">Category</label>
                    <select id="prod-cat" class="sh-input">
                        <?php foreach ($cats as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-5">
                <label class="sh-label">Description</label>
                <textarea id="prod-desc" class="sh-input" placeholder="Describe your product..."></textarea>
            </div>

            <div class="mb-8">
                <label class="sh-label">Product Image</label>
                <input type="file" id="prod-image-file" accept="image/*" class="sh-input" style="padding:8px 14px; cursor:pointer;">
            </div>

            <button type="submit" class="btn-primary" style="width:100%; justify-content:center; padding:13px; font-size:.9rem;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                Save Product
            </button>
        </form>

    </div>
</div>

<!-- ══════════════════ INVENTORY MODAL ══════════════════ -->
<div id="inv-modal" class="sh-modal-overlay hidden">
    <div class="sh-modal wide sh-scroll" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <p class="section-title mb-1">Digital / Gift Card</p>
                <h2 id="inv-modal-title" style="font-family:'Space Mono',monospace;font-size:1.2rem;font-weight:700;color:var(--text)">Manage Codes</h2>
            </div>
            <button onclick="closeInvModal()" class="btn-icon" style="width:36px;height:36px;border:1.5px solid var(--border);border-radius:9px;color:var(--muted)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Mini stats -->
        <div style="display:flex;gap:12px;margin-bottom:20px" id="inv-stats">
            <div class="inv-stat">
                <div class="inv-stat-val" id="inv-stat-total">—</div>
                <div class="inv-stat-label">Total</div>
            </div>
            <div class="inv-stat">
                <div class="inv-stat-val" style="color:#00e5c3" id="inv-stat-available">—</div>
                <div class="inv-stat-label">Available</div>
            </div>
            <div class="inv-stat">
                <div class="inv-stat-val" style="color:#7c6dff" id="inv-stat-sold">—</div>
                <div class="inv-stat-label">Sold</div>
            </div>
        </div>

        <!-- Add codes form -->
        <div class="sh-card p-4 mb-5" style="border-radius:12px">
            <p class="sh-label mb-3">Add Codes — one per line, optional PIN after pipe <span style="font-family:monospace">CODE|PIN</span></p>
            <textarea id="inv-codes-input" class="sh-input" rows="4" placeholder="XXXX-XXXX-XXXX&#10;YYYY-YYYY|1234&#10;..."></textarea>
            <button onclick="invAddCodes()" class="btn-primary" style="margin-top:12px;width:100%;justify-content:center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                </svg>
                Import Codes
            </button>
        </div>

        <!-- Codes list -->
        <div class="sh-card overflow-hidden" style="border-radius:12px">
            <div style="padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                <span style="font-size:.7rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted)">Code List</span>
                <span style="font-size:.7rem;color:var(--muted)" id="inv-codes-count"></span>
            </div>
            <div id="inv-codes-list" class="sh-scroll" style="max-height:280px;overflow-y:auto"></div>
        </div>
    </div>
</div>

<!-- ══════════════════ MYSTERY BOX MODAL ══════════════════ -->
<div id="mystery-modal" class="sh-modal-overlay hidden">
    <div class="sh-modal wide sh-scroll" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <p class="section-title mb-1">Mystery Box</p>
                <h2 id="mystery-modal-title" style="font-family:'Space Mono',monospace;font-size:1.2rem;font-weight:700;color:var(--text)">Loot Table</h2>
            </div>
            <button onclick="closeMysteryModal()" class="btn-icon" style="width:36px;height:36px;border:1.5px solid var(--border);border-radius:9px;color:var(--muted)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="modal-split">
            <!-- Left: loot list -->
            <div class="sh-card overflow-hidden" style="border-radius:12px">
                <div style="padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
                    <span style="font-size:.7rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted)">Loot Entries</span>
                    <span id="mystery-total-prob" style="font-size:.7rem;font-family:'Space Mono',monospace;color:var(--muted)"></span>
                </div>
                <!-- Table header -->
                <div style="display:grid;grid-template-columns:1fr auto auto auto auto;gap:12px;padding:8px 16px;border-bottom:1px solid var(--border)">
                    <span style="font-size:.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted)">Reward</span>
                    <span style="font-size:.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted)">Amt</span>
                    <span style="font-size:.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted)">Rarity</span>
                    <span style="font-size:.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted)">Chance</span>
                    <span></span>
                </div>
                <div id="mystery-loot-list" class="sh-scroll" style="max-height:380px;overflow-y:auto"></div>
            </div>

            <!-- Right: add form -->
            <div>
                <div class="sh-card p-5" style="border-radius:12px">
                    <p class="sh-label mb-4">Add Loot Entry</p>
                    <div style="display:flex;flex-direction:column;gap:14px">
                        <div>
                            <label class="sh-label">Reward Type</label>
                            <select id="mystery-reward-product" class="sh-input">
                                <option value="">Tokens (GASHY)</option>
                            </select>
                        </div>
                        <div>
                            <label class="sh-label">Amount</label>
                            <input type="number" id="mystery-reward-amount" step="0.001" value="0" class="sh-input mono" placeholder="0.000">
                        </div>
                        <div>
                            <label class="sh-label">Rarity</label>
                            <select id="mystery-rarity" class="sh-input">
                                <option value="common">Common</option>
                                <option value="rare">Rare</option>
                                <option value="epic">Epic</option>
                                <option value="legendary">Legendary</option>
                            </select>
                        </div>
                        <div>
                            <label class="sh-label">Probability %</label>
                            <input type="number" id="mystery-probability" step="0.01" class="sh-input mono" placeholder="e.g. 45.00">
                        </div>
                        <button onclick="mysteryAddLoot()" class="btn-primary" style="width:100%;justify-content:center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                            </svg>
                            Add to Table
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleTab(t) {
        ['products', 'withdrawals'].forEach(x => {
            document.getElementById('view-' + x).classList.add('hidden');
            document.getElementById('tab-' + x).classList.remove('active');
        });
        document.getElementById('view-' + t).classList.remove('hidden');
        document.getElementById('tab-' + t).classList.add('active');
    }

    // ── overlay click to close ──
    ['product-modal', 'inv-modal', 'mystery-modal'].forEach(id => {
        document.getElementById(id).addEventListener('click', function(e) {
            if (e.target === this) this.classList.add('hidden');
        });
    });

    function closeProductModal() {
        document.getElementById('product-modal').classList.add('hidden');
    }

    function closeInvModal() {
        document.getElementById('inv-modal').classList.add('hidden');
    }

    function closeMysteryModal() {
        document.getElementById('mystery-modal').classList.add('hidden');
    }

    /* ═══════════════════════════════════════════
       INVENTORY MODAL
    ═══════════════════════════════════════════ */
    let _invProductId = null;
    let _invProductTitle = '';

    async function openInvModal(productId, productTitle) {
        _invProductId = productId;
        _invProductTitle = productTitle;
        document.getElementById('inv-modal-title').textContent = productTitle;
        document.getElementById('inv-codes-input').value = '';
        document.getElementById('inv-modal').classList.remove('hidden');
        await loadInvCodes();
    }

    async function loadInvCodes() {
        document.getElementById('inv-codes-list').innerHTML = `<div style="padding:24px;text-align:center;color:var(--muted);font-size:.75rem">Loading...</div>`;
        try {
            const res = await App.post('./api/seller/inventory.php', {
                product_id: _invProductId,
                action: 'list'
            });
            if (!res.status) {
                notyf.error(res.message || 'Failed to load');
                return;
            }
            const codes = res.codes || [];
            const total = codes.length;
            const sold = codes.filter(c => c.is_sold == 1).length;
            document.getElementById('inv-stat-total').textContent = total;
            document.getElementById('inv-stat-available').textContent = total - sold;
            document.getElementById('inv-stat-sold').textContent = sold;
            document.getElementById('inv-codes-count').textContent = total + ' entries';
            if (total === 0) {
                document.getElementById('inv-codes-list').innerHTML = `<div style="padding:28px;text-align:center;color:var(--muted);font-size:.75rem;letter-spacing:.08em;text-transform:uppercase">No codes yet</div>`;
                return;
            }
            document.getElementById('inv-codes-list').innerHTML = codes.map(c => `
                <div class="code-row">
                    <div style="display:flex;align-items:center;gap:10px">
                        <span style="font-family:'Space Mono',monospace;font-size:.75rem;color:var(--text)">****-****-${c.code_tail}</span>
                        ${c.has_pin ? `<span class="sh-badge" style="color:var(--accent2);background:rgba(124,109,255,.08);border-color:var(--accent2)">PIN</span>` : ''}
                    </div>
                    <div style="display:flex;align-items:center;gap:10px">
                        <span class="sh-badge ${c.is_sold == 1 ? 'inactive' : 'active'}">${c.is_sold == 1 ? 'Sold' : 'Available'}</span>
                        ${c.is_sold == 0 ? `<button onclick="invDeleteCode(${c.id})" class="btn-icon del"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>` : `<span style="width:32px"></span>`}
                    </div>
                </div>
            `).join('');
        } catch (e) {
            notyf.error('Error loading codes');
        }
    }

    async function invAddCodes() {
        const raw = document.getElementById('inv-codes-input').value.trim();
        if (!raw) {
            notyf.error('Enter at least one code');
            return;
        }
        try {
            const res = await App.post('./api/seller/inventory.php', {
                product_id: _invProductId,
                action: 'add',
                codes: raw
            });
            if (res.status) {
                notyf.success(res.message || 'Codes imported');
                document.getElementById('inv-codes-input').value = '';
                loadHub();
                loadInvCodes();
            } else notyf.error(res.message || 'Failed');
        } catch (e) {
            notyf.error('Import failed');
        }
    }

    async function invDeleteCode(cid) {
        if (!confirm('Delete this code?')) return;
        try {
            const res = await App.post('./api/seller/inventory.php', {
                product_id: _invProductId,
                action: 'delete',
                code_id: cid
            });
            if (res.status) {
                notyf.success('Code removed');
                loadHub();
                loadInvCodes();
            } else notyf.error(res.message || 'Failed');
        } catch (e) {
            notyf.error('Delete failed');
        }
    }

    /* ═══════════════════════════════════════════
       MYSTERY BOX MODAL
    ═══════════════════════════════════════════ */
    let _mysteryProductId = null;
    let _mysterySellerProds = [];

    async function openMysteryModal(productId, productTitle) {
        _mysteryProductId = productId;
        document.getElementById('mystery-modal-title').textContent = productTitle;
        document.getElementById('mystery-modal').classList.remove('hidden');
        await loadMysteryLoot();
    }

    async function loadMysteryLoot() {
        document.getElementById('mystery-loot-list').innerHTML = `<div style="padding:24px;text-align:center;color:var(--muted);font-size:.75rem">Loading...</div>`;
        try {
            const res = await App.post('./api/seller/mystery.php', {
                box_id: _mysteryProductId,
                action: 'list'
            });
            if (!res.status) {
                notyf.error(res.message || 'Failed');
                return;
            }

            // Populate reward product dropdown
            _mysterySellerProds = res.products || [];
            const sel = document.getElementById('mystery-reward-product');
            sel.innerHTML = `<option value="">Tokens (GASHY)</option>` +
                _mysterySellerProds.map(p => `<option value="${p.id}">${p.title}</option>`).join('');

            const loot = res.loot || [];
            const totalProb = loot.reduce((s, l) => s + parseFloat(l.probability), 0);
            document.getElementById('mystery-total-prob').textContent = totalProb.toFixed(2) + '% total';

            if (loot.length === 0) {
                document.getElementById('mystery-loot-list').innerHTML = `<div style="padding:28px;text-align:center;color:var(--muted);font-size:.75rem;letter-spacing:.08em;text-transform:uppercase">No loot entries yet</div>`;
                return;
            }

            const rarityColors = {
                common: 'rarity-common',
                rare: 'rarity-rare',
                epic: 'rarity-epic',
                legendary: 'rarity-legendary'
            };
            document.getElementById('mystery-loot-list').innerHTML = loot.map(l => `
                <div class="loot-row">
                    <span style="font-size:.8rem;font-weight:700;color:var(--text)">${l.reward_product_id ? '📦 ' + (l.title || 'Product') : '🪙 Tokens'}</span>
                    <span style="font-family:'Space Mono',monospace;font-size:.75rem">${parseFloat(l.reward_amount).toFixed(3)}</span>
                    <span class="sh-badge ${rarityColors[l.rarity] || ''}">${l.rarity}</span>
                    <div>
                        <div style="font-family:'Space Mono',monospace;font-size:.72rem;margin-bottom:4px">${l.probability}%</div>
                        <div class="prob-bar-wrap"><div class="prob-bar-fill" style="width:${Math.min(l.probability, 100)}%"></div></div>
                    </div>
                    <button onclick="mysteryDeleteLoot(${l.id})" class="btn-icon del"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                </div>
            `).join('');
        } catch (e) {
            notyf.error('Error loading loot');
        }
    }

    async function mysteryAddLoot() {
        const payload = {
            box_id: _mysteryProductId,
            action: 'add',
            reward_product_id: document.getElementById('mystery-reward-product').value || null,
            reward_amount: parseFloat(document.getElementById('mystery-reward-amount').value) || 0,
            rarity: document.getElementById('mystery-rarity').value,
            probability: parseFloat(document.getElementById('mystery-probability').value) || 0
        };
        if (!payload.probability) {
            notyf.error('Enter a probability');
            return;
        }
        try {
            const res = await App.post('./api/seller/mystery.php', payload);
            if (res.status) {
                notyf.success('Loot added');
                loadMysteryLoot();
            } else notyf.error(res.message || 'Failed');
        } catch (e) {
            notyf.error('Add failed');
        }
    }

    async function mysteryDeleteLoot(lid) {
        if (!confirm('Remove this loot entry?')) return;
        try {
            const res = await App.post('./api/seller/mystery.php', {
                box_id: _mysteryProductId,
                action: 'delete',
                loot_id: lid
            });
            if (res.status) {
                notyf.success('Removed');
                loadMysteryLoot();
            } else notyf.error(res.message || 'Failed');
        } catch (e) {
            notyf.error('Delete failed');
        }
    }
</script>
<script src="./public/js/pages/seller-hub.js"></script>
<?php require_once 'footer.php'; ?>