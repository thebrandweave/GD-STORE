<?php
session_start();
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - GoldenDream Shop</title>
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
            --card-bg: #fff;
            --radius: 22px;
            --shadow: 0 8px 32px 0 rgba(0,0,0,0.08);
            --font-main: 'Montserrat', Arial, sans-serif;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body, html {
            background: var(--primary);
            color: var(--secondary);
            font-family: var(--font-main);
            min-height: 100vh;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Enhanced Page Header */
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
        
        /* Contact Content */
        .contact-content {
            margin-bottom: 80px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            position: relative;
        }
        
        /* Split Screen Diagonal Design */
        .design1 {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            min-height: 600px;
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }
        
        /* Contact Info Section (Left) */
        .contact-info {
            background: linear-gradient(135deg, var(--accent-dark) 0%, var(--accent) 100%);
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            clip-path: polygon(0 0, 100% 0, 85% 100%, 0% 100%);
        }
        
        .contact-info h2 {
            color: #fff;
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 40px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 30px;
            color: #fff;
        }
        
        .info-item .icon {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
            flex-shrink: 0;
            color: #fff;
        }
        
        .info-item h4 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: #fff;
        }
        
        .info-item p {
            font-size: 1rem;
            line-height: 1.6;
            color: rgba(255,255,255,0.9);
            font-weight: 500;
        }
        
        /* Contact Form Section (Right) */
        .contact-form {
            background: #fff;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            clip-path: polygon(15% 0, 100% 0, 100% 100%, 0% 100%);
            margin-left: -50px;
            border-radius: 20px 0 0 20px;
            box-shadow: -10px 0 30px rgba(0,0,0,0.1);
        }
        
        .contact-form h3 {
            color: #333;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .contact-form .form-group {
            margin-bottom: 25px;
        }
        
        .contact-form input,
        .contact-form textarea {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e8e9ea;
            border-radius: 12px;
            font-size: 1rem;
            font-family: var(--font-main);
            background: #fff;
            color: #333;
            transition: all 0.3s ease;
            resize: none;
        }
        
        .contact-form input:focus,
        .contact-form textarea:focus {
            outline: none;
            border-color: var(--accent-dark);
            box-shadow: 0 0 0 3px rgba(255, 179, 0, 0.1);
        }
        
        .contact-form textarea {
            min-height: 120px;
            line-height: 1.6;
        }
        
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            color: var(--secondary);
            border: none;
            border-radius: 999px;
            padding: 16px 32px;
            font-weight: 800;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: var(--font-main);
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 8px 25px rgba(255, 214, 0, 0.3);
        }
        
        .btn-submit:hover {
            background: var(--accent-dark);
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(255, 179, 0, 0.4);
        }
        
        /* Responsive Design */
        @media (max-width: 900px) {
            .design1 {
                grid-template-columns: 1fr;
                min-height: auto;
                clip-path: none;
            }
            
            .contact-info {
                clip-path: none;
                padding: 40px 30px;
            }
            
            .contact-form {
                clip-path: none;
                margin-left: 0;
                border-radius: 0 0 20px 20px;
                padding: 40px 30px;
            }
            
            .contact-info h2 {
                font-size: 2rem;
                text-align: center;
            }
            
            .info-item {
                margin-bottom: 25px;
            }
        }
        
        @media (max-width: 600px) {
            .contact-content {
                margin: 0 20px 60px 20px;
            }
            
            .contact-info {
                padding: 30px 20px;
            }
            
            .contact-form {
                padding: 30px 20px;
            }
            
            .contact-info h2 {
                font-size: 1.8rem;
                margin-bottom: 30px;
            }
            
            .info-item .icon {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
            
            .info-item h4 {
                font-size: 1.1rem;
            }
            
            .contact-form h3 {
                font-size: 1.6rem;
            }
            
            .btn-submit {
                padding: 14px 28px;
                font-size: 1rem;
            }
        }
        

        .contact-main-container {
            display: flex;
            gap: 60px;
            max-width: 1200px;
            margin: 60px auto 80px auto;
            padding: 0 20px;
        }
        .contact-form-section {
            flex: 2;
        }
        .contact-form-section h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .contact-form-section p {
            color: #888;
            font-size: 1.2rem;
            margin-bottom: 32px;
        }
        .contact-form-modern {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .contact-form-modern input,
        .contact-form-modern textarea {
            width: 100%;
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 16px;
            font-size: 1.1rem;
            background: #fafafa;
            margin-bottom: 0;
            transition: border 0.2s;
        }
        .contact-form-modern input:focus,
        .contact-form-modern textarea:focus {
            border: 1.5px solid #ffd600;
            outline: none;
            background: #fff;
        }
        .contact-form-modern .input-half {
            width: 48.5%;
        }
        .contact-form-modern textarea {
            min-height: 160px;
            resize: vertical;
        }
        .contact-form-modern .btn-send {
            background: #23211a;
            color: #fff;
            font-size: 1.2rem;
            font-weight: 600;
            border: none;
            border-radius: 32px;
            padding: 18px 48px;
            margin-top: 24px;
            cursor: pointer;
            transition: background 0.2s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .contact-form-modern .btn-send:hover {
            background: #ffd600;
            color: #23211a;
        }
        .contact-info-section {
            flex: 1;
            padding-top: 32px;
        }
        .contact-info-section h3 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 32px;
        }
        .contact-info-list {
            display: flex;
            flex-direction: column;
            gap: 28px;
        }
        .contact-info-list label {
            font-weight: 600;
            color: #23211a;
            font-size: 1.1rem;
            margin-bottom: 2px;
            display: block;
        }
        .contact-info-list span {
            color: #444;
            font-size: 1.08rem;
        }
        .contact-info-list .open-time {
            margin-top: 8px;
        }
        @media (max-width: 900px) {
            .contact-main-container {
                flex-direction: column;
                gap: 40px;
                margin: 40px 0 60px 0;
                padding: 0 10px;
            }
            .contact-form-section h2 {
                font-size: 2rem;
            }
            .contact-info-section h3 {
                font-size: 1.5rem;
            }
        }
        @media (max-width: 600px) {
            .contact-form-modern .input-half {
                width: 100%;
            }
            .contact-form-section h2 {
                font-size: 1.4rem;
            }
            .contact-info-section h3 {
                font-size: 1.1rem;
            }
        }
        /* THEME COLOR OVERRIDES FOR CONTACT PAGE */
        .contact-form-section h2,
        .contact-info-section h3 {
            color: var(--accent);
        }
        .contact-info-list label {
            color: var(--accent-dark);
        }
        .contact-form-modern input,
        .contact-form-modern textarea {
            color: var(--secondary);
        }
        .contact-form-modern input:focus,
        .contact-form-modern textarea:focus {
            border: 1.5px solid var(--accent);
        }
        .contact-form-section p,
        .contact-info-list span {
            color: var(--secondary);
        }
        .contact-form-modern .btn-send {
            background: var(--accent);
            color: var(--secondary);
        }
        .contact-form-modern .btn-send:hover {
            background: var(--accent-dark);
            color: #fff;
        }
        /* Ensure footer left column info items match product page */
        .company-info {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 16px;
        }
        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #666;
            font-size: 0.9rem;
            margin: 0;
        }
        .info-item i {
            color: #ffd600;
            font-size: 1rem;
            width: 16px;
        }
        .info-item span {
            font-weight: 500;
        }
    </style>
</head>
<body>
    <?php 
    $_GET['from_products'] = true;
    include '../components/navbar.php'; 
    ?>
    
    <div class="page-header">
        <div class="container">
            <h1 class="page-title">Contact</h1>
            <div class="breadcrumb">
                <a href="../index.php">Home</a>
                <span class="separator">></span>
                <span class="current">Contact</span>
            </div>
        </div>
    </div>

    <div class="contact-main-container">
        <div class="contact-form-section">
            <h2>Get In Touch</h2>
            <p>Use the form below to get in touch with our team</p>
            <form class="contact-form-modern" method="post" action="#">
                <input type="text" name="name" class="input-half" placeholder="Your Name*" required>
                <input type="email" name="email" class="input-half" placeholder="Your Email*" required>
                <textarea name="message" placeholder="Your Message*" required></textarea>
                <button type="submit" class="btn-send">Send Message</button>
            </form>
        </div>
        <div class="contact-info-section">
            <h3>Information</h3>
            <div class="contact-info-list">
                <div>
                    <label>Phone:</label>
                    <span>+91 8197458962</span>
                </div>
                <div>
                    <label>Email:</label>
                    <span>goldendream175@gmail.com</span>
                </div>
                <div>
                    <label>Address:</label>
                    <span>2-108/C-7, Ground Floor, Sri Mantame Complex,<br>Near Soorya Infotech Park, Kurnadu Post,<br>Mudipu Road, Bantwal- 574153</span>
                </div>
                <div class="open-time">
                    <label>Open Time:</label>
                    <span>Mon – Sat: 9:00 AM – 6:00 PM<br>Sunday: Closed</span>
                </div>
            </div>
        </div>
    </div>
    
    <?php 
    $_GET['from_products'] = true;
    include __DIR__ . '/../components/footer.php'; 
    ?>
</body>
</html> 