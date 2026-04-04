<?php 
require_once '../includes/admin_auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid form submission. Please try again.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $brand = trim($_POST['brand'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $category = $_POST['category'] ?? 'clothing';
        $description = trim($_POST['description'] ?? '');
        
        // Validation
        if (empty($name) || empty($brand) || $price <= 0) {
            $error = 'Please fill in all required fields.';
        } else {
            $imagePath = '';
            
            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['image'];
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                $maxSize = 5 * 1024 * 1024; // 5MB
                
                // Validate file type
                $fileType = mime_content_type($file['tmp_name']);
                if (!in_array($fileType, $allowedTypes)) {
                    $error = 'Invalid file type. Only JPG, JPEG, and PNG are allowed.';
                } elseif ($file['size'] > $maxSize) {
                    $error = 'File size too large. Maximum 5MB allowed.';
                } else {
                    // Generate unique filename
                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = uniqid('product_') . '_' . time() . '.' . $extension;
                    $uploadDir = '../uploads/products/';
                    $uploadPath = $uploadDir . $filename;
                    
                    // Create directory if not exists
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                        $imagePath = 'uploads/products/' . $filename;
                    } else {
                        $error = 'Failed to upload image. Please try again.';
                    }
                }
            } elseif (!empty($_POST['image_url'])) {
                // Use external URL if provided
                $imagePath = trim($_POST['image_url']);
            }
            
            if (empty($error)) {
                $conn = getConnection();
                $stmt = $conn->prepare("INSERT INTO products (name, brand, price, image, description, category) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssdsss", $name, $brand, $price, $imagePath, $description, $category);
                
                if ($stmt->execute()) {
                    setFlashMessage('success', 'Product "' . $name . '" added successfully!');
                    header('Location: products.php');
                    exit;
                } else {
                    $error = 'Failed to add product. Please try again.';
                }
                $conn->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - <?php echo SITE_NAME; ?></title>
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
                <h1><i class="fas fa-plus-circle"></i> Add Product</h1>
                <div class="header-right">
                    <a href="products.php" class="btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                </div>
            </header>

            <div class="admin-content">
                <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo escape($error); ?>
                </div>
                <?php endif; ?>
                
                <div class="admin-section">
                    <form method="POST" enctype="multipart/form-data" class="admin-form">
                        <?php echo csrfField(); ?>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name"><i class="fas fa-tag"></i> Product Name *</label>
                                <input type="text" id="name" name="name" required 
                                       value="<?php echo escape($_POST['name'] ?? ''); ?>"
                                       placeholder="Enter product name">
                            </div>
                            
                            <div class="form-group">
                                <label for="brand"><i class="fas fa-building"></i> Brand *</label>
                                <input type="text" id="brand" name="brand" required 
                                       value="<?php echo escape($_POST['brand'] ?? ''); ?>"
                                       placeholder="Enter brand name">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="price"><i class="fas fa-money-bill"></i> Price (৳ BDT) *</label>
                                <input type="number" id="price" name="price" required min="1" step="0.01"
                                       value="<?php echo escape($_POST['price'] ?? ''); ?>"
                                       placeholder="Enter price">
                            </div>
                            
                            <div class="form-group">
                                <label for="category"><i class="fas fa-folder"></i> Category *</label>
                                <select id="category" name="category" required>
                                    <option value="clothing" <?php echo (($_POST['category'] ?? '') === 'clothing') ? 'selected' : ''; ?>>Clothing</option>
                                    <option value="accessories" <?php echo (($_POST['category'] ?? '') === 'accessories') ? 'selected' : ''; ?>>Accessories</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="description"><i class="fas fa-align-left"></i> Description</label>
                            <textarea id="description" name="description" rows="4" 
                                      placeholder="Enter product description"><?php echo escape($_POST['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="image"><i class="fas fa-image"></i> Product Image</label>
                            <input type="file" id="image" name="image" accept="image/jpeg,image/jpg,image/png">
                            <small style="color: #666; display: block; margin-top: 8px;">
                                Allowed: JPG, JPEG, PNG (Max: 5MB)
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label for="image_url"><i class="fas fa-link"></i> Or Image URL</label>
                            <input type="url" id="image_url" name="image_url" 
                                   value="<?php echo escape($_POST['image_url'] ?? ''); ?>"
                                   placeholder="https://example.com/image.jpg">
                            <small style="color: #666; display: block; margin-top: 8px;">
                                Use this if you prefer an external image URL instead of uploading
                            </small>
                        </div>
                        
                        <div class="btn-group">
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-save"></i> Save Product
                            </button>
                            <a href="products.php" class="btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
