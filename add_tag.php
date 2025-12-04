<?php

$hostname = "localhost";
$username = "toll";
$password = "toll123";
$database = "fastag";

$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tag_id = $_POST['tag_id'];
$registration_number = $_POST['registration_number'];
$vehicletype = $_POST['vehicletype'];
$traveller_id = $_POST['traveller_id'];
$account_number = $_POST['account_number'];
$tag_status = $_POST['tag_status'];

$conn->begin_transaction();

$response = [];

try {
    $stmt1 = $conn->prepare("INSERT INTO vehicle_info (tag_id, registration_number, vehicletype, traveller_id, tag_status) VALUES (?, ?, ?, ?, ?)");
    $stmt1->bind_param("issss", $tag_id, $registration_number, $vehicletype, $traveller_id, $tag_status);
    
    $stmt2 = $conn->prepare("INSERT INTO tag_bank_details (tag_id, account_number) VALUES (?, ?)");
    $stmt2->bind_param("is", $tag_id, $account_number);
    
    if ($stmt1->execute() && $stmt2->execute()) {
        $conn->commit();
        $response['success'] = true;
    } else {
        $conn->rollback();
        $response['success'] = false;
        $response['message'] = "Failed to insert data.";
    }

    $stmt1->close();
    $stmt2->close();
} catch (Exception $e) {
    $conn->rollback();
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

$conn->close();

echo json_encode($response);

?>
