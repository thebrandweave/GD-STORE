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
            --primary: #f5f7fb;
            --secondary: #1e1e2f;
            --accent: #ffd600;
            --card-bg: #ffffff;
            --radius: 24px;
            --shadow: 0 10px 40px rgba(0,0,0,0.04);
        }

        body {
            background-color: #f0f2f5;
            font-family: 'Montserrat', sans-serif;
            color: var(--secondary);
            margin: 0;
        }

        .container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .page-header {
            margin-bottom: 40px;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: -1px;
        }

        /* BENTO GRID SYSTEM */
        .bento-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-auto-rows: 240px; /* Base height for rows */
            gap: 20px;
        }

        /* CARD STYLING */
        .bento-item {
            position: relative;
            background: var(--card-bg);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            border: 1px solid rgba(0,0,0,0.05);
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 25px;
        }

        .bento-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        }

        /* Image Handling */
        .bento-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
            transition: transform 0.6s ease;
        }

        .bento-item:hover .bento-img {
            transform: scale(1.08);
        }

        /* Overlay for legibility */
        .bento-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 60%);
            z-index: 2;
        }

        /* Content info */
        .bento-content {
            position: relative;
            z-index: 3;
            color: white;
        }

        .bento-name {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .bento-badge {
            display: inline-block;
            background: var(--accent);
            color: #000;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* VARIATIONS (The "Bento" Logic) */
        /* Make every 1st and 5th item large */
        .bento-item:nth-child(6n+1) {
            grid-column: span 2;
            grid-row: span 2;
        }

        /* Make every 4th item wide */
        .bento-item:nth-child(6n+4) {
            grid-column: span 2;
        }

        /* RESPONSIVE */
        @media (max-width: 1024px) {
            .bento-grid { grid-template-columns: repeat(2, 1fr); }
            .bento-item:nth-child(n) { grid-column: span 1; grid-row: span 1; } /* Reset for mobile */
        }

        @media (max-width: 600px) {
            .bento-grid { grid-template-columns: 1fr; grid-auto-rows: 200px; }
            .page-title { font-size: 1.8rem; }
        }
    </style>
</head>
<body>

<?php 
$_GET['from_products'] = true;
include __DIR__ . '/../components/navbar.php'; 
?>

<div class="container">
    <header class="page-header">
        <h1 class="page-title">Shop by Category</h1>
        <p style="color: #666;">Discover our curated collections</p>
    </header>

    <div class="bento-grid">
        <?php foreach ($categories as $index => $cat): 
            $img = $cat['image'] 
                ? '../uploads/categories/' . htmlspecialchars($cat['image']) 
                : 'https://via.placeholder.com/600x600?text=' . urlencode($cat['name']);
        ?>
            <a href="../products/index.php?category=<?php echo $cat['category_id']; ?>" 
               class="bento-item" 
               style="animation-delay: <?php echo $index * 0.1; ?>s">
                
                <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" class="bento-img">
                <div class="bento-overlay"></div>
                
                <div class="bento-content">
                    <span class="bento-badge">Collection</span>
                    <div class="bento-name"><?php echo htmlspecialchars($cat['name']); ?></div>
                    <small>Explore Items <i class="bi bi-arrow-right-short"></i></small>
                </div>
            </a>
        <?php endforeach; ?>

        <?php if (empty($categories)): ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 50px;">
                <i class="bi bi-box-seam" style="font-size: 3rem; color: #ccc;"></i>
                <p>No categories found.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../components/footer.php'; ?>

</body>
</html>