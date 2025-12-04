<?php
    $servername = "localhost";
    $username = "toll";
    $password = "toll123";
    $dbname = "fastag";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$prefix = $_GET['prefix'];
$sql = "SELECT username FROM users WHERE username LIKE ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$like = $prefix . '%';
$stmt->bind_param("s", $like);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $lastUsername = $row['username'];
    $number = (int)substr($lastUsername, 1);
    $newNumber = $number + 1;
    echo $prefix . $newNumber;
} else {
    echo $prefix . '1';
}

$stmt->close();
$conn->close();
?>
