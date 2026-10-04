<?php
require_once 'header.php';

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

if (empty($cart_items)) {
    header("Location: cart.php");
    exit();
}

$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = ($subtotal >= 15000) ? 0.00 : 750.00;
$grand_total = $subtotal + $shipping;

$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $payment_method = sanitize($_POST['payment_method']);

    if (empty($name) || empty($email) || empty($phone) || empty($address)) {
        $error_msg = "Please fill in all required contact and shipping details.";
    } elseif (isset($pdo)) {
        try {
            $pdo->beginTransaction();

            $user_id = is_logged_in() ? $_SESSION['user_id'] : null;

            // Insert into orders table
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, customer_name, customer_email, customer_phone, shipping_address, total_amount, payment_method, status) VALUES (:user_id, :name, :email, :phone, :address, :total, :payment_method, 'Processing')");
            $stmt->execute([
                ':user_id' => $user_id,
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone,
                ':address' => $address,
                ':total' => $grand_total,
                ':payment_method' => $payment_method
            ]);

            $order_id = $pdo->lastInsertId();

            // Insert each item into order_items table
            $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, size, color, quantity, price) VALUES (:order_id, :product_id, :size, :color, :quantity, :price)");
            foreach ($cart_items as $item) {
                $stmt_item->execute([
                    ':order_id' => $order_id,
                    ':product_id' => $item['id'],
                    ':size' => $item['size'],
                    ':color' => $item['color'],
                    ':quantity' => $item['quantity'],
                    ':price' => $item['price']
                ]);
            }

            $pdo->commit();

            // Clear session cart
            unset($_SESSION['cart']);

            header("Location: order_confirmation.php?order_id=" . $order_id);
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error_msg = "Order placement error: " . $e->getMessage();
        }
    }
}
?>

<div class="container" style="margin: 40px auto;">
    <h1 class="brand-font" style="margin-bottom: 20px; color: var(--primary-color);">Checkout & Transaction</h1>

    <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form action="checkout.php" method="POST" class="needs-validation">
        <div class="checkout-layout-grid">
            <!-- Shipping & Payment Form -->
            <div style="background: white; padding: 30px; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <h3 style="margin-bottom: 20px; border-bottom: 2px solid var(--border-color); padding-bottom: 10px;">1. Shipping Information</h3>
                
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" name="name" id="name" required class="form-control" value="<?php echo is_logged_in() ? sanitize($_SESSION['user_name']) : ''; ?>">
                </div>

                <div class="form-grid-2col">
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" name="email" id="email" required class="form-control" value="<?php echo is_logged_in() ? sanitize($_SESSION['user_email']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number *</label>
                        <input type="text" name="phone" id="phone" required class="form-control" placeholder="0771234567">
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Shipping Delivery Address *</label>
                    <textarea name="address" id="address" rows="3" required class="form-control" placeholder="Enter street address, city and postal code"></textarea>
                </div>

                <h3 style="margin: 30px 0 20px 0; border-bottom: 2px solid var(--border-color); padding-bottom: 10px;">2. Payment Method</h3>
                
                <div class="form-group">
                    <label style="display: block; margin-bottom: 8px;"><input type="radio" name="payment_method" value="Credit/Debit Card" checked> Credit / Debit Card (Visa, MasterCard)</label>
                    <label style="display: block;"><input type="radio" name="payment_method" value="Cash on Delivery"> Cash on Delivery (COD)</label>
                </div>

                <div id="cardDetails" style="background: #f8f9fa; padding: 18px; border-radius: var(--radius-sm); margin-top: 15px;">
                    <div class="form-group">
                        <label>Card Number</label>
                        <input type="text" class="form-control" placeholder="4532 •••• •••• 8901">
                    </div>
                    <div class="form-grid-2col">
                        <div class="form-group">
                            <label>Expiry Date</label>
                            <input type="text" class="form-control" placeholder="MM/YY">
                        </div>
                        <div class="form-group">
                            <label>CVC / CVV</label>
                            <input type="text" class="form-control" placeholder="123">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Review Panel -->
            <div>
                <div class="cart-summary">
                    <h3 style="border-bottom: 2px solid var(--border-color); padding-bottom: 10px; margin-bottom: 20px;">Review Order</h3>
                    
                    <ul style="margin-bottom: 20px;">
                        <?php foreach ($cart_items as $item): ?>
                            <li style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.9rem;">
                                <span><?php echo sanitize($item['title']); ?> (x<?php echo $item['quantity']; ?>)</span>
                                <strong><?php echo format_price($item['price'] * $item['quantity']); ?></strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span><?php echo format_price($subtotal); ?></span>
                    </div>

                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span><?php echo ($shipping == 0) ? 'FREE' : format_price($shipping); ?></span>
                    </div>

                    <div class="summary-row total">
                        <span>Total to Pay:</span>
                        <span><?php echo format_price($grand_total); ?></span>
                    </div>

                    <button type="submit" name="place_order" class="btn btn-secondary" style="width: 100%; margin-top: 20px; padding: 14px; font-size: 1.05rem;">
                        <i class="fa-solid fa-lock"></i> Complete Order
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php require_once 'footer.php'; ?>
