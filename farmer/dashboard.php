<?php
session_start();
require_once '../database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farmer') {
    header("Location: ../login.php");
    exit();
}

$conn = getConnection();
$farmer_id = $_SESSION['user_id'];

$r = $conn->query("SELECT COUNT(*) AS total FROM livestock WHERE farmer_id = $farmer_id");
$totalLivestock = $r->fetch_assoc()['total'];

$r = $conn->query("
    SELECT COUNT(*) AS total FROM vaccination_schedules vs
    JOIN livestock l ON vs.livestock_id = l.id
    WHERE l.farmer_id = $farmer_id AND vs.status = 'Pending'
");
$pendingVaccinations = $r->fetch_assoc()['total'];

$r = $conn->query("
    SELECT COUNT(*) AS total FROM disease_alerts da
    JOIN livestock l ON da.livestock_id = l.id
    WHERE l.farmer_id = $farmer_id AND da.status = 'Active'
");
$healthAlerts = $r->fetch_assoc()['total'];

$r = $conn->query("
    SELECT vs.vaccine_name, l.tag_number, vs.scheduled_date
    FROM vaccination_schedules vs
    JOIN livestock l ON vs.livestock_id = l.id
    WHERE l.farmer_id = $farmer_id AND vs.status = 'Pending'
      AND vs.scheduled_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    ORDER BY vs.scheduled_date ASC LIMIT 3
");
$upcoming = $r->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard – LHVTS</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { display: flex; min-height: 100vh; background: #f4f7f6; }

        .sidebar {
            width: 220px; background: #2e7d32; color: white;
            display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0; height: 100vh; padding: 25px 0;
        }
        .sidebar .brand { font-size: 1.4rem; font-weight: 700; text-align: center; padding: 0 20px 20px; border-bottom: 1px solid rgba(255,255,255,0.15); margin-bottom: 10px; }
        .sidebar a { color: rgba(255,255,255,0.85); text-decoration: none; padding: 11px 25px; display: block; font-size: 0.93rem; transition: 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.12); color: white; padding-left: 30px; }
        .sidebar .logout { margin-top: auto; border-top: 1px solid rgba(255,255,255,0.15); padding-top: 10px; }

        .main { margin-left: 220px; padding: 40px; flex: 1; }

        .page-header { margin-bottom: 25px; }
        .page-header h1 { font-size: 1.5rem; color: #1b5e20; }
        .page-header p  { color: #666; font-size: 0.93rem; margin-top: 4px; }

        .cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-bottom: 32px; }
        .card  { background: white; border-radius: 10px; padding: 22px; border-left: 4px solid #43a047; box-shadow: 0 1px 4px rgba(0,0,0,0.07); }
        .card h3 { font-size: 0.78rem; color: #999; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; }
        .card .number { font-size: 2rem; font-weight: 700; color: #2e7d32; }

        .section-label { font-size: 0.93rem; font-weight: 600; color: #555; margin-bottom: 12px; }
        .actions { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .action-card { background: white; border-radius: 10px; padding: 18px; text-decoration: none; color: #333; border: 1px solid #e8e8e8; transition: 0.2s; }
        .action-card:hover { border-color: #43a047; box-shadow: 0 2px 8px rgba(46,125,50,0.1); }
        .action-card h4 { font-size: 0.92rem; color: #2e7d32; margin-bottom: 4px; }
        .action-card p  { font-size: 0.82rem; color: #888; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand">LHVTS</div>
    <a href="dashboard.php" class="active">Dashboard</a>
    <a href="livestock.php">My Livestock</a>
    <a href="vaccinations.php">Vaccinations</a>
    <a href="health_records.php">Health Records</a>
    <div class="logout">
        <a href="../logout.php">Log Out</a>
    </div>
</div>

<div class="main">

    <div class="page-header">
        <h1>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?></h1>
        <p>Here's the current status of your herd.</p>
    </div>

    <?php if (!empty($upcoming)): ?>
        <div class="alert alert-warning" style="margin-bottom:24px;">
            <strong>Vaccination due soon:</strong>
            <?php foreach ($upcoming as $v): ?>
                <?= htmlspecialchars($v['vaccine_name']) ?> for <strong><?= htmlspecialchars($v['tag_number']) ?></strong>
                on <?= date('d M Y', strtotime($v['scheduled_date'])) ?>. &nbsp;
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="cards">
        <div class="card">
            <h3>Livestock Registered</h3>
            <div class="number"><?= $totalLivestock ?></div>
        </div>
        <div class="card">
            <h3>Vaccinations Due</h3>
            <div class="number"><?= $pendingVaccinations ?></div>
        </div>
        <div class="card">
            <h3>Health Alerts</h3>
            <div class="number"><?= $healthAlerts ?></div>
        </div>
    </div>

    <div class="section-label">Quick Actions</div>
    <div class="actions">
        <a href="livestock.php?action=register" class="action-card">
            <h4>Register Livestock</h4>
            <p>Add a new animal to your herd.</p>
        </a>
        <a href="vaccinations.php" class="action-card">
            <h4>Vaccination Schedule</h4>
            <p>See upcoming and past vaccinations.</p>
        </a>
        <a href="health_records.php" class="action-card">
            <h4>Health Records</h4>
            <p>View diagnoses and treatment history.</p>
        </a>
        <a href="livestock.php" class="action-card">
            <h4>Herd Health Status</h4>
            <p>Check the current condition of all animals.</p>
        </a>
    </div>

</div>
</body>
</html>