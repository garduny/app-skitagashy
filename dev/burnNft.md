1) Key idea (very important)

On Solana, “Publish an NFT collection” usually means:

Creator uploads assets + metadata (image + JSON metadata).

You store those files on decentralized storage (Arweave / IPFS / Shadow Drive).

You create an on-chain “drop config” that points to the metadata URIs.

Users mint = an on-chain transaction creates the NFT mint + metadata + gives it to the buyer.

Burning = owner signs a transaction that burns the NFT token and closes accounts.

So:
Publish ≠ Mint.
Publish is “prepare a mintable drop.” Mint is when the NFT actually gets created on-chain.

2) What “NFT” is on Solana (minimal accurate model)

A typical Solana NFT uses these main pieces:

A) SPL Token Mint (the NFT token)

An NFT is basically a token mint with 0 decimals and supply 1 (or pNFT / Core variations).

The buyer receives it in their Associated Token Account (ATA).

B) Metadata (Metaplex)

Solana NFTs use Metaplex’s Token Metadata program to store:

name, symbol, uri (points to JSON), creators, collection, royalties, etc.

C) The JSON metadata file (off-chain)

Example fields:

{
  "name": "Gashy #1",
  "symbol": "GASHY",
  "description": "Gashy Bazzar NFT",
  "image": "https://.../image.png",
  "attributes": [
    {"trait_type":"Background","value":"Neon"},
    {"trait_type":"Rarity","value":"Legendary"}
  ],
  "properties": {
    "files":[{"uri":"https://.../image.png","type":"image/png"}],
    "category":"image"
  }
}
3) Your required flow (Seller → Publish → Approve → Mint with GASH)
Roles

Seller/Creator: uploads NFT info and assets.

Gashy Bazzar Admin: approves and makes it “mintable”.

Buyer: mints using GASH token (SPL token).

A) Seller creates an account + submits a “Drop”

Seller provides:

Collection name (or uses existing collection)

NFT name template (optional)

Supply (how many can be minted)

Image(s)

Attributes

Description

Royalties (seller fee basis points)

Creator address(es) + shares

Mint price in GASH

Start/end time, max mints per wallet (optional)

Whether it is “collection verified” (optional)

B) Upload + Storage (critical)

Your backend must upload:

image.png (or .jpg)

metadata.json referencing the image URL

Store them on decentralized storage:

Arweave, IPFS, Shadow Drive, etc.

Output: a permanent URI for each metadata JSON.

Your DB saves:

seller wallet

drop config

metadata URIs list (for each NFT in the drop, or template-based)

status = PENDING_APPROVAL

C) Approval step (Admin)

Admin checks:

content valid

metadata valid

supply & price valid
Then admin sets:

status = APPROVED

now it appears on marketplace with “Mint” button.

At approval time, you choose one of two mint architectures:

4) Two valid architectures (pick ONE)
Option 1 (Most like LaunchMyNFT): Candy Machine v3

Best for “drop minting” experience.

Publish = create Candy Machine config with:

collection mint

list of metadata URIs (or config lines)

guards: price, start date, mint limit per wallet, etc.

Mint = buyer calls Candy Machine mint instruction.

Payment in GASH: use Candy Guard “tokenPayment” style guard OR a custom program to transfer GASH then mint.

✅ Pros: battle-tested drop model
⚠️ Cons: more Metaplex-specific setup

Option 2 (Simpler to reason about): Custom “Mint Program” (Anchor)

You build a program that does:

Transfer GASH from buyer → seller/treasury

Mint NFT to buyer

Create metadata + optionally verify collection

Mark minted index as used (so supply is enforced)

✅ Pros: full control, easier to integrate “approval + marketplace rules”
⚠️ Cons: you must implement security & supply limits correctly

For Gashy Bazzar with “approve then mint with GASH” I recommend Option 2 unless your dev already knows Candy Machine well.

5) What on-chain programs you will need (must-have list)

Regardless of option, you will use these Solana standards:

SPL Token Program

For GASH payments and token accounts.

Associated Token Account Program

To create buyer’s token accounts automatically.

Metaplex Token Metadata Program

To create NFT metadata on-chain that points to the JSON URI.

(Optional but common) Collection Mint

A “collection NFT” that your minted NFTs can be verified against.

Your own Gashy Bazzar Program (if custom minting)

Holds config, enforces supply, price, limits, approved status.

6) Step-by-step Mint (custom program approach)
On-chain accounts you need

dropPDA: stores drop settings (approved, price, supply, mintedCount, seller, treasury, etc.)

treasuryTokenAccount (GASH ATA owned by treasury or seller)

buyerTokenAccount (GASH ATA of buyer)

nftMint (new mint account)

buyerNftATA (ATA for the new NFT)

metadataPDA (Metaplex metadata account)

masterEditionPDA (Metaplex master edition account)

(optional) collectionMint, collectionMetadata, collectionMasterEdition

Mint transaction logic

In one transaction (single “Mint” click):

Verify drop is approved + live

dropPDA.approved == true

mintedCount < supply

check start/end time, wallet limit, etc.

Transfer payment in GASH

CPI call to SPL Token TransferChecked

buyerGASH_ATA → treasuryGASH_ATA

amount = priceInGash * 10^decimals (GASH decimals matter!)

Create NFT mint

create mint with decimals = 0

mint authority = your program (or PDA)

freeze authority optional

Create buyer ATA for the NFT

Associated Token Account program

Mint 1 token to buyer

SPL Token MintTo

Create Metadata + Master Edition

CPI call to Metaplex Token Metadata:

CreateMetadataAccountV3

CreateMasterEditionV3

metadata points to your stored JSON uri

Optionally verify collection

VerifySizedCollectionItem / collection verify instruction

Update drop state

mintedCount += 1

mark index used (if you assign unique URIs per mint)

7) Burning NFT (what it means + how to do it)

Burning should be owner-only.

What happens in burn

Owner signs a tx.

SPL Token burn:

Burn 1 token from owner’s NFT ATA.

Close the NFT ATA (optional).

Optionally “clean up” metadata/master edition:

Some metadata remains as history, but you can also close accounts depending on standard and authority rules.

Why burn exists in your marketplace

Remove supply

“Redeem” mechanic (burn to claim something)

Clean listings

Important: If you allow burn, define rules:

Who can burn (only owner)

Whether burn refunds anything (usually no)

How marketplace updates listing state (DB should mark as burned)

8) What your WEBSITE must have (front + back)
Frontend

Solana Wallet Adapter (Phantom, Backpack, etc.)

RPC connection (Helius/QuickNode/etc.)

“Sign in” method (recommended): Sign-In With Solana pattern

Mint button that builds the transaction:

calls your backend for mint parameters (uri/index)

sends tx to wallet for signature

confirms tx and shows minted NFT

Backend

File upload service (image + metadata JSON)

Storage uploader (Arweave/IPFS/Shadow Drive)

DB tables:

users (seller profiles)

drops (status, price, supply, approved, seller wallet)

assets (imageUri, metadataUri)

mints (mintAddress, buyer, dropId, txSig)

burns (mintAddress, owner, txSig)

Admin panel for approve/reject

Webhook/indexer (optional but powerful):

watch transactions / confirmations

update DB automatically after mint/burn

9) Payment with GASH token (SPL token) details

You must know:

GASH mint address (SPL token mint)

decimals (example 6 or 9, depends on token)

treasury wallet (where payments go)

Mint UI must ensure:

buyer has GASH ATA

buyer has enough balance

transfer amount uses correct decimals

10) Minimum “things to select” in Publish form (seller UI)

To match LaunchMyNFT feel, seller selects:

Drop settings

Collection name + symbol

Supply (max mints)

Mint price (in GASH)

Start/end time

Max per wallet

Royalties % (seller fee basis points)

Creators list + shares

NFT settings

Name / numbering style: “Gashy #001”

Description

Image(s)

Attributes (traits)

External link (optional)

Background, category, etc. (optional)

Approval

“Submit for approval”

status: pending → approved/rejected

11) Security rules (do not skip)

Never keep private keys in frontend.

If backend must sign (admin actions), keep signer in secure server env.

Enforce supply/limits on-chain (not only in DB).

Always validate:

drop approved

correct GASH mint

correct treasury

correct price

Rate-limit mint requests to avoid abuse.


**************************************************************************************
Publish pipeline (upload image → generate metadata → upload JSON → save URIs)

Drop approval system (admin flips approved)

On-chain mint program (transfer GASH + mint NFT + metadata)

Mint UI (wallet connect + mint tx)

Indexer (confirm tx → write minted NFT into DB)

Burn flow (owner burns + DB updates)