Here are the answers to your questions, followed by the plan to fix the fake data and missing seller logic.

1. How do Rewards and Payouts work? Is GASHY the main currency?

Yes, $GASHY is the absolute center of this ecosystem.

Buyers connect their Phantom wallet and pay with $GASHY.

Sellers list products priced in $GASHY. When they make a sale, their "Available Balance" increases. To get the money to their actual Phantom wallet, they click "Withdraw". The Admin sees this request in the Dashboard and manually transfers the $GASHY to the seller's wallet, then clicks "Approve".

Rewards (Lottery, Mystery Boxes, Quests) give the user free $GASHY by increasing their internal database balance, which they can spend on the platform.

2. Seller vs Order Management (The Missing Logic)

You are 100% correct. This is a misunderstanding on my part.
Right now, Sellers can see that they sold something, but they cannot click a button to say "I shipped this" or "Delivered".
We need to add an "Update Status" button next to their Recent Sales in the Seller Hub.

3. Digital Codes (Windows 11 License)

You are correct again.
If a product is a gift_card or digital, the seller needs a place to upload the secret keys (like WIN11-ABC-123).
Currently, only the Admin has inventory.php. We need to add a "Manage Keys" button in the Seller Hub so the seller can add codes to their own digital products. If a code exists, the order automatically becomes completed and the code is delivered.

4. What are Quests?

Quests are Gamification. They make users addicted to your platform.

Example: You create a Weekly Quest: "Burn 500 GASHY".

A user spends 500 GASHY playing the Lottery or Mystery Boxes.

The system tracks this. Once they hit 500, a "Claim" button appears.

They click it, and they win 50 GASHY as a reward for being active.

5. Fake Data in Sidebar (Price, Vol, %)

I used static $2.4M and +12.5% because we were focused on layout. We need to update oracle.php to fetch this real data from Birdeye, and core.js to push it to the sidebar.

🛠️ ACTION PLAN: SEND ME THESE FILES

As per your rules, I will not guess your files. Please paste the current code for these files so I can upgrade them to fix Q2, Q3, and Q5:

For Fake Sidebar Data (Q5):

sidebar.php

public/js/core.js

api/general/oracle.php

For Seller Hub Order Management & Digital Codes (Q2 & Q3):
4. pages/seller-hub.php
5. public/js/pages/seller-hub.js
6. api/seller/dashboard.php
7. api/seller/save.php

Paste them here, and I will give you the ultra-optimized, real-data versions without comments or empty lines. After this, we move to NFT Minting!