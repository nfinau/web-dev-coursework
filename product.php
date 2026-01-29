<?php
require_once 'config.php';

$message = '';
$needLogin = false;

// which product?
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die('Invalid product.');
}

$stmt = $conn->prepare("SELECT id, name, description, image, price FROM product WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    die('Product not found.');
}

// handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $qty = (int)($_POST['quantity'] ?? 0);

    if (!isLoggedIn()) {
        $needLogin = true;
    } elseif ($qty <= 0) {
        $message = 'Please enter a quantity of 1 or more.';
    } else {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // if already in cart, increase; otherwise set
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] += $qty;
        } else {
            $_SESSION['cart'][$id] = $qty;
        }

        $message = 'Product added to your cart.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['name']); ?> - Naomi’s Gadget Shop</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1>Naomi’s Gadget Shop</h1>
    <?php renderNav(); ?>
</header>

<main>
    <section class="product-detail">
        <img src="img/<?php echo htmlspecialchars($product['image']); ?>"
             alt="<?php echo htmlspecialchars($product['name']); ?>">

        <div class="product-info">
            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
            <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

            <?php if ($message): ?>
                <p class="feedback <?php echo $needLogin ? 'error' : 'success'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </p>
            <?php endif; ?>

            <?php if ($needLogin): ?>
                <p class="feedback error">
                    You must <a href="index.php">log in</a> before adding items to your cart.
                </p>
            <?php endif; ?>

            <form method="post" action="">
                <label for="quantity">Quantity:</label>
                <input type="number" name="quantity" id="quantity" min="1" value="1" required>
                <button type="submit" name="add_to_cart">Add to Cart</button>
            </form>
        </div>
    </section>
</main>
</body>
</html>
