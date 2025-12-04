<?php
$servername = "localhost";
$username = "toll";
$password = "toll123";
$dbname = "fastag";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$toll_no = $_POST['toll_no'];
$vehicletype = $_POST['vehicle_type'];
$vehicle_charge = $_POST['vehicle_charge'];

$sql = "SELECT * FROM toll_tariff WHERE toll_no = ? AND vehicletype = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $toll_no, $vehicletype);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $update_sql = "UPDATE toll_tariff SET vehicle_charge = ? WHERE toll_no = ? AND vehicletype = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("iss", $vehicle_charge, $toll_no, $vehicletype);
    if ($update_stmt->execute()) {
        echo '<script>alert("Vehicle charge updated successfully!"); window.location.href="admin.php";</script>';
    } else {
        echo '<script>alert("Error updating record: ' . $conn->error . '"); window.location.href="admin.php";</script>';
    }
} else {
    $insert_sql = "INSERT INTO toll_tariff (toll_no, vehicletype, vehicle_charge) VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ssi", $toll_no, $vehicletype, $vehicle_charge);
    if ($insert_stmt->execute()) {
        echo '<script>alert("Record added successfully!"); window.location.href="admin.php";</script>';
    } else {
        echo '<script>alert("Error adding record: ' . $conn->error . '"); window.location.href="admin.php";</script>';
    }
}

$stmt->close();
$conn->close();
?>
