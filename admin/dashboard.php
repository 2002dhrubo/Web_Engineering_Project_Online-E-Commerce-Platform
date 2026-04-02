<?php 
require_once '../includes/admin_auth.php';

$conn = getConnection();

// Get totals
$totalProducts = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$totalOrders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$totalUsers = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'")->fetch_assoc()['count'];
$totalRevenue = $conn->query("SELECT SUM(total_amount) as total FROM orders")->fetch_assoc()['total'] ?? 0;

// Get recent orders
$recentOrders = $conn->query("
    SELECT o.*, u.name as user_name, u.email as user_email 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC 
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2><span class="logo-smart">Smart</span><span class="logo-buy">Buy</span></h2>
                <p>Admin Panel</p>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="products.php"><i class="fas fa-box"></i> Products</a>
                <a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
                <div class="nav-divider"></div>
                <a href="../index.php"><i class="fas fa-store"></i> View Store</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
            <div class="sidebar-footer">
                <p>Logged in as:</p>
                <strong><?php echo escape(getCurrentUserName()); ?></strong>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
                <div class="header-right">
                    <span class="date"><?php echo date('F j, Y'); ?></span>
                </div>
            </header>

            <div class="admin-content">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon products">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo number_format($totalProducts); ?></h3>
                            <p>Total Products</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon orders">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo number_format($totalOrders); ?></h3>
                            <p>Total Orders</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon users">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo number_format($totalUsers); ?></h3>
                            <p>Customers</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon revenue">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-info">
                            <h3>৳<?php echo number_format($totalRevenue); ?></h3>
                            <p>Total Revenue</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="admin-section">
                    <div class="section-header">
                        <h2><i class="fas fa-clock"></i> Recent Orders</h2>
                        <a href="orders.php" class="btn-link">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentOrders)): ?>
                                <tr>
                                    <td colspan="5" class="empty-table">No orders yet</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td>
                                        <?php echo escape($order['user_name'] ?? 'Guest'); ?>
                                        <small><?php echo escape($order['user_email'] ?? ''); ?></small>
                                    </td>
                                    <td>৳<?php echo number_format($order['total_amount']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $order['status']; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="admin-section">
                    <div class="section-header">
                        <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
                    </div>
                    <div class="quick-actions">
                        <a href="add_product.php" class="action-card">
                            <i class="fas fa-plus-circle"></i>
                            <span>Add Product</span>
                        </a>
                        <a href="products.php" class="action-card">
                            <i class="fas fa-list"></i>
                            <span>Manage Products</span>
                        </a>
                        <a href="orders.php" class="action-card">
                            <i class="fas fa-tasks"></i>
                            <span>Manage Orders</span>
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
<?php $conn->close(); ?>
