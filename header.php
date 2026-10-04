<?php
require_once 'config.php';
$cart_count = get_cart_count();
$current_page = basename($_SERVER['PHP_SELF']);
$current_gender = isset($_GET['gender']) ? $_GET['gender'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Velvet Vogue | Trendy & Formal Fashion</title>
    <!-- CSS Stylesheet -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Top Announcement Bar -->
    <div class="top-bar">
        <div class="container">
            <span><i class="fa-solid fa-truck-fast"></i> Complimentary Islandwide Shipping on Orders Over Rs. 15,000 | Express Delivery Available</span>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="site-header">
        <div class="container navbar">
            <a href="index.php" class="logo">Velvet <span>Vogue</span></a>

            <div class="mobile-toggle" id="mobileToggle">
                <i class="fa-solid fa-bars"></i>
            </div>

            <nav class="nav-links" id="navLinks">
                <a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a>
                <a href="products.php" class="<?php echo ($current_page == 'products.php' && empty($current_gender)) ? 'active' : ''; ?>">Shop All</a>
                <a href="products.php?gender=Men" class="<?php echo ($current_page == 'products.php' && $current_gender == 'Men') ? 'active' : ''; ?>"><i class="fa-solid fa-mars"></i> Men</a>
                <a href="products.php?gender=Women" class="<?php echo ($current_page == 'products.php' && $current_gender == 'Women') ? 'active' : ''; ?>"><i class="fa-solid fa-venus"></i> Women</a>
                <a href="contact.php" class="<?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">Contact Us</a>
                
                <?php if (is_admin()): ?>
                    <a href="admin.php" class="<?php echo ($current_page == 'admin.php') ? 'active' : ''; ?>" style="color: var(--accent-gold); font-weight: 600;">
                        <i class="fa-solid fa-user-shield"></i> Admin Panel
                    </a>
                <?php endif; ?>
            </nav>

            <div class="nav-actions">
                <a href="cart.php" class="cart-icon" title="View Shopping Cart">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <?php if ($cart_count > 0): ?>
                        <span class="cart-badge"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>

                <?php if (is_logged_in()): ?>
                    <a href="profile.php" class="btn btn-outline btn-sm">
                        <i class="fa-solid fa-user"></i> <?php echo sanitize($_SESSION['user_name']); ?>
                    </a>
                    <a href="logout.php" class="btn btn-sm" style="color: #dc3545;" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary btn-sm">Sign In</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main style="flex: 1;">
