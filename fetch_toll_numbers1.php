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

// Fetch route details
$sql = "SELECT Route_name, S1, S2, S3, S4, S5, S6, S7, S8, S9, S10 FROM route WHERE Routeno = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $route_id);
$stmt->execute();
$result = $stmt->get_result();
$route = $result->fetch_assoc();

$stages = [];
for ($i = 1; $i <= 10; $i++) {
    if (!empty($route["S$i"])) {
        $stages[] = $route["S$i"];
    }
}

// Fetch toll stages related to the route
$toll_sql = "SELECT stage FROM route_toll
             JOIN tolls ON route_toll.toll_no = tolls.toll_no
             WHERE Routeno = ?";
$toll_stmt = $conn->prepare($toll_sql);
$toll_stmt->bind_param("i", $route_id);
$toll_stmt->execute();
$toll_result = $toll_stmt->get_result();

$toll_stages = [];
while ($toll_row = $toll_result->fetch_assoc()) {
    $toll_stages[] = $toll_row['stage'];
}

echo '<label for="stages">Stages:</label>';
foreach ($stages as $stage) {
    $class = in_array($stage, $toll_stages) ? 'toll-stage' : 'no-toll-stage';
    echo '<div class="' . $class . '">' . $stage . '</div>';
}

$stmt->close();
$toll_stmt->close();
$conn->close();
?>
