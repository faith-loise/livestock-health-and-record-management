<?php
session_start();
require_once '../database.php';

$conn = getConnection();

// Ensure only farmer can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farmer') {
    header("Location: ../login.php");
    exit();
}

$farmer_id = $_SESSION['user_id'];

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get and sanitize input
    $tag_number    = trim($_POST['tag_number']);
    $animal_name   = trim($_POST['animal_name']);
    $breed         = trim($_POST['breed']);
    $gender        = $_POST['gender'] ?? '';
    $date_of_birth = $_POST['date_of_birth'];

    // Validation
    if (empty($tag_number)) {
        $errors[] = "Tag number is required.";
    }

    if (empty($breed)) {
        $errors[] = "Breed is required.";
    }

    if (!in_array($gender, ['Male', 'Female'])) {
        $errors[] = "Please select a valid gender.";
    }

    if (empty($date_of_birth)) {
        $errors[] = "Date of birth is required.";
    }

    if (empty($errors)) {

        // Check duplicate tag number (global unique)
        $check = $conn->prepare("
            SELECT id FROM livestock WHERE tag_number = ?
        ");
        $check->bind_param("s", $tag_number);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $errors[] = "Tag number already exists. Use a different one.";
        } else {

            // Insert livestock
            $stmt = $conn->prepare("
                INSERT INTO livestock
                (farmer_id, tag_number, animal_name, breed, gender, date_of_birth)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "isssss",
                $farmer_id,
                $tag_number,
                $animal_name,
                $breed,
                $gender,
                $date_of_birth
            );

            if ($stmt->execute()) {
                $success = "Livestock added successfully!";
            } else {
                $errors[] = "Something went wrong. Please try again.";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Livestock</title>
    <link rel="stylesheet" href="../css/dashboard.css">

    <style>
        .form-container{
            max-width:600px;
            margin:30px auto;
            background:white;
            padding:25px;
            border-radius:10px;
            box-shadow:0 2px 8px rgba(0,0,0,0.1);
        }

        .form-group{
            margin-bottom:15px;
        }

        label{
            display:block;
            margin-bottom:6px;
            font-weight:bold;
        }

        input, select{
            width:100%;
            padding:10px;
            border:1px solid #ccc;
            border-radius:6px;
        }

        button{
            background:#2e7d32;
            color:white;
            padding:12px;
            border:none;
            width:100%;
            border-radius:6px;
            font-size:16px;
            cursor:pointer;
        }

        button:hover{
            background:#1b5e20;
        }

        .msg-error{
            background:#fdecea;
            color:#c62828;
            padding:10px;
            border-radius:6px;
            margin-bottom:10px;
        }

        .msg-success{
            background:#e8f5e9;
            color:#2e7d32;
            padding:10px;
            border-radius:6px;
            margin-bottom:10px;
        }

        a{
            display:block;
            margin-top:15px;
            text-align:center;
            color:#2e7d32;
            text-decoration:none;
        }
    </style>
</head>

<body>

<div class="form-container">

    <h2>Add New Livestock</h2>

    <!-- SUCCESS MESSAGE -->
    <?php if ($success): ?>
        <div class="msg-success"><?= $success ?></div>
    <?php endif; ?>

    <!-- ERROR MESSAGES -->
    <?php if (!empty($errors)): ?>
        <div class="msg-error">
            <?php foreach ($errors as $err): ?>
                <p><?= htmlspecialchars($err) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <div class="form-group">
            <label>Tag Number</label>
            <input type="text" name="tag_number" required>
        </div>

        <div class="form-group">
            <label>Animal Name (Optional)</label>
            <input type="text" name="animal_name">
        </div>

        <div class="form-group">
            <label>Breed</label>
            <input type="text" name="breed" required>
        </div>

        <div class="form-group">
            <label>Gender</label>
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>

        <div class="form-group">
            <label>Date of Birth</label>
            <input type="date" name="date_of_birth" required>
        </div>

        <button type="submit">Save Livestock</button>

    </form>

    <a href="livestock.php">← Back to Livestock</a>

</div>

</body>
</html>