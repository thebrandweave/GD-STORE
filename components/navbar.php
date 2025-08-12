<?php
// Dynamically determine the base path for shop components
$script_name = $_SERVER['SCRIPT_NAME'];
$shop_pos = strpos($script_name, '/shop/');

if ($shop_pos !== false) {
    $shop_base = substr($script_name, 0, $shop_pos + strlen('/shop/'));
} else {
    // Fallback for live server where /shop/ might not be in the path
    $shop_base = '/';
}
$current_path = $_SERVER['SCRIPT_NAME'];
?>
<!-- Top Info Bar -->
<div class="topbar">
  <div class="topbar-left">
    <span>+91 8105753472</span>
    <span>goldendream175@gmail.com</span>
  </div>
  <div class="topbar-right">
    <a href="<?php echo $shop_base; ?>contact/index.php">Contact Us</a>
    <a href="<?php echo $shop_base; ?>about/index.php">About Us</a>
  </div>
</div>

<!-- Main Navbar -->
<nav class="main-navbar">
  <div class="navbar-logo flex">
    <a href="<?php echo $shop_base; ?>index.php">
      <img src="<?php echo $shop_base; ?>assets/image/gd-store-logo2.png" alt="GD Store" class="logo-image">
    </a>
  </div>
  
  <!-- Desktop Navigation -->
  <ul class="navbar-links desktop-nav">
    <li><a href="<?php echo $shop_base; ?>index.php" class="<?php echo (strpos($current_path, '/shop/index.php') !== false) ? 'active' : ''; ?>">Home</a></li>
    <li><a href="<?php echo $shop_base; ?>category/index.php" class="<?php echo (strpos($current_path, '/shop/category') !== false) ? 'active' : ''; ?>">Categories</a></li>
    <li><a href="<?php echo $shop_base; ?>products/index.php" class="<?php echo (strpos($current_path, '/shop/products') !== false) ? 'active' : ''; ?>">Products</a></li>
    <li><a href="<?php echo $shop_base; ?>about/index.php" class="<?php echo (strpos($current_path, '/shop/about') !== false) ? 'active' : ''; ?>">About</a></li>
    <li><a href="<?php echo $shop_base; ?>contact/index.php" class="<?php echo (strpos($current_path, '/shop/contact') !== false) ? 'active' : ''; ?>">Contact</a></li>
  </ul>
  
  <div class="navbar-search-icons">
    <form class="navbar-search">
      <input type="text" placeholder="I'm looking for...">
    </form>
    <?php if (isset($_SESSION['user_id'])): ?>
      <div class="navbar-icons" style="display:flex;align-items:center;gap:18px;">
        <?php
        // Get unread notification count
        $unread_count = 0;
        $cart_count = 0;
        if (isset($_SESSION['user_id'])) {
            try {
                $db = new Database();
                $conn = $db->getConnection();
                
                // Get CustomerUniqueID for the current user
                $user_id = $_SESSION['user_id'];
                $user_source = $_SESSION['user_source'] ?? '';
                
                if ($user_source === Database::$shop_db) {
                    // For shop users, get CustomerUniqueID from shop_users table
                    $stmt = $conn->prepare('SELECT CustomerUniqueID FROM shop_users WHERE CustomerID = ?');
                    $stmt->execute([$user_id]);
                    $customer_unique_id = $stmt->fetchColumn();
                } else {
                    // For main users, use the user_id directly as CustomerUniqueID
                    $customer_unique_id = $user_id;
                }
                
                if ($customer_unique_id) {
                    // Get unread notification count
                    $stmt = $conn->prepare('SELECT COUNT(*) FROM shopnotifications WHERE CustomerUniqueID = ? AND is_read = 0');
                    $stmt->execute([$customer_unique_id]);
                    $unread_count = $stmt->fetchColumn();
                    
                    // Get cart count
                    $stmt = $conn->prepare('SELECT COUNT(*) FROM cart_items WHERE CustomerUniqueID = ?');
                    $stmt->execute([$customer_unique_id]);
                    $cart_count = $stmt->fetchColumn();
                }
            } catch (Exception $e) {
                // Handle error silently
            }
        }
        ?>
        <a href="<?php echo $shop_base; ?>notifications/index.php" class="icon-badge">
            <i class="bi bi-bell"></i>
            <?php if ($unread_count > 0): ?>
                <span class="badge"><?php echo $unread_count; ?></span>
            <?php endif; ?>
        </a>
        <a href="<?php echo $shop_base; ?>cart/index.php" class="icon-badge">
            <i class="bi bi-cart"></i>
            <?php if ($cart_count > 0): ?>
                <span class="badge"><?php echo $cart_count; ?></span>
            <?php endif; ?>
        </a>
        <a href="<?php echo $shop_base; ?>profile/index.php" class="profile-icon"><i class="bi bi-person-circle"></i></a>
        <a href="<?php echo $shop_base; ?>logout.php" class="navbar-btn" style="background:var(--accent-dark);color:#fff;padding:8px 22px;border-radius:999px;font-weight:700;text-decoration:none;transition:background 0.18s,color 0.18s;font-size: var(--font-size-sm);">Logout</a>
      </div>
    <?php else: ?>
      <div style="display:flex;gap:12px;align-items:center;">
        <a href="<?php echo $shop_base; ?>login.php" class="navbar-btn" style="background:transparent;color:var(--accent-dark);border:2px solid var(--accent-dark);padding:8px 22px;border-radius:999px;font-weight:700;text-decoration:none;transition:background 0.18s,color 0.18s;">Login</a>
        <!-- <a href="<?php echo $shop_base; ?>signup.php" class="navbar-btn" style="background:var(--accent-dark);color:#fff;padding:8px 22px;border-radius:999px;font-weight:700;text-decoration:none;transition:background 0.18s,color 0.18s;">Sign Up</a> -->
      </div>
    <?php endif; ?>
  </div>
</nav>

<!-- Mobile Bottom Navigation -->
<nav class="mobile-bottom-nav">
  <a href="<?php echo $shop_base; ?>index.php" class="mobile-nav-item <?php echo (strpos($current_path, '/shop/index.php') !== false) ? 'active' : ''; ?>">
    <i class="bi bi-house"></i>
    <span>Home</span>
  </a>
  <a href="<?php echo $shop_base; ?>category/index.php" class="mobile-nav-item <?php echo (strpos($current_path, '/shop/category') !== false) ? 'active' : ''; ?>">
    <i class="bi bi-grid"></i>
    <span>Categories</span>
  </a>
  <a href="<?php echo $shop_base; ?>products/index.php" class="mobile-nav-item <?php echo (strpos($current_path, '/shop/products') !== false) ? 'active' : ''; ?>">
    <i class="bi bi-box"></i>
    <span>Products</span>
  </a>
  <?php if (isset($_SESSION['user_id'])): ?>
    <a href="<?php echo $shop_base; ?>cart/index.php" class="mobile-nav-item <?php echo (strpos($current_path, '/shop/cart') !== false) ? 'active' : ''; ?>">
      <i class="bi bi-cart"></i>
      <span>Cart</span>
      <?php if ($cart_count > 0): ?>
        <span class="mobile-cart-badge"><?php echo $cart_count; ?></span>
      <?php endif; ?>
    </a>
    <a href="<?php echo $shop_base; ?>profile/index.php" class="mobile-nav-item <?php echo (strpos($current_path, '/shop/profile') !== false) ? 'active' : ''; ?>">
      <i class="bi bi-person"></i>
      <span>Profile</span>
    </a>
  <?php else: ?>
    <a href="<?php echo $shop_base; ?>about/index.php" class="mobile-nav-item <?php echo (strpos($current_path, '/shop/about') !== false) ? 'active' : ''; ?>">
      <i class="bi bi-info-circle"></i>
      <span>About</span>
    </a>
    <a href="<?php echo $shop_base; ?>login.php" class="mobile-nav-item <?php echo (strpos($current_path, '/shop/login') !== false) ? 'active' : ''; ?>">
      <i class="bi bi-person"></i>
      <span>Login</span>
    </a>
  <?php endif; ?>
</nav>

<style>
.navbar-links a.active {
    color: var(--accent-dark) !important;
    font-weight: bold;
}

.navbar-logo {
    display: flex;
    align-items: center;
}

.logo-image {
    height: 60px;
    width: auto;
    object-fit: contain;
    transition: transform 0.2s ease;
    margin: 2px 0;
}

.logo-image:hover {
    transform: scale(1.05);
}

.profile-icon {
    color: #232526;
    font-size: 1.5rem;
    position: relative;
    text-decoration: none;
    transition: color 0.2s ease;
}

.profile-icon:hover {
    color: var(--accent-dark);
}

/* Ensure badge is always visible */
.icon-badge .badge {
    display: inline-block !important;
    min-width: 18px;
    min-height: 18px;
    text-align: center;
    line-height: 14px;
    visibility: visible !important;
    opacity: 1 !important;
}

/* Mobile Bottom Navigation */
.mobile-bottom-nav {
    display: none;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    width: 100%;
    background: #fff;
    border-top: 1px solid #e0e0e0;
    padding: 8px 0;
    z-index: 9999;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
    /* Ensure it stays fixed */
    position: fixed !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
}

.mobile-nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex: 1;
    padding: 8px 4px;
    text-decoration: none;
    color: #666;
    font-size: 12px;
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
    min-height: 60px;
}

.mobile-nav-item i {
    font-size: 20px;
    margin-bottom: 4px;
    transition: all 0.3s ease;
}

.mobile-nav-item span {
    font-size: 11px;
    line-height: 1.2;
    text-align: center;
}

.mobile-nav-item:hover {
    color: var(--accent-dark);
}

.mobile-nav-item.active {
    color: var(--accent-dark);
}

.mobile-nav-item.active i {
    transform: scale(1.1);
}

.mobile-cart-badge {
    position: absolute;
    top: 4px;
    right: 50%;
    transform: translateX(50%);
    background: var(--accent-dark);
    color: white;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: bold;
    box-shadow: 0 2px 4px rgba(255, 214, 0, 0.3);
}

/* Responsive Design */
@media (max-width: 768px) {
    .desktop-nav {
        display: none !important;
    }
    
    .mobile-bottom-nav {
        display: flex;
    }
    
    .logo-image {
        height: 45px;
    }
    
    .main-navbar {
        justify-content: space-between;
        padding: 15px 20px;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .topbar {
        display: none;
    }
    
    /* Adjust main content to account for bottom nav */
    body {
        padding-bottom: 76px !important;
        margin-bottom: 0 !important;
    }
    
    /* Ensure main content doesn't get hidden */
    .main-content,
    main,
    .container {
        padding-bottom: 76px !important;
    }
    
    /* Mobile search adjustments */
    .navbar-search {
        flex: 1;
        max-width: 300px;
        margin: 0 15px;
    }
    
    .navbar-search input {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #e0e0e0;
        border-radius: 20px;
        font-size: 14px;
    }
    
    /* Mobile icons adjustments */
    .navbar-icons {
        gap: 12px !important;
    }
    
    .icon-badge {
        font-size: 18px;
    }
    
    .navbar-btn {
        padding: 6px 16px !important;
        font-size: 12px !important;
    }
}

@media (max-width: 480px) {
    .main-navbar {
        padding: 12px 15px;
    }
    
    .logo-image {
        height: 40px;
    }
    
    .navbar-search {
        margin: 0 10px;
    }
    
    .navbar-search input {
        padding: 8px 12px;
        font-size: 13px;
    }
    
    .navbar-icons {
        gap: 8px !important;
    }
    
    .icon-badge {
        font-size: 16px;
    }
    
    .navbar-btn {
        padding: 5px 12px !important;
        font-size: 11px !important;
    }
    
    .mobile-nav-item {
        padding: 6px 2px;
        min-height: 56px;
    }
    
    .mobile-nav-item i {
        font-size: 18px;
    }
    
    .mobile-nav-item span {
        font-size: 10px;
    }
    
    .mobile-cart-badge {
        width: 14px;
        height: 14px;
        font-size: 9px;
    }
}

@media (min-width: 769px) {
    .mobile-bottom-nav {
        display: none;
    }
    
    body {
        padding-bottom: 0;
    }
}

/* Ensure proper spacing for mobile */
@media (max-width: 768px) {
    .main-content {
        margin-bottom: 76px;
    }
    
    /* Force bottom nav to stay fixed */
    .mobile-bottom-nav {
        position: fixed !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        z-index: 9999 !important;
        transform: none !important;
    }
    
    /* Prevent any scrolling issues */
    html, body {
        overflow-x: hidden;
    }
}
</style> 
