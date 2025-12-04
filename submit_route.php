<?php
$servername = "localhost";
$username = "toll";
$password = "toll123";
$dbname = "fastag";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stage = $_POST['stages'];

// Get the latest toll_no from the tolls table
$sql = "SELECT toll_no FROM tolls ORDER BY toll_no DESC LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $last_toll_no = $row['toll_no'];
    $new_toll_no = $last_toll_no + 1;
} else {
    $new_toll_no = 100; // Default starting value if no records are found
}

// Insert new toll record into the tolls table if it doesn't already exist
$sql = "SELECT toll_no FROM tolls WHERE stage = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $stage);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Toll already exists, get the existing toll_no
    $stmt->bind_result($new_toll_no);
    $stmt->fetch();
    $stmt->close();
} else {
    // Insert new toll record
    $stmt->close();
    $sql = "INSERT INTO tolls (toll_no, stage) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $new_toll_no, $stage);

    if (!$stmt->execute()) {
        echo "Error inserting into tolls: " . $stmt->error;
        $stmt->close();
        $conn->close();
        exit();
    }

    $stmt->close();
}

// Find all routes that contain the specified stage
$sql = "
    SELECT Routeno
    FROM route
    WHERE S1 = ? OR S2 = ? OR S3 = ? OR S4 = ? OR S5 = ? 
       OR S6 = ? OR S7 = ? OR S8 = ? OR S9 = ? OR S10 = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssss", $stage, $stage, $stage, $stage, $stage, $stage, $stage, $stage, $stage, $stage);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($route_no);

$inserted_routes = [];
while ($stmt->fetch()) {
    // Insert new route-toll record into the route_toll table
    $sql = "INSERT INTO route_toll (toll_no, Routeno) VALUES (?, ?)";
    $stmt2 = $conn->prepare($sql);
    $stmt2->bind_param("ii", $new_toll_no, $route_no);

    if ($stmt2->execute()) {
        $inserted_routes[] = $route_no;
    } else {
        echo "Error inserting into route_toll for Routeno $route_no: " . $stmt2->error;
    }

    $stmt2->close();
}

$stmt->close();
$conn->close();

if (!empty($inserted_routes)) {
    echo '<script>
            alert("New toll established successfully");
            window.location.href = "admin.php";
          </script>';
} else {
    echo '<script>
            alert("No matching routes found for the specified stage.");
            window.location.href = "admin.php";
          </script>';
}
?>
