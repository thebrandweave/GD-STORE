<?php
session_start();
require_once __DIR__ . '/../config/config.php';

// Database connection
$db = new Database();
$conn = $db->getConnection();

// Fetch all categories
$categories = $conn->query('SELECT * FROM categories ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Categories - GoldenDream Shop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: #f7f7fa;
            --secondary: #232526;
            --accent: #ffd600;
            --accent-dark: #ffb300;
            --card-bg: #fff;
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
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 16px;
        }
        .page-header {
            background: #fff;
            padding: 40px 0;
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 1px solid #f0f0f0;
        }
        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 16px;
        }
        
        /* Breadcrumb Navigation */
        .breadcrumb {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.9rem;
            color: #666;
        }
        .breadcrumb a {
            color: var(--accent-dark);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }
        .breadcrumb a:hover {
            color: var(--accent);
        }
        .breadcrumb .separator {
            color: #ccc;
            font-weight: 400;
        }
        .breadcrumb .current {
            color: #666;
            font-weight: 500;
        }
        
        /* Page Subtitle */
        .page-subtitle {
            text-align: center;
            margin-bottom: 40px;
            padding: 0 20px;
        }
        .page-subtitle p {
            font-size: 1.1rem;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.5;
        }
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 32px;
            margin-bottom: 40px;
        }
        .category-card {
            background: #fff;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 32px 18px 24px 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: box-shadow 0.2s, transform 0.2s;
            border: 1.5px solid rgba(255,255,255,0.08);
        }
        .category-card:hover {
            box-shadow: 0 12px 40px 0 rgba(255,214,0,0.13), 0 4px 24px rgba(0,0,0,0.08);
            transform: translateY(-6px) scale(1.03);
        }
        .category-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 16px;
            margin-bottom: 16px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(255,214,0,0.10);
        }
        .category-name {
            font-size: 1.18rem;
            font-weight: 700;
            color: #232526;
            margin-bottom: 12px;
            text-align: center;
        }
        .category-btn {
            background: linear-gradient(90deg, #ffd600 0%, #ffb300 100%);
            color: #232526;
            font-weight: 700;
            border: none;
            border-radius: 999px;
            padding: 10px 28px;
            font-size: 1rem;
            box-shadow: 0 2px 8px rgba(255,214,0,0.10);
            cursor: pointer;
            transition: background 0.18s, color 0.18s;
            text-decoration: none;
        }
        .category-btn:hover {
            background: #ffb300;
            color: #fff;
        }
        @media (max-width: 600px) {
            .page-title { font-size: 1.2rem; }
            .categories-grid { gap: 18px; }
            .category-card { padding: 12px 4px 8px 4px; }
            .category-img { width: 80px; height: 80px; }
        }
        
    </style>
</head>
<body>
    <?php 
    $_GET['from_products'] = true;
    include __DIR__ . '/../components/navbar.php'; 
    ?>
    <div class="page-header">
        <div class="container">
            <h1 class="page-title">Categories</h1>
            <div class="breadcrumb">
                <a href="../index.php">Home</a>
                <span class="separator">></span>
                <span class="current">Categories</span>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="categories-grid">
            <?php foreach ($categories as $cat):
                $img = $cat['image'] ? '../uploads/categories/' . htmlspecialchars($cat['image']) : 'https://via.placeholder.com/120x120?text=No+Image';
            ?>
                <div class="category-card">
                    <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" class="category-img" />
                    <div class="category-name"><?php echo htmlspecialchars($cat['name']); ?></div>
                    <a href="../products/index.php?category=<?php echo $cat['category_id']; ?>" class="category-btn">View Products</a>
                </div>
            <?php endforeach; ?>
            <?php if (empty($categories)): ?>
                <div style="text-align:center; color:#aaa; padding:24px; grid-column: 1 / -1;">No categories found</div>
            <?php endif; ?>
        </div>
    </div>
    <?php 
    $_GET['from_products'] = true;
    include '../components/footer.php'; 
    ?>
</body>
</html> 