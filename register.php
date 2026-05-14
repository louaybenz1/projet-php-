<?php
// register.php
// ============================================================
// New Client Registration Page
// Uses the Client class to register a new account.
// On success, redirects to login.php
// ============================================================

session_start();

// If someone is already logged in, send them to their dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: " . $_SESSION['role'] . "/dashboard.php");
    exit;
}

require_once 'classes/Client.php';

$error   = "";
$success = "";

// ============================================================
// Handle form submission (POST request)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Grab and sanitise form values
    // trim() removes extra spaces the user might have typed
    $full_name        = trim($_POST['full_name']       ?? '');
    $email            = trim($_POST['email']           ?? '');
    $password         = trim($_POST['password']        ?? '');
    $confirm_password = trim($_POST['confirm_password']?? '');
    $phone            = trim($_POST['phone']           ?? '');
    $age              = intval($_POST['age']           ?? 0);

    // --- Basic server-side validation ---
    // (JS validation runs first, but we NEVER trust the client alone)
    if (empty($full_name) || empty($email) || empty($password)) {
        $error = "Please fill in all required fields.";

    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";

    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";

    } elseif ($age < 15) {
        $error = "You must be at least 15 years old to register.";

    } else {
        // All checks passed — try to register
        $client = new Client();
        $result = $client->register($full_name, $email, $password, $phone, $age);

        if ($result) {
            // Registration succeeded
            $success = "Account created! You can now log in.";
        } else {
            // register() returns false when email is already taken
            $error = "This email is already registered. Try logging in.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — TEK-UP GYM</title>
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
                <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="login.php" class="nav-link">Login</a></li>
            </ul>
        </nav>
    </header>

    <main class="auth-main">
        <div class="auth-card">

            <h2 class="auth-title">CREATE YOUR ACCOUNT</h2>
            <p class="auth-subtitle">Join the TEK-UP GYM community today.</p>

            <!-- Show error or success messages from PHP -->
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-xmark"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($success) ?>
                    <br><a href="login.php" class="alert-link">Click here to login →</a>
                </div>
            <?php endif; ?>

            <!-- Registration Form -->
            <!-- The id="registerForm" is used by validation.js -->
            <form action="register.php" method="POST" class="auth-form" id="registerForm" novalidate>

                <div class="input-group">
                    <label for="full_name">Full Name <span class="required">*</span></label>
                    <input
                        type="text"
                        id="full_name"
                        name="full_name"
                        placeholder="e.g. Ahmed Ben Salah"
                        value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"
                        required
                    >
                    <!-- JS will inject error messages into this span -->
                    <span class="field-error" id="err-full_name"></span>
                </div>

                <div class="input-group">
                    <label for="email">Email Address <span class="required">*</span></label>
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

                <div class="input-row">
                    <div class="input-group">
                        <label for="password">Password <span class="required">*</span></label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Min. 6 characters"
                            required
                        >
                        <span class="field-error" id="err-password"></span>
                    </div>

                    <div class="input-group">
                        <label for="confirm_password">Confirm Password <span class="required">*</span></label>
                        <input
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            placeholder="Repeat password"
                            required
                        >
                        <span class="field-error" id="err-confirm_password"></span>
                    </div>
                </div>

                <div class="input-row">
                    <div class="input-group">
                        <label for="phone">Phone Number</label>
                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            placeholder="e.g. 55 466 877"
                            value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                        >
                    </div>

                    <div class="input-group">
                        <label for="age">Age <span class="required">*</span></label>
                        <input
                            type="number"
                            id="age"
                            name="age"
                            placeholder="e.g. 22"
                            min="15"
                            max="100"
                            value="<?= htmlspecialchars($_POST['age'] ?? '') ?>"
                            required
                        >
                        <span class="field-error" id="err-age"></span>
                    </div>
                </div>

                <div class="auth-buttons">
                    <button type="reset" class="btn-reset">
                        <i class="fa-solid fa-rotate-left"></i> Clear
                    </button>
                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-user-plus"></i> Create Account
                    </button>
                </div>

            </form>

            <p class="auth-footer">
                Already have an account? <a href="login.php" class="auth-link">Login here</a>
            </p>

        </div>
    </main>

    <!-- JS validation runs BEFORE the form is submitted -->
    <script src="js/validation.js"></script>
</body>
</html>
