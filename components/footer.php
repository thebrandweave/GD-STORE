<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<?php
// Dynamically determine the base path for the project
$script_name = $_SERVER['SCRIPT_NAME'];
$request_uri = $_SERVER['REQUEST_URI'];

// Extract the project folder name from the script path
$path_parts = explode('/', trim($script_name, '/'));
$project_folder = $path_parts[0] ?? '';

// Build the base path dynamically
if (!empty($project_folder)) {
    $base_path = '/' ;
} else {
    // Fallback for root deployment
    $base_path = '/';
}
?>

  <style>
 body, html {
        font-family: 'Montserrat', sans-serif;
    }
 


    .tile {
      background-color: white;
      border-radius: 6px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      font-weight: bold;
      color: #333;
      transition: all 0.3s ease;
      overflow: hidden;
    }

    /* Make images fully contained inside tiles */
    .tile img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }
    .store-footer-modern {
    background-color: #000000; /* Sleek dark background */
    color: #e0e0e0;
    padding: 60px 0 20px 0;
    font-family: 'Inter', sans-serif;
}

.footer-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.footer-main-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr 1fr 1.2fr;
    gap: 40px;
    padding-bottom: 50px;
}

.brand-logo {
    max-width: 85px;
    filter: brightness(0) invert(1); /* Forces logo to white if it's dark */
    margin-bottom: 20px;
}

.brand-bio {
    font-size: 0.95rem;
    line-height: 1.6;
    color: #a0a0a0;
}

.block-title {
    color: #ffffff;
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 25px;
    position: relative;
}

.block-title::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: -8px;
    width: 30px;
    height: 2px;
    background-color: #ffc929; /* Using your requested yellow accent color */
}

.footer-nav {
    list-style: none;
    padding: 0;
}

.footer-nav li {
    margin-bottom: 12px;
}

.footer-nav a {
    color: #a0a0a0;
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer-nav a:hover {
    color: #ffc929;
}

.store-address {
    font-style: normal;
    color: #a0a0a0;
    line-height: 1.6;
    margin-bottom: 20px;
}

.social-wrapper a {
    color: #ffffff;
    font-size: 1.4rem;
    margin-right: 15px;
    transition: transform 0.3s ease;
    display: inline-block;
}

.social-wrapper a:hover {
    color: #ffc929;
    transform: translateY(-3px);
}

.footer-bottom-bar {
    border-top: 1px solid #333;
    padding-top: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.accent-text {
    color: #ffc929;
    font-weight: 600;
}

.dev-logo-small {
    height: 25px;
    margin-left: 10px;
    vertical-align: middle;
    opacity: 0.7;
}

/* Responsive Design */
@media (max-width: 992px) {
    .footer-main-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 576px) {
    .footer-main-grid {
        grid-template-columns: 1fr;
    }
    .footer-bottom-bar {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
}
  </style>


<footer class="store-footer-modern">
  <div class="footer-container">
    <div class="footer-main-grid">
      
      <div class="footer-block brand-summary">
        <div class="footer-logo-wrap">
          <img src="<?php echo $base_path; ?>assets/image/gdlogo.png" alt="GD Store" class="brand-logo">
        </div>
        <p class="brand-bio">
          Upgrade your lifestyle with the latest in smart electronics and handcrafted furniture. From high-performance appliances to elegant home decor, we bring quality to every corner of your home.
        </p>
        <div class="feature-strip">
          <div class="strip-item"><i class="bi bi-patch-check"></i> 100% Genuine</div>
          <div class="strip-item"><i class="bi bi-truck"></i> Expert Delivery</div>
        </div>
      </div>
      
      <div class="footer-block">
        <h5 class="block-title">Electronics</h5>
        <ul class="footer-nav">
          <li><a href="/products">Smart LED TVs</a></li>
          <li><a href="/products">Air Conditioners</a></li>
          <li><a href="/products">Refrigerators</a></li>
          <li><a href="/products">Washing Machines</a></li>
          <li><a href="/products">Kitchen Appliances</a></li>
        </ul>
      </div>

      <div class="footer-block">
        <h5 class="block-title">Furniture</h5>
        <ul class="footer-nav">
          <li><a href="/products">Sofa Sets</a></li>
          <li><a href="/products">Dining Tables</a></li>
          <li><a href="/products">Bedroom Sets</a></li>
          <li><a href="/products">Office Desks</a></li>
          <li><a href="/products">Home Decor</a></li>
        </ul>
      </div>
      
      <div class="footer-block">
        <h5 class="block-title">Visit Our Store</h5>
        <address class="store-address">
          2-108/C-7, Ground Floor, Sri Mantame Complex,<br>
          Near Soorya Infotech Park, Mudipu Road,<br>
          Bantwal - 574153
        </address>
        <div class="social-wrapper">
          <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
          <a href="#" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
        </div>
      </div>
      
    </div>
    
    <div class="footer-bottom-bar">
      <div class="copyright-text">
        &copy; <?php echo date('Y'); ?> <span class="accent-text">GD Store</span>. All Rights Reserved.
      </div>
      <div class="developer-credit">
        <span>Powered by</span>
        <img src="<?php echo $base_path; ?>assets/image/developer_logo.png" alt="Developer" class="dev-logo-small">
      </div>
    </div>
  </div>
</footer>

<button id="backToTopBtn" title="Back to Top"><i class="bi bi-chevron-up"></i></button>

<style>
.newsletter-section {
    background:  url('assets/image/parallex.jpeg');
    background-size: cover;

    /* background-position: center; */
    /* background-attachment: fixed; */
    padding: 80px 0;
    text-align: center;
    position: relative;
    color: #fff;
    width: 100%;
}

.newsletter-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    width: 100%;
    box-sizing: border-box;
}

.newsletter-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    /* background: rgba(210, 175, 50, 0.3); */
    /* filter:invert(1); */
    z-index: 1;
}

.newsletter-content {
    position: relative;
    z-index: 2;
    max-width: 600px;
    margin: 0 auto;
    animation: fadeInUp 0.8s ease-out;
    width: 100%;
    box-sizing: border-box;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.newsletter-title {
    color: #fff;
    font-size: 2.6rem;
    font-weight: 800;
    margin-bottom: 18px;
    line-height: 1.15;
    letter-spacing: 0.01em;
   
}

.newsletter-title .accent {
    color: #ffd600;
    background: none;
    padding: 0 4px;
    border-radius: 4px;
}

.newsletter-subtitle {
    color: #fff;
    font-size: 1.15rem;
    margin-bottom: 38px;
    font-weight: 400;
    opacity: 0.9;
}

.newsletter-form {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0;
    width: 100%;
    max-width: 540px;
    margin: 0 auto;
    background: #fff;
    border-radius: 50px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    border: 2px solid #ffd600;
    padding: 4px;
    overflow: hidden;
    position: relative;
    box-sizing: border-box;
}

.newsletter-input {
    flex: 1;
    border: none;
    outline: none;
    background: transparent;
    padding: 16px 24px;
    font-size: 1rem;
    border-radius: 46px 0 0 46px;
    color: #232526;
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    transition: all 0.3s ease;
}

.newsletter-input:focus {
    background: rgba(255, 214, 0, 0.05);
}

.newsletter-input::placeholder {
    color: #999;
    font-size: 1rem;
    font-family: 'Montserrat', sans-serif;
    font-weight: 400;
    opacity: 0.8;
}

.newsletter-btn {
    background: linear-gradient(135deg, #ffd600 0%, #ffed4e 100%);
    color: #23211a;
    font-weight: 700;
    border: none;
    border-radius: 0 46px 46px 0;
    padding: 16px 32px;
    font-size: 0.95rem;
    letter-spacing: 0.1em;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: 'Montserrat', sans-serif;
    box-shadow: 0 4px 16px rgba(255, 214, 0, 0.3);
    text-transform: uppercase;
    position: relative;
    overflow: hidden;
}

.newsletter-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.newsletter-btn:hover::before {
    left: 100%;
}

.newsletter-btn:hover {
    background: linear-gradient(135deg, #ffed4e 0%, #ffd600 100%);
    color: #23211a;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 214, 0, 0.4);
}

.newsletter-btn:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(255, 214, 0, 0.3);
}

/* Mobile touch improvements */
@media (max-width: 768px) {
    .newsletter-input {
        min-height: 48px; /* Better touch target */
    }
    
    .newsletter-btn {
        min-height: 48px; /* Better touch target */
        -webkit-tap-highlight-color: transparent;
    }
    
    .newsletter-form {
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box;
    }
}

/* Extra small screens */
@media (max-width: 320px) {
    .newsletter-section {
        padding: 25px 0;
    }
    
    .newsletter-container {
        padding: 0 8px;
    }
    
    .newsletter-title {
        font-size: 1.3rem;
        margin-bottom: 8px;
    }
    
    .newsletter-subtitle {
        font-size: 0.8rem;
        margin-bottom: 15px;
    }
    
    .newsletter-form {
        padding: 3px;
        gap: 3px;
        border-radius: 15px;
    }
    
    .newsletter-input, .newsletter-btn {
        padding: 10px 12px;
        font-size: 0.8rem;
        border-radius: 12px;
        min-height: 44px;
    }
    
    #backToTopBtn {
        bottom: 80px;
        right: 8px;
        width: 36px;
        height: 36px;
        font-size: 1.1rem;
        z-index: 10000;
    }
}

@media (max-width: 700px) {
    .newsletter-title { 
        font-size: 1.8rem; 
        margin-bottom: 15px;
    }
    .newsletter-subtitle {
        font-size: 1rem;
        margin-bottom: 25px;
    }
    .newsletter-form { 
        flex-direction: column !important; 
        border-radius: 20px; 
        max-width: 95%; 
        gap: 8px;
        padding: 8px;
        border: 2px solid #ffd600;
        width: 100%;
        box-sizing: border-box;
    }
    .newsletter-input, .newsletter-btn { 
        border-radius: 16px; 
        width: 100% !important; 
        padding: 16px 20px; 
        font-size: 1rem; 
        border: none;
        box-sizing: border-box;
        min-width: 0;
        max-width: 100%;
    }
    .newsletter-input {
        border: 1px solid #e0e0e0;
        margin-bottom: 0;
        background: #fff;
        flex: none;
    }
    .newsletter-btn { 
        margin-top: 0; 
        background: linear-gradient(135deg, #ffd600 0%, #ffed4e 100%);
        color: #23211a;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(255, 214, 0, 0.3);
        flex: none;
        white-space: nowrap;
    }
}

.shop-footer {
  background: #fff;
  border-top: 1px solid #f0f0f0;
  padding: 60px 0 20px 0;
  font-family: 'Montserrat', sans-serif;
  color: #333;
}

.footer-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.footer-content {
  display: grid;
  grid-template-columns: 2fr 1fr 1.5fr 1fr;
  gap: 40px;
  margin-bottom: 40px;
}

.footer-section {
  display: flex;
  flex-direction: column;
}

.footer-brand {
  grid-column: 1;
}

.footer-logo {
  display: flex;
  align-items: center;
  margin-bottom: 12px;
}

.footer-logo-image {
  height: 80px;
  width: 80px;
  object-fit: contain;
  border-radius: 50%;
  border: 3px solid #ffd600;
  padding: 8px;
  background: #fff;
  box-shadow: 0 4px 16px rgba(255, 214, 0, 0.15);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.footer-logo-image:hover {
  transform: scale(1.05);
  box-shadow: 0 6px 20px rgba(255, 214, 0, 0.25);
}

.footer-description {
  color: #666;
  font-size: 0.9rem;
  line-height: 1.5;
  margin: 0 0 20px 0;
}

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
}

.info-item i {
  color: #ffd600;
  font-size: 1rem;
  width: 16px;
}

.info-item span {
  font-weight: 500;
}

.footer-heading {
  font-size: 1rem;
  font-weight: 600;
  color: #23211a;
  margin-bottom: 16px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.footer-links {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-links li {
  margin-bottom: 8px;
}

.footer-links a {
  color: #666;
  text-decoration: none;
  font-size: 0.9rem;
  transition: color 0.2s ease;
}

.footer-links a:hover {
  color: #ffd600;
}

.contact-item {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin-bottom: 12px;
  font-size: 0.9rem;
  color: #666;
  line-height: 1.4;
}

.contact-item i {
  color: #ffd600;
  font-size: 1rem;
  margin-top: 2px;
  flex-shrink: 0;
}

.social-links {
  display: flex;
  gap: 15px;
}

.social-link {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  background: #f8f8f8;
  border-radius: 50%;
  color: #666;
  text-decoration: none;
  transition: all 0.2s ease;
}

.social-link:hover {
  background: #ffd600;
  color: #23211a;
  transform: translateY(-2px);
}

.social-link i {
  font-size: 1.1rem;
}

.footer-bottom {
  border-top: 1px solid #f0f0f0;
  padding-top: 20px;
  text-align: center;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 10px;
}

.copyright {
  color: #999;
  font-size: 0.85rem;
  margin: 0;
}

.developed-by {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #999;
  font-size: 0.85rem;
}

.developer-logo {
  height: 30px;
  width: auto;
  filter: grayscale(0.3);
  transition: filter 0.2s ease;
}

.developer-logo:hover {
  filter: grayscale(0);
}

#backToTopBtn {
  position: fixed;
  bottom: 32px;
  right: 32px;
  z-index: 10000;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: #ffd600;
  color: #23211a;
  border: none;
  box-shadow: 0 2px 12px rgba(0,0,0,0.10);
  display: none;
  align-items: center;
  justify-content: center;
  font-size: 1.7rem;
  cursor: pointer;
  transition: background 0.18s, color 0.18s, transform 0.18s;
  margin: 0;
  padding: 0;
}
#backToTopBtn:hover {
  background: #23211a;
  color: #ffd600;
  transform: translateY(-3px) scale(1.08);
}

@media (max-width: 768px) {
  .newsletter-section {
    padding: 60px 0;
  }
  
  .newsletter-container {
    padding: 0 15px;
  }
  
  .newsletter-title {
    font-size: 2.2rem;
    margin-bottom: 15px;
  }
  
  .newsletter-subtitle {
    font-size: 1rem;
    margin-bottom: 30px;
  }
  
  .newsletter-form {
    max-width: 100%;
    border-radius: 40px;
  }
  
  .footer-content {
    grid-template-columns: 1fr;
    gap: 30px;
  }
  
  .footer-brand {
    grid-column: 1;
    text-align: center;
  }
  
  .footer-logo-image {
    height: 70px;
    width: 70px;
  }
  
  .social-links {
    justify-content: center;
  }
  
  .footer-bottom {
    text-align: center;
    flex-direction: column;
    gap: 15px;
  }
  
  .footer-container {
    padding: 0 15px;
  }
  
  .shop-footer {
    padding: 40px 0 20px 0;
  }
  
  .footer-heading {
    font-size: 0.95rem;
    margin-bottom: 12px;
  }
  
  .footer-description {
    font-size: 0.85rem;
    margin-bottom: 15px;
  }
  
  .company-info {
    gap: 10px;
    margin-top: 12px;
  }
  
  .info-item {
    font-size: 0.85rem;
    gap: 8px;
  }
  
  .footer-links li {
    margin-bottom: 6px;
  }
  
  .footer-links a {
    font-size: 0.85rem;
  }
  
  .contact-item {
    font-size: 0.85rem;
    margin-bottom: 10px;
    gap: 8px;
  }
  
  .social-links {
    gap: 12px;
  }
  
  .social-link {
    width: 36px;
    height: 36px;
  }
  
  .social-link i {
    font-size: 1rem;
  }
  
  .copyright {
    font-size: 0.8rem;
  }
  
  .developed-by {
    font-size: 0.8rem;
    gap: 6px;
  }
  
  .developer-logo {
    height: 25px;
  }
  
  #backToTopBtn {
    bottom: 100px;
    right: 20px !important;
    width: 44px;
    height: 44px;
    font-size: 1.5rem;
    margin: 0 !important;
    padding: 0;
    z-index: 10000;
  }
}

@media (max-width: 480px) {
  .newsletter-section {
    padding: 40px 0;
  }
  
  .newsletter-container {
    padding: 0 12px;
  }
  
  .newsletter-title {
    font-size: 1.8rem;
    margin-bottom: 12px;
    line-height: 1.2;
  }
  
  .newsletter-subtitle {
    font-size: 0.9rem;
    margin-bottom: 25px;
    line-height: 1.4;
  }
  
  .newsletter-form {
    max-width: 100% !important;
    width: 100% !important;
    border-radius: 25px;
    gap: 6px;
    padding: 6px;
    flex-direction: column !important;
    box-sizing: border-box;
  }
  
  .newsletter-input, .newsletter-btn {
    border-radius: 20px;
    padding: 14px 16px;
    font-size: 0.9rem;
    width: 100% !important;
    box-sizing: border-box;
    min-width: 0;
    max-width: 100%;
    flex: none;
  }
  
  .newsletter-input {
    border: 1px solid #e0e0e0;
  }
  
  .newsletter-btn {
    margin-top: 0;
    font-weight: 600;
    white-space: nowrap;
  }
}
  
  .footer-content {
    gap: 25px;
  }
  
  .footer-logo-image {
    height: 60px;
    width: 60px;
  }
  
  .footer-description {
    font-size: 0.8rem;
    line-height: 1.4;
  }
  
  .company-info {
    gap: 8px;
  }
  
  .info-item {
    font-size: 0.8rem;
  }
  
  .info-item i {
    font-size: 0.9rem;
    width: 14px;
  }
  
  .footer-heading {
    font-size: 0.9rem;
    margin-bottom: 10px;
  }
  
  .footer-links li {
    margin-bottom: 5px;
  }
  
  .footer-links a {
    font-size: 0.8rem;
  }
  
  .contact-item {
    font-size: 0.8rem;
    margin-bottom: 8px;
    line-height: 1.3;
  }
  
  .contact-item i {
    font-size: 0.9rem;
  }
  
  .social-links {
    gap: 10px;
  }
  
  .social-link {
    width: 32px;
    height: 32px;
  }
  
  .social-link i {
    font-size: 0.9rem;
  }
  
  .footer-bottom {
    padding-top: 15px;
    gap: 12px;
  }
  
  .copyright {
    font-size: 0.75rem;
  }
  
  .developed-by {
    font-size: 0.75rem;
  }
  
  .developer-logo {
    height: 22px;
  }
  
  #backToTopBtn {
    bottom: 90px;
    right: 15px;
    width: 40px;
    height: 40px;
    font-size: 1.3rem;
    margin: 0;
    padding: 0;
    z-index: 10000;
  }
  
  .shop-footer {
    padding: 30px 0 15px 0;
  }
  
  .footer-container {
    padding: 0 12px;
  }
}

@media (max-width: 360px) {
  .newsletter-section {
    padding: 30px 0;
  }
  
  .newsletter-container {
    padding: 0 10px;
  }
  
  .newsletter-title {
    font-size: 1.5rem;
    margin-bottom: 10px;
  }
  
  .newsletter-subtitle {
    font-size: 0.85rem;
    margin-bottom: 20px;
  }
  
  .newsletter-form {
    border-radius: 20px;
    gap: 4px;
    padding: 4px;
    width: 100% !important;
    max-width: 100% !important;
    flex-direction: column !important;
    box-sizing: border-box;
  }
  
  .newsletter-input, .newsletter-btn {
    font-size: 0.85rem;
    padding: 12px 14px;
    border-radius: 16px;
    width: 100% !important;
    box-sizing: border-box;
    min-width: 0;
    max-width: 100%;
    flex: none;
  }
  
  #backToTopBtn {
    bottom: 85px;
    right: 15px;
    width: 38px;
    height: 38px;
    font-size: 1.2rem;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    max-width: calc(100vw - 30px);
    max-height: calc(100vh - 30px);
    z-index: 10000;
  }
  
  .footer-logo-image {
    height: 55px;
    width: 55px;
  }
  
  .footer-description {
    font-size: 0.75rem;
  }
  
  .info-item {
    font-size: 0.75rem;
  }
  
  .footer-heading {
    font-size: 0.85rem;
  }
  
  .footer-links a {
    font-size: 0.75rem;
  }
  
  .contact-item {
    font-size: 0.75rem;
  }
  
  .social-link {
    width: 30px;
    height: 30px;
  }
  
  .social-link i {
    font-size: 0.85rem;
  }
  
  .copyright {
    font-size: 0.7rem;
  }
  
  .developed-by {
    font-size: 0.7rem;
  }
  
  .developer-logo {
    height: 20px;
  }
  
  #backToTopBtn {
    bottom: 15px;
    right: 15px;
    width: 38px;
    height: 38px;
    font-size: 1.2rem;
    margin: 0;
    padding: 0;
  }
}



</style> 

<script>
// Show/hide back to top button
window.addEventListener('scroll', function() {
  const btn = document.getElementById('backToTopBtn');
  if (window.scrollY > 100) {
    btn.style.display = 'flex';
  } else {
    btn.style.display = 'none';
  }
});
// Smooth scroll to top
const backToTopBtn = document.getElementById('backToTopBtn');
if (backToTopBtn) {
  backToTopBtn.onclick = function() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };
}
</script> 