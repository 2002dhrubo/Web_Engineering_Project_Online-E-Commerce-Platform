<?php
require_once '../config.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

$conn = getConnection();
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

// Check if user is logged in for cart operations (except GET which can show login prompt)
if (!isLoggedIn() && $method !== 'GET') {
    jsonResponse(['success' => false, 'message' => 'Please login to manage cart', 'require_login' => true], 401);
}

// Use user_id if logged in, otherwise use session_id
$user_id = isLoggedIn() ? getCurrentUserId() : null;
$session_id = session_id();

switch ($method) {
    case 'GET':
        if (isLoggedIn()) {
            $sql = "SELECT c.*, p.name, p.brand, p.price, p.image 
                    FROM cart c 
                    JOIN products p ON c.product_id = p.id 
                    WHERE c.user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
        } else {
            $sql = "SELECT c.*, p.name, p.brand, p.price, p.image 
                    FROM cart c 
                    JOIN products p ON c.product_id = p.id 
                    WHERE c.session_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $session_id);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $items = $result->fetch_all(MYSQLI_ASSOC);
        
        $total = 0;
        $count = 0;
        foreach ($items as &$item) {
            $item['subtotal'] = $item['price'] * $item['quantity'];
            $total += $item['subtotal'];
            $count += $item['quantity'];
        }
        
        jsonResponse(['items' => $items, 'total' => $total, 'count' => $count]);
        break;

    case 'POST':
        $product_id = (int)$data['product_id'];
        $quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;
        
        // Check if product already in cart
        $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();
        
        if ($existing) {
            $newQty = $existing['quantity'] + $quantity;
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $stmt->bind_param("ii", $newQty, $existing['id']);
        } else {
            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        }
        
        if ($stmt->execute()) {
            $countStmt = $conn->prepare("SELECT SUM(quantity) as count FROM cart WHERE user_id = ?");
            $countStmt->bind_param("i", $user_id);
            $countStmt->execute();
            $count = $countStmt->get_result()->fetch_assoc()['count'] ?? 0;
            $_SESSION['cart_count'] = $count;
            
            jsonResponse(['success' => true, 'message' => 'Added to cart', 'cart_count' => $count]);
        } else {
            jsonResponse(['success' => false, 'message' => 'Failed to add'], 500);
        }
        break;

    case 'PUT':
        $product_id = (int)$data['product_id'];
        $quantity = (int)$data['quantity'];
        
        if ($quantity <= 0) {
            $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param("ii", $user_id, $product_id);
        } else {
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param("iii", $quantity, $user_id, $product_id);
        }
        
        if ($stmt->execute()) {
            $countStmt = $conn->prepare("SELECT SUM(quantity) as count FROM cart WHERE user_id = ?");
            $countStmt->bind_param("i", $user_id);
            $countStmt->execute();
            $count = $countStmt->get_result()->fetch_assoc()['count'] ?? 0;
            $_SESSION['cart_count'] = $count;
            
            jsonResponse(['success' => true, 'cart_count' => $count]);
        } else {
            jsonResponse(['success' => false], 500);
        }
        break;

    case 'DELETE':
        $product_id = (int)$data['product_id'];
        
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        
        if ($stmt->execute()) {
            $countStmt = $conn->prepare("SELECT SUM(quantity) as count FROM cart WHERE user_id = ?");
            $countStmt->bind_param("i", $user_id);
            $countStmt->execute();
            $count = $countStmt->get_result()->fetch_assoc()['count'] ?? 0;
            $_SESSION['cart_count'] = $count;
            
            jsonResponse(['success' => true, 'cart_count' => $count]);
        } else {
            jsonResponse(['success' => false], 500);
        }
        break;
}

$conn->close();
?>
