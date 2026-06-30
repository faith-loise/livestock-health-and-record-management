<?php
session_start();
require_once '../database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'vet') {
    header("Location: ../login.php");
    exit();
}

$conn = getConnection();
$vet_id = $_SESSION['user_id'];

// Stats
$result = $conn->query("SELECT COUNT(*) AS total FROM health_records WHERE vet_id = $vet_id");
$totalRecords = $result->fetch_assoc()['total'];

$result = $conn->query("
    SELECT COUNT(*) AS total 
    FROM vaccination_schedules vs
    JOIN livestock l ON vs.livestock_id = l.id
    WHERE vs.status = 'Pending'
");
$pendingVax = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) AS total FROM livestock");
$totalLivestock = $result->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vet Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="sidebar">
    <h2>LHVTS</h2>
    <a href="dashboard.php" class="active">Dashboard</a>
    <a href="actions.php">Record Health</a>
    <a href="completion.php">Complete Vaccinations</a>
    <a href="../logout.php">Logout</a>
</div>

<div class="main">
    <h1>Welcome, Dr. <?= htmlspecialchars($_SESSION['full_name']) ?></h1>

    <div class="cards">
        <div class="card">
            <h3>Total Livestock (All Farms)</h3>
            <p><?= $totalLivestock ?></p>
        </div>
        <div class="card">
            <h3>Pending Vaccinations</h3>
            <p><?= $pendingVax ?></p>
        </div>
        <div class="card">
            <h3>Health Records Logged</h3>
            <p><?= $totalRecords ?></p>
        </div>
    </div>
    </div>
</div>

</body>
</html>