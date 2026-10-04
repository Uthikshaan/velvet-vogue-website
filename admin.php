<?php
require_once 'header.php';

// Access Control: Admin only
if (!is_admin()) {
    header("Location: login.php");
    exit();
}

$msg = '';
$error_msg = '';

// Handle Add Product POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $title = sanitize($_POST['title']);
    $category_id = (int)$_POST['category_id'];
    $gender = sanitize($_POST['gender']);
    $price = (float)$_POST['price'];
    $description = sanitize($_POST['description']);
    $sizes = sanitize($_POST['sizes']);
    $colors = sanitize($_POST['colors']);
    $image_url = sanitize($_POST['image_url']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_new = isset($_POST['is_new']) ? 1 : 0;

    if (empty($title) || $category_id <= 0 || $price <= 0 || empty($image_url)) {
        $error_msg = "Please fill in all mandatory product fields (Title, Category, Price, Image URL).";
    } elseif (isset($pdo)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (title, category_id, gender, price, description, sizes, colors, image_url, is_featured, is_new) VALUES (:title, :category_id, :gender, :price, :description, :sizes, :colors, :image_url, :is_featured, :is_new)");
            $stmt->execute([
                ':title' => $title,
                ':category_id' => $category_id,
                ':gender' => $gender,
                ':price' => $price,
                ':description' => $description,
                ':sizes' => !empty($sizes) ? $sizes : 'S, M, L, XL',
                ':colors' => !empty($colors) ? $colors : 'Black, Navy, Beige',
                ':image_url' => $image_url,
                ':is_featured' => $is_featured,
                ':is_new' => $is_new
            ]);
            $msg = "New product successfully added to Velvet Vogue showcase!";
        } catch (PDOException $e) {
            $error_msg = "Error adding product: " . $e->getMessage();
        }
    }
}

// Fetch categories & product count for stats
$categories = [];
$all_products = [];
$total_orders = 0;
$total_inquiries = 0;

if (isset($pdo)) {
    try {
        $categories = $pdo->query("SELECT * FROM categories")->fetchAll();
        $all_products = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll();
        $total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
        $total_inquiries = $pdo->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();
    } catch (PDOException $e) {}
}
?>

<div class="container" style="margin: 40px auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 class="brand-font" style="color: var(--primary-color);">Admin Management Dashboard</h1>
            <p style="color: var(--text-muted);">Manage Velvet Vogue catalog items, categories, and customer data.</p>
        </div>
        <span class="badge-tag badge-gold" style="position: static; font-size: 0.9rem; padding: 6px 14px;">Logged in as Founder/Admin</span>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-success"><?php echo $msg; ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <!-- Overview Stat Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <div style="background: white; padding: 20px; border-radius: var(--radius-md); border: 1px solid var(--border-color); text-align: center;">
            <i class="fa-solid fa-shirt" style="font-size: 2rem; color: var(--primary-color); margin-bottom: 8px;"></i>
            <h3 style="font-size: 1.8rem;"><?php echo count($all_products); ?></h3>
            <p style="color: var(--text-muted); font-size: 0.85rem;">Total Catalog Items</p>
        </div>
        <div style="background: white; padding: 20px; border-radius: var(--radius-md); border: 1px solid var(--border-color); text-align: center;">
            <i class="fa-solid fa-tags" style="font-size: 2rem; color: var(--accent-gold); margin-bottom: 8px;"></i>
            <h3 style="font-size: 1.8rem;"><?php echo count($categories); ?></h3>
            <p style="color: var(--text-muted); font-size: 0.85rem;">Product Categories</p>
        </div>
        <div style="background: white; padding: 20px; border-radius: var(--radius-md); border: 1px solid var(--border-color); text-align: center;">
            <i class="fa-solid fa-cart-check" style="font-size: 2rem; color: #28a745; margin-bottom: 8px;"></i>
            <h3 style="font-size: 1.8rem;"><?php echo $total_orders; ?></h3>
            <p style="color: var(--text-muted); font-size: 0.85rem;">Orders Processed</p>
        </div>
        <div style="background: white; padding: 20px; border-radius: var(--radius-md); border: 1px solid var(--border-color); text-align: center;">
            <i class="fa-solid fa-envelope-open-text" style="font-size: 2rem; color: #17a2b8; margin-bottom: 8px;"></i>
            <h3 style="font-size: 1.8rem;"><?php echo $total_inquiries; ?></h3>
            <p style="color: var(--text-muted); font-size: 0.85rem;">Customer Inquiries</p>
        </div>
    </div>

    <div class="shop-layout">
        <!-- Add Product Form -->
        <div style="background: white; padding: 30px; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
            <h3 style="margin-bottom: 20px; border-bottom: 2px solid var(--primary-color); padding-bottom: 8px;">Add New Product to Showcase</h3>
            
            <form action="admin.php" method="POST" class="needs-validation">
                <div class="form-group">
                    <label for="title">Product Title *</label>
                    <input type="text" name="title" id="title" required class="form-control" placeholder="e.g. Velvet Night Evening Tuxedo">
                </div>

                <div class="form-grid-2col">
                    <div class="form-group">
                        <label for="category_id">Category *</label>
                        <select name="category_id" id="category_id" required class="form-control">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo sanitize($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="gender">Target Gender *</label>
                        <select name="gender" id="gender" class="form-control">
                            <option value="Men">Men</option>
                            <option value="Women">Women</option>
                            <option value="Unisex">Unisex</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="price">Price (LKR - Rs.) *</label>
                    <input type="number" step="0.01" name="price" id="price" required class="form-control" placeholder="12500.00">
                </div>

                <div class="form-group">
                    <label for="image_url">Image URL *</label>
                    <input type="url" name="image_url" id="image_url" required class="form-control" placeholder="https://images.unsplash.com/photo-...">
                </div>

                <div class="form-group">
                    <label for="description">Product Description</label>
                    <textarea name="description" id="description" rows="3" class="form-control" placeholder="Detailed garment specifications, fabric details, fit instructions..."></textarea>
                </div>

                <div class="form-grid-2col">
                    <div class="form-group">
                        <label for="sizes">Available Sizes</label>
                        <input type="text" name="sizes" id="sizes" class="form-control" value="S, M, L, XL">
                    </div>
                    <div class="form-group">
                        <label for="colors">Available Colors</label>
                        <input type="text" name="colors" id="colors" class="form-control" value="Black, Navy, Burgundy">
                    </div>
                </div>

                <div class="form-group" style="display: flex; gap: 20px;">
                    <label><input type="checkbox" name="is_featured" value="1"> Featured on Home</label>
                    <label><input type="checkbox" name="is_new" value="1" checked> Mark as New Arrival</label>
                </div>

                <button type="submit" name="add_product" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 10px;">
                    <i class="fa-solid fa-plus"></i> Publish Product
                </button>
            </form>
        </div>

        <!-- Inventory List -->
        <div style="background: white; padding: 30px; border-radius: var(--radius-md); border: 1px solid var(--border-color);" class="table-responsive">
            <h3 style="margin-bottom: 20px; border-bottom: 2px solid var(--border-color); padding-bottom: 8px;">Current Product Inventory</h3>
            
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Category</th>
                        <th>Price (LKR)</th>
                        <th>Gender</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_products as $p): ?>
                        <tr>
                            <td style="display: flex; align-items: center; gap: 10px;">
                                <img src="<?php echo sanitize($p['image_url']); ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                <strong><?php echo sanitize($p['title']); ?></strong>
                            </td>
                            <td><?php echo sanitize($p['category_name']); ?></td>
                            <td style="color: var(--primary-color); font-weight: 600;"><?php echo format_price($p['price']); ?></td>
                            <td><?php echo sanitize($p['gender']); ?></td>
                            <td>
                                <a href="product_detail.php?id=<?php echo $p['id']; ?>" class="btn btn-outline btn-sm" target="_blank">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
