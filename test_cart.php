<?php
session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/UserManager.php';

echo "<h2>Current Cart State</h2>";

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
echo "<p>User: $customerUniqueID</p>";

$db = new Database();
$conn = $db->getConnection();

// Get all cart items for this user
$stmt = $conn->prepare("SELECT ci.cart_item_id, ci.product_id, ci.quantity, p.name, p.price FROM cart_items ci 
                        JOIN products p ON ci.product_id = p.product_id 
                        WHERE ci.CustomerUniqueID = ?");
$stmt->execute([$customerUniqueID]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>Your Cart Items:</h3>";
if (empty($cartItems)) {
    echo "<p style='color: orange;'>Your cart is empty. Please add some items first.</p>";
} else {
    echo "<table border='1'>";
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
    
    echo "<p><strong>Total Items:</strong> " . count($cartItems) . "</p>";
    echo "<p><strong>Note:</strong> These items have proper IDs and should work correctly with the checkout system.</p>";
}
?>

