<?php
// ---------- php connect + create account logic ----------
require_once 'config.php';

$username = '';
$message  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $verify   = $_POST['verify_password'] ?? '';

    if ($username === '' || $password === '' || $verify === '') {
        $message = 'Please fill in all fields.';
    } elseif ($password !== $verify) {
        $message = 'Passwords do not match.';
    } else {
        // check if username already exists
        $stmt = $conn->prepare("SELECT id FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = 'That username is already taken. Please choose another.';
        } else {
            // hash password and insert
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO user (username, password) VALUES (?, ?)");
            $insert->bind_param("ss", $username, $hashed);

            if ($insert->execute()) {
                $message  = 'Account created successfully! You can log in now.';
                $username = ''; // clear field
            } else {
                $message = 'Error creating account. Please try again.';
            }
            $insert->close();
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Account - Naomi's Gadget Shop</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <h1>Naomi's Gadget Shop</h1>
    <nav class="main-nav">
        <ul>
            <li><a href="catalog.php">Products</a></li>
            <li><a href="index.php">Log In</a></li>
            <li><a href="create-account.php" class="active">Create Account</a></li>
        </ul>
    </nav>
</header>

<main>
    <section class="login-card">
        <h2>Create Account</h2>
        <p class="small-text">
            Create your customer account to shop Naomi-approved gadgets.
            Password must meet all rules below.
        </p>

        <?php if ($message !== ''): ?>
            <div class="feedback <?php echo (strpos($message, 'successfully') !== false) ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form id="createAccountForm" action="create-account.php" method="post">
            <div class="form-row">
                <label for="username">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    required
                    value="<?php echo htmlspecialchars($username); ?>"
                >
            </div>

            <div class="form-row">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                >
            </div>

            <div class="form-row">
                <label for="verify_password">Verify Password</label>
                <input
                    type="password"
                    id="verify_password"
                    name="verify_password"
                    required
                >
            </div>

            <ul class="password-rules" id="passwordRules">
                <li id="rule-length">At least 8 characters</li>
                <li id="rule-number">Contains at least one number</li>
                <li id="rule-match">Password and Verify Password match</li>
            </ul>

            <div class="form-row">
                <button id="createAccountBtn" type="submit" disabled>
                    Create Account
                </button>
                <button type="reset">Reset</button>
            </div>
        </form>
    </section>
</main>


<script>
// === validation that enables the button ===

const passwordInput = document.getElementById('password');
const verifyInput   = document.getElementById('verify_password');
const usernameInput = document.getElementById('username');
const createBtn     = document.getElementById('createAccountBtn');

// rule items
const ruleLength = document.getElementById('rule-length');
const ruleNumber = document.getElementById('rule-number');
const ruleMatch  = document.getElementById('rule-match');

// helper to toggle rule color
function setRule(li, ok) {
    if (!li) return;
    if (ok) li.classList.add('valid');
    else    li.classList.remove('valid');
}

function validateForm() {
    const username = usernameInput.value.trim();
    const pwd = passwordInput.value;
    const verify = verifyInput.value;

    // password rules
    const hasLength = pwd.length >= 8;
    const hasNumber = /\d/.test(pwd);
    const matches   = pwd !== "" && pwd === verify;

    // update UI
    setRule(ruleLength, hasLength);
    setRule(ruleNumber, hasNumber);
    setRule(ruleMatch, matches);

    // require username + all rules 
    const allGood = username !== "" && hasLength && hasNumber && matches;

    createBtn.disabled = !allGood;
}

usernameInput.addEventListener("input", validateForm);
passwordInput.addEventListener("input", validateForm);
verifyInput.addEventListener("input", validateForm);

// run once at page load
validateForm();
</script>

</body>
</html>
