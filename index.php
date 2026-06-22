<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LHVTS - Livestock Health & Vaccination Tracking System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="nav-inner">
        <span class="brand">LHVTS</span>
        <div class="nav-links">
            <a href="login.php">Login</a>
            <a href="signup.php" class="btn-primary">Sign Up</a>
        </div>
    </div>
</nav>

<!-- Hero -->
<section class="hero">
    <div class="hero-content">
        <h1>Livestock Health & Vaccination Tracking System</h1>
        <p>A centralised platform for small-scale dairy farmers and veterinary officers in Kenya to manage livestock health records, vaccination schedules, and disease alerts.</p>
        <div class="hero-buttons">
            <a href="signup.php" class="btn-primary">Get Started</a>
            <a href="login.php" class="btn-outline">Login</a>
        </div>
    </div>
</section>

<!-- Features -->
<section class="features">
    <div class="section-inner">
        <h2>What the System Offers</h2>
        <div class="features-grid">

            <div class="feature-card">
                <h3>Livestock Registration</h3>
                <p>Register animals with a unique tag number, breed, gender, and date of birth.</p>
            </div>

            <div class="feature-card">
                <h3>Health Records</h3>
                <p>Veterinary officers log diagnoses, treatments, and medication records per animal.</p>
            </div>

            <div class="feature-card">
                <h3>Vaccination Scheduling</h3>
                <p>Schedule vaccinations and receive automatic reminders before due dates.</p>
            </div>

            <div class="feature-card">
                <h3>Disease Alerts</h3>
                <p>Get notified when health data patterns suggest a potential disease outbreak.</p>
            </div>

            <div class="feature-card">
                <h3>Health Reports</h3>
                <p>View individual animal health history and herd-wide vaccination status.</p>
            </div>

            <div class="feature-card">
                <h3>Role-Based Access</h3>
                <p>Separate dashboards and access rights for farmers, veterinary officers, and administrators.</p>
            </div>

        </div>
    </div>
</section>

<!-- Who It's For -->
<section class="roles">
    <div class="section-inner">
        <h2>Who Is It For</h2>
        <div class="roles-grid">

            <div class="role-card">
                <h3>Farmers</h3>
                <p>Register livestock, view vaccination schedules, monitor health records, and receive disease alerts.</p>
            </div>

            <div class="role-card role-featured">
                <h3>Veterinary Officers</h3>
                <p>Update health records, schedule and confirm vaccinations, issue disease alerts, and monitor livestock health.</p>
            </div>

            <div class="role-card">
                <h3>Administrators</h3>
                <p>Manage user accounts, control access rights, and view system-wide reports.</p>
            </div>

        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="cta">
    <div class="section-inner">
        <h2>Ready to Get Started?</h2>
        <p>Create a free account today and start managing your livestock health records.</p>
        <a href="signup.php" class="btn-primary">Create an Account</a>
    </div>
</section>

<!-- Footer -->
<footer>
    <p>&copy; <?= date('Y') ?> LHVTS - Strathmore University</p>
</footer>

</body>
</html>