<?php
// login.php
session_start();
require_once 'database.php';

$conn = getConnection();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim(htmlspecialchars($_POST['email']));
    $password = $_POST['password'];

    // Basic validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if (empty($password)) {
        $errors[] = "Please enter your password.";
    }

    if (empty($errors)) {
        // Fetch user by email
        $stmt = $conn->prepare("SELECT id, full_name, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                // Set session variables
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email']     = $user['email'];
                $_SESSION['role']      = $user['role'];

                // Redirect based on role
                switch ($user['role']) {
                    case 'admin':
                        header("Location: admin/dashboard.php");
                        break;

                    case 'vet':
                        header("Location: vet/dashboard.php");
                        break;

                    case 'farmer':
                    default:
                        header("Location: farmer/dashboard.php");
                        break;
                }

                exit();
            } else {
                $errors[] = "Incorrect email or password.";
            }
        } else {
            $errors[] = "Incorrect email or password.";
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
    <title>Login – LHVTS</title>
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

    <!-- Right Panel -->
    <div class="auth-panel auth-form-panel">
        <div class="auth-form-wrapper">

            <h2 class="form-title">Welcome Back</h2>
            <p class="form-subtitle">
                Log in to access your livestock health dashboard.
            </p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" novalidate>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="e.g. jane@example.com"
                        value="<?= isset($email) ? htmlspecialchars($email) : '' ?>"
                        required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required>
                </div>

                <button type="submit" class="btn-primary btn-full">
                    Log In
                </button>

            </form>

            <p class="auth-switch">
                Don't have an account?
                <a href="signup.php">Sign up</a>
            </p>

        </div>
    </div>

</div>

</body>
</html>