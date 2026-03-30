<?php 
require_once 'config.php';
require_once 'includes/auth.php';

// Require login to view orders
requireLogin();

$conn = getConnection();
$user_id = getCurrentUserId();

// Get user's orders
$stmt = $conn->prepare("
    SELECT o.* FROM orders o 
    WHERE o.user_id = ? 
    ORDER BY o.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get order items for each order
foreach ($orders as &$order) {
    $stmt = $conn->prepare("
        SELECT oi.*, p.name as product_name, p.image as product_image, p.brand 
        FROM order_items oi 
        JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?
    ");
    $stmt->bind_param("i", $order['id']);
    $stmt->execute();
    $order['items'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .orders-section {
            padding: 120px 20px 60px;
            max-width: 1000px;
            margin: 0 auto;
        }
        .page-title {
            font-size: 28px;
            color: #333;
            margin-bottom: 30px;
        }
        .page-title i {
            color: #667eea;
            margin-right: 10px;
        }
        .order-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 25px;
            overflow: hidden;
        }
        .order-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .order-header h3 {
            font-size: 18px;
        }
        .order-date {
            font-size: 14px;
            opacity: 0.9;
        }
        .order-body {
            padding: 20px 25px;
        }
        .order-items {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .order-item {
            display: flex;
            gap: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .order-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .order-item img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
        }
        .order-item-info {
            flex: 1;
        }
        .order-item-info h4 {
            color: #333;
            margin-bottom: 5px;
        }
        .order-item-info .brand {
            color: #888;
            font-size: 13px;
        }
        .order-item-info .qty-price {
            color: #667eea;
            font-weight: 600;
            margin-top: 5px;
        }
        .order-footer {
            padding: 15px 25px;
            background: #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .order-status {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #cce5ff; color: #004085; }
        .status-shipped { background: #d4edda; color: #155724; }
        .status-completed { background: #c3e6cb; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .order-total {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }
        .empty-orders {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }
        .empty-orders i {
            font-size: 60px;
            color: #ddd;
            margin-bottom: 20px;
        }
        .empty-orders h2 {
            color: #666;
            margin-bottom: 10px;
        }
        .empty-orders p {
            color: #888;
            margin-bottom: 25px;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="orders-section">
        <h1 class="page-title"><i class="fas fa-box"></i> My Orders</h1>
        
        <?php if (empty($orders)): ?>
        <div class="empty-orders">
            <i class="fas fa-shopping-bag"></i>
            <h2>No orders yet</h2>
            <p>You haven't placed any orders yet. Start shopping!</p>
            <a href="index.php" class="btn-primary">Browse Products</a>
        </div>
        <?php else: ?>
        
        <?php foreach ($orders as $order): ?>
        <div class="order-card">
            <div class="order-header">
                <div>
                    <h3>Order #<?php echo $order['id']; ?></h3>
                    <span class="order-date">
                        <i class="fas fa-calendar"></i> 
                        <?php echo date('F j, Y - g:i A', strtotime($order['created_at'])); ?>
                    </span>
                </div>
            </div>
            <div class="order-body">
                <div class="order-items">
                    <?php foreach ($order['items'] as $item): ?>
                    <div class="order-item">
                        <img src="<?php echo (strpos($item['product_image'], 'http') === 0) ? escape($item['product_image']) : escape($item['product_image']); ?>" 
                             alt="<?php echo escape($item['product_name']); ?>"
                             onerror="this.src='https://via.placeholder.com/70x70?text=No+Image'">
                        <div class="order-item-info">
                            <h4><?php echo escape($item['product_name']); ?></h4>
                            <p class="brand"><?php echo escape($item['brand']); ?></p>
                            <p class="qty-price">Qty: <?php echo $item['quantity']; ?> × ৳<?php echo number_format($item['price']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="order-footer">
                <span class="order-status status-<?php echo $order['status']; ?>">
                    <?php echo ucfirst($order['status']); ?>
                </span>
                <span class="order-total">Total: ৳<?php echo number_format($order['total_amount']); ?></span>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php endif; ?>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>
