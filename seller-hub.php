<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) require_once 'server/init.php';
require_once 'header.php';
require_once 'sidebar.php';
$cats = getQuery(" SELECT * FROM categories WHERE is_active=1 ORDER BY name ASC ");
?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Syne:wght@400;600;700;800&display=swap');

    :root {
        --accent: #00e5c3;
        --accent2: #7c6dff;
        --danger: #ff4d6a;
        --warn: #f5a623;
        --ok: #22c55e;
        --bg: #f4f6f8;
        --surface: #ffffff;
        --surface2: #eef1f5;
        --surface3: #e7ebf0;
        --text: #0f172a;
        --muted: #6b7280;
        --line: rgba(15, 23, 42, .08);
        --shadow: 0 12px 34px rgba(15, 23, 42, .08);
        --glow: 0 0 0 1px rgba(0, 229, 195, .12), 0 16px 40px rgba(0, 229, 195, .08);
    }

    html.dark {
        --bg: #070b12;
        --surface: #0e1420;
        --surface2: #131b29;
        --surface3: #1a2434;
        --text: #eef2ff;
        --muted: #8a94a7;
        --line: rgba(255, 255, 255, .06);
        --shadow: 0 18px 44px rgba(0, 0, 0, .45);
        --glow: 0 0 0 1px rgba(0, 229, 195, .08), 0 18px 40px rgba(0, 229, 195, .05);
    }

    .mono {
        font-family: 'Space Mono', monospace
    }

    .sh-wrap {
        min-height: 100vh;
        background:
            radial-gradient(circle at top right, rgba(124, 109, 255, .08), transparent 28%),
            radial-gradient(circle at top left, rgba(0, 229, 195, .08), transparent 24%),
            var(--bg);
        color: var(--text);
    }

    .sh-shell {
        max-width: 1700px;
        margin: auto;
        padding: 32px 18px
    }

    .sh-card {
        background: linear-gradient(180deg, var(--surface), var(--surface));
        border: 1px solid var(--line);
        border-radius: 18px;
        box-shadow: var(--shadow);
        position: relative;
        overflow: hidden;
        transition: .2s ease;
    }

    .sh-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--glow)
    }

    .sh-card:before {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        height: 2px;
        background: linear-gradient(90deg, var(--accent), var(--accent2));
        opacity: .9;
    }

    .sh-soft {
        background: var(--surface2)
    }

    .sh-title {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -.03em;
    }

    .sh-sub {
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .18em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .stat-num {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .stat-num.sm {
        font-size: 1.55rem
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: .62rem;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
        border: 1px solid currentColor;
    }

    .badge.active {
        color: var(--ok);
        background: rgba(34, 197, 94, .08)
    }

    .badge.inactive {
        color: var(--danger);
        background: rgba(255, 77, 106, .08)
    }

    .badge.pending {
        color: var(--warn);
        background: rgba(245, 166, 35, .08)
    }

    .badge.approved {
        color: var(--ok);
        background: rgba(34, 197, 94, .08)
    }

    .badge.rejected {
        color: var(--danger);
        background: rgba(255, 77, 106, .08)
    }

    .badge.digital {
        color: #3b82f6;
        background: rgba(59, 130, 246, .08)
    }

    .badge.gift {
        color: #8b5cf6;
        background: rgba(139, 92, 246, .08)
    }

    .badge.mystery {
        color: #f59e0b;
        background: rgba(245, 158, 11, .08)
    }

    .badge.physical {
        color: #14b8a6;
        background: rgba(20, 184, 166, .08)
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: none;
        cursor: pointer;
        transition: .2s;
        font-weight: 800;
        letter-spacing: .04em;
        border-radius: 12px;
        padding: 11px 18px;
        font-size: .82rem;
    }

    .btn:disabled {
        opacity: .55;
        cursor: not-allowed
    }

    .btn-main {
        background: var(--accent);
        color: #041012;
        box-shadow: 0 12px 24px rgba(0, 229, 195, .22)
    }

    .btn-main:hover {
        transform: translateY(-1px);
        filter: brightness(1.04)
    }

    .btn-alt {
        background: var(--surface2);
        color: var(--text);
        border: 1px solid var(--line)
    }

    .btn-alt:hover {
        background: var(--surface3)
    }

    .btn-red {
        background: rgba(255, 77, 106, .12);
        color: var(--danger)
    }

    .btn-red:hover {
        background: rgba(255, 77, 106, .18)
    }

    .icon-btn {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        color: var(--muted);
        transition: .15s;
    }

    .icon-btn:hover {
        background: var(--surface2);
        color: var(--text)
    }

    .icon-btn.red:hover {
        background: rgba(255, 77, 106, .12);
        color: var(--danger)
    }

    .icon-btn.green:hover {
        background: rgba(0, 229, 195, .12);
        color: var(--accent)
    }

    .icon-btn.purple:hover {
        background: rgba(124, 109, 255, .12);
        color: var(--accent2)
    }

    .icon-btn.gold:hover {
        background: rgba(245, 166, 35, .12);
        color: var(--warn)
    }

    .toolbar {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center
    }

    .input,
    .select,
    .textarea {
        width: 100%;
        background: var(--surface2);
        border: 1px solid var(--line);
        border-radius: 12px;
        padding: 11px 14px;
        color: var(--text);
        outline: none;
        font-size: .88rem;
    }

    .input:focus,
    .select:focus,
    .textarea:focus {
        border-color: rgba(0, 229, 195, .45);
        box-shadow: 0 0 0 3px rgba(0, 229, 195, .08);
        background: var(--surface);
    }

    .textarea {
        resize: vertical;
        min-height: 110px
    }

    .label {
        display: block;
        font-size: .66rem;
        font-weight: 800;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: var(--muted);
        margin-bottom: 8px;
    }

    .tabbar {
        display: flex;
        gap: 6px;
        padding: 5px;
        background: var(--surface2);
        border-radius: 14px;
        width: max-content;
        max-width: 100%;
        overflow: auto;
    }

    .tabbtn {
        border: none;
        background: transparent;
        padding: 9px 16px;
        border-radius: 10px;
        font-size: .74rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--muted);
        cursor: pointer;
        white-space: nowrap;
    }

    .tabbtn.active {
        background: var(--surface);
        color: var(--text);
        box-shadow: 0 6px 16px rgba(0, 0, 0, .08);
    }

    .table-wrap {
        overflow: auto
    }

    .table {
        width: 100%;
        border-collapse: collapse
    }

    .table th {
        padding: 12px 14px;
        font-size: .63rem;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: var(--muted);
        text-align: left;
        border-bottom: 1px solid var(--line);
        white-space: nowrap;
    }

    .table td {
        padding: 13px 14px;
        border-bottom: 1px solid var(--line);
        vertical-align: middle;
        font-size: .84rem;
    }

    .table tr:hover td {
        background: rgba(0, 229, 195, .03)
    }

    .prod {
        display: flex;
        gap: 12px;
        align-items: center;
        min-width: 220px
    }

    .thumb {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        object-fit: cover;
        flex: none;
        background: var(--surface2);
        border: 1px solid var(--line)
    }

    .thumb-lg {
        width: 100%;
        height: 180px;
        border-radius: 16px;
        object-fit: cover;
        background: var(--surface2);
        border: 1px solid var(--line)
    }

    .empty {
        padding: 42px 18px;
        text-align: center;
        color: var(--muted)
    }

    .empty b {
        display: block;
        color: var(--text);
        font-size: .95rem;
        margin-bottom: 6px
    }

    .modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 80;
        background: rgba(0, 0, 0, .72);
        backdrop-filter: blur(8px);
        padding: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-backdrop.hidden {
        display: none !important
    }

    .sh-modal {
        width: 100%;
        max-width: 760px;
        max-height: 92vh;
        overflow: auto;
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: 22px;
        padding: 26px;
        position: relative;
        box-shadow: 0 28px 80px rgba(0, 0, 0, .35);
    }

    .sh-modal.wide {
        max-width: 1120px
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px
    }

    .grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px
    }

    .grid-4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px
    }

    .skeleton {
        height: 78px;
        border-radius: 16px;
        background: linear-gradient(90deg, var(--surface2), var(--surface3), var(--surface2));
        background-size: 200% 100%;
        animation: shimmer 1.2s linear infinite;
    }

    @keyframes shimmer {
        to {
            background-position: -200% 0
        }
    }

    @media(max-width:1100px) {
        .grid-4 {
            grid-template-columns: repeat(2, 1fr)
        }
    }

    @media(max-width:760px) {
        .sh-shell {
            padding: 22px 12px
        }

        .grid-2,
        .grid-3,
        .grid-4 {
            grid-template-columns: 1fr
        }

        .sh-title {
            font-size: 1.55rem
        }

        .stat-num {
            font-size: 1.55rem
        }

        .sh-modal {
            padding: 18px
        }

        .table thead {
            display: none
        }

        .table,
        .table tbody,
        .table tr,
        .table td {
            display: block;
            width: 100%
        }

        .table tr {
            border-bottom: 1px solid var(--line);
            padding: 10px 0
        }

        .table td {
            border: none;
            padding: 6px 0
        }

        .table td:before {
            content: attr(data-label);
            display: block;
            font-size: .62rem;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 4px;
            font-weight: 800;
        }
    }
</style>
<main class="sh-wrap pt-24 lg:pl-72 transition-all duration-300">
    <div class="sh-shell">
        <div id="hub-loader" class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="skeleton"></div>
                <div class="skeleton"></div>
                <div class="skeleton"></div>
                <div class="skeleton"></div>
            </div>
            <div class="skeleton" style="height:420px"></div>
        </div>

        <div id="hub-content" class="hidden space-y-6">
            <div class="sh-card p-5 md:p-6">
                <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-5">
                    <div class="space-y-2">
                        <div class="sh-sub">Kitta Gashy Seller Terminal</div>
                        <div class="sh-title">Seller Hub</div>
                        <div class="text-sm text-[var(--muted)] max-w-2xl">Manage products, gift card options, code inventory, mystery box loot, payouts, and seller performance from one premium control center.</div>
                    </div>
                    <div class="toolbar w-full xl:w-auto">
                        <button type="button" onclick="loadHub()" class="btn btn-alt">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m14.836 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-14.837-2m14.837 2H15" />
                            </svg>
                            Refresh
                        </button>
                        <button type="button" onclick="openWithdrawModal()" class="btn btn-alt">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a5 5 0 00-10 0v2m-2 0h14a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2v-7a2 2 0 012-2z" />
                            </svg>
                            Withdraw
                        </button>
                        <button type="button" onclick="openProductModal()" class="btn btn-main">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                            </svg>
                            New Product
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid-4">
                <div class="sh-card p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="sh-sub mb-3">Available</div>
                            <div class="stat-num" style="color:var(--accent)"><span id="stat-available">0.00</span><span class="mono" style="font-size:.82rem;opacity:.55;margin-left:4px">G</span></div>
                        </div>
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(0,229,195,.1);color:var(--accent)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between text-xs text-[var(--muted)]">
                        <span>Withdrawable now</span>
                        <button type="button" onclick="openWithdrawModal()" class="btn btn-alt" style="padding:7px 12px;font-size:.68rem">Request</button>
                    </div>
                </div>

                <div class="sh-card p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="sh-sub mb-3">Lifetime Earnings</div>
                            <div class="stat-num"><span id="stat-earnings">0.00</span><span class="mono" style="font-size:.82rem;opacity:.55;margin-left:4px">G</span></div>
                        </div>
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(124,109,255,.1);color:var(--accent2)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                    <div id="stat-fee" class="mt-4 text-xs text-[var(--muted)] border-t pt-3" style="border-color:var(--line)">After Platform Fee</div>
                </div>

                <div class="sh-card p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="sh-sub mb-3">Units Sold</div>
                            <div class="stat-num" id="stat-sales">0</div>
                        </div>
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(255,77,106,.1);color:var(--danger)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-2 text-xs text-[var(--muted)]">
                        <span class="badge active">Live Sales</span>
                        <span>Products moving through completed orders</span>
                    </div>
                </div>

                <div class="sh-card p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="sh-sub mb-3">Store Rating</div>
                            <div class="stat-num" id="stat-rating" style="color:var(--warn)">0.0</div>
                        </div>
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(245,166,35,.1);color:var(--warn)">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-[var(--muted)]">Trust, delivery quality, and seller reputation signal.</div>
                </div>
            </div>

            <div class="grid grid-cols-1 2xl:grid-cols-[1.7fr_1fr] gap-6">
                <div class="space-y-6">
                    <div class="sh-card overflow-hidden">
                        <div class="p-5 border-b" style="border-color:var(--line)">
                            <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
                                <div class="tabbar">
                                    <button type="button" onclick="toggleTab('products')" id="tab-products" class="tabbtn active">Inventory <span id="stat-products" style="opacity:.55">0</span></button>
                                    <button type="button" onclick="toggleTab('withdrawals')" id="tab-withdrawals" class="tabbtn">Withdrawals</button>
                                </div>
                                <div class="toolbar w-full xl:w-auto">
                                    <div class="relative w-full xl:w-[290px]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--muted)">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z" />
                                        </svg>
                                        <input id="product-search" type="text" class="input" placeholder="Search products..." style="padding-left:40px">
                                    </div>
                                    <select id="product-type-filter" class="select w-full xl:w-[170px]">
                                        <option value="">All Types</option>
                                        <option value="digital">Digital</option>
                                        <option value="gift_card">Gift Card</option>
                                        <option value="mystery_box">Mystery Box</option>
                                        <option value="nft">NFT</option>
                                        <option value="physical">Physical</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="view-products">
                            <div class="table-wrap hidden md:block">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Pricing</th>
                                            <th>Inventory</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th style="text-align:right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="product-list">
                                        <tr>
                                            <td colspan="6">
                                                <div class="empty"><b>Loading products...</b>Seller inventory is being prepared.</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div id="product-cards-mobile" class="md:hidden p-4 space-y-3">
                                <div class="empty"><b>Loading products...</b>Seller inventory is being prepared.</div>
                            </div>
                        </div>

                        <div id="view-withdrawals" class="hidden">
                            <div class="table-wrap hidden md:block">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#ID</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th style="text-align:right">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody id="withdrawal-list">
                                        <tr>
                                            <td colspan="4">
                                                <div class="empty"><b>No withdrawals yet</b>Your payout requests will appear here.</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div id="withdrawal-cards-mobile" class="md:hidden p-4 space-y-3">
                                <div class="empty"><b>No withdrawals yet</b>Your payout requests will appear here.</div>
                            </div>
                        </div>
                    </div>

                    <div class="sh-card p-5">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-5">
                            <div>
                                <div class="sh-sub mb-2">Seller Notes</div>
                                <div class="text-lg font-extrabold">Tools & workflow help</div>
                            </div>
                            <div class="badge active">Premium Seller Flow</div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div class="sh-soft rounded-2xl p-4 border" style="border-color:var(--line)">
                                <div class="font-extrabold mb-2">Gift Cards</div>
                                <div class="text-[var(--muted)]">Create options first, then import codes for each option or default stock pool.</div>
                            </div>
                            <div class="sh-soft rounded-2xl p-4 border" style="border-color:var(--line)">
                                <div class="font-extrabold mb-2">Mystery Boxes</div>
                                <div class="text-[var(--muted)]">Keep total probability at or below 100% and balance reward value carefully.</div>
                            </div>
                            <div class="sh-soft rounded-2xl p-4 border" style="border-color:var(--line)">
                                <div class="font-extrabold mb-2">Attributes</div>
                                <div class="text-[var(--muted)]">Use JSON attributes like color, size, region, platform, or edition for richer listings.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="sh-card overflow-hidden">
                        <div class="p-5 border-b flex items-center justify-between" style="border-color:var(--line)">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(0,229,195,.1);color:var(--accent)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="sh-sub">Activity</div>
                                    <div class="text-base font-extrabold">Recent Sales</div>
                                </div>
                            </div>
                            <div class="badge active">Live</div>
                        </div>
                        <div id="sales-list" class="max-h-[480px] overflow-y-auto">
                            <div class="empty"><b>No sales yet</b>Completed sales will appear here.</div>
                        </div>
                    </div>

                    <div class="sh-card p-5">
                        <div class="sh-sub mb-2">Quick Actions</div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <button type="button" onclick="openProductModal()" class="btn btn-main w-full">Add Product</button>
                            <button type="button" onclick="toggleTab('withdrawals')" class="btn btn-alt w-full">View Payouts</button>
                            <button type="button" onclick="openWithdrawModal()" class="btn btn-alt w-full">Request Withdraw</button>
                            <button type="button" onclick="loadHub()" class="btn btn-alt w-full">Reload Hub</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</main>
<div id="product-modal" class="modal-backdrop hidden">
    <div class="sh-modal" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <div class="sh-sub mb-2">Seller Product Studio</div>
                <div id="modal-title" class="text-2xl font-extrabold">Add Product</div>
            </div>
            <button type="button" onclick="closeProductModal()" class="icon-btn">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="product-form" onsubmit="event.preventDefault();saveProduct();" class="space-y-5">
            <input type="hidden" id="prod-id" value="0">

            <div class="grid-2">
                <div>
                    <label class="label">Product Title</label>
                    <input id="prod-title" type="text" class="input" placeholder="Premium Steam Key">
                </div>
                <div>
                    <label class="label">Price USD</label>
                    <input id="prod-price" type="number" step="0.01" class="input mono" placeholder="0.00">
                </div>
            </div>

            <div class="grid-3">
                <div>
                    <label class="label">Stock Qty</label>
                    <input id="prod-stock" type="number" class="input mono" placeholder="0">
                </div>
                <div>
                    <label class="label">Product Type</label>
                    <select id="prod-type" class="select" onchange="hubTypeUI()">
                        <option value="digital">Digital</option>
                        <option value="gift_card">Gift Card</option>
                        <option value="mystery_box">Mystery Box</option>
                        <option value="nft">NFT</option>
                        <option value="physical">Physical</option>
                    </select>
                </div>
                <div>
                    <label class="label">Category</label>
                    <select id="prod-cat" class="select">
                        <?php foreach ($cats as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div id="type-help-box" class="sh-soft rounded-2xl p-4 border text-sm" style="border-color:var(--line)">
                Gift card products support options and inventory codes.
            </div>

            <div>
                <label class="label">Description</label>
                <textarea id="prod-desc" class="textarea" placeholder="Describe your product..."></textarea>
            </div>

            <div>
                <label class="label">Attributes JSON</label>
                <textarea id="prod-attributes" class="textarea mono" placeholder='{"platform":"Steam","region":"Global","edition":"Ultimate"}'></textarea>
            </div>

            <div class="grid-2">
                <div>
                    <label class="label">Product Image</label>
                    <input id="prod-image-file" type="file" accept="image/*" class="input" onchange="hubPreviewImage(this)">
                </div>
                <div>
                    <label class="label">Preview</label>
                    <img id="prod-preview" class="thumb-lg" src="assets/placeholder.png">
                </div>
            </div>

            <div class="grid-2">
                <button type="button" onclick="duplicateCurrentProduct()" class="btn btn-alt w-full">Duplicate Product</button>
                <button type="submit" class="btn btn-main w-full">Save Product</button>
            </div>
        </form>
    </div>
</div>

<div id="withdraw-modal" class="modal-backdrop hidden">
    <div class="sh-modal" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-6">
            <div>
                <div class="sh-sub mb-2">Seller Payout</div>
                <div class="text-2xl font-extrabold">Withdraw Funds</div>
            </div>
            <button type="button" onclick="closeWithdrawModal()" class="icon-btn">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="space-y-5">
            <div class="sh-soft rounded-2xl p-4 border text-sm" style="border-color:var(--line)">
                Available balance: <b><span id="withdraw-available">0.000</span> G</b>
            </div>
            <div>
                <label class="label">Amount</label>
                <input id="withdraw-amount" type="number" step="0.001" class="input mono" placeholder="0.000">
            </div>
            <div class="grid-2">
                <button type="button" onclick="closeWithdrawModal()" class="btn btn-alt w-full">Cancel</button>
                <button type="button" onclick="requestWithdraw()" class="btn btn-main w-full">Submit Request</button>
            </div>
        </div>
    </div>
</div>

<div id="delete-modal" class="modal-backdrop hidden">
    <div class="sh-modal" style="max-width:520px" onclick="event.stopPropagation()">
        <div class="text-center space-y-4">
            <div class="mx-auto w-14 h-14 rounded-2xl flex items-center justify-center" style="background:rgba(255,77,106,.12);color:var(--danger)">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <div class="text-2xl font-extrabold">Delete Product?</div>
            <div class="text-sm text-[var(--muted)]">This will hide the product from listings and deactivate sales.</div>
            <div class="grid-2">
                <button type="button" onclick="closeDeleteModal()" class="btn btn-alt w-full">Cancel</button>
                <button type="button" id="delete-confirm-btn" class="btn btn-red w-full">Delete</button>
            </div>
        </div>
    </div>
</div>

<div id="inv-modal" class="modal-backdrop hidden">
    <div class="sh-modal wide" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-6">
            <div>
                <div class="sh-sub mb-2">Digital Inventory</div>
                <div id="inv-modal-title" class="text-2xl font-extrabold">Manage Codes</div>
            </div>
            <button type="button" onclick="closeInvModal()" class="icon-btn">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="grid-3 mb-5">
            <div class="sh-soft rounded-2xl p-4 border text-center" style="border-color:var(--line)">
                <div class="sh-sub mb-2">Total</div>
                <div id="inv-stat-total" class="stat-num sm">0</div>
            </div>
            <div class="sh-soft rounded-2xl p-4 border text-center" style="border-color:var(--line)">
                <div class="sh-sub mb-2">Available</div>
                <div id="inv-stat-available" class="stat-num sm" style="color:var(--accent)">0</div>
            </div>
            <div class="sh-soft rounded-2xl p-4 border text-center" style="border-color:var(--line)">
                <div class="sh-sub mb-2">Sold</div>
                <div id="inv-stat-sold" class="stat-num sm" style="color:var(--accent2)">0</div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[1.1fr_.9fr] gap-5">
            <div class="space-y-5">
                <div class="sh-card p-5">
                    <label class="label">Gift Card Option</label>
                    <div class="grid grid-cols-[1fr_auto] gap-3">
                        <select id="inv-option" class="select"></select>
                        <button type="button" onclick="invAddOptionModal()" class="btn btn-main">Add</button>
                    </div>

                    <div class="mt-5">
                        <label class="label">Import Codes</label>
                        <textarea id="inv-codes-input" class="textarea mono" placeholder="CODE-1234|PIN&#10;CODE-5678"></textarea>
                    </div>

                    <div class="grid-2 mt-4">
                        <button type="button" onclick="loadInvCodes()" class="btn btn-alt w-full">Reload</button>
                        <button type="button" onclick="invAddCodes()" class="btn btn-main w-full">Import</button>
                    </div>
                </div>

                <div class="sh-card overflow-hidden">
                    <div class="p-4 border-b flex items-center justify-between" style="border-color:var(--line)">
                        <div class="sh-sub">Codes</div>
                        <input id="inv-filter" class="input w-[180px]" placeholder="Search tail...">
                    </div>
                    <div id="inv-codes-list" class="max-h-[420px] overflow-y-auto">
                        <div class="empty"><b>No codes yet</b>Add your first inventory batch.</div>
                    </div>
                </div>
            </div>

            <div class="sh-card overflow-hidden">
                <div class="p-4 border-b flex items-center justify-between" style="border-color:var(--line)">
                    <div class="sh-sub">Options Grid</div>
                    <div class="text-xs text-[var(--muted)]">USD / GASHY</div>
                </div>
                <div id="inv-options-grid" class="p-4 space-y-3 max-h-[560px] overflow-y-auto">
                    <div class="empty"><b>No options yet</b>Create variants like $10 / $25 / $50.</div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<div id="mystery-modal" class="modal-backdrop hidden">
    <div class="sh-modal wide" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-6">
            <div>
                <div class="sh-sub mb-2">Mystery Box Builder</div>
                <div id="mystery-modal-title" class="text-2xl font-extrabold">Loot Table</div>
            </div>
            <button type="button" onclick="closeMysteryModal()" class="icon-btn">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="sh-soft rounded-2xl p-4 border text-sm mb-5" style="border-color:var(--line)">
            Keep total probability at or below 100%. Avoid reward values larger than box price unless intentionally promotional.
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[1.25fr_.75fr] gap-5">
            <div class="sh-card overflow-hidden">
                <div class="p-4 border-b flex items-center justify-between" style="border-color:var(--line)">
                    <div class="sh-sub">Loot Entries</div>
                    <div id="mystery-total-prob" class="mono text-sm text-[var(--muted)]">0%</div>
                </div>
                <div id="mystery-loot-list" class="max-h-[520px] overflow-y-auto">
                    <div class="empty"><b>No loot entries</b>Add rewards to activate the box.</div>
                </div>
            </div>

            <div class="sh-card p-5 space-y-4">
                <div class="font-extrabold text-lg">Add Loot Entry</div>

                <div>
                    <label class="label">Reward Product</label>
                    <select id="mystery-reward-product" class="select">
                        <option value="">Tokens (GASHY)</option>
                    </select>
                </div>

                <div>
                    <label class="label">Amount</label>
                    <input id="mystery-reward-amount" type="number" step="0.001" value="0" class="input mono">
                </div>

                <div>
                    <label class="label">Rarity</label>
                    <select id="mystery-rarity" class="select">
                        <option value="common">Common</option>
                        <option value="rare">Rare</option>
                        <option value="epic">Epic</option>
                        <option value="legendary">Legendary</option>
                    </select>
                </div>

                <div>
                    <label class="label">Probability %</label>
                    <input id="mystery-probability" type="number" step="0.01" class="input mono" placeholder="25.00">
                </div>

                <button type="button" onclick="mysteryAddLoot()" class="btn btn-main w-full">Add Entry</button>
            </div>
        </div>
    </div>
</div>
<?php require_once 'footer.php'; ?>