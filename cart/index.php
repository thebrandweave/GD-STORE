<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_source'])) {
    header('Location: ../login.php');
    exit();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/UserManager.php';

$userManager = new UserManager();
$user = $userManager->getUserById($_SESSION['user_id'], $_SESSION['user_source']);

if (!$user) {
    session_destroy();
    header('Location: ../login.php');
    exit();
}

$customerUniqueID = $user['CustomerUniqueID'];

// Fetch cart items for this user with product details
$conn = (new Database())->getConnection();
$sql = "SELECT ci.cart_item_id, ci.product_id, ci.quantity, p.name, p.price, p.stock,
               (SELECT pi.image_url FROM product_images pi WHERE pi.product_id = p.product_id ORDER BY pi.uploaded_at ASC LIMIT 1) as image_url
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.product_id
        WHERE ci.CustomerUniqueID = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$customerUniqueID]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cartItems = [];
$total = 0;
$itemCount = 0;
foreach ($result as $row) {
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $total += $row['subtotal'];
    $itemCount += $row['quantity'];
    $cartItems[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart - GoldenDream Shop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: #f7f7fa;
            --secondary: #232526;
            --accent: #ffd600;
            --accent-dark: #ffb300;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
            --light: #f8f9fa;
            --dark: #343a40;
            --card-bg: #fff;
            --radius: 16px;
            --shadow: 0 8px 32px 0 rgba(0,0,0,0.08);
            --font-main: 'Montserrat', Arial, sans-serif;
        }
        
        body, html {
            background: var(--primary);
            color: var(--secondary);
            font-family: var(--font-main);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        
        .cart-container {
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        /* Responsive Spacing for Large Screens */
        @media (min-width: 1600px) {
            .cart-container {
                margin: 60px auto;
            }
        }
        
        @media (min-width: 1920px) {
            .cart-container {
                margin: 80px auto;
            }
        }
        
        @media (min-width: 2560px) {
            .cart-container {
                margin: 100px auto;
            }
        }
        
        /* Container Fluid for Large Screens */
        .container-fluid {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        @media (min-width: 1600px) {
            .container-fluid {
                max-width: 1600px;
                padding: 0 40px;
            }
            .cart-container {
                max-width: 1600px;
            }
        }
        
        @media (min-width: 1920px) {
            .container-fluid {
                max-width: 1800px;
                padding: 0 60px;
            }
            .cart-container {
                max-width: 1800px;
            }
        }
        
        @media (min-width: 2560px) {
            .container-fluid {
                max-width: 2200px;
                padding: 0 80px;
            }
            .cart-container {
                max-width: 2200px;
            }
        }
        
        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .cart-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--secondary);
            margin: 0;
        }
        
        /* Responsive Typography for Large Screens */
        @media (min-width: 1600px) {
            .cart-title {
                font-size: 3rem;
            }
        }
        
        @media (min-width: 1920px) {
            .cart-title {
                font-size: 3.5rem;
            }
        }
        
        @media (min-width: 2560px) {
            .cart-title {
                font-size: 4rem;
            }
        }
        
        .cart-subtitle {
            font-size: 1.1rem;
            color: #666;
            margin: 8px 0 0 0;
        }
        
        .clear-cart-btn {
            background: none;
            border: none;
            color: var(--danger);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .clear-cart-btn:hover {
            background: rgba(220, 53, 69, 0.1);
        }
        
        .cart-layout {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 40px;
        }
        
        /* Responsive Cart Layout for Large Screens */
        @media (min-width: 1600px) {
            .cart-layout {
                gap: 60px;
            }
            .cart-items {
                padding: 40px;
            }
        }
        
        @media (min-width: 1920px) {
            .cart-layout {
                gap: 80px;
            }
            .cart-items {
                padding: 48px;
            }
        }
        
        @media (min-width: 2560px) {
            .cart-layout {
                gap: 100px;
            }
            .cart-items {
                padding: 56px;
            }
        }
        
        .cart-items {
            background: #fff;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        }
        
        .cart-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .cart-table th {
            text-align: left;
            padding: 16px 12px;
            border-bottom: 2px solid #eee;
            font-weight: 700;
            color: var(--secondary);
            font-size: 0.95rem;
        }
        
        /* Responsive Cart Table for Large Screens */
        @media (min-width: 1600px) {
            .cart-table th {
                padding: 20px 16px;
                font-size: 1rem;
            }
            .cart-table td {
                padding: 24px 16px;
            }
        }
        
        @media (min-width: 1920px) {
            .cart-table th {
                padding: 24px 20px;
                font-size: 1.05rem;
            }
            .cart-table td {
                padding: 28px 20px;
            }
        }
        
        @media (min-width: 2560px) {
            .cart-table th {
                padding: 28px 24px;
                font-size: 1.1rem;
            }
            .cart-table td {
                padding: 32px 24px;
            }
        }
        
        .cart-table td {
            padding: 20px 12px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }
        
        .product-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .product-checkbox {
            width: 18px;
            height: 18px;
            accent-color: var(--success);
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        /* Responsive Product Elements for Large Screens */
        @media (min-width: 1600px) {
            .product-image {
                width: 72px;
                height: 72px;
            }
            .product-details h4 {
                font-size: 1.2rem;
            }
            .price {
                font-size: 1.2rem;
            }
        }
        
        @media (min-width: 1920px) {
            .product-image {
                width: 80px;
                height: 80px;
            }
            .product-details h4 {
                font-size: 1.3rem;
            }
            .price {
                font-size: 1.3rem;
            }
        }
        
        @media (min-width: 2560px) {
            .product-image {
                width: 88px;
                height: 88px;
            }
            .product-details h4 {
                font-size: 1.4rem;
            }
            .price {
                font-size: 1.4rem;
            }
        }
        
        .product-details h4 {
            margin: 0 0 4px 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--secondary);
        }
        
        .price {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--secondary);
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .quantity-btn {
            width: 32px;
            height: 32px;
            border: 1px solid #ddd;
            background: #fff;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        
        /* Responsive Quantity Controls for Large Screens */
        @media (min-width: 1600px) {
            .quantity-btn {
                width: 36px;
                height: 36px;
            }
            .quantity-input {
                width: 56px;
                height: 36px;
                font-size: 1rem;
            }
        }
        
        @media (min-width: 1920px) {
            .quantity-btn {
                width: 40px;
                height: 40px;
            }
            .quantity-input {
                width: 60px;
                height: 40px;
                font-size: 1.05rem;
            }
        }
        
        @media (min-width: 2560px) {
            .quantity-btn {
                width: 44px;
                height: 44px;
            }
            .quantity-input {
                width: 64px;
                height: 44px;
                font-size: 1.1rem;
            }
        }
        
        .quantity-btn:hover {
            background: var(--light);
            border-color: var(--accent);
        }
        
        .quantity-input {
            width: 50px;
            height: 32px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
            font-size: 0.9rem;
        }
        
        .subtotal {
            font-size: 1.1rem;
            font-weight: 700;
        }
        
        .remove-btn {
            background: none;
            border: none;
            color: var(--danger);
            cursor: pointer;
            padding: 8px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }
        
        .remove-btn:hover {
            background: rgba(220, 53, 69, 0.1);
        }
        
        .order-summary {
            background: #fff;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            height: fit-content;
            position: sticky;
            top: 20px;
        }
        
        /* Responsive Order Summary for Large Screens */
        @media (min-width: 1600px) {
            .order-summary {
                padding: 40px;
            }
            .summary-title {
                font-size: 1.6rem;
            }
        }
        
        @media (min-width: 1920px) {
            .order-summary {
                padding: 48px;
            }
            .summary-title {
                font-size: 1.7rem;
            }
        }
        
        @media (min-width: 2560px) {
            .order-summary {
                padding: 56px;
            }
            .summary-title {
                font-size: 1.8rem;
            }
        }
        
        .summary-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 24px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .summary-item:last-child {
            border-bottom: none;
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--success);
        }
        
        .summary-label {
            color: #666;
            font-weight: 500;
        }
        
        .summary-value {
            font-weight: 600;
            color: var(--secondary);
        }
        
        .checkout-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            color: var(--secondary);
            border: none;
            padding: 16px 24px;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(255, 214, 0, 0.3);
        }
        
        .checkout-btn:hover {
            background: linear-gradient(135deg, var(--accent-dark) 0%, #e6a800 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 214, 0, 0.4);
        }
        
.checkout-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    background: #ccc;
    transform: none;
    box-shadow: none;
}

.checkout-btn:disabled:hover {
    background: #ccc;
    transform: none;
    box-shadow: none;
}

.cart-table tr.selected {
    background-color: rgba(40, 167, 69, 0.05);
    border-left: 3px solid var(--success);
}

         .cart-table tr.selected:hover {
             background-color: rgba(40, 167, 69, 0.08);
         }
         
         /* Download Button */
         .download-btn {
             background: var(--info);
             color: white;
             border: none;
             padding: 12px 24px;
             border-radius: 8px;
             font-weight: 600;
             cursor: pointer;
             display: flex;
             align-items: center;
             gap: 8px;
             transition: all 0.3s ease;
             margin-top: 16px;
             width: 100%;
             justify-content: center;
         }
         
         .download-btn:hover {
             background: #138496;
             transform: translateY(-2px);
             box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3);
         }
        
        .cart-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #eee;
        }
        
        .action-btn {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            color: var(--secondary);
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(255, 214, 0, 0.2);
        }
        
        .action-btn:hover {
            background: linear-gradient(135deg, var(--accent-dark) 0%, #e6a800 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(255, 214, 0, 0.3);
        }
        
        
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .empty-cart i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .empty-cart h3 {
            font-size: 1.5rem;
            margin-bottom: 12px;
            color: var(--secondary);
        }
        
        .empty-cart p {
            font-size: 1.1rem;
            margin-bottom: 24px;
        }
        
        .shop-now-btn {
            background: var(--accent);
            color: var(--secondary);
            border: none;
            padding: 0px 24px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        .shop-now-btn i {
            font-size: 2rem;
            color: var(--secondary);
            margin-top: 18px;
        }
        
        .shop-now-btn:hover {
            background: var(--accent-dark);
            transform: translateY(-2px);
        }
        
                 /* Professional Confirmation Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
             background: rgba(35, 37, 38, 0.85);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
             backdrop-filter: blur(12px);
        }
        
        .modal-content {
             background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
             border-radius: 24px;
             padding: 48px 40px;
             max-width: 480px;
            width: 90%;
            text-align: center;
             box-shadow: 0 25px 80px rgba(0, 0, 0, 0.25), 0 8px 32px rgba(0, 0, 0, 0.1);
             transform: scale(0.9) translateY(20px);
            opacity: 0;
             transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
             border: 1px solid rgba(255, 214, 0, 0.15);
             position: relative;
             overflow: hidden;
         }
         
         .modal-content::before {
             content: '';
             position: absolute;
             top: 0;
             left: 0;
             right: 0;
             height: 4px;
             background: linear-gradient(90deg, var(--accent) 0%, var(--accent-dark) 50%, var(--success) 100%);
        }
        
        .modal-overlay.show .modal-content {
             transform: scale(1) translateY(0);
            opacity: 1;
        }
        
        .modal-icon {
             font-size: 4rem;
             margin-bottom: 24px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
             filter: drop-shadow(0 4px 8px rgba(255, 214, 0, 0.3));
        }
        
        .modal-title {
             font-size: 1.8rem;
            font-weight: 800;
            color: var(--secondary);
             margin-bottom: 20px;
            letter-spacing: 0.5px;
             background: linear-gradient(135deg, var(--secondary) 0%, #4a5568 100%);
             -webkit-background-clip: text;
             -webkit-text-fill-color: transparent;
             background-clip: text;
        }
        
        .modal-message {
             font-size: 1.1rem;
             color: #4a5568;
             margin-bottom: 36px;
             line-height: 1.7;
            font-weight: 500;
             padding: 0 10px;
        }
        
        .modal-buttons {
            display: flex;
             gap: 20px;
            justify-content: center;
             flex-wrap: wrap;
        }
        
        .modal-btn {
             padding: 16px 32px;
             border-radius: 16px;
            font-weight: 700;
            cursor: pointer;
             transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            font-size: 1rem;
            letter-spacing: 0.3px;
             min-width: 140px;
             position: relative;
             overflow: hidden;
         }
         
         .modal-btn::before {
             content: '';
             position: absolute;
             top: 0;
             left: -100%;
             width: 100%;
             height: 100%;
             background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
             transition: left 0.5s;
         }
         
         .modal-btn:hover::before {
             left: 100%;
        }
        
        .modal-btn.cancel {
             background: linear-gradient(145deg, #f8f9fa 0%, #e9ecef 100%);
             color: #495057;
             border: 2px solid #dee2e6;
             box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        
        .modal-btn.cancel:hover {
             background: linear-gradient(145deg, #e9ecef 0%, #dee2e6 100%);
             border-color: #adb5bd;
             transform: translateY(-3px);
             box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .modal-btn.confirm {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            color: var(--secondary);
             box-shadow: 0 6px 20px rgba(255, 214, 0, 0.4);
        }
        
        .modal-btn.confirm:hover {
            background: linear-gradient(135deg, var(--accent-dark) 0%, #e6a800 100%);
             transform: translateY(-3px);
             box-shadow: 0 10px 30px rgba(255, 214, 0, 0.5);
         }
         
         .modal-btn.success {
             background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
             color: white;
             box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
         }
         
         .modal-btn.success:hover {
             background: linear-gradient(135deg, #20c997 0%, #1ea085 100%);
             transform: translateY(-3px);
             box-shadow: 0 10px 30px rgba(40, 167, 69, 0.5);
         }
         
                   .modal-details {
              background: linear-gradient(145deg, #f8f9fa 0%, #e9ecef 100%);
              border-radius: 16px;
              padding: 20px;
              margin: 24px 0;
              border: 1px solid #dee2e6;
          }
          
          .modal-details h4 {
              margin: 0 0 16px 0;
              color: var(--secondary);
              font-size: 1.1rem;
              font-weight: 600;
          }
          
          .modal-details p {
              margin: 8px 0;
              color: #6c757d;
              font-size: 0.95rem;
          }
          
          .modal-details .highlight {
              color: var(--success);
              font-weight: 700;
              font-size: 1.1rem;
          }
          
          /* Success Modal Specific Styles */
          .modal-content.success-modal {
              max-width: 520px;
              padding: 40px 32px;
          }
          
          .success-icon {
              font-size: 5rem;
              margin-bottom: 20px;
              background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
              -webkit-background-clip: text;
              -webkit-text-fill-color: transparent;
              background-clip: text;
              filter: drop-shadow(0 4px 12px rgba(40, 167, 69, 0.3));
          }
          
          .success-title {
              font-size: 2rem;
              font-weight: 800;
              color: var(--success);
              margin-bottom: 16px;
              letter-spacing: 0.5px;
          }
          
          .success-message {
              font-size: 1.1rem;
              color: #4a5568;
              margin-bottom: 32px;
              line-height: 1.6;
              font-weight: 500;
              padding: 0 15px;
          }
          
          .order-summary-box {
              background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
              border-radius: 20px;
              padding: 24px;
              margin: 24px 0;
              border: 2px solid #e9ecef;
              box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
          }
          
          .order-summary-box h4 {
              margin: 0 0 20px 0;
              color: var(--secondary);
              font-size: 1.2rem;
              font-weight: 700;
              text-align: center;
              padding-bottom: 12px;
              border-bottom: 2px solid #e9ecef;
          }
          
          .summary-row {
              display: flex;
              justify-content: space-between;
              align-items: center;
              padding: 12px 0;
              border-bottom: 1px solid #f0f0f0;
          }
          
          .summary-row:last-child {
              border-bottom: none;
              padding-top: 16px;
              margin-top: 8px;
              border-top: 2px solid #e9ecef;
          }
          
          .summary-label {
              color: #6c757d;
              font-weight: 600;
              font-size: 1rem;
          }
          
          .summary-value {
              color: var(--secondary);
              font-weight: 700;
              font-size: 1.1rem;
          }
          
          .summary-value.highlight {
              color: var(--success);
              font-size: 1.3rem;
          }
          
          .success-actions {
              margin-top: 32px;
              text-align: center;
          }
          
          .success-btn {
              background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
              color: white;
              border: none;
              padding: 16px 40px;
              border-radius: 50px;
              font-weight: 700;
              font-size: 1.1rem;
              cursor: pointer;
              transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
              box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
              letter-spacing: 0.5px;
              min-width: 200px;
          }
          
          .success-btn:hover {
              background: linear-gradient(135deg, #20c997 0%, #1ea085 100%);
              transform: translateY(-3px);
              box-shadow: 0 10px 30px rgba(40, 167, 69, 0.5);
          }
         
         /* Responsive Modal Design */
         @media (max-width: 768px) {
             .modal-content {
                 padding: 32px 24px;
                 max-width: 90%;
                 margin: 20px;
             }
             
             .modal-title {
                 font-size: 1.5rem;
             }
             
             .modal-message {
                 font-size: 1rem;
                 padding: 0 5px;
             }
             
             .modal-buttons {
                 flex-direction: column;
                 gap: 12px;
             }
             
             .modal-btn {
                 min-width: 100%;
                 padding: 14px 24px;
             }
             
             .modal-details {
                 padding: 16px;
                 margin: 20px 0;
             }
             
             /* Success Modal Mobile Styles */
             .modal-content.success-modal {
                 padding: 28px 20px;
                 max-width: 95%;
             }
             
             .success-icon {
                 font-size: 4rem;
                 margin-bottom: 16px;
             }
             
             .success-title {
                 font-size: 1.6rem;
                 margin-bottom: 12px;
             }
             
             .success-message {
                 font-size: 1rem;
                 padding: 0 8px;
                 margin-bottom: 24px;
             }
             
             .order-summary-box {
                 padding: 20px;
                 margin: 20px 0;
             }
             
             .success-btn {
                 min-width: 180px;
                 padding: 14px 32px;
                 font-size: 1rem;
             }
         }
         
         @media (max-width: 480px) {
             .modal-content {
                 padding: 24px 20px;
                 border-radius: 20px;
             }
             
             .modal-icon {
                 font-size: 3rem;
                 margin-bottom: 20px;
             }
             
             .modal-title {
                 font-size: 1.3rem;
             }
             
             .modal-message {
                 font-size: 0.95rem;
             }
             
             /* Success Modal Small Screen Styles */
             .modal-content.success-modal {
                 padding: 20px 16px;
             }
             
             .success-icon {
                 font-size: 3.5rem;
                 margin-bottom: 12px;
             }
             
             .success-title {
                 font-size: 1.4rem;
                 margin-bottom: 10px;
             }
             
             .success-message {
                 font-size: 0.9rem;
                 padding: 0 5px;
                 margin-bottom: 20px;
             }
             
             .order-summary-box {
                 padding: 16px;
                 margin: 16px 0;
             }
             
             .order-summary-box h4 {
                 font-size: 1.1rem;
                 margin-bottom: 16px;
             }
             
             .summary-row {
                 padding: 10px 0;
             }
             
             .summary-label {
                 font-size: 0.9rem;
             }
             
             .summary-value {
                 font-size: 1rem;
             }
             
             .summary-value.highlight {
                 font-size: 1.1rem;
             }
             
             .success-btn {
                 min-width: 160px;
                 padding: 12px 28px;
                 font-size: 0.95rem;
             }
        }
        
        @media (max-width: 1024px) {
            .cart-layout {
                grid-template-columns: 1fr;
                gap: 24px;
            }
            
            .order-summary {
                position: static;
            }
        }
        
        @media (max-width: 768px) {
            .cart-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
            
            .cart-title {
                font-size: 2rem;
            }
            
            
            .cart-items, .order-summary {
                padding: 20px;
            }
            
            .product-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            
            .cart-table th, .cart-table td {
                padding: 12px 8px;
            }
            
            .cart-actions {
                flex-direction: column;
                gap: 12px;
            }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/../components/navbar.php'; ?>

<div class="cart-container">
    <div class="cart-header">
        <div>
            <h1 class="cart-title">Your Cart</h1>
            <p class="cart-subtitle">There are <?= $itemCount ?> products in your cart</p>
            <p class="cart-subtitle" id="selection-status" style="margin-top: 4px; font-size: 0.9rem; color: #888;">Select items to proceed to checkout</p>

        </div>
        <?php if (!empty($cartItems)): ?>
            <button class="clear-cart-btn" onclick="clearCart()">
                <i class="bi bi-trash"></i>
                Clear Cart
            </button>
        <?php endif; ?>
    </div>

    <?php if (empty($cartItems)): ?>
        <div class="empty-cart">
            <i class="bi bi-cart-x"></i>
            <h3>Your cart is empty</h3>
            <p>Looks like you haven't added any products to your cart yet.</p>
            <a href="../products/" class="shop-now-btn">
                <i class="bi bi-arrow-left"></i>
                Continue Shopping
            </a>
        </div>
    <?php else: ?>
        <?php if (isset($_GET['error']) && $_GET['error'] === 'no_items_selected'): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <i class="bi bi-exclamation-triangle-fill"></i>
                Please select at least one item to proceed to checkout.
            </div>
        <?php endif; ?>
        

        

        
        <?php if (isset($_GET['success']) && $_GET['success'] === 'order_placed'): ?>
            <div style="background: #d4edda; color: #155724; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                <i class="bi bi-check-circle-fill"></i>
                Your order has been successfully placed! Order ID: <?= htmlspecialchars($_GET['order_id'] ?? '') ?>
            </div>
        <?php endif; ?>
        <div class="cart-layout">
            <div class="cart-items">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">
                                <input type="checkbox" id="select-all-checkbox" class="select-all-checkbox">
                                <label for="select-all-checkbox" style="margin-left: 8px; font-weight: 600; cursor: pointer;">Select All</label>
                            </th>
                            <th>Product</th>
                            <th>Unit Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                            <tr class="cart-item-row" data-cart-item-id="<?= $item['cart_item_id'] ?>" data-price="<?= $item['price'] ?>" data-quantity="<?= $item['quantity'] ?>">
                                <td>
                                    <input type="checkbox" class="product-checkbox" data-cart-item-id="<?= $item['cart_item_id'] ?>" data-price="<?= $item['price'] ?>" data-quantity="<?= $item['quantity'] ?>">
                                </td>
                                <td>
                                    <div class="product-info">
                                        <img src="<?= $item['image_url'] ? '../uploads/products/' . htmlspecialchars(basename($item['image_url'])) : 'https://via.placeholder.com/60x60?text=No+Image' ?>" 
                                             alt="<?= htmlspecialchars($item['name']) ?>" 
                                             class="product-image">
                                        <div class="product-details">
                                            <h4><?= htmlspecialchars($item['name']) ?></h4>
                                        </div>
                                    </div>
                                </td>
                                <td class="price">₹<?= number_format($item['price'], 2, '.', ',') ?></td>
                                <td>
                                    <div class="quantity-controls">
                                        <button class="quantity-btn" onclick="updateQuantity(<?= $item['cart_item_id'] ?>, -1)">-</button>
                                        <input type="number" class="quantity-input" value="<?= $item['quantity'] ?>" 
                                               min="1" max="<?= $item['stock'] ?>" 
                                               onchange="updateQuantity(<?= $item['cart_item_id'] ?>, this.value, true)">
                                        <button class="quantity-btn" onclick="updateQuantity(<?= $item['cart_item_id'] ?>, 1)">+</button>
                                    </div>
                                </td>
                                <td class="subtotal">₹<?= number_format($item['subtotal'], 2, '.', ',') ?></td>
                                <td>
                                    <button class="remove-btn" onclick="removeItem(<?= $item['cart_item_id'] ?>)" title="Remove item">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="order-summary">
                <h3 class="summary-title">Order Summary</h3>
                <div class="summary-item">
                    <span class="summary-label">Selected Items</span>
                    <span class="summary-value" id="selected-count">0</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Subtotal</span>
                    <span class="summary-value" id="selected-subtotal">₹0.00</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Total</span>
                    <span class="summary-value" id="selected-total">₹0.00</span>
                </div>
                
                <form method="post" action="place_order.php" id="checkout-form">
                    <input type="hidden" name="selected_items" id="selected-items-input">
                    <button type="button" class="checkout-btn" id="checkout-btn" disabled onclick="confirmCheckout()">
                        Proceed To Checkout
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
                
                <button type="button" class="download-btn" onclick="downloadOrderDetails()">
                    <i class="bi bi-file-earmark-pdf"></i>
                    Download as PDF
                </button>
            </div>
        </div>

        <div class="cart-actions">
            <a href="../products/" class="action-btn">
                <i class="bi bi-arrow-left"></i>
                Continue Shopping
            </a>
            <button class="action-btn" onclick="updateCart()">
                <i class="bi bi-arrow-clockwise"></i>
                Update Cart
            </button>

        </div>
    <?php endif; ?>
</div>

<div id="confirmation-modal" class="modal-overlay">
    <div class="modal-content">
         <div class="modal-icon" id="modal-icon"><i class="bi bi-question-circle-fill"></i></div>
         <h3 class="modal-title" id="modal-title">Confirm Action</h3>
         <p class="modal-message" id="modal-message"></p>
         <div class="modal-details" id="modal-details" style="display: none;">
             <h4>Order Summary</h4>
             <div class="summary-row">
                 <span class="summary-label">Total Items:</span>
                 <span class="summary-value" id="modal-items-count">0</span>
             </div>
             <div class="summary-row">
                 <span class="summary-label">Total Amount:</span>
                 <span class="summary-value highlight" id="modal-total-amount">₹0.00</span>
             </div>
         </div>
         <div class="modal-buttons" id="modal-buttons">
            <button class="modal-btn cancel" onclick="closeModal()">Cancel</button>
            <button class="modal-btn confirm" onclick="confirmAction()">Confirm</button>
        </div>
    </div>
</div>

<script>
function updateQuantity(cartItemId, change, isDirectInput = false) {
    let newQuantity;
    if (isDirectInput) {
        newQuantity = parseInt(change);
    } else {
        const input = event.target.parentNode.querySelector('.quantity-input');
        const currentQty = parseInt(input.value);
        newQuantity = currentQty + parseInt(change);
    }
    
    if (newQuantity < 1) return;
    
    // Send AJAX request to update quantity
    fetch('update_quantity.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            cart_item_id: cartItemId,
            quantity: newQuantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the data attributes and order summary
            const row = document.querySelector(`[data-cart-item-id="${cartItemId}"]`);
            const checkbox = row.querySelector('.product-checkbox');
            checkbox.dataset.quantity = newQuantity;
            
            // Update the quantity input value
            const quantityInput = row.querySelector('.quantity-input');
            quantityInput.value = newQuantity;
            
            // Update subtotal display
            const price = parseFloat(checkbox.dataset.price);
            const subtotal = price * newQuantity;
            row.querySelector('.subtotal').textContent = '₹' + subtotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            // Update order summary
            updateOrderSummary();
        } else {
            alert('Error updating quantity: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating quantity');
    });
}

function removeItem(cartItemId) {
     const modalMessage = 'Are you sure you want to remove this item from your cart? This action cannot be undone.';
    showConfirmationModal(modalMessage, () => {
        fetch('remove_item.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                cart_item_id: cartItemId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error removing item: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error removing item');
        });
     }, 'confirm');
}

function clearCart() {
     const modalMessage = 'Are you sure you want to clear your entire cart? This will remove all items and cannot be undone.';
    showConfirmationModal(modalMessage, () => {
        fetch('clear_cart.php', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error clearing cart: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error clearing cart');
        });
     }, 'confirm');
}

function updateCart() {
    location.reload();
}

let currentConfirmCallback = null;
 let currentModalType = 'confirm';

 function showConfirmationModal(message, onConfirm, modalType = 'confirm', orderDetails = null) {
    console.log('showConfirmationModal called with type:', modalType);
    const modalOverlay = document.getElementById('confirmation-modal');
     const modalIcon = document.getElementById('modal-icon');
     const modalTitle = document.getElementById('modal-title');
     const modalMessage = document.getElementById('modal-message');
     const modalDetails = document.getElementById('modal-details');
     const modalButtons = document.getElementById('modal-buttons');
     
    currentConfirmCallback = onConfirm;
     currentModalType = modalType;
     
     // Reset modal to default state
     modalDetails.style.display = 'none';
     modalButtons.innerHTML = '';
     
     // Reset modal classes
     const modalContent = modalOverlay.querySelector('.modal-content');
     modalContent.classList.remove('success-modal');
     modalIcon.className = 'modal-icon';
     modalTitle.className = 'modal-title';
     modalMessage.className = 'modal-message';
     modalButtons.className = 'modal-buttons';
     
     if (modalType === 'confirm') {
         // Confirmation modal
         modalIcon.innerHTML = '<i class="bi bi-question-circle-fill"></i>';
         modalTitle.textContent = 'Confirm Checkout';
         modalMessage.textContent = message;
         
         if (orderDetails) {
             modalDetails.style.display = 'block';
             document.getElementById('modal-items-count').textContent = orderDetails.itemsCount;
             document.getElementById('modal-total-amount').textContent = '₹' + orderDetails.totalAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
         }
         
         modalButtons.innerHTML = `
             <button class="modal-btn cancel" onclick="closeModal()">Cancel</button>
             <button class="modal-btn confirm" onclick="confirmAction()">Proceed to Checkout</button>
         `;
     } else if (modalType === 'success') {
         // Success/Thank you modal
         const modalContent = modalOverlay.querySelector('.modal-content');
         modalContent.classList.add('success-modal');
         modalIcon.innerHTML = '<i class="bi bi-check-circle-fill"></i>';
         modalIcon.className = 'modal-icon success-icon';
         modalTitle.textContent = 'Thank You for Shopping!';
         modalTitle.className = 'modal-title success-title';
         modalMessage.textContent = message;
         modalMessage.className = 'modal-message success-message';
         
         // Create a better order summary layout
         if (orderDetails) {
             modalDetails.style.display = 'block';
             modalDetails.className = 'modal-details order-summary-box';
             modalDetails.innerHTML = `
                 <h4>Order Summary</h4>
                 <div class="summary-row">
                     <span class="summary-label">Total Items:</span>
                     <span class="summary-value">${orderDetails.itemsCount}</span>
                 </div>
                 <div class="summary-row">
                     <span class="summary-label">Total Amount:</span>
                     <span class="summary-value highlight">₹${orderDetails.totalAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                 </div>
             `;
         }
         
         modalButtons.className = 'modal-buttons success-actions';
         modalButtons.innerHTML = `
             <button class="success-btn" onclick="handleSuccessContinue()">
                 <i class="bi bi-arrow-left" style="margin-right: 8px;"></i>
                 Continue Shopping
             </button>
         `;
     }
    
    console.log('Displaying modal');
    modalOverlay.style.display = 'flex';
    setTimeout(() => {
        modalOverlay.classList.add('show');
        console.log('Modal shown');
    }, 50);
}

function closeModal() {
    console.log('closeModal called');
    const modalOverlay = document.getElementById('confirmation-modal');
    modalOverlay.classList.remove('show');
    setTimeout(() => {
        modalOverlay.style.display = 'none';
        console.log('Modal hidden');
    }, 300);
    currentConfirmCallback = null;
}

function confirmAction() {
    if (currentConfirmCallback) {
        currentConfirmCallback();
    }
    closeModal();
}

function handleSuccessContinue() {
    console.log('Success continue clicked');
    console.log('Form data being submitted:', document.getElementById('selected-items-input').value);
    if (currentConfirmCallback) {
        currentConfirmCallback();
    }
    closeModal();
}

// Close modal when clicking outside (only for confirmation, not success)
document.getElementById('confirmation-modal').addEventListener('click', function(event) {
    if (event.target === this && currentModalType === 'confirm') {
        closeModal();
    }
});

// Initialize order summary and attach event listeners
document.addEventListener('DOMContentLoaded', function() {
    updateOrderSummary();
    

    
    // Attach event listeners to all product checkboxes
    const productCheckboxes = document.querySelectorAll('.product-checkbox');
    productCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateOrderSummary);
    });
    
    // Attach event listener to select all checkbox
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const productCheckboxes = document.querySelectorAll('.product-checkbox');
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateOrderSummary();
        });
    }
});







function updateOrderSummary() {
    const selectedCheckboxes = document.querySelectorAll('.product-checkbox:checked');
    const totalItems = selectedCheckboxes.length;
    
    let subtotal = 0;
    let totalItemsCount = 0;
    
    selectedCheckboxes.forEach(checkbox => {
        const price = parseFloat(checkbox.dataset.price);
        const quantity = parseInt(checkbox.dataset.quantity);
        subtotal += price * quantity;
        totalItemsCount += quantity;
    });
    
    // Update display
    document.getElementById('selected-count').textContent = totalItemsCount;
    document.getElementById('selected-subtotal').textContent = '₹' + subtotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('selected-total').textContent = '₹' + subtotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    
    // Enable/disable checkout button
    const checkoutBtn = document.getElementById('checkout-btn');
    checkoutBtn.disabled = totalItems === 0;
    
    // Update selected items input (only for display purposes, not for form submission)
    const selectedItems = Array.from(selectedCheckboxes).map(checkbox => checkbox.dataset.cartItemId);
    
    // Update select all checkbox state
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const allCheckboxes = document.querySelectorAll('.product-checkbox');
    const checkedCheckboxes = document.querySelectorAll('.product-checkbox:checked');
    
    if (checkedCheckboxes.length === 0) {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
    } else if (checkedCheckboxes.length === allCheckboxes.length) {
        selectAllCheckbox.checked = true;
        selectAllCheckbox.indeterminate = false;
    } else {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = true;
    }
    
    // Update row highlighting
    const cartRows = document.querySelectorAll('.cart-item-row');
    cartRows.forEach(row => {
        const checkbox = row.querySelector('.product-checkbox');
        if (checkbox.checked) {
            row.classList.add('selected');
        } else {
            row.classList.remove('selected');
        }
    });
    
    // Update selection status message
    const selectionStatus = document.getElementById('selection-status');
    if (selectionStatus) {
        if (totalItems === 0) {
            selectionStatus.textContent = 'Select items to proceed to checkout';
            selectionStatus.style.color = '#888';
        } else if (totalItems === allCheckboxes.length) {
            selectionStatus.textContent = `All ${totalItems} items selected`;
            selectionStatus.style.color = '#28a745';
        } else {
            selectionStatus.textContent = `${totalItems} of ${allCheckboxes.length} items selected`;
            selectionStatus.style.color = '#ffc107';
        }
    }
}

function updateQuantity(cartItemId, change, isDirectInput = false) {
    let newQuantity;
    if (isDirectInput) {
        newQuantity = parseInt(change);
    } else {
        const input = event.target.parentNode.querySelector('.quantity-input');
        const currentQty = parseInt(input.value);
        newQuantity = currentQty + parseInt(change);
    }
    
    if (newQuantity < 1) return;
    
    // Send AJAX request to update quantity
    fetch('update_quantity.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            cart_item_id: cartItemId,
            quantity: newQuantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the data attributes and order summary
            const row = document.querySelector(`[data-cart-item-id="${cartItemId}"]`);
            const checkbox = row.querySelector('.product-checkbox');
            checkbox.dataset.quantity = newQuantity;
            
            // Update the quantity input value
            const quantityInput = row.querySelector('.quantity-input');
            quantityInput.value = newQuantity;
            
            // Update subtotal display
            const price = parseFloat(checkbox.dataset.price);
            const subtotal = price * newQuantity;
            row.querySelector('.subtotal').textContent = '₹' + subtotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            // Update order summary
            updateOrderSummary();
        } else {
            alert('Error updating quantity: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating quantity');
    });
}

function downloadOrderDetails() {
    const selectedCheckboxes = document.querySelectorAll('.product-checkbox:checked');
    
    if (selectedCheckboxes.length === 0) {
        alert('Please select at least one item to download.');
        return;
    }
    
    let totalItems = 0;
    let totalAmount = 0;
    let itemsHTML = '';
    
    selectedCheckboxes.forEach(checkbox => {
        const row = checkbox.closest('.cart-item-row');
        const productName = row.querySelector('.product-details h4').textContent;
        const price = parseFloat(checkbox.dataset.price);
        const quantity = parseInt(checkbox.dataset.quantity);
        const subtotal = price * quantity;
        
        totalItems += quantity;
        totalAmount += subtotal;
        
        itemsHTML += `
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">${productName}</td>
                <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: center;">₹${price.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: center;">${quantity}</td>
                <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: right;">₹${subtotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            </tr>
        `;
    });
    
    const currentDate = new Date().toLocaleDateString('en-IN');
    const currentTime = new Date().toLocaleTimeString('en-IN');
    
    // Create PDF content
    const pdfContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Order Details - GoldenDream Shop</title>
            <style>
                @page {
                    margin: 20mm;
                    size: A4;
                }
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                    color: #333;
                }
                .header {
                    text-align: center;
                    border-bottom: 3px solid #ffd600;
                    padding-bottom: 20px;
                    margin-bottom: 30px;
                }
                .logo {
                    font-size: 28px;
                    font-weight: bold;
                    color: #232526;
                    margin-bottom: 10px;
                }
                .company-info {
                    font-size: 14px;
                    color: #666;
                    margin-bottom: 10px;
                }
                .order-info {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 30px;
                    font-size: 14px;
                }
                .order-details {
                    background: #f8f9fa;
                    padding: 15px;
                    border-radius: 8px;
                    margin-bottom: 30px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 20px 0;
                }
                th {
                    background-color: #ffd600;
                    color: #232526;
                    padding: 12px 8px;
                    text-align: left;
                    font-weight: bold;
                    border-bottom: 2px solid #e6a800;
                }
                td {
                    padding: 8px;
                    border-bottom: 1px solid #eee;
                }
                .total-section {
                    margin-top: 30px;
                    text-align: right;
                    font-size: 16px;
                }
                .total-row {
                    margin: 8px 0;
                    font-weight: bold;
                }
                .footer {
                    margin-top: 50px;
                    text-align: center;
                    color: #666;
                    font-size: 14px;
                    border-top: 1px solid #eee;
                    padding-top: 20px;
                }
                .thank-you {
                    font-size: 18px;
                    color: #28a745;
                    font-weight: bold;
                    margin-bottom: 10px;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="logo">GoldenDream Shop</div>
                <div class="company-info">Quality Products, Great Prices</div>
                <div style="font-size: 16px; color: #888;">Order Details</div>
            </div>
            
            <div class="order-info">
                <div>
                    <strong>Date:</strong> ${currentDate}<br>
                    <strong>Time:</strong> ${currentTime}
                </div>
                <div>
                    <strong>Order Type:</strong> Cart Items<br>
                    <strong>Items Count:</strong> ${selectedCheckboxes.length}
                </div>
            </div>
            
            <div class="order-details">
                <h3 style="margin: 0 0 15px 0; color: #232526;">Selected Items</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th style="text-align: center;">Unit Price</th>
                            <th style="text-align: center;">Quantity</th>
                            <th style="text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemsHTML}
                    </tbody>
                </table>
            </div>
            
            <div class="total-section">
                <div class="total-row">Total Items: ${totalItems}</div>
                <div class="total-row" style="font-size: 20px; color: #28a745;">Total Amount: ₹${totalAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
            </div>
            
            <div class="footer">
                <div class="thank-you">Thank you for your purchase!</div>
                <div>GoldenDream Shop - Your Trusted Shopping Partner</div>
                <div style="margin-top: 10px;">
                    For any queries, please contact our customer support<br>
                    Email: support@goldendreamshop.com | Phone: +91-XXXXXXXXXX
                </div>
            </div>
        </body>
        </html>
    `;
    
    // Create a new window with the PDF content
    const printWindow = window.open('', '_blank');
    printWindow.document.write(pdfContent);
    printWindow.document.close();
    
    // Wait for content to load, then trigger print
    printWindow.onload = function() {
        // Use browser's print to PDF functionality
        printWindow.print();
        
        // Close the window after a short delay
        setTimeout(() => {
            printWindow.close();
        }, 1000);
    };
}

function confirmCheckout() {
    console.log('confirmCheckout called');
    const selectedCheckboxes = document.querySelectorAll('.product-checkbox:checked');
    console.log('Selected checkboxes:', selectedCheckboxes.length);
    
    if (selectedCheckboxes.length === 0) {
        alert('Please select at least one item to proceed to checkout.');
        return;
    }
    
    // Ensure selected items are properly set in the form
    const selectedItems = Array.from(selectedCheckboxes).map(checkbox => checkbox.dataset.cartItemId);
    document.getElementById('selected-items-input').value = JSON.stringify(selectedItems);
    
    const totalItems = selectedCheckboxes.length;
    let totalAmount = 0;
    let totalQuantity = 0;
    
    selectedCheckboxes.forEach(checkbox => {
        const price = parseFloat(checkbox.dataset.price);
        const quantity = parseInt(checkbox.dataset.quantity);
        totalAmount += price * quantity;
        totalQuantity += quantity;
    });
    
    const modalMessage = `You're about to place an order for ${totalQuantity} item(s) from ${totalItems} product(s). Please review the details below before proceeding.`;
    
    const orderDetails = {
        itemsCount: totalQuantity,
        totalAmount: totalAmount
    };
    // Show confirmation modal and submit form immediately after confirmation
    showConfirmationModal(modalMessage, () => {
        // Submit the form immediately after confirmation
        document.getElementById('checkout-form').submit();
    }, 'confirm', orderDetails);
}
 
 function showSuccessModal(itemsCount, totalAmount, onContinue) {
     const modalMessage = `Your order has been successfully placed! We've received your order and will process it shortly. You will receive an email confirmation with tracking details.`;
     
     const orderDetails = {
         itemsCount: itemsCount,
         totalAmount: totalAmount
     };
     
     showConfirmationModal(modalMessage, onContinue, 'success', orderDetails);
 }

// The checkout form is now handled by the confirmCheckout() function
// which shows a confirmation modal before submitting
</script>

<?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>