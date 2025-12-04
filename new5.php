<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contractor Details</title>
<style>
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f8f9fa;
}

h1 {
    color: #343a40;
    margin: 20px 0;
}

table {
    width: 80%;
    margin: 20px auto;
    border-collapse: collapse;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    background-color: #ffffff;
}

th, td {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 12px;
}

th {
    background-color:  #4b0082;
    color: white;
}

tr:nth-child(even) {
    background-color: #f2f2f2;
}

.charges {
    margin-top: 10px;
}

.logout {
    text-align: right;
    margin: 20px;
}

.button {
    background-color: #4b0082; /* Indigo color */
    color: white;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    border-radius: 5px;
    font-size: 16px;
    text-align: center;
    text-decoration: none;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.button:hover {
    background-color: #3a006e; /* Darker indigo color */
    transform: scale(1.05); /* Slightly enlarges the button on hover */
}

.button:active {
    background-color: #2a004a; /* Even darker indigo for active state */
    transform: scale(0.95); /* Slightly reduces the button size on click */
}

.center-align {
    text-align: center;
}

.button-container {
    text-align: center;
    margin: 20px 0;
}

</style>
<script>
function fetchVehicleCharges(toll_no) {
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        document.getElementById("vehicleCharges_" + toll_no).innerHTML = this.responseText;
    }
    xhttp.open("GET", "fetch_charges.php?toll_no=" + toll_no, true);
    xhttp.send();
}

function fetchTransactionDetails(toll_no) {
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        document.getElementById("transactionDetails_" + toll_no).innerHTML = this.responseText;
    }
    xhttp.open("GET", "fetch_transaction_details.php?toll_no=" + toll_no, true);
    xhttp.send();
}
</script>
</head>
<body>
<center><h1>Contractor Details</h1></center>
<div class="logout">
    <button class="button" onclick="window.location.href='main2.php'">Logout</button>
</div>

<?php
    $servername = "localhost";
    $username = "toll";
    $password = "toll123";
    $dbname = "fastag";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $contractor_id = $_GET['username'];

    $stmt = $conn->prepare("SELECT DISTINCT E.toll_no, B.stage
            FROM contractor E
            JOIN tolls B ON E.toll_no = B.toll_no
            WHERE E.C_ID = ?");
    
    $stmt->bind_param("s", $contractor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>Toll Number</th><th>Stage</th><th>Vehicle Charges</th><th>Transaction Details</th></tr>";

        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>".$row["toll_no"]."</td>";
            echo "<td>".$row["stage"]."</td>";
            echo "<td><button class='button' onclick=\"fetchVehicleCharges(".$row['toll_no'].")\">Show Vehicle Charges</button><div id=\"vehicleCharges_".$row['toll_no']."\" class=\"charges\"></div></td>";
            echo "<td><button class='button' onclick=\"fetchTransactionDetails(".$row['toll_no'].")\">Show Transaction Details</button><div id=\"transactionDetails_".$row['toll_no']."\" class=\"charges\"></div></td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "<p>No results found for Contractor ID: $contractor_id</p>";
    }

    $conn->close();
?>
<div class="button-container">
    <form method="post" action="f2.php?username=<?php echo urlencode($contractor_id); ?>">
        <input type="submit" name="buy_toll" value="Buy" class="button">
    </form>
</div>
</body>
</html>
