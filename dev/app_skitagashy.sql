-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 28, 2026 at 07:28 AM
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
(1, '6dygwo6jHPrExGKrohykhYoC1DkAA6CyPp9qDbhMe1JT', 'gardunydev', 'gardunydeveloper@gmail.com', 'seller', 'bronze', 'bd45660a3cd2a193c8f2578d9ff80752', 0, '2026-01-16 04:03:30', '2026-02-21 13:35:22', 0, 'c6a0bad7'),
(2, 'Di6...TEST_WALLET_2', 'CryptoKing', 'seller2@test.com', 'account', 'bronze', NULL, 0, '2026-02-02 02:50:08', '2026-02-02 03:14:25', 0, NULL),
(3, 'Hmdv1Asp6uhvG9SCX64fdCX7wVkYZ792v5uavjEjACXb', NULL, NULL, 'account', 'bronze', 'b46799ba324cc88d29b1432bd1fabfdb', 0, '2026-02-26 22:23:11', '2026-02-26 22:23:11', 0, 'be443d4c');

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
(1, 1, 1, 1201, 1, '2026-02-27 15:38:34'),
(2, 1, 2, 490, 1, '2026-02-27 15:49:11');

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
(1, 1, '57b71ebb2ded2c8c1569a26891d3ffb2dce1d3a44d0eaf8b4d6bcc1f4f4ab243', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-03 07:49:06', '2026-02-24 04:49:06'),
(2, 1, 'f1f36e5fce9f32b36a96bb64d76889e505451e77898cf775975a784aa4d09fb6', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-03 09:47:02', '2026-02-24 06:47:02'),
(3, 1, '74e1782e1c7d1f7788208689ffc817c6ba72db962313b3a805ce798799b338cc', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-05 09:11:46', '2026-02-26 06:11:46'),
(4, 1, 'f6778e01aed71c253d477d06e2df61fb2ff13343a6cad8c4ba4b80bb1566ff8d', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-05 09:39:06', '2026-02-26 06:39:06'),
(5, 1, 'caa208399dc5669e17133f162e9b7714acbe7567ce81d9eee83f17552030533b', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-05 11:43:33', '2026-02-26 08:43:33'),
(6, 1, '656f117a8eae378f847847f0ec7ad26514de8a069120b01416e1d996439f3fc7', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 00:30:48', '2026-02-26 21:30:48'),
(7, 1, '66c2d3938d906796fc7200ca38db331fb4f48059295ae8d3404de74dfe08f530', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 00:50:36', '2026-02-26 21:50:36'),
(8, 1, '90bda16a1974f58dc96827331e1e7c76127f71518830f8d7a7080269108bf898', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 00:51:02', '2026-02-26 21:51:02'),
(9, 1, 'acd98b79329fd6dbc4e6d90b8ed86c400859ce494c75ccba7754d6a0395b4f84', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 01:05:40', '2026-02-26 22:05:40'),
(10, 1, 'dbe6e81674e8f57b9b3ac612a192e97148d5d50f22fd51019218fc490daa6be7', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 01:19:37', '2026-02-26 22:19:37'),
(11, 3, 'c15cf4b5df02c9743430c98530408b38a7096878504c5476e486d43e1ee73962', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-06 01:23:11', '2026-02-26 22:23:11'),
(12, 1, 'ae7bb995b0535f10cdaf7ba12a7679085f53524982d798bbca10e1854d23f9cc', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 18:18:54', '2026-02-27 15:18:54'),
(13, 3, '4aa66fa3df846c7b313dd13368bd9b1c3262729e49c9ba1084bb88af0ea0fcb7', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-06 18:19:48', '2026-02-27 15:19:48'),
(14, 3, 'edd77ce4e4cdd87c9f0b877025df91c340edc2306ca7d01dd082c5559a40fd04', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-06 18:41:12', '2026-02-27 15:41:12');

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

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_type`, `user_id`, `action`, `details`, `ip_address`, `created_at`) VALUES
(1, 'account', 1, 'view_secret', 'Viewed Order #1', '::1', '2026-02-24 05:27:32'),
(2, 'account', 1, 'view_secret', 'Viewed Order #1', '::1', '2026-02-24 05:27:36'),
(3, 'account', 1, 'view_secret', 'Viewed Order #2', '::1', '2026-02-24 06:57:06'),
(4, 'account', 1, 'view_secret', 'Viewed Order #2', '::1', '2026-02-24 06:57:24'),
(5, 'account', 1, 'view_secret', 'Viewed Order #1', '::1', '2026-02-24 06:57:33'),
(6, 'account', 1, 'view_secret', 'Viewed Order #4', '::1', '2026-02-24 07:02:44'),
(7, 'account', 1, 'view_secret', 'Viewed Order #4', '::1', '2026-02-24 07:03:28'),
(8, 'account', 1, 'view_secret', 'Viewed Order #4', '::1', '2026-02-24 07:13:29'),
(9, 'account', 1, 'view_secret', 'Viewed Order #2', '::1', '2026-02-24 07:14:25'),
(10, 'account', 1, 'view_secret', 'Viewed Order #1', '::1', '2026-02-24 07:14:29'),
(11, 'account', 1, 'view_secret', 'Viewed Order #4', '::1', '2026-02-24 07:14:51'),
(12, 'account', 1, 'view_secret', 'Viewed Order #4', '::1', '2026-02-24 07:16:01'),
(13, 'account', 1, 'view_secret', 'Viewed Order #2', '::1', '2026-02-24 07:16:03'),
(14, 'account', 1, 'view_secret', 'Viewed Order #1', '::1', '2026-02-24 07:16:04'),
(15, 'account', 1, 'view_secret', 'Viewed Order #7', '::1', '2026-02-24 07:28:35'),
(16, 'account', 1, 'view_secret', 'Viewed Order #4', '::1', '2026-02-24 07:28:37'),
(17, 'account', 1, 'view_secret', 'Viewed Order #7', '::1', '2026-02-24 07:38:26'),
(18, 'account', 1, 'view_secret', 'Viewed Order #4', '::1', '2026-02-24 07:38:28'),
(19, 'account', 1, 'view_secret', 'Viewed Order #7', '::1', '2026-02-26 02:57:26'),
(20, 'account', 1, 'view_secret', 'Viewed Order #4', '::1', '2026-02-26 03:10:49'),
(21, 'account', 1, 'view_secret', 'Viewed Order #7', '::1', '2026-02-26 08:49:57'),
(22, 'account', 1, 'view_secret', 'Viewed Order #4', '::1', '2026-02-26 08:50:00'),
(23, 'account', 1, 'view_secret', 'Viewed Order #2', '::1', '2026-02-26 08:50:01'),
(24, 'account', 1, 'view_secret', 'Viewed Order #1', '::1', '2026-02-26 08:50:03'),
(25, 'account', 1, 'view_secret', 'Viewed Order #10', '::1', '2026-02-26 23:00:27'),
(26, 'account', 1, 'view_secret', 'Viewed Order #23', '::1', '2026-02-27 04:56:13'),
(27, 'account', 1, 'view_secret', 'Viewed Order #23', '::1', '2026-02-27 04:57:35'),
(28, 'account', 1, 'view_secret', 'Viewed Order #24', '::1', '2026-02-27 05:02:39'),
(29, 'admin', 1, 'order_update', 'Changed Order #25 to shipped: dgjsdhgsd', '::1', '2026-02-27 15:23:56'),
(30, 'account', 1, 'view_secret', 'Viewed Order #27', '::1', '2026-02-27 15:26:20'),
(31, 'account', 1, 'view_secret', 'Viewed Order #27', '::1', '2026-02-27 15:26:34');

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
(1, 1, '2026-02-27 09:49:00', '2026-02-27 07:35:00', 1.000000000, 2.000000000, 4.000000000, 1, 'ended'),
(2, 1, '2026-02-27 18:32:00', '2026-02-27 18:39:00', 10.000000000, 20.000000000, 12.000000000, 1, 'ended');

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

--
-- Dumping data for table `burn_log`
--

INSERT INTO `burn_log` (`id`, `account_id`, `amount`, `purpose`, `tx_signature`, `usd_value_at_burn`, `created_at`) VALUES
(1, 1, 10.000000000, 'lottery_entry', '59d944f8df377060ec229f1a4595f6dd51a026bb1691608590a0d831cde7d4ae8dc8e1df9d4714c1df275726', 0.00, '2026-02-24 09:02:04'),
(2, 1, 10.000000000, 'lottery_entry', '2df9fad6a7aae9da75a25d810f6575cd52dafbc09bb069a18ce45c2964e9d075ec283a020aa0010b3d15ce1f', 0.00, '2026-02-24 09:02:37'),
(3, 1, 40.000000000, 'lottery_entry', '4b0001d39f107071935400c3adcc10f0cb951b0b09c478323127f11ce4b60869422d7c25a9b8d822960db078', 0.00, '2026-02-24 09:03:07'),
(4, 1, 100.000000000, 'lottery_entry', '68212de81d02f22b4e1cbd439f0a82d3e5033ce1827c13ad0eadc677dd2c06b3aabb65ed6bf3a18e4ffa39e2', 0.00, '2026-02-24 09:03:20'),
(5, 1, 20.000000000, 'lottery_entry', '3tthQy57myz2TDamMaeTTc891woB7XuDJxHdwA8neAdpamNpCWSkec8ZUnp1FVJM6znWE6wR5CRUv6qqb1b8CKr3', 0.00, '2026-02-27 04:32:39'),
(6, 1, 50.000000000, 'lottery_entry', '4M8tY8ZVk6JdZz99q4LAyGxMT4FazoBbe2HMMRKiY73wMFvCqiLNwVojEqX1wthVWHMhtCHdpZ5J2cyyTgZmDUyi', 0.00, '2026-02-27 15:35:26');

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

--
-- Dumping data for table `gift_cards`
--

INSERT INTO `gift_cards` (`id`, `product_id`, `code_enc`, `pin_enc`, `expiry_date`, `is_sold`, `sold_to_order_id`) VALUES
(1, 1, 'NVltUzJ0SWpwdzY0aXJRNnZpR2JwUT09Ojo/Ecdg9k3Xc6lc4wxzjFK/', NULL, NULL, 1, 1),
(2, 1, 'UDZpN0tkZVNrM0NtSnp6RXRrODRHQT09OjqkBpR5/xqp8tHOAh9W4bA3', NULL, NULL, 1, 1),
(3, 1, 'WkNDbzl4bWtBUy9JZnI5dmFpTXNxZz09OjqSriicybF04UInoWHiG4H9', NULL, NULL, 1, 2),
(4, 12, 'bGdTV2FZL3l4NW5ieDYxQVhaUm5pQT09OjrGcaYCiZXWmn+L30UYkYki', NULL, NULL, 1, 4),
(5, 12, 'SUxSU1ZwdWFaclR2ZHBvMm1scWpwdz09Ojphc/qyigellXQggdacX2hN', NULL, NULL, 1, 7),
(6, 15, 'NDVmN09JamVSdVZiRTV2YkNnYnY4Zz09OjqZeooNn3nUpxermfYKDuSr', NULL, NULL, 0, NULL),
(7, 15, 'eCt5U1hBNXJvUExmWWFsYW82Mk5CUT09OjrX6ZNH+MbXbpRyLwVZDFW4', NULL, NULL, 0, NULL),
(8, 15, 'NzJnQ2lsMEIxR1VCVmt6QW1tUStoUT09OjqrR5+QObTplBl1xd0AH+lt', NULL, NULL, 0, NULL),
(9, 1, 'Z3JWbVBEcm1aczQ0cFAzcEozTzExQT09OjrDi0SrdRG9NK22CSPAkh+j', NULL, NULL, 1, 10),
(10, 1, 'b3FrUjRoTG52bzJ6YzE5VzdqUGFzUT09OjpHArSokUx2dHNPKnpkOqmj', NULL, NULL, 1, 22),
(11, 12, 'dEF1L1pNb3JtUHErRTNjc0t5V0U3dz09OjoOAqzUgiZpLjo1cUxlquck', NULL, NULL, 1, 24),
(12, 18, 'M0gxS0VENDdKS1NjQk10eXhjSC9pdz09Ojr2YK1j4okeyY6hfPv/0GCn', NULL, NULL, 1, 27),
(13, 18, 'Z1Z3S3k2aXR6alVsQjR5cmJEVW1nVnpHOGF6Zk1PMlpOdG96aTd2WkpyST06OtC5N17L8wufyvAyAy8D7P8=', NULL, NULL, 0, NULL),
(14, 18, 'Z0M0cmc1WE54OUQ5UGw0SXdIWmN1QT09OjrcqQ4BESNiBMoNIkpe8wyh', NULL, NULL, 0, NULL),
(15, 1, 'c2NKT3o1aWsvUitFYVBxV2V1UVpEdz09OjpiBNZbHvW4cnEFLWaWP3Vm', NULL, NULL, 0, NULL),
(16, 1, 'anl1YWlLaEdXb29yQUJLalorMDdEUT09Ojqm4b+rRXJDFX8/S/9Lg+Z/', NULL, NULL, 0, NULL);

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
(1, 1, 1, '59d944f8df377060ec229f1a4595f6dd51a026bb1691608590a0d831cde7d4ae8dc8e1df9d4714c1df275726', 1),
(2, 1, 1, '2df9fad6a7aae9da75a25d810f6575cd52dafbc09bb069a18ce45c2964e9d075ec283a020aa0010b3d15ce1f', 1),
(3, 1, 1, '4b0001d39f107071935400c3adcc10f0cb951b0b09c478323127f11ce4b60869422d7c25a9b8d822960db078', 4),
(4, 1, 1, '68212de81d02f22b4e1cbd439f0a82d3e5033ce1827c13ad0eadc677dd2c06b3aabb65ed6bf3a18e4ffa39e2', 10),
(5, 2, 1, '3tthQy57myz2TDamMaeTTc891woB7XuDJxHdwA8neAdpamNpCWSkec8ZUnp1FVJM6znWE6wR5CRUv6qqb1b8CKr3', 2),
(6, 3, 1, '4M8tY8ZVk6JdZz99q4LAyGxMT4FazoBbe2HMMRKiY73wMFvCqiLNwVojEqX1wthVWHMhtCHdpZ5J2cyyTgZmDUyi', 5);

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
(1, 1, 160.000000000, '2026-02-24 09:03:42', 'closed', '[{\"rank\":1,\"user\":1,\"amount\":\"160.000000000\"}]'),
(2, 2, 20.000000000, '2026-02-27 04:34:24', 'closed', '[{\"rank\":1,\"user\":1,\"amount\":\"20.000000000\"}]'),
(3, 3, 50.000000000, '2026-02-27 15:37:31', 'closed', '[{\"rank\":1,\"user\":1,\"amount\":25}]'),
(4, 4, 0.000000000, '2026-03-06 15:37:31', 'open', NULL);

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

--
-- Dumping data for table `nft_burn_campaigns`
--

INSERT INTO `nft_burn_campaigns` (`id`, `collection_address`, `name`, `reward_amount`, `is_active`) VALUES
(1, 'TEST-COLLECTION-XYZ', 'Gashy Genesis Burn', 500.000000000, 1);

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

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `account_id`, `total_gashy`, `tx_signature`, `status`, `created_at`) VALUES
(1, 1, 2.000000000, 'bc00b62d3c04a81656f2c760adf3a2a5e19f9bdae5df10c86a8a90de04eaa31751d421483145fa47324a5459', 'completed', '2026-02-24 05:27:15'),
(2, 1, 2.000000000, 'AUC_WIN_1_1771916171.5768', 'completed', '2026-02-24 06:56:11'),
(4, 1, 25.000000000, '6659c81730b41d861a9db3e8e25d9596962493c7c56604f88edfe0fc9a0ae2a4d66519f3aab21c4d5e0f086d', 'completed', '2026-02-24 07:02:39'),
(7, 1, 25.000000000, '34d0f397b78299a91d4d3f8bcc9dd6e6bf9e1c870d7e2415db235ca987773a53fae9be590b0191b6d170d359', 'completed', '2026-02-24 07:28:30'),
(8, 2, 10.000000000, '34d0f397b78299a9hh4d3f8bcc9dd6e6bf9e1c870d7dffa65db235ca987773a53fae9be590b0191b6d170d35', 'delivered', '2026-02-24 07:28:30'),
(10, 1, 1.000000000, '9c8bFxnceMF9dryQsc1CfcFr8haaix1ZmTtWa5F1QPGs5GE4YmWapLzVjNUWwer7i9dSWezReVRhWLXvn2oxu9G', 'completed', '2026-02-26 23:00:21'),
(11, 1, 100.000000000, '2N13dzVHMWdiFA6u6AaTL5yB5xwP3xQbLjPsivWzSMUHd997CBUtXwF5DZ61vsaegy84CKiMaLdCE13XtaLQeA4w', 'processing', '2026-02-26 23:05:22'),
(12, 1, 100.000000000, '52L9EwRrnBTmmWRGZ35YTc38q46FNzSnCy9qurTR9Z11wj4Ve9SfD8MV1cGkn1fnxUAhBBdrTdA8NVhu7kC7JQKJ', 'processing', '2026-02-26 23:11:12'),
(13, 1, 100.000000000, '2CNnXCYXjWg2hYuPk3kGWC61ZMPD3tQMqxxnoLePeBN1yqpC3ufxS9qhiZa7eEbZbW4LBykLw1NUwdA4nEA8bU1P', 'processing', '2026-02-26 23:16:01'),
(14, 1, 100.000000000, '4fU6ZnAzPiVEmVgSe2EatPF8G5uimkxSaJahBJxSPRKc3xeWegyEetavoagbiLkZ6LMvSaTzStXMFaAQAwE9RwvJ', 'processing', '2026-02-26 23:17:58'),
(15, 1, 100.000000000, '3GopVLhXsxjjwHQhgZs1tcfcE1VET6cN85JEC9RoJ3oCYN7YyGtW1sKk4SZGp8TdiV9uNjJDKj8YDxChquXNJptk', 'processing', '2026-02-27 03:42:25'),
(16, 1, 100.000000000, 't7VjfFwPYEUMX63rL6WivxLWKczko51CRkuTK2UjLx5sDYFSRxAByUEQSQw3Gp7HQKfD8iwVByYae5juEFg5kJB', 'processing', '2026-02-27 04:01:36'),
(17, 1, 100.000000000, '3tnMfsvNwKsuPTjq9VEHsnTPbzw23KztyG9rf1MWQX8Diwj2ap9VrQA6seEAQvB8iqQVrL8b5gzEyfQFtLNKYDux', 'processing', '2026-02-27 04:07:36'),
(18, 1, 100.000000000, '1rg56AytzfrQ41x8oSJFEDwepr78iCr7m68D8bBXhD96XE8dv25kxC7YqKdoCmdmLfjJ6nWfmAkXokLcYz5PgcN', 'processing', '2026-02-27 04:12:18'),
(19, 1, 100.000000000, 'PeVeuQBrRA2ASGFNKUET7B4JVKpsMXrp9Y38psEj7RVoGD1pArPyKVZnp5MSUGEZFxE8ywTeyWPZNfA2qjLRaWc', 'processing', '2026-02-27 04:14:33'),
(20, 1, 100.000000000, '2y5Rwd1yg7EZ4xv8NZnBnyqin9nnoZanpEzWqZFUvZcPwLQPtsAMVWfNifPrMKqSur7oDJdaZRLydCNWMCdrpbt6', 'processing', '2026-02-27 04:15:58'),
(21, 1, 100.000000000, '5oKpEmGHnaxwMn992VACj5T2tRPcZMYAXh8mFrGGQr9tcTo7QfyQFBdqzBCQ6wUEsWs3o6H1SRCk8mjG415gGePG', 'processing', '2026-02-27 04:18:44'),
(22, 1, 4.000000000, 'AUC_WIN_1_1772167420.6881', 'completed', '2026-02-27 04:43:40'),
(23, 1, 0.000000000, '3so6VcHdJ6cqanBbvFRUVqpvn2gfVJjhrHr3ELiP797jNL24qYyZf2qHXrpvwJt1U8MMSPtShroostWwvrAn74RA', 'completed', '2026-02-27 04:55:59'),
(24, 1, 0.000000000, '2epEhdrtrsmzh8S9PcTc4kaTx29ttdBrXNXeW5KgwxLGwaNGduojGjVcdTCBSTwE9xM8v2jTV251VUSFwTddEwUF', 'completed', '2026-02-27 05:01:37'),
(25, 1, 10.000000000, 'yXkMJfipsahuGSGGAN9mfVQiQTHLh2qh7eEA4rU9nwMeV69Mq9JU5giBPR6d4RRGuFYmyCMdj6y8qpyAi5Az7VX', 'shipped', '2026-02-27 15:22:43'),
(26, 1, 10.000000000, '4wNroSZEtMW7YNHCN3AWefT8A3yigA8tR8bmLHtX3redEiaboeeU5Pj4ShcvNVAFAXs4UPYE8vFGm1BnHshxiaTZ', 'processing', '2026-02-27 15:25:47'),
(27, 1, 15.000000000, 'QzYPE4J5ygBoxy8UqDwFNJKMC6axhRMqaXMLcRfSGA7CtjuFpWZU99y6dVGnmbJyX9yrtoYLFSt9tZvNZmejVKe', 'completed', '2026-02-27 15:26:11'),
(28, 1, 40.000000000, '4JVM2754gbQHiHiVCt57pzSJEhxk6NnfrJ7p5sFcu5Jxsynf9Rk9WFR7eokG43winusqEgqUmZvUNrqJtAdLK5Yz', 'completed', '2026-02-27 15:38:34');

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
(1, 1, 1, 2, 1.000000000, NULL),
(2, 2, 1, 1, 2.000000000, NULL),
(3, 4, 12, 1, 25.000000000, NULL),
(4, 7, 12, 1, 25.000000000, NULL),
(5, 8, 2, 1, 40.000000000, NULL),
(6, 10, 1, 1, 1.000000000, NULL),
(7, 11, 16, 1, 100.000000000, NULL),
(8, 12, 16, 1, 100.000000000, NULL),
(9, 13, 16, 1, 100.000000000, NULL),
(10, 14, 16, 1, 100.000000000, NULL),
(11, 15, 16, 1, 100.000000000, NULL),
(12, 16, 16, 1, 100.000000000, NULL),
(13, 17, 16, 1, 100.000000000, NULL),
(14, 18, 16, 1, 100.000000000, NULL),
(15, 19, 16, 1, 100.000000000, NULL),
(16, 20, 16, 1, 100.000000000, NULL),
(17, 21, 16, 1, 100.000000000, NULL),
(18, 22, 1, 1, 4.000000000, NULL),
(19, 23, 12, 1, 0.000000000, NULL),
(20, 24, 12, 1, 0.000000000, NULL),
(21, 25, 17, 1, 10.000000000, NULL),
(22, 26, 17, 1, 10.000000000, NULL),
(23, 27, 18, 1, 15.000000000, NULL),
(24, 28, 19, 1, 40.000000000, NULL);

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
(1, 2, 1, 'Amazon $50 Gift Card (US)', 'amazon-50-gift-card-us-', 'Valid for US accounts only. Code delivered instantly via email and order dashboard upon blockchain confirmation.', 1.000000000, 0.01, 2, 'gift_card', '[\"/server/uploads/products/6998ffe2ee3ab4.45018495.jpg\"]', NULL, 'active', 479, '2026-01-16 08:40:08'),
(2, 1, 1, 'Steam Wallet $20 Global', 'steam-wallet-20-global', 'Add funds to your Steam Wallet. Works globally. Instant delivery.', 40.000000000, 20.00, 50, 'gift_card', '[\"/server/uploads/products/6998ffddd54654.89335006.jpg\"]', NULL, 'active', 902, '2026-01-16 08:40:08'),
(3, 1, 1, 'PUBG Mobile 660 UC', 'pubg-mobile-660-uc', 'pubg pubg pubg pubg pubg pubg pubg pubg pubg pubg pubg pubg', 30.000000000, 10.00, 200, 'digital', '[\"/server/uploads/products/6998ffd7dfa877.05088825.jfif\"]', NULL, 'active', 128, '2026-01-16 08:40:08'),
(4, 1, 3, 'Windows 11 Pro License', 'windows-11-pro-license', 'Lifetime retail key for Windows 11 Pro. Supports multi-language installation.', 6.000000000, 25.00, 17, 'digital', '[\"/server/uploads/products/6998fa5e48ed03.37830430.webp\"]', NULL, 'active', 71, '2026-01-16 08:40:08'),
(5, 1, 4, 'CyberPunk Samurai #042', 'cyberpunk-samurai-042', 'Rare NFT from the CyberPunk collection. Verified ownership on Solana.', 15.000000000, 250.00, 0, 'nft', '[\"/server/uploads/products/6998f9e1b69b83.11482242.jfif\"]', NULL, 'active', 1211, '2026-01-16 08:40:08'),
(6, 1, 5, 'Legendary Mystery Box', 'legendary-mystery-box', 'Contains a chance to win 50,000 GASHY or a Rare NFT. High risk, high reward.', 50.000000000, 20.00, 0, 'mystery_box', '[\"/server/uploads/products/6998fa3a558396.36014683.webp\"]', NULL, 'banned', 3021, '2026-01-16 08:40:08'),
(7, 1, 4, 'Bored Ape #9999 (Test)', 'bored-ape-9999-test-', 'Original BAYC NFT. Verified on Ethereum.', 10.000000000, NULL, 1, 'nft', '[\"/server/uploads/products/6998fa590ac966.04235643.jpg\"]', NULL, 'active', 6, '2026-01-17 12:23:19'),
(8, 1, 5, 'Starter Mystery Box', 'starter-mystery-box', 'jhfgdjfhdgfjdhf', 10.000000000, NULL, 493, 'mystery_box', '[\"/server/uploads/products/6998fa4b9e5517.58520314.webp\"]', NULL, 'active', 12, '2026-01-17 12:23:19'),
(9, 1, 1, 'GTA-6', 'gta-6', 'undefined', 10.000000000, NULL, 4, 'digital', '[\"/server/uploads/products/6998fa1e769023.06569469.jpg\"]', NULL, 'active', 19, '2026-01-29 01:53:02'),
(10, 1, 4, 'nft test', 'nft-test', 'test product test product test product test product test product test product test product test product test product test product test product test product test product test product test product test product test product test product test product test product test product test product test product test product test product test product test product test product test product', 1.000000000, NULL, 1, 'nft', '[\"/server/uploads/products/6998fa76d88368.86052092.jfif\"]', NULL, 'active', 7, '2026-01-29 01:53:43'),
(11, 1, 1, 'Gifts', 'gifts', '....', 1.000000000, NULL, 2, 'gift_card', '[\"/server/uploads/products/6998fbbba44ac0.24472211.webp\"]', NULL, 'active', 9, '2026-02-02 01:11:44'),
(12, 2, 2, 'Valorant 1000 Points', 'valorant-1000-points', 'Instant delivery code.', 25.000000000, NULL, 0, 'digital', '[\"/server/uploads/products/6998f945730096.26491339.jpg\"]', NULL, 'active', 34, '2026-02-02 02:50:08'),
(13, 1, 5, 'first mystery', 'first-mystery', 'first mystery  detail', 10.000000000, NULL, 1, 'digital', '[\"/server/uploads/products/6998fbb4c5f740.95288782.jfif\"]', NULL, 'active', 2, '2026-02-07 10:27:45'),
(15, 1, 1, 'loxera software 1YEAR', 'sdsd-482', 'loxera software license key 1 year', 1.000000000, NULL, 8, 'digital', '[\"/server/uploads/products/699fd5ea7639d0.75533383.png\"]', NULL, 'active', 47, '2026-02-26 05:10:37'),
(16, 2, 6, 'test gashy', 'test-gashy', 'ddsdsdsdsd', 100.000000000, NULL, 4, 'physical', '[\"/server/uploads/products/69a0d1969cbd83.73127799.png\"]', NULL, 'active', 40, '2026-02-26 23:04:54'),
(17, 2, 6, 'test test', 'test-test', 'dghjsdgsdjs', 10.000000000, NULL, 8, 'physical', '[\"/server/uploads/products/69a1b695d2de12.26621603.jpg\"]', NULL, 'active', 2, '2026-02-27 15:21:57'),
(18, 2, 3, 'test test 2', 'test-test-2', 'cmxcdxjhdgdsj', 15.000000000, NULL, 2, 'digital', '[\"/server/uploads/products/69a1b745a98b80.34502289.jpg\"]', NULL, 'active', 1, '2026-02-27 15:24:53'),
(19, 3, 6, 'seller-2 product-1', 'seller-2-product-1-894', 'fhdjkfhdfdf', 40.000000000, NULL, 2, 'physical', '[\"/server/uploads/products/69a1ba14963073.51894396.jpg\"]', NULL, 'active', 1, '2026-02-27 15:36:52'),
(20, 2, 5, 'mystery-1', 'mystery-1', 'djhsdjhgsjhdasgds', 50.000000000, NULL, 1, 'mystery_box', '[\"/server/uploads/products/69a1bc68603ce7.93450568.png\"]', NULL, 'active', 0, '2026-02-27 15:46:48');

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
(1, 'test', 'buy', 10, 30.000000000, 'daily', 1),
(2, 'test-2', 'burn', 10, 30.000000000, 'once', 1);

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
(1, 'Gashy Official Store', 'gashy-official', 5.00, 5.00, 0, 1),
(2, 'CryptoKing Store', 'crypto-king', 5.00, 4.80, 0, 1),
(3, 'DEVSELLER', 'devseller', 5.00, 0.00, 0, 1);

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
('::1', 'auth_attempt', 1, 1772206932),
('::1', 'global_api', 7, 1772260091),
('::1', 'transaction', 1, 1772206774);

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

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `account_id`, `type`, `amount`, `tx_signature`, `reference_id`, `status`, `created_at`) VALUES
(1, 1, 'purchase', -2.000000000, 'bc00b62d3c04a81656f2c760adf3a2a5e19f9bdae5df10c86a8a90de04eaa31751d421483145fa47324a5459', 1, 'confirmed', '2026-02-24 05:27:15'),
(2, 1, 'auction_bid', 2.000000000, 'BID_1771915796_1', 1, 'confirmed', '2026-02-24 06:49:56'),
(3, 1, 'purchase', -25.000000000, '6659c81730b41d861a9db3e8e25d9596962493c7c56604f88edfe0fc9a0ae2a4d66519f3aab21c4d5e0f086d', 4, 'confirmed', '2026-02-24 07:02:39'),
(4, 1, 'purchase', -25.000000000, '34d0f397b78299a91d4d3f8bcc9dd6e6bf9e1c870d7e2415db235ca987773a53fae9be590b0191b6d170d359', 7, 'confirmed', '2026-02-24 07:28:30'),
(5, 1, 'reward', 30.000000000, NULL, 1, 'confirmed', '2026-02-24 08:18:42'),
(6, 1, 'lottery_ticket', -10.000000000, '59d944f8df377060ec229f1a4595f6dd51a026bb1691608590a0d831cde7d4ae8dc8e1df9d4714c1df275726', 1, 'confirmed', '2026-02-24 09:02:04'),
(7, 1, 'lottery_ticket', -10.000000000, '2df9fad6a7aae9da75a25d810f6575cd52dafbc09bb069a18ce45c2964e9d075ec283a020aa0010b3d15ce1f', 1, 'confirmed', '2026-02-24 09:02:37'),
(8, 1, 'lottery_ticket', -40.000000000, '4b0001d39f107071935400c3adcc10f0cb951b0b09c478323127f11ce4b60869422d7c25a9b8d822960db078', 1, 'confirmed', '2026-02-24 09:03:07'),
(9, 1, 'lottery_ticket', -100.000000000, '68212de81d02f22b4e1cbd439f0a82d3e5033ce1827c13ad0eadc677dd2c06b3aabb65ed6bf3a18e4ffa39e2', 1, 'confirmed', '2026-02-24 09:03:20'),
(10, 1, 'reward', 160.000000000, NULL, 1, 'confirmed', '2026-02-24 09:03:42'),
(11, 1, 'reward', 30.000000000, NULL, 2, 'confirmed', '2026-02-26 02:15:19'),
(12, 1, 'purchase', -1.000000000, '9c8bFxnceMF9dryQsc1CfcFr8haaix1ZmTtWa5F1QPGs5GE4YmWapLzVjNUWwer7i9dSWezReVRhWLXvn2oxu9G', 10, 'confirmed', '2026-02-26 23:00:21'),
(13, 1, 'purchase', -100.000000000, '2N13dzVHMWdiFA6u6AaTL5yB5xwP3xQbLjPsivWzSMUHd997CBUtXwF5DZ61vsaegy84CKiMaLdCE13XtaLQeA4w', 11, 'confirmed', '2026-02-26 23:05:22'),
(14, 1, 'purchase', -100.000000000, '52L9EwRrnBTmmWRGZ35YTc38q46FNzSnCy9qurTR9Z11wj4Ve9SfD8MV1cGkn1fnxUAhBBdrTdA8NVhu7kC7JQKJ', 12, 'confirmed', '2026-02-26 23:11:12'),
(15, 1, 'purchase', -100.000000000, '2CNnXCYXjWg2hYuPk3kGWC61ZMPD3tQMqxxnoLePeBN1yqpC3ufxS9qhiZa7eEbZbW4LBykLw1NUwdA4nEA8bU1P', 13, 'confirmed', '2026-02-26 23:16:01'),
(16, 1, 'purchase', -100.000000000, '4fU6ZnAzPiVEmVgSe2EatPF8G5uimkxSaJahBJxSPRKc3xeWegyEetavoagbiLkZ6LMvSaTzStXMFaAQAwE9RwvJ', 14, 'confirmed', '2026-02-26 23:17:58'),
(17, 1, 'purchase', -100.000000000, '3GopVLhXsxjjwHQhgZs1tcfcE1VET6cN85JEC9RoJ3oCYN7YyGtW1sKk4SZGp8TdiV9uNjJDKj8YDxChquXNJptk', 15, 'confirmed', '2026-02-27 03:42:25'),
(18, 1, 'purchase', -100.000000000, 't7VjfFwPYEUMX63rL6WivxLWKczko51CRkuTK2UjLx5sDYFSRxAByUEQSQw3Gp7HQKfD8iwVByYae5juEFg5kJB', 16, 'confirmed', '2026-02-27 04:01:36'),
(19, 1, 'purchase', -100.000000000, '3tnMfsvNwKsuPTjq9VEHsnTPbzw23KztyG9rf1MWQX8Diwj2ap9VrQA6seEAQvB8iqQVrL8b5gzEyfQFtLNKYDux', 17, 'confirmed', '2026-02-27 04:07:36'),
(20, 1, 'purchase', -100.000000000, '1rg56AytzfrQ41x8oSJFEDwepr78iCr7m68D8bBXhD96XE8dv25kxC7YqKdoCmdmLfjJ6nWfmAkXokLcYz5PgcN', 18, 'confirmed', '2026-02-27 04:12:18'),
(21, 1, 'purchase', -100.000000000, 'PeVeuQBrRA2ASGFNKUET7B4JVKpsMXrp9Y38psEj7RVoGD1pArPyKVZnp5MSUGEZFxE8ywTeyWPZNfA2qjLRaWc', 19, 'confirmed', '2026-02-27 04:14:33'),
(22, 1, 'purchase', -100.000000000, '2y5Rwd1yg7EZ4xv8NZnBnyqin9nnoZanpEzWqZFUvZcPwLQPtsAMVWfNifPrMKqSur7oDJdaZRLydCNWMCdrpbt6', 20, 'confirmed', '2026-02-27 04:15:58'),
(23, 1, 'purchase', -100.000000000, '5oKpEmGHnaxwMn992VACj5T2tRPcZMYAXh8mFrGGQr9tcTo7QfyQFBdqzBCQ6wUEsWs3o6H1SRCk8mjG415gGePG', 21, 'confirmed', '2026-02-27 04:18:44'),
(24, 1, 'auction_bid', 3.000000000, 'BID_1772166176_1', 1, 'failed', '2026-02-27 04:22:56'),
(25, 1, 'auction_bid', 4.000000000, 'BID_1772166568_1', 1, 'confirmed', '2026-02-27 04:29:28'),
(26, 1, 'lottery_ticket', -20.000000000, '3tthQy57myz2TDamMaeTTc891woB7XuDJxHdwA8neAdpamNpCWSkec8ZUnp1FVJM6znWE6wR5CRUv6qqb1b8CKr3', 2, 'confirmed', '2026-02-27 04:32:39'),
(27, 1, 'reward', 20.000000000, NULL, 2, 'confirmed', '2026-02-27 04:34:24'),
(28, 1, 'purchase', 10.000000000, '4CV3HTTfNS6nQc3CLVUMMDqRkDHUoaYwQVfKNSEqe7wRUat3MoxY8QkXNBXMsXUJ5dh6eh7mHLoNghFKtZe3eMMo', NULL, 'confirmed', '2026-02-27 04:37:15'),
(29, 1, 'reward', 10.000000000, NULL, 8, 'confirmed', '2026-02-27 04:37:15'),
(30, 1, 'purchase', 50.000000000, 'MhPZ5G8uH6qy7Ks9nYth8pqqj2cfQr6VPLxdXr5cF5Lx9omrxdzcS8G4Y6gs7iXAMqFS2kKnDFPgtQDiWKQNByx', NULL, 'confirmed', '2026-02-27 04:52:14'),
(31, 1, 'reward', 10.000000000, NULL, 6, 'confirmed', '2026-02-27 04:52:14'),
(32, 1, 'purchase', 50.000000000, '3ebPVgJ8Wdnacv8xGJcEU8AoEtUxKeJdvMHQATKSXSAsQeDSX35YrWMwx7tqQdHpYtgjrKUk6XrAp7KtiZsyXSeo', NULL, 'confirmed', '2026-02-27 04:54:53'),
(33, 1, 'reward', 10.000000000, NULL, 6, 'confirmed', '2026-02-27 04:54:53'),
(34, 1, 'purchase', 50.000000000, '3so6VcHdJ6cqanBbvFRUVqpvn2gfVJjhrHr3ELiP797jNL24qYyZf2qHXrpvwJt1U8MMSPtShroostWwvrAn74RA', NULL, 'confirmed', '2026-02-27 04:55:59'),
(35, 1, 'purchase', 50.000000000, '2epEhdrtrsmzh8S9PcTc4kaTx29ttdBrXNXeW5KgwxLGwaNGduojGjVcdTCBSTwE9xM8v2jTV251VUSFwTddEwUF', NULL, 'confirmed', '2026-02-27 05:01:37'),
(36, 1, 'purchase', -10.000000000, 'yXkMJfipsahuGSGGAN9mfVQiQTHLh2qh7eEA4rU9nwMeV69Mq9JU5giBPR6d4RRGuFYmyCMdj6y8qpyAi5Az7VX', 25, 'confirmed', '2026-02-27 15:22:43'),
(37, 1, 'purchase', -10.000000000, '4wNroSZEtMW7YNHCN3AWefT8A3yigA8tR8bmLHtX3redEiaboeeU5Pj4ShcvNVAFAXs4UPYE8vFGm1BnHshxiaTZ', 26, 'confirmed', '2026-02-27 15:25:47'),
(38, 1, 'purchase', -15.000000000, 'QzYPE4J5ygBoxy8UqDwFNJKMC6axhRMqaXMLcRfSGA7CtjuFpWZU99y6dVGnmbJyX9yrtoYLFSt9tZvNZmejVKe', 27, 'confirmed', '2026-02-27 15:26:11'),
(39, 1, 'auction_bid', 12.000000000, 'BID_1772206432_1', 2, 'failed', '2026-02-27 15:33:52'),
(40, 1, 'lottery_ticket', -50.000000000, '4M8tY8ZVk6JdZz99q4LAyGxMT4FazoBbe2HMMRKiY73wMFvCqiLNwVojEqX1wthVWHMhtCHdpZ5J2cyyTgZmDUyi', 3, 'confirmed', '2026-02-27 15:35:26'),
(41, 1, 'reward', 25.000000000, NULL, 3, 'confirmed', '2026-02-27 15:37:29'),
(42, 1, 'purchase', -40.000000000, '4JVM2754gbQHiHiVCt57pzSJEhxk6NnfrJ7p5sFcu5Jxsynf9Rk9WFR7eokG43winusqEgqUmZvUNrqJtAdLK5Yz', 28, 'confirmed', '2026-02-27 15:38:34'),
(43, 1, 'purchase', 50.000000000, '243haA8JdWQ8ZbiiabpUmr7u5FN7ct9984TKiyfkEjdcmsDE3cypoqrMrJVozd4TYbBqTrULEfwHaHMcoZjScZYt', NULL, 'confirmed', '2026-02-27 15:49:05'),
(44, 1, 'reward', 60.000000000, NULL, 20, 'confirmed', '2026-02-27 15:49:06');

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

--
-- Dumping data for table `user_sessions`
--

INSERT INTO `user_sessions` (`id`, `user_id`, `token`, `ip_address`, `user_agent`, `expires_at`, `created_at`) VALUES
(1, 1, '14c5c31e751b0c8ee003529ea7e0daca29e1bedfdf6c946fc7dc78a56ebce8b4', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-03-02 03:42:30', '2026-01-31 00:42:30'),
(2, 1, 'faeeaea13fb42b8054be1ce9280306913b209eeb32c697198ac0d718ee82f7b9', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-03-03 04:14:59', '2026-02-01 01:14:59'),
(3, 1, '5e39455dd7b2f462b1b028d65d2903989f5c2c19a349b57872df96456d09bb4d', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-03-09 12:51:32', '2026-02-07 09:51:32'),
(4, 1, 'accace3e7f3ce39e9975feb817792c280d115cd9b6f87caa75c1b1567f60f946', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-21 23:58:29', '2026-02-19 20:58:29'),
(5, 1, '50275fcf5472048782d2f62ef99656adcf490b174400612233f2fe459ce930fc', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-23 02:10:29', '2026-02-20 23:10:29'),
(6, 1, 'a4fd004714b4606a62ac64644b72357a5a89ab814d891a04fa0ba4f338258fa6', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-26 07:50:14', '2026-02-24 04:50:14'),
(7, 1, '06cf8b756a8ad344d8d749d88a2186b81eff069444434b3095ebd4998744d8da', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-29 18:20:55', '2026-02-27 15:20:55');

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

--
-- Dumping data for table `withdrawals`
--

INSERT INTO `withdrawals` (`id`, `account_id`, `amount`, `status`, `tx_signature`, `created_at`) VALUES
(1, 1, 20.000000000, 'approved', 'GASHYWITHDRAW', '2026-02-26 03:42:11'),
(2, 1, 200.000000000, 'pending', NULL, '2026-02-27 15:40:25'),
(3, 3, 38.000000000, 'pending', NULL, '2026-02-27 15:41:24');

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `account_quests`
--
ALTER TABLE `account_quests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `account_referrals`
--
ALTER TABLE `account_referrals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `account_sessions`
--
ALTER TABLE `account_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `auctions`
--
ALTER TABLE `auctions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `burn_log`
--
ALTER TABLE `burn_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `gift_cards`
--
ALTER TABLE `gift_cards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `lottery_entries`
--
ALTER TABLE `lottery_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lottery_rounds`
--
ALTER TABLE `lottery_rounds`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `quests`
--
ALTER TABLE `quests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
