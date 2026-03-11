-- Smart Buy Store Database Setup
-- Run this in phpMyAdmin or MySQL CLI

CREATE DATABASE IF NOT EXISTS smartbuy_db;
USE smartbuy_db;

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    brand VARCHAR(100),
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(500),
    description TEXT,
    category ENUM('clothing', 'accessories') DEFAULT 'clothing',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cart table
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100) NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100),
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Sample products
INSERT INTO products (name, brand, price, image, category) VALUES
('Classic T-Shirt', 'Nike', 599, 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=300', 'clothing'),
('Denim Jeans', 'Levis', 1299, 'https://images.unsplash.com/photo-1542272604-787c3835535d?w=300', 'clothing'),
('Summer Dress', 'Zara', 899, 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=300', 'clothing'),
('Casual Shirt', 'H&M', 799, 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=300', 'clothing'),
('Leather Watch', 'Fossil', 2499, 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=300', 'accessories'),
('Sunglasses', 'Ray-Ban', 1999, 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=300', 'accessories'),
('Handbag', 'Guess', 1599, 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=300', 'accessories'),
('Sneakers', 'Adidas', 2999, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=300', 'clothing');
