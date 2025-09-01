<?php
session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/UserManager.php';

echo "<h2>Checkout Process Test Results</h2>";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_source'])) {
    echo "<p style='color: red;'>User not logged in</p>";
    exit();
}

$userManager = new UserManager();
$user = $userManager->getUserById($_SESSION['user_id'], $_SESSION['user_source']);
if (!$user) {
    echo "<p style='color: red;'>User not found</p>";
    exit();
}

$customerUniqueID = $user['CustomerUniqueID'];
echo "<p><strong>User:</strong> $customerUniqueID</p>";

// Get selected items from POST
$selectedItems = $_POST['selected_items'] ?? [];
echo "<p><strong>Selected Items:</strong> " . implode(', ', $selectedItems) . "</p>";

if (empty($selectedItems)) {
    echo "<p style='color: red;'>No items selected!</p>";
    echo "<p><a href='debug_checkout_test.php'>Go back</a></p>";
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Get cart items for selected items only
$placeholders = str_repeat('?,', count($selectedItems) - 1) . '?';
$query = "SELECT ci.cart_item_id, ci.product_id, ci.quantity, p.name, p.price FROM cart_items ci 
          JOIN products p ON ci.product_id = p.product_id 
          WHERE ci.CustomerUniqueID = ? AND ci.cart_item_id IN ($placeholders)";
$params = array_merge([$customerUniqueID], $selectedItems);

echo "<p><strong>Query:</strong> $query</p>";
echo "<p><strong>Parameters:</strong> " . implode(', ', $params) . "</p>";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>Cart Items Found:</h3>";
if (empty($cartItems)) {
    echo "<p style='color: red;'>No cart items found for selected items!</p>";
} else {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Cart Item ID</th><th>Product ID</th><th>Product Name</th><th>Price</th><th>Quantity</th><th>Subtotal</th></tr>";
    $total = 0;
    foreach ($cartItems as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;
        echo "<tr>";
        echo "<td>{$item['cart_item_id']}</td>";
        echo "<td>{$item['product_id']}</td>";
        echo "<td>{$item['name']}</td>";
        echo "<td>₹{$item['price']}</td>";
        echo "<td>{$item['quantity']}</td>";
        echo "<td>₹{$subtotal}</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p><strong>Total Amount:</strong> ₹{$total}</p>";
}

// Test the actual checkout process
echo "<h3>Testing Checkout Process:</h3>";

// Start transaction
$conn->beginTransaction();

try {
    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (CustomerUniqueID, total_amount, order_status) VALUES (?, ?, 'pending')");
    $stmt->execute([$customerUniqueID, $total]);
    $order_id = $conn->lastInsertId();
    
    echo "<p style='color: green;'>✅ Order created with ID: $order_id</p>";
    
    // Insert order items
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_time) VALUES (?, ?, ?, ?)");
    foreach ($cartItems as $item) {
        $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
        echo "<p>✅ Order item inserted: product_id={$item['product_id']}, quantity={$item['quantity']}</p>";
    }
    
    // Remove selected items from cart
    $deleteQuery = "DELETE FROM cart_items WHERE CustomerUniqueID = ? AND cart_item_id IN ($placeholders)";
    $deleteParams = array_merge([$customerUniqueID], $selectedItems);
    
    $stmt = $conn->prepare($deleteQuery);
    $stmt->execute($deleteParams);
    $deletedRows = $stmt->rowCount();
    
    echo "<p style='color: green;'>✅ Removed $deletedRows items from cart</p>";
    
    // Commit transaction
    $conn->commit();
    echo "<p style='color: green; font-weight: bold;'>✅ Transaction committed successfully!</p>";
    
    // Check remaining cart items
    $stmt = $conn->prepare("SELECT ci.cart_item_id, ci.product_id, ci.quantity, p.name FROM cart_items ci 
                            JOIN products p ON ci.product_id = p.product_id 
                            WHERE ci.CustomerUniqueID = ?");
    $stmt->execute([$customerUniqueID]);
    $remainingItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Remaining Cart Items:</h3>";
    if (empty($remainingItems)) {
        echo "<p style='color: green;'>✅ All items removed from cart</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Some items still in cart:</p>";
        foreach ($remainingItems as $item) {
            echo "<p>- {$item['name']} (ID: {$item['cart_item_id']})</p>";
        }
    }
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p style='color: red;'>❌ Transaction rolled back</p>";
}

echo "<br>";
echo "<p><a href='debug_checkout_test.php'>Test Again</a> | <a href='cart/'>Go to Cart</a></p>";
?>

