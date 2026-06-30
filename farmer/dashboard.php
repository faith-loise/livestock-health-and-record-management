<?php
session_start();
require_once '../database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'farmer') {
    header("Location: ../login.php");
    exit();
}

$conn = getConnection();
$farmer_id = $_SESSION['user_id'];

/* Statistics */

// Total livestock
$result = $conn->query("SELECT COUNT(*) AS total FROM livestock WHERE farmer_id = $farmer_id");
$totalLivestock = $result->fetch_assoc()['total'];

// Pending vaccinations
$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM vaccination_schedules vs
    JOIN livestock l ON vs.livestock_id = l.id
    WHERE l.farmer_id = $farmer_id
    AND vs.status='Pending'
");
$pendingVaccinations = $result->fetch_assoc()['total'];

// Health records
$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM health_records hr
    JOIN livestock l ON hr.livestock_id = l.id
    WHERE l.farmer_id = $farmer_id
");
$totalHealthRecords = $result->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Farmer Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<div class="sidebar">
    <h2>LHVTS</h2>

    <a href="dashboard.php">Dashboard</a>
    <a href="livestock.php">My Livestock</a>
    <a href="vaccinations.php">Vaccinations</a>
    <a href="health_records.php">Health Records</a>
    <a href="../logout.php">Logout</a>
</div>

<div class="main">

    <h1>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?></h1>

    <div class="cards">

        <div class="card">
            <h3>Total Livestock</h3>
            <p><?= $totalLivestock ?></p>
        </div>

        <div class="card">
            <h3>Pending Vaccinations</h3>
            <p><?= $pendingVaccinations ?></p>
        </div>

        <div class="card">
            <h3>Health Records</h3>
            <p><?= $totalHealthRecords ?></p>
        </div>

    </div>

</div>

</body>
</html>