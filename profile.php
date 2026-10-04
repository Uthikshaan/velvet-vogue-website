<?php
require_once 'header.php';

if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user = null;
$my_orders = [];

if (isset($pdo)) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $user_id]);
        $user = $stmt->fetch();

        $stmt_orders = $pdo->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY id DESC");
        $stmt_orders->execute([':user_id' => $user_id]);
        $my_orders = $stmt_orders->fetchAll();
    } catch (PDOException $e) {}
}
?>

<div class="container" style="margin: 40px auto;">
    <h1 class="brand-font" style="margin-bottom: 20px; color: var(--primary-color);">My Account Profile</h1>

    <?php if (isset($_GET['registered'])): ?>
        <div class="alert alert-success">Welcome to Velvet Vogue! Your account has been successfully created.</div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 320px 1fr; gap: 30px;">
        <!-- User Information Box -->
        <div style="background: white; padding: 25px; border-radius: var(--radius-md); border: 1px solid var(--border-color); height: fit-content;">
            <div style="text-align: center; margin-bottom: 20px;">
                <i class="fa-solid fa-circle-user" style="font-size: 4rem; color: var(--primary-color);"></i>
                <h3 style="margin-top: 10px;"><?php echo sanitize($_SESSION['user_name']); ?></h3>
                <span style="background: #e2e8f0; padding: 2px 10px; border-radius: 10px; font-size: 0.8rem; text-transform: uppercase;">
                    <?php echo sanitize($_SESSION['user_role']); ?>
                </span>
            </div>

            <div style="border-top: 1px solid var(--border-color); padding-top: 15px; font-size: 0.95rem;">
                <p style="margin-bottom: 10px;"><strong>Email:</strong><br><?php echo sanitize($_SESSION['user_email']); ?></p>
                <p style="margin-bottom: 10px;"><strong>Phone:</strong><br><?php echo isset($user['phone']) ? sanitize($user['phone']) : 'N/A'; ?></p>
                <p style="margin-bottom: 10px;"><strong>Address:</strong><br><?php echo isset($user['address']) ? sanitize($user['address']) : 'N/A'; ?></p>
            </div>

            <a href="logout.php" class="btn btn-outline btn-sm" style="width: 100%; margin-top: 15px; text-align: center; color: #dc3545; border-color: #dc3545;">
                Log Out
            </a>
        </div>

        <!-- Order History -->
        <div style="background: white; padding: 25px; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
            <h3 style="margin-bottom: 20px; border-bottom: 2px solid var(--border-color); padding-bottom: 10px;">My Order History</h3>

            <?php if (!empty($my_orders)): ?>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($my_orders as $ord): ?>
                            <tr>
                                <td>#VV-<?php echo $ord['id']; ?></td>
                                <td><?php echo date('M d, Y', strtotime($ord['created_at'])); ?></td>
                                <td><?php echo format_price($ord['total_amount']); ?></td>
                                <td>
                                    <span style="background: #d4edda; color: #155724; padding: 3px 8px; border-radius: 10px; font-size: 0.8rem;">
                                        <?php echo sanitize($ord['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="order_confirmation.php?order_id=<?php echo $ord['id']; ?>" class="btn btn-outline btn-sm">View Receipt</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: var(--text-muted);">You haven't placed any orders with Velvet Vogue yet.</p>
                <a href="products.php" class="btn btn-primary btn-sm" style="margin-top: 15px;">Start Shopping</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
