<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Handle add to cart from other pages
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    addToCart($productId, $quantity);
    header('Location: cart.php');
    exit();
}

$cart = getCart();
$cartItems = [];
$total = 0;

if (!empty($cart)) {
    $conn = getDBConnection();
    foreach ($cart as $productId => $quantity) {
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        
        if ($product) {
            $product['quantity'] = $quantity;
            $product['subtotal'] = $product['price'] * $quantity;
            $cartItems[] = $product;
            $total += $product['subtotal'];
        }
    }
}

// Handle quantity updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_quantity'])) {
        $productId = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];
        
        if ($quantity > 0) {
            updateCartItem($productId, $quantity);
        } else {
            removeFromCart($productId);
        }
        
        header('Location: cart.php');
        exit();
    } elseif (isset($_POST['remove_item'])) {
        $productId = (int)$_POST['product_id'];
        removeFromCart($productId);
        header('Location: cart.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - NEXTGEN</title>
    
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
        <h1 class="mb-4">Shopping Cart</h1>
        
        <?php if (empty($cartItems)): ?>
            <div class="alert alert-info">
                Your cart is empty. <a href="products.php">Continue shopping</a>
            </div>
        <?php else: ?>
            <div class="row">
                <!-- Cart Items -->
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <?php foreach ($cartItems as $item): ?>
                                <div class="cart-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                                 class="img-fluid rounded" 
                                                 alt="<?php echo htmlspecialchars($item['name']); ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <h5 class="mb-1">
                                                <a href="product.php?id=<?php echo $item['id']; ?>" class="text-decoration-none">
                                                    <?php echo htmlspecialchars($item['name']); ?>
                                                </a>
                                            </h5>
                                            <p class="text-muted mb-0"><?php echo htmlspecialchars($item['brand']); ?></p>
                                        </div>
                                        <div class="col-md-2">
                                            <p class="mb-0"><?php echo formatPrice($item['price']); ?></p>
                                        </div>
                                        <div class="col-md-2">
                                            <form method="POST" action="cart.php" class="d-flex align-items-center">
                                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                                <div class="input-group input-group-sm">
                                                    <button type="button" class="btn btn-outline-secondary" 
                                                            onclick="decrementQuantity(this)">-</button>
                                                    <input type="number" class="form-control text-center" 
                                                           name="quantity" value="<?php echo $item['quantity']; ?>" 
                                                           min="1" max="<?php echo $item['stock']; ?>">
                                                    <button type="button" class="btn btn-outline-secondary" 
                                                            onclick="incrementQuantity(this)">+</button>
                                                </div>
                                                <button type="submit" name="update_quantity" 
                                                        class="btn btn-link text-primary ms-2">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <p class="mb-0 fw-bold"><?php echo formatPrice($item['subtotal']); ?></p>
                                            <form method="POST" action="cart.php" class="d-inline">
                                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                                <button type="submit" name="remove_item" class="btn btn-link text-danger p-0">
                                                    <i class="fas fa-trash-alt"></i> Remove
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Order Summary</h5>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span><?php echo formatPrice($total); ?></span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping</span>
                                <span>Free</span>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-4">
                                <span class="fw-bold">Total</span>
                                <span class="fw-bold"><?php echo formatPrice($total); ?></span>
                            </div>
                            
                            <a href="checkout.php" class="btn btn-primary w-100">Proceed to Checkout</a>
                            
                            <div class="text-center mt-3">
                                <a href="products.php" class="text-decoration-none">
                                    <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
    
    <script>
        function incrementQuantity(button) {
            const input = button.parentElement.querySelector('input');
            const max = parseInt(input.getAttribute('max'));
            const value = parseInt(input.value);
            if (value < max) {
                input.value = value + 1;
            }
        }
        
        function decrementQuantity(button) {
            const input = button.parentElement.querySelector('input');
            const value = parseInt(input.value);
            if (value > 1) {
                input.value = value - 1;
            }
        }
    </script>
</body>
</html> 