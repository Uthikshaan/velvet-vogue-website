<?php
require_once 'header.php';

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$order = null;
$order_items = [];

if ($order_id > 0 && isset($pdo)) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->execute([':id' => $order_id]);
        $order = $stmt->fetch();

        if ($order) {
            $stmt_items = $pdo->prepare("SELECT oi.*, p.title, p.image_url FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = :order_id");
            $stmt_items->execute([':order_id' => $order_id]);
            $order_items = $stmt_items->fetchAll();
        }
    } catch (PDOException $e) {}
}

if (!$order) {
    header("Location: index.php");
    exit();
}
?>

<div class="container" style="margin: 50px auto; max-width: 750px;">
    <div style="background: white; padding: 40px; border-radius: var(--radius-md); border: 1px solid var(--border-color); text-align: center; box-shadow: var(--shadow-md);">
        <i class="fa-solid fa-circle-check" style="font-size: 4rem; color: #28a745; margin-bottom: 20px;"></i>
        <h1 class="brand-font" style="color: var(--primary-color);">Order Confirmed!</h1>
        <p style="font-size: 1.1rem; margin-bottom: 25px;">Thank you for shopping with <strong>Velvet Vogue</strong>, <?php echo sanitize($order['customer_name']); ?>!</p>
        
        <div style="background: #f8f9fa; padding: 20px; border-radius: var(--radius-sm); text-align: left; margin-bottom: 30px;">
            <p><strong>Order Reference Number:</strong> #VV-<?php echo $order['id']; ?></p>
            <p><strong>Payment Method:</strong> <?php echo sanitize($order['payment_method']); ?></p>
            <p><strong>Status:</strong> <span style="background: #e2e8f0; padding: 3px 10px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;"><?php echo sanitize($order['status']); ?></span></p>
            <p><strong>Shipping Address:</strong> <?php echo sanitize($order['shipping_address']); ?></p>
            <p><strong>Total Paid:</strong> <strong style="color: var(--primary-color); font-size: 1.1rem;"><?php echo format_price($order['total_amount']); ?></strong></p>
        </div>

        <h3 style="text-align: left; margin-bottom: 15px;">Purchased Items:</h3>
        <table class="cart-table" style="margin-bottom: 30px;">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th>Size & Color</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td style="display: flex; align-items: center; gap: 10px;">
                            <img src="<?php echo sanitize($item['image_url']); ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                            <span><?php echo sanitize($item['title']); ?></span>
                        </td>
                        <td><?php echo sanitize($item['size']); ?> / <?php echo sanitize($item['color']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo format_price($item['price'] * $item['quantity']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="products.php" class="btn btn-primary">Continue Shopping</a>
    </div>
</div>

<?php require_once 'footer.php'; ?>
