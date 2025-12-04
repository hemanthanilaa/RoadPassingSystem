<?php
$conn = mysqli_connect("localhost", "toll", "toll123", "fastag");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$account_number = isset($_POST['account_number']) ? $_POST['account_number'] : null;
$tag_id = isset($_POST['tag_id']) ? intval($_POST['tag_id']) : null;
$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : null;

if ($account_number && $tag_id && $amount) {
    $update_balance_query = $conn->prepare("UPDATE bank_details 
                                            SET bank_balance = bank_balance + ? 
                                            WHERE account_number = ?");
    $update_balance_query->bind_param('ds', $amount, $account_number);
    
    if ($update_balance_query->execute()) {
        $check_balance_query = $conn->prepare("SELECT bank_balance FROM bank_details WHERE account_number = ?");
        $check_balance_query->bind_param('s', $account_number);
        $check_balance_query->execute();
        $balance_result = $check_balance_query->get_result();
        
        if ($balance_result->num_rows > 0) {
            $balance_row = $balance_result->fetch_assoc();
            $new_balance = $balance_row['bank_balance'];
            

            $new_status = ($new_balance < 500) ? 'Inactive' : 'Active';
            $update_tag_status_query = $conn->prepare("UPDATE vehicle_info SET tag_status = ? WHERE tag_id = ?");
            $update_tag_status_query->bind_param('si', $new_status, $tag_id);
            $update_tag_status_query->execute();
            
            echo 'success';
        } else {
            echo "Error: No account found";
        }
    } else {
        echo "Error: " . $update_balance_query->error;
    }

    $update_balance_query->close();
    $check_balance_query->close();
    $update_tag_status_query->close();
} else {
    echo "Error: Missing data";
}

$conn->close();
?>
