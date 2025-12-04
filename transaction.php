<?php
$conn = mysqli_connect("localhost", "toll", "toll123", "fastag");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$tag_id = isset($_GET['tag_id']) ? intval($_GET['tag_id']) : null;
$stage = isset($_GET['stage']) ? $_GET['stage'] : null;

if ($tag_id && $stage) {
    $transact_date = date("Y-m-d");
    $transact_time = date("H:i:s");

    // Get the toll number for the given stage
    $stage_query = $conn->prepare("SELECT toll_no FROM tolls WHERE stage = ?");
    $stage_query->bind_param('s', $stage);
    $stage_query->execute();
    $stage_result = $stage_query->get_result();

    if ($stage_result && $stage_result->num_rows > 0) {
        $stage_row = $stage_result->fetch_assoc();
        $toll_no = $stage_row['toll_no'];

        // Get the vehicle and billing details
        $query = $conn->prepare("SELECT 
                                    vehicle_info.registration_number, 
                                    vehicle_info.vehicletype, 
                                    vehicle_info.traveller_id, 
                                    vehicle_info.tag_status, 
                                    toll_tariff.vehicle_charge, 
                                    contractor.C_ID,
                                    tag_bank_details.account_number,
                                    bank_details.bank_balance
                                FROM vehicle_info 
                                JOIN tag_bank_details ON vehicle_info.tag_id = tag_bank_details.tag_id
                                JOIN toll_tariff ON toll_tariff.vehicletype = vehicle_info.vehicletype
                                JOIN contractor ON toll_tariff.toll_no = contractor.toll_no
                                JOIN bank_details ON tag_bank_details.account_number = bank_details.account_number
                                WHERE vehicle_info.tag_id = ? AND toll_tariff.toll_no = ?");
        $query->bind_param('is', $tag_id, $toll_no);
        $query->execute();
        $result = $query->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $registration_number = $row['registration_number'];
            $vehicletype = $row['vehicletype'];
            $traveller_id = $row['traveller_id'];
            $tag_status = $row['tag_status'];
            $vehicle_charge = $row['vehicle_charge'];
            $c_id = $row['C_ID'];
            $account_number = $row['account_number'];
            $bank_balance = $row['bank_balance'];

            // Check if the tag status is Active
            if ($tag_status !== 'Active') {
                echo "Transaction cannot be processed as the tag status is not active.";
                $conn->close();
                exit;
            }

            // Check if a transaction already exists for the same vehicle and toll in the past day
            $sql_check_previous = $conn->prepare("SELECT * FROM transaction_details 
                                                   WHERE registration_number = ? 
                                                   AND toll_no = ? 
                                                   AND transact_date >= DATE_SUB(NOW(), INTERVAL 1 DAY)");
            $sql_check_previous->bind_param('ss', $registration_number, $toll_no);
            $sql_check_previous->execute();
            $result_check_previous = $sql_check_previous->get_result();

            if ($result_check_previous && $result_check_previous->num_rows > 0) {
                $vehicle_charge *= 0.5;
            }

            // Insert the transaction details
            $sql_insert_transaction = $conn->prepare("INSERT INTO transaction_details (
                                                       transact_date, 
                                                       transact_time, 
                                                       vehicle_charge, 
                                                       toll_no, 
                                                       tag_id, 
                                                       vehicletype, 
                                                       traveller_id, 
                                                       registration_number,
                                                       C_ID
                                                   ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $sql_insert_transaction->bind_param('ssissssss', 
                                                $transact_date, 
                                                $transact_time, 
                                                $vehicle_charge, 
                                                $toll_no, 
                                                $tag_id, 
                                                $vehicletype, 
                                                $traveller_id, 
                                                $registration_number,
                                                $c_id);

            if ($sql_insert_transaction->execute() === TRUE) {
                // Update bank balance
                $new_balance = $bank_balance - $vehicle_charge;
                $sql_update_balance = $conn->prepare("UPDATE bank_details SET bank_balance = ? WHERE account_number = ?");
                $sql_update_balance->bind_param('is', $new_balance, $account_number);
                $sql_update_balance->execute();

                // Check if the balance is below 500 and update tag status
                if ($new_balance < 500) {
                    $sql_update_tag_status = $conn->prepare("UPDATE vehicle_info SET tag_status = 'Inactive' WHERE tag_id = ?");
                    $sql_update_tag_status->bind_param('i', $tag_id);
                    $sql_update_tag_status->execute();
                }

                echo "Transaction successful.";
            } else {
                echo "Error: " . $sql_insert_transaction->error;
            }
        } else {
            echo "No vehicle found with the provided tag ID";
        }
    } else {
        echo "No toll found for the provided stage";
    }
} else {
    echo "No Tag ID or Stage provided.";
}

$conn->close();
?>
