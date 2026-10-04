<?php
// In config.php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "velvet_vogue_db";

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // Automatic check & migration: Ensure all database prices are set in LKR above Rs. 3,000
    $low_price_count = $pdo->query("SELECT COUNT(*) FROM products WHERE price < 3000")->fetchColumn();
    if ($low_price_count > 0) {
        $pdo->exec("UPDATE products SET price = 24500.00 WHERE id = 1");
        $pdo->exec("UPDATE products SET price = 32000.00 WHERE id = 2");
        $pdo->exec("UPDATE products SET price = 16500.00 WHERE id = 3");
        $pdo->exec("UPDATE products SET price = 6800.00 WHERE id = 4");
        $pdo->exec("UPDATE products SET price = 8900.00 WHERE id = 5");
        $pdo->exec("UPDATE products SET price = 36500.00 WHERE id = 6");
        $pdo->exec("UPDATE products SET price = 14200.00 WHERE id = 7");
        $pdo->exec("UPDATE products SET price = 9500.00 WHERE id = 8");
        $pdo->exec("UPDATE products SET price = price * 250 WHERE price < 3000");
    }
} catch (PDOException $e) {
    $db_connected = false;
    $db_error = $e->getMessage();
}

// Global Site Helpers - Currency set to LKR (Rs.)
function format_price($amount) {
    return 'Rs. ' . number_format((float)$amount, 2);
}

function get_cart_count() {
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        return 0;
    }
    $total_items = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_items += $item['quantity'];
    }
    return $total_items;
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
?>
