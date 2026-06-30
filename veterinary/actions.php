<?php
session_start();
require_once '../database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'vet') {
    header("Location: ../login.php");
    exit();
}

$conn = getConnection();
$vet_id = $_SESSION['user_id'];

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $livestock_id   = $_POST['livestock_id'];
    $diagnosis      = trim($_POST['diagnosis']);
    $treatment      = trim($_POST['treatment']);
    $treatment_date = $_POST['treatment_date'];
    $notes          = trim($_POST['notes']);

    if (empty($livestock_id)) $errors[] = "Please select an animal.";
    if (empty($diagnosis))    $errors[] = "Diagnosis is required.";
    if (empty($treatment))    $errors[] = "Treatment is required.";
    if (empty($treatment_date)) $errors[] = "Treatment date is required.";

    if (empty($errors)) {
        $stmt = $conn->prepare(
            "INSERT INTO health_records (livestock_id, vet_id, diagnosis, treatment, treatment_date, notes)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("iissss", $livestock_id, $vet_id, $diagnosis, $treatment, $treatment_date, $notes);

        if ($stmt->execute()) {
            $success = "Health record saved successfully.";
        } else {
            $errors[] = "Something went wrong. Please try again.";
        }
        $stmt->close();
    }
}

// Get all livestock for the dropdown
$livestock = $conn->query("SELECT id, tag_number, breed FROM livestock ORDER BY tag_number");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Record Health - LHVTS</title>
    <link rel="stylesheet" href="vet.css">
</head>
<body>

<div class="sidebar">
    <h2>LHVTS</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="actions.php" class="active">Record Health</a>
    <a href="completion.php">Complete Vaccinations</a>
    <a href="../logout.php">Logout</a>
</div>

<div class="main">
    <h1>Record Health</h1>

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

    <form method="POST" action="actions.php">

        <div class="form-group">
            <label for="livestock_id">Animal</label>
            <select id="livestock_id" name="livestock_id" required>
                <option value="" disabled selected>Select an animal</option>
                <?php while ($row = $livestock->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>">
                        <?= htmlspecialchars($row['tag_number']) ?> - <?= htmlspecialchars($row['breed']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="diagnosis">Diagnosis</label>
            <input type="text" id="diagnosis" name="diagnosis" placeholder="e.g. Mastitis" required>
        </div>

        <div class="form-group">
            <label for="treatment">Treatment</label>
            <textarea id="treatment" name="treatment" placeholder="Describe the treatment given" required></textarea>
        </div>

        <div class="form-group">
            <label for="treatment_date">Treatment Date</label>
            <input type="date" id="treatment_date" name="treatment_date" required>
        </div>

        <div class="form-group">
            <label for="notes">Notes (optional)</label>
            <textarea id="notes" name="notes" placeholder="Any additional notes"></textarea>
        </div>

        <button type="submit" class="btn-primary">Save Record</button>

    </form>
</div>

</body>
</html>