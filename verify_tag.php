<?php
// verify_tag.php
$servername = "localhost";
$username = "toll";
$password = "toll123";
$dbname = "fastag";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tag_id'])) {
    $tag_id = $_POST['tag_id'];

    // Check if the Tag ID is present in the vehicle_info table
    $sql = "SELECT tag_id FROM vehicle_info WHERE tag_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $tag_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["valid" => true]);
    } else {
        echo json_encode(["valid" => false]);
    }

    $stmt->close();
}

$conn->close();
?>
