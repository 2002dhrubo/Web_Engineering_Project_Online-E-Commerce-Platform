<link rel="stylesheet" href="css/footer.css">
<footer>
    <div class="footer-container">
        <div class="footer-section">
            <h3><span class="logo-smart">Smart</span><span class="logo-buy">Buy</span> Store</h3>
            <p>Your one-stop online shop for the best products at the best prices.</p>
        </div>
        <div class="footer-section">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="cart.php">Cart</a></li>
                <?php if (isLoggedIn()): ?>
                <li><a href="profile.php">My Profile</a></li>
                <li><a href="my-orders.php">My Orders</a></li>
                <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="footer-section">
            <h4>Contact Us</h4>
            <ul>
                <li><i class="fas fa-phone"></i> +880 1XXX-XXXXXX</li>
                <li><i class="fas fa-envelope"></i> support@smartbuy.com</li>
                <li><i class="fas fa-map-marker-alt"></i> Dhaka, Bangladesh</li>
            </ul>
        </div>
        <div class="footer-section">
            <h4>Follow Us</h4>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> SmartBuy Store. All rights reserved.</p>
    </div>
</footer>
