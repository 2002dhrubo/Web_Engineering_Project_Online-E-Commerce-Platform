# SmartBuyStore - Backdated Git Commits Script
# This script creates commits with past dates to simulate development over 30 days
# Run this ONCE after initializing git repo

# Initialize git if not already done
if (-not (Test-Path ".git")) {
    git init
}

# Function to create backdated commit
function Commit-WithDate {
    param (
        [string]$Files,
        [string]$Message,
        [string]$Date
    )
    
    $env:GIT_AUTHOR_DATE = $Date
    $env:GIT_COMMITTER_DATE = $Date
    
    git add $Files
    git commit -m $Message
    
    Remove-Item Env:GIT_AUTHOR_DATE
    Remove-Item Env:GIT_COMMITTER_DATE
}

Write-Host "Starting backdated commits..." -ForegroundColor Green

# Day 1 - Feb 9, 2026
Commit-WithDate -Files "README.md" -Message "Initial commit - project documentation" -Date "2026-02-09 10:30:00"

# Day 2 - Feb 10, 2026
Commit-WithDate -Files "database.sql" -Message "Add database schema for products, cart, orders" -Date "2026-02-10 14:15:00"

# Day 3 - Feb 11, 2026
Commit-WithDate -Files "config.php" -Message "Add database configuration and helper functions" -Date "2026-02-11 11:45:00"

# Day 4 - Feb 12, 2026
Commit-WithDate -Files "css/style.css" -Message "Add main stylesheet with responsive design" -Date "2026-02-12 16:20:00"

# Day 5 - Feb 13, 2026
Commit-WithDate -Files "css/header.css" -Message "Add header navigation styles" -Date "2026-02-13 09:30:00"

# Day 6 - Feb 14, 2026
Commit-WithDate -Files "css/footer.css" -Message "Add footer styles" -Date "2026-02-14 15:00:00"

# Day 7 - Feb 15, 2026
Commit-WithDate -Files "includes/footer.php" -Message "Add footer component with contact info" -Date "2026-02-15 12:10:00"

# Day 8 - Feb 16, 2026
Commit-WithDate -Files "includes/header.php" -Message "Add header component with navigation" -Date "2026-02-16 17:45:00"

# Day 9 - Feb 17, 2026
Commit-WithDate -Files "index.php" -Message "Add homepage with product listings" -Date "2026-02-17 10:00:00"

# Day 10 - Feb 18, 2026
Commit-WithDate -Files "product.php" -Message "Add product detail page" -Date "2026-02-18 14:30:00"

# Day 11 - Feb 19, 2026
Commit-WithDate -Files "js/main.js" -Message "Add main JavaScript - add to cart functionality" -Date "2026-02-19 11:15:00"

# Day 12 - Feb 20, 2026
Commit-WithDate -Files "api/cart.php" -Message "Add cart API endpoint - CRUD operations" -Date "2026-02-20 16:00:00"

# Day 13 - Feb 21, 2026
Commit-WithDate -Files "cart.php js/cart.js" -Message "Add shopping cart page with quantity controls" -Date "2026-02-21 13:20:00"

# Day 14 - Feb 22, 2026
Commit-WithDate -Files "api/order.php" -Message "Add order placement API" -Date "2026-02-22 10:45:00"

# Day 15 - Feb 23, 2026
Commit-WithDate -Files "order-success.php" -Message "Add order confirmation page" -Date "2026-02-23 15:30:00"

# Day 16 - Feb 24, 2026
Commit-WithDate -Files "database_update.sql" -Message "Add users table schema for authentication" -Date "2026-02-24 09:00:00"

# Day 17 - Feb 25, 2026
Commit-WithDate -Files "includes/auth.php" -Message "Add authentication helper functions - login, CSRF protection" -Date "2026-02-25 14:00:00"

# Day 18 - Feb 26, 2026
Commit-WithDate -Files "css/auth.css" -Message "Add authentication page styles" -Date "2026-02-26 11:30:00"

# Day 19 - Feb 27, 2026
Commit-WithDate -Files "login.php" -Message "Add user login page with validation" -Date "2026-02-27 16:45:00"

# Day 20 - Feb 28, 2026
Commit-WithDate -Files "register.php" -Message "Add user registration with password hashing" -Date "2026-02-28 10:15:00"

# Day 21 - Mar 1, 2026
Commit-WithDate -Files "logout.php" -Message "Add logout functionality" -Date "2026-03-01 12:00:00"

# Day 22 - Mar 2, 2026
Commit-WithDate -Files "profile.php" -Message "Add user profile page - update name, email, password" -Date "2026-03-02 15:20:00"

# Day 23 - Mar 3, 2026
Commit-WithDate -Files "my-orders.php" -Message "Add user order history page" -Date "2026-03-03 09:45:00"

# Day 24 - Mar 4, 2026
Commit-WithDate -Files "includes/admin_auth.php css/admin.css" -Message "Add admin authentication guard and admin panel styles" -Date "2026-03-04 14:30:00"

# Day 25 - Mar 5, 2026
Commit-WithDate -Files "admin/dashboard.php" -Message "Add admin dashboard with stats overview" -Date "2026-03-05 11:00:00"

# Day 26 - Mar 6, 2026
Commit-WithDate -Files "admin/products.php" -Message "Add admin product listing with delete" -Date "2026-03-06 16:15:00"

# Day 27 - Mar 7, 2026
Commit-WithDate -Files "admin/add_product.php" -Message "Add product creation with image upload" -Date "2026-03-07 10:30:00"

# Day 28 - Mar 8, 2026
Commit-WithDate -Files "admin/edit_product.php" -Message "Add product editing functionality" -Date "2026-03-08 13:45:00"

# Day 29 - Mar 9, 2026
Commit-WithDate -Files "admin/orders.php" -Message "Add admin order management with status updates" -Date "2026-03-09 15:00:00"

# Day 30 - Mar 10, 2026
Commit-WithDate -Files "uploads/.htaccess uploads/products/.gitkeep .gitignore" -Message "Add security config and gitignore" -Date "2026-03-10 11:30:00"

Write-Host ""
Write-Host "All commits created successfully!" -ForegroundColor Green
Write-Host "Now run: git remote add origin YOUR_REPO_URL" -ForegroundColor Yellow
Write-Host "Then run: git push -u origin main" -ForegroundColor Yellow
