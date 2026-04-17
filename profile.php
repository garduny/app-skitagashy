<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
?>
<style>
    @keyframes fade-in {
        0% {
            opacity: 0;
            transform: translateY(24px)
        }

        100% {
            opacity: 1;
            transform: translateY(0)
        }
    }

    @keyframes float-y {

        0%,
        100% {
            transform: translateY(0)
        }

        50% {
            transform: translateY(-8px)
        }
    }

    @keyframes pulse-glow {

        0%,
        100% {
            box-shadow: 0 0 0 rgba(59, 130, 246, 0), 0 0 30px rgba(59, 130, 246, .18)
        }

        50% {
            box-shadow: 0 0 0 rgba(139, 92, 246, 0), 0 0 45px rgba(139, 92, 246, .28)
        }
    }

    @keyframes shine {
        0% {
            transform: translateX(-150%)
        }

        100% {
            transform: translateX(150%)
        }
    }

    @keyframes border-flow {
        0% {
            background-position: 0 50%
        }

        100% {
            background-position: 200% 50%
        }
    }

    .fade-in {
        animation: fade-in .55s cubic-bezier(.22, 1, .36, 1)
    }

    .profile-shell {
        position: relative;
        isolation: isolate
    }

    .profile-shell::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background:
            radial-gradient(circle at top right, rgba(59, 130, 246, .12), transparent 28%),
            radial-gradient(circle at bottom left, rgba(168, 85, 247, .12), transparent 30%),
            linear-gradient(180deg, rgba(255, 255, 255, .03), rgba(255, 255, 255, 0));
        z-index: -1
    }

    .hero-card {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, .08);
        background: linear-gradient(135deg, rgba(13, 18, 30, .92), rgba(20, 25, 41, .82));
        backdrop-filter: blur(16px);
        box-shadow: 0 20px 60px rgba(2, 8, 23, .45)
    }

    html:not(.dark) .hero-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, .96), rgba(248, 250, 252, .92));
        border-color: rgba(15, 23, 42, .08);
        box-shadow: 0 20px 60px rgba(15, 23, 42, .08)
    }

    .hero-card::before {
        content: "";
        position: absolute;
        inset: -1px;
        padding: 1px;
        border-radius: inherit;
        background: linear-gradient(120deg, rgba(59, 130, 246, .35), rgba(168, 85, 247, .35), rgba(236, 72, 153, .35), rgba(59, 130, 246, .35));
        background-size: 200% 200%;
        animation: border-flow 8s linear infinite;
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        pointer-events: none
    }

    .hero-orb {
        position: absolute;
        border-radius: 9999px;
        filter: blur(60px);
        opacity: .55;
        pointer-events: none
    }

    .hero-orb-1 {
        width: 240px;
        height: 240px;
        top: -50px;
        right: -30px;
        background: rgba(59, 130, 246, .18)
    }

    .hero-orb-2 {
        width: 220px;
        height: 220px;
        bottom: -70px;
        left: -20px;
        background: rgba(168, 85, 247, .16)
    }

    .hero-orb-3 {
        width: 160px;
        height: 160px;
        top: 30%;
        left: 35%;
        background: rgba(236, 72, 153, .08)
    }

    .avatar-frame {
        position: relative;
        padding: 5px;
        border-radius: 9999px;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6, #ec4899);
        animation: pulse-glow 3.8s ease-in-out infinite;
        box-shadow: 0 10px 30px rgba(59, 130, 246, .25)
    }

    .avatar-core {
        width: 100%;
        height: 100%;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(255, 255, 255, .98), rgba(241, 245, 249, .92));
        color: #111827;
        font-size: 2.4rem
    }

    .dark .avatar-core {
        background: linear-gradient(135deg, rgba(10, 14, 26, .95), rgba(16, 23, 37, .94));
        color: #fff
    }

    .tier-badge {
        position: absolute;
        right: -2px;
        bottom: -2px;
        width: 44px;
        height: 44px;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(255, 255, 255, .98), rgba(248, 250, 252, .95));
        border: 1px solid rgba(255, 255, 255, .55);
        box-shadow: 0 12px 25px rgba(2, 8, 23, .18)
    }

    .dark .tier-badge {
        background: linear-gradient(135deg, rgba(17, 24, 39, .98), rgba(30, 41, 59, .96));
        border-color: rgba(255, 255, 255, .08)
    }

    .wallet-pill {
        display: inline-flex;
        align-items: center;
        gap: .625rem;
        padding: .75rem .9rem;
        border-radius: 1rem;
        background: linear-gradient(135deg, rgba(59, 130, 246, .10), rgba(139, 92, 246, .08));
        border: 1px solid rgba(59, 130, 246, .22);
        backdrop-filter: blur(10px)
    }

    html:not(.dark) .wallet-pill {
        background: linear-gradient(135deg, rgba(59, 130, 246, .07), rgba(139, 92, 246, .06));
        border-color: rgba(59, 130, 246, .16)
    }

    .wallet-pill p {
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap
    }

    .metric-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem
    }

    @media (max-width:767px) {
        .metric-grid {
            grid-template-columns: 1fr
        }
    }

    .metric-card {
        position: relative;
        overflow: hidden;
        border-radius: 1.25rem;
        padding: 1.05rem 1rem;
        background: linear-gradient(135deg, rgba(15, 23, 42, .72), rgba(24, 30, 46, .72));
        border: 1px solid rgba(255, 255, 255, .06);
        backdrop-filter: blur(12px);
        transition: .25s ease
    }

    html:not(.dark) .metric-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, .96), rgba(248, 250, 252, .94));
        border-color: rgba(15, 23, 42, .08)
    }

    .metric-card::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .12), transparent);
        transform: translateX(-130%)
    }

    .metric-card:hover {
        transform: translateY(-4px);
        border-color: rgba(59, 130, 246, .28);
        box-shadow: 0 16px 35px rgba(59, 130, 246, .14)
    }

    .metric-card:hover::after {
        animation: shine .9s ease
    }

    .panel-card {
        position: relative;
        overflow: hidden;
        border-radius: 1.5rem;
        background: linear-gradient(180deg, rgba(13, 18, 30, .88), rgba(17, 24, 39, .78));
        border: 1px solid rgba(255, 255, 255, .06);
        backdrop-filter: blur(16px);
        box-shadow: 0 18px 50px rgba(2, 8, 23, .28)
    }

    html:not(.dark) .panel-card {
        background: linear-gradient(180deg, rgba(255, 255, 255, .98), rgba(248, 250, 252, .96));
        border-color: rgba(15, 23, 42, .08);
        box-shadow: 0 18px 50px rgba(15, 23, 42, .07)
    }

    .panel-card.soft-purple {
        background: linear-gradient(180deg, rgba(45, 20, 68, .72), rgba(28, 21, 52, .80));
        border-color: rgba(168, 85, 247, .20)
    }

    html:not(.dark) .panel-card.soft-purple {
        background: linear-gradient(180deg, rgba(253, 244, 255, .98), rgba(250, 245, 255, .98));
        border-color: rgba(168, 85, 247, .16)
    }

    .panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.25rem 1.25rem 0 1.25rem
    }

    .panel-body {
        padding: 1.25rem
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: .75rem;
        font-size: 1.12rem;
        font-weight: 900;
        letter-spacing: -.02em
    }

    .section-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 1rem;
        background: linear-gradient(135deg, rgba(59, 130, 246, .14), rgba(139, 92, 246, .14));
        border: 1px solid rgba(59, 130, 246, .18);
        color: #3b82f6
    }

    .dark .section-icon {
        color: #60a5fa
    }

    .input-label {
        display: block;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: #6b7280;
        margin-bottom: .6rem
    }

    .dark .input-label {
        color: #94a3b8
    }

    .input-wrap {
        position: relative
    }

    .input-icon {
        position: absolute;
        left: .95rem;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: #64748b
    }

    .elite-input {
        width: 100%;
        height: 3.2rem;
        padding: .85rem 1rem .85rem 2.8rem;
        border-radius: 1rem;
        background: rgba(8, 12, 22, .58);
        border: 1px solid rgba(255, 255, 255, .06);
        color: #fff;
        font-size: .95rem;
        font-weight: 600;
        transition: .25s ease
    }

    html:not(.dark) .elite-input {
        background: rgba(248, 250, 252, .96);
        border-color: rgba(15, 23, 42, .10);
        color: #111827
    }

    .elite-input::placeholder {
        color: #94a3b8
    }

    .elite-input:focus {
        outline: none;
        border-color: rgba(59, 130, 246, .42);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, .12), 0 10px 30px rgba(59, 130, 246, .10);
        background: rgba(10, 14, 26, .88)
    }

    html:not(.dark) .elite-input:focus {
        background: #fff
    }

    .action-btn {
        position: relative;
        overflow: hidden;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .65rem;
        border-radius: 1rem;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        color: #fff;
        font-weight: 900;
        letter-spacing: .01em;
        box-shadow: 0 12px 30px rgba(37, 99, 235, .28);
        transition: .25s ease
    }

    .action-btn::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .22), transparent);
        transform: translateX(-140%)
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 35px rgba(37, 99, 235, .34)
    }

    .action-btn:hover::before {
        animation: shine .85s ease
    }

    .action-btn.secondary {
        background: linear-gradient(135deg, #7c3aed, #ec4899);
        box-shadow: 0 12px 30px rgba(168, 85, 247, .24)
    }

    .action-btn.success {
        background: linear-gradient(135deg, #059669, #16a34a);
        box-shadow: 0 12px 30px rgba(22, 163, 74, .24)
    }

    .disconnect-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .55rem;
        border-radius: 1rem;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
        font-weight: 800;
        box-shadow: 0 12px 28px rgba(239, 68, 68, .25);
        transition: .25s ease
    }

    .disconnect-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 35px rgba(239, 68, 68, .32)
    }

    .orders-wrap {
        padding: 1rem 1.25rem 1.25rem 1.25rem
    }

    .orders-list {
        display: flex;
        flex-direction: column;
        gap: .85rem;
        max-height: 760px;
        overflow: auto;
        padding-right: .15rem
    }

    .orders-list::-webkit-scrollbar,
    .history-list::-webkit-scrollbar {
        width: 8px
    }

    .orders-list::-webkit-scrollbar-thumb,
    .history-list::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, .28);
        border-radius: 9999px
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .8rem;
        min-height: 420px;
        text-align: center;
        border-radius: 1.2rem;
        border: 1px dashed rgba(59, 130, 246, .18);
        background: linear-gradient(180deg, rgba(59, 130, 246, .05), rgba(168, 85, 247, .04))
    }

    html:not(.dark) .empty-state {
        background: linear-gradient(180deg, rgba(59, 130, 246, .04), rgba(168, 85, 247, .03));
        border-color: rgba(59, 130, 246, .14)
    }

    .order-item {
        position: relative;
        overflow: hidden;
        border-radius: 1.15rem;
        padding: 1rem;
        background: linear-gradient(135deg, rgba(15, 23, 42, .68), rgba(22, 28, 44, .66));
        border: 1px solid rgba(255, 255, 255, .05);
        backdrop-filter: blur(10px);
        transition: .25s ease
    }

    html:not(.dark) .order-item {
        background: linear-gradient(135deg, rgba(255, 255, 255, .96), rgba(248, 250, 252, .96));
        border-color: rgba(15, 23, 42, .08)
    }

    .order-item:hover {
        transform: translateX(6px);
        border-color: rgba(59, 130, 246, .25);
        box-shadow: 0 12px 28px rgba(59, 130, 246, .12)
    }

    .stat-line {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: .9rem 1rem;
        border-radius: 1rem;
        background: rgba(255, 255, 255, .03);
        border: 1px solid rgba(255, 255, 255, .05)
    }

    html:not(.dark) .stat-line {
        background: rgba(248, 250, 252, .86);
        border-color: rgba(15, 23, 42, .07)
    }

    .referral-box {
        display: flex;
        gap: .65rem;
        align-items: center
    }

    .referral-input {
        flex: 1;
        height: 3rem;
        padding: 0 .95rem;
        border-radius: 1rem;
        background: rgba(255, 255, 255, .55);
        border: 1px solid rgba(168, 85, 247, .20);
        color: #7e22ce;
        font-size: .8rem;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-weight: 800
    }

    .dark .referral-input {
        background: rgba(0, 0, 0, .26);
        border-color: rgba(168, 85, 247, .28);
        color: #d8b4fe
    }

    .balance-card {
        padding: 1.25rem;
        border-radius: 1.25rem;
        background: linear-gradient(135deg, rgba(5, 150, 105, .12), rgba(37, 99, 235, .10));
        border: 1px solid rgba(16, 185, 129, .18)
    }

    html:not(.dark) .balance-card {
        background: linear-gradient(135deg, rgba(236, 253, 245, .96), rgba(239, 246, 255, .96));
        border-color: rgba(16, 185, 129, .14)
    }

    .balance-amount {
        font-size: 2rem;
        line-height: 1;
        font-weight: 950;
        letter-spacing: -.04em;
        background: linear-gradient(135deg, #10b981, #22c55e);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent
    }

    .subtle-text {
        color: #64748b
    }

    .dark .subtle-text {
        color: #94a3b8
    }

    .mini-tag {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .45rem .7rem;
        border-radius: 9999px;
        font-size: .72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
        background: rgba(59, 130, 246, .10);
        border: 1px solid rgba(59, 130, 246, .16);
        color: #2563eb
    }

    .dark .mini-tag {
        color: #93c5fd
    }

    .connect-card {
        position: relative;
        overflow: hidden;
        border-radius: 2rem;
        padding: 2rem;
        border: 1px solid rgba(255, 255, 255, .08);
        background: linear-gradient(135deg, rgba(13, 18, 30, .88), rgba(24, 30, 46, .82));
        box-shadow: 0 20px 60px rgba(2, 8, 23, .38)
    }

    html:not(.dark) .connect-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, .98), rgba(248, 250, 252, .96));
        border-color: rgba(15, 23, 42, .08);
        box-shadow: 0 20px 60px rgba(15, 23, 42, .07)
    }

    .connect-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at top right, rgba(59, 130, 246, .14), transparent 32%),
            radial-gradient(circle at bottom left, rgba(168, 85, 247, .14), transparent 34%)
    }

    .connect-icon {
        position: relative;
        width: 6.4rem;
        height: 6.4rem;
        border-radius: 1.8rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(255, 255, 255, .12), rgba(255, 255, 255, .04));
        border: 1px solid rgba(255, 255, 255, .14);
        backdrop-filter: blur(10px);
        animation: float-y 4s ease-in-out infinite
    }

    html:not(.dark) .connect-icon {
        background: linear-gradient(135deg, rgba(248, 250, 252, .96), rgba(255, 255, 255, .98));
        border-color: rgba(15, 23, 42, .08)
    }

    .history-list {
        display: flex;
        flex-direction: column;
        gap: .7rem;
        max-height: 280px;
        overflow: auto;
        padding-right: .15rem
    }

    .quick-links {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .85rem
    }

    @media (max-width:639px) {
        .quick-links {
            grid-template-columns: 1fr
        }
    }

    .quick-link {
        display: flex;
        align-items: center;
        gap: .85rem;
        padding: 1rem;
        border-radius: 1rem;
        background: rgba(255, 255, 255, .03);
        border: 1px solid rgba(255, 255, 255, .05);
        transition: .25s ease
    }

    html:not(.dark) .quick-link {
        background: rgba(248, 250, 252, .88);
        border-color: rgba(15, 23, 42, .07)
    }

    .quick-link:hover {
        transform: translateY(-3px);
        border-color: rgba(59, 130, 246, .2);
        box-shadow: 0 12px 25px rgba(59, 130, 246, .10)
    }

    .quick-link-icon {
        width: 2.7rem;
        height: 2.7rem;
        border-radius: .95rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(59, 130, 246, .14), rgba(139, 92, 246, .16));
        border: 1px solid rgba(59, 130, 246, .18);
        color: #3b82f6
    }

    .dark .quick-link-icon {
        color: #93c5fd
    }

    .page-title {
        font-size: 2rem;
        line-height: 1.05;
        font-weight: 950;
        letter-spacing: -.045em
    }

    @media (max-width:640px) {
        .page-title {
            font-size: 1.65rem
        }
    }

    .hero-topline {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: .65rem
    }
</style>
<main class="min-h-screen pt-20 lg:pl-72 bg-slate-50 dark:bg-gradient-to-br dark:from-dark-900 dark:via-dark-800 dark:to-dark-900 text-gray-900 dark:text-white transition-colors duration-300">
    <div class="profile-shell min-h-screen">
        <div class="absolute inset-0 overflow-hidden pointer-events-none hidden dark:block">
            <div class="hero-orb hero-orb-1"></div>
            <div class="hero-orb hero-orb-2"></div>
            <div class="hero-orb hero-orb-3"></div>
        </div>
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
            <div id="guest-view" class="hidden fade-in min-h-[80vh] items-center justify-center">
                <div class="connect-card w-full max-w-3xl mx-auto">
                    <div class="relative z-10 flex flex-col items-center text-center gap-6">
                        <div class="connect-icon">
                            <svg class="w-12 h-12 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div class="space-y-3">
                            <div class="hero-topline justify-center">
                                <span class="mini-tag">Secure Access</span>
                                <span class="mini-tag">Solana Ready</span>
                            </div>
                            <h1 class="page-title bg-gradient-to-r from-slate-900 via-blue-700 to-purple-700 dark:from-white dark:via-blue-200 dark:to-purple-200 bg-clip-text text-transparent">Connect Your Wallet</h1>
                            <p class="max-w-2xl mx-auto text-base md:text-lg text-gray-600 dark:text-gray-400 leading-relaxed">Access your premium dashboard, monitor orders, manage profile settings, and unlock account features directly from your connected Phantom wallet.</p>
                        </div>
                        <button onclick="App.connectWallet()" class="action-btn px-8 py-4 text-base">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Connect Phantom Wallet
                        </button>
                    </div>
                </div>
            </div>
            <div id="auth-view" class="block fade-in space-y-6">
                <div class="hero-card rounded-[1.75rem] p-5 md:p-7">
                    <div class="hero-orb hero-orb-1"></div>
                    <div class="hero-orb hero-orb-2"></div>
                    <div class="relative z-10 flex flex-col gap-6">
                        <div class="flex flex-col xl:flex-row xl:items-center gap-6">
                            <div class="flex items-start sm:items-center gap-4 md:gap-5 flex-1">
                                <div class="relative shrink-0">
                                    <div class="avatar-frame w-24 h-24 md:w-28 md:h-28">
                                        <div class="avatar-core">👤</div>
                                    </div>
                                    <div class="tier-badge" title="Tier Badge">
                                        <span id="account-tier-icon" class="text-xl">🥇</span>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="hero-topline mb-3">
                                        <span class="mini-tag">Profile Center</span>
                                        <span class="mini-tag">Live Account</span>
                                    </div>
                                    <h1 class="page-title text-gray-900 dark:text-white">Welcome back, <span id="profile-accountname" class="bg-gradient-to-r from-blue-600 via-violet-600 to-pink-600 bg-clip-text text-transparent">gardunydev</span></h1>
                                    <p class="mt-2 subtle-text text-sm md:text-base">Manage your account identity, monitor wallet activity, review orders, and handle withdrawals from one premium dashboard.</p>
                                    <div class="wallet-pill mt-4 max-w-full">
                                        <svg class="w-4 h-4 md:w-5 md:h-5 text-blue-600 dark:text-blue-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" />
                                        </svg>
                                        <p id="profile-wallet" class="text-blue-700 dark:text-blue-300 font-mono text-xs md:text-sm font-semibold">6dygwo6jHPrExGKrohykhYoC1DkAA6CyPp9qDbhMe1JT</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <button onclick="App.logout()" class="disconnect-btn px-5 py-3 whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Disconnect
                                </button>
                            </div>
                        </div>
                        <div class="metric-grid">
                            <div class="metric-card">
                                <div class="text-[11px] uppercase tracking-[0.18em] font-extrabold text-gray-500 dark:text-slate-400 mb-2">Tier</div>
                                <div id="profile-tier" class="text-xl font-black bg-gradient-to-r from-blue-600 via-violet-600 to-pink-600 bg-clip-text text-transparent">gold</div>
                                <div class="mt-2 subtle-text text-sm">Account level and reward status</div>
                            </div>
                            <div class="metric-card">
                                <div class="text-[11px] uppercase tracking-[0.18em] font-extrabold text-gray-500 dark:text-slate-400 mb-2">Spent</div>
                                <div id="profile-spent" class="text-xl font-black text-gray-900 dark:text-white">0 GASHY</div>
                                <div class="mt-2 subtle-text text-sm">Total account spend volume</div>
                            </div>
                            <div class="metric-card">
                                <div class="text-[11px] uppercase tracking-[0.18em] font-extrabold text-gray-500 dark:text-slate-400 mb-2">Orders</div>
                                <div id="profile-orders-count" class="text-xl font-black text-gray-900 dark:text-white">0</div>
                                <div class="mt-2 subtle-text text-sm">Completed and active orders</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
                    <div class="xl:col-span-7">
                        <div class="panel-card h-full">
                            <div class="panel-head">
                                <div class="section-title text-gray-900 dark:text-white">
                                    <div class="section-icon">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div>Recent Orders</div>
                                        <div class="text-sm font-semibold text-gray-500 dark:text-slate-400 mt-1">Your latest marketplace activity</div>
                                    </div>
                                </div>
                                <a href="orders.php" class="action-btn px-4 py-2.5 text-sm">View All</a>
                            </div>
                            <div class="orders-wrap">
                                <div id="recent-orders-list" class="orders-list">
                                    <div class="empty-state">
                                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500/15 to-purple-500/15 flex items-center justify-center border border-blue-500/15">
                                            <svg class="w-8 h-8 text-blue-500 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </div>
                                        <div class="space-y-2">
                                            <h3 class="text-lg font-black text-gray-900 dark:text-white">No orders found</h3>
                                            <p class="subtle-text max-w-md">Once your orders are loaded, they will appear here with the same live structure used by your profile JavaScript.</p>
                                        </div>
                                        <a href="market.php" class="action-btn px-5 py-3 text-sm">Browse Market</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="xl:col-span-5 space-y-6">
                        <div class="panel-card">
                            <div class="panel-head">
                                <div class="section-title text-gray-900 dark:text-white">
                                    <div class="section-icon">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div>Account Settings</div>
                                        <div class="text-sm font-semibold text-gray-500 dark:text-slate-400 mt-1">Profile identity and contact details</div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body space-y-4">
                                <div>
                                    <label class="input-label">Username</label>
                                    <div class="input-wrap">
                                        <div class="input-icon">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <input type="text" id="input-accountname" value="gardunydev" class="elite-input" placeholder="Enter username">
                                    </div>
                                </div>
                                <div>
                                    <label class="input-label">Email Address</label>
                                    <div class="input-wrap">
                                        <div class="input-icon">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H8m8 0l-3.5 3.5M16 12L12.5 8.5M4 6h16v12H4z" />
                                            </svg>
                                        </div>
                                        <input type="email" id="input-email" value="gardunydeveloper@gmail.com" class="elite-input" placeholder="your@email.com">
                                    </div>
                                </div>
                                <button onclick="saveProfile()" class="action-btn w-full py-3.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Save Changes
                                </button>
                            </div>
                        </div>
                        <div class="panel-card soft-purple">
                            <div class="panel-head">
                                <div class="section-title text-gray-900 dark:text-white">
                                    <div class="section-icon" style="background:linear-gradient(135deg,rgba(168,85,247,.18),rgba(236,72,153,.18));border-color:rgba(168,85,247,.24)">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div>Referral Program</div>
                                        <div class="text-sm font-semibold text-gray-500 dark:text-slate-400 mt-1">Share your code and earn rewards</div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body space-y-4">
                                <div class="stat-line">
                                    <div>
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">Referral Bonus</div>
                                        <div class="subtle-text text-sm">Earn <span class="font-black text-purple-600 dark:text-purple-300">5%</span> of trading fees from referred activity.</div>
                                    </div>
                                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 text-white flex items-center justify-center shadow-lg shadow-purple-500/20">%</div>
                                </div>
                                <div class="referral-box">
                                    <input type="text" id="referral-code" readonly value="GASHY-REF-Account1" class="referral-input">
                                    <button onclick="navigator.clipboard.writeText(document.getElementById('referral-code').value);notyf.success('Referral Code Copied!')" class="action-btn secondary px-4 py-3 text-sm">Copy</button>
                                </div>
                                <div class="quick-links">
                                    <a href="orders.php" class="quick-link">
                                        <div class="quick-link-icon">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-black text-gray-900 dark:text-white text-sm">My Orders</div>
                                            <div class="subtle-text text-xs">Track purchases</div>
                                        </div>
                                    </a>
                                    <a href="market.php" class="quick-link">
                                        <div class="quick-link-icon">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v4H3zM5 7h14l1 13H4L5 7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-black text-gray-900 dark:text-white text-sm">Marketplace</div>
                                            <div class="subtle-text text-xs">Browse products</div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="panel-card">
                            <div class="panel-head">
                                <div class="section-title text-gray-900 dark:text-white">
                                    <div class="section-icon" style="background:linear-gradient(135deg,rgba(16,185,129,.16),rgba(37,99,235,.12));border-color:rgba(16,185,129,.20)">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 10v2m8-6a8 8 0 11-16 0 8 8 0 0116 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div>Withdrawals</div>
                                        <div class="text-sm font-semibold text-gray-500 dark:text-slate-400 mt-1">Balance request and payment history</div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body space-y-4">
                                <div class="balance-card">
                                    <div class="text-[11px] uppercase tracking-[0.18em] font-extrabold text-gray-500 dark:text-slate-400 mb-3">Available Balance</div>
                                    <div id="withdrawable-balance" class="balance-amount">5.000 GASHY</div>
                                    <div class="subtle-text text-sm mt-2">Request a withdrawal using the same live handler connected in your profile JavaScript.</div>
                                </div>
                                <div>
                                    <label class="input-label">Withdrawal Amount</label>
                                    <div class="input-wrap">
                                        <div class="input-icon">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 10v2" />
                                            </svg>
                                        </div>
                                        <input type="number" id="withdraw-amount" placeholder="Enter amount" class="elite-input">
                                    </div>
                                </div>
                                <button onclick="requestWithdraw()" class="action-btn success w-full py-3.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a5 5 0 00-10 0v2M5 9h14l-1 10H6L5 9z" />
                                    </svg>
                                    Request Withdrawal
                                </button>
                                <div>
                                    <div class="flex items-center justify-between gap-3 mb-3">
                                        <h3 class="text-base font-black text-gray-900 dark:text-white">Withdrawal History</h3>
                                        <span class="mini-tag">Recent</span>
                                    </div>
                                    <div id="withdrawals-list" class="history-list min-h-[110px]">
                                        <div class="stat-line">
                                            <div>
                                                <div class="font-bold text-gray-900 dark:text-white text-sm">No withdrawals yet</div>
                                                <div class="subtle-text text-xs">Your future withdrawal requests will appear here.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</main>
<?php require_once 'footer.php'; ?>