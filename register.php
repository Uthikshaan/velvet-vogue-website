<?php
require_once 'header.php';

if (is_logged_in()) {
    header("Location: profile.php");
    exit();
}

$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);

    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_msg = "Please fill in all mandatory registration fields.";
    } elseif ($password !== $confirm_password) {
        $error_msg = "Passwords do not match. Please try again.";
    } elseif (isset($pdo)) {
        try {
            // Check existing email
            $stmt_check = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt_check->execute([':email' => $email]);
            if ($stmt_check->rowCount() > 0) {
                $error_msg = "An account with this email address already exists.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role, phone, address) VALUES (:full_name, :email, :password, 'customer', :phone, :address)");
                $stmt->execute([
                    ':full_name' => $full_name,
                    ':email' => $email,
                    ':password' => $hashed_password,
                    ':phone' => $phone,
                    ':address' => $address
                ]);

                $user_id = $pdo->lastInsertId();
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $full_name;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = 'customer';

                header("Location: profile.php?registered=1");
                exit();
            }
        } catch (PDOException $e) {
            $error_msg = "Registration failed: " . $e->getMessage();
        }
    }
}
?>

<div class="container">
    <div class="auth-card" style="max-width: 550px;">
        <h2>Create Account</h2>
        <p style="text-align: center; color: var(--text-muted); margin-bottom: 25px;">Join Velvet Vogue for exclusive offers and easy order tracking</p>

        <?php if ($error_msg): ?>
            <div class="alert alert-danger"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="needs-validation">
            <div class="form-group">
                <label for="full_name">Full Name *</label>
                <input type="text" name="full_name" id="full_name" required class="form-control" placeholder="John Finlo">
            </div>

            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" name="email" id="email" required class="form-control" placeholder="john@example.com">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" name="password" id="password" required class="form-control" placeholder="••••••••">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password *</label>
                    <input type="password" name="confirm_password" id="confirm_password" required class="form-control" placeholder="••••••••">
                </div>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" name="phone" id="phone" class="form-control" placeholder="0771234567">
            </div>

            <div class="form-group">
                <label for="address">Delivery Address</label>
                <textarea name="address" id="address" rows="2" class="form-control" placeholder="Enter your full home address"></textarea>
            </div>

            <button type="submit" name="register" class="btn btn-primary" style="width: 100%; margin-top: 10px; padding: 12px;">
                Create Account <i class="fa-solid fa-user-plus"></i>
            </button>
        </form>

        <div style="margin-top: 25px; text-align: center; border-top: 1px solid var(--border-color); padding-top: 15px; font-size: 0.9rem;">
            <p>Already have an account? <a href="login.php" style="color: var(--primary-color); font-weight: 600;">Log In</a></p>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
