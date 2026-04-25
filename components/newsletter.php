<?php
// Dynamic base path
$script_name = $_SERVER['SCRIPT_NAME'];
$path_parts = explode('/', trim($script_name, '/'));
$project_folder = $path_parts[0] ?? '';
$base_path = !empty($project_folder) ? '/' . $project_folder . '/' : '/';
?>

<section class="newsletter-section">
    <div class="newsletter-overlay"></div>

    <div class="newsletter-container">
        <div class="newsletter-content">
            <h2 class="newsletter-title">Sign up for updates</h2>
            <p class="newsletter-subtitle">
                Sign up for early sale access, new arrivals & promotions
            </p>

            <form class="newsletter-form">
                <input type="email" placeholder="Enter your e-mail" class="newsletter-input" required>
                <button type="submit" class="newsletter-btn">SUBSCRIBE</button>
            </form>
        </div>
    </div>
</section>

<style>
.newsletter-section {
    background: url('<?php echo $base_path; ?>assets/image/parallex.jpeg') center/cover;
    padding: 80px 20px;
    text-align: center;
    position: relative;
    color: #fff;
}

.newsletter-overlay {
    position: absolute;
    inset: 0;
    /* background: rgba(0,0,0,0.5); */
}

.newsletter-container {
    max-width: 1200px;
    margin: auto;
}

.newsletter-content {
    position: relative;
    z-index: 2;
    max-width: 600px;
    margin: auto;
}

.newsletter-title {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 15px;
}

.newsletter-subtitle {
    margin-bottom: 30px;
    opacity: 0.9;
}

/* FORM */
.newsletter-form {
    display: flex;
    background: #fff;
    border-radius: 50px;
    overflow: hidden;
}

.newsletter-input {
    flex: 1;
    border: none;
    padding: 15px;
    outline: none;
}

.newsletter-btn {
    background: #ffd600;
    border: none;
    padding: 0 25px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.3s;
}

.newsletter-btn:hover {
    background: #ffb300;
}

/* MOBILE */
@media (max-width: 600px) {
    .newsletter-form {
        flex-direction: column;
        border-radius: 20px;
    }

    .newsletter-btn {
        padding: 15px;
    }
}
</style>