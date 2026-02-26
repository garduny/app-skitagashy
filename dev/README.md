🦁 GASHY BAZAAR - Operational Ecosystem Manual

Version: 1.0.0 (Production)
Architect: Garduny Dev
Tech Stack: Native PHP 8.4, MySQL 8, TailwindCSS, Solana Web3

📖 The Vision

Gashy Bazaar is not just a marketplace; it is a self-sustaining Web3 Economy. It bridges the gap between meme culture and real-world utility by allowing users to spend $GASHY Tokens on digital goods, compete in high-stakes auctions, and engage in deflationary gamification.

The platform operates on two distinct levels:

The Client Ecosystem (Root): Where users trade, play, and interact.

The Command Center (Dashboard): Where admins govern, monitor, and secure the system.

🛍️ Part 1: The Client Experience (Front-End)

The journey of a user from connection to transaction.

1. 🔐 Secure Onboarding

The platform uses Web3 Authentication instead of traditional passwords for users.

Wallet Connection: Users connect Phantom or Solflare.

Cryptographic Signature: The system demands a unique message signature (Nonce) to prove wallet ownership without charging gas fees.

Session Fingerprinting: Once verified, a secure session is created bound to the user's IP and Device to prevent session hijacking.

New User? They are auto-registered and assigned a unique Referral Code.

2. 🛒 The Marketplace Engine

Users browse a "crowded," vibrant interface featuring Flash Deals, New Arrivals, and Top Sellers.

Real-Time Pricing: The price of items in $GASHY updates automatically based on a live Oracle fetch (Birdeye API), ensuring the USD value remains stable even if the token fluctuates.

Smart Purchasing:

Digital Items: (e.g., Software Keys) are delivered instantly.

Physical Items: (e.g., Merch) trigger a "Processing/Shipping" workflow.

Gift Cards: The system checks the gift_cards vault. If a code is available, it is allocated, decrypted, and delivered immediately. If stock is low, the purchase is blocked to prevent errors.

3. 🎲 Gamification & Deflation (The Burn)

To increase token value, the platform integrates mechanisms to burn supply.

Mystery Boxes: Users pay a fixed amount (e.g., 500 GASHY) to roll a provably fair RNG (Random Number Generator). They might win a common reward (100 GASHY) or a Legendary NFT. The entry fee is burned.

The Lottery: A pooling system. Users buy tickets. 100% of the ticket cost goes into the Prize Pool.

Tiered Winners: The system automatically picks 3 winners (1st: 50%, 2nd: 30%, 3rd: 20%).

Automation: A Cron Job runs hourly to check if the round has ended, distributes rewards, and starts a new round instantly.

4. 🔨 Live Auctions

For premium assets (High-value NFTs), users engage in bidding wars.

Real-Time Logic: Users place bids that must exceed the previous bid + a minimum increment (5%).

Anti-Sniping: If a bid is placed in the last 5 minutes, the timer extends automatically.

Settlement: When time expires, the system automatically transfers ownership to the winner and creates an invoice.

5. 👤 User Progression (Tiers)

The system rewards loyalty.

Tier Calculation: A background worker scans the user's "Total Spent" and "Total Burned".

Status: Users progress from Bronze → Silver → Gold → Platinum → Diamond.

Perks: Higher tiers receive automatic discounts (up to 15%) on all marketplace purchases.

📊 Part 2: The Seller Hub (Multi-Vendor)

Empowering the community to sell.

Application: Users apply to become sellers. The Admin reviews the application in the Dashboard.

Inventory Management: Once approved, Sellers get a private Seller Hub to add products and manage stock.

Financials:

The platform takes a configurable Commission Fee (e.g., 5%) from every sale.

Sellers view Net Earnings.

Sellers can request a Payout, which appears in the Admin Dashboard for approval.

🛡️ Part 3: The Admin Dashboard (Back-End)

Total control over the economy.

1. 🌍 The "God View" (Overview)

Admins see real-time charts of Revenue, Total Orders, Active Sellers, and Burn Statistics.

2. 👥 User & Role Management (RBAC)

Accounts: View all Web3 users, their balances, and tiers. Ability to Ban malicious wallets instantly.

Admins: Manage staff access.

Roles & Permissions: Assign granular rights (e.g., "Support Agent" can view orders but cannot delete products; "Super Admin" can do everything).

3. 📦 Inventory & Secrets

Products: Create, Edit, or Ban products.

Secure Inventory: A dedicated interface to upload bulk Gift Card codes (e.g., Amazon, Steam).

Encryption: All codes are encrypted (AES-256-CBC) in the database. Even database admins cannot read them without the application key.

4. ⚙️ System Configuration

Admins can tweak the economy without coding:

Platform Fee: Change the seller commission % instantly.

Treasury Wallet: Update where fees are sent.

Maintenance Mode: One-click "Kill Switch" to lock the frontend during upgrades.

5. 🔒 Security Audit

Activity Logs: Every action (Login, Purchase, Ban, Edit) is logged with IP, User Agent, and Timestamp.

2FA: Admins are protected by Two-Factor Authentication (Email OTP) to prevent unauthorized access.

Rate Limiting: Integrated protection against Brute Force attacks and API spam.

🤖 Part 4: Automation (The Heartbeat)

The system runs autonomously via Cron Jobs:

Oracle: Updates the $GASHY price every 5 minutes.

Auctioneer: Checks every minute for ended auctions to declare winners.

Lottery Manager: Runs hourly to draw winners and reset pools.

Janitor: Runs daily to clean up expired sessions, delete expired gift card codes, and rotate logs.

📝 Summary

Gashy Bazaar is a production-grade, secure, and scalable platform. It combines the speed of Web2 e-commerce with the transparency and incentives of Web3. It is designed to handle real money, real products, and real community growth securely.
