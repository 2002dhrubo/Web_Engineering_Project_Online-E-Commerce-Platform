<?php require_once __DIR__ . '/auth.php'; ?>
<link rel="stylesheet" href="css/header.css">
<header>
    <div class="header-container">
        <div class="logo">
            <a href="index.php"><span class="logo-smart">Smart</span><span class="logo-buy">Buy</span> Store</a>
        </div>
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="index.php#clothing">Clothing</a>
            <a href="index.php#accessories">Accessories</a>
            <?php if (isAdmin()): ?>
            <a href="admin/dashboard.php" class="admin-link"><i class="fas fa-cog"></i> Admin</a>
            <?php endif; ?>
        </nav>
        <div class="header-right">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search products...">
                <i class="fas fa-search"></i>
            </div>
            <?php if (isLoggedIn()): ?>
            <a href="cart.php" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-badge"><?php echo getCartCount(); ?></span>
            </a>
            <a href="my-orders.php" class="nav-icon" title="My Orders">
                <i class="fas fa-box"></i>
            </a>
            <div class="user-menu">
                <a href="profile.php" class="user-greeting" title="My Profile">
                    <i class="fas fa-user-circle"></i> <?php echo escape(getCurrentUserName()); ?>
                </a>
                <a href="logout.php" class="btn-logout" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
            <?php else: ?>
            <div class="auth-links">
                <a href="login.php" class="btn-login">Login</a>
                <a href="register.php" class="btn-register">Register</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</header>
