<?php
session_start();
require_once '../database.php';

$conn = getConnection();
$farmer_id = $_SESSION['user_id'];

$sql = "
SELECT
    l.tag_number,
    vs.vaccine_name,
    vs.scheduled_date,
    vs.status
FROM vaccination_schedules vs
JOIN livestock l ON vs.livestock_id = l.id
WHERE l.farmer_id = ?
ORDER BY vs.scheduled_date ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $farmer_id);
$stmt->execute();

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vaccinations</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<h1>Vaccination Schedule</h1>

<table>
<tr>
    <th>Animal Tag</th>
    <th>Vaccine</th>
    <th>Date</th>
    <th>Status</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>

<tr>
    <td><?= $row['tag_number'] ?></td>
    <td><?= $row['vaccine_name'] ?></td>
    <td><?= $row['scheduled_date'] ?></td>
    <td><?= $row['status'] ?></td>
</tr>

<?php endwhile; ?>

</table>

</body>
</html>