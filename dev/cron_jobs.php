Here is the complete list of Cron Jobs required for Gashy Bazaar.
Change /path/to/app-skitagashy to your actual server path (e.g., /var/www/html/app-skitagashy or C:\xampp\htdocs\app-skitagashy).
1. Auction Monitor (Every Minute)
Function: Closes ended auctions, transfers ownership, creates orders.
code
Bash
* * * * * php /path/to/server/cron/auctions.php
2. Price Oracle (Every 5 Minutes)
Function: Updates $GASHY price from API or Simulation.
code
Bash
*/5 * * * * php /path/to/server/cron/oracle.php
3. Lottery Draw (Every Hour)
Function: Checks if draw time passed, picks winners, starts new round.
code
Bash
0 * * * * php /path/to/server/cron/lottery.php
4. System Maintenance (Daily at Midnight)
Function: Removes expired gift cards, cleans old sessions, rotates logs.
code
Bash
0 0 * * * php /path/to/server/cron/maintenance.php