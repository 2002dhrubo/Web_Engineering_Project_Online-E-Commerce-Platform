# Smart Buy Store

A simple e-commerce website built with HTML, CSS, JavaScript, and PHP/MySQL.

## Features

- Product catalog with categories (Clothing & Accessories)
- Product detail pages
- Shopping cart with add/update/remove functionality
- Order placement system
- Search functionality
- Responsive design

## Tech Stack

- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Backend:** PHP 7+
- **Database:** MySQL
- **Server:** Apache (XAMPP)

## Prerequisites

- [XAMPP](https://www.apachefriends.org/) installed on your system

## Installation

### 1. Copy Project to XAMPP

Copy the `SmartBuyStore` folder to your XAMPP htdocs directory:

```
C:\xampp\htdocs\SmartBuyStore
```

### 2. Start XAMPP Services

1. Open XAMPP Control Panel
2. Start **Apache**
3. Start **MySQL**

### 3. Create Database

**Option A: Using phpMyAdmin (Recommended)**
1. Open browser and go to: `http://localhost/phpmyadmin`
2. Click "Import" tab
3. Click "Choose File" and select `SmartBuyStore/database.sql`
4. Click "Go" to execute

**Option B: Manual SQL**
1. In phpMyAdmin, click "SQL" tab
2. Copy and paste contents of `database.sql`
3. Click "Go"

### 4. Configure Database Connection (if needed)

Edit `config.php` if your MySQL credentials differ from default:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');           // Default XAMPP has empty password
define('DB_NAME', 'smartbuy_db');
```

### 5. Access the Website

Open your browser and navigate to:

```
http://localhost/SmartBuyStore
```

## Project Structure

```
SmartBuyStore/
├── api/
│   ├── cart.php          # Cart API (GET, POST, PUT, DELETE)
│   └── order.php         # Order placement API
├── css/
│   ├── style.css         # Main styles
│   ├── header.css        # Header styles
│   └── footer.css        # Footer styles
├── includes/
│   ├── header.php        # Reusable header
│   └── footer.php        # Reusable footer
├── js/
│   ├── main.js           # Main JavaScript
│   └── cart.js           # Cart page JavaScript
├── config.php            # Database configuration
├── index.php             # Homepage
├── product.php           # Product detail page
├── cart.php              # Shopping cart page
├── order-success.php     # Order confirmation page
├── database.sql          # Database schema & sample data
└── README.md             # This file
```

## Database Schema

- **products** - Product catalog
- **cart** - Shopping cart items (session-based)
- **orders** - Completed orders
- **order_items** - Order line items

## Screenshots

### Homepage
- Hero banner
- Product grid with clothing and accessories sections
- Search functionality

### Product Detail
- Large product image
- Quantity selector
- Add to cart button

### Shopping Cart
- List of cart items
- Quantity controls
- Order summary
- Place order button

### Order Success
- Order confirmation with order ID
- Total amount display

## License

This project is open source and available for educational purposes.
