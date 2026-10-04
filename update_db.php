<?php
require_once 'config.php';

if (isset($pdo)) {
    try {
        $pdo->exec("UPDATE products SET price = 24500.00 WHERE id = 1");
        $pdo->exec("UPDATE products SET price = 32000.00 WHERE id = 2");
        $pdo->exec("UPDATE products SET price = 16500.00 WHERE id = 3");
        $pdo->exec("UPDATE products SET price = 6800.00 WHERE id = 4");
        $pdo->exec("UPDATE products SET price = 8900.00 WHERE id = 5");
        $pdo->exec("UPDATE products SET price = 36500.00 WHERE id = 6");
        $pdo->exec("UPDATE products SET price = 14200.00 WHERE id = 7");
        $pdo->exec("UPDATE products SET price = 9500.00 WHERE id = 8");
        $pdo->exec("UPDATE products SET price = price * 250 WHERE price < 3000");
        echo "SUCCESS: Database prices updated successfully in MySQL!\n";
    } catch (PDOException $e) {
        echo "DB ERROR: " . $e->getMessage() . "\n";
    }
} else {
    echo "NO PDO CONNECTION\n";
}
?>
