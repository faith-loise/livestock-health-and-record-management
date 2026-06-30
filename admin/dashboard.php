<?php
session_start();
require_once '../database.php';

$conn = getConnection();

// Allow only admins
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Statistics
$farmerQuery = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='farmer'");
$farmers = $farmerQuery->fetch_assoc()['total'];

$vetQuery = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='vet'");
$vets = $vetQuery->fetch_assoc()['total'];

$livestockQuery = $conn->query("SELECT COUNT(*) AS total FROM livestock");
$livestock = $livestockQuery->fetch_assoc()['total'];

$vaccinationQuery = $conn->query("
SELECT COUNT(*) AS total
FROM vaccination_schedules
WHERE status='Pending'
");
$pendingVaccinations = $vaccinationQuery->fetch_assoc()['total'];

$alertQuery = $conn->query("
SELECT COUNT(*) AS total
FROM disease_alerts
WHERE status='Active'
");
$alerts = $alertQuery->fetch_assoc()['total'];

$recentUsers = $conn->query("
SELECT full_name, role, created_at
FROM users
ORDER BY created_at DESC
LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

<div class="container">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">

        <?php include 'header.php'; ?>

        <h2>Dashboard</h2>

        <div class="cards">

            <div class="card">
                <h3>Farmers</h3>
                <p><?= $farmers ?></p>
            </div>

            <div class="card">
                <h3>Veterinary Officers</h3>
                <p><?= $vets ?></p>
            </div>

            <div class="card">
                <h3>Livestock</h3>
                <p><?= $livestock ?></p>
            </div>

            <div class="card">
                <h3>Pending Vaccinations</h3>
                <p><?= $pendingVaccinations ?></p>
            </div>

            <div class="card">
                <h3>Disease Alerts</h3>
                <p><?= $alerts ?></p>
            </div>

        </div>

        <div class="table-section">

            <h3>Recently Registered Users</h3>

            <table>

                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Date Registered</th>
                </tr>

                <?php while($row = $recentUsers->fetch_assoc()) { ?>

                <tr>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= ucfirst($row['role']) ?></td>
                    <td><?= $row['created_at'] ?></td>
                </tr>

                <?php } ?>

            </table>

        </div>

    </div>

</div>

</body>
</html>