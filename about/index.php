<?php
session_start();
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About - GoldenDream Store</title>
      <link rel="icon" type="image/png" href="../assets/image/gdlogo.png">
<link rel="shortcut icon" href="../assets/image/gdlogo.ico">
<link rel="apple-touch-icon" href="../assets/image/gdlogo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
        :root {
            --primary-bg: #fdfdfd;
            --dark: #1a1a1d;
            --accent: #ffd600;
            --accent-soft: rgba(255, 214, 0, 0.1);
            --text-muted: #6c757d;
            --glass: rgba(255, 255, 255, 0.8);
            --border-color: #eee;
        }

        body {
            background-color: var(--primary-bg);
            color: var(--dark);
            font-family: 'Montserrat', sans-serif;
            overflow-x: hidden;
        }

        /* --- Hero Section --- */
        .hero-banner {
            padding: 120px 0 60px;
            background: radial-gradient(circle at top right, var(--accent-soft), transparent);
            text-align: center;
        }

        .hero-banner h1 {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 800;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: -1px;
        }

        .breadcrumb {
            display: flex;
            justify-content: center;
            gap: 10px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .breadcrumb a { color: var(--text-muted); text-decoration: none; }
        .breadcrumb .active { color: var(--accent-dark); }

        /* --- Intro Section --- */
        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            padding: 80px 0;
        }

        .image-stack {
            position: relative;
            padding: 20px;
        }

        .image-stack img {
            width: 100%;
            border-radius: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .image-stack::after {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            width: 100px;
            height: 100px;
            border-left: 8px solid var(--accent);
            border-top: 8px solid var(--accent);
            border-radius: 10px 0 0 0;
        }

        .content-box h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            position: relative;
        }

        .content-box p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #444;
            margin-bottom: 1.5rem;
        }

        /* --- Vision & Mission Cards --- */
        .mission-vision-wrapper {
            background: var(--dark);
            color: white;
            padding: 100px 0;
            border-radius: 60px 60px 0 0;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .mv-card {
            background: rgba(255,255,255,0.05);
            padding: 50px;
            border-radius: 40px;
            border: 1px solid rgba(255,255,255,0.1);
            transition: 0.4s ease;
        }

        .mv-card:hover {
            background: var(--accent);
            color: var(--dark);
            transform: translateY(-10px);
        }

        .mv-card i {
            font-size: 3rem;
            margin-bottom: 20px;
            display: block;
        }

        .mv-card h3 { font-size: 1.8rem; margin-bottom: 15px; }

        /* --- Core Values --- */
        .values-section { padding: 100px 0; text-align: center; }
        
        .values-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            margin-top: 50px;
        }

        .value-item i {
            width: 80px;
            height: 80px;
            line-height: 80px;
            background: var(--accent);
            border-radius: 20px;
            font-size: 2rem;
            margin-bottom: 20px;
            display: inline-block;
        }

        /* --- Modern CTA --- */
        .cta-box {
            background: var(--accent);
            border-radius: 40px;
            padding: 80px 40px;
            text-align: center;
            margin: 80px 0;
            background-image: url('https://www.transparenttextures.com/patterns/cubes.png');
        }

        .cta-btn {
            background: var(--dark);
            color: white;
            padding: 20px 50px;
            border-radius: 100px;
            text-decoration: none;
            font-weight: 800;
            display: inline-block;
            margin-top: 30px;
            transition: 0.3s;
        }

        .cta-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        /* --- Responsive --- */
        @media (max-width: 992px) {
            .about-grid { grid-template-columns: 1fr; }
            .values-grid { grid-template-columns: 1fr; }
            .image-stack { order: 2; }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
    </style>
</head>
<body>

    <?php 
    $_GET['from_products'] = true;
    include __DIR__ . '/../components/navbar.php'; 
    ?>

    <section class="hero-banner">
        <div class="container">
            <h1>Our Story</h1>
            <div class="breadcrumb">
                <a href="../">Home</a>
                <span>/</span>
                <span class="active">About Us</span>
            </div>
        </div>
    </section>

    <div class="container">
        <section class="about-grid">
            <div class="image-stack">
                <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&q=80&w=1000" alt="About GD Store">
            </div>
            <div class="content-box">
                <h2>Who We Are</h2>
                <p>The GD Store is the official online store of <strong>Progee Dee Ventures</strong>, a platform where quality meets convenience. We were born out of a desire to provide qualified clients a premium way to utilize company vouchers for high-end items.</p>
                <p>From fashion and lifestyle to daily essentials, we curate products that provide enduring value. At our core, we believe in remarkable service and outstanding, dependable quality.</p>
            </div>
        </section>
    </div>

    <section class="mission-vision-wrapper">
        <div class="container">
            <div class="cards-container">
                <div class="mv-card">
                    <i class="bi bi-rocket-takeoff"></i>
                    <h3>Our Mission</h3>
                    <p>Centering on trust and transparency, we provide a purposeful shopping experience built on a customer-first philosophy.</p>
                </div>
                <div class="mv-card">
                    <i class="bi bi-eye"></i>
                    <h3>Our Vision</h3>
                    <p>To become the leading benchmark for voucher-based luxury retail, known globally for our dependable quality and remarkable service.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="values-section">
        <div class="container">
            <h2>Our Core Values</h2>
            <div class="values-grid">
                <div class="value-item">
                    <i class="bi bi-shield-check"></i>
                    <h4>Trust</h4>
                    <p>Building lasting relationships through honesty and transparency.</p>
                </div>
                <div class="value-item">
                    <i class="bi bi-gem"></i>
                    <h4>Quality</h4>
                    <p>Only the finest products make it to our storefront.</p>
                </div>
                <div class="value-item">
                    <i class="bi bi-heart"></i>
                    <h4>Service</h4>
                    <p>Exceeding customer expectations at every touchpoint.</p>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <section class="cta-box">
            <h2>Experience the GoldenDream</h2>
            <p>Your premium lifestyle is just a click away. Start exploring our curated collections today.</p>
            <a href="../products/" class="cta-btn">BROWSE COLLECTION</a>
        </section>
    </div>

    <?php 
    $_GET['from_products'] = true;
    include '../components/footer.php'; 
    ?>
</body>
</html>