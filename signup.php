<?php
// signup.php
session_start();
require_once 'database.php';

$conn = getConnection();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize inputs
    $full_name    = trim(htmlspecialchars($_POST['full_name']));
    $email        = trim(htmlspecialchars($_POST['email']));
    $phone        = trim(htmlspecialchars($_POST['phone']));
    $role         = trim(htmlspecialchars($_POST['role']));
    $password     = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    // Validation
    if (empty($full_name))
        $errors[] = "Full name is required.";

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = "A valid email address is required.";

    if (empty($phone))
        $errors[] = "Phone number is required.";

    if (empty($role))
        $errors[] = "Please select a role.";

    // Only farmer and vet can register
    if (!in_array($role, ['farmer', 'vet']))
        $errors[] = "Invalid role selected.";

    if (strlen($password) < 8)
        $errors[] = "Password must be at least 8 characters.";

    if ($password !== $confirm_pass)
        $errors[] = "Passwords do not match.";

    if (empty($errors)) {

        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {

            $errors[] = "An account with this email already exists.";

        } else {

            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare(
                "INSERT INTO users (full_name, email, phone, role, password)
                 VALUES (?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sssss",
                $full_name,
                $email,
                $phone,
                $role,
                $hashed_password
            );

            if ($stmt->execute()) {
                $success = "Account created successfully. <a href='login.php'>Login here</a>.";
            } else {
                $errors[] = "Something went wrong. Please try again.";
            }
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up – LHVTS</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">

<div class="auth-container">

    <!-- Left Panel -->
    <div class="auth-panel auth-brand">
        <div class="brand-content">
            
            <h1 class="brand-title">Livestock Health &amp; Vaccination Tracking System</h1>
          
        </div>
    </div>

    <!-- Right Panel — Form -->
    <div class="auth-panel auth-form-panel">
        <div class="auth-form-wrapper">
            <h2 class="form-title">Create an Account</h2>
            <p class="form-subtitle">Register here</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST" action="signup.php" novalidate>

                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name"
                           placeholder="e.g. Jane Wambui"
                           value="<?= isset($full_name) ? htmlspecialchars($full_name) : '' ?>"
                           required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email"
                           placeholder="e.g. jane@example.com"
                           value="<?= isset($email) ? htmlspecialchars($email) : '' ?>"
                           required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone"
                           placeholder="e.g. 0712 345 678"
                           value="<?= isset($phone) ? htmlspecialchars($phone) : '' ?>"
                           required>
                </div>

                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="" disabled <?= !isset($role) ? 'selected' : '' ?>>Select your role</option>
                        <option value="farmer"    <?= (isset($role) && $role === 'farmer')    ? 'selected' : '' ?>>Farmer</option>
                        <option value="vet"       <?= (isset($role) && $role === 'vet')       ? 'selected' : '' ?>>Veterinary Officer</option>
                        
                    </select>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"
                           placeholder="Minimum 8 characters"
                           required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password"
                           placeholder="Re-enter your password"
                           required>
                </div>

                <button type="submit" class="btn-primary btn-full">Create Account</button>

            </form>

            <p class="auth-switch">Already have an account? <a href="login.php">Log in</a></p>
        </div>
    </div>

</div>

</body>
</html>