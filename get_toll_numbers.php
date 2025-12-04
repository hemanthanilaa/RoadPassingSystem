<?php

$servername = "localhost";
$username = "toll";
$password = "toll123";
$dbname = "fastag";


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT toll_no FROM tolls";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    
    $tollNumbers = array();
    while ($row = $result->fetch_assoc()) {
        $tollNumbers[] = $row;
    }
 
    echo json_encode($tollNumbers);
} else {
    echo "0 results";
}

$conn->close();
?>
