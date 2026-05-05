<?php
session_start();
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - GoldenDream Shop</title>
      <link rel="icon" type="image/png" href="../assets/image/gdlogo.png">
<link rel="shortcut icon" href="../assets/image/gdlogo.ico">
<link rel="apple-touch-icon" href="../assets/image/gdlogo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary-bg: #f8f9fc;
            --white: #ffffff;
            --dark: #232526;
            --accent: #ffd600;
            --accent-dark: #ffb300;
            --text-gray: #6c757d;
            --card-shadow: 0 20px 40px rgba(0,0,0,0.05);
            --inner-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
        }

        body {
            background-color: var(--primary-bg);
            font-family: 'Montserrat', sans-serif;
            color: var(--dark);
            margin: 0;
        }

        /* --- Minimalist Header --- */
        .contact-header {
            padding: 80px 0 40px;
            text-align: center;
            background: linear-gradient(to bottom, #fff, var(--primary-bg));
        }

        .contact-header h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 10px;
            letter-spacing: -1px;
        }

        .breadcrumb {
            display: flex;
            justify-content: center;
            gap: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-gray);
        }

        .breadcrumb a { color: var(--accent-dark); text-decoration: none; }

        /* --- Main Layout --- */
        .contact-wrapper {
            max-width: 1200px;
            margin: -20px auto 80px;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 40px;
        }

        /* --- Left Side: Info Cards --- */
        .info-sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .contact-card {
            background: var(--white);
            padding: 30px;
            border-radius: 24px;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s ease;
            border: 1px solid rgba(0,0,0,0.02);
        }

        .contact-card:hover {
            transform: translateY(-5px);
        }

        .card-icon {
            width: 50px;
            height: 50px;
            background: var(--accent);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: var(--dark);
        }

        .contact-card h4 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--dark);
        }

        .contact-card p, .contact-card span {
            font-size: 0.95rem;
            color: var(--text-gray);
            line-height: 1.6;
        }

        /* --- Right Side: Form Card --- */
        .form-container {
            background: var(--white);
            padding: 50px;
            border-radius: 32px;
            box-shadow: var(--card-shadow);
        }

        .form-container h2 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .form-container p {
            color: var(--text-gray);
            margin-bottom: 40px;
        }

        .modern-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .input-group.full-width {
            grid-column: span 2;
        }

        .modern-form label {
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--dark);
        }

        .modern-form input, .modern-form textarea {
            padding: 16px 20px;
            border-radius: 16px;
            border: 2px solid #f0f0f0;
            background: #fafafa;
            font-family: inherit;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .modern-form input:focus, .modern-form textarea:focus {
            outline: none;
            border-color: var(--accent);
            background: #fff;
            box-shadow: 0 10px 20px rgba(255, 214, 0, 0.1);
        }

        .modern-form textarea {
            min-height: 150px;
            resize: vertical;
        }

        .submit-btn {
            grid-column: span 2;
            background: var(--dark);
            color: white;
            padding: 20px;
            border-radius: 16px;
            border: none;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .submit-btn:hover {
            background: var(--accent);
            color: var(--dark);
            transform: scale(1.02);
        }

        /* --- Responsive Design --- */
        @media (max-width: 992px) {
            .contact-wrapper { grid-template-columns: 1fr; }
            .contact-header h1 { font-size: 2.2rem; }
        }

        @media (max-width: 600px) {
            .modern-form { grid-template-columns: 1fr; }
            .input-group { grid-column: span 2 !important; }
            .form-container { padding: 30px 20px; }
        }
    </style>
</head>
<body>

    <?php 
    $_GET['from_products'] = true;
    include '../components/navbar.php'; 
    ?>

    <header class="contact-header">
        <div class="container">
            <h1>Let's Connect</h1>
            <div class="breadcrumb">
                <a href="../index.php">Home</a>
                <span>/</span>
                <span class="active">Contact Us</span>
            </div>
        </div>
    </header>

    <main class="contact-wrapper">
        <aside class="info-sidebar">
            <div class="contact-card">
                <div class="card-icon"><i class="bi bi-telephone"></i></div>
                <h4>Call Us</h4>
                <p>+91 8197458962</p>
            </div>

            <div class="contact-card">
                <div class="card-icon"><i class="bi bi-envelope"></i></div>
                <h4>Email Support</h4>
                <p>goldendream175@gmail.com</p>
            </div>

            <div class="contact-card">
                <div class="card-icon"><i class="bi bi-geo-alt"></i></div>
                <h4>Visit Us</h4>
                <span>2-108/C-7, Sri Mantame Complex,<br>Mudipu Road, Bantwal- 574153</span>
            </div>

            <div class="contact-card">
                <div class="card-icon"><i class="bi bi-clock"></i></div>
                <h4>Working Hours</h4>
                <p>Mon – Sat: 9 AM – 6 PM<br>Sunday: Closed</p>
            </div>
        </aside>

        <section class="form-container">
            <h2>Send a Message</h2>
            <p>Have a question or feedback? We'd love to hear from you.</p>

            <form class="modern-form" method="post" action="#">
                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="Enter your name" required>
                </div>
                
                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="example@mail.com" required>
                </div>

                <div class="input-group full-width">
                    <label>Subject</label>
                    <input type="text" name="subject" placeholder="How can we help?">
                </div>

                <div class="input-group full-width">
                    <label>Your Message</label>
                    <textarea name="message" placeholder="Type your message here..." required></textarea>
                </div>

                <button type="submit" class="submit-btn">
                    Send Message <i class="bi bi-send"></i>
                </button>
            </form>
        </section>
    </main>

    <?php 
    $_GET['from_products'] = true;
    include __DIR__ . '/../components/footer.php'; 
    ?>

</body>
</html>