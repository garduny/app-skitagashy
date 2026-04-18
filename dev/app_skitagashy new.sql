-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 18, 2026 at 05:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `app_skitagashy`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `wallet_address` varchar(44) NOT NULL,
  `accountname` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('account','seller','admin') DEFAULT 'account',
  `tier` enum('bronze','silver','gold','platinum','diamond') DEFAULT 'bronze',
  `nonce` varchar(32) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `is_banned` tinyint(1) DEFAULT 0,
  `my_referral_code` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `wallet_address`, `accountname`, `email`, `role`, `tier`, `nonce`, `is_verified`, `is_banned`, `my_referral_code`, `created_at`, `updated_at`) VALUES
(1, '6dygwo6jHPrExGKrohykhYoC1DkAA6CyPp9qDbhMe1JT', 'gardunydev', 'gardunydeveloper@gmail.com', 'seller', 'bronze', 'bd45660a3cd2a193c8f2578d9ff80752', 0, 0, 'c6a0bad7', '2026-01-16 04:03:30', '2026-04-18 00:48:53'),
(2, 'Di6...TEST_WALLET_2', 'CryptoKing', 'seller2@test.com', 'account', 'bronze', NULL, 0, 0, NULL, '2026-02-02 02:50:08', '2026-03-10 07:21:27'),
(3, 'Hmdv1Asp6uhvG9SCX64fdCX7wVkYZ792v5uavjEjACXb', 'gardunyguard', 'gardunyguard@gmail.com', 'account', 'bronze', 'b46799ba324cc88d29b1432bd1fabfdb', 0, 0, 'be443d4c', '2026-02-26 22:23:11', '2026-03-10 08:45:10');

-- --------------------------------------------------------

--
-- Table structure for table `account_quests`
--

CREATE TABLE `account_quests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `quest_id` int(10) UNSIGNED NOT NULL,
  `progress` int(11) DEFAULT 0,
  `is_claimed` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `account_referrals`
--

CREATE TABLE `account_referrals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `referrer_id` bigint(20) UNSIGNED NOT NULL,
  `referee_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `account_sessions`
--

CREATE TABLE `account_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `token` varchar(64) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `account_agent` varchar(255) DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_type` enum('admin','account','system') NOT NULL DEFAULT 'account',
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auctions`
--

CREATE TABLE `auctions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `option_id` int(11) DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `start_price_usd` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `reserve_price_usd` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `current_bid_usd` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `highest_bidder_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('pending','active','ended','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` int(10) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `position` varchar(50) DEFAULT 'home_slider',
  `sort_order` int(10) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `image_path`, `link_url`, `position`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, '/server/uploads/banners/6998ed0a652f69.13985581.jpg', '', 'home_slider', 1, 1, '2026-04-18 03:39:06', '2026-04-18 03:39:06'),
(2, '/server/uploads/banners/6998ee0b4f61a3.08594739.jpg', '', 'home_slider', 2, 1, '2026-04-18 03:39:06', '2026-04-18 03:39:06');

-- --------------------------------------------------------

--
-- Table structure for table `burn_log`
--

CREATE TABLE `burn_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(20,9) NOT NULL,
  `purpose` varchar(50) NOT NULL,
  `tx_signature` varchar(88) NOT NULL,
  `usd_value_at_burn` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `icon`, `is_active`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Gift Cards', 'gift-cards', '/server/uploads/categories/6998f28b3ac808.35555738.jfif', 1, '2026-04-18 03:38:22', '2026-04-18 03:38:22'),
(2, NULL, 'Gaming Assets', 'gaming-assets', '/server/uploads/categories/6998f290b1e954.78181829.jfif', 1, '2026-04-18 03:38:22', '2026-04-18 03:38:22'),
(3, NULL, 'Software Keys', 'software-keys', '/server/uploads/categories/6998f2979bb8d9.48209717.jpg', 1, '2026-04-18 03:38:22', '2026-04-18 03:38:22'),
(4, NULL, 'Premium NFTs', 'premium-nfts', '/server/uploads/categories/6998f29e37fed3.54323784.jfif', 1, '2026-04-18 03:38:22', '2026-04-18 03:38:22'),
(5, NULL, 'Mystery Boxes', 'mystery-boxes', '/server/uploads/categories/6998f2b8a3a877.37083302.jfif', 1, '2026-04-18 03:38:22', '2026-04-18 03:38:22'),
(6, NULL, 'custom', 'custom', '/server/uploads/categories/6998f3464935a9.84464338.png', 1, '2026-04-18 03:38:22', '2026-04-18 03:38:22');

-- --------------------------------------------------------

--
-- Table structure for table `gift_cards`
--

CREATE TABLE `gift_cards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `option_id` int(11) DEFAULT NULL,
  `gift_card_option_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code_enc` text NOT NULL,
  `pin_enc` text DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `is_sold` tinyint(1) DEFAULT 0,
  `sold_to_order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sold_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gift_card_options`
--

CREATE TABLE `gift_card_options` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price_usd` decimal(12,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lottery_entries`
--

CREATE TABLE `lottery_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `round_id` int(10) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `burn_tx` varchar(88) NOT NULL,
  `ticket_count` int(10) UNSIGNED DEFAULT 1,
  `is_winner` enum('no','yes') DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lottery_rounds`
--

CREATE TABLE `lottery_rounds` (
  `id` int(10) UNSIGNED NOT NULL,
  `round_number` int(10) UNSIGNED NOT NULL,
  `prize_pool` decimal(20,9) DEFAULT 0.000000000,
  `draw_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('open','drawing','closed') DEFAULT 'open',
  `winning_numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`winning_numbers`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mystery_box_loot`
--

CREATE TABLE `mystery_box_loot` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `box_product_id` bigint(20) UNSIGNED NOT NULL,
  `reward_product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reward_option_id` int(11) DEFAULT NULL,
  `reward_amount` decimal(20,9) DEFAULT 0.000000000,
  `probability` decimal(5,2) NOT NULL,
  `rarity` enum('common','rare','epic','legendary') DEFAULT 'common',
  `is_active` enum('yes','no') DEFAULT 'yes',
  `won_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nft_burns`
--

CREATE TABLE `nft_burns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mint_address` varchar(88) NOT NULL,
  `owner_account_id` bigint(20) UNSIGNED NOT NULL,
  `tx_signature` varchar(88) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nft_burn_campaigns`
--

CREATE TABLE `nft_burn_campaigns` (
  `id` int(10) UNSIGNED NOT NULL,
  `collection_address` varchar(100) NOT NULL COMMENT 'Can be a Solana address or internal drop ID',
  `name` varchar(100) NOT NULL,
  `reward_amount` decimal(20,9) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nft_burn_logs`
--

CREATE TABLE `nft_burn_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `nft_mint_address` varchar(100) NOT NULL,
  `campaign_id` int(10) UNSIGNED NOT NULL,
  `tx_signature` varchar(88) NOT NULL,
  `reward_paid` decimal(20,9) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nft_drops`
--

CREATE TABLE `nft_drops` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_account_id` bigint(20) UNSIGNED NOT NULL,
  `collection_name` varchar(100) NOT NULL,
  `symbol` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `price_gashy` decimal(20,9) NOT NULL,
  `max_supply` int(10) UNSIGNED NOT NULL,
  `minted_count` int(10) UNSIGNED DEFAULT 0,
  `royalties` int(5) DEFAULT 0,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `image_uri` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected','paused') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nft_mints`
--

CREATE TABLE `nft_mints` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `drop_id` bigint(20) UNSIGNED NOT NULL,
  `buyer_account_id` bigint(20) UNSIGNED NOT NULL,
  `mint_address` varchar(88) NOT NULL,
  `tx_signature` varchar(88) NOT NULL,
  `mint_price` decimal(20,9) NOT NULL,
  `is_burned` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `total_usd` decimal(12,2) DEFAULT NULL,
  `total_gashy` decimal(20,9) NOT NULL,
  `tx_signature` varchar(88) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','completed','refunded','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `option_id` int(11) DEFAULT NULL,
  `gift_card_option_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `price_usd_at_purchase` decimal(12,2) DEFAULT NULL,
  `price_at_purchase` decimal(20,9) NOT NULL,
  `meta_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta_data`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `group_name` varchar(50) DEFAULT 'general',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `slug`, `group_name`, `created_at`, `updated_at`) VALUES
(1, 'view users', 'view.users', 'users', '2026-04-18 03:36:11', '2026-04-18 03:36:11'),
(2, 'edit users', 'edit.users', 'users', '2026-04-18 03:36:11', '2026-04-18 03:36:11');

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

CREATE TABLE `permission_role` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `permission_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price_gashy` decimal(20,9) NOT NULL,
  `price_usd` decimal(12,2) DEFAULT NULL,
  `stock` int(10) UNSIGNED DEFAULT 0,
  `type` enum('gift_card','digital','nft','physical','mystery_box') NOT NULL,
  `has_options` tinyint(1) NOT NULL DEFAULT 0,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `attributes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attributes`)),
  `status` enum('active','inactive','banned') DEFAULT 'active',
  `views` bigint(20) UNSIGNED DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `seller_id`, `category_id`, `title`, `slug`, `description`, `price_gashy`, `price_usd`, `stock`, `type`, `has_options`, `images`, `attributes`, `status`, `views`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Apple Gift Card (US) – Instant Digital Delivery', 'apple-gift-card-us-instant-digital-delivery', 'Buy Apple Gift Card (US Region) with instant digital delivery.\r\nRedeem on App Store, iTunes, Apple Music, iCloud, and all Apple services.\r\n\r\n✔ Valid for US accounts only\r\n✔ Instant code delivery\r\n✔ Secure checkout\r\n✔ 100% digital product\r\n\r\nPerfect for apps, games, subscriptions, and gifting.', 5000000.000000000, 0.01, 10, 'gift_card', 0, '[\"/server/uploads/products/69a5855c6cd1a5.64859327.png\"]', NULL, 'active', 18, '2026-03-02 09:41:00', '2026-04-18 03:04:56'),
(2, 1, 1, 'PUBG Mobile UC – Instant Top Up', 'pubg-mobile-uc-instant-top-up', 'Top up your PUBG Mobile account instantly with UC credits.\r\n\r\n✔ Fast digital delivery\r\n✔ Works globally\r\n✔ Safe and secure\r\n✔ No waiting\r\n\r\nUpgrade skins, royale pass, and exclusive items instantly.', 3000000.000000000, 0.01, 10, 'gift_card', 0, '[\"/server/uploads/products/69a585f48b5666.51709512.png\"]', NULL, 'active', 10, '2026-03-02 09:43:32', '2026-04-18 03:04:56'),
(3, 1, 1, 'PlayStation Network (US)', 'playstation-network-us-', 'PlayStation Network Card (US) – Instant Code\r\n\r\nDescription:\r\n\r\nPurchase PSN US card for PlayStation Store credit.\r\n\r\n✔ Valid for US accounts\r\n✔ Instant digital code\r\n✔ Safe payment\r\n✔ Works on PS4 &amp;amp; PS5\r\n\r\nBuy games, subscriptions, DLC instantly.', 5000000.000000000, 0.01, 10, 'gift_card', 0, '[\"/server/uploads/products/69a58655494ed6.92950653.png\"]', NULL, 'active', 12, '2026-03-02 09:45:09', '2026-04-18 03:04:56'),
(4, 1, 1, 'Xbox Gift Card (US) – Instant Digital Code', 'xbox-gift-card-us-instant-digital-code', 'Buy Xbox US digital card instantly.\r\n\r\n✔ US region\r\n✔ Fast delivery\r\n✔ Redeem on Xbox &amp;amp; Microsoft Store\r\n✔ Secure checkout\r\n\r\nPerfect for games and subscriptions.', 5000000.000000000, 0.01, 10, 'gift_card', 0, '[\"/server/uploads/products/69a586c37780c2.59477988.png\"]', NULL, 'active', 14, '2026-03-02 09:46:59', '2026-04-18 03:04:56'),
(5, 1, 1, 'Fortnite V-Bucks Card (US) – Instant Delivery', 'fortnite-v-bucks-card-us-instant-delivery', 'Recharge your Fortnite account instantly.\r\n\r\n✔ US region\r\n✔ Digital code delivery\r\n✔ Safe &amp;amp; secure\r\n✔ Instant activation\r\n\r\nBuy skins, battle pass, and upgrades.', 5000000.000000000, 0.01, 10, 'gift_card', 0, '[\"/server/uploads/products/69a58717c899b2.87738555.png\"]', NULL, 'active', 12, '2026-03-02 09:48:23', '2026-04-18 03:04:56'),
(6, 1, 1, 'Free Fire Diamonds – Instant Top Up', 'free-fire-diamonds-instant-top-up', 'Buy Free Fire Diamonds instantly.\r\n\r\n✔ Fast delivery\r\n✔ Secure checkout\r\n✔ Global support\r\n\r\nUpgrade characters and skins instantly.', 5000000.000000000, 0.01, 10, 'gift_card', 0, '[\"/server/uploads/products/69a58785c7aea6.76989112.png\"]', NULL, 'active', 7, '2026-03-02 09:50:13', '2026-04-18 03:04:56'),
(7, 1, 1, 'Amazon Gift Card – Instant Code', 'amazon-gift-card-instant-code', 'Buy Amazon digital gift card instantly.\r\n\r\n✔ Digital delivery\r\n✔ Safe &amp;amp; secure\r\n✔ Use for millions of products\r\n\r\nPerfect for online shopping and gifting.', 5000000.000000000, 0.01, 10, 'gift_card', 0, '[\"/server/uploads/products/69a587d35f3c76.54561429.png\"]', NULL, 'active', 9, '2026-03-02 09:51:31', '2026-04-18 03:04:56'),
(8, 1, 1, 'Nintendo eShop Card (US) – Instant Delivery', 'nintendo-eshop-card-us-instant-delivery', 'Top up your Nintendo account instantly.\r\n\r\n✔ US region\r\n✔ Digital code\r\n✔ Fast activation\r\n\r\nBuy games and DLC easily.', 6500000.000000000, 0.01, 10, 'gift_card', 0, '[\"/server/uploads/products/69a588428700f9.57994480.png\"]', NULL, 'active', 11, '2026-03-02 09:53:22', '2026-04-18 03:04:56'),
(9, 1, 1, 'Minecraft Gift Code – Instant Delivery', 'minecraft-gift-code-instant-delivery', 'Purchase Minecraft digital code instantly.\r\n\r\n✔ Instant code\r\n✔ Safe payment\r\n✔ Works on supported platforms\r\n\r\nStart building today.', 7500000.000000000, 0.01, 10, 'gift_card', 0, '[\"/server/uploads/products/69a588a0a01e87.17403561.png\"]', NULL, 'active', 13, '2026-03-02 09:54:56', '2026-04-18 03:04:56'),
(10, 1, 1, 'Spotify Premium Gift Card (US)', 'spotify-premium-gift-card-us-', 'Activate Spotify Premium instantly.\r\n\r\n✔ US region\r\n✔ Instant digital code\r\n✔ Safe &amp;amp; secure\r\n\r\nEnjoy ad-free music.', 5000000.000000000, 0.01, 10, 'gift_card', 0, '[\"/server/uploads/products/69a588f83fa656.82082568.png\"]', NULL, 'active', 10, '2026-03-02 09:56:24', '2026-04-18 03:04:56'),
(11, 1, 1, 'eBay Gift Card – Instant Digital Code', 'ebay-gift-card-instant-digital-code', 'Shop on eBay using digital gift card.\r\n\r\n✔ Instant delivery\r\n✔ Secure checkout\r\n✔ Easy redemption\r\n\r\nPerfect for online shopping.', 5000000.000000000, 0.01, 10, 'gift_card', 0, '[\"/server/uploads/products/69a58937bc2b56.17375323.png\"]', NULL, 'active', 7, '2026-03-02 09:57:27', '2026-04-18 03:04:56'),
(12, 1, 3, 'McAfee Antivirus Digital Key – Instant Activation', 'mcafee-antivirus-digital-key-instant-activation', 'Protect your devices with McAfee.\r\n\r\n✔ Instant license key\r\n✔ Secure delivery\r\n✔ Digital activation\r\n\r\nReliable cybersecurity protection.', 100000000.000000000, 0.01, 10, 'digital', 0, '[\"/server/uploads/products/69a58a348bc994.26823379.png\"]', NULL, 'active', 6, '2026-03-02 10:01:40', '2026-04-18 03:04:56'),
(13, 1, 1, 'Likee Recharge – Instant Top Up', 'likee-recharge-instant-top-up', 'Recharge your Likee account instantly.\r\n\r\n✔ Fast digital delivery\r\n✔ Secure checkout\r\n✔ Instant activation\r\n\r\nBoost your presence.', 9750000.000000000, 0.01, 10, 'gift_card', 0, '[\"/server/uploads/products/69a58ab7b18058.07603675.png\"]', NULL, 'active', 8, '2026-03-02 10:03:51', '2026-04-18 03:04:56'),
(14, 1, 1, 'Bigo Live Diamonds – Instant Recharge', 'bigo-live-diamonds-instant-recharge', 'Recharge Bigo Live diamonds instantly.\r\n\r\n✔ Instant delivery\r\n✔ Secure payment\r\n✔ Safe checkout\r\n\r\nSupport your favorite creators.', 3750000.000000000, 0.01, 10, 'gift_card', 0, '[\"/server/uploads/products/69a58b599b2794.46398536.png\"]', NULL, 'active', 7, '2026-03-02 10:06:33', '2026-04-18 03:04:56'),
(15, 1, 1, 'GameStop Gift Card – Instant Digital Code', 'gamestop-gift-card-instant-digital-code', 'Purchase GameStop digital gift card instantly.\r\n\r\n✔ Instant email delivery\r\n✔ Secure checkout\r\n✔ Valid for online purchases\r\n✔ Perfect for gamers\r\n\r\nShop consoles, games, and accessories easily.', 8000000.000000000, 0.01, 10, 'gift_card', 0, '[\"/server/uploads/products/69a58b9654b0b3.65303702.png\"]', NULL, 'active', 11, '2026-03-02 10:07:34', '2026-04-18 03:04:56'),
(16, 1, 1, 'Twitch Gift Card (US) – Instant Code', 'twitch-gift-card-us-instant-code', 'Support your favorite streamers instantly.\r\n\r\n✔ US region\r\n✔ Instant digital delivery\r\n✔ Secure payment\r\n✔ Easy redemption\r\n\r\nBuy subscriptions and bits easily.', 6500000.000000000, 0.01, 10, 'gift_card', 0, '[\"/server/uploads/products/69a58bdf621e85.53753458.png\"]', NULL, 'active', 11, '2026-03-02 10:08:47', '2026-04-18 03:04:56'),
(17, 1, 1, 'Viber Out Credit – Instant Recharge', 'viber-out-credit-instant-recharge', 'Recharge your Viber account instantly.\r\n\r\n✔ Instant digital delivery\r\n✔ Secure checkout\r\n✔ Works for international calls\r\n\r\nStay connected worldwide.', 3500000.000000000, 0.01, 10, 'gift_card', 0, '[\"/server/uploads/products/69a58c1bd07f69.27198719.png\"]', NULL, 'active', 10, '2026-03-02 10:09:47', '2026-04-18 03:04:56'),
(18, 1, 1, 'Hulu Gift Card – Instant Activation', 'hulu-gift-card-instant-activation', 'Subscribe to Hulu instantly.\r\n\r\n✔ Digital delivery\r\n✔ Secure checkout\r\n✔ Fast activation\r\n\r\nStream your favorite shows.', 9500000.000000000, 0.01, 10, 'gift_card', 0, '[\"/server/uploads/products/69a58ca31398a7.97119963.png\"]', NULL, 'active', 7, '2026-03-02 10:12:03', '2026-04-18 03:04:56'),
(19, 1, 1, 'Apex Legends Coins – Instant Top Up', 'apex-legends-coins-instant-top-up', 'Recharge Apex Legends instantly.\r\n\r\n✔ Instant digital code\r\n✔ Secure checkout\r\n✔ Fast delivery\r\n\r\nUpgrade your battle pass and skins.', 10000000.000000000, 0.01, 0, 'gift_card', 0, '[\"/server/uploads/products/69a58cd6eb8206.23041110.png\"]', NULL, 'active', 11, '2026-03-02 10:12:54', '2026-04-18 03:04:56'),
(20, 1, 1, 'Kaspersky Antivirus Key – Instant Digital License', 'kaspersky-antivirus-key-instant-digital-license', 'Protect your devices with Kaspersky.\r\n\r\n✔ Instant license key\r\n✔ Digital delivery\r\n✔ Secure activation\r\n\r\nReliable cybersecurity protection.', 8000000.000000000, 0.01, 10, 'gift_card', 0, '[\"/server/uploads/products/69a58d0c7c7271.67805686.png\"]', NULL, 'active', 8, '2026-03-02 10:13:48', '2026-04-18 03:04:56'),
(21, 1, 1, 'Discord Nitro – Instant Activation', 'discord-nitro-instant-activation', 'Upgrade your Discord experience instantly.\r\n\r\n✔ Instant digital delivery\r\n✔ Secure checkout\r\n✔ Fast activation\r\n\r\nUnlock premium features.', 9500000.000000000, 0.01, 10, 'gift_card', 0, '[\"/server/uploads/products/69a58d50686747.69078619.png\"]', NULL, 'active', 14, '2026-03-02 10:14:56', '2026-04-18 03:04:56'),
(22, 1, 1, 'EA Play Membership – Instant Code', 'ea-play-membership-instant-code', 'Access premium EA games instantly.\r\n\r\n✔ Digital code delivery\r\n✔ Secure checkout\r\n✔ Fast activation\r\n\r\nPlay more, pay less.', 6750000.000000000, 0.01, 0, 'gift_card', 0, '[\"/server/uploads/products/69a58dd32dcce6.47397092.png\"]', NULL, 'active', 9, '2026-03-02 10:17:07', '2026-04-18 03:04:56'),
(23, 1, 1, 'Genshin Impact Genesis Crystals – Instant Top Up', 'genshin-impact-genesis-crystals-instant-top-up', 'Recharge your Genshin Impact account instantly.\r\n\r\n✔ Fast digital delivery\r\n✔ Secure checkout\r\n✔ Global support\r\n\r\nUnlock characters and weapons instantly.', 9500000.000000000, 0.01, 10, 'gift_card', 0, '[\"/server/uploads/products/69a58e677091c4.75512206.png\"]', NULL, 'active', 8, '2026-03-02 10:19:35', '2026-04-18 03:04:56'),
(24, 1, 1, 'Honor of Kings Tokens – Instant Top Up', 'honor-of-kings-tokens-instant-top-up', 'Recharge Honor of Kings instantly.\r\n\r\n✔ Digital delivery\r\n✔ Secure checkout\r\n✔ Fast activation\r\n\r\nUpgrade heroes and skins.', 11000000.000000000, 0.01, 0, 'gift_card', 0, '[\"/server/uploads/products/69a58ece44fc49.84632161.png\"]', NULL, 'active', 9, '2026-03-02 10:21:18', '2026-04-18 03:12:50'),
(25, 1, 1, 'Black Clover Mobile Diamonds – Instant Recharge', 'black-clover-mobile-diamonds-instant-recharge', 'Top up your Black Clover account instantly.\r\n\r\n✔ Instant digital delivery\r\n✔ Secure checkout\r\n✔ Fast activation\r\n\r\nUpgrade your magic squad.', 9000000.000000000, 0.01, 10, 'gift_card', 0, '[\"/server/uploads/products/69a58f1da24059.67091492.png\"]', NULL, 'active', 21, '2026-03-02 10:22:37', '2026-04-18 03:04:56'),
(26, 1, 1, 'Blizzard Gift Card (USA) – Instant Code', 'blizzard-gift-card-usa-instant-code', 'Recharge Blizzard balance instantly.\r\n\r\n✔ US region\r\n✔ Instant delivery\r\n✔ Secure checkout\r\n\r\nBuy games and in-game items easily.', 9000000.000000000, 0.01, 10, 'gift_card', 0, '[\"/server/uploads/products/69a58f6f94b0b9.42744468.png\"]', NULL, 'active', 19, '2026-03-02 10:23:59', '2026-04-18 03:04:56'),
(27, 1, 1, 'Roblox Gift Card (Region) – Instant Delivery', 'roblox-gift-card-region-instant-delivery', 'Recharge your Roblox account instantly.\r\n\r\n✔ Region-specific\r\n✔ Instant digital code\r\n✔ Secure checkout\r\n\r\nBuy Robux safely and quickly.', 9000000.000000000, 0.01, 10, 'gift_card', 0, '[\"/server/uploads/products/69a58fad188364.10679348.png\"]', NULL, 'active', 21, '2026-03-02 10:25:01', '2026-04-18 03:04:56'),
(28, 1, 1, 'Netflix Gift Card (US) – Instant Activation', 'netflix-gift-card-us-instant-activation', 'Subscribe to Netflix instantly.\r\n\r\n✔ US region\r\n✔ Digital delivery\r\n✔ Secure checkout\r\n\r\nStream movies and shows easily.', 150000000.000000000, 0.01, 1, 'gift_card', 0, '[\"/server/uploads/products/69a58fed6e2358.87588839.png\"]', NULL, 'active', 24, '2026-03-02 10:26:05', '2026-04-18 03:04:56'),
(29, 1, 1, 'Steam Gift Card (EUR) – Instant Code', 'steam-gift-card-eur-instant-code', 'Top up your Steam wallet instantly.\r\n\r\n✔ Europe region\r\n✔ Instant digital code\r\n✔ Secure checkout\r\n\r\nBuy games easily.', 5000000.000000000, 0.01, 10, 'gift_card', 0, '[\"/server/uploads/products/69a5902bc94e29.95996885.png\"]', NULL, 'active', 21, '2026-03-02 10:27:07', '2026-04-18 03:04:56'),
(30, 1, 1, 'Call of Duty Points (Xbox) – Instant Top Up', 'call-of-duty-points-xbox-instant-top-up', 'Recharge your Call of Duty account instantly.\r\n\r\n✔ Xbox compatible\r\n✔ Instant delivery\r\n✔ Secure checkout\r\n\r\nUnlock skins and battle pass.', 9000000.000000000, 0.01, 10, 'gift_card', 0, '[\"/server/uploads/products/69a590610b87c9.21373443.png\"]', NULL, 'active', 21, '2026-03-02 10:28:01', '2026-04-18 03:04:56'),
(31, 1, 1, 'Airbnb Gift Card – Instant Digital Code', 'airbnb-gift-card-instant-digital-code', 'Book your next stay instantly.\r\n\r\n✔ Digital delivery\r\n✔ Secure checkout\r\n✔ Easy redemption\r\n\r\nPerfect for travel.', 9500000.000000000, 0.01, 0, 'gift_card', 0, '[\"/server/uploads/products/69a590954edb26.77306889.png\"]', NULL, 'active', 20, '2026-03-02 10:28:53', '2026-04-18 03:04:56'),
(32, 1, 5, 'Gashy Mystery Box – Digital Surprise Drop', 'gashy-mystery-box-digital-surprise-drop', 'Unlock a surprise digital reward instantly.\r\n\r\nInside every Gashy Mystery Box, you receive one random digital product from our premium collection.\r\n\r\n✔ Instant digital delivery\r\n✔ Guaranteed value\r\n✔ Gaming, streaming, or shopping cards\r\n✔ Secure &amp;amp;amp;amp; fair system\r\n\r\nPossible rewards include:\r\n\r\n• Gaming credits (PUBG, Roblox, Steam, etc.)\r\n• Gift cards (Amazon, PlayStation, Xbox, etc.)\r\n• Streaming subscriptions\r\n• Premium digital services\r\n\r\nEach box contains equal or higher value than purchase price.\r\n\r\nBuy, open, and discover your reward instantly.', 15000000.000000000, 0.01, 9, 'mystery_box', 0, '[\"/server/uploads/products/69a591090f5012.54013300.png\"]', '{}', 'active', 38, '2026-03-02 10:30:49', '2026-04-18 03:04:56'),
(33, 1, 6, 'ups battery', 'ups-battery', 'ups battery  ups battery  ups battery ups battery  ups battery  ups battery ups battery  ups battery  ups battery ups battery  ups battery  ups battery', 0.000000000, 0.01, 2, 'physical', 0, '[\"/server/uploads/products/69e2101d84ecd1.09570801.jpg\"]', '{\"color\":[\"black\"],\"size\":[\"12inch\"]}', 'active', 26, '2026-04-17 10:49:01', '2026-04-18 03:04:56');

-- --------------------------------------------------------

--
-- Table structure for table `quests`
--

CREATE TABLE `quests` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `action_type` enum('login','buy','burn','refer') NOT NULL,
  `target_count` int(11) NOT NULL,
  `reward_gashy` decimal(20,9) NOT NULL,
  `reset_period` enum('daily','weekly','once') DEFAULT 'daily',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'super-admin', '2026-01-23 15:28:44', '2026-04-18 03:35:38'),
(2, 'simple', 'simple', '2026-01-23 16:22:54', '2026-04-18 03:35:38');

-- --------------------------------------------------------

--
-- Table structure for table `sellers`
--

CREATE TABLE `sellers` (
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `store_name` varchar(100) NOT NULL,
  `store_slug` varchar(100) NOT NULL,
  `commission_rate` decimal(5,2) DEFAULT 5.00,
  `rating` decimal(3,2) DEFAULT 0.00,
  `total_sales` int(10) UNSIGNED DEFAULT 0,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sellers`
--

INSERT INTO `sellers` (`account_id`, `store_name`, `store_slug`, `commission_rate`, `rating`, `total_sales`, `is_approved`, `created_at`, `updated_at`) VALUES
(1, 'Gashy Official Store', 'gashy-official', 5.00, 5.00, 0, 1, '2026-04-18 03:35:23', '2026-04-18 03:35:23');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `key_name` varchar(50) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key_name`, `value`, `created_at`, `updated_at`) VALUES
(1, 'site_title', 'Gashy Bazaar', '2026-04-18 03:35:10', '2026-04-18 03:35:10'),
(2, 'treasury_wallet', 'GS4tXdRS7CQ5PgePt795fK2oJe5q34XBhEugNNn5AVPb', '2026-04-18 03:35:10', '2026-04-18 03:35:10'),
(3, 'platform_fee', '5', '2026-04-18 03:35:10', '2026-04-18 03:35:10'),
(4, 'maintenance_mode', '0', '2026-04-18 03:35:10', '2026-04-18 03:35:10'),
(5, 'burn_address', '1nc1nerator11111111111111111111111111111111', '2026-04-18 03:35:10', '2026-04-18 03:35:10'),
(6, 'email', 'darinkrd2020@gmail.com', '2026-04-18 03:35:10', '2026-04-18 03:35:10'),
(7, 'token_address', 'DokPYQ33k3T9S7EEesvwvuuAtoQb4pY8NWszukKwXWjv', '2026-04-18 03:35:10', '2026-04-18 03:35:10'),
(8, 'heluis', '1fca693b-e96d-42f8-94b7-ab1c5bb0c9ed', '2026-04-18 03:35:10', '2026-04-18 03:35:10'),
(9, 'site_logo', '/server/uploads/setting/69e2e0e9aae237.18751928.png', '2026-04-18 03:35:10', '2026-04-18 03:35:10');

-- --------------------------------------------------------

--
-- Table structure for table `system_rate_limits`
--

CREATE TABLE `system_rate_limits` (
  `ip_address` varchar(45) NOT NULL,
  `endpoint` varchar(50) NOT NULL,
  `requests` int(10) UNSIGNED DEFAULT 1,
  `reset_time` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_rate_limits`
--

INSERT INTO `system_rate_limits` (`ip_address`, `endpoint`, `requests`, `reset_time`, `created_at`, `updated_at`) VALUES
('::1', 'auth_attempt', 1, 1776483276, '2026-04-18 03:34:57', '2026-04-18 03:34:57'),
('::1', 'global_api', 2, 1776483755, '2026-04-18 03:41:35', '2026-04-18 03:41:36');

-- --------------------------------------------------------

--
-- Table structure for table `tier_configs`
--

CREATE TABLE `tier_configs` (
  `tier` enum('bronze','silver','gold','platinum','diamond') NOT NULL,
  `required_gashy_held` decimal(20,9) NOT NULL,
  `discount_percent` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tier_configs`
--

INSERT INTO `tier_configs` (`tier`, `required_gashy_held`, `discount_percent`, `created_at`, `updated_at`) VALUES
('bronze', 0.000000000, 0.00, '2026-04-18 03:34:39', '2026-04-18 03:34:40'),
('silver', 1000.000000000, 2.00, '2026-04-18 03:34:39', '2026-04-18 03:34:40'),
('gold', 5000.000000000, 5.00, '2026-04-18 03:34:39', '2026-04-18 03:34:40'),
('platinum', 25000.000000000, 10.00, '2026-04-18 03:34:39', '2026-04-18 03:34:40'),
('diamond', 100000.000000000, 15.00, '2026-04-18 03:34:39', '2026-04-18 03:34:40');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('purchase','deposit','withdrawal','burn','auction_bid','reward','lottery_ticket') NOT NULL,
  `amount` decimal(20,9) NOT NULL,
  `tx_signature` varchar(88) DEFAULT NULL,
  `reference_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('pending','confirmed','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `username`, `email`, `password`, `avatar`, `is_active`, `otp_code`, `otp_expires`, `created_at`, `updated_at`) VALUES
(1, 1, 'garduny', 'gardunydeveloper@gmail.com', '$2y$10$4OZPo3/fyf/Im87BpGzKTe4IKeJi1eKn.AnyjScOl0uHKZxDCjGZu', '/server/uploads/users/69ad29109201b6.23697789.png', 1, NULL, NULL, '2026-01-23 15:28:44', '2026-04-18 03:32:58'),
(2, 1, 'shalaw', 'darinkrd2020@gmail.com', '$2y$10$TVnDIUB2wBP8RGmLr5Ze2O1O4C5i5.6YuXCVV5.3bGnGmeK.QnyvC', NULL, 1, NULL, NULL, '2026-01-23 16:21:13', '2026-01-31 00:54:32');

-- --------------------------------------------------------

--
-- Table structure for table `users_forget`
--

CREATE TABLE `users_forget` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(100) NOT NULL,
  `code` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `token` varchar(64) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_auctions_live`
-- (See below for the actual view)
--
CREATE TABLE `view_auctions_live` (
`id` bigint(20) unsigned
,`end_time` datetime
,`current_bid_usd` decimal(20,8)
,`status` enum('pending','active','ended','cancelled')
,`title` varchar(255)
,`images` longtext
,`product_slug` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_products_marketplace`
-- (See below for the actual view)
--
CREATE TABLE `view_products_marketplace` (
`id` bigint(20) unsigned
,`title` varchar(255)
,`slug` varchar(255)
,`price_gashy` decimal(20,9)
,`price_usd` decimal(12,2)
,`type` enum('gift_card','digital','nft','physical','mystery_box')
,`images` longtext
,`stock` int(10) unsigned
,`category_name` varchar(100)
,`category_slug` varchar(100)
,`store_name` varchar(100)
,`seller_rating` decimal(3,2)
,`is_approved` tinyint(1)
);

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE `withdrawals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(20,9) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `tx_signature` varchar(88) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure for view `view_auctions_live`
--
DROP TABLE IF EXISTS `view_auctions_live`;

CREATE VIEW `view_auctions_live`  AS SELECT `a`.`id` AS `id`, `a`.`end_time` AS `end_time`, `a`.`current_bid_usd` AS `current_bid_usd`, `a`.`status` AS `status`, `p`.`title` AS `title`, `p`.`images` AS `images`, `p`.`slug` AS `product_slug` FROM (`auctions` `a` join `products` `p` on(`a`.`product_id` = `p`.`id`)) WHERE `a`.`status` = 'active' AND `a`.`end_time` > current_timestamp() ;

-- --------------------------------------------------------

--
-- Structure for view `view_products_marketplace`
--
DROP TABLE IF EXISTS `view_products_marketplace`;

CREATE VIEW `view_products_marketplace`  AS SELECT `p`.`id` AS `id`, `p`.`title` AS `title`, `p`.`slug` AS `slug`, `p`.`price_gashy` AS `price_gashy`, `p`.`price_usd` AS `price_usd`, `p`.`type` AS `type`, `p`.`images` AS `images`, `p`.`stock` AS `stock`, `c`.`name` AS `category_name`, `c`.`slug` AS `category_slug`, `s`.`store_name` AS `store_name`, `s`.`rating` AS `seller_rating`, `s`.`is_approved` AS `is_approved` FROM ((`products` `p` join `categories` `c` on(`p`.`category_id` = `c`.`id`)) join `sellers` `s` on(`p`.`seller_id` = `s`.`account_id`)) WHERE `p`.`status` = 'active' AND `p`.`stock` > 0 AND `s`.`is_approved` = 1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wallet_address` (`wallet_address`),
  ADD UNIQUE KEY `accountname` (`accountname`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `account_quests`
--
ALTER TABLE `account_quests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `account_quest_unique` (`account_id`,`quest_id`),
  ADD KEY `quest_id` (`quest_id`);

--
-- Indexes for table `account_referrals`
--
ALTER TABLE `account_referrals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `referrer_id` (`referrer_id`),
  ADD KEY `referee_id` (`referee_id`);

--
-- Indexes for table `account_sessions`
--
ALTER TABLE `account_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `auctions`
--
ALTER TABLE `auctions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `status` (`status`),
  ADD KEY `end_time` (`end_time`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `burn_log`
--
ALTER TABLE `burn_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tx_signature` (`tx_signature`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `gift_cards`
--
ALTER TABLE `gift_cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `gift_card_option_id` (`gift_card_option_id`),
  ADD KEY `idx_gift_cards_option` (`gift_card_option_id`,`is_sold`);

--
-- Indexes for table `gift_card_options`
--
ALTER TABLE `gift_card_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `lottery_entries`
--
ALTER TABLE `lottery_entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `burn_tx` (`burn_tx`),
  ADD KEY `round_id` (`round_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `lottery_rounds`
--
ALTER TABLE `lottery_rounds`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mystery_box_loot`
--
ALTER TABLE `mystery_box_loot`
  ADD PRIMARY KEY (`id`),
  ADD KEY `box_product_id` (`box_product_id`),
  ADD KEY `reward_product_id` (`reward_product_id`),
  ADD KEY `reward_option_id` (`reward_option_id`);

--
-- Indexes for table `nft_burns`
--
ALTER TABLE `nft_burns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mint_address` (`mint_address`),
  ADD KEY `owner_account_id` (`owner_account_id`);

--
-- Indexes for table `nft_burn_campaigns`
--
ALTER TABLE `nft_burn_campaigns`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nft_burn_logs`
--
ALTER TABLE `nft_burn_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `nft_drops`
--
ALTER TABLE `nft_drops`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_account_id` (`seller_account_id`);

--
-- Indexes for table `nft_mints`
--
ALTER TABLE `nft_mints`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_mint` (`mint_address`),
  ADD KEY `drop_id` (`drop_id`),
  ADD KEY `buyer_account_id` (`buyer_account_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tx_signature` (`tx_signature`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `gift_card_option_id` (`gift_card_option_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `fk_pr_perm` (`permission_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `price_gashy` (`price_gashy`);

--
-- Indexes for table `quests`
--
ALTER TABLE `quests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `sellers`
--
ALTER TABLE `sellers`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `store_slug` (`store_slug`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key_name` (`key_name`);

--
-- Indexes for table `system_rate_limits`
--
ALTER TABLE `system_rate_limits`
  ADD PRIMARY KEY (`ip_address`,`endpoint`),
  ADD KEY `reset_time` (`reset_time`);

--
-- Indexes for table `tier_configs`
--
ALTER TABLE `tier_configs`
  ADD PRIMARY KEY (`tier`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `tx_signature` (`tx_signature`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `users_forget`
--
ALTER TABLE `users_forget`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `fk_us_user` (`user_id`);

--
-- Indexes for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `account_quests`
--
ALTER TABLE `account_quests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `account_referrals`
--
ALTER TABLE `account_referrals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `account_sessions`
--
ALTER TABLE `account_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auctions`
--
ALTER TABLE `auctions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `burn_log`
--
ALTER TABLE `burn_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `gift_cards`
--
ALTER TABLE `gift_cards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gift_card_options`
--
ALTER TABLE `gift_card_options`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lottery_entries`
--
ALTER TABLE `lottery_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lottery_rounds`
--
ALTER TABLE `lottery_rounds`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mystery_box_loot`
--
ALTER TABLE `mystery_box_loot`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nft_burns`
--
ALTER TABLE `nft_burns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nft_burn_campaigns`
--
ALTER TABLE `nft_burn_campaigns`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nft_burn_logs`
--
ALTER TABLE `nft_burn_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nft_drops`
--
ALTER TABLE `nft_drops`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nft_mints`
--
ALTER TABLE `nft_mints`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `quests`
--
ALTER TABLE `quests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users_forget`
--
ALTER TABLE `users_forget`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `account_quests`
--
ALTER TABLE `account_quests`
  ADD CONSTRAINT `account_quests_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `account_quests_ibfk_2` FOREIGN KEY (`quest_id`) REFERENCES `quests` (`id`);

--
-- Constraints for table `account_referrals`
--
ALTER TABLE `account_referrals`
  ADD CONSTRAINT `account_referrals_ibfk_1` FOREIGN KEY (`referrer_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `account_referrals_ibfk_2` FOREIGN KEY (`referee_id`) REFERENCES `accounts` (`id`);

--
-- Constraints for table `account_sessions`
--
ALTER TABLE `account_sessions`
  ADD CONSTRAINT `fk_sess_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `auctions`
--
ALTER TABLE `auctions`
  ADD CONSTRAINT `fk_auc_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_cat_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `gift_cards`
--
ALTER TABLE `gift_cards`
  ADD CONSTRAINT `fk_gc_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lottery_entries`
--
ALTER TABLE `lottery_entries`
  ADD CONSTRAINT `fk_lot_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `fk_lot_round` FOREIGN KEY (`round_id`) REFERENCES `lottery_rounds` (`id`);

--
-- Constraints for table `mystery_box_loot`
--
ALTER TABLE `mystery_box_loot`
  ADD CONSTRAINT `mystery_box_loot_ibfk_1` FOREIGN KEY (`box_product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `nft_burns`
--
ALTER TABLE `nft_burns`
  ADD CONSTRAINT `nft_burns_ibfk_1` FOREIGN KEY (`mint_address`) REFERENCES `nft_mints` (`mint_address`) ON DELETE CASCADE,
  ADD CONSTRAINT `nft_burns_ibfk_2` FOREIGN KEY (`owner_account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `nft_burn_logs`
--
ALTER TABLE `nft_burn_logs`
  ADD CONSTRAINT `fk_nbl_acc` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `nft_drops`
--
ALTER TABLE `nft_drops`
  ADD CONSTRAINT `nft_drops_ibfk_1` FOREIGN KEY (`seller_account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `nft_mints`
--
ALTER TABLE `nft_mints`
  ADD CONSTRAINT `nft_mints_ibfk_1` FOREIGN KEY (`drop_id`) REFERENCES `nft_drops` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `nft_mints_ibfk_2` FOREIGN KEY (`buyer_account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_ord_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_oi_ord` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_oi_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD CONSTRAINT `fk_pr_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pr_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_prod_cat` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `fk_prod_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`account_id`);

--
-- Constraints for table `sellers`
--
ALTER TABLE `sellers`
  ADD CONSTRAINT `fk_seller_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_tx_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `fk_us_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD CONSTRAINT `fk_wd_acc` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
