<?php 
require_once 'config.php';
require_once 'includes/auth.php';

// Require login
requireLogin();

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order = null;

if ($order_id > 0) {
    $conn = getConnection();
    $user_id = getCurrentUserId();
    
    // Verify order belongs to current user
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $conn->close();
}

// Redirect if order not found or doesn't belong to user
if (!$order) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="order-success">
        <div class="container">
            <div class="success-card">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1>Order Placed Successfully!</h1>
                <p class="success-message">Thank you for shopping with Smart Buy Store</p>
                
                <?php if ($order): ?>
                <div class="order-details">
                    <div class="detail-row">
                        <span>Order ID:</span>
                        <span>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></span>
                    </div>
                    <div class="detail-row">
                        <span>Total Amount:</span>
                        <span>৳<?php echo number_format($order['total_amount']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span>Status:</span>
                        <span class="status-badge"><?php echo ucfirst($order['status']); ?></span>
                    </div>
                </div>
                <?php endif; ?>
                
                <a href="index.php" class="btn-primary">
                    <i class="fas fa-shopping-bag"></i> Continue Shopping
                </a>
                <a href="my-orders.php" class="btn-secondary" style="margin-left: 15px; padding: 15px 30px; background: #6c757d; color: white; border-radius: 30px; text-decoration: none;">
                    <i class="fas fa-list"></i> View All Orders
                </a>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
