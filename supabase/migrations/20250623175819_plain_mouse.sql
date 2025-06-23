/*
  # Food Ordering System Database Schema

  1. New Tables
    - `tbl_admin` - Admin user management
    - `tbl_category` - Food categories
    - `tbl_food` - Food items with category relationships
    - `tbl_customer` - Customer authentication and profiles
    - `tbl_order` - Order management with customer relationships
    - `tbl_payment` - Payment transaction tracking
    - `tbl_refund` - Refund management
    - `tbl_review` - Customer reviews for food items
    - `tbl_payment_config` - Payment gateway configurations

  2. Security
    - Enable RLS on all tables
    - Add policies for authenticated users
    - Secure admin access

  3. Features
    - Bangladesh payment gateway support (bKash, Nagad, Upay, Rocket)
    - Customer authentication system
    - Order tracking and management
    - Review and rating system
*/

-- Admin table for backend management
CREATE TABLE IF NOT EXISTS tbl_admin (
    id SERIAL PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Categories table
CREATE TABLE IF NOT EXISTS tbl_category (
    id SERIAL PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    image_name VARCHAR(255) DEFAULT '',
    featured VARCHAR(3) DEFAULT 'No' CHECK (featured IN ('Yes', 'No')),
    active VARCHAR(3) DEFAULT 'Yes' CHECK (active IN ('Yes', 'No')),
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Food items table
CREATE TABLE IF NOT EXISTS tbl_food (
    id SERIAL PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_name VARCHAR(255) DEFAULT '',
    category_id INTEGER REFERENCES tbl_category(id) ON DELETE SET NULL,
    featured VARCHAR(3) DEFAULT 'No' CHECK (featured IN ('Yes', 'No')),
    active VARCHAR(3) DEFAULT 'Yes' CHECK (active IN ('Yes', 'No')),
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Customer table for authentication
CREATE TABLE IF NOT EXISTS tbl_customer (
    id SERIAL PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    address TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Orders table
CREATE TABLE IF NOT EXISTS tbl_order (
    id SERIAL PRIMARY KEY,
    customer_id INTEGER REFERENCES tbl_customer(id) ON DELETE SET NULL,
    food VARCHAR(150) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    qty INTEGER NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    order_date TIMESTAMPTZ DEFAULT NOW(),
    status VARCHAR(20) DEFAULT 'Ordered' CHECK (status IN ('Ordered', 'Confirmed (COD)', 'Paid', 'On Delivery', 'Delivered', 'Cancelled', 'Refunded')),
    customer_name VARCHAR(100) NOT NULL,
    customer_contact VARCHAR(20) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_address TEXT NOT NULL,
    payment_status VARCHAR(20) DEFAULT 'pending' CHECK (payment_status IN ('pending', 'completed', 'failed', 'refunded')),
    transaction_id VARCHAR(100) DEFAULT NULL
);

-- Payment table for tracking all payments
CREATE TABLE IF NOT EXISTS tbl_payment (
    id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL REFERENCES tbl_order(id) ON DELETE CASCADE,
    payment_method VARCHAR(20) NOT NULL CHECK (payment_method IN ('bkash', 'nagad', 'upay', 'rocket', 'dutch_bangla', 'brac_bank', 'city_bank', 'cod')),
    transaction_id VARCHAR(100) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    account_number VARCHAR(50) DEFAULT NULL,
    payment_status VARCHAR(20) DEFAULT 'pending' CHECK (payment_status IN ('pending', 'completed', 'failed', 'refunded')),
    payment_date TIMESTAMPTZ DEFAULT NOW(),
    gateway_response TEXT DEFAULT NULL
);

-- Refund table
CREATE TABLE IF NOT EXISTS tbl_refund (
    id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL REFERENCES tbl_order(id) ON DELETE CASCADE,
    refund_id VARCHAR(100) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    refund_status VARCHAR(20) DEFAULT 'processing' CHECK (refund_status IN ('processing', 'completed', 'failed')),
    refund_date TIMESTAMPTZ DEFAULT NOW()
);

-- Reviews table
CREATE TABLE IF NOT EXISTS tbl_review (
    id SERIAL PRIMARY KEY,
    food_id INTEGER NOT NULL REFERENCES tbl_food(id) ON DELETE CASCADE,
    customer_name VARCHAR(100) NOT NULL,
    rating INTEGER NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    review_date TIMESTAMPTZ DEFAULT NOW()
);

-- Payment gateway configurations
CREATE TABLE IF NOT EXISTS tbl_payment_config (
    id SERIAL PRIMARY KEY,
    gateway_name VARCHAR(50) NOT NULL,
    api_key VARCHAR(255),
    secret_key VARCHAR(255),
    base_url VARCHAR(255),
    is_active VARCHAR(3) DEFAULT 'No' CHECK (is_active IN ('Yes', 'No')),
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Enable Row Level Security
ALTER TABLE tbl_admin ENABLE ROW LEVEL SECURITY;
ALTER TABLE tbl_category ENABLE ROW LEVEL SECURITY;
ALTER TABLE tbl_food ENABLE ROW LEVEL SECURITY;
ALTER TABLE tbl_customer ENABLE ROW LEVEL SECURITY;
ALTER TABLE tbl_order ENABLE ROW LEVEL SECURITY;
ALTER TABLE tbl_payment ENABLE ROW LEVEL SECURITY;
ALTER TABLE tbl_refund ENABLE ROW LEVEL SECURITY;
ALTER TABLE tbl_review ENABLE ROW LEVEL SECURITY;
ALTER TABLE tbl_payment_config ENABLE ROW LEVEL SECURITY;

-- Policies for public access to categories and food (read-only)
CREATE POLICY "Allow public read access to categories" ON tbl_category
    FOR SELECT USING (true);

CREATE POLICY "Allow public read access to food" ON tbl_food
    FOR SELECT USING (true);

CREATE POLICY "Allow public read access to reviews" ON tbl_review
    FOR SELECT USING (true);

-- Policies for customers
CREATE POLICY "Customers can read their own data" ON tbl_customer
    FOR SELECT USING (auth.uid()::text = id::text);

CREATE POLICY "Customers can update their own data" ON tbl_customer
    FOR UPDATE USING (auth.uid()::text = id::text);

CREATE POLICY "Allow customer registration" ON tbl_customer
    FOR INSERT WITH CHECK (true);

-- Policies for orders
CREATE POLICY "Customers can read their own orders" ON tbl_order
    FOR SELECT USING (auth.uid()::text = customer_id::text);

CREATE POLICY "Customers can create orders" ON tbl_order
    FOR INSERT WITH CHECK (true);

CREATE POLICY "Allow public order creation" ON tbl_order
    FOR INSERT WITH CHECK (true);

-- Policies for payments
CREATE POLICY "Customers can read their own payments" ON tbl_payment
    FOR SELECT USING (
        EXISTS (
            SELECT 1 FROM tbl_order 
            WHERE tbl_order.id = tbl_payment.order_id 
            AND (auth.uid()::text = tbl_order.customer_id::text OR auth.uid() IS NULL)
        )
    );

CREATE POLICY "Allow payment creation" ON tbl_payment
    FOR INSERT WITH CHECK (true);

-- Policies for reviews
CREATE POLICY "Allow public review creation" ON tbl_review
    FOR INSERT WITH CHECK (true);

-- Policies for refunds
CREATE POLICY "Customers can read their own refunds" ON tbl_refund
    FOR SELECT USING (
        EXISTS (
            SELECT 1 FROM tbl_order 
            WHERE tbl_order.id = tbl_refund.order_id 
            AND (auth.uid()::text = tbl_order.customer_id::text OR auth.uid() IS NULL)
        )
    );

-- Admin policies (for service role)
CREATE POLICY "Service role can manage all data" ON tbl_admin
    FOR ALL USING (auth.role() = 'service_role');

CREATE POLICY "Service role can manage categories" ON tbl_category
    FOR ALL USING (auth.role() = 'service_role');

CREATE POLICY "Service role can manage food" ON tbl_food
    FOR ALL USING (auth.role() = 'service_role');

CREATE POLICY "Service role can manage orders" ON tbl_order
    FOR ALL USING (auth.role() = 'service_role');

CREATE POLICY "Service role can manage payments" ON tbl_payment
    FOR ALL USING (auth.role() = 'service_role');

CREATE POLICY "Service role can manage refunds" ON tbl_refund
    FOR ALL USING (auth.role() = 'service_role');

CREATE POLICY "Service role can manage payment config" ON tbl_payment_config
    FOR ALL USING (auth.role() = 'service_role');

-- Insert default admin user (password: admin123)
INSERT INTO tbl_admin (full_name, username, password) VALUES 
('Administrator', 'admin', MD5('admin123'));

-- Insert sample categories
INSERT INTO tbl_category (title, featured, active) VALUES
('Pizza', 'Yes', 'Yes'),
('Burger', 'Yes', 'Yes'),
('Momo', 'Yes', 'Yes'),
('Biryani', 'Yes', 'Yes'),
('Chicken', 'No', 'Yes'),
('Fish', 'No', 'Yes'),
('Vegetarian', 'No', 'Yes'),
('Desserts', 'No', 'Yes');

-- Insert sample food items
INSERT INTO tbl_food (title, description, price, category_id, featured, active) VALUES
('Margherita Pizza', 'Classic pizza with tomato sauce, mozzarella cheese, and fresh basil', 450.00, 1, 'Yes', 'Yes'),
('Chicken Burger', 'Juicy grilled chicken breast with lettuce, tomato, and special sauce', 320.00, 2, 'Yes', 'Yes'),
('Chicken Momo', 'Steamed dumplings filled with seasoned chicken and vegetables', 180.00, 3, 'Yes', 'Yes'),
('Chicken Biryani', 'Aromatic basmati rice cooked with tender chicken and traditional spices', 280.00, 4, 'Yes', 'Yes'),
('Beef Burger', 'Premium beef patty with cheese, lettuce, and tomato', 380.00, 2, 'No', 'Yes'),
('Vegetable Momo', 'Healthy steamed dumplings with mixed vegetables', 150.00, 3, 'No', 'Yes');

-- Insert payment gateway configurations
INSERT INTO tbl_payment_config (gateway_name, base_url, is_active) VALUES
('bkash', 'https://tokenized.pay.bka.sh/v1.2.0-beta', 'Yes'),
('nagad', 'https://api.mynagad.com', 'Yes'),
('upay', 'https://api.upay.com.bd', 'Yes'),
('rocket', 'https://api.rocket.com.bd', 'Yes');

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_food_category ON tbl_food(category_id);
CREATE INDEX IF NOT EXISTS idx_order_customer ON tbl_order(customer_id);
CREATE INDEX IF NOT EXISTS idx_order_status ON tbl_order(status);
CREATE INDEX IF NOT EXISTS idx_payment_order ON tbl_payment(order_id);
CREATE INDEX IF NOT EXISTS idx_review_food ON tbl_review(food_id);
CREATE INDEX IF NOT EXISTS idx_customer_email ON tbl_customer(email);

-- Create function to update updated_at timestamp
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Create trigger for customer table
CREATE TRIGGER update_customer_updated_at 
    BEFORE UPDATE ON tbl_customer 
    FOR EACH ROW 
    EXECUTE FUNCTION update_updated_at_column();