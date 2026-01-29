<?php
// config.php
session_start();


// database
$host = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "database_name";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// helpers
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function currentUsername() {
    return $_SESSION['username'] ?? '';
}

function renderNav($active = '') {
    ?>
    <nav class="main-nav">
        <ul>
            <li><a href="catalog.php" class="<?php echo $active === 'catalog' ? 'active' : ''; ?>">Products</a></li>

            <?php if (!isLoggedIn()): ?>
                <li><a href="index.php" class="<?php echo $active === 'login' ? 'active' : ''; ?>">Log In</a></li>
                <li><a href="create-account.php" class="<?php echo $active === 'create' ? 'active' : ''; ?>">Create Account</a></li>
            <?php else: ?>
                <li><a href="cart.php" class="<?php echo $active === 'cart' ? 'active' : ''; ?>">Cart</a></li>
                <li><a href="logout.php">Log Out</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php
}
?>
