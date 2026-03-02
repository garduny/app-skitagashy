-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 02, 2026 at 05:45 AM
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
-- Database: `gashybazaar_gashy`
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_banned` tinyint(1) DEFAULT 0,
  `my_referral_code` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `wallet_address`, `accountname`, `email`, `role`, `tier`, `nonce`, `is_verified`, `created_at`, `updated_at`, `is_banned`, `my_referral_code`) VALUES
(1, '6dygwo6jHPrExGKrohykhYoC1DkAA6CyPp9qDbhMe1JT', NULL, NULL, 'account', 'bronze', '7d5cd082c28b9037f77eaf126d767755', 0, '2026-03-02 04:44:52', '2026-03-02 04:44:52', 0, '7cea660e');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account_sessions`
--

INSERT INTO `account_sessions` (`id`, `account_id`, `token`, `ip_address`, `account_agent`, `expires_at`, `created_at`) VALUES
(1, 1, '85a6cdd47627d733a8092b56ce0f9d03c36a66ff9687cf960e4f1193629efa51', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 07:44:52', '2026-03-02 04:44:52');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auctions`
--

CREATE TABLE `auctions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `start_price` decimal(20,9) NOT NULL,
  `reserve_price` decimal(20,9) DEFAULT NULL,
  `current_bid` decimal(20,9) DEFAULT 0.000000000,
  `highest_bidder_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('pending','active','ended','cancelled') DEFAULT 'pending'
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
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `image_path`, `link_url`, `position`, `sort_order`, `is_active`) VALUES
(1, '/server/uploads/banners/6998ed0a652f69.13985581.jpg', '', 'home_slider', 1, 1),
(2, '/server/uploads/banners/6998ee0b4f61a3.08594739.jpg', '', 'home_slider', 2, 1);

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
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
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `icon`, `is_active`) VALUES
(1, NULL, 'Gift Cards', 'gift-cards', '/server/uploads/categories/6998f28b3ac808.35555738.jfif', 1),
(2, NULL, 'Gaming Assets', 'gaming-assets', '/server/uploads/categories/6998f290b1e954.78181829.jfif', 1),
(3, NULL, 'Software Keys', 'software-keys', '/server/uploads/categories/6998f2979bb8d9.48209717.jpg', 1),
(4, NULL, 'Premium NFTs', 'premium-nfts', '/server/uploads/categories/6998f29e37fed3.54323784.jfif', 1),
(5, NULL, 'Mystery Boxes', 'mystery-boxes', '/server/uploads/categories/6998f2b8a3a877.37083302.jfif', 1),
(6, NULL, 'custom', 'custom', '/server/uploads/categories/6998f3464935a9.84464338.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `gift_cards`
--

CREATE TABLE `gift_cards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `code_enc` text NOT NULL,
  `pin_enc` text DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `is_sold` tinyint(1) DEFAULT 0,
  `sold_to_order_id` bigint(20) UNSIGNED DEFAULT NULL
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
  `ticket_count` int(10) UNSIGNED DEFAULT 1
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
  `winning_numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`winning_numbers`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mystery_box_loot`
--

CREATE TABLE `mystery_box_loot` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `box_product_id` bigint(20) UNSIGNED NOT NULL,
  `reward_product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reward_amount` decimal(20,9) DEFAULT 0.000000000,
  `reward_gashy_amount` decimal(20,9) DEFAULT 0.000000000,
  `probability` decimal(5,2) NOT NULL,
  `rarity` enum('common','rare','epic','legendary') DEFAULT 'common'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mystery_box_loot`
--

INSERT INTO `mystery_box_loot` (`id`, `box_product_id`, `reward_product_id`, `reward_amount`, `reward_gashy_amount`, `probability`, `rarity`) VALUES
(1, 6, NULL, 100.000000000, 0.000000000, 40.00, 'legendary'),
(2, 8, NULL, 10.000000000, 0.000000000, 80.00, 'common'),
(3, 8, NULL, 10.000000000, 0.000000000, 80.00, 'common'),
(4, 6, 12, 0.000000000, 0.000000000, 99.00, 'common'),
(5, 20, NULL, 60.000000000, 0.000000000, 80.00, 'common'),
(6, 20, NULL, 150.000000000, 0.000000000, 30.00, 'legendary');

-- --------------------------------------------------------

--
-- Table structure for table `nft_burns`
--

CREATE TABLE `nft_burns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mint_address` varchar(88) NOT NULL,
  `owner_account_id` bigint(20) UNSIGNED NOT NULL,
  `tx_signature` varchar(88) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
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
  `is_active` tinyint(1) DEFAULT 1
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `total_gashy` decimal(20,9) NOT NULL,
  `tx_signature` varchar(88) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','completed','refunded','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `price_at_purchase` decimal(20,9) NOT NULL,
  `meta_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta_data`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `group_name` varchar(50) DEFAULT 'general'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `slug`, `group_name`) VALUES
(1, 'view users', 'view.users', 'users'),
(2, 'edit users', 'edit.users', 'users');

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

CREATE TABLE `permission_role` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `permission_id` int(10) UNSIGNED NOT NULL
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
  `price_usd_peg` decimal(10,2) DEFAULT NULL,
  `stock` int(10) UNSIGNED DEFAULT 0,
  `type` enum('gift_card','digital','nft','physical','mystery_box') NOT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `attributes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attributes`)),
  `status` enum('active','inactive','banned') DEFAULT 'active',
  `views` bigint(20) UNSIGNED DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'Super Admin', 'super-admin', '2026-01-23 15:28:44'),
(2, 'simple', 'simple', '2026-01-23 16:22:54');

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
  `is_approved` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sellers`
--

INSERT INTO `sellers` (`account_id`, `store_name`, `store_slug`, `commission_rate`, `rating`, `total_sales`, `is_approved`) VALUES
(1, 'Gashy Official Store', 'gashy-official', 5.00, 5.00, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `key_name` varchar(50) NOT NULL,
  `value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key_name`, `value`) VALUES
(1, 'site_title', 'Gashy Bazaar'),
(2, 'treasury_wallet', 'GS4tXdRS7CQ5PgePt795fK2oJe5q34XBhEugNNn5AVPb'),
(3, 'platform_fee', '5'),
(4, 'maintenance_mode', '0'),
(5, 'burn_address', '1nc1nerator11111111111111111111111111111111'),
(6, 'email', 'darinkrd2020@gmail.com'),
(7, 'logo', 'https://api.phantom.app/image-proxy/?image=https%3A%2F%2Fcoin-images.coingecko.com%2Fcoins%2Fimages%2F69906%2Flarge%2FUntitled_design_%25282%2529.png%3F1767493770&amp;anim=false&amp;fit=cover&amp;width=128&amp;height=128'),
(8, 'token_address', 'DokPYQ33k3T9S7EEesvwvuuAtoQb4pY8NWszukKwXWjv'),
(9, 'heluis', '1fca693b-e96d-42f8-94b7-ab1c5bb0c9ed');

-- --------------------------------------------------------

--
-- Table structure for table `system_rate_limits`
--

CREATE TABLE `system_rate_limits` (
  `ip_address` varchar(45) NOT NULL,
  `endpoint` varchar(50) NOT NULL,
  `requests` int(10) UNSIGNED DEFAULT 1,
  `reset_time` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_rate_limits`
--

INSERT INTO `system_rate_limits` (`ip_address`, `endpoint`, `requests`, `reset_time`) VALUES
('::1', 'auth_attempt', 1, 1772426752),
('::1', 'global_api', 4, 1772426752);

-- --------------------------------------------------------

--
-- Table structure for table `tier_configs`
--

CREATE TABLE `tier_configs` (
  `tier` enum('bronze','silver','gold','platinum','diamond') NOT NULL,
  `required_gashy_held` decimal(20,9) NOT NULL,
  `discount_percent` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tier_configs`
--

INSERT INTO `tier_configs` (`tier`, `required_gashy_held`, `discount_percent`) VALUES
('bronze', 0.000000000, 0.00),
('silver', 1000.000000000, 2.00),
('gold', 5000.000000000, 5.00),
('platinum', 25000.000000000, 10.00),
('diamond', 100000.000000000, 15.00);

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `username`, `email`, `password`, `avatar`, `is_active`, `created_at`, `updated_at`, `otp_code`, `otp_expires`) VALUES
(1, 1, 'garduny', 'gardunydeveloper@gmail.com', '$2y$10$4OZPo3/fyf/Im87BpGzKTe4IKeJi1eKn.AnyjScOl0uHKZxDCjGZu', 'https://api.phantom.app/image-proxy/?image=https%3A%2F%2Fcoin-images.coingecko.com%2Fcoins%2Fimages%2F69906%2Flarge%2FUntitled_design_%25282%2529.png%3F1767493770&amp;anim=false&amp;fit=cover&amp;width=128&amp;height=128', 1, '2026-01-23 15:28:44', '2026-02-27 15:20:55', NULL, NULL),
(2, 1, 'shalaw', 'darinkrd2020@gmail.com', '$2y$10$TVnDIUB2wBP8RGmLr5Ze2O1O4C5i5.6YuXCVV5.3bGnGmeK.QnyvC', NULL, 1, '2026-01-23 16:21:13', '2026-01-31 00:54:32', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_forget`
--

CREATE TABLE `users_forget` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(100) NOT NULL,
  `code` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_auctions_live`
-- (See below for the actual view)
--
CREATE TABLE `view_auctions_live` (
`id` bigint(20) unsigned
,`end_time` datetime
,`current_bid` decimal(20,9)
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure for view `view_auctions_live`
--
DROP TABLE IF EXISTS `view_auctions_live`;

CREATE VIEW `view_auctions_live`  AS SELECT `a`.`id` AS `id`, `a`.`end_time` AS `end_time`, `a`.`current_bid` AS `current_bid`, `a`.`status` AS `status`, `p`.`title` AS `title`, `p`.`images` AS `images`, `p`.`slug` AS `product_slug` FROM (`auctions` `a` join `products` `p` on(`a`.`product_id` = `p`.`id`)) WHERE `a`.`status` = 'active' AND `a`.`end_time` > current_timestamp() ;

-- --------------------------------------------------------

--
-- Structure for view `view_products_marketplace`
--
DROP TABLE IF EXISTS `view_products_marketplace`;

CREATE VIEW `view_products_marketplace`  AS SELECT `p`.`id` AS `id`, `p`.`title` AS `title`, `p`.`slug` AS `slug`, `p`.`price_gashy` AS `price_gashy`, `p`.`type` AS `type`, `p`.`images` AS `images`, `p`.`stock` AS `stock`, `c`.`name` AS `category_name`, `c`.`slug` AS `category_slug`, `s`.`store_name` AS `store_name`, `s`.`rating` AS `seller_rating`, `s`.`is_approved` AS `is_approved` FROM ((`products` `p` join `categories` `c` on(`p`.`category_id` = `c`.`id`)) join `sellers` `s` on(`p`.`seller_id` = `s`.`account_id`)) WHERE `p`.`status` = 'active' AND `p`.`stock` > 0 AND `s`.`is_approved` = 1 ;

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
  ADD KEY `box_product_id` (`box_product_id`);

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
  ADD KEY `product_id` (`product_id`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
