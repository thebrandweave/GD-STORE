<?php
session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/UserManager.php';

echo "<h2>Checkout Debug Test</h2>";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_source'])) {
    echo "<p style='color: red;'>User not logged in</p>";
    echo "<p><a href='login.php'>Login here</a></p>";
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

$db = new Database();
$conn = $db->getConnection();

// Get all cart items for this user
$stmt = $conn->prepare("SELECT ci.cart_item_id, ci.product_id, ci.quantity, p.name, p.price FROM cart_items ci 
                        JOIN products p ON ci.product_id = p.product_id 
                        WHERE ci.CustomerUniqueID = ?");
$stmt->execute([$customerUniqueID]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>Current Cart Items:</h3>";
if (empty($cartItems)) {
    echo "<p style='color: orange;'>Your cart is empty. Please add some items first.</p>";
    echo "<p><a href='products/'>Go to products</a></p>";
} else {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Cart Item ID</th><th>Product ID</th><th>Product Name</th><th>Price</th><th>Quantity</th><th>Subtotal</th></tr>";
    foreach ($cartItems as $item) {
        $subtotal = $item['price'] * $item['quantity'];
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
    
    echo "<h3>Test Checkout Process:</h3>";
    echo "<form method='post' action='test_checkout_process.php'>";
    echo "<p>Select items to test checkout:</p>";
    foreach ($cartItems as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid #ddd;'>";
        echo "<input type='checkbox' name='selected_items[]' value='{$item['cart_item_id']}' id='item_{$item['cart_item_id']}'>";
        echo "<label for='item_{$item['cart_item_id']}' style='margin-left: 10px;'>";
        echo "{$item['name']} - ₹{$item['price']} x {$item['quantity']} = ₹{$subtotal}";
        echo "</label>";
        echo "</div>";
    }
    echo "<br>";
    echo "<button type='submit' style='background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Test Checkout Process</button>";
    echo "</form>";
}
?>

