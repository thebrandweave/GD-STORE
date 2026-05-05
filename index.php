<?php
session_start();
require_once __DIR__ . '/config/config.php';

// Initialize variables
$categories = [];
$productCounts = [];
$products = [];
$productImages = [];

try {
    // Fetch categories and product counts
    $db = new Database();
    $conn = $db->getConnection();
    
    if ($conn) {
        $categories = $conn->query('SELECT * FROM categories ORDER BY category_id DESC')->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt = $conn->query('SELECT category_id, COUNT(*) as cnt FROM products GROUP BY category_id');
        foreach ($stmt as $row) {
            $productCounts[$row['category_id']] = $row['cnt'];
        }

        // Fetch products for homepage
        $products = $conn->query('SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id ORDER BY p.created_at DESC LIMIT 8')->fetchAll(PDO::FETCH_ASSOC);
        
        // Fetch images for all products
        if ($products) {
            $ids = array_column($products, 'product_id');
            $in = str_repeat('?,', count($ids) - 1) . '?';
            $stmt = $conn->prepare('SELECT product_id, image_url FROM product_images WHERE product_id IN (' . $in . ') ORDER BY uploaded_at ASC');
            $stmt->execute($ids);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $img) {
                $productImages[$img['product_id']][] = $img['image_url'];
            }
        }
    }
} catch (Exception $e) {
    // Log the error
    error_log("Shop index error: " . $e->getMessage());
    
    // Redirect to local error page
    header("Location: error.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GoldenDream Shop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: #f7f7fa;
            --secondary: #232526;
            --accent: #ffc929;
            --accent-dark: #ffc929;
            --card-bg: #fff;
            --card-blur: blur(12px);
            --radius: 22px;
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
        
        .hero {
            max-width: 1400px;
            margin: 48px auto 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 48px;
            flex-wrap: wrap;
            padding: 0 20px;
        }
        .hero-content {
            flex: 1 1 350px;
        }
        .hero-title {
            font-size: 2.8rem;
            font-weight: 900;
            color: var(--accent-dark);
            margin-bottom: 18px;
            letter-spacing: 1.5px;
        }
        .hero-desc {
            font-size: 1.18rem;
            color: #555;
            margin-bottom: 32px;
            max-width: 480px;
        }
        .hero-btn {
            background: var(--accent);
            color: #232526;
            font-weight: 700;
            border: none;
            border-radius: 999px;
            padding: 14px 38px;
            font-size: 1.15rem;
            box-shadow: 0 2px 12px rgba(140,108,79,0.10);
            cursor: pointer;
            transition: background 0.18s, color 0.18s;
        }
        .hero-btn:hover {
            background: var(--accent-dark);
            color: #fff;
        }
        .hero-image {
            flex: 1 1 350px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hero-image img {
            width: 340px;
            max-width: 90vw;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            background: #fff;
        }
        .features-section {
            max-width: 1400px;
            margin: 80px auto 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 32px;
            padding: 0 20px;
            position: relative;
            z-index: 0;
        }
        .feature-card {
            background: #fff;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 32px 24px 28px 24px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            backdrop-filter: var(--card-blur);
            -webkit-backdrop-filter: var(--card-blur);
            border: 1.5px solid rgba(255,255,255,0.08);
        }
        .feature-icon {
            font-size: 2.2rem;
            color: var(--accent);
            margin-bottom: 16px;
        }
        .feature-title {
            font-size: 1.18rem;
            font-weight: 700;
            color: var(--accent-dark);
            margin-bottom: 8px;
        }
        .feature-desc {
            color: #555;
            font-size: 1rem;
        }
        .products-section {
            max-width: 1400px;
            margin: 120px auto 0 auto;
            padding: 0 20px;
            margin-bottom: 80px;
            position: relative;
            z-index: 0;
        }
        .products-title {
            font-size: 2rem;
            font-weight: 800;
            color: var(--accent-dark);
            margin-bottom: 8px;
            letter-spacing: 1px;
            text-align: center;
        }
        .products-subtitle {
            color: #555;
            font-size: 1.08rem;
            margin-bottom: 32px;
            text-align: center;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 32px;
            margin-top: 24px;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }
        .product-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            position: relative;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            border: 1px solid #f0f0f0;
            cursor: pointer;
        }
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.15);
        }
        .product-image-container {
            position: relative;
            width: 100%;
            height: 280px;
            overflow: hidden;
            background: #f8f9fa;
            border-radius: 12px 12px 0 0;
        }
        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        .product-card:hover .product-image {
            transform: scale(1.08);
        }
        .product-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: #4a6072;
            color: #fff;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 4px;
            z-index: 2;
            transform: translateY(-2px);
            opacity: 0.9;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        .product-card:hover .product-badge {
            transform: translateY(0);
            opacity: 1;
        }
        .product-hot-sale {
            position: absolute;
            bottom: 12px;
            left: 12px;
            background: #000;
            color: #fff;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 6px 10px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
            transform: translateY(2px);
            opacity: 0.9;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        .product-card:hover .product-hot-sale {
            transform: translateY(0);
            opacity: 1;
        }
        .product-content {
            padding: 16px;
            transform: translateY(0);
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        .product-card:hover .product-content {
            transform: translateY(-4px);
        }
        .product-name {
            font-size: 1rem;
            font-weight: 600;
            color: #2d3436;
            line-height: 1.4;
            margin: 0 0 8px 0;
            transition: color 0.3s ease;
        }
        .product-card:hover .product-name {
            color: #ffc929;
        }
        .product-price-container {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }
        .product-price {
            color: #2d3436;
            font-size: 1.1rem;
            font-weight: 700;
        }
        .product-original-price {
            color: #636e72;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: line-through;
        }

        .product-btn {
            flex: 1;
            background: #2d3436;
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            padding: 10px 16px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
            overflow: hidden;
        }
        .product-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        .product-btn:hover::before {
            left: 100%;
        }
        .product-btn:hover {
            background: #4a6072;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74,96,114,0.3);
        }
        .product-wishlist {
            background: #fff;
            border: 1px solid #ddd;
            color: #636e72;
            border-radius: 6px;
            padding: 10px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .product-wishlist::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: #4a6072;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: all 0.3s ease;
        }
        .product-wishlist:hover::before {
            width: 100%;
            height: 100%;
        }
        .product-wishlist:hover {
            color: #fff;
            border-color: #4a6072;
        }
        .product-wishlist i {
            position: relative;
            z-index: 1;
        }
        .product-color-swatches {
            display: flex;
            gap: 6px;
            margin-top: 8px;
            transform: translateY(0);
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        .product-card:hover .product-color-swatches {
            transform: translateY(-2px);
        }
        .color-swatch {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
        }
        .color-swatch::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255,255,255,0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: all 0.3s ease;
        }
        .color-swatch:hover {
            transform: scale(1.3);
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        .color-swatch:hover::before {
            width: 100%;
            height: 100%;
        }
        @media (max-width: 1200px) {
            .products-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 24px;
            }
            .benefits-container { 
                grid-template-columns: repeat(2, 1fr); 
                gap: 20px;
            }
        }
        
        @media (min-width: 1600px) {
            .products-grid {
                grid-template-columns: repeat(5, 1fr);
                gap: 40px;
            }
            .features-section {
                grid-template-columns: repeat(4, 1fr);
                gap: 40px;
            }
            .hero-title {
                font-size: 3rem;
            }
            .hero-desc {
                font-size: 1.25rem;
            }
            .products-title,
            .shop-category-title {
                font-size: 2.2rem;
            }
            .products-subtitle,
            .shop-category-subtitle {
                font-size: 1.15rem;
            }
        }
        
        @media (min-width: 1920px) {
            .products-grid {
                grid-template-columns: repeat(6, 1fr);
                gap: 48px;
            }
            .features-section {
                grid-template-columns: repeat(5, 1fr);
                gap: 48px;
            }
            .hero-title {
                font-size: 3.2rem;
            }
            .hero-desc {
                font-size: 1.3rem;
            }
            .products-title,
            .shop-category-title {
                font-size: 2.4rem;
            }
            .products-subtitle,
            .shop-category-subtitle {
                font-size: 1.2rem;
            }
        }
        @media (max-width: 900px) {
            .hero { 
                flex-direction: column; 
                gap: 32px; 
                padding: 0 20px;
            }
            .hero-title {
                font-size: 2.2rem;
            }
            .hero-desc {
                font-size: 1.1rem;
            }
            .features-section { 
                grid-template-columns: 1fr; 
                gap: 24px;
                padding: 0 20px;
            }
            .products-title { 
                font-size: 1.8rem; 
            }
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                padding: 0 20px;
            }
            .products-section {
                padding: 0 20px;
            }
            .shop-category-section {
                padding: 0 20px;
            }
            .shop-category-title {
                font-size: 1.8rem;
            }
            .fullimg-hero-category { 
                font-size: 1.5rem; 
            }
            .fullimg-hero-content { 
                max-width: 98vw; 
                padding: 0 20px;
            }
            .fullimg-hero-btns { 
                flex-direction: column; 
                gap: 12px; 
            }
            .fullimg-hero-btn {
                padding: 12px 28px;
                font-size: 1rem;
            }

            .benefits-container {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
            .benefit-item {
                padding: 0 12px;
            }
            .benefit-icon {
                font-size: 2.2rem;
                margin-bottom: 14px;
            }
            .benefit-title {
                font-size: 0.9rem;
                line-height: 1.3;
            }
        }
        @media (max-width: 768px) {
            .hero {
                margin: 32px auto 0 auto;
                gap: 24px;
            }
            .hero-title {
                font-size: 1.8rem;
                margin-bottom: 12px;
            }
            .hero-desc {
                font-size: 1rem;
                margin-bottom: 24px;
            }
            .hero-btn {
                padding: 12px 28px;
                font-size: 1rem;
            }
            .hero-image img {
                width: 280px;
            }
            .features-section {
                margin: 60px auto 0 auto;
                gap: 20px;
            }
            .feature-card {
                padding: 24px 20px;
            }
            .feature-icon {
                font-size: 1.8rem;
            }
            .feature-title {
                font-size: 1.1rem;
            }
            .products-section {
                margin: 100px auto 0 auto;
            }
            .products-title {
                font-size: 1.6rem;
            }
            .products-subtitle {
                font-size: 1rem;
            }
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 16px;
            }
            .product-card {
                border-radius: 12px;
            }
            .product-image-container {
                height: 200px;
            }
            .product-content {
                padding: 12px;
            }
            .product-name {
                font-size: 0.9rem;
            }
            .product-price {
                font-size: 1rem;
            }
            .view-details-btn {
                padding: 8px 12px;
                font-size: 0.8rem;
            }
            .shop-category-title {
                font-size: 1.6rem;
            }
            .shop-category-subtitle {
                font-size: 1rem;
            }
            .shop-category-card {
                padding: 20px 16px;
            }
            .shop-category-img {
                width: 100px;
                height: 100px;
            }
            .shop-category-name {
                font-size: 1rem;
            }
            .shop-category-btn {
                padding: 8px 18px;
                font-size: 0.9rem;
            }
            .fullimg-hero-carousel {
                min-height: 60vh;
            }
            .fullimg-hero-category {
                font-size: 1.3rem;
                margin-bottom: 8px;
            }
            .fullimg-hero-tagline {
                font-size: 0.9rem;
                margin-bottom: 20px;
            }
            .fullimg-hero-btns {
                gap: 10px;
                margin-bottom: 24px;
            }
            .fullimg-hero-btn {
                padding: 10px 24px;
                font-size: 0.9rem;
            }
            .fullimg-hero-dots {
                right: 20px;
                gap: 12px;
            }
            .fullimg-hero-dot {
                width: 8px;
                height: 8px;
            }
            .benefits-merged {
                display: none;
            }
            .benefits-mobile {
                display: block;
            }
            .benefits-section {
                margin-top: 50px;
            }

            .benefits-container {
                grid-template-columns: repeat(2, 1fr);
                gap: 16px;
            }
            .benefit-item {
                padding: 0 10px;
            }
            .benefit-icon {
                font-size: 1.8rem;
                margin-bottom: 12px;
            }
            .benefit-title {
                font-size: 0.8rem;
                line-height: 1.3;
                margin-bottom: 0;
            }
            .newsletter-section {
                padding: 48px 20px 56px 20px;
            }
            .newsletter-title {
                font-size: 1.8rem;
            }
            .newsletter-subtitle {
                font-size: 1rem;
            }
            .newsletter-form {
                max-width: 100%;
                flex-direction: column;
                border-radius: 16px;
            }
            .newsletter-input,
            .newsletter-btn {
                border-radius: 16px;
                padding: 14px 20px;
                font-size: 1rem;
            }
            .newsletter-btn {
                margin-top: 8px;
            }
            .view-all-products-btn {
                padding: 14px 32px;
                font-size: 1rem;
                margin-bottom: 25px;
            }
        }
        @media (max-width: 600px) {
            .hero {
                margin: 24px auto 0 auto;
                gap: 20px;
            }
            .hero-title {
                font-size: 1.5rem;
            }
            .hero-desc {
                font-size: 0.9rem;
            }
            .hero-btn {
                padding: 10px 24px;
                font-size: 0.9rem;
            }
            .hero-image img {
                width: 240px;
            }
            .features-section {
                margin: 40px auto 0 auto;
                gap: 16px;
            }
            .feature-card {
                padding: 20px 16px;
            }
            .feature-icon {
                font-size: 1.6rem;
            }
            .feature-title {
                font-size: 1rem;
            }
            .feature-desc {
                font-size: 0.9rem;
            }
            .products-section {
                margin: 60px auto 0 auto;
            }
            .products-title {
                font-size: 1.4rem;
            }
            .products-subtitle {
                font-size: 0.9rem;
            }
            .products-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            .product-card {
                border-radius: 10px;
            }
            .product-image-container {
                height: 180px;
            }
            .product-content {
                padding: 10px;
            }
            .product-name {
                font-size: 0.85rem;
            }
            .product-price {
                font-size: 0.9rem;
            }
            .view-details-btn {
                padding: 6px 10px;
                font-size: 0.75rem;
            }
            .shop-category-title {
                font-size: 1.2rem;
            }
            .shop-category-subtitle {
                font-size: 0.8rem;
            }
            .shop-category-card {
                padding: 16px 12px;
            }
            .shop-category-img {
                width: 80px;
                height: 80px;
            }
            .shop-category-name {
                font-size: 0.8rem;
            }
            .shop-category-btn {
                padding: 6px 14px;
                font-size: 0.8rem;
            }
            .fullimg-hero-carousel {
                min-height: 50vh;
            }
            .fullimg-hero-category {
                font-size: 1.1rem;
            }
            .fullimg-hero-tagline {
                font-size: 0.8rem;
            }
            .fullimg-hero-btns {
                gap: 8px;
                margin-bottom: 20px;
            }
            .fullimg-hero-btn {
                padding: 8px 20px;
                font-size: 0.75rem;
            }
            .fullimg-hero-dots {
                right: 16px;
                gap: 10px;
            }
            .fullimg-hero-dot {
                width: 6px;
                height: 6px;
            }
            .benefits-section {
                margin-top: 40px;
            }

            .benefits-container {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .benefit-item {
                padding: 0 12px;
            }
            .benefit-icon {
                font-size: 1.6rem;
                margin-bottom: 12px;
            }
            .benefit-title {
                font-size: 0.8rem;
                line-height: 1.3;
            }
            .newsletter-section {
                padding: 40px 16px 48px 16px;
            }
            .newsletter-title {
                font-size: 1.5rem;
            }
            .newsletter-subtitle {
                font-size: 0.8rem;
            }
            .newsletter-form {
                border-radius: 12px;
            }
            .newsletter-input,
            .newsletter-btn {
                border-radius: 12px;
                padding: 12px 16px;
                font-size: 0.9rem;
            }
            .view-all-products-btn {
                padding: 12px 28px;
                font-size: 0.9rem;
            }
        }
        @media (max-width: 480px) {
            .hero {
                margin: 20px auto 0 auto;
                gap: 16px;
            }
            .hero-title {
                font-size: 1.3rem;
            }
            .hero-desc {
                font-size: 0.85rem;
            }
            .hero-btn {
                padding: 8px 20px;
                font-size: 0.85rem;
            }
            .hero-image img {
                width: 200px;
            }
            .features-section {
                margin: 40px auto 0 auto;
                gap: 12px;
            }
            .feature-card {
                padding: 16px 12px;
            }
            .feature-icon {
                font-size: 1.4rem;
            }
            .feature-title {
                font-size: 0.9rem;
            }
            .feature-desc {
                font-size: 0.8rem;
            }
            .products-section {
                margin: 60px auto 0 auto;
            }
            .products-title {
                font-size: 1.2rem;
            }
            .products-subtitle {
                font-size: 0.8rem;
            }
            .products-grid {
                gap: 12px;
            }
            .product-card {
                border-radius: 8px;
            }
            .product-image-container {
                height: 160px;
            }
            .product-content {
                padding: 8px;
            }
            .product-name {
                font-size: 0.8rem;
            }
            .product-price {
                font-size: 0.85rem;
            }
            .view-details-btn {
                padding: 5px 8px;
                font-size: 0.7rem;
            }
            .shop-category-title {
                font-size: 1.2rem;
            }
            .shop-category-subtitle {
                font-size: 0.8rem;
            }
            .shop-category-card {
                padding: 12px 8px;
            }
            .shop-category-img {
                width: 60px;
                height: 60px;
            }
            .shop-category-name {
                font-size: 0.8rem;
            }
            .shop-category-btn {
                padding: 5px 10px;
                font-size: 0.7rem;
            }
            .fullimg-hero-carousel {
                min-height: 40vh;
            }
            .fullimg-hero-category {
                font-size: 1rem;
            }
            .fullimg-hero-tagline {
                font-size: 0.75rem;
            }
            .fullimg-hero-btns {
                gap: 6px;
                margin-bottom: 16px;
            }
            .fullimg-hero-btn {
                padding: 6px 16px;
                font-size: 0.75rem;
            }
            .fullimg-hero-dots {
                right: 12px;
                gap: 8px;
            }
            .fullimg-hero-dot {
                width: 5px;
                height: 5px;
            }
            .benefits-section {
                margin-top: 30px;
            }

            .benefits-container {
                gap: 16px;
            }
            .benefit-item {
                padding: 0 8px;
            }
            .benefit-icon {
                font-size: 1.4rem;
                margin-bottom: 10px;
            }
            .benefit-title {
                font-size: 0.75rem;
                line-height: 1.3;
            }
            .newsletter-section {
                padding: 32px 12px 40px 12px;
            }
            .newsletter-title {
                font-size: 1.3rem;
            }
            .newsletter-subtitle {
                font-size: 0.8rem;
            }
            .newsletter-form {
                border-radius: 8px;
            }
            .newsletter-input,
            .newsletter-btn {
                border-radius: 8px;
                padding: 10px 12px;
                font-size: 0.8rem;
            }
            .view-all-products-btn {
                padding: 10px 24px;
                font-size: 0.8rem;
            }
        }
        
        
        .section-divider {
            background: #ececec;
        }
        #contact {
            color: #232526;
        }
        .fullimg-hero-carousel {
            position: relative;
            width: 100vw;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
            min-height: 80vh;
            max-height: 700px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
        .fullimg-hero-slide {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 0.7s cubic-bezier(.4,0,.2,1);
            z-index: 1;
        }
        .fullimg-hero-slide.active {
            opacity: 1;
            z-index: 2;
        }
        .fullimg-hero-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(120deg, rgba(34,34,34,0.45) 0%, rgba(140,108,79,0.18) 100%);
            z-index: 3;
        }
        .fullimg-hero-content {
            position: relative;
            z-index: 4;
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            text-align: center;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .fullimg-hero-category {
            font-size: 1.3rem;
            font-weight: 900;
            letter-spacing: 1.5px;
            margin-bottom: 12px;
            color: #fff;
            text-shadow: 0 2px 16px rgba(0,0,0,0.18);
        }
        .fullimg-hero-category .gold {
            color: #ffc929;
            background: linear-gradient(90deg, #ffc929 0%, #eae7b0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .fullimg-hero-tagline {
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 24px;
            color: #fdfbf9;
            text-shadow: 0 2px 12px rgba(0,0,0,0.18);
        }
        .fullimg-hero-btns {
            display: flex;
            gap: 22px;
            justify-content: center;
            margin-bottom: 32px;
        }
        .fullimg-hero-btn {
            padding: 15px 38px;
            font-size: 1.08rem;
            font-weight: 800;
            border-radius: 999px;
            border: none;
            cursor: pointer;
            transition: background 0.18s, color 0.18s, box-shadow 0.18s;
            box-shadow: 0 2px 18px rgba(140,108,79,0.10);
            text-decoration: none;
            display: inline-block;
        }
        .fullimg-hero-btn.gold {
            background: linear-gradient(90deg, #ffc929 0%, #f8e8a3 100%);
            color: #232526;
            border: 2.5px solid #ffc929;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        .fullimg-hero-btn.gold:hover {
            background: #ffffff;
            color: #6b5139;
            box-shadow: 0 4px 32px rgba(140,108,79,0.22);
            transform: translateY(-3px); /* Subtle lift adds to the "premium" feel */
        }
        .fullimg-hero-btn.outline {
            background: #fff;
            color: #232526;
            border: 2.5px solid #ffc929;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        .fullimg-hero-btn.outline:hover {
       background: linear-gradient(90deg, #ffc929 0%, #f8e8a3 100%);
            color: #232526;
                        transform: translateY(-3px); /* Subtle lift adds to the "premium" feel */

        }
        .fullimg-hero-dots {
            display: flex;
            flex-direction: column;
            gap: 16px;
            position: absolute;
            top: 50%;
            right: 32px;
            left: auto;
            bottom: auto;
            transform: translateY(-50%);
            margin: 0;
            z-index: 5;
            justify-content: flex-start;
            align-items: center;
        }
        .fullimg-hero-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ffc929;
            opacity: 0.35;
            border: 2px solid #ffc929;
            cursor: pointer;
            transition: opacity 0.2s, background 0.2s;
            display: flex;
            flex-direction: column;
            
        }
        .fullimg-hero-dot.active {
            opacity: 1;
            background: #6b5139;
        }
        @media (max-width: 900px) {
            .fullimg-hero-category { font-size: 1.5rem; }
            .fullimg-hero-content { max-width: 98vw; padding: 0 20px; }
            .fullimg-hero-dots {
                right: 20px;
            }
        }
        @media (max-width: 600px) {
            .fullimg-hero-category { font-size: 1.1rem; }
            .fullimg-hero-btns { flex-direction: column; gap: 12px; }
            .fullimg-hero-content { padding: 0 16px; }
            .fullimg-hero-dots {
                right: 16px;
            }
        }
        @media (max-width: 480px) {
            .fullimg-hero-content { padding: 0 12px; }
            .fullimg-hero-dots {
                right: 12px;
            }
        }
        /* Shop by Category Section */
        .shop-category-section {
            /* margin: 50px auto 0 auto;
            padding: 10px 0; */
            text-align: center;
            position: relative;
            z-index: 0;
        }
        .shop-category-section a {
            text-decoration: none;
            color: #232526;
        }
        /* Ensure benefits overlay doesn't overlap the category section on desktop */
        @media (min-width: 901px) {
            .shop-category-section {
                margin: 100px auto 0 auto;
            }
        }
        .shop-category-title {
            font-size: 2.6rem;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        .shop-category-title .gold {
            color: #ffc929;
            background: linear-gradient(90deg, #ffc929 0%, #6b5139 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .shop-category-subtitle {
            color: #555;
            font-size: 1.08rem;
            margin-bottom: 32px;
        }
        .shop-category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 32px;
        }
        .shop-category-card {
            /* background: rgba(255,255,255,0.92); */
            /* border-radius: 22px; */
            /* box-shadow: 0 8px 32px 0 rgba(140,108,79,0.08), 0 2px 12px rgba(0,0,0,0.04); */
            padding: 24px 18px 18px 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            border: 1.5px solid rgba(255,255,255,0.08);
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .shop-category-card:hover {
            /* box-shadow: 0 12px 40px 0 rgba(140,108,79,0.13), 0 4px 24px rgba(0,0,0,0.08); */
            transform: translateY(-6px) scale(1.03);
        }
        .shop-category-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 100px;
            margin-bottom: 16px;
            /* background: #fff; */
            box-shadow: 0 2px 8px rgba(140,108,79,0.10);
        }
        .shop-category-name {
            font-size: 1.08rem;
            font-weight: 700;
            color: #232526;
            margin-bottom: 12px;
            text-align: center;
        }
        .shop-category-btn {
            background: linear-gradient(90deg, #ffc929 0%, #6b5139 100%);
            color: #232526;
            font-weight: 700;
            border: none;
            border-radius: 999px;
            padding: 8px 22px;
            font-size: 1rem;
            box-shadow: 0 2px 8px rgba(140,108,79,0.10);
            cursor: pointer;
            transition: background 0.18s, color 0.18s;
            text-decoration: none;
            display: inline-block;
        }
        .shop-category-btn:hover {
            background: #6b5139;
            color: #fff;
            text-decoration: none;
        }
        @media (max-width: 600px) {
            .shop-category-title { font-size: 1.2rem; }
            .shop-category-grid { gap: 18px; }
            .shop-category-card { padding: 12px 4px 8px 4px; }
            .shop-category-img { width: 80px; height: 80px; }
        }
    .shop-category-carousel-wrapper {
        position: relative;
        max-width: 1400px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        padding: 0 16px;
        z-index: 0;
    }
    .shop-category-carousel {
        display: flex;
        gap: 32px;
        overflow-x: auto;
        scroll-behavior: smooth;
        padding: 24px 32px 18px 32px;
        width: 100%;
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* IE 10+ */
    }
    .shop-category-carousel::-webkit-scrollbar {
        display: none;
    }
    .shop-category-card {
        min-width: 220px;
        max-width: 240px;
        flex: 0 0 23%;
    
        border-radius: 22px;
        /* box-shadow: 0 8px 32px 0 rgba(140,108,79,0.08), 0 2px 12px rgba(0,0,0,0.04); */
        padding: 24px 18px 18px 18px;
        display: flex;
        flex-direction: column;
        align-items: center;
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        border: 1.5px solid rgba(255,255,255,0.08);
        transition: box-shadow 0.2s, transform 0.2s;
    }
    .shop-category-card:hover {
        /* box-shadow: 0 12px 40px 0 rgba(140,108,79,0.13), 0 4px 24px rgba(0,0,0,0.08); */
        transform: translateY(-8px) scale(1.05);
    }
    .shop-category-arrow {
        background: #fff;
        border: none;
        border-radius: 50%;
        width: 44px;
        height: 44px;
        font-size: 1.6rem;
        color: #ffc929;
        box-shadow: 0 2px 8px rgba(140,108,79,0.10);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 2;
        transition: background 0.18s, color 0.18s;
    }
    .shop-category-arrow.left { left: -18px; }
    .shop-category-arrow.right { right: -18px; }
    .shop-category-arrow:hover { background: #ffc929; color: #fff; }
    @media (max-width: 1100px) {
        .shop-category-card { flex: 0 0 32%; }
    }
    @media (max-width: 900px) {
        .shop-category-card { flex: 0 0 48%; }
        .shop-category-carousel-wrapper {
            padding: 0 20px;
        }
    }
    @media (max-width: 768px) {
        .shop-category-card { flex: 0 0 60%; min-width: 200px; }
        .shop-category-carousel { gap: 16px; }
        .shop-category-arrow { width: 40px; height: 40px; font-size: 1.4rem; }
        .shop-category-carousel-wrapper {
            padding: 0 16px;
        }
    }
    @media (max-width: 600px) {
        .shop-category-card { flex: 0 0 75%; min-width: 180px; }
        .shop-category-carousel { gap: 14px; }
        .shop-category-arrow { width: 36px; height: 36px; font-size: 1.2rem; }
        .shop-category-carousel-wrapper {
            padding: 0 12px;
        }
    }
    @media (max-width: 480px) {
        .shop-category-card { flex: 0 0 85%; min-width: 160px; }
        .shop-category-carousel { gap: 12px; }
        .shop-category-arrow { width: 32px; height: 32px; font-size: 1rem; }
        .shop-category-carousel-wrapper {
            padding: 0 8px;
        }
    }
    .product-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        position: relative;
    }
    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 32px rgba(0,0,0,0.15);
    }
    .product-image-box {
        background: #fafbfc;
        border-radius: 18px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        padding: 24px 18px 18px 18px;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 220px;
        height: 300px;
    }
    .product-image {
        width: 100%;
        object-fit: contain;
        background: #fff;
        box-shadow: none;
    }
    .product-content {
        padding: 20px;
    }
    .product-category {
        color: var(--accent-dark);
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    .product-category a {
        text-decoration: none;
        color: #232526;
    }
    .product-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--secondary);
        margin-bottom: 12px;
        line-height: 1.4;
    }
    .product-name a {
        text-decoration: none;
        color: #232526;
    }
    .product-description {
        font-size: 0.9rem;
        color: #666;
        line-height: 1.4;
        margin-bottom: 16px;
    }
    .product-info {
        display: flex;  
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .product-price {
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--secondary);
    }
    .product-stock {
        font-size: 0.9rem;
        color: #666;
        font-weight: 500;
    }
    .product-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .view-details-btn {
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
        color: #232526;
        border: none;
        border-radius: 8px;
        padding: 8px 16px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .view-details-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 214, 0, 0.3);
    }
    .product-original-price {
        color: #999;
        font-size: 1rem;
        font-weight: 600;
        text-decoration: line-through;
        text-align: center;
    }
    .product-btn {
        background: linear-gradient(90deg, #ffc929 0%, #6b5139 100%);
        color: #232526;
        font-weight: 700;
        border: none;
        border-radius: 999px;
        padding: 10px 24px;
        font-size: 1rem;
        box-shadow: 0 2px 8px rgba(140,108,79,0.10);
        cursor: pointer;
        transition: background 0.18s, color 0.18s;
        margin-top: 4px;
    }
    .product-btn:hover {
        background: #6b5139;
        color: #fff;
    }
    .product-action-icons {
        position: absolute;
        top: 18px;
        right: 18px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        opacity: 0;
        pointer-events: none;
        transform: translateY(10px);
        transition: opacity 0.35s cubic-bezier(.39,.575,.56,1), transform 0.35s cubic-bezier(.39,.575,.56,1);
        z-index: 2;
    }
    .product-card:hover .product-action-icons {
        opacity: 1;
        pointer-events: auto;
        transform: translateY(0);
    }
    .product-action-btn {
        background: rgba(255,255,255,0.95);
        border: none;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: #2d3436;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        cursor: pointer;
        transition: background 0.2s, color 0.2s, box-shadow 0.2s;
    }
    .product-action-btn:hover {
        background: #6b5139;
        color: #fff;
        box-shadow: 0 4px 16px rgba(140,108,79,0.18);
    }
    .product-hover-cart-btn {
        position: absolute;
        left: 50%;
        bottom: 18px;
        transform: translate(-50%, 20px);
        opacity: 0;
        background: #fff;
        color: #2d3436;
        border: none;
        border-radius: 20px;
        padding: 8px 0;
        width: 70%;
        font-size: 0.9rem;
        font-weight: 700;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        cursor: pointer;
        transition: opacity 0.35s cubic-bezier(.39,.575,.56,1), transform 0.35s cubic-bezier(.39,.575,.56,1);
        z-index: 2;
    }
    .product-card:hover .product-hover-cart-btn {
        opacity: 1;
        transform: translate(-50%, 0);
    }
    .product-hover-cart-btn:hover {
        background: #6b5139;
        color: #fff;
    }
    .product-image-container {
        position: relative;
        height: 240px;
        overflow: hidden;
        background: #f8f9fa;
    }
    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .product-card:hover .product-image {
        transform: scale(1.1);
    }
    .product-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: var(--accent);
        color: #232526;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        z-index: 2;
    }
    .product-stock-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
        z-index: 2;
    }
    .stock-high {
        background: #28a745;
        color: white;
    }
    .stock-medium {
        background: #ffc107;
        color: #232526;
    }
    .stock-low {
        background: #dc3545;
        color: white;
    }
    .product-actions {
        position: absolute;
        bottom: 12px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 12px;
        opacity: 0;
        transition: all 0.3s ease;
        z-index: 3;
    }
    .product-card:hover .product-actions {
        opacity: 1;
    }
    .action-btn {
        width: 36px;
        height: 36px;
        background: rgba(255,255,255,0.95);
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #232526;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .action-btn:hover {
        background: var(--accent);
        color: #232526;
        transform: scale(1.1);
    }
    /* Benefits Section */
    .benefits-section {
        position: relative;
        margin-top: -60px;
        margin-bottom: 10px;
    
        width: 100%;
        padding: 0 16px;
        text-align: center;
        z-index: 99 !important;
        background: rgba(0, 0, 0, 0.9);
        border-radius: 16px;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        /* margin-left: auto;
        margin-right: auto; */
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
        .hero,
        .features-section,
        .products-section,
        .benefits-section,
        .shop-category-carousel-wrapper {
            max-width: 1600px;
        }
        .products-grid {
            max-width: 1600px;
        }
    }
    
    @media (min-width: 1920px) {
        .container-fluid {
            max-width: 1800px;
            padding: 0 60px;
        }
        .hero,
        .features-section,
        .products-section,
        .benefits-section,
        .shop-category-carousel-wrapper {
            max-width: 1800px;
        }
        .products-grid {
            max-width: 1800px;
        }
    }
    
        @media (min-width: 2560px) {
            .container-fluid {
                max-width: 2200px;
                padding: 0 80px;
            }
            .hero,
            .features-section,
            .products-section,
            .benefits-section,
            .shop-category-carousel-wrapper {
                max-width: 2200px;
            }
            .products-grid {
                max-width: 2200px;
            }
            .hero {
                margin: 64px auto 0 auto;
                gap: 64px;
            }
            .features-section {
                margin: 120px auto 0 auto;
                gap: 48px;
            }
            .products-section {
                margin: 160px auto 0 auto;
            }
            .shop-category-section {
                margin: 140px auto 0 auto;
            }
        }
    
    /* Benefits Section - Responsive Design */
    
    /* Benefits Section - Mobile Responsive */
    @media (max-width: 900px) {
        .benefits-section {
            margin-top: 0px;
            margin-bottom: 40px;
            background: rgb(0, 0, 0);
            border-radius: 0;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.10);
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
        }
    }
    
    .benefits-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255, 214, 0, 0.05) 0%, rgba(0, 0, 0, 0) 100%);
        pointer-events: none;
        border-radius: 16px;
    }
    
    @media (max-width: 900px) {
        .benefits-section::before {
            border-radius: 0;
        }
    }
    
    .benefits-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0;
        background: none;
        position: relative;
        z-index: 1;
        padding: 24px 16px;
    }
    .benefit-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 0 18px;
        transition: transform 0.3s ease;
    }
    .benefit-item:hover {
        transform: translateY(-5px);
    }
    .benefit-icon {
        font-size: 2.8rem;
        color: var(--accent);
        margin-bottom: 16px;
        transition: transform 0.3s ease;
    }
    .benefit-item:hover .benefit-icon {
        transform: scale(1.1);
    }
    .benefit-title {
        font-size: 1rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 6px;
        line-height: 1.3;
    }

    @media (max-width: 1200px) {
        .benefits-container { 
            grid-template-columns: repeat(2, 1fr); 
            gap: 20px;
        }
        .benefit-item {
            padding: 0 12px;
        }
    }
    @media (max-width: 900px) {
        .benefits-container { 
            grid-template-columns: repeat(2, 1fr); 
            gap: 20px;
            padding: 36px 32px 32px 32px;
        }
        .benefit-item {
            padding: 0 12px;
        }
    }

    @media (max-width: 600px) {
        .benefits-container { 
            grid-template-columns: 1fr; 
            gap: 24px;
            padding: 24px 20px;
        }
        .benefit-item {
            padding: 0 16px;
        }
    }
    
    @media (max-width: 360px) {
        .benefits-container {
            gap: 20px;
            padding: 20px 12px;
        }
        .benefit-item {
            padding: 0 10px;
        }
        .benefit-icon {
            font-size: 1.3rem;
            margin-bottom: 8px;
        }
        .benefit-title {
            font-size: 0.7rem;
        }
    }

        
    .view-all-products-btn {
        display: inline-block;
        background: #ffc929;
        color: #23211a;
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        font-size: 1.08rem;
        padding: 16px 44px;
        border-radius: 999px;
        text-decoration: none;
        letter-spacing: 0.08em;
        box-shadow: 0 2px 8px 0 rgba(140,108,79,0.08);
        transition: background 0.18s, color 0.18s, transform 0.18s;
        border: none;
    }
    .view-all-products-btn:hover {
        background: #a58161;
        color: #23211a;
        transform: scale(0.97);
    }
    @media (max-width: 768px) {
        .view-all-products-btn {
            padding: 14px 32px;
            font-size: 1rem;
        }
    }
    @media (max-width: 480px) {
        .view-all-products-btn {
            padding: 12px 28px;
            font-size: 0.9rem;
        }
    }
    /* Preloader Styles */
#preloader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: var(--primary); /* Matches your page background */
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000000; /* Higher than any other element */
    transition: opacity 0.5s ease, visibility 0.5s;
}

.loader-content {
    text-align: center;
    position: relative;
}

.loader-logo {
    width: 120px; /* Adjust size as needed */
    height: auto;
    margin-bottom: 20px;
    animation: pulseLogo 1.5s ease-in-out infinite;
}

.loader-line {
    width: 100px;
    height: 3px;
    background: rgba(0,0,0,0.1);
    margin: 0 auto;
    position: relative;
    overflow: hidden;
    border-radius: 10px;
}

.loader-line::after {
    content: '';
    position: absolute;
    left: -100%;
    width: 100%;
    height: 100%;
    background: var(--accent);
    animation: loadingLine 1.5s infinite;
}

/* Animations */
@keyframes pulseLogo {
    0%, 100% { transform: scale(1); opacity: 0.8; }
    50% { transform: scale(1.05); opacity: 1; }
}

@keyframes loadingLine {
    0% { left: -100%; }
    50% { left: 0%; }
    100% { left: 100%; }
}

/* Class to hide loader */
#preloader.fade-out {
    opacity: 0;
    visibility: hidden;
}

/* Promo Bar */
.promo-bar {
    background: linear-gradient(90deg, #f3d2b280, #ffffff);
    display: flex;
    justify-content: space-around;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}

.promo-text { font-size: 1.5rem; color: var(--accent-orange); }
.promo-code { background: #fff; padding: 5px 15px; border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }

/* Hero Wrapper */
.shop-hero { height: 500px; background: #fff; overflow: hidden; }
.shop-hero-wrapper { display: flex; height: 100%; }

/* Left Visuals */
.shop-hero-visual {
    flex: 0 0 70%; /* 70% width for image */
    position: relative;
}

.shop-slide {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    opacity: 0;
    transition: opacity 0.6s ease-in-out;
}

.shop-slide.active { opacity: 1; }

.brand-logo-overlay {
    position: absolute;
    top: 20px;
    left: 20px;
    background: #fff;
    padding: 10px;
    z-index: 5;
}

/* Right Content */
.shop-hero-info {
    flex: 0 0 30%;
    display: flex;
    background-color:#a7856280;
    flex-direction: column;
    justify-content: center;
    padding: 0 50px;
    position: relative;
    /* border-left: 1px solid #eee; */
}

.brand-name {
    font-size: 3rem;
    font-weight: 900;
    margin: 0;
    line-height: 1;
    color: var(--text-main);
    letter-spacing: -1px;
}

.offer-text {
    border-radius: 50px;
    font-size: 1.2 rem;
    color: var(--text-muted);
    margin: 20px 0;
}

.explore-link {
    text-decoration: none;
    color: var(--text-muted);
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Slider Dots */
.shop-dots {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 8px;
}

.dot {
    width: 6px;
    height: 6px;
    background: #a7856280;
    border-radius: 50%;
    cursor: pointer;
}

.dot.active { background: var(--text-main); }

/* Styling for the Timer or Benefit box */
.promo-timer, .promo-benefit {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(4px);
    padding: 6px 16px;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    color: #282c3f;
    border: 1px solid rgba(0,0,0,0.05);
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 8px;
    /* z-index: -9; */
}

/* Make the specific values stand out */
.promo-timer span {
    color: #ff0000; /* High visibility for the countdown */
    font-family: monospace;
    font-size: 1rem;
}

/* Responsive adjustment */
@media (max-width: 768px) {
    .promo-bar {
        flex-direction: column;
        gap: 8px;
        text-align: center;
       
    }
}


.trust-bar {
 background: linear-gradient(90deg, #ffd864, #ffffff);
    padding: 30px 0;
    border-top: 1px solid #eeeeee;
    border-bottom: 1px solid #eeeeee;
    font-family: 'Segoe UI', Roboto, Arial, sans-serif;
}

.trust-bar-container {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    gap: 20px;
}

.trust-bar-item {
    display: flex;
    align-items: center;
    gap: 15px;
    flex: 1;
    min-width: 200px;
}

.trust-bar-icon i {
    font-size: 2.5rem;
    color: #090707; /* Muted gray for the icons */
    display: block;
}

.trust-bar-text {
    display: flex;
    flex-direction: column;
}

.trust-bar-title {
    font-size: 1rem;
    font-weight: 700;
    color: #333a40; /* Dark slate for titles */
    margin: 0;
    line-height: 1.2;
}

.trust-bar-subtitle {
    font-size: 0.85rem;
    color: #707070; /* Medium gray for subtitles */
    margin: 4px 0 0 0;
    line-height: 1.2;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .trust-bar-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        padding: 0 20px;
    }
}

@media (max-width: 576px) {
    .trust-bar-container {
        grid-template-columns: 1fr;
    }
    .promo-timer{
         z-index: -9;
    }
}   

.explore-link {
   padding:10px;
    font-size: 0.8rem;
    color: #7e818c;
    text-decoration: none;
    transition: 0.3s;
}

.explore-link:hover {
    color: #000;
    text-decoration: underline;
}

.btn-outline-shop {
    display: inline-block;
    padding: 12px 18px;
    margin: 20px 0;
    background-color: #282c3f;
    color: #d4c2b0; /* Matches your brand-name color */
    border: 1px solid #282c3f;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    cursor: pointer;
}
.btn-outline-shop-1 {
    display: inline-block;
    padding: 12px 28px;
    margin: 20px 0;
    background-color: transparent;
    color: #282c3f; /* Matches your brand-name color */
    border: 1px solid #282c3f;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    cursor: pointer;
}


.btn-outline-shop:hover {
transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
.btn-outline-shop-1:hover {
transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Adjusting the spacing for the inner container */
.info-inner {
    display: flex;
    flex-direction: column;
    align-items: flex-start; /* Aligns button to the left */
}

.explore-link {
    margin-top: 10px;
    font-size: 0.8rem;
    color: #7e818c;
    text-decoration: none;
    transition: 0.3s;
}

.explore-link:hover {
    color: #000;
    text-decoration: underline;
}

.buttons{
    display:flex;
    gap:10px;
}

/* Container & Header */
.mg-section { padding: 80px 0; background-color: #fcfcfc; }
.mg-container { max-width: 1400px; margin: 0 auto; padding: 0 20px; }
.mg-header { text-align: center; margin-bottom: 50px; }
.mg-title { font-size: 2.5rem; font-weight: 700; color: #222; margin-bottom: 10px; }
.mg-subtitle { color: #777; font-size: 1.1rem; }
.gold { color: #d4af37; }

/* The Grid System - 5 Columns */
.mg-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 25px;
}

/* Card Styling */
.mg-card {
    background: #fff;
    border-radius: 15px;
    overflow: hidden;
    transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.3s ease;
    border: 1px solid #eee;
}

.mg-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    border-color: #d4af37;
}

/* Media/Image Handling */
.mg-media-wrapper {
    position: relative;
    aspect-ratio: 1 / 1;
    overflow: hidden;
    background: #f8f8f8;
}

.mg-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
}

.mg-card:hover .mg-image { transform: scale(1.1); }

/* Interaction Overlay */
.mg-overlay {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.mg-card:hover .mg-overlay { opacity: 1; }

.mg-button-group {
    display: flex;
    gap: 12px;
    transform: translateY(20px);
    transition: transform 0.4s ease;
}

.mg-card:hover .mg-button-group { transform: translateY(0); }

.mg-icon-btn {
    width: 45px; height: 45px;
    border-radius: 50%;
    border: none;
    background: #fff;
    color: #333;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    transition: background 0.3s, color 0.3s;
}

.mg-icon-btn:hover { background: #d4af37; color: #fff; }

/* Status Badges */
.mg-status-badge {
    position: absolute;
    top: 12px; left: 12px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    z-index: 5;
    text-transform: uppercase;
}
.mg-badge-high { background: #e8f5e9; color: #2e7d32; }
.mg-badge-medium { background: #fff3e0; color: #ef6c00; }
.mg-badge-low { background: #ffebee; color: #c62828; }

/* Content Details */
.mg-details { padding: 20px; text-align: center; }
.mg-category { font-size: 0.8rem; color: #999; text-transform: uppercase; letter-spacing: 1px; }
.mg-item-name { font-size: 1.05rem; margin: 10px 0; font-weight: 600; }
.mg-item-name a { color: #333; text-decoration: none; transition: color 0.2s; }
.mg-item-name a:hover { color: #d4af37; }
.mg-price-tag { font-size: 1.2rem; font-weight: 800; color: #222; }

/* Footer */
.mg-footer { margin-top: 50px; text-align: center; }
.mg-view-all-btn {
    padding: 14px 40px;
    background: #222;
    color: #fff;
    text-decoration: none;
    border-radius: 30px;
    font-weight: 600;
    transition: background 0.3s;
}
.mg-view-all-btn:hover { background: #d4af37; }

/* --- RESPONSIVE BREAKPOINTS --- */

@media (max-width: 1200px) {
    .mg-grid { grid-template-columns: repeat(4, 1fr); }
}

@media (max-width: 992px) {
    .mg-grid { grid-template-columns: repeat(3, 1fr); }
}

@media (max-width: 768px) {
    .mg-grid { grid-template-columns: repeat(2, 1fr); gap: 15px; }
    .mg-title { font-size: 2rem; }
}

@media (max-width: 480px) {
    .mg-grid { grid-template-columns: 1fr; }
}

@media (max-width: 992px) {
    .shop-hero-wrapper {
        flex-direction: column;
    }

    .shop-hero-visual {
        height: 300px;
        width: 100%;
    }

    .shop-hero-info {
        padding: 8px 20px;
        text-align: center;
    }

    .brand-name {
        font-size: 2rem;
        line-height: 1.2;
    }

    .buttons {
        justify-content: center;
        /* flex-wrap: wrap; */
        gap: 10px;
    }
}
@media (max-width: 768px) {
      .shop-hero-info {
        padding: 20px;
        text-align: center;
    }
    .shop-hero-visual {
        height: 220px;
    }

    .brand-name {
        font-size: 1.8rem;

    }
        .buttons {
        justify-content: center;
        gap: 10px;
    }

    .btn-outline-shop,
    .btn-outline-shop-1 {
          padding: 8px 14px;   /* smaller buttons */
        font-size: 14px;
    }

    .shop-dots {
        margin-top: 15px;
    }
}
@media (max-width: 480px) {
    .shop-hero-visual {
        height: 180px;
    }

    .brand-name {
        font-size: 1.3rem;
    }

    .buttons {
        flex-direction: row;
        align-items: center;
    }

    .btn-outline-shop,
    .btn-outline-shop-1 {
        width: 100%;
        max-width: 220px;
    }
}
    </style>
</head>
<script>
    function startCountdown() {
    // Set the date we're counting down to (Example: 24 hours from now)
    const targetDate = new Date();
    targetDate.setHours(targetDate.getHours() + 24); 

    const timerElement = document.getElementById('timer');

    const updateTimer = () => {
        const now = new Date().getTime();
        const distance = targetDate - now;

        // Time calculations for hours, minutes and seconds
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Format with leading zeros (02h : 05m : 09s)
        const hDisplay = hours.toString().padStart(2, '0');
        const mDisplay = minutes.toString().padStart(2, '0');
        const sDisplay = seconds.toString().padStart(2, '0');

        timerElement.innerHTML = `${hDisplay}h : ${mDisplay}m : ${sDisplay}s`;

        // If the count down is finished
        if (distance < 0) {
            clearInterval(interval);
            timerElement.innerHTML = "OFFER EXPIRED";
        }
    };

    // Run once immediately, then every second
    updateTimer();
    const interval = setInterval(updateTimer, 1000);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', startCountdown);
</script>
<body>
    <!-- <div id="preloader">
    <div class="loader-content">
        <img src="assets/image/gd-store-logo2.png" alt="Loading..." class="loader-logo">
        <div class="loader-line"></div>
    </div>
</div> -->
    <?php include __DIR__ . '/components/navbar.php'; ?>
<div class="promo-bar">
    <div class="promo-text">MID-SUMMER SALE: <strong>UP TO 10% OFF</strong></div>
    <div class="promo-timer">
        ENDS IN: <span id="timer">02h : 45m : 12s</span>
    </div>
</div>

<section class="shop-hero" id="shopHero">
    <div class="shop-hero-wrapper">
        <div class="shop-hero-visual">
          <div class="shop-slide active" style="background-image: url('https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=1200&q=80');"></div>
            <div class="shop-slide" style="background-image: url('https://images.unsplash.com/photo-1505693314120-0d443867891c?auto=format&fit=crop&w=1200&q=80');"></div>
            <div class="shop-slide" style="background-image: url('https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=1200&q=80');"></div>
            
            <!-- <div class="brand-logo-overlay">
                <img src="your-logo.png" alt="Logo">
            </div> -->
        </div>

        <div class="shop-hero-info">
            <div class="info-inner">
    <h2 class="brand-name">TECH & <br>LIVING.</h2>
  <div class="buttons">
      <a href="/products" class="btn-outline-shop">Shop Electronics</a>

     <a href="/products" class="btn-outline-shop-1">View Furniture</a>
  </div>
    <!-- <a href="#" class="explore-link">+ Explore Collection</a> -->
</div>
            <div class="shop-dots">
                <span class="dot active" data-index="0"></span>
                <span class="dot" data-index="1"></span>
                <span class="dot" data-index="2"></span>
            </div>
            
         
        </div>
    </div>
</section>
    <!-- Benefits Section -->
   <section class="trust-bar">
    <div class="trust-bar-container">
        <div class="trust-bar-item">
            <div class="trust-bar-icon">
                <i class="bi bi-headset"></i>
            </div>
            <div class="trust-bar-text">
                <h3 class="trust-bar-title">Customer Support</h3>
                <p class="trust-bar-subtitle">24/7 Dedicated Assistance*</p>
            </div>
        </div>

        <div class="trust-bar-item">
            <div class="trust-bar-icon">
                <i class="bi bi-star-fill"></i>
            </div>
            <div class="trust-bar-text">
                <h3 class="trust-bar-title">Best Seller</h3>
                <p class="trust-bar-subtitle">Top Rated by Thousands*</p>
            </div>
        </div>

        <div class="trust-bar-item">
            <div class="trust-bar-icon">
                <i class="bi bi-award"></i>
            </div>
            <div class="trust-bar-text">
                <h3 class="trust-bar-title">Premium Quality</h3>
                <p class="trust-bar-subtitle">Certified Standard Materials*</p>
            </div>
        </div>

        <div class="trust-bar-item">
            <div class="trust-bar-icon">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="trust-bar-text">
                <h3 class="trust-bar-title">Safe & Secure Checkout</h3>
                <p class="trust-bar-subtitle">100% Encrypted Payments*</p>
            </div>
        </div>
    </div>
</section>
    

    <!-- Shop by Category Section -->
    <section class="shop-category-section">
        <div class="shop-category-title"><span class="gold">Explore Collections</span></div>
        <div class="shop-category-subtitle">Find your favorites by category.</div>
        <div class="shop-category-carousel-wrapper">
            <div class="shop-category-carousel" id="catCarousel">
            <?php foreach ($categories as $cat):
                $img = $cat['image'] ? 'uploads/categories/' . htmlspecialchars($cat['image']) : 'https://via.placeholder.com/120x120?text=No+Image';
            ?>
                <div class="shop-category-card">
                    <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" class="shop-category-img" />
                    <div class="shop-category-name">
                    <a href="products/?category=<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></a>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </section>
    <!-- Featured Products Section -->
 <section class="mg-section" id="products">
    <div class="mg-container">
        <div class="mg-header">
            <h2 class="mg-title">Featured <span class="gold">Products</span></h2>
            <p class="mg-subtitle">Discover our handpicked collection</p>
        </div>

        <div class="mg-grid">
            <?php foreach ($products as $prod): 
                // Image handling
                $imgs = $productImages[$prod['product_id']] ?? [];
                $img = (!empty($imgs)) ? 'uploads/products/' . htmlspecialchars(basename($imgs[0])) : 'https://via.placeholder.com/300x300?text=No+Image';
                
                // Stock Logic based on your variables
                $statusClass = ($prod['stock'] <= 5) ? 'mg-badge-low' : (($prod['stock'] <= 15) ? 'mg-badge-medium' : 'mg-badge-high');
                $statusText = ($prod['stock'] <= 5) ? 'Low Stock' : (($prod['stock'] <= 15) ? 'Limited' : 'In Stock');
            ?>
                <div class="mg-card">
                    <div class="mg-media-wrapper">
                        <span class="mg-status-badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                        
                        <img src="<?php echo $img; ?>" class="mg-image" alt="<?php echo htmlspecialchars($prod['name']); ?>">
                        
                        <div class="mg-overlay">
                            <div class="mg-button-group">
                                <button class="mg-icon-btn" title="Add to Cart"><i class="bi bi-cart-plus"></i></button>
                                <button class="mg-icon-btn" title="Quick View" onclick="quickView(<?php echo $prod['product_id']; ?>)"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="mg-details">
                        <span class="mg-category"><?php echo htmlspecialchars($prod['category_name'] ?? 'General'); ?></span>
                        <h3 class="mg-item-name">
                            <a href="products/details.php?id=<?= $prod['product_id'] ?>"><?php echo htmlspecialchars($prod['name']); ?></a>
                        </h3>
                        <div class="mg-price-tag">₹<?php echo number_format($prod['price'], 2); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($products)): ?>
            <div class="mg-empty">No products found in this collection.</div>
        <?php endif; ?>

        <div class="mg-footer">
            <a href="products/" class="mg-view-all-btn">View All Products</a>
        </div>
    </div>
</section>
    <!-- Newsletter Signup Section -->
    <!-- Removed newsletter section from here -->

    <script>
       
    let fullimgCarouselIndex = 0;
    const fullimgSlides = document.querySelectorAll('.fullimg-hero-slide');
    const fullimgCatEl = document.getElementById('fullimgHeroCategory');
    const fullimgTagEl = document.getElementById('fullimgHeroTagline');
    const fullimgBtnEl = document.getElementById('fullimgHeroBtn');
    const fullimgDots = document.querySelectorAll('.fullimg-hero-dot');
    function updateFullimgCarousel(idx) {
        fullimgSlides.forEach((slide, i) => slide.classList.toggle('active', i === idx));
        const d = fullimgCarouselData[idx];
        fullimgCatEl.innerHTML = `<span class='gold'>${d.category}</span>`;
        fullimgTagEl.textContent = d.tagline;
        fullimgBtnEl.innerHTML = d.btn + ' <i class="bi bi-arrow-right" style="margin-left:8px;"></i>';
        fullimgBtnEl.href = d.link;
        fullimgDots.forEach((dot, i) => dot.classList.toggle('active', i === idx));
    }
    fullimgDots.forEach((dot, i) => {
        dot.addEventListener('click', () => {
            fullimgCarouselIndex = i;
            updateFullimgCarousel(fullimgCarouselIndex);
        });
    });
    setInterval(() => {
        fullimgCarouselIndex = (fullimgCarouselIndex + 1) % fullimgCarouselData.length;
        updateFullimgCarousel(fullimgCarouselIndex);
    }, 5000);

    // Shop by Category Carousel
    const catCarousel = document.getElementById('catCarousel');
    if (catCarousel) {
        // Clone all cards for infinite effect
        const originalCards = Array.from(catCarousel.children);
        originalCards.forEach(card => catCarousel.appendChild(card.cloneNode(true)));
        
        // Calculate original width with proper gap
        const getGap = () => {
            const computedStyle = window.getComputedStyle(catCarousel);
            return parseInt(computedStyle.gap) || 32;
        };
        
        const originalWidth = originalCards.reduce((acc, card) => acc + card.offsetWidth + getGap(), 0);
        let autoScrollInterval;
        
        function startAutoScroll() {
            // Only auto-scroll on larger screens
            if (window.innerWidth <= 768) return;
            
            autoScrollInterval = setInterval(() => {
                if (!catCarousel) return;
                // If at end of original set, jump to equivalent position in clones
                if (catCarousel.scrollLeft >= originalWidth) {
                    catCarousel.scrollLeft = catCarousel.scrollLeft - originalWidth;
                }
                catCarousel.scrollBy({ left: 2, behavior: 'smooth' });
            }, 20); // Adjust speed here
        }
        
        function stopAutoScroll() { 
            if (autoScrollInterval) {
                clearInterval(autoScrollInterval);
                autoScrollInterval = null;
            }
        }
        
        catCarousel.addEventListener('mouseenter', stopAutoScroll);
        catCarousel.addEventListener('mouseleave', startAutoScroll);
        
        // Handle window resize
        window.addEventListener('resize', () => {
            stopAutoScroll();
            if (window.innerWidth > 768) {
                startAutoScroll();
            }
        });
        
        // Start auto-scroll initially
        startAutoScroll();
    }

    window.addEventListener('load', function() {
    const preloader = document.getElementById('preloader');
    // Small delay to ensure smooth transition
    setTimeout(() => {
        preloader.classList.add('fade-out');
    }, 900); 
});
const slides = document.querySelectorAll('.shop-slide');
const dots = document.querySelectorAll('.dot');
let currentIdx = 0;

function updateSlider(index) {
    slides.forEach(s => s.classList.remove('active'));
    dots.forEach(d => d.classList.remove('active'));
    
    slides[index].classList.add('active');
    dots[index].classList.add('active');
    currentIdx = index;
}

dots.forEach((dot, i) => {
    dot.addEventListener('click', () => updateSlider(i));
});

// Auto-rotate every 5 seconds
setInterval(() => {
    let next = (currentIdx + 1) % slides.length;
    updateSlider(next);
}, 5000);
    </script>
      <?php include __DIR__ . '/components/newsletter.php'; ?>
    <?php include __DIR__ . '/components/footer.php'; ?>
</body>
</html> 