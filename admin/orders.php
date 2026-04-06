<?php 
require_once '../includes/admin_auth.php';

$conn = getConnection();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Invalid form submission.');
    } else {
        $orderId = (int)$_POST['order_id'];
        $status = $_POST['status'];
        
        $validStatuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];
        if (in_array($status, $validStatuses)) {
            $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $orderId);
            
            if ($stmt->execute()) {
                setFlashMessage('success', 'Order status updated successfully.');
            } else {
                setFlashMessage('error', 'Failed to update order status.');
            }
        }
    }
    header('Location: orders.php');
    exit;
}

// Get filter parameters
$statusFilter = $_GET['status'] ?? '';
$dateFilter = $_GET['date'] ?? '';

// Build query
$sql = "SELECT o.*, u.name as user_name, u.email as user_email 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        WHERE 1=1";
$params = [];
$types = "";

if ($statusFilter) {
    $sql .= " AND o.status = ?";
    $params[] = $statusFilter;
    $types .= "s";
}

if ($dateFilter) {
    $sql .= " AND DATE(o.created_at) = ?";
    $params[] = $dateFilter;
    $types .= "s";
}

$sql .= " ORDER BY o.created_at DESC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get order items for each order
foreach ($orders as &$order) {
    $stmt = $conn->prepare("
        SELECT oi.*, p.name as product_name, p.image as product_image 
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
    <title>Manage Orders - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .filters-bar {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .filter-group label {
            font-weight: 500;
            color: #666;
        }
        .filter-group select,
        .filter-group input {
            padding: 8px 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
        }
        .order-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .order-info h3 {
            color: #333;
            margin-bottom: 5px;
        }
        .order-info p {
            color: #666;
            font-size: 14px;
        }
        .order-items {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin: 15px 0;
            padding: 15px 0;
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
        }
        .order-item {
            display: flex;
            align-items: center;
            gap: 10px;
            background: white;
            padding: 10px;
            border-radius: 8px;
        }
        .order-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 6px;
        }
        .order-item-info h4 {
            font-size: 14px;
            color: #333;
        }
        .order-item-info p {
            font-size: 12px;
            color: #888;
        }
        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .order-total {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }
        .status-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .status-form select {
            padding: 8px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
        }
        .btn-update {
            padding: 8px 20px;
            background: #17a2b8;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-update:hover {
            background: #138496;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2><span class="logo-smart">Smart</span><span class="logo-buy">Buy</span></h2>
                <p>Admin Panel</p>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="products.php"><i class="fas fa-box"></i> Products</a>
                <a href="orders.php" class="active"><i class="fas fa-shopping-bag"></i> Orders</a>
                <div class="nav-divider"></div>
                <a href="../index.php"><i class="fas fa-store"></i> View Store</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
            <div class="sidebar-footer">
                <p>Logged in as:</p>
                <strong><?php echo escape(getCurrentUserName()); ?></strong>
            </div>
        </aside>

        <main class="admin-main">
            <header class="admin-header">
                <h1><i class="fas fa-shopping-bag"></i> Manage Orders</h1>
            </header>

            <div class="admin-content">
                <?php displayFlashMessage(); ?>
                
                <!-- Filters -->
                <div class="admin-section">
                    <form method="GET" class="filters-bar">
                        <div class="filter-group">
                            <label>Status:</label>
                            <select name="status" onchange="this.form.submit()">
                                <option value="">All</option>
                                <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="processing" <?php echo $statusFilter === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="shipped" <?php echo $statusFilter === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Date:</label>
                            <input type="date" name="date" value="<?php echo escape($dateFilter); ?>" onchange="this.form.submit()">
                        </div>
                        <?php if ($statusFilter || $dateFilter): ?>
                        <a href="orders.php" class="btn-secondary" style="padding: 8px 15px;"><i class="fas fa-times"></i> Clear</a>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Orders List -->
                <div class="admin-section">
                    <div class="section-header">
                        <h2><i class="fas fa-list"></i> Orders (<?php echo count($orders); ?>)</h2>
                    </div>
                    
                    <?php if (empty($orders)): ?>
                    <p class="empty-table" style="text-align: center; padding: 40px;">No orders found.</p>
                    <?php else: ?>
                    
                    <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-info">
                                <h3>Order #<?php echo $order['id']; ?></h3>
                                <p><i class="fas fa-user"></i> <?php echo escape($order['user_name'] ?? 'Guest'); ?> 
                                   <?php if ($order['user_email']): ?>(<?php echo escape($order['user_email']); ?>)<?php endif; ?></p>
                                <p><i class="fas fa-calendar"></i> <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></p>
                            </div>
                            <span class="status-badge status-<?php echo $order['status']; ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </div>
                        
                        <div class="order-items">
                            <?php foreach ($order['items'] as $item): ?>
                            <div class="order-item">
                                <img src="<?php echo (strpos($item['product_image'], 'http') === 0) ? escape($item['product_image']) : '../' . escape($item['product_image']); ?>" 
                                     alt="<?php echo escape($item['product_name']); ?>"
                                     onerror="this.src='https://via.placeholder.com/50x50?text=No+Image'">
                                <div class="order-item-info">
                                    <h4><?php echo escape($item['product_name']); ?></h4>
                                    <p>Qty: <?php echo $item['quantity']; ?> × ৳<?php echo number_format($item['price']); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="order-footer">
                            <div class="order-total">
                                Total: ৳<?php echo number_format($order['total_amount']); ?>
                            </div>
                            <form method="POST" class="status-form">
                                <?php echo csrfField(); ?>
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status">
                                    <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <button type="submit" name="update_status" class="btn-update">
                                    <i class="fas fa-sync"></i> Update
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
<?php $conn->close(); ?>
