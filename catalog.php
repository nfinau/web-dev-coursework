<?php
require_once 'config.php';

// get all products
$result = $conn->query("SELECT id, name, image, price FROM product ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Catalog - Naomi’s Gadget Shop</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1>Naomi’s Gadget Shop</h1>
    <?php renderNav('catalog'); ?>
</header>

<main>
    <section class="catalog-intro">
        <h2>Naomi's Products</h2>
        <p>Browse the finest selection of Naomi-approved devices and contraptions.</p>
    </section>

    <section class="product-grid">
        <?php while ($row = $result->fetch_assoc()): ?>
            <article class="product-card">
                <img src="img/<?php echo htmlspecialchars($row['image']); ?>"
                     alt="<?php echo htmlspecialchars($row['name']); ?>">
                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                <p class="price">$<?php echo number_format($row['price'], 2); ?></p>
                <a class="btn" href="product.php?id=<?php echo $row['id']; ?>">View product details</a>
            </article>
        <?php endwhile; ?>
    </section>
</main>
</body>
</html>
