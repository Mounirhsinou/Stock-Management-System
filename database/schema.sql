-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 08, 2026 at 12:11 AM
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
-- Database: `stock_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `sku` varchar(50) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `purchase_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `selling_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `minimum_quantity` int(11) NOT NULL DEFAULT 10,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `supplier_id` int(10) UNSIGNED DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sku`, `name`, `description`, `purchase_price`, `selling_price`, `quantity`, `minimum_quantity`, `is_active`, `supplier_id`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'SKU-001', 'Laptop Dell XPS 15', 'High-performance laptop for business use', 1200.00, 1500.00, 15, 5, 1, 1, 1, '2026-01-07 22:04:34', '2026-01-07 22:30:22'),
(2, 'SKU-002', 'Wireless Mouse Logitech', 'Ergonomic wireless mouse', 25.00, 35.00, 50, 20, 1, 2, 1, '2026-01-07 22:04:34', '2026-01-07 22:30:22'),
(3, 'SKU-003', 'USB-C Cable 2m', 'High-speed USB-C charging cable', 8.00, 15.00, 100, 30, 1, 3, 1, '2026-01-07 22:04:34', '2026-01-07 22:30:22'),
(4, 'SKU-004', 'Monitor Samsung 27\"', '4K UHD monitor with HDR support', 350.00, 450.00, 8, 10, 1, 1, 1, '2026-01-07 22:04:34', '2026-01-07 22:30:22'),
(5, 'SKU-005', 'Keyboard Mechanical RGB', 'Gaming mechanical keyboard with RGB lighting', 80.00, 120.00, 3, 10, 1, 2, 1, '2026-01-07 22:04:34', '2026-01-07 22:30:22'),
(6, '006', 'demo mounir', 'demo mounir', 12.00, 100.00, 200, 300, 1, 4, 1, '2026-01-07 22:36:56', '2026-01-07 22:37:16');

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `movement_type` enum('IN','OUT') NOT NULL,
  `quantity` int(11) NOT NULL,
  `note` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `product_id`, `movement_type`, `quantity`, `note`, `created_by`, `created_at`) VALUES
(1, 1, 'IN', 20, 'Initial stock purchase', 1, '2026-01-07 22:04:34'),
(2, 1, 'OUT', 5, 'Sold to corporate client', 1, '2026-01-07 22:04:34'),
(3, 2, 'IN', 100, 'Bulk purchase from supplier', 1, '2026-01-07 22:04:34'),
(4, 2, 'OUT', 50, 'Retail sales', 1, '2026-01-07 22:04:34'),
(5, 3, 'IN', 150, 'Restocking cables', 1, '2026-01-07 22:04:34'),
(6, 3, 'OUT', 50, 'Sold with laptops', 1, '2026-01-07 22:04:34'),
(7, 4, 'IN', 10, 'New monitor shipment', 1, '2026-01-07 22:04:34'),
(8, 4, 'OUT', 2, 'Office setup', 1, '2026-01-07 22:04:34'),
(9, 5, 'IN', 15, 'Gaming peripherals stock', 1, '2026-01-07 22:04:34'),
(10, 5, 'OUT', 12, 'Gaming event sales', 1, '2026-01-07 22:04:34'),
(11, 6, 'IN', 100, 'demo', 1, '2026-01-07 22:37:16');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `contact_name`, `email`, `phone`, `address`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'TechWorld Distribution', 'John Smith', 'john@techworld.local', '555-0101', '123 Tech Lane, Silicon Valley', 1, '2026-01-07 22:30:22', '2026-01-07 22:30:22'),
(2, 'Global Peripherals Inc.', 'Sarah Jones', 'sarah@global.local', '555-0102', '456 Component Way, Austin', 1, '2026-01-07 22:30:22', '2026-01-07 22:30:22'),
(3, 'Office Max Solutions', 'Mike Brown', 'mike@officemax.local', '555-0103', '789 Supply St, Chicago', 1, '2026-01-07 22:30:22', '2026-01-07 22:30:22'),
(4, 'Mounir Hsinou', 'Mounir Hsinou', 'contactmhapp@gmail.com', '0656311642', 'Centre Zoumi Ouezzane', 1, '2026-01-07 22:35:22', '2026-01-07 22:35:22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','staff','viewer') NOT NULL DEFAULT 'viewer',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@stocksystem.local', '$2a$12$AdrFWEyNQIQ0CPVgUp39fepf1OEo.ZChC.q3s0kX7OigWv13DNVZW', 'System Administrator', 'admin', 1, '2026-01-07 22:04:34', '2026-01-07 22:43:32'),
(2, 'staff', 'staff@stocksystem.local', '$2a$12$POqW.K7EJxL48qPk0rmnKewVXB8Bhk8vt/CrtNYAjXOoK9mLQNKt6', 'Staff Member', 'staff', 1, '2026-01-07 22:04:34', '2026-01-07 22:43:54'),
(3, 'viewer', 'viewer@stocksystem.local', '$2a$12$c.n4H3P1R6FpgiMIIx1sz.ycbIjKbUXGQ2IQPRMdh3vdfU/miAFq6', 'Viewer User', 'viewer', 1, '2026-01-07 22:04:34', '2026-01-07 22:44:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `idx_sku` (`sku`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_quantity` (`quantity`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_supplier_id` (`supplier_id`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_movement_type` (`movement_type`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_movements_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
