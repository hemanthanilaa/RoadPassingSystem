<?php

$hostname = "localhost";
$username = "toll";
$password = "toll123";
$database = "fastag";

$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$traveller_username = $_GET['username'];

$stmt = $conn->prepare("SELECT * FROM vehicle_info WHERE traveller_id = ?");
$stmt->bind_param("s", $traveller_username); 
$stmt->execute();
$result = $stmt->get_result();

$vehicles = []; 
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row; 
    }
} else {
    die("No vehicle information found for this user.");
}

$stmt->close(); 
$conn->close(); 
?>

<!DOCTYPE html>
<html>
<head>
    <title>Traveller Vehicle Information</title>
    <style>
       
       <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        .header {
            position: absolute;
            top: 10px;
            right: 20px;
            z-index: 1000;
        }

        .header a {
            text-decoration: none;
            color: #fff;
            background-color: #4b0082;
            font-size: 1em;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .header a:hover {
            background-color: #6a0dad;
        }

        .info-container {
            flex: 1;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            max-width: 800px;
            overflow-y: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4b0082;
            color: white;
            font-size: 1.1em;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #f1f1f1;
            padding: 10px 20px;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .footer button {
            padding: 10px 20px;
            font-size: 1em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: #4b0082;
            color: white;
            transition: background-color 0.3s ease;
        }

        .footer button:hover {
            background-color: #6a0dad;
        }

        #addTagDialog {
            display: none;
            position: fixed;
            top: 20%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            border-radius: 8px;
        }

        #addTagDialog h2 {
            font-size: 1.5em;
            margin-bottom: 20px;
            color: #4b0082;
        }

        #addTagDialog label {
            font-size: 1.2em;
            font-weight: bold;
            color: #4b0082;
            display: block;
            margin-bottom: 10px;
        }

        #addTagDialog input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 16px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
        }

        #addTagDialog button {
            padding: 10px 20px;
            font-size: 1em;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background-color: #4b0082;
            color: white;
            transition: background-color 0.3s ease;
        }

        #addTagDialog button:hover {
            background-color: #6a0dad;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="main2.php">Logout</a>
    </div>

    <div class="info-container">
        <center><h2>VEHICLE INFORMATION</h2></center>
        <table border="1">
            <thead>
                <tr>
                    <th>Tag ID</th>
                    <th>Registration Number</th>
                    <th>Vehicle Type</th>
                    <th>Traveller ID</th>
                    <th>Tag Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vehicles as $vehicle) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($vehicle['tag_id']); ?></td>
                        <td><?php echo htmlspecialchars($vehicle['registration_number']); ?></td>
                        <td><?php echo htmlspecialchars($vehicle['vehicletype']); ?></td>
                        <td><?php echo htmlspecialchars($vehicle['traveller_id']); ?></td>
                        <td><?php echo htmlspecialchars($vehicle['tag_status']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <button onclick="openAddTagDialog()">Add Tag</button>
        <button onclick="startRide()">Start Ride</button>
        <button onclick="goToRecharge()">Recharge Tag</button>
        <button onclick="renewTag()">Renew Tag</button>
        <button onclick="tagSummary()">Tag Summary</button>
    </div>

    <!-- Add Tag Dialog -->
    <div id="addTagDialog">
        <h2>Add Tag</h2>
        <form id="addTagForm">
            <label for="registration_number">Registration Number:</label>
            <input type="text" id="registration_number" name="registration_number" required><br><br>
            <label for="vehicletype">Vehicle Type:</label>
            <input type="text" id="vehicletype" name="vehicletype" required><br><br>
            <label for="account_number">Account Number:</label>
            <input type="text" id="account_number" name="account_number" required><br><br>
            <button type="button" onclick="submitAddTag()">OK</button>
            <button type="button" onclick="closeAddTagDialog()">Cancel</button>
        </form>
    </div>

    <script>
        let tagID = getMaxTagID() + 1;
        const travellerID = <?php echo json_encode($traveller_username); ?>;

        function getMaxTagID() {
            const tagElements = document.querySelectorAll('td:nth-child(1)');
            let maxID = 1001;
            tagElements.forEach(element => {
                const tagID = parseInt(element.textContent.trim());
                if (tagID > maxID) {
                    maxID = tagID;
                }
            });
            return maxID;
        }

        function openAddTagDialog() {
            document.getElementById('addTagDialog').style.display = 'block';
        }

        function closeAddTagDialog() {
            document.getElementById('addTagDialog').style.display = 'none';
        }

        function submitAddTag() {
    const registrationNumber = document.getElementById('registration_number').value;
    const vehicleType = document.getElementById('vehicletype').value;
    const accountNumber = document.getElementById('account_number').value;

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "add_tag.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                addTagToTable(tagID, registrationNumber, vehicleType, travellerID, "Active");
                tagID++;
                closeAddTagDialog();
            } else {
                alert("Failed to add tag: " + response.message);
            }
        }
    };
    xhr.send("tag_id=" + tagID + "&registration_number=" + registrationNumber + "&vehicletype=" + vehicleType + "&traveller_id=" + travellerID + "&account_number=" + accountNumber + "&tag_status=Active");
}


        function addTagToTable(tagID, registrationNumber, vehicleType, travellerID, tagStatus) {
            const table = document.querySelector('tbody');
            const newRow = table.insertRow();
            newRow.innerHTML = `
                <td>${tagID}</td>
                <td>${registrationNumber}</td>
                <td>${vehicleType}</td>
                <td>${travellerID}</td>
                <td>${tagStatus}</td>
            `;
        }

        function startRide() {
            window.location.href = 'route(1).php';
        }

        function goToRecharge() {
            window.location.href = 'RECHARGE2.php?username=' + travellerID;
        }

        function renewTag() {
    window.location.href = 'renew_tag.php?username=' + travellerID;
}


        function tagSummary() {
            window.location.href = 'tag_summary.php?username=' + travellerID;
        }
    </script>
</body>
</html>
