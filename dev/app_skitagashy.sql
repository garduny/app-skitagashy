-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 24, 2026 at 07:42 PM
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_banned` tinyint(1) DEFAULT 0,
  `my_referral_code` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `wallet_address`, `accountname`, `email`, `role`, `tier`, `nonce`, `is_verified`, `created_at`, `updated_at`, `is_banned`, `my_referral_code`) VALUES
(1, '6dygwo6jHPrExGKrohykhYoC1DkAA6CyPp9qDbhMe1JT', 'gardunydev', 'gardunydeveloper@gmail.com', 'admin', 'bronze', 'bd45660a3cd2a193c8f2578d9ff80752', 0, '2026-01-16 04:03:30', '2026-01-17 11:32:36', 0, 'c6a0bad7');

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

--
-- Dumping data for table `account_quests`
--

INSERT INTO `account_quests` (`id`, `account_id`, `quest_id`, `progress`, `is_claimed`, `updated_at`) VALUES
(1, 1, 1, 530, 0, '2026-01-18 02:29:05');

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
(1, 1, '09b5c68431752508d22fd3b66d30bc3243057436038de9690428662579bc626f', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-23 07:52:34', '2026-01-16 04:52:34'),
(2, 1, 'f32cca1e85d1ced4c1d0d92618e80a3aa418d4e2df090bf108bf788fc74dbc32', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-23 08:03:24', '2026-01-16 05:03:24'),
(3, 1, 'e19f2a8cf125ce0d75deeea205881a98a5139d4316f95d692487058e1432f818', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-24 14:31:55', '2026-01-17 11:31:55'),
(4, 1, 'f696b50cb1ad17d95a851e1d246efdf98e74b7f6eb19d7373960267f227c28de', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-24 15:10:05', '2026-01-17 12:10:05'),
(5, 1, 'a0f00f16cb411e6e4318249e24e71756f7852bc3d401a3b4bf32992c0e0b9c43', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-24 15:45:42', '2026-01-17 12:45:42'),
(6, 1, 'f6a570f367bbfdf3a9886e4c9720c82ea60ba8b3d5cbb1d278289019bccf08ec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-25 04:23:20', '2026-01-18 01:23:20'),
(7, 1, 'f89cbe0e19e0dc54ff93afdd36a7c70e0ca7bceefa29b71d4964ca22ae01b7d6', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-25 04:54:08', '2026-01-18 01:54:08'),
(8, 1, 'b47396259cb52710e64b729fb1096e5d05fc6f8786d7d15051ad394ccfc60d7d', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-25 04:54:35', '2026-01-18 01:54:35'),
(9, 1, '68db32bad8f47b3bbe12dc19963982042aa1aca4c7077db879a8735a982adcc5', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-25 05:33:37', '2026-01-18 02:33:37'),
(10, 1, 'faa04e34bcd1d06466cfe8fa4f104785d00e68275c7053c70638f43f604ab34b', '::1', 'Unknown', '2026-01-30 18:55:06', '2026-01-23 15:55:06');

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
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

--
-- Dumping data for table `auctions`
--

INSERT INTO `auctions` (`id`, `product_id`, `start_time`, `end_time`, `start_price`, `reserve_price`, `current_bid`, `highest_bidder_id`, `status`) VALUES
(1, 7, '2026-01-17 15:23:00', '2026-01-19 15:23:00', 5000.000000000, NULL, 5500.000000000, 1, 'active');

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

--
-- Dumping data for table `burn_log`
--

INSERT INTO `burn_log` (`id`, `account_id`, `amount`, `purpose`, `tx_signature`, `usd_value_at_burn`, `created_at`) VALUES
(1, 1, 0.000000000, 'lottery_entry', '495e42bb95d34f45bc5121ff5ee82977c1b17d281dadd03e9834568df8544262cfb4c9e88c5cf0d99eebe154', 0.00, '2026-01-17 12:30:32'),
(2, 1, 0.000000000, 'lottery_entry', 'f35e3d7f9fef319a191b63e039c28774072a1474edba76c2d8faa0a777096359df0838be3ceda29e29c473d3', 0.00, '2026-01-18 02:19:11'),
(3, 1, 0.000000000, 'lottery_entry', '21acef095ca724d146f13cbc4c99c3914eba7f07082d133908d4f46dd4d838137eec690eb685a732053e2dfb', 0.00, '2026-01-18 02:28:54');

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
(1, NULL, 'Gift Cards', 'gift-cards', 'fa-solid fa-gift', 1),
(2, NULL, 'Gaming Assets', 'gaming', 'fa-solid fa-gamepad', 1),
(3, NULL, 'Software Keys', 'software', 'fa-brands fa-windows', 1),
(4, NULL, 'Premium NFTs', 'nfts', 'fa-solid fa-gem', 1),
(5, NULL, 'Mystery Boxes', 'mystery', 'fa-solid fa-box-open', 1);

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

--
-- Dumping data for table `gift_cards`
--

INSERT INTO `gift_cards` (`id`, `product_id`, `code_enc`, `pin_enc`, `expiry_date`, `is_sold`, `sold_to_order_id`) VALUES
(1, 1, 'AMZN-TEST-CODE-1', NULL, NULL, 1, 4),
(2, 1, 'AMZN-TEST-CODE-2', NULL, NULL, 0, NULL),
(3, 2, 'STEAM-TEST-CODE-1', NULL, NULL, 0, NULL);

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

--
-- Dumping data for table `lottery_entries`
--

INSERT INTO `lottery_entries` (`id`, `round_id`, `account_id`, `burn_tx`, `ticket_count`) VALUES
(1, 1, 1, '495e42bb95d34f45bc5121ff5ee82977c1b17d281dadd03e9834568df8544262cfb4c9e88c5cf0d99eebe154', 1),
(2, 1, 1, 'f35e3d7f9fef319a191b63e039c28774072a1474edba76c2d8faa0a777096359df0838be3ceda29e29c473d3', 1),
(3, 1, 1, '21acef095ca724d146f13cbc4c99c3914eba7f07082d133908d4f46dd4d838137eec690eb685a732053e2dfb', 1);

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

--
-- Dumping data for table `lottery_rounds`
--

INSERT INTO `lottery_rounds` (`id`, `round_number`, `prize_pool`, `draw_time`, `status`, `winning_numbers`) VALUES
(1, 1, 100030.000000000, '2026-01-18 02:28:54', 'open', NULL);

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
(1, 6, NULL, 100.000000000, 0.000000000, 60.00, 'common'),
(2, 6, NULL, 1000.000000000, 0.000000000, 30.00, 'rare'),
(3, 6, 1, 0.000000000, 0.000000000, 9.00, 'epic'),
(4, 6, NULL, 50000.000000000, 0.000000000, 1.00, 'legendary'),
(5, 8, NULL, 5.000000000, 0.000000000, 50.00, 'common'),
(6, 8, NULL, 50.000000000, 0.000000000, 40.00, 'rare'),
(7, 8, NULL, 500.000000000, 0.000000000, 10.00, 'legendary');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `total_gashy` decimal(20,9) NOT NULL,
  `tx_signature` varchar(88) NOT NULL,
  `status` enum('pending','processing','completed','failed','refunded') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `account_id`, `total_gashy`, `tx_signature`, `status`, `created_at`) VALUES
(4, 1, 0.000000000, 'f7275f11ccb2cbdf7fb154cdbf37153499a4b6c41630662c288c00d434cd425d903c0ef12142c8576f0d6b6b', 'pending', '2026-01-17 12:19:13');

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

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price_at_purchase`, `meta_data`) VALUES
(1, 4, 1, 1, 1.000000000, NULL);

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

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `seller_id`, `category_id`, `title`, `slug`, `description`, `price_gashy`, `price_usd_peg`, `stock`, `type`, `images`, `attributes`, `status`, `views`, `created_at`) VALUES
(1, 1, 1, 'Amazon $50 Gift Card (US)', 'amazon-50-us', 'Valid for US accounts only. Code delivered instantly via email and order dashboard upon blockchain confirmation.', 1.000000000, 0.01, 97, 'gift_card', '[\"https://upload.wikimedia.org/wikipedia/commons/thumb/d/de/Amazon_icon.png/1024px-Amazon_icon.png\"]', NULL, 'active', 466, '2026-01-16 08:40:08'),
(2, 1, 1, 'Steam Wallet $20 Global', 'steam-20-global', 'Add funds to your Steam Wallet. Works globally. Instant delivery.', 480.000000000, 20.00, 50, 'gift_card', '[\"https://upload.wikimedia.org/wikipedia/commons/thumb/8/83/Steam_icon_logo.svg/2048px-Steam_icon_logo.svg.png\"]', NULL, 'active', 898, '2026-01-16 08:40:08'),
(3, 1, 2, 'PUBG Mobile 660 UC', 'pubg-660-uc', 'Redeemable code for PUBG Mobile. Get skins and upgrades instantly.', 240.000000000, 10.00, 200, 'digital', '[\"https://w7.pngwing.com/pngs/380/764/png-transparent-pubg-mobile-playerunknown-s-battlegrounds-logo-game-t-shirt-pubg-mobile-logo-game-text-logo-thumbnail.png\"]', NULL, 'active', 121, '2026-01-16 08:40:08'),
(4, 1, 3, 'Windows 11 Pro License', 'win-11-pro', 'Lifetime retail key for Windows 11 Pro. Supports multi-language installation.', 600.000000000, 25.00, 15, 'digital', '[\"https://upload.wikimedia.org/wikipedia/commons/thumb/e/e6/Windows_11_logo.svg/2048px-Windows_11_logo.svg.png\"]', NULL, 'active', 66, '2026-01-16 08:40:08'),
(5, 1, 4, 'CyberPunk Samurai #042', 'cyber-samurai-042', 'Rare NFT from the CyberPunk collection. Verified ownership on Solana.', 5000.000000000, 250.00, 1, 'nft', '[\"https://images.unsplash.com/photo-1620641788421-7a1c342ea42e?q=80&w=1974&auto=format&fit=crop\"]', NULL, 'active', 1203, '2026-01-16 08:40:08'),
(6, 1, 5, 'Legendary Mystery Box', 'legendary-box', 'Contains a chance to win 50,000 GASHY or a Rare NFT. High risk, high reward.', 500.000000000, 20.00, 994, 'mystery_box', '[\"https://cdn-icons-png.flaticon.com/512/1162/1162951.png\"]', NULL, 'active', 3001, '2026-01-16 08:40:08'),
(7, 1, 4, 'Bored Ape #9999 (Test)', 'bored-ape-test', 'Original BAYC NFT. Verified on Ethereum.', 10000.000000000, NULL, 1, 'nft', '[\"https://img.seadn.io/files/87722776263889657062473859663749.png?auto=format&fit=max&w=384\"]', NULL, 'active', 0, '2026-01-17 12:23:19'),
(8, 1, 5, 'Starter Mystery Box', 'starter-box', 'Cheap box for testing luck.', 10.000000000, NULL, 498, 'mystery_box', '[\"https://cdn-icons-png.flaticon.com/512/4213/4213650.png\"]', NULL, 'active', 7, '2026-01-17 12:23:19');

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

--
-- Dumping data for table `quests`
--

INSERT INTO `quests` (`id`, `title`, `action_type`, `target_count`, `reward_gashy`, `reset_period`, `is_active`) VALUES
(1, 'Burn 500 $GASHY', 'burn', 500, 50.000000000, 'weekly', 1);

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
(1, 'Gashy Official Store', 'gashy-official', 5.00, 5.00, 1500, 1);

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
(5, 'burn_address', '1nc1nerator11111111111111111111111111111111');

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
  `type` enum('purchase','deposit','withdrawal','burn','auction_bid','reward') NOT NULL,
  `amount` decimal(20,9) NOT NULL,
  `tx_signature` varchar(88) DEFAULT NULL,
  `reference_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('pending','confirmed','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `account_id`, `type`, `amount`, `tx_signature`, `reference_id`, `status`, `created_at`) VALUES
(1, 1, 'purchase', 0.000000000, 'f7275f11ccb2cbdf7fb154cdbf37153499a4b6c41630662c288c00d434cd425d903c0ef12142c8576f0d6b6b', NULL, 'pending', '2026-01-17 12:19:13'),
(2, 1, 'purchase', 10.000000000, '108b1ed1167f1e1b0a842f137853c5b0f232f89573520932f688001bc0912b54d1460cf96f9de189454267b7', NULL, 'confirmed', '2026-01-17 12:24:10'),
(3, 1, 'reward', 5.000000000, NULL, 8, 'confirmed', '2026-01-17 12:24:10'),
(4, 1, 'auction_bid', 5500.000000000, NULL, 1, 'pending', '2026-01-17 12:33:10'),
(5, 1, 'purchase', 10.000000000, '108b1ed1167f1e1b0a842f137853c5b0f232f89573520932f688001bc0912b54d1460cf96f9de189454267b7', NULL, 'confirmed', '2026-01-18 02:08:14'),
(6, 1, 'reward', 5.000000000, NULL, 8, 'confirmed', '2026-01-18 02:08:14'),
(7, 1, 'purchase', 500.000000000, 'cb9d6381f5d598118138ca7f31a7c122f0800d3410d325b76c608f413fc4e9bb13100b5c55a1f26a2e38d251', NULL, 'confirmed', '2026-01-18 02:29:05'),
(8, 1, 'reward', 1000.000000000, NULL, 6, 'confirmed', '2026-01-18 02:29:05');

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `username`, `email`, `password`, `avatar`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'admin', 'admin@gashy.com', '$2y$10$lpkvg3.1MbV46.BUD0iUw.kJJ.bFDB3fcKTRqUJPTOfeJOxufWEqu', 'https://skita.io/img/logo.png', 1, '2026-01-23 15:28:44', '2026-01-24 18:30:35'),
(2, 1, 'test', 'test@gmail.com', '$2y$10$KoNsaVFGcy4Oc22u3JZ7K.qLztmCp7vnfcywoDTIDHCRroDezaCfm', NULL, 1, '2026-01-23 16:21:13', '2026-01-23 16:21:13');

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

--
-- Dumping data for table `user_sessions`
--

INSERT INTO `user_sessions` (`id`, `user_id`, `token`, `ip_address`, `user_agent`, `expires_at`, `created_at`) VALUES
(1, 1, '5dc464b3bc5f29cd657fccf4c73f042909002c844e048478eaafea5da7c064ef', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-22 20:55:25', '2026-01-23 17:55:25');

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `account_referrals`
--
ALTER TABLE `account_referrals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `account_sessions`
--
ALTER TABLE `account_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auctions`
--
ALTER TABLE `auctions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `burn_log`
--
ALTER TABLE `burn_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `gift_cards`
--
ALTER TABLE `gift_cards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lottery_entries`
--
ALTER TABLE `lottery_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lottery_rounds`
--
ALTER TABLE `lottery_rounds`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mystery_box_loot`
--
ALTER TABLE `mystery_box_loot`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `quests`
--
ALTER TABLE `quests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
