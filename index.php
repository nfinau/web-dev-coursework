<?php
require_once 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $message = 'Please enter both username and password.';
    } else {
        $stmt = $conn->prepare("SELECT id, password FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($uid, $hashedPassword);
            $stmt->fetch();

            if (password_verify($password, $hashedPassword)) {
                $_SESSION['user_id'] = $uid;
                $_SESSION['username'] = $username;
                header('Location: catalog.php');
                exit;
            } else {
                $message = 'Invalid username or password.';
            }
        } else {
            $message = 'Invalid username or password.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Naomi’s Gadget Shop - Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1>Naomi’s Gadget Shop </h1>
    <?php renderNav('login'); ?>
</header>

<main>
        <section class="about-card">
            <h2>Welcome to Naomi’s Gadget Shop</h2>
            <p>Shop fun, imaginative creations and delightfully quirky gadgets... perfect for adding a little sparkle to your everyday routine. ✨</p>
        </section>
		
    <?php if (isLoggedIn()): ?>
        <section class="welcome-card">
            <h2>Welcome back, <?php echo htmlspecialchars(currentUsername()); ?>!</h2>
            <p>Thanks for logging in. Visit our <a href="catalog.php">product catalog</a> to start shopping.</p>
        </section>
    <?php else: ?>
        <section class="login-card">
            <h2>Log In</h2>

            <?php if ($message): ?>
                <p class="feedback error"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-row">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-row">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-row">
                    <button type="submit" name="login">Log In</button>
                </div>
            </form>

            <p class="small-text">
                Don&apos;t have an account yet?
                <a href="create-account.php">Create one here.</a>
            </p>
        </section>
		
    <?php endif; ?>
</main>
</body>
</html>
