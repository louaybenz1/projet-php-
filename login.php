<?php
// login.php
// ============================================================
// Login Page
// Accepts email + password, verifies credentials,
// and redirects the user to the correct dashboard
// based on their role (client / coach / admin).
// ============================================================

session_start();

// If already logged in, redirect away
if (isset($_SESSION['user_id'])) {
    header("Location: " . $_SESSION['role'] . "/dashboard.php");
    exit;
}

require_once 'classes/User.php';

$error = "";

// ============================================================
// Handle POST: the login form was submitted
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = "Please enter your email and password.";

    } else {
        // Use the base User class to look up the account
        $userObj = new User();
        $user    = $userObj->findByEmail($email);

        // password_verify() checks the plain text against the hash
        // stored in the database — this is the correct, safe way.
        if ($user && password_verify($password, $user['password'])) {

            // ------------------------------------------------
            // Login successful — store user info in the session
            // The session is like a "memory" that persists
            // across pages until the user logs out.
            // ------------------------------------------------
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email']     = $user['email'];
            $_SESSION['role']      = $user['role'];

            // Redirect each role to their own dashboard
            // This keeps dashboards separate and secure.
            switch ($user['role']) {
                case 'admin':
                    header("Location: admin/dashboard.php");
                    break;
                case 'coach':
                    header("Location: coach/dashboard.php");
                    break;
                case 'client':
                default:
                    header("Location: client/dashboard.php");
                    break;
            }
            exit;

        } else {
            // Wrong email or wrong password
            // We use the same message for both — intentionally.
            // Telling an attacker WHICH one was wrong is a security risk.
            $error = "Incorrect email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — TEK-UP GYM</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="auth-page">

    <!-- Navbar -->
    <header>
        <nav class="navgym section-content">
            <a href="index.php" class="nav-logo">
                <h2 class="logo-text">💪gym</h2>
            </a>
            <ul class="nav-menu">
                <li class="nav-item"><a href="index.php"    class="nav-link">Home</a></li>
                <li class="nav-item"><a href="register.php" class="nav-link">Register</a></li>
            </ul>
        </nav>
    </header>

    <main class="auth-main">
        <div class="auth-card">

            <h2 class="auth-title">WELCOME BACK</h2>
            <p class="auth-subtitle">Log in to your TEK-UP GYM account.</p>

            <!-- PHP error message -->
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-xmark"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="login.php" method="POST" class="auth-form" id="loginForm" novalidate>

                <div class="input-group">
                    <label for="email">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="example@mail.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        required
                    >
                    <span class="field-error" id="err-email"></span>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Your password"
                        required
                    >
                    <span class="field-error" id="err-password"></span>
                </div>

                <div class="auth-buttons">
                    <button type="submit" class="btn-submit" style="flex: 1;">
                        <i class="fa-solid fa-right-to-bracket"></i> Login
                    </button>
                </div>

            </form>

            <!-- Quick login hint for testing — remove in production -->
            <div class="test-accounts">
                <p class="test-title">🧪 Test Accounts (password: <code>password</code>)</p>
                <div class="test-list">
                    <span>Admin: <code>admin@tekupgym.tn</code></span>
                    <span>Coach: <code>ahmed@tekupgym.tn</code></span>
                    <span>Client: <code>louay@mail.com</code></span>
                </div>
            </div>

            <p class="auth-footer">
                Don't have an account? <a href="register.php" class="auth-link">Register here</a>
            </p>

        </div>
    </main>

    <script src="js/validation.js"></script>
</body>
</html>
