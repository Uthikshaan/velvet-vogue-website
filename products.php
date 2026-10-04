<?php
require_once 'header.php';

// Filter parameters from GET request
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$gender = isset($_GET['gender']) ? trim($_GET['gender']) : '';
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 60000;

// Fetch categories for sidebar filter
$categories = [];
if (isset($pdo)) {
    try {
        $categories = $pdo->query("SELECT * FROM categories")->fetchAll();
    } catch (PDOException $e) {}
}

// Build Dynamic SQL Query
$sql = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (p.title LIKE :search OR p.description LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

if ($category_id > 0) {
    $sql .= " AND p.category_id = :category_id";
    $params[':category_id'] = $category_id;
}

if (!empty($gender) && in_array($gender, ['Men', 'Women', 'Unisex'])) {
    $sql .= " AND p.gender = :gender";
    $params[':gender'] = $gender;
}

if ($max_price > 0) {
    $sql .= " AND p.price <= :max_price";
    $params[':max_price'] = $max_price;
}

$sql .= " ORDER BY p.id DESC";

$products = [];
if (isset($pdo)) {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error_msg = "Database query error: " . $e->getMessage();
    }
}
?>

<div class="container">
    <div style="margin: 30px 0 10px 0;">
        <h1 class="brand-font" style="font-size: 2.2rem; color: var(--primary-color);">
            <?php 
                if ($gender == 'Men') echo "Men's Fashion Collection";
                elseif ($gender == 'Women') echo "Women's Fashion Collection";
                else echo "Velvet Vogue Catalog";
            ?>
        </h1>
        <p style="color: var(--text-muted);">Explore our full range of trendy casualwear and tailored formal wear.</p>
    </div>

    <div class="shop-layout">
        <!-- Filter Sidebar -->
        <aside class="filter-sidebar">
            <h3 style="margin-bottom: 15px; border-bottom: 2px solid var(--primary-color); padding-bottom: 8px;">Filter Products</h3>
            
            <form action="products.php" method="GET">
                <!-- Search Input -->
                <div class="filter-group">
                    <h4>Search Keyword</h4>
                    <input type="text" name="search" class="form-control" placeholder="Search clothes..." value="<?php echo sanitize($search); ?>">
                </div>

                <!-- Gender Filter -->
                <div class="filter-group">
                    <h4>Gender Focus</h4>
                    <div class="filter-options">
                        <label>
                            <input type="radio" name="gender" value="" <?php echo (empty($gender)) ? 'checked' : ''; ?>> All Genders
                        </label>
                        <label>
                            <input type="radio" name="gender" value="Men" <?php echo ($gender == 'Men') ? 'checked' : ''; ?>> Men's Fashion
                        </label>
                        <label>
                            <input type="radio" name="gender" value="Women" <?php echo ($gender == 'Women') ? 'checked' : ''; ?>> Women's Fashion
                        </label>
                        <label>
                            <input type="radio" name="gender" value="Unisex" <?php echo ($gender == 'Unisex') ? 'checked' : ''; ?>> Unisex Style
                        </label>
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="filter-group">
                    <h4>Clothing Category</h4>
                    <select name="category" class="form-control">
                        <option value="0">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($category_id == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo sanitize($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Price Filter (LKR - Above Rs 3000) -->
                <div class="filter-group">
                    <h4>Max Price: <span id="priceVal">Rs. <?php echo number_format($max_price, 2); ?></span></h4>
                    <input type="range" name="max_price" min="3000" max="60000" step="1000" value="<?php echo $max_price; ?>" class="form-control" oninput="document.getElementById('priceVal').innerText = 'Rs. ' + parseFloat(this.value).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 10px;">Apply Filters</button>
                <a href="products.php" class="btn btn-outline btn-sm" style="display: block; text-align: center;">Reset Filters</a>
            </form>
        </aside>

        <!-- Product Grid Area -->
        <section>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <p style="color: var(--text-muted);">Showing <strong><?php echo count($products); ?></strong> item(s)</p>
            </div>

            <?php if (!empty($products)): ?>
                <div class="product-grid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-thumb">
                                <?php if ($product['is_featured']): ?>
                                    <span class="badge-tag">Featured</span>
                                <?php elseif ($product['is_new']): ?>
                                    <span class="badge-tag badge-gold">New</span>
                                <?php endif; ?>
                                <img src="<?php echo sanitize($product['image_url']); ?>" alt="<?php echo sanitize($product['title']); ?>">
                            </div>
                            <div class="product-details">
                                <span class="product-category"><?php echo sanitize($product['category_name']); ?> | <?php echo sanitize($product['gender']); ?></span>
                                <h3 class="product-title"><?php echo sanitize($product['title']); ?></h3>
                                <div class="product-price"><?php echo format_price($product['price']); ?></div>
                                <div class="product-actions">
                                    <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-primary btn-sm" style="width: 100%;">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="background: white; padding: 40px; text-align: center; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                    <i class="fa-solid fa-shirt" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 15px;"></i>
                    <h2>No items found matching your criteria</h2>
                    <p style="color: var(--text-muted); margin-bottom: 20px;">Try adjusting your filter settings or search term.</p>
                    <a href="products.php" class="btn btn-primary">View All Products</a>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>

<?php require_once 'footer.php'; ?>
