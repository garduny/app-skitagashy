<?php
define('gashy_exec', true);
if (file_exists('server/init.php')) {
    require_once 'server/init.php';
}
require_once 'header.php';
require_once 'sidebar.php';
$priceData = json_decode(@file_get_contents('server/.cache/price.json'), true) ?: [];
$price = $priceData['price'] ?? 0.045;
$mcap = $priceData['marketcap'] ?? 0;
$vol = $priceData['volume24h'] ?? 0;
$liq = $priceData['liquidity'] ?? 0;
$banners = [];
try {
    $banners = getQuery(" SELECT image_path,link_url FROM banners WHERE is_active=1 ORDER BY sort_order ASC LIMIT 5");
} catch (Exception $e) {
}
$flash_deals = getQuery(" SELECT p.id,p.title,p.slug,p.price_gashy,p.images,p.type FROM products p WHERE p.status='active' AND p.stock>0 ORDER BY RAND() LIMIT 4");
$top_sellers = getQuery(" SELECT store_name,total_sales,rating FROM sellers WHERE is_approved=1 ORDER BY total_sales DESC LIMIT 5");
$new_arrivals = getQuery(" SELECT p.id,p.title,p.slug,p.price_gashy,p.images,p.type FROM products p WHERE p.status='active' ORDER BY p.created_at DESC LIMIT 8");
$lottery = findQuery(" SELECT prize_pool,round_number,draw_time FROM lottery_rounds WHERE status='open' ORDER BY id DESC LIMIT 1");
$mystery_box = findQuery(" SELECT p.id,p.title,p.slug,p.price_gashy,p.images FROM products p INNER JOIN mystery_box_loot m ON m.box_product_id=p.id WHERE p.status='active' AND p.stock>0 GROUP BY p.id ORDER BY p.created_at DESC LIMIT 1");
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
<style>
:root {
--neon: #00ffaa;
--neon-dim: #00d48f;
--plasma: #ff2d78;
--void: #03050d;
--void-2: #070b14;
--void-3: #0c1120;
--panel: rgba(8, 13, 26, 0.85);
--panel-border: rgba(0, 255, 170, 0.12);
--text-dim: rgba(148, 163, 184, 0.8);
--font-display: 'Orbitron', sans-serif;
--font-body: 'Rajdhani', sans-serif;
}
* {
box-sizing: border-box;
margin: 0;
padding: 0;
}
html {
scroll-behavior: smooth;
}
body {
background: var(--void);
color: #e2e8f0;
font-family: var(--font-body);
overflow-x: hidden;
}
@keyframes ticker {
0% { transform: translateX(0) }
100% { transform: translateX(-50%) }
}
@keyframes pulse {
0%, 100% { opacity: 1 }
50% { opacity: 0.6 }
}
@keyframes slideUp {
from { opacity: 0; transform: translateY(30px) }
to { opacity: 1; transform: translateY(0) }
}
.main-wrap {
padding-top: 6rem;
padding-left: 0;
min-height: 100vh;
position: relative;
}
@media(min-width:1024px) {
.main-wrap {
padding-left: 18rem
}
}
.bg-grid {
position: fixed;
inset: 0;
pointer-events: none;
z-index: 0;
background-image:
linear-gradient(rgba(0, 255, 170, 0.02) 1px, transparent 1px),
linear-gradient(90deg, rgba(0, 255, 170, 0.02) 1px, transparent 1px);
background-size: 60px 60px;
}
.bg-orb {
position: fixed;
width: 600px;
height: 600px;
border-radius: 50%;
filter: blur(100px);
opacity: 0.04;
pointer-events: none;
z-index: 0;
}
.orb-1 {
background: #00ffaa;
top: -200px;
left: 10%;
}
.orb-2 {
background: #3b82f6;
bottom: -100px;
right: 5%;
}
.content {
position: relative;
z-index: 10;
max-width: 1800px;
margin: 0 auto;
padding: 0 1.5rem 5rem;
}
.ticker-bar {
position: sticky;
top: 5rem;
z-index: 50;
overflow: hidden;
border-top: 1px solid rgba(0, 255, 170, 0.2);
border-bottom: 1px solid rgba(0, 255, 170, 0.2);
background: rgba(3, 5, 13, 0.95);
backdrop-filter: blur(10px);
}
.ticker-inner {
display: inline-flex;
gap: 3rem;
padding: 0.75rem 1rem;
animation: ticker 30s linear infinite;
white-space: nowrap;
}
.ticker-item {
font-family: var(--font-body);
font-size: 0.75rem;
font-weight: 700;
letter-spacing: 0.05em;
display: flex;
align-items: center;
gap: 0.4rem;
}
.ticker-dot {
width: 5px;
height: 5px;
border-radius: 50%;
flex-shrink: 0;
animation: pulse 2s ease-in-out infinite;
}
.hero-section {
padding: 4rem 0 3rem;
text-align: center;
position: relative;
}
.hero-badge {
display: inline-flex;
align-items: center;
gap: 0.5rem;
padding: 0.4rem 1.2rem;
border: 1px solid rgba(0, 255, 170, 0.3);
background: rgba(0, 255, 170, 0.05);
border-radius: 999px;
font-family: var(--font-body);
font-size: 0.7rem;
font-weight: 700;
letter-spacing: 0.1em;
color: var(--neon);
text-transform: uppercase;
margin-bottom: 2rem;
}
.hero-badge-dot {
width: 6px;
height: 6px;
background: var(--neon);
border-radius: 50%;
box-shadow: 0 0 8px var(--neon);
animation: pulse 2s ease-in-out infinite;
}
.hero-title {
font-family: var(--font-display);
font-size: clamp(2.5rem, 8vw, 6rem);
font-weight: 900;
line-height: 1;
letter-spacing: -0.02em;
color: #fff;
margin-bottom: 0.5rem;
}
.hero-title-accent {
font-family: var(--font-display);
font-size: clamp(3rem, 9vw, 7rem);
font-weight: 900;
line-height: 1;
letter-spacing: -0.02em;
background: linear-gradient(135deg, var(--neon), #00c8ff);
-webkit-background-clip: text;
-webkit-text-fill-color: transparent;
background-clip: text;
display: block;
margin-bottom: 2rem;
}
.hero-desc {
font-family: var(--font-body);
font-size: 1.1rem;
font-weight: 500;
color: var(--text-dim);
max-width: 680px;
margin: 0 auto 3rem;
line-height: 1.7;
}
.hero-desc span {
color: var(--neon);
font-weight: 700;
}
.btn {
display: inline-flex;
align-items: center;
gap: 0.6rem;
padding: 1rem 2rem;
font-family: var(--font-display);
font-size: 0.85rem;
font-weight: 700;
letter-spacing: 0.05em;
border-radius: 0.5rem;
border: none;
cursor: pointer;
transition: all 0.2s ease;
text-decoration: none;
}
.btn-primary {
background: var(--neon);
color: #000;
}
.btn-primary:hover {
background: #00ffcc;
box-shadow: 0 4px 20px rgba(0, 255, 170, 0.3);
transform: translateY(-2px);
}
.btn-secondary {
background: transparent;
color: var(--neon);
border: 1px solid rgba(0, 255, 170, 0.5);
}
.btn-secondary:hover {
background: rgba(0, 255, 170, 0.08);
border-color: var(--neon);
transform: translateY(-2px);
}
.btn-cg {
background: #8DC351;
color: #fff;
}
.btn-cg:hover {
background: #7caf43;
transform: translateY(-2px);
}
.hero-buttons {
display: flex;
flex-wrap: wrap;
gap: 1rem;
justify-content: center;
}
.stat-grid {
display: grid;
grid-template-columns: repeat(2, 1fr);
gap: 1px;
background: rgba(0, 255, 170, 0.1);
border: 1px solid rgba(0, 255, 170, 0.1);
border-radius: 1rem;
overflow: hidden;
margin: 4rem 0;
}
@media(min-width:768px) {
.stat-grid {
grid-template-columns: repeat(4, 1fr)
}
}
.stat-cell {
background: var(--panel);
padding: 2rem 1.5rem;
text-align: center;
transition: background 0.3s ease;
}
.stat-cell:hover {
background: rgba(0, 255, 170, 0.04)
}
.stat-label {
font-family: var(--font-body);
font-size: 0.7rem;
font-weight: 700;
letter-spacing: 0.15em;
color: rgba(148, 163, 184, 0.6);
text-transform: uppercase;
margin-bottom: 0.75rem;
}
.stat-val {
font-family: var(--font-display);
font-size: clamp(1.5rem, 4vw, 2.5rem);
font-weight: 900;
color: var(--neon);
line-height: 1;
}
.stat-val.white {
color: #fff
}
.panel {
background: var(--panel);
border: 1px solid var(--panel-border);
border-radius: 1rem;
transition: all 0.3s ease;
}
.panel:hover {
border-color: rgba(0, 255, 170, 0.25);
box-shadow: 0 10px 40px rgba(0, 255, 170, 0.05);
}
.chart-panel {
padding: 2rem;
}
.chart-header {
display: flex;
align-items: center;
gap: 0.75rem;
margin-bottom: 2rem;
}
.chart-title {
font-family: var(--font-display);
font-size: 1rem;
font-weight: 700;
letter-spacing: 0.05em;
color: #fff;
}
.section-title {
font-family: var(--font-display);
font-size: clamp(1.5rem, 4vw, 2.5rem);
font-weight: 900;
letter-spacing: 0.03em;
color: #fff;
margin-bottom: 0.5rem;
}
.section-title .accent {
color: var(--neon)
}
.section-sub {
font-family: var(--font-body);
font-size: 1rem;
color: var(--text-dim);
margin-bottom: 3rem;
}
.feature-grid {
display: grid;
grid-template-columns: repeat(1, 1fr);
gap: 1.5rem;
}
@media(min-width:640px) {
.feature-grid {
grid-template-columns: repeat(2, 1fr)
}
}
@media(min-width:1024px) {
.feature-grid {
grid-template-columns: repeat(3, 1fr)
}
}
.feature-card {
background: var(--panel);
border: 1px solid var(--panel-border);
border-radius: 1rem;
padding: 2rem;
transition: all 0.3s ease;
}
.feature-card:hover {
border-color: rgba(0, 255, 170, 0.3);
transform: translateY(-4px);
box-shadow: 0 10px 40px rgba(0, 255, 170, 0.08);
}
.feature-icon {
width: 3.5rem;
height: 3.5rem;
border-radius: 0.75rem;
background: rgba(0, 255, 170, 0.08);
border: 1px solid rgba(0, 255, 170, 0.2);
display: flex;
align-items: center;
justify-content: center;
font-size: 1.5rem;
margin-bottom: 1.5rem;
transition: all 0.3s ease;
}
.feature-card:hover .feature-icon {
background: rgba(0, 255, 170, 0.15);
}
.feature-title {
font-family: var(--font-display);
font-size: 0.95rem;
font-weight: 700;
letter-spacing: 0.05em;
color: #fff;
margin-bottom: 0.75rem;
}
.feature-desc {
font-family: var(--font-body);
font-size: 0.9rem;
color: var(--text-dim);
line-height: 1.6;
font-weight: 500;
}
.hero-banner-grid {
display: grid;
grid-template-columns: 1fr;
gap: 1.5rem;
margin-bottom: 5rem;
}
@media(min-width:1024px) {
.hero-banner-grid {
grid-template-columns: 3fr 1fr
}
}
.main-banner {
position: relative;
height: 400px;
border-radius: 1.25rem;
overflow: hidden;
border: 1px solid rgba(0, 255, 170, 0.1);
}
@media(min-width:768px) {
.main-banner {
height: 480px
}
}
.main-banner img {
width: 100%;
height: 100%;
object-fit: cover;
transition: transform 0.5s ease;
}
.main-banner:hover img {
transform: scale(1.05)
}
.main-banner-overlay {
position: absolute;
inset: 0;
background: linear-gradient(to top, rgba(3, 5, 13, 0.97) 0%, rgba(3, 5, 13, 0.5) 50%, transparent 100%);
}
.main-banner-content {
position: absolute;
bottom: 0;
left: 0;
padding: 2rem;
z-index: 10;
}
@media(min-width:768px) {
.main-banner-content {
padding: 2.5rem
}
}
.banner-tag {
display: inline-block;
padding: 0.3rem 0.8rem;
background: var(--neon);
color: #000;
font-family: var(--font-body);
font-size: 0.65rem;
font-weight: 700;
letter-spacing: 0.15em;
border-radius: 0.25rem;
text-transform: uppercase;
margin-bottom: 1rem;
}
.banner-title {
font-family: var(--font-display);
font-size: clamp(1.8rem, 5vw, 3.5rem);
font-weight: 900;
color: #fff;
line-height: 1;
letter-spacing: 0.02em;
margin-bottom: 1.5rem;
}
.banner-title .hl {
color: var(--neon);
}
.side-promos {
display: flex;
flex-direction: column;
gap: 1.5rem
}
.promo-card {
flex: 1;
border-radius: 1.25rem;
padding: 2rem;
display: flex;
flex-direction: column;
justify-content: space-between;
min-height: 200px;
position: relative;
overflow: hidden;
transition: transform 0.3s ease;
text-decoration: none;
color: #fff;
}
.promo-card:hover {
transform: translateY(-4px);
}
.promo-bg-1 {
background: linear-gradient(135deg, #7c3aed, #ec4899)
}
.promo-bg-2 {
background: linear-gradient(135deg, #059669, #0891b2)
}
.promo-label {
font-family: var(--font-display);
font-size: 1.3rem;
font-weight: 900;
letter-spacing: 0.04em;
margin-bottom: 0.4rem;
}
.promo-sub {
font-family: var(--font-body);
font-size: 0.75rem;
opacity: 0.8;
}
.promo-btn {
padding: 0.75rem;
background: rgba(255, 255, 255, 0.15);
border: 1px solid rgba(255, 255, 255, 0.2);
border-radius: 0.5rem;
text-align: center;
font-family: var(--font-display);
font-size: 0.75rem;
font-weight: 700;
letter-spacing: 0.08em;
transition: background 0.2s ease;
}
.promo-btn:hover {
background: rgba(255, 255, 255, 0.25)
}
.deals-header {
display: flex;
align-items: center;
justify-content: space-between;
flex-wrap: wrap;
gap: 1rem;
margin-bottom: 2rem;
}
.timer-badge {
display: flex;
align-items: center;
gap: 0.5rem;
padding: 0.5rem 1rem;
background: rgba(239, 68, 68, 0.1);
border: 1px solid rgba(239, 68, 68, 0.3);
border-radius: 0.5rem;
font-family: var(--font-body);
font-size: 0.75rem;
font-weight: 700;
color: #f87171;
}
.timer-dot {
width: 6px;
height: 6px;
background: #f87171;
border-radius: 50%;
animation: pulse 1s ease-in-out infinite;
}
.products-grid {
display: grid;
grid-template-columns: repeat(1, 1fr);
gap: 1.5rem;
}
@media(min-width:640px) {
.products-grid {
grid-template-columns: repeat(2, 1fr)
}
}
@media(min-width:1024px) {
.products-grid {
grid-template-columns: repeat(4, 1fr)
}
}
.product-card {
background: var(--panel);
border: 1px solid var(--panel-border);
border-radius: 1rem;
overflow: hidden;
transition: all 0.3s ease;
text-decoration: none;
display: block;
}
.product-card:hover {
border-color: rgba(0, 255, 170, 0.3);
transform: translateY(-4px);
box-shadow: 0 10px 40px rgba(0, 255, 170, 0.08);
}
.product-img {
aspect-ratio: 1;
overflow: hidden;
position: relative;
background: var(--void-3);
}
.product-img img {
width: 100%;
height: 100%;
object-fit: cover;
transition: transform 0.5s ease;
}
.product-card:hover .product-img img {
transform: scale(1.08)
}
.product-badge {
position: absolute;
top: 0.75rem;
right: 0.75rem;
padding: 0.3rem 0.6rem;
background: #dc2626;
color: #fff;
font-family: var(--font-body);
font-size: 0.65rem;
font-weight: 700;
border-radius: 0.25rem;
letter-spacing: 0.05em;
}
.product-info {
padding: 1.25rem
}
.product-name {
font-family: var(--font-display);
font-size: 0.85rem;
font-weight: 700;
color: #fff;
letter-spacing: 0.03em;
margin-bottom: 0.75rem;
white-space: nowrap;
overflow: hidden;
text-overflow: ellipsis;
}
.product-price-row {
display: flex;
align-items: center;
justify-content: space-between;
}
.product-price {
font-family: var(--font-body);
font-size: 1rem;
font-weight: 700;
color: var(--neon);
}
.product-price-old {
font-family: var(--font-body);
font-size: 0.75rem;
color: rgba(148, 163, 184, 0.5);
text-decoration: line-through;
}
.arrivals-sellers-grid {
display: grid;
grid-template-columns: 1fr;
gap: 2rem;
}
@media(min-width:1024px) {
.arrivals-sellers-grid {
grid-template-columns: 2fr 1fr
}
}
.arrival-item {
display: flex;
align-items: center;
gap: 1.25rem;
padding: 1.25rem;
background: var(--panel);
border: 1px solid var(--panel-border);
border-radius: 0.75rem;
transition: all 0.3s ease;
text-decoration: none;
}
.arrival-item:hover {
border-color: rgba(0, 255, 170, 0.25);
background: rgba(0, 255, 170, 0.02);
transform: translateX(4px);
}
.arrival-thumb {
width: 5rem;
height: 5rem;
border-radius: 0.5rem;
overflow: hidden;
flex-shrink: 0;
border: 1px solid rgba(255, 255, 255, 0.05);
background: var(--void-3);
}
.arrival-thumb img {
width: 100%;
height: 100%;
object-fit: cover
}
.arrival-info {
flex: 1;
min-width: 0
}
.arrival-name {
font-family: var(--font-display);
font-size: 0.9rem;
font-weight: 700;
color: #fff;
letter-spacing: 0.03em;
margin-bottom: 0.25rem;
}
.arrival-type {
font-family: var(--font-body);
font-size: 0.65rem;
font-weight: 700;
letter-spacing: 0.1em;
color: rgba(148, 163, 184, 0.5);
text-transform: uppercase;
}
.arrival-right {
text-align: right;
flex-shrink: 0
}
.arrival-price {
font-family: var(--font-body);
font-size: 1rem;
font-weight: 700;
color: var(--neon);
}
.buy-pill {
margin-top: 0.4rem;
padding: 0.3rem 0.8rem;
font-family: var(--font-body);
font-size: 0.65rem;
font-weight: 700;
letter-spacing: 0.1em;
background: rgba(0, 255, 170, 0.08);
border: 1px solid rgba(0, 255, 170, 0.2);
border-radius: 999px;
color: var(--neon);
cursor: pointer;
transition: all 0.2s ease;
display: inline-block;
text-decoration: none;
}
.buy-pill:hover {
background: rgba(0, 255, 170, 0.15);
border-color: rgba(0, 255, 170, 0.5);
}
.sellers-panel {
background: var(--panel);
border: 1px solid var(--panel-border);
border-radius: 1rem;
padding: 1.5rem;
}
.seller-row {
display: flex;
align-items: center;
gap: 1rem;
padding: 1rem 0;
border-bottom: 1px solid rgba(255, 255, 255, 0.04);
}
.seller-row:last-child {
border-bottom: none
}
.seller-rank {
width: 2.5rem;
height: 2.5rem;
border-radius: 0.5rem;
display: flex;
align-items: center;
justify-content: center;
font-family: var(--font-display);
font-size: 0.9rem;
font-weight: 900;
flex-shrink: 0;
}
.rank-1 {
background: linear-gradient(135deg, #f59e0b, #ef4444);
color: #000
}
.rank-2 {
background: linear-gradient(135deg, #6b7280, #9ca3af);
color: #000
}
.rank-3 {
background: linear-gradient(135deg, #92400e, #b45309);
color: #fff
}
.rank-other {
background: rgba(0, 255, 170, 0.08);
border: 1px solid rgba(0, 255, 170, 0.2);
color: var(--neon)
}
.seller-name {
font-family: var(--font-display);
font-size: 0.85rem;
font-weight: 700;
color: #fff;
letter-spacing: 0.03em;
}
.seller-rating {
font-family: var(--font-body);
font-size: 0.7rem;
color: #fbbf24;
display: flex;
align-items: center;
gap: 0.25rem;
}
.seller-sales {
font-family: var(--font-body);
font-size: 0.7rem;
font-weight: 700;
color: rgba(148, 163, 184, 0.5);
margin-left: auto;
flex-shrink: 0;
}
.become-seller {
border-radius: 1rem;
padding: 2rem;
text-align: center;
background: linear-gradient(135deg, #1e40af, #7c3aed);
margin-top: 1.5rem;
}
.become-title {
font-family: var(--font-display);
font-size: 1.2rem;
font-weight: 900;
color: #fff;
letter-spacing: 0.05em;
margin-bottom: 0.5rem;
}
.become-sub {
font-family: var(--font-body);
font-size: 0.75rem;
color: rgba(255, 255, 255, 0.7);
margin-bottom: 1.5rem;
}
.spacer {
margin-bottom: 5rem
}
.section-heading {
margin-bottom: 1rem
}
.slide-up {
animation: slideUp 0.6s ease-out
}
html:not(.dark) body {
background: #f1f5f9;
color: #0f172a;
}
html:not(.dark) .bg-grid {
background-image:
linear-gradient(rgba(0, 163, 114, 0.04) 1px, transparent 1px),
linear-gradient(90deg, rgba(0, 163, 114, 0.04) 1px, transparent 1px);
}
html:not(.dark) .bg-orb {
opacity: 0.06;
}
html:not(.dark) .orb-1 {
background: #00c896;
}
html:not(.dark) .orb-2 {
background: #3b82f6;
}
html:not(.dark) .ticker-bar {
background: rgba(255, 255, 255, 0.95);
border-top: 1px solid rgba(0, 163, 114, 0.2);
border-bottom: 1px solid rgba(0, 163, 114, 0.2);
}
html:not(.dark) .ticker-item span[style*="rgba(148,163,184,0.7)"] {
color: #475569 !important;
}
html:not(.dark) .hero-badge {
border-color: rgba(0, 163, 114, 0.3);
background: rgba(0, 163, 114, 0.08);
color: #007a55;
}
html:not(.dark) .hero-badge-dot {
background: #007a55;
box-shadow: 0 0 8px rgba(0, 163, 114, 0.5);
}
html:not(.dark) .hero-title {
color: #0f172a;
}
html:not(.dark) .hero-title-accent {
background: linear-gradient(135deg, #007a55, #0ea5e9);
-webkit-background-clip: text;
-webkit-text-fill-color: transparent;
background-clip: text;
}
html:not(.dark) .hero-desc {
color: #475569;
}
html:not(.dark) .hero-desc span {
color: #007a55;
}
html:not(.dark) .btn-primary {
background: linear-gradient(135deg, #00a372, #00c896);
color: #fff;
}
html:not(.dark) .btn-primary:hover {
background: linear-gradient(135deg, #007a55, #00a372);
}
html:not(.dark) .btn-secondary {
border-color: rgba(0, 163, 114, 0.5);
color: #007a55;
}
html:not(.dark) .btn-secondary:hover {
background: rgba(0, 163, 114, 0.08);
border-color: #00a372;
}
html:not(.dark) .stat-grid {
background: rgba(0, 163, 114, 0.08);
border-color: rgba(0, 163, 114, 0.15);
}
html:not(.dark) .stat-cell {
background: rgba(255, 255, 255, 0.95);
}
html:not(.dark) .stat-cell:hover {
background: rgba(0, 163, 114, 0.04);
}
html:not(.dark) .stat-label {
color: #64748b;
}
html:not(.dark) .stat-val {
color: #007a55;
}
html:not(.dark) .stat-val.white {
color: #0f172a;
}
html:not(.dark) .panel {
background: rgba(255, 255, 255, 0.97);
border-color: rgba(0, 163, 114, 0.12);
}
html:not(.dark) .panel:hover {
border-color: rgba(0, 163, 114, 0.25);
}
html:not(.dark) .chart-title {
color: #0f172a;
}
html:not(.dark) .section-title {
color: #0f172a;
}
html:not(.dark) .section-title .accent {
color: #007a55;
}
html:not(.dark) .section-sub {
color: #64748b;
}
html:not(.dark) .feature-card {
background: rgba(255, 255, 255, 0.97);
border-color: rgba(0, 163, 114, 0.12);
}
html:not(.dark) .feature-card:hover {
border-color: rgba(0, 163, 114, 0.3);
}
html:not(.dark) .feature-icon {
background: rgba(0, 163, 114, 0.08);
border-color: rgba(0, 163, 114, 0.2);
}
html:not(.dark) .feature-card:hover .feature-icon {
background: rgba(0, 163, 114, 0.14);
}
html:not(.dark) .feature-title {
color: #0f172a;
}
html:not(.dark) .feature-desc {
color: #475569;
}
html:not(.dark) .main-banner {
border-color: rgba(0, 163, 114, 0.15);
}
html:not(.dark) .product-card {
background: rgba(255, 255, 255, 0.97);
border-color: rgba(0, 0, 0, 0.07);
}
html:not(.dark) .product-card:hover {
border-color: rgba(0, 163, 114, 0.3);
}
html:not(.dark) .product-img {
background: #f1f5f9;
}
html:not(.dark) .product-name {
color: #0f172a;
}
html:not(.dark) .product-price {
color: #007a55;
}
html:not(.dark) .product-price-old {
color: #94a3b8;
}
html:not(.dark) .arrival-item {
background: rgba(255, 255, 255, 0.97);
border-color: rgba(0, 0, 0, 0.07);
}
html:not(.dark) .arrival-item:hover {
border-color: rgba(0, 163, 114, 0.25);
background: rgba(0, 163, 114, 0.02);
}
html:not(.dark) .arrival-thumb {
border-color: rgba(0, 0, 0, 0.06);
background: #f1f5f9;
}
html:not(.dark) .arrival-name {
color: #0f172a;
}
html:not(.dark) .arrival-type {
color: #94a3b8;
}
html:not(.dark) .arrival-price {
color: #007a55;
}
html:not(.dark) .buy-pill {
background: rgba(0, 163, 114, 0.08);
border-color: rgba(0, 163, 114, 0.2);
color: #007a55;
}
html:not(.dark) .buy-pill:hover {
background: rgba(0, 163, 114, 0.15);
border-color: rgba(0, 163, 114, 0.4);
}
html:not(.dark) .sellers-panel {
background: rgba(255, 255, 255, 0.97);
border-color: rgba(0, 163, 114, 0.12);
}
html:not(.dark) .seller-row {
border-bottom-color: rgba(0, 0, 0, 0.05);
}
html:not(.dark) .seller-name {
color: #0f172a;
}
html:not(.dark) .seller-sales {
color: #94a3b8;
}
html:not(.dark) .rank-other {
background: rgba(0, 163, 114, 0.1);
border-color: rgba(0, 163, 114, 0.2);
color: #007a55;
}
</style>
<div class="bg-grid"></div>
<div class="bg-orb orb-1"></div>
<div class="bg-orb orb-2"></div>
<main class="main-wrap">
<div class="ticker-bar">
<div class="ticker-inner">
<span class="ticker-item"><span class="ticker-dot" style="background:#00ffaa;"></span><span style="color:#00ffaa">GASHY</span> <span style="color:rgba(148,163,184,0.7)">$<?= number_format($price, 5) ?></span> <span style="color:#00ffaa">▲ 5.2%</span></span>
<span class="ticker-item"><span class="ticker-dot" style="background:#60a5fa;"></span><span style="color:#60a5fa">SOL</span> <span style="color:rgba(148,163,184,0.7)">$145.20</span> <span style="color:#60a5fa">▲ 2.1%</span></span>
<span class="ticker-item"><span class="ticker-dot" style="background:#f97316;"></span><span style="color:#f97316">BTC</span> <span style="color:rgba(148,163,184,0.7)">$68,420</span> <span style="color:#f97316">▲ 1.8%</span></span>
<span class="ticker-item"><span class="ticker-dot" style="background:#a78bfa;"></span><span style="color:#a78bfa">ETH</span> <span style="color:rgba(148,163,184,0.7)">$3,850</span> <span style="color:#f87171">▼ 0.5%</span></span>
<span class="ticker-item"><span class="ticker-dot" style="background:#fbbf24;"></span><span style="color:#fbbf24">BNB</span> <span style="color:rgba(148,163,184,0.7)">$2,400</span> <span style="color:#fbbf24">▲ 3.0%</span></span>
<span class="ticker-item"><span class="ticker-dot" style="background:#00ffaa;"></span><span style="color:#00ffaa">GASHY</span> <span style="color:rgba(148,163,184,0.7)">$<?= number_format($price, 5) ?></span> <span style="color:#00ffaa">▲ 5.2%</span></span>
<span class="ticker-item"><span class="ticker-dot" style="background:#60a5fa;"></span><span style="color:#60a5fa">SOL</span> <span style="color:rgba(148,163,184,0.7)">$145.20</span> <span style="color:#60a5fa">▲ 2.1%</span></span>
<span class="ticker-item"><span class="ticker-dot" style="background:#f97316;"></span><span style="color:#f97316">BTC</span> <span style="color:rgba(148,163,184,0.7)">$68,420</span> <span style="color:#f97316">▲ 1.8%</span></span>
<span class="ticker-item"><span class="ticker-dot" style="background:#a78bfa;"></span><span style="color:#a78bfa">ETH</span> <span style="color:rgba(148,163,184,0.7)">$3,850</span> <span style="color:#f87171">▼ 0.5%</span></span>
<span class="ticker-item"><span class="ticker-dot" style="background:#fbbf24;"></span><span style="color:#fbbf24">BNB</span> <span style="color:rgba(148,163,184,0.7)">$2,400</span> <span style="color:#fbbf24">▲ 3.0%</span></span>
</div>
</div>
<div class="content">
<section class="hero-section slide-up">
<div class="hero-badge">
<span class="hero-badge-dot"></span>
THE APEX PREDATOR OF SOLANA MEMECOINS
</div>
<h1 class="hero-title">GASHY IS THE</h1>
<span class="hero-title-accent">MEME ALPHA</span>
<p class="hero-desc">
CoinGecko listed Solana memecoin with <span>revoked authorities</span>, <span>burned LP</span>, and <span>real utility</span>. Join the #GashyGang revolution.
</p>
<div class="hero-buttons">
<a href="https://jup.ag/swap/SOL-GASHY" target="_blank" class="btn btn-primary">
<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
<path d="M4.5 19.5l15-15M4.5 4.5h15v15" />
</svg>
BUY ON JUPITER
</a>
<a href="https://www.coingecko.com/en/coins/gashy" target="_blank" class="btn btn-cg">
🦎 COINGECKO
</a>
<button onclick="navigator.clipboard.writeText('DokPYQ33k3T9S7EEesvwvuuAtoQb4pY8NWszukKwXWjv');notyf.success('Contract Copied!')" class="btn btn-secondary">
<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
<rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
</svg>
COPY CONTRACT
</button>
</div>
</section>
<div class="stat-grid slide-up">
<div class="stat-cell">
<div class="stat-label">PRICE (USD)</div>
<div class="stat-val">$<?= number_format($price, 6) ?></div>
</div>
<div class="stat-cell">
<div class="stat-label">MARKET CAP</div>
<div class="stat-val white">$<?= number_format($mcap / 1000, 1) ?>K</div>
</div>
<div class="stat-cell">
<div class="stat-label">24H VOLUME</div>
<div class="stat-val white">$<?= number_format($vol / 1000, 2) ?>K</div>
</div>
<div class="stat-cell">
<div class="stat-label">LIQUIDITY</div>
<div class="stat-val white">$<?= number_format($liq / 1000, 2) ?>K</div>
</div>
</div>
<div class="panel chart-panel spacer slide-up">
<div class="chart-header">
<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#00ffaa" stroke-width="2">
<polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
</svg>
<span class="chart-title">GASHY / USD — LIVE PRICE</span>
<span style="margin-left:auto;font-family:var(--font-body);font-size:0.7rem;color:rgba(148,163,184,0.4);letter-spacing:0.1em">SOLANA</span>
</div>
<div id="priceChart" style="height:380px"></div>
</div>
<div class="hero-banner-grid spacer slide-up">
<div class="main-banner" id="bannerSlider" style="position:relative;overflow:hidden;">
<?php if (!empty($banners)): ?>
<?php foreach ($banners as $bi => $b): ?>
<div class="banner-slide" data-index="<?= $bi ?>" style="position:absolute;inset:0;opacity:<?= $bi === 0 ? '1' : '0' ?>;transition:opacity 0.8s ease;z-index:<?= $bi === 0 ? 2 : 1 ?>">
<img src="./<?= htmlspecialchars($b['image_path']) ?>" alt="Banner <?= $bi + 1 ?>" style="width:100%;height:100%;object-fit:cover;">
<div class="main-banner-overlay"></div>
<div class="main-banner-content">
<span class="banner-tag">FEATURED DROP</span>
<h2 class="banner-title">GASHY <span class="hl">MARKETPLACE</span></h2>
<a href="<?= htmlspecialchars($b['link_url']) ?>" class="btn btn-primary">
EXPLORE NOW
<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
<path d="M5 12h14M12 5l7 7-7 7" />
</svg>
</a>
</div>
</div>
<?php endforeach; ?>
<div style="position:absolute;bottom:1.2rem;right:1.5rem;z-index:10;display:flex;gap:0.5rem;">
<?php foreach ($banners as $bi => $b): ?>
<button onclick="goSlide(<?= $bi ?>)" id="dot-<?= $bi ?>" style="width:8px;height:8px;border-radius:50%;border:none;cursor:pointer;transition:all 0.3s;background:<?= $bi === 0 ? 'var(--neon)' : 'rgba(255,255,255,0.35)' ?>;box-shadow:<?= $bi === 0 ? '0 0 8px var(--neon)' : '' ?>"></button>
<?php endforeach; ?>
</div>
<?php else: ?>
<div style="width:100%;height:100%;background:linear-gradient(135deg,#1e3a5f,#312e81);display:flex;align-items:center;justify-content:center;">
<h2 style="font-family:var(--font-display);font-size:2.5rem;font-weight:900;color:#fff;text-align:center;padding:2rem;">WELCOME TO<br><span style="color:var(--neon)">GASHY BAZAAR</span></h2>
</div>
<?php endif; ?>
</div>
<div class="side-promos">
<a href="mystery-boxes.php<?= $mystery_box ? '?id=' . $mystery_box['id'] : '' ?>" class="promo-card promo-bg-1">
<div>
<div class="promo-label">MYSTERY BOX</div>
<div class="promo-sub"><?= $mystery_box ? htmlspecialchars(strtoupper($mystery_box['title'])) : 'No Box Added' ?></div>
<?php if ($mystery_box): ?>
<div style="font-family:var(--font-body);font-size:0.7rem;color:rgba(255,255,255,0.55);margin-top:0.25rem"><?= number_format($mystery_box['price_gashy']) ?> G / OPEN</div>
<?php endif; ?>
</div>
<div class="promo-btn">OPEN NOW →</div>
</a>
<a href="lottery.php" class="promo-card promo-bg-2">
<div>
<div class="promo-label">LOTTERY #<?= htmlspecialchars($lottery['round_number'] ?? '—') ?></div>
<div class="promo-sub">POOL: <?= number_format($lottery['prize_pool'] ?? 0) ?> G</div>
<?php if (!empty($lottery['draw_time'])): ?>
<div style="font-family:var(--font-body);font-size:0.7rem;color:rgba(255,255,255,0.55);margin-top:0.25rem">DRAW: <?= date('d M H:i', strtotime($lottery['draw_time'])) ?></div>
<?php endif; ?>
</div>
<div class="promo-btn">BUY TICKET →</div>
</a>
</div>
</div>
<div class="spacer slide-up">
<div class="section-heading" style="text-align:center">
<h2 class="section-title">WHY CHOOSE <span class="accent">$GASHY?</span></h2>
<p class="section-sub">More than just a meme — real utility, transparency, and community.</p>
</div>
<div class="feature-grid">
<?php
$features = [
['🔐', 'SECURITY FIRST', 'Mint and freeze authorities permanently revoked. 100% LP burned. Verified on SolSniffer.'],
['✅', 'COINGECKO LISTED', 'Official listing on CoinGecko with verified pricing and market data across 17+ platforms.'],
['💎', 'ACTIVE STAKING', 'Earn rewards by staking your $GASHY. Non-custodial via Streamflow on Solana.'],
['🎨', 'LIVE NFTS', '51-piece genesis NFT collection with holder perks and exclusive benefits.'],
['🤝', 'TRANSPARENT TEAM', 'Clear founder reserve with on-chain proofs. No hidden allocations or shady practices.'],
['📈', 'REAL ROADMAP', '30/60/90 day milestones with measurable deliverables. Not just empty promises.'],
];
foreach ($features as $f): ?>
<div class="feature-card">
<div class="feature-icon"><?= $f[0] ?></div>
<div class="feature-title"><?= $f[1] ?></div>
<div class="feature-desc"><?= $f[2] ?></div>
</div>
<?php endforeach; ?>
</div>
</div>
<div class="spacer slide-up">
<div class="deals-header">
<h2 class="section-title" style="margin-bottom:0">
<span style="color:#fbbf24">⚡</span> FLASH DEALS
</h2>
<div class="timer-badge">
<span class="timer-dot"></span>
ENDS: <span id="countdown" style="letter-spacing:0.05em">04:22:19</span>
</div>
</div>
<div class="products-grid">
<?php foreach ($flash_deals as $p):
$img = json_decode($p['images'])[0] ?? 'assets/placeholder.png';
?>
<a href="product.php?slug=<?= htmlspecialchars($p['slug']) ?>" class="product-card">
<div class="product-img">
<img src="./<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['title']) ?>" loading="lazy">
<span class="product-badge">-20%</span>
</div>
<div class="product-info">
<div class="product-name"><?= htmlspecialchars($p['title']) ?></div>
<div class="product-price-row">
<span class="product-price"><?= number_format($p['price_gashy']) ?> G</span>
<span class="product-price-old"><?= number_format($p['price_gashy'] * 1.2) ?></span>
</div>
</div>
</a>
<?php endforeach; ?>
</div>
</div>
<div class="arrivals-sellers-grid spacer">
<div class="slide-up">
<h2 class="section-title" style="margin-bottom:2rem">NEW ARRIVALS</h2>
<div style="display:flex;flex-direction:column;gap:1rem">
<?php foreach ($new_arrivals as $p):
$img = json_decode($p['images'])[0] ?? 'assets/placeholder.png';
?>
<a href="product.php?slug=<?= htmlspecialchars($p['slug']) ?>" class="arrival-item">
<div class="arrival-thumb">
<img src="./<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['title']) ?>" loading="lazy">
</div>
<div class="arrival-info">
<div class="arrival-name"><?= htmlspecialchars($p['title']) ?></div>
<div class="arrival-type"><?= htmlspecialchars($p['type']) ?></div>
</div>
<div class="arrival-right">
<div class="arrival-price"><?= number_format($p['price_gashy']) ?> G</div>
<span class="buy-pill">BUY NOW</span>
</div>
</a>
<?php endforeach; ?>
</div>
</div>
<div class="slide-up">
<h2 class="section-title" style="margin-bottom:2rem">TOP SELLERS</h2>
<div class="sellers-panel">
<?php foreach ($top_sellers as $i => $s): ?>
<div class="seller-row">
<div class="seller-rank <?= match ($i) {
0 => 'rank-1',
1 => 'rank-2',
2 => 'rank-3',
default => 'rank-other'
} ?>"><?= $i + 1 ?></div>
<div>
<div class="seller-name"><?= htmlspecialchars($s['store_name']) ?></div>
<div class="seller-rating">
<svg width="10" height="10" viewBox="0 0 20 20" fill="currentColor">
<path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
</svg>
<?= htmlspecialchars($s['rating']) ?>
</div>
</div>
<div class="seller-sales"><?= htmlspecialchars($s['total_sales']) ?> SOLD</div>
</div>
<?php endforeach; ?>
</div>
<div class="become-seller">
<div class="become-title">BECOME A SELLER</div>
<div class="become-sub">Launch your own crypto store today</div>
<a href="seller.php" class="btn btn-primary" style="width:100%;justify-content:center">APPLY NOW</a>
</div>
</div>
</div>
</div>
</main>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
var basePrice = <?= $price ?>;
var points = 48;
var cats = [],data = [];
var now = Date.now();
var last = basePrice;
function nextPrice(p) {
var drift = (Math.random() - 0.48) * 0.035;
var shock = (Math.random() < 0.08) ? (Math.random() - 0.5) * 0.12 : 0;
var v = p * (1 + drift + shock);
if (v <= 0) v = p * 1.002;
return parseFloat(v.toFixed(8));
}
for (var i = points - 1; i >= 0; i--) {
last = nextPrice(last);
data.push(last);
cats.push(new Date(now - i * 1800000).toLocaleTimeString([], {
hour: '2-digit',
minute: '2-digit'
}));
}
var isDark = document.documentElement.classList.contains('dark');
var neon = isDark ? '#00ffaa' : '#007a55';
var label = isDark ? 'rgba(148,163,184,0.45)' : '#64748b';
var grid = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.06)';
var mode = isDark ? 'dark' : 'light';
var chart = new ApexCharts(document.querySelector('#priceChart'), {
series: [{
name: 'GASHY',
data: data
}],
chart: {
type: 'area',
height: 380,
background: 'transparent',
toolbar: {
show: false
},
animations: {
enabled: true,
easing: 'easeinout',
speed: 600
},
zoom: {
enabled: false
}
},
stroke: {
curve: 'smooth',
width: 2,
colors: [neon]
},
colors: [neon],
fill: {
type: 'gradient',
gradient: {
shadeIntensity: 1,
opacityFrom: isDark ? 0.25 : 0.15,
opacityTo: 0,
stops: [0, 100]
}
},
dataLabels: {
enabled: false
},
markers: {
size: 0,
hover: {
size: 5
}
},
xaxis: {
categories: cats,
tickAmount: 6,
labels: {
style: {
colors: label,
fontFamily: 'Rajdhani, sans-serif',
fontSize: '11px'
}
},
axisBorder: {
show: false
},
axisTicks: {
show: false
}
},
yaxis: {
labels: {
style: {
colors: label,
fontFamily: 'Rajdhani, sans-serif',
fontSize: '11px'
},
formatter: function(v) {
if (v < 0.001) return '$' + v.toFixed(6);
if (v < 1) return '$' + v.toFixed(4);
return '$' + v.toFixed(2);
}
}
},
grid: {
borderColor: grid,
strokeDashArray: 4,
padding: {
left: 10,
right: 20
}
},
tooltip: {
theme: mode,
style: {
fontFamily: 'Rajdhani, sans-serif',
fontSize: '12px'
},
y: {
formatter: function(v) {
return '$' + v.toFixed(6)
}
}
},
theme: {
mode: mode
}
});
chart.render();
setInterval(function() {
last = nextPrice(last);
data.push(last);
data.shift();
cats.push(new Date().toLocaleTimeString([], {
hour: '2-digit',
minute: '2-digit'
}));
cats.shift();
chart.updateSeries([{
data: data
}], true);
chart.updateOptions({
xaxis: {
categories: cats
}
}, false, false);
}, 15000);
var observer = new MutationObserver(function() {
var dark = document.documentElement.classList.contains('dark');
var nc = dark ? '#00ffaa' : '#007a55';
var lc = dark ? 'rgba(148,163,184,0.45)' : '#64748b';
var gc = dark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.06)';
chart.updateOptions({
stroke: {
colors: [nc]
},
colors: [nc],
fill: {
gradient: {
opacityFrom: dark ? 0.25 : 0.15
}
},
xaxis: {
labels: {
style: {
colors: lc
}
}
},
yaxis: {
labels: {
style: {
colors: lc
}
}
},
grid: {
borderColor: gc
},
theme: {
mode: dark ? 'dark' : 'light'
},
tooltip: {
theme: dark ? 'dark' : 'light'
}
}, false, true);
});
observer.observe(document.documentElement, {
attributes: true,
attributeFilter: ['class']
});
});
</script>
<script>
(function() {
var slides = document.querySelectorAll('.banner-slide');
var dots = document.querySelectorAll('[id^="dot-"]');
var cur = 0,total = slides.length;
if (total < 2) return;
function goSlide(n) {
slides[cur].style.opacity = '0';
slides[cur].style.zIndex = '1';
dots[cur].style.background = 'rgba(255,255,255,0.35)';
dots[cur].style.boxShadow = '';
cur = (n + total) % total;
slides[cur].style.opacity = '1';
slides[cur].style.zIndex = '2';
dots[cur].style.background = 'var(--neon)';
dots[cur].style.boxShadow = '0 0 8px var(--neon)';
}
window.goSlide = goSlide;
setInterval(function() {
goSlide(cur + 1);
}, 5000);
})();
</script>
<?php require_once 'footer.php'; ?>
</body>
</html>