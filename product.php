<?php 
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$conn = getConnection();
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="product-detail">
        <div class="container">
            <div class="product-detail-grid">
                <div class="product-detail-image">
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                </div>
                <div class="product-detail-info">
                    <span class="category-tag"><?php echo ucfirst($product['category']); ?></span>
                    <h1><?php echo $product['name']; ?></h1>
                    <p class="brand">by <?php echo $product['brand']; ?></p>
                    <p class="price">৳<?php echo number_format($product['price']); ?></p>
                    <p class="description">
                        <?php echo $product['description'] ?? 'Premium quality ' . strtolower($product['category']) . ' from ' . $product['brand'] . '. Perfect for any occasion.'; ?>
                    </p>
                    
                    <div class="quantity-selector">
                        <label>Quantity:</label>
                        <div class="qty-controls">
                            <button class="qty-btn" id="qtyMinus">-</button>
                            <input type="number" id="quantity" value="1" min="1" max="10">
                            <button class="qty-btn" id="qtyPlus">+</button>
                        </div>
                    </div>
                    
                    <button class="btn-add-cart-large" data-id="<?php echo $product['id']; ?>">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                    
                    <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="js/main.js"></script>
</body>
</html>
<?php $conn->close(); ?>
