<?php
$servername = "localhost";
$username = "toll";
$password = "toll123";
$dbname = "fastag";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$contractor_id = $_GET['username']; // Retrieve contractor ID from query parameter

$sql = "SELECT Routeno, Route_name FROM route";
$result = $conn->query($sql);

$options = '';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
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
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        background-color: #f0f0f0;
    }
    .container {
        text-align: center;
        background-color: #ffffff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    form {
        margin: 20px;
    }
    label {
        display: block;
        margin-bottom: 10px;
        font-weight: bold;
    }
    select {
        width: 100%;
        max-width: 300px;
        padding: 10px;
        font-size: 16px;
        margin-bottom: 20px;
        background-color: #ffffff;
        color: #000000;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    select:hover {
        background-color: #f0f0f0;
    }
    button {
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        background-color: #4b0082;
        color: #ffffff;
        border: none;
        border-radius: 4px;
        margin-top: 20px;
        transition: background-color 0.3s ease;
    }
    button:hover {
        background-color: #38006b;
    }
    .toll-stage {
        color: #ff0000; /* Red color for toll stages */
    }
    .note {
        margin-top: 20px;
        color: #ff0000; /* Red color for note */
        font-weight: bold;
    }
</style>
<script>
function fetchRouteDetails(routeId) {
    // Fetch stages
    const xhttpStages = new XMLHttpRequest();
    xhttpStages.onload = function() {
        document.getElementById("stages").innerHTML = this.responseText;
    }
    xhttpStages.open("GET", "fetch_toll_numbers1.php?route_id=" + routeId, true);
    xhttpStages.send();

    // Fetch toll numbers
    const xhttpTolls = new XMLHttpRequest();
    xhttpTolls.onload = function() {
        document.getElementById("toll").innerHTML = this.responseText;
    }
    xhttpTolls.open("GET", "fetch_toll_numbers.php?route_id=" + routeId, true);
    xhttpTolls.send();
}

function showAlert() {
    alert('Toll bought successfully');
    return false; // Prevents the form from actually submitting
}
</script>
</head>
<body>
<div class="container">
    <h2>Select Route and Toll to Buy</h2>

    <form method="post" action="process_buy.php">
        <input type="hidden" name="contractor_id" value="<?php echo htmlspecialchars($contractor_id); ?>">

        <label for="route">Select Route</label>
        <select id="route" name="route" required onchange="fetchRouteDetails(this.value)">
            <option value="">Select Route</option>
            <?php echo $options; ?>
        </select>

        <div id="stages"></div>
        
        <label for="toll">Select Toll Number</label>
        <select id="toll" name="toll" required>
            <option value="">Select Toll Number</option>
        </select>

        <button type="submit" onclick="showAlert()">Buy</button>
    </form>

    <div class="note">
        Note: The red-colored places indicate toll places.
    </div>
</div>
</body>
</html>