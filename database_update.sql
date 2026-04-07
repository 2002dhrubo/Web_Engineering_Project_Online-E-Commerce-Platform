-- SmartBuyStore RBAC Update
-- Run this in phpMyAdmin to add user authentication

USE smartbuy_db;

-- Users table for authentication
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add user_id to cart table
ALTER TABLE cart ADD COLUMN user_id INT NULL AFTER id;
ALTER TABLE cart ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

-- Add user_id to orders table  
ALTER TABLE orders ADD COLUMN user_id INT NULL AFTER id;
ALTER TABLE orders ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;

-- Create default admin account (password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('Admin', 'admin@smartbuy.com', '$2y$10$FZZpHS8XZjBoOfnqTi8Utu9RGYeOGjufLdVPZcPT0CWSlKnYkDZ.u', 'admin');

-- Note: The password hash above is for 'admin123'
-- Generated using: password_hash('admin123', PASSWORD_DEFAULT)
