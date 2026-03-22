<?php
require_once '../config.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid method'], 405);
}

// Require login to place order
if (!isLoggedIn()) {
    jsonResponse(['success' => false, 'message' => 'Please login to place order', 'require_login' => true], 401);
}

$conn = getConnection();
$user_id = getCurrentUserId();

$sql = "SELECT c.product_id, c.quantity, p.price 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cartItems = $result->fetch_all(MYSQLI_ASSOC);

if (empty($cartItems)) {
    jsonResponse(['success' => false, 'message' => 'Cart is empty'], 400);
}

$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

$stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
$stmt->bind_param("id", $user_id, $total);

if (!$stmt->execute()) {
    jsonResponse(['success' => false, 'message' => 'Failed to create order'], 500);
}

$order_id = $conn->insert_id;

$stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
foreach ($cartItems as $item) {
    $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
    $stmt->execute();
}

$stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$_SESSION['cart_count'] = 0;

jsonResponse([
    'success' => true, 
    'message' => 'Order placed successfully',
    'order_id' => $order_id,
    'total' => $total
]);

$conn->close();
?>
