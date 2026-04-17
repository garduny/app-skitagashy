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
        --surface2: #eef1f5;
        --border: rgba(0, 0, 0, .07);
        --text: #0d0f1a;
        --muted: #6b7280;
        --shadow: 0 10px 35px rgba(0, 0, 0, .08);
        --glow: rgba(0, 229, 195, .08)
    }

    html.dark {
        --bg: #070b12;
        --surface: #0e1320;
        --surface2: #141a29;
        --border: rgba(255, 255, 255, .06);
        --text: #e8ecf4;
        --muted: #6f7890;
        --shadow: 0 18px 55px rgba(0, 0, 0, .45);
        --glow: rgba(0, 229, 195, .05)
    }


    .mono {
        font-family: 'Space Mono', monospace
    }

    .ord-wrap {
        background: radial-gradient(circle at top right, var(--glow), transparent 35%), var(--bg);
        min-height: 100vh;
        color: var(--text)
    }

    .top-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 18px;
        box-shadow: var(--shadow)
    }

    .tab-bar {
        display: flex;
        gap: 6px;
        padding: 5px;
        background: var(--surface2);
        border-radius: 12px;
        width: max-content
    }

    .tab-pill {
        border: 0;
        background: transparent;
        color: var(--muted);
        padding: 8px 18px;
        border-radius: 10px;
        font-size: .74rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        cursor: pointer;
        transition: .2s
    }

    .tab-pill.active {
        background: var(--surface);
        color: var(--text);
        box-shadow: 0 8px 22px rgba(0, 0, 0, .08)
    }

    html.dark .tab-pill.active {
        box-shadow: 0 8px 22px rgba(0, 0, 0, .3)
    }

    .btn-refresh {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        border: 1px solid var(--border);
        background: var(--surface);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--muted);
        cursor: pointer;
        transition: .25s
    }

    .btn-refresh:hover {
        color: var(--accent);
        border-color: var(--accent);
        transform: rotate(180deg)
    }

    .metric {
        padding: 16px;
        border-radius: 16px;
        background: var(--surface2);
        border: 1px solid var(--border)
    }

    .ord-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 18px;
        box-shadow: var(--shadow);
        overflow: hidden;
        position: relative
    }

    .ord-card:before {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        height: 2px;
        background: linear-gradient(90deg, var(--accent), var(--accent2));
        opacity: 0;
        transition: .2s
    }

    .ord-card:hover:before {
        opacity: 1
    }

    .ord-head {
        padding: 16px 18px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap
    }

    .ord-items {
        padding: 0
    }

    .ord-item {
        display: flex;
        gap: 12px;
        padding: 14px 18px;
        border-bottom: 1px solid var(--border)
    }

    .ord-item:last-child {
        border-bottom: 0
    }

    .ord-item:hover {
        background: var(--glow)
    }

    .ord-img {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        object-fit: cover;
        background: var(--surface2);
        flex-shrink: 0
    }

    .ord-foot {
        padding: 14px 18px;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: .62rem;
        font-weight: 800;
        letter-spacing: .09em;
        text-transform: uppercase;
        border: 1px solid currentColor
    }

    .pending {
        color: var(--warn);
        background: rgba(245, 166, 35, .08)
    }

    .processing {
        color: #7c6dff;
        background: rgba(124, 109, 255, .08)
    }

    .shipped {
        color: #60a5fa;
        background: rgba(96, 165, 250, .08)
    }

    .delivered,
    .completed {
        color: var(--accent);
        background: rgba(0, 229, 195, .08)
    }

    .failed,
    .refunded {
        color: var(--danger);
        background: rgba(255, 77, 106, .08)
    }

    .btn-reveal {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 14px;
        border-radius: 11px;
        background: rgba(0, 229, 195, .1);
        border: 1px solid rgba(0, 229, 195, .28);
        color: var(--accent);
        font-size: .73rem;
        font-weight: 800;
        cursor: pointer
    }

    .btn-reveal:hover {
        background: rgba(0, 229, 195, .18)
    }

    .status-select {
        background: var(--surface2);
        border: 1px solid var(--border);
        color: var(--text);
        padding: 8px 10px;
        border-radius: 10px;
        font-size: .72rem;
        font-weight: 800
    }

    .loader {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 38vh;
        gap: 12px
    }

    .spin {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid var(--border);
        border-top-color: var(--accent);
        animation: sp .7s linear infinite
    }

    @keyframes sp {
        to {
            transform: rotate(360deg)
        }
    }

    .empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 38vh;
        text-align: center;
        gap: 16px
    }

    .btn-market {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 18px;
        border-radius: 12px;
        background: var(--accent);
        color: #071018;
        font-weight: 800;
        text-decoration: none
    }

    .modal-bg {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .75);
        backdrop-filter: blur(8px);
        z-index: 70;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px
    }

    .modal-bg.hidden {
        display: none !important
    }

    .modal {
        width: 100%;
        max-width: 560px;
        max-height: 90vh;
        overflow: auto;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 22px;
        box-shadow: 0 30px 80px rgba(0, 0, 0, .4);
        padding: 26px
    }

    .hidden {
        display: none !important
    }
</style>
<main class="ord-wrap pt-24 lg:pl-72">
    <div class="max-w-[1320px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="top-card p-5 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div>
                    <div class="text-[.68rem] font-black tracking-[.22em] uppercase text-[var(--muted)] mb-2">Kitta Gashy</div>
                    <h1 class="mono text-3xl font-bold leading-none">Orders Center</h1>
                    <p class="text-sm mt-2 text-[var(--muted)]">Track purchases, reveal digital codes and manage sales.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="tab-bar">
                        <button id="tab-my" class="tab-pill active" onclick="App.switchTab('my')">My Orders</button>
                        <button id="tab-sold" class="tab-pill hidden" onclick="App.switchTab('sold')">Sold</button>
                    </div>
                    <button class="btn-refresh" onclick="App.fetchOrders()">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mt-5">
                <div class="metric">
                    <div class="text-[.62rem] uppercase tracking-[.18em] text-[var(--muted)]">Orders</div>
                    <div id="m-orders" class="mono text-xl font-bold mt-1">0</div>
                </div>
                <div class="metric">
                    <div class="text-[.62rem] uppercase tracking-[.18em] text-[var(--muted)]">Spent</div>
                    <div id="m-spent" class="mono text-xl font-bold mt-1">0 G</div>
                </div>
                <div class="metric">
                    <div class="text-[.62rem] uppercase tracking-[.18em] text-[var(--muted)]">Completed</div>
                    <div id="m-completed" class="mono text-xl font-bold mt-1">0</div>
                </div>
                <div class="metric">
                    <div class="text-[.62rem] uppercase tracking-[.18em] text-[var(--muted)]">Pending</div>
                    <div id="m-pending" class="mono text-xl font-bold mt-1">0</div>
                </div>
            </div>
        </div>
        <div id="orders-container">
            <div class="loader">
                <div class="spin"></div>
                <div class="text-[.68rem] tracking-[.2em] uppercase font-bold text-[var(--muted)]">Loading Orders</div>
            </div>
        </div>
        <div id="empty-state" class="hidden empty">
            <div class="w-16 h-16 rounded-2xl bg-[var(--surface2)] flex items-center justify-center text-[var(--muted)]">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <div>
                <div class="font-bold text-lg">No orders yet</div>
                <div class="text-sm text-[var(--muted)] mt-1">Browse the marketplace to make your first purchase.</div>
            </div>
            <a href="market.php" class="btn-market">Browse Market</a>
        </div>
    </div>
</main>
<div id="reveal-modal" class="modal-bg hidden">
    <div class="modal" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between gap-4 mb-5">
            <div>
                <div class="text-[.65rem] uppercase tracking-[.18em] font-bold text-[var(--muted)]">Decrypted Content</div>
                <div class="mono text-xl font-bold mt-1">Order <span id="reveal-order-id"></span></div>
            </div>
            <button onclick="document.getElementById('reveal-modal').classList.add('hidden')" class="btn-refresh">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div id="reveal-list" class="space-y-3"></div>
        <div class="text-center text-[.7rem] mt-5 text-[var(--muted)]">Keep these codes safe — they are your purchased digital products.</div>
    </div>
</div>
<?php require_once 'footer.php'; ?>