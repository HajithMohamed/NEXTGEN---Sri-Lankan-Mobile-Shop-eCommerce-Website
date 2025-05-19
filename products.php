<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get filters
$categoriesSelected = isset($_GET['category']) ? (array)$_GET['category'] : [];
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : null;
$minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
$maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;
$sort = isset($_GET['sort']) ? sanitizeInput($_GET['sort']) : 'newest';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

// Get products
$conn = getDBConnection();
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE 1=1";
$params = [];

if (!empty($categoriesSelected)) {
    $placeholders = implode(',', array_fill(0, count($categoriesSelected), '?'));
    $sql .= " AND c.slug IN ($placeholders)";
    foreach ($categoriesSelected as $catSlug) {
        $params[] = sanitizeInput($catSlug);
    }
}

if ($search) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($minPrice !== null) {
    $sql .= " AND p.price >= ?";
    $params[] = $minPrice;
}

if ($maxPrice !== null) {
    $sql .= " AND p.price <= ?";
    $params[] = $maxPrice;
}

// Get total count for pagination
$countSql = str_replace("p.*, c.name as category_name", "COUNT(*) as total", $sql);
$stmt = $conn->prepare($countSql);
$stmt->execute($params);
$totalProducts = $stmt->fetch()['total'];
$totalPages = ceil($totalProducts / $perPage);

// Auto-insert demo products if none exist
$productCount = $conn->query('SELECT COUNT(*) FROM products')->fetchColumn();
if ($productCount == 0) {
    // Get all categories
    $allCats = $conn->query('SELECT * FROM categories')->fetchAll();
    $demoProducts = [
        ['iPhone 14 Pro', 420000, 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=400&q=80'],
        ['Samsung Galaxy S23', 350000, 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=400&q=80'],
        ['Xiaomi 13 Pro', 220000, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=400&q=80'],
        ['Oppo Reno 8', 180000, 'https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=400&q=80'],
        // Add more demo products as needed
    ];
    $i = 0;
    foreach ($allCats as $cat) {
        foreach ($demoProducts as $demo) {
            $stmt = $conn->prepare('INSERT INTO products (name, price, image, category_id, stock, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
            $stmt->execute([
                $demo[0],
                $demo[1],
                $demo[2],
                $cat['id'],
                rand(5, 20)
            ]);
            $i++;
            if ($i >= 20) break 2; // Insert up to 20 demo products
        }
    }
    // Reload to show new products
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit();
}

// Add sorting
switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY p.price DESC";
        break;
    case 'name_asc':
        $sql .= " ORDER BY p.name ASC";
        break;
    case 'name_desc':
        $sql .= " ORDER BY p.name DESC";
        break;
    default:
        $sql .= " ORDER BY p.created_at DESC";
}

// Add pagination
$sql .= " LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;

$stmt = $conn->prepare($sql);
// Bind all params except the last two (LIMIT, OFFSET)
$paramIndex = 1;
for ($i = 0; $i < count($params) - 2; $i++, $paramIndex++) {
    $stmt->bindValue($paramIndex, $params[$i]);
}
// Bind LIMIT and OFFSET as integers
$stmt->bindValue($paramIndex++, (int)$params[count($params) - 2], PDO::PARAM_INT);
$stmt->bindValue($paramIndex, (int)$params[count($params) - 1], PDO::PARAM_INT);

$stmt->execute();
$products = $stmt->fetchAll();

// Get categories for filter
$stmt = $conn->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();

function buildQuery($params, $overrides = []) {
    $query = array_merge($params, $overrides);
    return http_build_query($query);
}
$currentParams = $_GET;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - NEXTGEN</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <div class="container py-5">
        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-md-3">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Filters</h5>
                        
                        <form action="products.php" method="GET" id="filterForm">
                            <!-- Search -->
                            <div class="mb-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" value="<?php echo $search; ?>">
                            </div>
                            
                            <!-- Categories -->
                            <div class="mb-3">
                                <label class="form-label">Categories</label>
                                <div class="form-check d-flex align-items-center mb-1">
                                    <input class="form-check-input me-2" type="checkbox" name="category[]" id="category_all" value="all" <?php if (empty($categoriesSelected)) echo 'checked'; ?>>
                                    <label class="form-check-label d-flex align-items-center" for="category_all">
                                        <i class="bi bi-grid me-2" style="color: var(--primary-color);"></i>All
                                    </label>
                                </div>
                                <?php foreach ($categories as $cat): ?>
                                    <?php if (in_array($cat['slug'], ['smartphones', 'tablets', 'accessories', 'wearables'])): ?>
                                    <div class="form-check d-flex align-items-center mb-1">
                                        <input class="form-check-input me-2" type="checkbox" name="category[]" 
                                               id="category_<?php echo $cat['id']; ?>" 
                                               value="<?php echo $cat['slug']; ?>"
                                               <?php echo in_array($cat['slug'], $categoriesSelected) ? 'checked' : ''; ?>>
                                        <label class="form-check-label d-flex align-items-center" for="category_<?php echo $cat['id']; ?>">
                                            <i class="bi bi-phone-fill me-2" style="color: var(--primary-color);"></i>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </label>
                                    </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Price Range -->
                            <div class="mb-3">
                                <label class="form-label">Price Range</label>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="number" class="form-control" name="min_price" 
                                               placeholder="Min" value="<?php echo $minPrice; ?>">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" class="form-control" name="max_price" 
                                               placeholder="Max" value="<?php echo $maxPrice; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sort -->
                            <div class="mb-3">
                                <label for="sort" class="form-label">Sort By</label>
                                <select class="form-select" id="sort" name="sort">
                                    <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest</option>
                                    <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                                    <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                                    <option value="name_asc" <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>>Name: A to Z</option>
                                    <option value="name_desc" <?php echo $sort === 'name_desc' ? 'selected' : ''; ?>>Name: Z to A</option>
                                </select>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-fill">Apply Filters</button>
                                <a href="products.php" class="btn btn-outline-secondary flex-fill">Clear Filters</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Products Grid -->
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Products</h2>
                    <div class="text-muted">
                        Showing <?php echo count($products); ?> of <?php echo $totalProducts; ?> products
                    </div>
                </div>
                
                <div class="row" id="productGrid">
                    <?php foreach ($products as $product): ?>
                        <?php
                        $isNew = isset($product['created_at']) && (strtotime($product['created_at']) > strtotime('-30 days'));
                        $isBestSeller = isset($product['sales']) && $product['sales'] > 10;
                        $isLowStock = isset($product['stock']) && $product['stock'] <= 5;
                        ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 position-relative text-center p-3 border-0 shadow-sm">
                                <div class="position-absolute top-0 start-0 m-2" style="z-index:2;">
                                    <?php if ($isNew): ?>
                                        <span class="badge bg-success">New</span>
                                    <?php endif; ?>
                                    <?php if ($isBestSeller): ?>
                                        <span class="badge bg-warning text-dark">Best Seller</span>
                                    <?php endif; ?>
                                    <?php if ($isLowStock): ?>
                                        <span class="badge bg-danger">Low Stock</span>
                                    <?php endif; ?>
                                </div>
                                <img src="<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top mb-3 img-fluid rounded" alt="<?php echo htmlspecialchars($product['name']); ?>" style="height:180px;object-fit:cover;">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="card-text text-muted"><?php echo htmlspecialchars($product['category_name']); ?></p>
                                    <p class="card-text product-price text-primary fw-bold" style="font-size:1.5rem;">
                                        <?php echo formatPrice($product['price']); ?>
                                    </p>
                                    <p class="mb-2">
                                        <?php if (isset($product['stock']) && $product['stock'] > 5): ?>
                                            <span class="text-success">In Stock</span>
                                        <?php elseif (isset($product['stock']) && $product['stock'] > 0): ?>
                                            <span class="text-danger">Only <?php echo $product['stock']; ?> left!</span>
                                        <?php else: ?>
                                            <span class="text-danger">Out of Stock</span>
                                        <?php endif; ?>
                                    </p>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary btn-sm">View Details</a>
                                        <?php if (!isset($product['stock']) || $product['stock'] > 0): ?>
                                        <form method="post" action="cart.php" class="d-inline">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" name="add_to_cart" class="btn btn-primary btn-sm"><i class="fas fa-cart-plus"></i></button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (count($products) === 0): ?>
                        <div class="col-12">
                            <div class="alert alert-warning text-center">
                                No products found for your filters.
                                <a href="products.php" class="btn btn-link">Reset Filters</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo buildQuery($currentParams, ['page' => $page - 1]); ?>">
                                        Previous
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?<?php echo buildQuery($currentParams, ['page' => $i]); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo buildQuery($currentParams, ['page' => $page + 1]); ?>">
                                        Next
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
</body>
</html> 