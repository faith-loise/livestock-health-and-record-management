<?php
session_start();
require_once '../database.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'vet') {
    header("Location: ../login.php");
    exit();
}

$conn = getConnection();

// Mark a vaccination as completed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vaccination_id'])) {
    $vaccination_id = $_POST['vaccination_id'];
    $today = date('Y-m-d');

    $stmt = $conn->prepare(
        "UPDATE vaccination_schedules SET status = 'Completed', administered_date = ? WHERE id = ?"
    );
    $stmt->bind_param("si", $today, $vaccination_id);
    $stmt->execute();
    $stmt->close();

    header("Location: completion.php");
    exit();
}

// Get all pending vaccinations
$pending = $conn->query("
    SELECT vs.id, vs.vaccine_name, vs.scheduled_date, l.tag_number, l.breed
    FROM vaccination_schedules vs
    JOIN livestock l ON vs.livestock_id = l.id
    WHERE vs.status = 'Pending'
    ORDER BY vs.scheduled_date ASC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Complete Vaccinations - LHVTS</title>
    <link rel="stylesheet" href="vet.css">
</head>
<body>

<div class="sidebar">
    <h2>LHVTS</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="actions.php">Record Health</a>
    <a href="completion.php" class="active">Complete Vaccinations</a>
    <a href="../logout.php">Logout</a>
</div>

<div class="main">
    <h1>Pending Vaccinations</h1>

    <table>
        <tr>
            <th>Tag Number</th>
            <th>Breed</th>
            <th>Vaccine</th>
            <th>Scheduled Date</th>
            <th>Action</th>
        </tr>

        <?php if ($pending->num_rows === 0): ?>
            <tr><td colspan="5">No pending vaccinations.</td></tr>
        <?php endif; ?>

        <?php while ($row = $pending->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['tag_number']) ?></td>
                <td><?= htmlspecialchars($row['breed']) ?></td>
                <td><?= htmlspecialchars($row['vaccine_name']) ?></td>
                <td><?= htmlspecialchars($row['scheduled_date']) ?></td>
                <td>
                    <form method="POST" action="completion.php">
                        <input type="hidden" name="vaccination_id" value="<?= $row['id'] ?>">
                        <button type="submit" class="btn-primary">Mark Completed</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>