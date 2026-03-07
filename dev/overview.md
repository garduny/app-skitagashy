❌ Missing or “not matching the plan” (blocking items)

These are explicitly promised in the plan 

GASHY_BAZAAR_PROJECT_OVERVIEW

 but not present / not implemented as described in the zip:

1) Gift Card Delivery System — MISSING

Plan includes:

gift-cards.php, inventory table/system, encrypted code storage, instant email delivery, redemption instructions, expiry tracking 

GASHY_BAZAAR_PROJECT_OVERVIEW


Code reality:

No gift-card pages, no gift-card APIs, no gift-card dashboard module, no gift-card files at all.

➡️ If gift cards are part of Phase 1 launch promise: not ready.

2) “Provably fair / on-chain randomness” for Mystery Boxes — NOT IMPLEMENTED

Plan says “Provably Fair / on-chain randomness verification” 

GASHY_BAZAAR_PROJECT_OVERVIEW


Code does:

Uses random_int() server-side for loot roll (not on-chain / not provably fair).

➡️ This is a trust & brand risk if you marketed “provably fair”.

3) CSRF protection — DISABLED

Plan claims CSRF protection 

GASHY_BAZAAR_PROJECT_OVERVIEW


Code reality:

CSRF check is present but commented out in server/logic.php.

➡️ This is a security blocker before public launch.

4) Multi-source oracle (Birdeye/Jupiter/DexScreener) — PARTIAL

Plan says multi-source oracle with fallbacks 

GASHY_BAZAAR_PROJECT_OVERVIEW


Code reality:

Oracle cron uses DexScreener only.

➡️ Not fatal, but does not match the spec.

5) 2FA + KYC seller verification — NOT CONFIRMED

Plan says optional 2FA + KYC seller verification 

GASHY_BAZAAR_PROJECT_OVERVIEW


Code reality:

I see roles/permissions, but I did not find a real 2FA flow or a KYC module (uploads/review/approval pipeline) consistent with the plan.

➡️ If “KYC verified sellers” is in your positioning, it’s not ready.

⚠️ High-risk technical concerns (even if you launch)

These are not “missing files”, but they matter for “ready to publish”:

Several APIs build SQL with interpolated variables (even if sanitized). That increases risk.

CSRF off + public launch = easy target.

Mystery box “fairness” is off-chain; users may accuse it of manipulation unless you change wording.

Go / No-Go decision
NO-GO for “Plan-complete” launch

Because Gift Cards are missing, CSRF is disabled, and Mystery Box fairness does not match your spec.

GO for “Beta Phase 1” launch (only if you change the promise)

If you publish it as:

Marketplace + Seller Hub + Auctions + Lottery + Mystery Boxes (non-provably-fair) + NFT drops/burn + PWA beta
…and you remove any mention of:

gift cards being live

provably-fair randomness

full multi-oracle feed

Then yes, it can be positioned as Beta Phase 1.





















Developer Brief — Gashy Bazaar “Ready to Publish” Fix Pack (Must-Fix + Spec Alignment)
Objective

Bring GashyBazaar.com to a “public launch safe” state by:

Closing security blockers

Implementing missing Phase-1 plan features (Gift Cards)

Aligning Mystery Box fairness claims with reality (either implement provably-fair or change messaging)

Improving oracle reliability (optional but recommended)

Repo reference: /mnt/data/cache (2).zip
Plan reference: GASHY_BAZAAR_PROJECT_OVERVIEW.md

0) Definition of Done (DoD)

Launch is approved only when:

CSRF protection is enabled and verified across all state-changing endpoints.

Gift Cards module exists end-to-end (DB + admin + purchase + delivery + redemption).

Mystery Boxes: either provably fair implemented or all UI text removed/updated to avoid that claim.

Critical flows pass smoke tests:

Sign up / login

Add to cart / checkout

Order history

Seller onboarding + product listing

Auction bid + settlement

Lottery purchase + draw

Mystery box open + reward deliver

Admin: manage products/sellers/orders/auctions/lottery/mystery boxes

Cron jobs run without fatal errors (auctions, lottery, oracle).

1) Security Blocker — CSRF Is Disabled (MUST FIX)
Problem

CSRF check exists but is commented out in server/logic.php. This is a public launch blocker.

Required Fix

Re-enable CSRF validation globally.

Ensure all POST/PUT/DELETE actions require valid CSRF token.

Ensure token is issued on session creation and refreshed properly.

Acceptance Tests

Any state-changing request without CSRF token → 403

With correct token → 200

Confirm coverage for:

login/register (if applicable), profile update

cart/checkout/order actions

seller actions (create/update product)

bid, lottery purchase, mystery open

admin create/update/delete actions

2) Missing Feature — Gift Cards System (MUST BUILD)
Status

Not present in code. Plan explicitly includes full Gift Card module.

Deliverables
A) Database

Create tables (example structure — dev can adjust):

gift_cards

id, code_hash, value, currency, status(available/sold/redeemed/expired), expires_at, created_at

gift_card_orders

id, user_id, gift_card_id, order_id, delivered_to_email, delivered_at

gift_card_redemptions

id, user_id, gift_card_id, redeemed_at, amount_applied, balance_remaining

Optional: gift_card_inventory_batches for bulk uploads

✅ Codes must be stored as hashed + encrypted (if you keep plaintext)
Minimum: store hash, not plaintext.

B) Admin/Dashboard

Add dashboard module:

dashboard/gift-cards.php (list, add, bulk import, status)

dashboard/gift-card-detail.php (view history, mark expired, revoke)
Functions:

Generate codes in bulk

Import codes (CSV)

Set expiry

Track sold/redeemed

C) Storefront

gift-cards.php listing page

Product-like purchase flow (fixed denominations or custom)

After purchase: “My Gift Cards” page under profile/orders

D) Delivery

Instant email delivery (or on-screen reveal + email)

Include: code, value, expiry, redemption steps, support link

E) Redemption

Checkout option: “Apply Gift Card”

Validate:

valid code

not expired

not redeemed

apply up to cart total

Partial balance support optional but ideal

Acceptance Tests

Admin can add 100 codes, mark as available.

User buys gift card → receives email + sees it in account.

Redeem at checkout reduces payable amount.

Redeem again fails if already redeemed.

Expired codes fail.

3) Mystery Boxes — “Provably Fair” Is NOT Implemented (MUST DECIDE)
Current Reality

Loot selection uses server-side randomness (random_int()), not on-chain/provably fair.

Option A (Preferred): Implement Provably Fair

Implement a provably-fair scheme (no need to be fully on-chain; cryptographic is enough):

Use server_seed (secret) + client_seed (user-provided or generated) + nonce

Publish hash(server_seed) before opening

After opening, reveal server_seed so users can verify roll result

Deterministic RNG from seeds → produces roll value → maps to loot table

UI requirement

Show “Provably Fair” panel on mystery box page:

server seed hash

client seed

nonce

roll output

verification instructions

Option B (Fastest): Remove All “Provably Fair” Claims

If you don’t implement provably fair now:

Update UI/marketing text everywhere:

Replace with: “Randomized rewards (server generated)”

Remove any “on-chain fairness” or “provably fair” statements

Acceptance Tests

Option A: user can reproduce roll outcome with provided seeds.

Option B: no page/text claims provably fair.

4) Oracle Improvements (Recommended)
Current

Oracle cron uses DexScreener only (server/cron/oracle.php).

Fix

Add fallback sources (choose 2):

Birdeye

Jupiter price API

Raydium pool stats

Implement:

If primary fails or returns outlier → fallback

Cache last-good price

Store source + timestamp in DB

Acceptance Tests

If DexScreener 500s → system still updates price using fallback

UI shows last update time & source

5) KYC + 2FA (Only if you promised it publicly)
Task

Audit whether KYC/2FA exist. If not:

Either implement minimal versions

Or remove “KYC verified sellers / 2FA security” claims from UI & docs

Minimal KYC

Seller uploads ID docs → admin approves → seller badge shown
Tables: seller_kyc_submissions, seller_kyc_status

2FA

TOTP (Google Authenticator compatible)

Backup codes

Enforce for admin accounts first

6) Launch Smoke Test Script (Developer must run)

Provide a short runbook:

Fresh DB migrate

Seed admin user

Run crons manually once:

auctions

lottery

oracle

Test flows:

create seller → list product → buy → order shows in admin

create auction → bid → close → winner assigned

create lottery → buy tickets → draw

create mystery box → open → reward assigned

gift card create → buy → deliver → redeem

7) Deliverable Output from Developer

PR/ZIP with changes

DB migration scripts

Config notes (env vars for email/oracle keys)

A short “release notes” file:

What changed

How to test

Any remaining known issues

Priority Order (Do this exactly)

Enable CSRF (blocker)

Gift Cards module (missing)

Mystery fairness (implement or change text)

Oracle fallback

KYC/2FA only if needed for claims