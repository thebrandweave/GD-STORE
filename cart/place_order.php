<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/UserManager.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_source'])) {
    header('Location: ../login.php');
    exit();
}

$userManager = new UserManager();
$user = $userManager->getUserById($_SESSION['user_id'], $_SESSION['user_source']);
if (!$user) {
    session_destroy();
    header('Location: ../login.php');
    exit();
}
$customerUniqueID = $user['CustomerUniqueID'];

$db = new Database();
$conn = $db->getConnection();

// Get selected cart items from POST data
$selectedItemsJson = $_POST['selected_items'] ?? '[]';
$selectedItems = json_decode($selectedItemsJson, true);

// Basic logging for order processing
error_log("Order processing for user: " . $customerUniqueID);



if (empty($selectedItems)) {
    error_log("No items selected - redirecting to error page");
    header('Location: index.php?error=no_items_selected');
    exit();
}

// Get cart items for selected items only
$placeholders = str_repeat('?,', count($selectedItems) - 1) . '?';
$query = "SELECT ci.cart_item_id, ci.product_id, ci.quantity, p.price FROM cart_items ci 
          JOIN products p ON ci.product_id = p.product_id 
          WHERE ci.CustomerUniqueID = ? AND ci.cart_item_id IN ($placeholders)";
$params = array_merge([$customerUniqueID], $selectedItems);

$stmt = $conn->prepare($query);
$stmt->execute($params);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cartItems)) {
    error_log("No cart items found for selected items - redirecting to error page");
    header('Location: index.php?error=invalid_selection');
    exit();
}

// Calculate total for selected items only
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Start transaction
$conn->beginTransaction();

try {
    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (CustomerUniqueID, total_amount, order_status) VALUES (?, ?, 'pending')");
    $stmt->execute([$customerUniqueID, $total]);
    $order_id = $conn->lastInsertId();

    // Check if order was created successfully
    if (!$order_id || $order_id == 0) {
        throw new Exception("Failed to create order - order_id is invalid: " . $order_id);
    }

    // Insert order items for selected items only
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_time) VALUES (?, ?, ?, ?)");
    foreach ($cartItems as $item) {
        $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
    }

    // Remove selected items from cart
    $placeholders = str_repeat('?,', count($selectedItems) - 1) . '?';
    $deleteQuery = "DELETE FROM cart_items WHERE CustomerUniqueID = ? AND cart_item_id IN ($placeholders)";
    $deleteParams = array_merge([$customerUniqueID], $selectedItems);
    
    $stmt = $conn->prepare($deleteQuery);
    $stmt->execute($deleteParams);

    // Commit transaction
    $conn->commit();

    // Redirect back to cart with success message
    header("Location: index.php?success=order_placed&order_id=" . $order_id);
    exit();

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    error_log("Order processing error: " . $e->getMessage());
    header('Location: index.php?error=order_processing_failed');
    exit();
}