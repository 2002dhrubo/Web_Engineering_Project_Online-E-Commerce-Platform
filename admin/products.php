<?php 
require_once '../includes/admin_auth.php';

$conn = getConnection();

// Handle delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    
    // Get product image to delete
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    
    // Delete product
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    
    if ($stmt->execute()) {
        // Delete image file if it's a local upload
        if ($product && strpos($product['image'], 'uploads/') !== false) {
            $imagePath = '../' . $product['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        setFlashMessage('success', 'Product deleted successfully.');
    } else {
        setFlashMessage('error', 'Failed to delete product.');
    }
    header('Location: products.php');
    exit;
}

// Get all products
$products = $conn->query("SELECT * FROM products ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin.css">
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
                <a href="products.php" class="active"><i class="fas fa-box"></i> Products</a>
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

        <main class="admin-main">
            <header class="admin-header">
                <h1><i class="fas fa-box"></i> Manage Products</h1>
                <div class="header-right">
                    <a href="add_product.php" class="btn-primary">
                        <i class="fas fa-plus"></i> Add Product
                    </a>
                </div>
            </header>

            <div class="admin-content">
                <?php displayFlashMessage(); ?>
                
                <div class="admin-section">
                    <div class="section-header">
                        <h2><i class="fas fa-list"></i> All Products (<?php echo count($products); ?>)</h2>
                    </div>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Brand</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Added</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="7" class="empty-table">
                                        No products found. <a href="add_product.php">Add your first product</a>
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo escape($product['image']); ?>" 
                                             alt="<?php echo escape($product['name']); ?>" 
                                             class="product-thumb"
                                             onerror="this.src='https://via.placeholder.com/60x60?text=No+Image'">
                                    </td>
                                    <td><strong><?php echo escape($product['name']); ?></strong></td>
                                    <td><?php echo escape($product['brand']); ?></td>
                                    <td><?php echo ucfirst(escape($product['category'])); ?></td>
                                    <td>৳<?php echo number_format($product['price']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($product['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn-edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn-danger" onclick="confirmDelete(<?php echo $product['id']; ?>, '<?php echo escape($product['name']); ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    function confirmDelete(id, name) {
        if (confirm('Are you sure you want to delete "' + name + '"?\nThis action cannot be undone.')) {
            window.location.href = 'products.php?delete=' + id;
        }
    }
    </script>
</body>
</html>
<?php $conn->close(); ?>
