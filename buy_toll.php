<?php

$servername = "localhost";
$username = "toll";
$password = "toll123";
$dbname = "fastag";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$sql = "SELECT Routeno, Route_name FROM route";
$result = $conn->query($sql);


$options = '';
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $options .= '<option value="' . $row['Routeno'] . '">' . $row['Route_name'] . '</option>';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Select Route and Toll</title>
<style>
    body {
        font-family: Arial, sans-serif;
    }
    form {
        margin: 20px;
    }
    label {
        display: block;
        margin-bottom: 10px;
    }
    select {
        width: 300px;
        padding: 8px;
        font-size: 16px;
        margin-bottom: 10px;
    }
    button {
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
    }
</style>
<script>

function fetchTollNumbers(routeId) {
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        document.getElementById("toll").innerHTML = this.responseText;
    }
    xhttp.open("GET", "fetch_toll_numbers.php?route_id=" + routeId, true);
    xhttp.send();
}
</script>
</head>
<body>
<h2>Select Route and Toll to Buy</h2>

<form method="post">
    <label for="route">Select Route:</label>
    <select id="route" name="route" required onchange="fetchTollNumbers(this.value)">
        <option value="">Select Route</option>
        <?php echo $options; ?>
    </select>

    <label for="toll">Select Toll Number:</label>
    <select id="toll" name="toll" required>
        <option value="">Select Toll Number</option>
        
    </select>

    <button type="submit">Buy</button>
</form>

</body>
</html>
