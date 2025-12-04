<?php
if (isset($_GET["toll_no"])) {
   
    $toll_no = $_GET["toll_no"]; 
    
    $servername = "localhost";
    $username = "toll";
    $password = "toll123";
    $dbname = "fastag";

  
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT transact_date, registration_number, vehicletype, vehicle_charge
            FROM transaction_details
            WHERE toll_no = ?";

    
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Error preparing statement: ' . $conn->error);
    }

    $stmt->bind_param('s', $toll_no);

   
    $stmt->execute();


    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<h3>Transaction Details for Toll Number: $toll_no</h3>";
        echo "<table>";
        echo "<tr><th>Date of Transaction</th><th>Vehicle Registration Number</th><th>Vehicle Type</th><th>Amount</th></tr>";

       
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row["transact_date"]) . "</td>"; // Sanitize output
            echo "<td>" . htmlspecialchars($row["registration_number"]) . "</td>"; // Sanitize output
            echo "<td>" . htmlspecialchars($row["vehicletype"]) . "</td>"; // Sanitize output
            echo "<td>" . htmlspecialchars($row["vehicle_charge"]) . "</td>"; // Sanitize output
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "<p>No transaction details found for Toll Number: $toll_no</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
