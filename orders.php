<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) require_once 'server/init.php';
require_once 'header.php';
require_once 'sidebar.php';
?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Syne:wght@400;600;700;800&display=swap');

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
        --glow: rgba(0, 229, 195, 0.08)
    }

    html.dark {
        --bg: #080b12;
        --surface: #0e1320;
        --surface2: #131929;
        --border: rgba(255, 255, 255, 0.06);
        --text: #e8ecf4;
        --muted: #5a6478;
        --card-shadow: 0 2px 24px rgba(0, 0, 0, 0.4);
        --glow: rgba(0, 229, 195, 0.06)
    }

    * {
        font-family: 'Syne', sans-serif
    }

    .mono {
        font-family: 'Space Mono', monospace
    }

    .ord-wrap {
        background: var(--bg);
        min-height: 100vh;
        color: var(--text);
        transition: background .3s, color .3s
    }

    .ord-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
        transition: border-color .2s, box-shadow .2s;
        position: relative
    }

    .ord-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, var(--accent), var(--accent2));
        opacity: 0;
        transition: opacity .2s
    }

    .ord-card:hover::before {
        opacity: 1
    }

    .ord-card:hover {
        border-color: rgba(0, 229, 195, .2);
        box-shadow: 0 6px 28px rgba(0, 229, 195, .1)
    }

    .ord-header {
        padding: 14px 18px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px
    }

    .ord-body {
        padding: 0
    }

    .ord-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 18px;
        border-bottom: 1px solid var(--border)
    }

    .ord-item:last-child {
        border-bottom: none
    }

    .ord-item:hover {
        background: var(--glow)
    }

    .ord-img {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        object-fit: cover;
        background: var(--surface2);
        flex-shrink: 0
    }

    .ord-footer {
        padding: 12px 18px;
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px
    }

    .sh-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: .6rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        padding: 4px 10px;
        border-radius: 99px;
        border: 1px solid currentColor
    }

    .sh-badge.pending {
        color: var(--warn);
        background: rgba(245, 166, 35, .08)
    }

    .sh-badge.processing {
        color: #7c6dff;
        background: rgba(124, 109, 255, .08)
    }

    .sh-badge.shipped {
        color: #5b8af5;
        background: rgba(91, 138, 245, .08)
    }

    .sh-badge.delivered,
    .sh-badge.completed {
        color: var(--accent);
        background: rgba(0, 229, 195, .08)
    }

    .sh-badge.failed,
    .sh-badge.refunded {
        color: var(--danger);
        background: rgba(255, 77, 106, .08)
    }

    .tab-bar {
        display: flex;
        gap: 4px;
        padding: 4px;
        background: var(--surface2);
        border-radius: 10px;
        width: fit-content
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
        transition: all .2s
    }

    .tab-pill.active {
        background: var(--surface);
        color: var(--text);
        box-shadow: 0 2px 8px rgba(0, 0, 0, .1)
    }

    html.dark .tab-pill.active {
        box-shadow: 0 2px 8px rgba(0, 0, 0, .3)
    }

    .section-title {
        font-size: .65rem;
        font-weight: 700;
        letter-spacing: .15em;
        text-transform: uppercase;
        color: var(--muted);
        display: flex;
        align-items: center;
        gap: 8px
    }

    .section-title::before {
        content: '';
        display: block;
        width: 12px;
        height: 2px;
        background: var(--accent);
        border-radius: 2px
    }

    .btn-refresh {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        border: 1.5px solid var(--border);
        background: var(--surface);
        color: var(--muted);
        cursor: pointer;
        transition: all .3s
    }

    .btn-refresh:hover {
        border-color: var(--accent);
        color: var(--accent);
        transform: rotate(180deg);
        background: rgba(0, 229, 195, .06)
    }

    .btn-reveal {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: rgba(0, 229, 195, .1);
        color: var(--accent);
        border: 1.5px solid rgba(0, 229, 195, .3);
        border-radius: 9px;
        padding: 8px 16px;
        font-size: .75rem;
        font-weight: 700;
        letter-spacing: .06em;
        cursor: pointer;
        transition: all .2s
    }

    .btn-reveal:hover {
        background: rgba(0, 229, 195, .2);
        border-color: var(--accent)
    }

    .status-select {
        font-family: 'Syne', sans-serif;
        font-size: .7rem;
        font-weight: 700;
        background: var(--surface2);
        border: 1.5px solid var(--border);
        border-radius: 8px;
        padding: 5px 10px;
        color: var(--text);
        cursor: pointer;
        outline: none;
        transition: border-color .2s
    }

    .status-select:focus {
        border-color: var(--accent)
    }

    .ord-loader {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 40vh;
        gap: 14px
    }

    .ord-spinner {
        width: 38px;
        height: 38px;
        border: 2px solid var(--border);
        border-top-color: var(--accent);
        border-radius: 50%;
        animation: spin .7s linear infinite
    }

    @keyframes spin {
        to {
            transform: rotate(360deg)
        }
    }

    .ord-loader-text {
        font-size: .65rem;
        font-weight: 700;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: var(--muted)
    }

    .empty-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 40vh;
        gap: 16px;
        text-align: center
    }

    .empty-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background: var(--surface2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--muted)
    }

    .btn-browse {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--accent);
        color: #080b12;
        font-weight: 700;
        font-size: .85rem;
        padding: 10px 22px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 18px rgba(0, 229, 195, .25);
        transition: all .2s;
        text-decoration: none
    }

    .btn-browse:hover {
        background: #00ffd5;
        box-shadow: 0 6px 28px rgba(0, 229, 195, .4);
        transform: translateY(-1px)
    }

    .sh-modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 70;
        background: rgba(0, 0, 0, .75);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px
    }

    .sh-modal-overlay.hidden {
        display: none !important
    }

    .sh-modal {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 20px;
        width: 100%;
        max-width: 520px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 24px 80px rgba(0, 0, 0, .35);
        padding: 28px;
        position: relative
    }

    .sh-modal::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, var(--accent), var(--accent2));
        border-radius: 20px 20px 0 0
    }

    .sh-scroll::-webkit-scrollbar {
        width: 4px
    }

    .sh-scroll::-webkit-scrollbar-thumb {
        background: var(--border);
        border-radius: 4px
    }

    #orders-container.hidden,
    #empty-state.hidden {
        display: none !important
    }
</style>
<main class="ord-wrap pt-24 lg:pl-72 transition-all duration-300">
    <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-start justify-between gap-4 mb-8">
            <div>
                <p class="section-title mb-2">Kitta Gashy</p>
                <h1 class="mono" style="font-size:1.9rem;font-weight:700;line-height:1;color:var(--text)">Orders</h1>
            </div>
            <button class="btn-refresh" onclick="App.fetchOrders()" title="Refresh">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
        </div>
        <div class="tab-bar mb-6">
            <button id="tab-my" onclick="App.switchTab('my')" class="tab-pill active">My Orders</button>
            <button id="tab-sold" onclick="App.switchTab('sold')" class="tab-pill hidden">Sold by Me</button>
        </div>
        <div id="orders-container" class="space-y-4">
            <div class="ord-loader">
                <div class="ord-spinner"></div>
                <p class="ord-loader-text">Loading Orders</p>
            </div>
        </div>
        <div id="empty-state" class="hidden empty-wrap">
            <div class="empty-icon">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <div>
                <p style="font-size:.95rem;font-weight:700;color:var(--text);margin-bottom:6px">No orders yet</p>
                <p style="font-size:.8rem;color:var(--muted)">Browse the marketplace to make your first purchase.</p>
            </div>
            <a href="market.php" class="btn-browse">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
                Browse Market
            </a>
        </div>
    </div>
</main>
<div id="reveal-modal" class="sh-modal-overlay hidden">
    <div class="sh-modal sh-scroll" onclick="event.stopPropagation()">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
            <div>
                <p class="section-title" style="margin-bottom:4px">Decrypted Content</p>
                <h2 class="mono" style="font-size:1.1rem;font-weight:700;color:var(--text)">Order <span id="reveal-order-id"></span></h2>
            </div>
            <button onclick="document.getElementById('reveal-modal').classList.add('hidden')" style="display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border:1.5px solid var(--border);border-radius:9px;background:transparent;color:var(--muted);cursor:pointer">
                <svg style="width:16px;height:16px" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div id="reveal-list" style="display:flex;flex-direction:column;gap:10px"></div>
        <p style="font-size:.65rem;color:var(--muted);text-align:center;margin-top:16px;letter-spacing:.06em">Keep these codes safe — they are your purchased digital products.</p>
    </div>
</div>
<script src="./public/js/pages/orders.js"></script>
<?php require_once 'footer.php'; ?>