<?php
require_once 'header.php';


$featured_products = [];
$new_products = [];

if (isset($pdo)) {
    try {
        $stmt_featured = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_featured = 1 LIMIT 4");
        $featured_products = $stmt_featured->fetchAll();

        $stmt_new = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_new = 1 LIMIT 4");
        $new_products = $stmt_new->fetchAll();
    } catch (PDOException $e) {
        
    }
}
?>

<!-- Hero Banner Section -->
<section class="hero">
    <div class="container hero-content">
        <h1>Define Your Style with Velvet Vogue</h1>
        <p>Curated casualwear and tailored formal wear for ambitious young adults. Express your authentic identity through premium fashion.</p>
        <a href="products.php" class="btn btn-secondary" style="font-size: 1.1rem; padding: 14px 32px;">
            <i class="fa-solid fa-store"></i> Explore Collection
        </a>
    </div>
</section>

<!-- Product Categories Section -->
<section class="container" style="margin-bottom: 60px;">
    <h2 class="section-title">Shop by Category</h2>
    <p class="section-subtitle">Discover our broad range of trendy collections designed for every occasion</p>

    <div class="category-grid">
        <div class="category-card">
            <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&auto=format&fit=crop&q=80" alt="Casual Wear">
            <div class="category-overlay">
                <h3>Casual Wear</h3>
                <a href="products.php?category=1" class="btn btn-outline btn-sm" style="color: white; border-color: white;">Shop Casual</a>
            </div>
        </div>

        <div class="category-card">
            <img src="https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=600&auto=format&fit=crop&q=80" alt="Formal Wear">
            <div class="category-overlay">
                <h3>Formal Wear</h3>
                <a href="products.php?category=2" class="btn btn-outline btn-sm" style="color: white; border-color: white;">Shop Formal</a>
            </div>
        </div>

        <div class="category-card">
            <img src="https://images.unsplash.com/photo-1544441893-675973e31985?w=600&auto=format&fit=crop&q=80" alt="Outerwear">
            <div class="category-overlay">
                <h3>Outerwear</h3>
                <a href="products.php?category=3" class="btn btn-outline btn-sm" style="color: white; border-color: white;">Shop Coats</a>
            </div>
        </div>

        <div class="category-card">
            <img src="https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=600&auto=format&fit=crop&q=80" alt="Accessories">
            <div class="category-overlay">
                <h3>Accessories</h3>
                <a href="products.php?category=4" class="btn btn-outline btn-sm" style="color: white; border-color: white;">Shop Accessories</a>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="container" style="margin-bottom: 60px;">
    <h2 class="section-title">Featured Highlights</h2>
    <p class="section-subtitle">Top picks selected by founder John Finlo for this season</p>

    <div class="product-grid">
        <?php if (!empty($featured_products)): ?>
            <?php foreach ($featured_products as $product): ?>
                <div class="product-card">
                    <div class="product-thumb">
                        <span class="badge-tag">Featured</span>
                        <img src="<?php echo sanitize($product['image_url']); ?>" alt="<?php echo sanitize($product['title']); ?>">
                    </div>
                    <div class="product-details">
                        <span class="product-category"><?php echo sanitize($product['category_name']); ?> | <?php echo sanitize($product['gender']); ?></span>
                        <h3 class="product-title"><?php echo sanitize($product['title']); ?></h3>
                        <div class="product-price"><?php echo format_price($product['price']); ?></div>
                        <div class="product-actions">
                            <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-outline btn-sm" style="width: 100%;">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; grid-column: 1/-1;">Please import <code>db_setup.sql</code> in phpMyAdmin to load products into MySQL database.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Promotional Banner Section -->
<section style="background-color: var(--primary-color); color: white; padding: 50px 0; margin-bottom: 60px;">
    <div class="container" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <div>
            <h2 style="font-size: 2rem; color: white;">Exclusive Student & Young Adult Offer!</h2>
            <p style="font-size: 1.1rem; opacity: 0.9;">Get 15% off on your first order with code: <strong>VELVET15</strong></p>
        </div>
        <a href="products.php" class="btn btn-secondary">Shop Promo Items</a>
    </div>
</section>

<!-- New Arrivals Section -->
<section class="container" style="margin-bottom: 60px;">
    <h2 class="section-title">New Arrivals</h2>
    <p class="section-subtitle">Fresh styles added to the Velvet Vogue showcase</p>

    <div class="product-grid">
        <?php if (!empty($new_products)): ?>
            <?php foreach ($new_products as $product): ?>
                <div class="product-card">
                    <div class="product-thumb">
                        <span class="badge-tag badge-gold">New</span>
                        <img src="<?php echo sanitize($product['image_url']); ?>" alt="<?php echo sanitize($product['title']); ?>">
                    </div>
                    <div class="product-details">
                        <span class="product-category"><?php echo sanitize($product['category_name']); ?> | <?php echo sanitize($product['gender']); ?></span>
                        <h3 class="product-title"><?php echo sanitize($product['title']); ?></h3>
                        <div class="product-price"><?php echo format_price($product['price']); ?></div>
                        <div class="product-actions">
                            <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-primary btn-sm" style="width: 100%;">View Item</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'footer.php'; ?>
