<?php
require_once 'header.php';

// Handle Cart Actions (Update Quantity or Remove)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $key = sanitize($_POST['cart_key']);

        if ($_POST['action'] === 'update' && isset($_SESSION['cart'][$key])) {
            $new_qty = max(1, (int)$_POST['quantity']);
            $_SESSION['cart'][$key]['quantity'] = $new_qty;
        } elseif ($_POST['action'] === 'remove' && isset($_SESSION['cart'][$key])) {
            unset($_SESSION['cart'][$key]);
        }
    }
    header("Location: cart.php");
    exit();
}

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = ($subtotal >= 15000 || $subtotal == 0) ? 0.00 : 750.00;
$grand_total = $subtotal + $shipping;
?>

<div class="container" style="margin: 40px auto;">
    <h1 class="brand-font" style="margin-bottom: 20px; color: var(--primary-color);">Your Shopping Cart</h1>

    <?php if (!empty($cart_items)): ?>
        <div class="cart-layout-grid">
            <!-- Cart Items Table -->
            <div class="table-responsive">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product Item</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $key => $item): ?>
                            <?php $item_total = $item['price'] * $item['quantity']; ?>
                            <tr>
                                <td>
                                    <div class="cart-item-info">
                                        <img src="<?php echo sanitize($item['image_url']); ?>" alt="<?php echo sanitize($item['title']); ?>" class="cart-item-img">
                                        <div>
                                            <h4 style="font-size: 1rem;"><?php echo sanitize($item['title']); ?></h4>
                                            <p style="font-size: 0.8rem; color: var(--text-muted);">
                                                Size: <strong><?php echo sanitize($item['size']); ?></strong> | Color: <strong><?php echo sanitize($item['color']); ?></strong>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo format_price($item['price']); ?></td>
                                <td>
                                    <form action="cart.php" method="POST" style="display: flex; gap: 5px; align-items: center;">
                                        <input type="hidden" name="cart_key" value="<?php echo $key; ?>">
                                        <input type="hidden" name="action" value="update">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="10" class="form-control qty-input">
                                        <button type="submit" class="btn btn-outline btn-sm" title="Update Quantity">
                                            <i class="fa-solid fa-rotate"></i>
                                        </button>
                                    </form>
                                </td>
                                <td style="font-weight: 600; color: var(--primary-color);"><?php echo format_price($item_total); ?></td>
                                <td>
                                    <form action="cart.php" method="POST">
                                        <input type="hidden" name="cart_key" value="<?php echo $key; ?>">
                                        <input type="hidden" name="action" value="remove">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Remove Item">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <a href="products.php" class="btn btn-outline" style="margin-top: 15px;">
                    &larr; Continue Shopping
                </a>
            </div>

            <!-- Cart Summary Box -->
            <div>
                <div class="cart-summary">
                    <h3 style="border-bottom: 2px solid var(--border-color); padding-bottom: 10px; margin-bottom: 20px;">Order Summary</h3>
                    
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span><?php echo format_price($subtotal); ?></span>
                    </div>

                    <div class="summary-row">
                        <span>Estimated Shipping:</span>
                        <span><?php echo ($shipping == 0) ? '<strong style="color: green;">FREE</strong>' : format_price($shipping); ?></span>
                    </div>

                    <div class="summary-row total">
                        <span>Total Payable:</span>
                        <span><?php echo format_price($grand_total); ?></span>
                    </div>

                    <a href="checkout.php" class="btn btn-primary" style="width: 100%; margin-top: 20px; padding: 14px; font-size: 1.05rem;">
                        Proceed to Checkout <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div style="background: white; padding: 50px; text-align: center; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
            <i class="fa-solid fa-cart-flatbed" style="font-size: 4rem; color: var(--text-muted); margin-bottom: 20px;"></i>
            <h2>Your Shopping Cart is Empty</h2>
            <p style="color: var(--text-muted); margin-bottom: 25px;">Looks like you haven't added any trendy items from Velvet Vogue yet.</p>
            <a href="products.php" class="btn btn-primary">Browse Collections Now</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
