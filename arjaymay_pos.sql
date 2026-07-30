-- ======================================================
-- ARJAYMAY SUTUKIL POS - Complete Database Schema
-- ======================================================

CREATE DATABASE IF NOT EXISTS arjaymay_pos;
USE arjaymay_pos;

-- ======================================================
-- USERS TABLE
-- ======================================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    role ENUM('admin', 'cashier') DEFAULT 'cashier',
    status ENUM('active', 'inactive') DEFAULT 'active',
    is_first_login BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ======================================================
-- CATEGORIES TABLE
-- ======================================================
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ======================================================
-- MENU ITEMS TABLE
-- ======================================================
CREATE TABLE menu_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    cost DECIMAL(10,2) DEFAULT 0.00,
    stock INT DEFAULT 0,
    min_stock INT DEFAULT 10,
    status ENUM('available', 'low_stock', 'out_of_stock') DEFAULT 'available',
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_category (category_id),
    INDEX idx_status (status)
);

-- ======================================================
-- ORDERS TABLE
-- ======================================================
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(20) UNIQUE NOT NULL,
    cashier_id INT NOT NULL,
    order_type ENUM('dine_in', 'take_out') DEFAULT 'dine_in',
    table_number VARCHAR(10) DEFAULT NULL,
    customer_name VARCHAR(100) DEFAULT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    tax DECIMAL(10,2) DEFAULT 0.00,
    takeout_fee DECIMAL(10,2) DEFAULT 0.00,
    discount DECIMAL(10,2) DEFAULT 0.00,
    discount_type ENUM('none', 'senior', 'pwd') DEFAULT 'none',
    discount_id_number VARCHAR(50) DEFAULT NULL,
    total DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'card', 'gcash') DEFAULT 'cash',
    payment_amount DECIMAL(10,2) DEFAULT 0.00,
    change_amount DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    kitchen_status ENUM('pending', 'preparing', 'ready', 'served') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cashier_id) REFERENCES users(id),
    INDEX idx_cashier (cashier_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
);

-- ======================================================
-- ORDER ITEMS TABLE
-- ======================================================
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    notes VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id),
    INDEX idx_order (order_id)
);

-- ======================================================
-- PAYMENTS TABLE
-- ======================================================
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'card', 'gcash') NOT NULL,
    reference_number VARCHAR(50),
    status ENUM('pending', 'completed', 'failed') DEFAULT 'completed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- ======================================================
-- ACTIVITY LOGS TABLE
-- ======================================================
CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at)
);

-- ======================================================
-- INITIAL DATA
-- ======================================================

-- Insert Default Admin (password: admin123)
INSERT INTO users (username, password, full_name, email, phone, role, is_first_login) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin@arjaymay.com', '09519334143', 'admin', FALSE);

-- Insert Categories
INSERT INTO categories (name, description, sort_order) VALUES
('Chicken', 'Chicken dishes', 1),
('Pork', 'Pork dishes', 2),
('Beef', 'Beef dishes', 3),
('Fish', 'Fish dishes', 4),
('Squid', 'Squid dishes', 5),
('Shrimp', 'Shrimp dishes', 6),
('Vegetable', 'Vegetable dishes', 7),
('Noodles', 'Noodle dishes', 8),
('Breakfast', 'Breakfast meals', 9),
('Beverages', 'Drinks and beverages', 10),
('We Also Serve', 'Additional items', 11);

-- Insert Menu Items
INSERT INTO menu_items (category_id, name, price, stock, min_stock, status) VALUES
-- We Also Serve (category_id = 11)
(11, 'Plain Rice', 20.00, 200, 10, 'available'),
(11, 'Garlic Rice', 30.00, 150, 10, 'available'),
(11, 'French Fries', 80.00, 60, 10, 'available'),
(11, 'Scallops', 150.00, 30, 10, 'available'),
(11, 'Lechon Manok', 300.00, 20, 10, 'available'),
(11, 'Liempo', 280.00, 25, 10, 'available'),
-- Chicken (category_id = 1)
(1, 'Chicken Tinola', 150.00, 50, 10, 'available'),
(1, 'Chicken Sinigang', 150.00, 50, 10, 'available'),
(1, 'Chicken Spicy', 150.00, 5, 10, 'low_stock'),
(1, 'Chicken Curry', 150.00, 50, 10, 'available'),
(1, 'Chicken Afritada', 150.00, 0, 10, 'out_of_stock'),
(1, 'Chicken Adobo', 150.00, 50, 10, 'available'),
(1, 'Fried Chicken', 150.00, 50, 10, 'available'),
-- Pork (category_id = 2)
(2, 'Pork Curry', 150.00, 50, 10, 'available'),
(2, 'Pork Menudo', 150.00, 50, 10, 'available'),
(2, 'Pork Steak', 150.00, 50, 10, 'available'),
(2, 'Sweet & Sour Pork', 150.00, 50, 10, 'available'),
(2, 'Pork Adobo', 150.00, 50, 10, 'available'),
(2, 'Pork Sinigang', 150.00, 50, 10, 'available'),
(2, 'Pork Afritada', 150.00, 50, 10, 'available'),
-- Beef (category_id = 3)
(3, 'Beef Steak', 150.00, 40, 10, 'available'),
(3, 'Beef with Onion', 150.00, 40, 10, 'available'),
(3, 'Beef Curry', 150.00, 40, 10, 'available'),
(3, 'Beef Afritada', 150.00, 40, 10, 'available'),
(3, 'Beef Menudo', 150.00, 40, 10, 'available'),
-- Fish (category_id = 4)
(4, 'Sweet & Sour Fish', 150.00, 30, 10, 'available'),
(4, 'Grilled Fish', 150.00, 30, 10, 'available'),
(4, 'Fish Fillet', 150.00, 30, 10, 'available'),
(4, 'Fish Salad', 150.00, 30, 10, 'available'),
(4, 'Steam Fish', 150.00, 30, 10, 'available'),
(4, 'Fish Soup', 150.00, 30, 10, 'available'),
-- Squid (category_id = 5)
(5, 'Grilled Squid', 150.00, 25, 10, 'available'),
(5, 'Calamares', 150.00, 25, 10, 'available'),
(5, 'Squid Adobo', 150.00, 25, 10, 'available'),
(5, 'Squid Chili Sauce', 150.00, 25, 10, 'available'),
(5, 'Squid Curry', 150.00, 25, 10, 'available'),
-- Shrimp (category_id = 6)
(6, 'Shrimp Chili Sauce', 150.00, 25, 10, 'available'),
(6, 'Garlic with Butter', 150.00, 25, 10, 'available'),
(6, 'Shrimp Sinigang', 150.00, 25, 10, 'available'),
(6, 'Shrimp BreadCrumbs', 150.00, 25, 10, 'available'),
(6, 'Garlic with Onion', 150.00, 25, 10, 'available'),
-- Vegetable (category_id = 7)
(7, 'Chopsuey', 130.00, 35, 10, 'available'),
(7, 'Stir Fry Veg', 130.00, 35, 10, 'available'),
(7, 'Vegetable Curry', 130.00, 35, 10, 'available'),
(7, 'Vegetable Salad', 130.00, 35, 10, 'available'),
-- Noodles (category_id = 8)
(8, 'Bam-e', 130.00, 40, 10, 'available'),
(8, 'Pancit Canton', 130.00, 40, 10, 'available'),
(8, 'Bihon Guisado', 130.00, 40, 10, 'available'),
(8, 'Sutanghon Guisado', 130.00, 40, 10, 'available'),
(8, 'Chicken Mami', 130.00, 40, 10, 'available'),
(8, 'Lomie', 130.00, 40, 10, 'available'),
(8, 'Sutanghon Soup', 130.00, 40, 10, 'available'),
-- Breakfast (category_id = 9)
(9, 'Fried Egg', 20.00, 100, 10, 'available'),
(9, 'Scrambled Egg', 50.00, 100, 10, 'available'),
(9, 'Boiled Egg', 30.00, 100, 10, 'available'),
(9, 'Egg', 20.00, 100, 10, 'available'),
(9, 'Hotdog w Egg', 50.00, 50, 10, 'available'),
(9, 'Hotdog', 30.00, 50, 10, 'available'),
(9, 'Beef Tapa w Egg', 80.00, 40, 10, 'available'),
(9, 'Corned Beef', 80.00, 45, 10, 'available'),
-- Beverages (category_id = 10)
(10, 'Nature Spring 500ml', 25.00, 60, 10, 'available'),
(10, 'Nature Spring 1L', 35.00, 50, 10, 'available'),
(10, 'San Miguel Pilsen', 60.00, 80, 10, 'available'),
(10, 'Mountain Dew', 25.00, 60, 10, 'available'),
(10, 'San Miguel Light', 60.00, 80, 10, 'available'),
(10, 'Pepsi Small', 20.00, 100, 10, 'available'),
(10, '7up Small', 20.00, 100, 10, 'available'),
(10, 'Coke/Sprite 1L', 55.00, 60, 10, 'available'),
(10, 'Coke/Sprite Small', 20.00, 100, 10, 'available');