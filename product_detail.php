<?php
require_once 'header.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;

if ($product_id > 0 && isset($pdo)) {
    try {
        $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = :id");
        $stmt->execute([':id' => $product_id]);
        $product = $stmt->fetch();
    } catch (PDOException $e) {
        // error
    }
}

// Redirect if product not found
if (!$product) {
    header("Location: products.php");
    exit();
}

$sizes = array_map('trim', explode(',', $product['sizes']));
$colors = array_map('trim', explode(',', $product['colors']));

// Handle Add to Cart POST
$added_notice = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $size = isset($_POST['size']) ? sanitize($_POST['size']) : $sizes[0];
    $color = isset($_POST['color']) ? sanitize($_POST['color']) : $colors[0];
    $quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Unique key per product + size + color combination
    $cart_key = $product['id'] . '_' . $size . '_' . $color;

    if (isset($_SESSION['cart'][$cart_key])) {
        $_SESSION['cart'][$cart_key]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$cart_key] = [
            'id' => $product['id'],
            'title' => $product['title'],
            'price' => $product['price'],
            'image_url' => $product['image_url'],
            'size' => $size,
            'color' => $color,
            'quantity' => $quantity
        ];
    }

    $added_notice = true;
    // Refresh page or header cart count
    header("Location: product_detail.php?id=" . $product_id . "&success=1");
    exit();
}
?>

<div class="container">
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success" style="margin-top: 20px;">
            <i class="fa-solid fa-circle-check"></i> Item successfully added to your shopping cart! 
            <a href="cart.php" style="font-weight: 600; text-decoration: underline; margin-left: 10px;">View Shopping Cart & Checkout &rarr;</a>
        </div>
    <?php endif; ?>

    <div class="detail-container">
        <!-- Left Image -->
        <div>
            <img src="<?php echo sanitize($product['image_url']); ?>" alt="<?php echo sanitize($product['title']); ?>" class="detail-img">
        </div>

        <!-- Right Specs -->
        <div class="detail-info">
            <span class="product-category"><?php echo sanitize($product['category_name']); ?> | <?php echo sanitize($product['gender']); ?></span>
            <h1><?php echo sanitize($product['title']); ?></h1>
            <div class="detail-price"><?php echo format_price($product['price']); ?></div>

            <p class="detail-desc"><?php echo nl2br(sanitize($product['description'])); ?></p>

            <form action="product_detail.php?id=<?php echo $product['id']; ?>" method="POST">
                <!-- Size Selector -->
                <div class="option-group">
                    <label>Select Size:</label>
                    <div class="pill-options">
                        <?php foreach ($sizes as $idx => $s): ?>
                            <button type="button" class="pill-btn size-pill <?php echo ($idx === 0) ? 'active' : ''; ?>" data-value="<?php echo sanitize($s); ?>">
                                <?php echo sanitize($s); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" name="size" id="selected_size" value="<?php echo sanitize($sizes[0]); ?>">
                </div>

                <!-- Color Selector -->
                <div class="option-group">
                    <label>Select Color:</label>
                    <div class="pill-options">
                        <?php foreach ($colors as $idx => $c): ?>
                            <button type="button" class="pill-btn color-pill <?php echo ($idx === 0) ? 'active' : ''; ?>" data-value="<?php echo sanitize($c); ?>">
                                <?php echo sanitize($c); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" name="color" id="selected_color" value="<?php echo sanitize($colors[0]); ?>">
                </div>

                <!-- Quantity Selector -->
                <div class="option-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" name="quantity" id="quantity" value="1" min="1" max="10" class="form-control qty-input">
                </div>

                <div style="display: flex; gap: 15px; margin-top: 25px;">
                    <button type="submit" name="add_to_cart" class="btn btn-primary" style="flex: 1; padding: 14px;">
                        <i class="fa-solid fa-cart-plus"></i> Add To Shopping Cart
                    </button>
                    <a href="products.php" class="btn btn-outline" style="padding: 14px 20px;">
                        Back to Shop
                    </a>
                </div>
            </form>

            <div style="margin-top: 30px; border-top: 1px solid var(--border-color); padding-top: 15px; font-size: 0.85rem; color: var(--text-muted);">
                <p><i class="fa-solid fa-shield-halved"></i> 100% Authentic Velvet Vogue Guarantee</p>
                <p><i class="fa-solid fa-rotate-left"></i> Easy 14-Day Exchanges & Returns</p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
