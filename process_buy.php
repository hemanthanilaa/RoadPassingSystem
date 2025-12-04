<?php

$servername = "localhost";
$username = "toll";
$password = "toll123";
$dbname = "fastag";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data
$contractor_id = $_POST['contractor_id'];
$route = $_POST['route'];
$toll_no = $_POST['toll'];

// Insert into contractor table
$stmt = $conn->prepare("INSERT INTO contractor (C_ID, Routeno, toll_no) VALUES (?, ?, ?)");
$stmt->bind_param("sis", $contractor_id, $route, $toll_no);

if ($stmt->execute()) {
    // Update contractor's ownership of the toll in all routes where it exists
    $stmt = $conn->prepare("
        INSERT INTO contractor (C_ID, Routeno, toll_no)
        SELECT ?, Routeno, ?
        FROM route_toll
        WHERE toll_no = ?
        ON DUPLICATE KEY UPDATE C_ID = VALUES(C_ID)
    ");
    $stmt->bind_param("sis", $contractor_id, $toll_no, $toll_no);
    $stmt->execute();
    // Prepare JavaScript for alert and redirection
    echo "<script>
            alert('Toll bought successfully!');
            window.location.href = 'new5.php?username=" . urlencode($contractor_id) . "';
          </script>";

} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
