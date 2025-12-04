<?php
$servername = "localhost";
$username = "toll";
$password = "toll123";
$dbname = "fastag";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch route details
$sql = "SELECT Routeno as Route_no, Route_name, S1, S2, S3, S4, S5, S6, S7, S8, S9, S10 FROM route";
$result = $conn->query($sql);

$routes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $routes[] = $row;
    }
}

// Fetch tolls related to routes
$sql_toll = "SELECT Routeno, stage FROM route_toll
             JOIN tolls ON route_toll.toll_no = tolls.toll_no";
$result_toll = $conn->query($sql_toll);

$tolls = [];
if ($result_toll->num_rows > 0) {
    while ($row = $result_toll->fetch_assoc()) {
        $tolls[$row['Routeno']][] = $row['stage'];
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode(['routes' => $routes, 'tolls' => $tolls]);
?>
