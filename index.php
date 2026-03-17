<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Welcome to <span>Smart Buy</span> Store</h1>
            <p>Discover the latest trends in fashion and accessories</p>
            <a href="#clothing" class="btn-primary">Shop Now</a>
        </div>
    </section>

    <!-- Clothing Section -->
    <section id="clothing" class="products-section">
        <div class="container">
            <h2 class="section-title">Clothing</h2>
            <div class="products-grid">
                <?php
                $conn = getConnection();
                $result = $conn->query("SELECT * FROM products WHERE category = 'clothing'");
                while ($product = $result->fetch_assoc()):
                ?>
                <div class="product-card">
                    <a href="product.php?id=<?php echo $product['id']; ?>">
                        <div class="product-image">
                            <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        </div>
                        <div class="product-info">
                            <h3><?php echo $product['name']; ?></h3>
                            <p class="brand"><?php echo $product['brand']; ?></p>
                            <p class="price">৳<?php echo number_format($product['price']); ?></p>
                        </div>
                    </a>
                    <button class="btn-add-cart" data-id="<?php echo $product['id']; ?>">
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </button>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Accessories Section -->
    <section id="accessories" class="products-section accessories-bg">
        <div class="container">
            <h2 class="section-title">Accessories</h2>
            <div class="products-grid">
                <?php
                $result = $conn->query("SELECT * FROM products WHERE category = 'accessories'");
                while ($product = $result->fetch_assoc()):
                ?>
                <div class="product-card">
                    <a href="product.php?id=<?php echo $product['id']; ?>">
                        <div class="product-image">
                            <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        </div>
                        <div class="product-info">
                            <h3><?php echo $product['name']; ?></h3>
                            <p class="brand"><?php echo $product['brand']; ?></p>
                            <p class="price">৳<?php echo number_format($product['price']); ?></p>
                        </div>
                    </a>
                    <button class="btn-add-cart" data-id="<?php echo $product['id']; ?>">
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </button>
                </div>
                <?php endwhile; 
                $conn->close();
                ?>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="js/main.js"></script>
</body>
</html>
