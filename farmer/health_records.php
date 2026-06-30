<?php
session_start();
require_once '../database.php';

$conn = getConnection();
$farmer_id = $_SESSION['user_id'];

$sql = "
SELECT
    l.tag_number,
    hr.diagnosis,
    hr.treatment,
    hr.treatment_date
FROM health_records hr
JOIN livestock l ON hr.livestock_id = l.id
WHERE l.farmer_id = ?
ORDER BY hr.treatment_date DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $farmer_id);
$stmt->execute();

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Health Records</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<h1>Health Records</h1>

<table>

<tr>
    <th>Animal Tag</th>
    <th>Diagnosis</th>
    <th>Treatment</th>
    <th>Date</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>

<tr>
    <td><?= $row['tag_number'] ?></td>
    <td><?= $row['diagnosis'] ?></td>
    <td><?= $row['treatment'] ?></td>
    <td><?= $row['treatment_date'] ?></td>
</tr>

<?php endwhile; ?>

</table>

</body>
</html>