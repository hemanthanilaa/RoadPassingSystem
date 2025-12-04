<?php

$servername = "localhost";
$username = "toll";
$password = "toll123";
$dbname = "fastag";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$route_id = intval($_GET['route_id']);

$sql = "SELECT t.toll_no
        FROM tolls t
        JOIN route_toll rt ON t.toll_no = rt.toll_no
        WHERE rt.Routeno = ? AND NOT EXISTS (
            SELECT 1
            FROM contractor c
            WHERE c.toll_no = t.toll_no
              AND c.Routeno = ?
        )";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $route_id, $route_id);
$stmt->execute();
$result = $stmt->get_result();

$options = '';
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $options .= '<option value="' . $row['toll_no'] . '">Toll ' . $row['toll_no'] . '</option>';
    }
} else {
    $options .= '<option value="">No toll numbers available</option>';
}

$stmt->close();
$conn->close();

echo $options;
?>
