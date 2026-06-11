<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/UserManager.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_source'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login first'
    ]);
    exit;
}

$userManager = new UserManager();
$user = $userManager->getUserById(
    $_SESSION['user_id'],
    $_SESSION['user_source']
);

$customerUniqueID = $user['CustomerUniqueID'];

$data = json_decode(file_get_contents("php://input"), true);

$product_id = (int)$data['product_id'];
$quantity = (int)($data['quantity'] ?? 1);

$conn = (new Database())->getConnection();

/* Check if already exists */
$stmt = $conn->prepare("
    SELECT cart_item_id, quantity
    FROM cart_items
    WHERE CustomerUniqueID = ?
    AND product_id = ?
");
$stmt->execute([$customerUniqueID, $product_id]);

$item = $stmt->fetch(PDO::FETCH_ASSOC);

if ($item) {
    $stmt = $conn->prepare("
        UPDATE cart_items
        SET quantity = quantity + ?
        WHERE cart_item_id = ?
    ");
    $stmt->execute([$quantity, $item['cart_item_id']]);
} else {
    $stmt = $conn->prepare("
        INSERT INTO cart_items
        (CustomerUniqueID, product_id, quantity)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([
        $customerUniqueID,
        $product_id,
        $quantity
    ]);
}

echo json_encode([
    'success' => true,
    'message' => 'Added to cart'
]);