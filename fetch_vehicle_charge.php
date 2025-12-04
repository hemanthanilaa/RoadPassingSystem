<?php
$conn = mysqli_connect("localhost", "toll", "toll123", "fastag");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$stage = isset($_GET['stage']) ? $_GET['stage'] : null;

if ($stage) {
    $sql = "SELECT toll_tariff.vehicle_charge
            FROM toll_tariff 
            JOIN tolls ON toll_tariff.toll_no = tolls.toll_no
            WHERE tolls.stage = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $stage);
    $stmt->execute();
    $stmt->bind_result($vehicle_charge);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => true, 'vehicle_charge' => $vehicle_charge]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No charge found for the provided stage.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'No stage provided.']);
}

$conn->close();
?>
