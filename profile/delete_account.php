<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../config/config.php';
require_once '../config/UserManager.php';

$userManager = new UserManager();
$userId = $_SESSION['user_id'];

try {
    // Delete user from database
    $result = $userManager->deleteShopUser($userId);

    if ($result) {
        // Destroy session
        $_SESSION = [];
        session_destroy();

        // Redirect to home/login
        header("Location:../login.php?msg=account_deleted");
        exit();
    } else {
        die("Failed to delete account.");
    }

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>