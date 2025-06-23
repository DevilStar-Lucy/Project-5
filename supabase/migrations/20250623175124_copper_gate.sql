-- Create database and tables for the food ordering system

-- Create database
CREATE DATABASE IF NOT EXISTS WEB_Project;
USE WEB_Project;

-- Admin table
CREATE TABLE IF NOT EXISTS tbl_admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS tbl_category (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    image_name VARCHAR(255) DEFAULT '',
    featured ENUM('Yes', 'No') DEFAULT 'No',
    active ENUM('Yes', 'No') DEFAULT 'Yes',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Food items table
CREATE TABLE IF NOT EXISTS tbl_food (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_name VARCHAR(255) DEFAULT '',
    category_id INT,
    featured ENUM('Yes', 'No') DEFAULT 'No',
    active ENUM('Yes', 'No') DEFAULT 'Yes',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES tbl_category(id) ON DELETE SET NULL
);

-- Customer table for authentication
CREATE TABLE IF NOT EXISTS tbl_customer (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Orders table
CREATE TABLE IF NOT EXISTS tbl_order (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    food VARCHAR(150) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    qty INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Ordered', 'Confirmed (COD)', 'Paid', 'On Delivery', 'Delivered', 'Cancelled', 'Refunded') DEFAULT 'Ordered',
    customer_name VARCHAR(100) NOT NULL,
    customer_contact VARCHAR(20) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_address TEXT NOT NULL,
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    transaction_id VARCHAR(100) DEFAULT NULL,
    FOREIGN KEY (customer_id) REFERENCES tbl_customer(id) ON DELETE SET NULL
);

-- Payment table for tracking all payments
CREATE TABLE IF NOT EXISTS tbl_payment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_method ENUM('bkash', 'nagad', 'upay', 'rocket', 'dutch_bangla', 'brac_bank', 'city_bank', 'cod') NOT NULL,
    transaction_id VARCHAR(100) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    account_number VARCHAR(50) DEFAULT NULL,
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    gateway_response TEXT DEFAULT NULL,
    FOREIGN KEY (order_id) REFERENCES tbl_order(id) ON DELETE CASCADE
);

-- Refund table
CREATE TABLE IF NOT EXISTS tbl_refund (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    refund_id VARCHAR(100) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    refund_status ENUM('processing', 'completed', 'failed') DEFAULT 'processing',
    refund_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES tbl_order(id) ON DELETE CASCADE
);

-- Reviews table
CREATE TABLE IF NOT EXISTS tbl_review (
    id INT AUTO_INCREMENT PRIMARY KEY,
    food_id INT NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (food_id) REFERENCES tbl_food(id) ON DELETE CASCADE
);

-- Payment gateway configurations
CREATE TABLE IF NOT EXISTS tbl_payment_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gateway_name VARCHAR(50) NOT NULL,
    api_key VARCHAR(255),
    secret_key VARCHAR(255),
    base_url VARCHAR(255),
    is_active ENUM('Yes', 'No') DEFAULT 'No',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user
INSERT INTO tbl_admin (full_name, username, password) VALUES 
('Administrator', 'admin', MD5('admin123'));

-- Insert payment gateway configurations
INSERT INTO tbl_payment_config (gateway_name, base_url, is_active) VALUES
('bkash', 'https://tokenized.pay.bka.sh/v1.2.0-beta', 'Yes'),
('nagad', 'https://api.mynagad.com', 'Yes'),
('upay', 'https://api.upay.com.bd', 'Yes'),
('rocket', 'https://api.rocket.com.bd', 'Yes');