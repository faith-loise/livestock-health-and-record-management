<?php
session_start();
require_once '../database.php';

$conn = getConnection();

// Ensure user is logged in as farmer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farmer') {
    header("Location: ../login.php");
    exit();
}

$farmer_id = $_SESSION['user_id'];

/* =========================
   DELETE LIVESTOCK (OPTIONAL)
========================= */
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    $stmt = $conn->prepare("
        DELETE FROM livestock
        WHERE id = ? AND farmer_id = ?
    ");
    $stmt->bind_param("ii", $delete_id, $farmer_id);
    $stmt->execute();

    header("Location: livestock.php");
    exit();
}

/* =========================
   FETCH LIVESTOCK
========================= */
$stmt = $conn->prepare("
    SELECT *
    FROM livestock
    WHERE farmer_id = ?
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Livestock</title>
    <link rel="stylesheet" href="../css/dashboard.css">

    <style>
        .top-bar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:20px;
        }

        .btn-add{
            background:#2e7d32;
            color:white;
            padding:10px 15px;
            text-decoration:none;
            border-radius:6px;
        }

        .btn-edit{
            background:#1976d2;
            color:white;
            padding:6px 10px;
            border-radius:5px;
            text-decoration:none;
            margin-right:5px;
        }

        .btn-delete{
            background:#d32f2f;
            color:white;
            padding:6px 10px;
            border-radius:5px;
            text-decoration:none;
        }

        table{
            width:100%;
            border-collapse:collapse;
            background:white;
        }

        table th{
            background:#2e7d32;
            color:white;
            padding:10px;
        }

        table td{
            padding:10px;
            border:1px solid #ddd;
            text-align:center;
        }
    </style>
</head>

<body>

<div class="main">

    <div class="top-bar">
        <h1>My Livestock</h1>

        <a href="add_livestock.php" class="btn-add">
            + Add Livestock
        </a>
    </div>

    <table>

        <tr>
            <th>Tag Number</th>
            <th>Name</th>
            <th>Breed</th>
            <th>Gender</th>
            <th>Date of Birth</th>
            <th>Actions</th>
        </tr>

        <?php if ($result->num_rows > 0): ?>

            <?php while ($row = $result->fetch_assoc()): ?>

                <tr>
                    <td><?= htmlspecialchars($row['tag_number']) ?></td>
                    <td><?= htmlspecialchars($row['animal_name']) ?></td>
                    <td><?= htmlspecialchars($row['breed']) ?></td>
                    <td><?= htmlspecialchars($row['gender']) ?></td>
                    <td><?= htmlspecialchars($row['date_of_birth']) ?></td>

                    <td>
                        <a class="btn-edit"
                           href="edit_livestock.php?id=<?= $row['id'] ?>">
                           Edit
                        </a>

                        <a class="btn-delete"
                           href="livestock.php?delete=<?= $row['id'] ?>"
                           onclick="return confirm('Are you sure you want to delete this animal?')">
                           Delete
                        </a>
                    </td>
                </tr>

            <?php endwhile; ?>

        <?php else: ?>

            <tr>
                <td colspan="6">No livestock found.</td>
            </tr>

        <?php endif; ?>

    </table>

</div>

</body>
</html>