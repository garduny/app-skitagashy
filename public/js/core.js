if (!window.notyf) {
    window.notyf = new Notyf({
        duration: 4000,
        position: { x: 'right', y: 'bottom' },
        types: [
            { type: 'success', background: '#10B981', icon: false },
            { type: 'error', background: '#EF4444', icon: false }
        ]
    });
}
if (!window.App) {
    window.App = {
        state: {
            token: localStorage.getItem('gashy_token'),
            account: null,
            wallet: null,
            theme: localStorage.getItem('theme') || 'dark'
        },
        async init() {
            this.initTheme();
            if (this.state.token) {
                await this.fetchProfile();
            }
            this.renderUI();
        },
        initTheme() {
            const html = document.documentElement;
            if (this.state.theme === 'dark') {
                html.classList.add('dark');
            } else {
                html.classList.remove('dark');
            }
            this.updateThemeIcons();
        },
        toggleTheme() {
            const html = document.documentElement;
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                this.state.theme = 'light';
            } else {
                html.classList.add('dark');
                this.state.theme = 'dark';
            }
            localStorage.setItem('theme', this.state.theme);
            this.updateThemeIcons();
        },
        updateThemeIcons() {
            const sun = document.getElementById('theme-sun');
            const moon = document.getElementById('theme-moon');
            if (!sun || !moon) return;
            if (this.state.theme === 'dark') {
                sun.classList.add('hidden');
                moon.classList.remove('hidden');
            } else {
                sun.classList.remove('hidden');
                moon.classList.add('hidden');
            }
        },
        async connectWallet() {
            const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.accountAgent);
            if (!window.solana || !window.solana.isPhantom) {
                if (isMobile) {
                    const url = encodeURIComponent(window.location.href);
                    window.location.href = `https://phantom.app/ul/browse/${url}?ref=${window.location.host}`;
                    return;
                } else {
                    return window.open('https://phantom.app/', '_blank');
                }
            }
            try {
                const resp = await window.solana.connect();
                this.state.wallet = resp.publicKey.toString();
                const messageText = `Login to Gashy Bazaar: ${Date.now()}`;
                const msg = new TextEncoder().encode(messageText);
                const signed = await window.solana.signMessage(msg, 'utf8');
                let signature = '';
                if (window.solana.isPhantom) {
                    const signatureBytes = signed.signature || signed;
                    signature = Array.from(new Uint8Array(signatureBytes))
                        .map(b => b.toString(16).padStart(2, '0'))
                        .join('');
                } else {
                    signature = 'SIMULATED_SIG';
                }
                await this.login(this.state.wallet, signature, messageText);
            } catch (err) {
                console.error(err);
                window.notyf.error('Connection Failed');
            }
        },
        async login(wallet, signature, message) {
            const res = await this.post('api/auth/login.php', {
                wallet_address: wallet,
                signature: signature,
                message: message
            });
            if (res.status) {
                this.state.token = res.token;
                this.state.account = res.account;
                localStorage.setItem('gashy_token', res.token);
                window.notyf.success('Wallet Connected');
                this.renderUI();
            } else {
                window.notyf.error(res.message);
            }
        },
        async fetchProfile() {
            try {
                const res = await this.post('api/account/profile.php', {});
                if (res.status) {
                    this.state.account = res.data;
                } else {
                    this.logout();
                }
            } catch (e) {
                this.logout();
            }
        },
        renderUI() {
            const connectBtn = document.getElementById('wallet-btn');
            const logoutBtn = document.getElementById('logout-btn');
            const walletText = document.getElementById('wallet-text');
            const balanceText = document.getElementById('account-balance');
            const accountInfo = document.getElementById('account-info');
            const authLinks = document.querySelectorAll('.auth-link');
            if (!connectBtn || !logoutBtn) return;
            if (this.state.account) {
                const w = this.state.account.wallet_address;
                connectBtn.classList.add('hidden');
                connectBtn.classList.remove('hidden');
                connectBtn.onclick = () => location.href = 'profile.php';
                connectBtn.classList.add('bg-blue-600/20', 'border-blue-500');
                walletText.innerText = w.substring(0, 4) + '...' + w.substring(w.length - 4);
                logoutBtn.classList.remove('hidden');
                authLinks.forEach(el => el.classList.remove('hidden'));
                if (accountInfo) {
                    accountInfo.classList.remove('hidden');
                    balanceText.innerText = (this.state.account.tier_progress?.current || '0.00') + ' GASHY';
                }
            } else {
                walletText.innerText = 'Connect Wallet';
                connectBtn.onclick = () => this.connectWallet();
                connectBtn.classList.remove('hidden', 'bg-blue-600/20', 'border-blue-500');
                logoutBtn.classList.add('hidden');
                if (accountInfo) accountInfo.classList.add('hidden');
                authLinks.forEach(el => el.classList.add('hidden'));
            }
        },
        logout() {
            this.state.token = null;
            this.state.account = null;
            this.state.wallet = null;
            localStorage.removeItem('gashy_token');
            this.renderUI();
            window.notyf.success('Disconnected');
        },
        checkAuth() {
            if (!this.state.token) {
                window.notyf.error('Please Connect Wallet First');
                this.connectWallet();
                return false;
            }
            return true;
        },
        async post(url, data) {
            const headers = { 'Content-Type': 'application/json' };
            if (this.state.token) {
                headers['Authorization'] = 'Bearer ' + this.state.token;
            }
            try {
                const r = await fetch(url, {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify(data)
                });
                return await r.json();
            } catch (e) {
                console.error(e);
                return { status: false, message: 'Network Error' };
            }
        }
    };
    document.addEventListener('DOMContentLoaded', () => window.App.init());
}