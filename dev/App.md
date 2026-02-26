Here is the Master Operational Guide for the three core gamification engines of Gashy Bazaar.

This guide explains the lifecycle of each feature from Admin Setup → Customer Action → System Logic.

📦 1. Mystery Boxes (RNG Engine)

A "Gacha" system where users pay a fixed price for a chance to win high-value items or tokens.

A. Dashboard (Admin Setup)

Create the Box:

Go to Products -> Add Product.

Create a product (e.g., "Legendary Chest"), set Price (e.g., 500 GASHY), and select Type: Mystery Box.

Configure Loot Table:

Go to Features -> Mystery Boxes -> Click Edit/Manage on the box.

Add Items: You define what is inside.

Slot 1: 10,000 GASHY Tokens (Rarity: Legendary, Chance: 1%).

Slot 2: Bored Ape NFT (Product ID #50, Chance: 5%).

Slot 3: 100 GASHY (Common, Chance: 94%).

Logic: The system ensures the total probability allows for a fair roll.

B. Customer (User Action)

Purchase: User goes to mystery-boxes.php, sees "Legendary Chest" for 500 GASHY.

Transaction: User clicks "Open Now". Phantom Wallet pops up. User signs the transaction to "Burn" 500 GASHY.

The Reveal: The box shakes (CSS animation), opens, and displays the prize immediately (e.g., "You Won: 100 GASHY").

C. Behind the Scenes (The Logic)

File: api/mystery_box/open.php

Math: The script generates a random number between 0 and 100. It loops through your Loot Table to see which item matches that number range.

Delivery:

If they win Tokens: A reward transaction is added to their history.

If they win a Product: A $0.00 Order is automatically created and marked "Completed", assigning the item to them.

⚡ 2. Live Auctions (Competitive Engine)

A time-based bidding war where the highest bidder wins the item.

A. Dashboard (Admin Setup)

Create Auction:

Go to Features -> Auctions -> Create Auction.

Select an existing Product (e.g., "CyberPunk NFT").

Set Start Price (e.g., 1,000 GASHY).

Set Reserve Price (Hidden minimum, e.g., 5,000 GASHY).

Set End Time (e.g., Friday at 8:00 PM).

Monitor: Admin can see current bids and force-close the auction if needed.

B. Customer (User Action)

Bidding: User goes to auctions.php. They see the item and the current bid.

Placing Bid: User enters 1,100 GASHY.

Rule: Bid must be higher than current + 5% increment.

Transaction: Phantom Wallet pops up. User signs to "Commit" the bid.

Winning: If they are the top bidder when the timer hits 00:00:00, they win.

C. Behind the Scenes (The Logic)

Bid File: api/auctions/bid.php updates the current_bid and highest_bidder_id columns in the database.

Anti-Sniping: If a bid comes in the last 5 minutes, the system adds 5 minutes to the end_time.

The Cron Job (server/cron/auctions.php):

Runs every minute.

Checks if NOW() > end_time.

If Reserve Met: It creates an Order for the winner, marks it paid, and transfers product ownership. Sends email to winner.

If Reserve Not Met: It marks auction as "Ended" (Unsold).

🎰 3. Lottery (Pooling Engine)

A community pool where many enter, and 3 winners take the pot.

A. Dashboard (Admin Setup)

Automated: You don't usually touch this. The system creates rounds automatically.

Manual Override:

Go to Features -> Lotteries.

You can see the current prize_pool (e.g., 100,000 GASHY).

You can click "Draw Winner" manually if you want to end the round early.

B. Customer (User Action)

Entry: User goes to lottery.php.

Purchase: User buys 10 Tickets (Cost: 100 GASHY).

Transaction: Phantom Wallet pops up. User burns 100 GASHY.

Result: The Prize Pool increases by 100 GASHY immediately. The user waits for the countdown.

C. Behind the Scenes (The Logic)

Entry File: api/lottery/enter.php inserts a row into lottery_entries and updates lottery_rounds pool size.

The Cron Job (server/cron/lottery.php):

Runs every hour.

Checks if draw_time has passed.

Selection: It builds an array of all tickets (if User A bought 10 tickets, their ID is in the hat 10 times).

Winners: It picks 3 random IDs.

1st Place: Gets 50% of Pool.

2nd Place: Gets 30% of Pool.

3rd Place: Gets 20% of Pool.

Payout: Updates user balances via transactions table.

Reset: Creates Round #(N+1) automatically for next week.