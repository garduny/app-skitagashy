-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 29, 2026 at 04:05 AM
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
(10, 1, 'faa04e34bcd1d06466cfe8fa4f104785d00e68275c7053c70638f43f604ab34b', '::1', 'Unknown', '2026-01-30 18:55:06', '2026-01-23 15:55:06'),
(11, 1, 'b545337b3a8611be1bf10639de73d1229349d107f645a45fa6edcd30b73f5d02', '::1', 'Unknown', '2026-02-04 05:38:34', '2026-01-28 02:38:34'),
(12, 1, '752034e25508bcdd2a5ef7b32f1cddc38fe27f16bb6cc69781404a84159c16d2', '::1', 'Unknown', '2026-02-05 03:09:39', '2026-01-29 00:09:39'),
(13, 1, 'c033ddc4919e68566d9d22c3fdd816106e2ae97e41164283d52aebc5e5fc9a3b', '::1', 'Unknown', '2026-02-05 03:09:41', '2026-01-29 00:09:41'),
(14, 1, 'cc6bddd17d3d9430b17314edd3390cec7a4a32e2009e8eb91c3b42ba8d6c0181', '::1', 'Unknown', '2026-02-05 03:10:05', '2026-01-29 00:10:05'),
(15, 1, '4545551f8e70a14428baceed599f08d22db32ba0590a7f9001b88432ef644710', '::1', 'Unknown', '2026-02-05 03:10:06', '2026-01-29 00:10:06'),
(16, 1, '248914ce148a61c22851267cdf64b8964a9175ce5344d1c03ed999779feced56', '::1', 'Unknown', '2026-02-05 04:33:49', '2026-01-29 01:33:49'),
(17, 1, '374f125375068c84e849f7ee8cd19446b3588123ef2a750656218c9046196182', '::1', 'Unknown', '2026-02-05 04:41:18', '2026-01-29 01:41:18'),
(18, 1, '1250ccd7825eef71b182bd85c293f491e6366f96d35ec73734d697b7c606dbaf', '::1', 'Unknown', '2026-02-05 04:42:00', '2026-01-29 01:42:00'),
(19, 1, '5797676ce91cdf5456e85d9fec53e56a19d899a0d71a26b193a59c9960339043', '::1', 'Unknown', '2026-02-05 04:42:02', '2026-01-29 01:42:02');

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
(1, 7, '2026-01-17 15:23:00', '2026-01-19 15:23:00', 5000.000000000, NULL, 5500.000000000, 1, 'ended'),
(2, 2, '2026-01-28 05:39:00', '2026-01-29 05:39:00', 10.000000000, NULL, 10.000000000, NULL, 'active');

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
(1, NULL, 'Gift Cards', 'gift-cards', 'fa-brands fa-windows', 1),
(2, NULL, 'Gaming Assets', 'gaming-assets', 'fa-solid fa-gamepad', 1),
(3, NULL, 'Software Keys', 'software', 'fa-brands fa-windows', 1),
(4, NULL, 'Premium NFTs', 'premium-nfts', 'fa-solid fa-gem', 1),
(5, NULL, 'Mystery Boxes', 'mystery-boxes', 'fa-solid fa-box-open', 1),
(6, NULL, 'a', 'a', 'a', 1);

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
(1, 1, 100030.000000000, '2026-01-29 02:11:00', 'closed', '{\"winner_id\":1,\"amount\":100030.000000000}'),
(2, 2, 0.000000000, '2026-02-05 02:11:00', 'open', NULL);

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
(4, 1, 0.000000000, 'f7275f11ccb2cbdf7fb154cdbf37153499a4b6c41630662c288c00d434cd425d903c0ef12142c8576f0d6b6b', 'pending', '2026-01-17 12:19:13'),
(5, 1, 0.000000000, '280d43bb9c8a75828780f0705982efd49a502b23ac26787b894bdd0cf30c6ef9c03ab3cff9bbece2339f6b7d', 'pending', '2026-01-28 02:38:48'),
(6, 1, 5500.000000000, 'AUC_WIN_1_1769652738', 'completed', '2026-01-29 02:12:18');

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
(1, 4, 1, 1, 1.000000000, NULL),
(2, 5, 8, 1, 10.000000000, NULL),
(3, 6, 7, 1, 5500.000000000, NULL);

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
(1, 1, 1, 'Amazon $50 Gift Card (US)', 'amazon-50-us', 'Valid for US accounts only. Code delivered instantly via email and order dashboard upon blockchain confirmation.', 1.000000000, 0.01, 97, 'gift_card', '[\"https://upload.wikimedia.org/wikipedia/commons/thumb/d/de/Amazon_icon.png/1024px-Amazon_icon.png\"]', NULL, 'active', 467, '2026-01-16 08:40:08'),
(2, 1, 1, 'Steam Wallet $20 Global', 'steam-20-global', 'Add funds to your Steam Wallet. Works globally. Instant delivery.', 480.000000000, 20.00, 50, 'gift_card', '[\"https://upload.wikimedia.org/wikipedia/commons/thumb/8/83/Steam_icon_logo.svg/2048px-Steam_icon_logo.svg.png\"]', NULL, 'active', 899, '2026-01-16 08:40:08'),
(3, 1, 2, 'PUBG Mobile 660 UC', 'pubg-660-uc', 'Redeemable code for PUBG Mobile. Get skins and upgrades instantly.', 240.000000000, 10.00, 200, 'digital', '[\"https://w7.pngwing.com/pngs/380/764/png-transparent-pubg-mobile-playerunknown-s-battlegrounds-logo-game-t-shirt-pubg-mobile-logo-game-text-logo-thumbnail.png\"]', NULL, 'active', 121, '2026-01-16 08:40:08'),
(4, 1, 3, 'Windows 11 Pro License', 'win-11-pro', 'Lifetime retail key for Windows 11 Pro. Supports multi-language installation.', 600.000000000, 25.00, 15, 'digital', '[\"https://upload.wikimedia.org/wikipedia/commons/thumb/e/e6/Windows_11_logo.svg/2048px-Windows_11_logo.svg.png\"]', NULL, 'active', 66, '2026-01-16 08:40:08'),
(5, 1, 4, 'CyberPunk Samurai #042', 'cyber-samurai-042', 'Rare NFT from the CyberPunk collection. Verified ownership on Solana.', 5000.000000000, 250.00, 1, 'nft', '[\"https://images.unsplash.com/photo-1620641788421-7a1c342ea42e?q=80&w=1974&auto=format&fit=crop\"]', NULL, 'active', 1204, '2026-01-16 08:40:08'),
(6, 1, 5, 'Legendary Mystery Box', 'legendary-box', 'Contains a chance to win 50,000 GASHY or a Rare NFT. High risk, high reward.', 500.000000000, 20.00, 994, 'mystery_box', '[\"https://cdn-icons-png.flaticon.com/512/1162/1162951.png\"]', NULL, 'active', 3001, '2026-01-16 08:40:08'),
(7, 1, 4, 'Bored Ape #9999 (Test)', 'bored-ape-test', 'Original BAYC NFT. Verified on Ethereum.', 10000.000000000, NULL, 1, 'nft', '[\"https://img.seadn.io/files/87722776263889657062473859663749.png?auto=format&fit=max&w=384\"]', NULL, 'inactive', 0, '2026-01-17 12:23:19'),
(8, 1, 6, 'Starter Mystery Box', 'starter-box', '', 10.000000000, NULL, 497, '', '[\"https://cdn-icons-png.flaticon.com/512/4213/4213650.png\"]', NULL, 'active', 10, '2026-01-17 12:23:19'),
(9, 1, 1, 'GTA-6', 'test-873', 'undefined', 10.000000000, NULL, 5, 'digital', '[\"data:image/jpeg;base64,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\"]', NULL, 'active', 1, '2026-01-29 01:53:02'),
(10, 1, 6, 'aaa', 'aaa-633', '.', 1.000000000, NULL, 1, 'digital', '[\".\"]', NULL, 'inactive', 0, '2026-01-29 01:53:43');

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
(8, 1, 'reward', 1000.000000000, NULL, 6, 'confirmed', '2026-01-18 02:29:05'),
(9, 1, 'purchase', 0.000000000, '280d43bb9c8a75828780f0705982efd49a502b23ac26787b894bdd0cf30c6ef9c03ab3cff9bbece2339f6b7d', NULL, 'pending', '2026-01-28 02:38:48'),
(10, 1, 'reward', 100030.000000000, NULL, 1, 'confirmed', '2026-01-29 02:11:00');

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
(1, 1, 'admin', 'admin@gashy.com', '$2y$10$npxUdczn1lsjuUg1r4Rk/ek7LUMcpQ.A5sG38MtBnymCnSbK84ryu', 'https://skita.io/img/logo.png', 1, '2026-01-23 15:28:44', '2026-01-28 02:32:40'),
(2, 1, 'test', 'test@gmail.com', '$2y$10$53tAtHb3aJ8gIrHLKwR.uuzizSb0uok8ZjIS5oiVXGL7LL4f/.V8u', NULL, 1, '2026-01-23 16:21:13', '2026-01-28 02:32:37');

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
(1, 1, '44e2d2cb806e61ca56671b77a0e4dcbf2ac6cfbbaf060c40b3393a37b9e53d3e', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-27 05:32:40', '2026-01-28 02:32:40');

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

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_auctions_live`  AS SELECT `a`.`id` AS `id`, `a`.`end_time` AS `end_time`, `a`.`current_bid` AS `current_bid`, `a`.`status` AS `status`, `p`.`title` AS `title`, `p`.`images` AS `images`, `p`.`slug` AS `product_slug` FROM (`auctions` `a` join `products` `p` on(`a`.`product_id` = `p`.`id`)) WHERE `a`.`status` = 'active' AND `a`.`end_time` > current_timestamp() ;

-- --------------------------------------------------------

--
-- Structure for view `view_products_marketplace`
--
DROP TABLE IF EXISTS `view_products_marketplace`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_products_marketplace`  AS SELECT `p`.`id` AS `id`, `p`.`title` AS `title`, `p`.`slug` AS `slug`, `p`.`price_gashy` AS `price_gashy`, `p`.`type` AS `type`, `p`.`images` AS `images`, `p`.`stock` AS `stock`, `c`.`name` AS `category_name`, `c`.`slug` AS `category_slug`, `s`.`store_name` AS `store_name`, `s`.`rating` AS `seller_rating`, `s`.`is_approved` AS `is_approved` FROM ((`products` `p` join `categories` `c` on(`p`.`category_id` = `c`.`id`)) join `sellers` `s` on(`p`.`seller_id` = `s`.`account_id`)) WHERE `p`.`status` = 'active' AND `p`.`stock` > 0 AND `s`.`is_approved` = 1 ;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auctions`
--
ALTER TABLE `auctions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `mystery_box_loot`
--
ALTER TABLE `mystery_box_loot`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
