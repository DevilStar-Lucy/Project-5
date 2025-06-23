-- Database setup for missing tables
-- Run this SQL to create the required tables

-- Create customer table if not exists
CREATE TABLE IF NOT EXISTS `tbl_customer` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create payment table if not exists
CREATE TABLE IF NOT EXISTS `tbl_payment` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` int(10) UNSIGNED NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  `gateway_response` text DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create review table if not exists
CREATE TABLE IF NOT EXISTS `tbl_review` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `food_id` int(10) UNSIGNED NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `rating` tinyint(1) NOT NULL CHECK (rating >= 1 AND rating <= 5),
  `comment` text DEFAULT NULL,
  `review_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `food_id` (`food_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create refund table if not exists
CREATE TABLE IF NOT EXISTS `tbl_refund` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` int(10) UNSIGNED NOT NULL,
  `refund_id` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `refund_status` enum('processing','completed','failed') NOT NULL DEFAULT 'processing',
  `refund_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Add missing columns to existing tables if they don't exist
ALTER TABLE `tbl_order` 
ADD COLUMN IF NOT EXISTS `customer_id` int(10) UNSIGNED DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `payment_status` enum('pending','completed','failed') DEFAULT 'pending',
ADD COLUMN IF NOT EXISTS `transaction_id` varchar(100) DEFAULT NULL;

-- Insert sample customer for testing
INSERT IGNORE INTO `tbl_customer` (`id`, `full_name`, `email`, `phone`, `password`, `address`) VALUES
(1, 'John Doe', 'john@example.com', '01234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123 Main Street, Dhaka'),
(2, 'Jane Smith', 'jane@example.com', '01987654321', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '456 Oak Avenue, Chittagong');

-- Insert sample reviews
INSERT IGNORE INTO `tbl_review` (`food_id`, `customer_name`, `rating`, `comment`) VALUES
(1, 'John Doe', 5, 'Absolutely delicious! Best pizza in town.'),
(1, 'Jane Smith', 4, 'Great taste, quick delivery.'),
(2, 'John Doe', 5, 'Amazing burger, highly recommended!'),
(3, 'Jane Smith', 4, 'Fresh and tasty momo, loved it.');