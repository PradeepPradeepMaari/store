-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 26, 2026 at 01:27 PM
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
-- Database: `store_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `created_at`, `updated_at`) VALUES
(1, 'Surya Kumar', 'surya@example.com', '2026-10-26 05:38:59', '2026-10-26 05:38:59'),
(2, 'Priya Sharma', 'priya@example.com', '2026-10-26 05:38:59', '2026-10-26 05:38:59'),
(3, 'Raj Patel', 'raj@example.com', '2026-10-26 05:38:59', '2026-10-26 05:38:59'),
(4, 'Trey Fadel', 'augusta01@example.net', '2026-10-26 05:38:59', '2026-10-26 05:38:59'),
(5, 'Raymundo Trantow', 'gunnar50@example.net', '2026-10-26 05:38:59', '2026-10-26 05:38:59'),
(6, 'Millie Gusikowski', 'monique.parker@example.org', '2026-10-26 05:38:59', '2026-10-26 05:38:59'),
(7, 'Jack Krajcik V', 'bgorczany@example.net', '2026-10-26 05:38:59', '2026-10-26 05:38:59'),
(8, 'Dr. Thad Hegmann II', 'lacy85@example.net', '2026-10-26 05:38:59', '2026-10-26 05:38:59'),
(9, 'Ms. Genevieve Jenkins MD', 'turner.kaylin@example.com', '2026-10-26 05:38:59', '2026-10-26 05:38:59'),
(10, 'Alaina Moen', 'breynolds@example.com', '2026-10-26 05:38:59', '2026-10-26 05:38:59'),
(11, 'gokul', 'gokul@gmail.com', '2026-10-26 05:41:44', '2026-10-26 05:41:44'),
(12, 'surya', 'surya@gmail.com', '2026-10-26 06:07:15', '2026-10-26 06:07:15');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(1, 'default', '{\"uuid\":\"0c3be68a-e991-4dce-a0e8-a7df65f2af08\",\"displayName\":\"App\\\\Jobs\\\\SendOrderConfirmationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"30\",\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendOrderConfirmationJob\",\"command\":\"O:33:\\\"App\\\\Jobs\\\\SendOrderConfirmationJob\\\":2:{s:7:\\\"orderId\\\";i:1;s:11:\\\"afterCommit\\\";b:1;}\"}}', 0, NULL, 1793013104, 1793013104),
(2, 'default', '{\"uuid\":\"8f042b37-2be8-4d4b-b4eb-31cfaaa4d15f\",\"displayName\":\"App\\\\Jobs\\\\SendOrderConfirmationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"30\",\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendOrderConfirmationJob\",\"command\":\"O:33:\\\"App\\\\Jobs\\\\SendOrderConfirmationJob\\\":2:{s:7:\\\"orderId\\\";i:2;s:11:\\\"afterCommit\\\";b:1;}\"}}', 0, NULL, 1793013474, 1793013474),
(3, 'default', '{\"uuid\":\"0a58aa3e-6ebb-4052-87d7-ad3df3acfabc\",\"displayName\":\"App\\\\Jobs\\\\SendOrderConfirmationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"30\",\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendOrderConfirmationJob\",\"command\":\"O:33:\\\"App\\\\Jobs\\\\SendOrderConfirmationJob\\\":2:{s:7:\\\"orderId\\\";i:3;s:11:\\\"afterCommit\\\";b:1;}\"}}', 0, NULL, 1793014636, 1793014636);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2024_01_01_000001_create_products_table', 1),
(6, '2024_01_01_000002_create_customers_table', 1),
(7, '2024_01_01_000003_create_orders_table', 1),
(8, '2024_01_01_000004_create_order_items_table', 1),
(9, '2026_10_26_093612_create_jobs_table', 1),
(10, '2026_10_26_102706_add_amount_paid_to_orders_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `subtotal` decimal(12,2) NOT NULL COMMENT 'Sum of all item subtotals before tax',
  `tax_amount` decimal(12,2) NOT NULL COMMENT 'Total tax across all items',
  `grand_total` decimal(12,2) NOT NULL COMMENT 'subtotal + tax_amount',
  `amount_paid` decimal(12,2) DEFAULT NULL COMMENT 'Cash amount handed by customer',
  `change_amount` decimal(12,2) DEFAULT NULL COMMENT 'Change returned to customer (grand_total - amount_paid)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `subtotal`, `tax_amount`, `grand_total`, `amount_paid`, `change_amount`, `created_at`, `updated_at`) VALUES
(1, 11, 172.47, 8.62, 181.09, 181.09, 0.00, '2026-10-26 05:41:44', '2026-10-26 05:41:44'),
(2, 11, 1748.00, 209.76, 1957.76, 1957.76, 0.00, '2026-10-26 05:47:54', '2026-10-26 05:47:54'),
(3, 12, 71.36, 8.56, 79.92, 79.96, 0.03, '2026-10-26 06:07:15', '2026-10-26 06:07:15');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `unit_price` decimal(10,2) NOT NULL COMMENT 'Product price at time of order',
  `tax_percentage` decimal(5,2) NOT NULL COMMENT 'Tax rate at time of order',
  `subtotal` decimal(12,2) NOT NULL COMMENT 'unit_price * quantity',
  `tax_amount` decimal(12,2) NOT NULL COMMENT 'subtotal * tax_percentage / 100',
  `total` decimal(12,2) NOT NULL COMMENT 'subtotal + tax_amount',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`, `tax_percentage`, `subtotal`, `tax_amount`, `total`, `created_at`, `updated_at`) VALUES
(1, 1, 11, 1, 172.47, 5.00, 172.47, 8.62, 181.09, '2026-10-26 05:41:44', '2026-10-26 05:41:44'),
(2, 2, 2, 1, 1299.00, 12.00, 1299.00, 155.88, 1454.88, '2026-10-26 05:47:54', '2026-10-26 05:47:54'),
(3, 2, 8, 1, 449.00, 12.00, 449.00, 53.88, 502.88, '2026-10-26 05:47:54', '2026-10-26 05:47:54'),
(4, 3, 10, 1, 71.36, 12.00, 71.36, 8.56, 79.92, '2026-10-26 06:07:15', '2026-10-26 06:07:15');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `price` decimal(10,2) UNSIGNED NOT NULL COMMENT 'Unit price in base currency',
  `tax_percentage` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Tax rate as a percentage (e.g. 18.00)',
  `stock_quantity` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `code`, `price`, `tax_percentage`, `stock_quantity`, `created_at`, `updated_at`) VALUES
(1, 'Wireless Bluetooth Headphones', 'PROD-001', 2499.99, 18.00, 50, '2026-10-26 05:38:59', '2026-10-26 05:38:59'),
(2, 'USB-C Laptop Charger', 'PROD-002', 1299.00, 12.00, 74, '2026-10-26 05:38:59', '2026-10-26 05:47:54'),
(3, 'Mechanical Keyboard', 'PROD-003', 3499.50, 18.00, 30, '2026-10-26 05:38:59', '2026-10-26 05:38:59'),
(4, 'Ergonomic Mouse', 'PROD-004', 899.00, 12.00, 120, '2026-10-26 05:38:59', '2026-10-26 05:38:59'),
(5, 'Monitor Stand', 'PROD-005', 599.00, 5.00, 200, '2026-10-26 05:38:59', '2026-10-26 05:38:59'),
(6, 'Webcam HD 1080p', 'PROD-006', 1999.00, 18.00, 3, '2026-10-26 05:38:59', '2026-10-26 05:38:59'),
(7, 'Laptop Stand Aluminium', 'PROD-007', 749.00, 5.00, 7, '2026-10-26 05:38:59', '2026-10-26 05:38:59'),
(8, 'USB Hub 7-Port', 'PROD-008', 449.00, 12.00, 0, '2026-10-26 05:38:59', '2026-10-26 05:47:54'),
(9, 'Discontinued Cable Set', 'PROD-009', 199.00, 5.00, 0, '2026-10-26 05:38:59', '2026-10-26 05:38:59'),
(10, 'tempore dolorem exercitationem', 'YCV-2950', 71.36, 12.00, 99, '2026-10-26 05:38:59', '2026-10-26 06:07:15'),
(11, 'suscipit excepturi aut', '4T4-3610', 172.47, 5.00, 99, '2026-10-26 05:38:59', '2026-10-26 05:41:44'),
(12, 'sapiente minima ea', 'HYZ-8975', 405.58, 18.00, 100, '2026-10-26 05:38:59', '2026-10-26 05:38:59'),
(13, 'excepturi omnis nobis', 'UJ0-2621', 107.48, 0.00, 100, '2026-10-26 05:38:59', '2026-10-26 05:38:59'),
(14, 'et aut inventore', 'ZON-5520', 316.10, 28.00, 100, '2026-10-26 05:38:59', '2026-10-26 05:38:59');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_email_unique` (`email`),
  ADD KEY `customers_email_index` (`email`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_customer_id_index` (`customer_id`),
  ADD KEY `orders_created_at_index` (`created_at`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_index` (`order_id`),
  ADD KEY `order_items_product_id_index` (`product_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_code_unique` (`code`),
  ADD KEY `products_code_index` (`code`),
  ADD KEY `products_stock_quantity_index` (`stock_quantity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
