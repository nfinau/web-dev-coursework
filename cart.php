<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$cart = $_SESSION['cart'] ?? [];

// handle updates / place order
$orderedItems = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['qty'] as $pid => $qty) {
            $qty = (int)$qty;
            if ($qty <= 0) {
                unset($cart[$pid]);
            } else {
                $cart[$pid] = $qty;
            }
        }
        $_SESSION['cart'] = $cart;
    } elseif (isset($_POST['place_order'])) {
        // build ordered list before clearing cart
        $orderedItems = $cart;
        $_SESSION['cart'] = [];
        $cart = [];
    }
}

// if there are items, pull product details
$products = [];
$total = 0;

if (!empty($cart)) {
    $ids = implode(',', array_map('intval', array_keys($cart)));
    $result = $conn->query("SELECT id, name, price FROM product WHERE id IN ($ids)");

    while ($row = $result->fetch_assoc()) {
        $pid = $row['id'];
        $row['quantity'] = $cart[$pid];
        $row['line_total'] = $row['price'] * $row['quantity'];
        $products[] = $row;
        $total += $row['line_total'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart - Naomi’s Gadget Shop</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1>Naomi’s Gadget Shop Catalog</h1>
    <?php renderNav('cart'); ?>
</header>

<main>
    <section class="cart-section">
        <h2>Your Shopping Cart</h2>

        <?php if ($orderedItems !== null): ?>
            <p class="feedback success">Thank you for your order!</p>
            <?php if (!empty($orderedItems)): ?>
                <h3>Items ordered:</h3>
                <ul>
                    <?php
                    $ids = implode(',', array_map('intval', array_keys($orderedItems)));
                    $res = $conn->query("SELECT id, name FROM product WHERE id IN ($ids)");
                    while ($row = $res->fetch_assoc()):
                        $pid = $row['id'];
                        $qty = $orderedItems[$pid];
                    ?>
                        <li><?php echo htmlspecialchars($row['name']); ?> (x<?php echo $qty; ?>)</li>
                    <?php endwhile; ?>
                </ul>
            <?php endif; ?>
            <p>Your cart is now empty.</p>

        <?php elseif (empty($products)): ?>
            <p>Your cart is empty.</p>

        <?php else: ?>
            <form method="post" action="">
                <table class="cart-table">
                    <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price / unit</th>
                        <th>Quantity</th>
                        <th>Line total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($p['name']); ?></td>
                            <td>$<?php echo number_format($p['price'], 2); ?></td>
                            <td>
                                <input type="number"
                                       name="qty[<?php echo $p['id']; ?>]"
                                       min="0"
                                       value="<?php echo $p['quantity']; ?>">
                            </td>
                            <td>$<?php echo number_format($p['line_total'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="3" class="total-label">Total:</td>
                        <td class="total-value">$<?php echo number_format($total, 2); ?></td>
                    </tr>
                    </tfoot>
                </table>

                <div class="cart-buttons">
                    <button type="submit" name="update_cart">Update Cart</button>
                    <button type="submit" name="place_order">Place Order</button>
                </div>
            </form>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
