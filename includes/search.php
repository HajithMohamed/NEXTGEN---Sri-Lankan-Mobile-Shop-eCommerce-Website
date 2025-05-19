<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Search products based on search term and filters
 * 
 * @param string $search_term The search term to look for
 * @param int|null $category_id Optional category filter
 * @param string|null $brand Optional brand filter
 * @param float|null $min_price Optional minimum price filter
 * @param float|null $max_price Optional maximum price filter
 * @param string $sort_by Sort field (name, price, created_at)
 * @param string $sort_order Sort order (ASC, DESC)
 * @return array Array of matching products
 */
function searchProducts($search_term = '', $category_id = null, $brand = null, $min_price = null, $max_price = null, $sort_by = 'name', $sort_order = 'ASC') {
    $conn = getDBConnection();
    
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE 1=1";
    $params = array();
    
    // Add search term condition
    if (!empty($search_term)) {
        $query .= " AND (p.name LIKE ? OR p.brand LIKE ? OR p.description LIKE ?)";
        $search_param = "%" . $search_term . "%";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
    }
    
    // Add category filter
    if ($category_id !== null) {
        $query .= " AND p.category_id = ?";
        $params[] = $category_id;
    }
    
    // Add brand filter
    if ($brand !== null) {
        $query .= " AND p.brand = ?";
        $params[] = $brand;
    }
    
    // Add price range filters
    if ($min_price !== null) {
        $query .= " AND p.price >= ?";
        $params[] = $min_price;
    }
    if ($max_price !== null) {
        $query .= " AND p.price <= ?";
        $params[] = $max_price;
    }
    
    // Add sorting
    $allowed_sort_fields = array('name', 'price', 'created_at');
    $sort_by = in_array($sort_by, $allowed_sort_fields) ? $sort_by : 'name';
    $sort_order = strtoupper($sort_order) === 'DESC' ? 'DESC' : 'ASC';
    $query .= " ORDER BY p.$sort_by $sort_order";
    
    try {
        $stmt = $conn->prepare($query);
        if (!empty($params)) {
            $stmt->execute($params);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Search error: " . $e->getMessage());
        return array();
    }
}

/**
 * Get all unique brands from products
 * 
 * @return array Array of unique brands
 */
function getAllBrands() {
    $conn = getDBConnection();
    
    try {
        $stmt = $conn->query("SELECT DISTINCT brand FROM products ORDER BY brand ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        error_log("Error fetching brands: " . $e->getMessage());
        return array();
    }
}

/**
 * Get price range (min and max) from products
 * 
 * @return array Array with min and max prices
 */
function getPriceRange() {
    $conn = getDBConnection();
    
    try {
        $stmt = $conn->query("SELECT MIN(price) as min_price, MAX(price) as max_price FROM products");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching price range: " . $e->getMessage());
        return array('min_price' => 0, 'max_price' => 0);
    }
}

$current_page = basename($_SERVER['PHP_SELF']);
$show_search = in_array($current_page, ['index.php', 'products.php']); 