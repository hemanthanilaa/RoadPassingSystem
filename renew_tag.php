<!DOCTYPE html>
<html>
<head>
    <title>Renew Tag</title>
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

        .container {
            flex: 1;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            max-width: 800px;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            font-size: 1.2em;
            font-weight: bold;
            color: #4b0082;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 16px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
        }

        button {
            padding: 10px 20px;
            font-size: 1em;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background-color: #4b0082;
            color: white;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #6a0dad;
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

        td input[type="text"] {
            border: none;
            background: none;
            font-size: 1em;
            padding: 0;
            color: #333;
            cursor: default;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .message {
            margin-top: 20px;
            font-size: 1.2em;
            color: red;
            text-align: center;
        }

        .home-link {
            text-align: right;
            margin-bottom: 20px;
        }

        .home-link a {
            text-decoration: none;
            font-size: 1em;
            color: #4b0082;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="home-link">
            <a href="traveller.php?username=<?php echo urlencode($_GET['username'] ?? ''); ?>">Home</a>
        </div>
        <form id="tagForm" method="GET" action="renew_tag.php">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($_GET['username'] ?? ''); ?>">
            <label for="tag_id">Enter Tag ID:</label>
            <input type="text" id="tag_id" name="tag_id" required>
            <button type="submit">Submit</button>
        </form>

        <table id="renewTable">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Tag ID</th>
                    <th>Registration Number</th>
                    <th>Vehicle Type</th>
                    <th>Tag Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Database connection details
                $servername = "localhost";
                $username = "toll";
                $password = "toll123";
                $dbname = "fastag";

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $tag_id = $_GET['tag_id'] ?? null;
                if ($tag_id) {
                    // Query to fetch specific tag info
                    $stmt = $conn->prepare("SELECT tag_id, registration_number, vehicletype, tag_status FROM vehicle_info WHERE tag_id = ?");
                    $stmt->bind_param("s", $tag_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        echo "<tr>";
                        echo "<td>1</td>";
                        echo "<td><input type='text' value='" . htmlspecialchars($row["tag_id"]) . "' readonly></td>";
                        echo "<td><input type='text' value='" . htmlspecialchars($row["registration_number"]) . "' readonly></td>";
                        echo "<td><input type='text' value='" . htmlspecialchars($row["vehicletype"]) . "' readonly></td>";
                        echo "<td><input type='text' value='" . htmlspecialchars($row["tag_status"]) . "' readonly></td>";
                        echo "</tr>";
                        if ($row["tag_status"] == "Inactive") {
                            echo "<tr><td colspan='5' class='message'>The tag is INACTIVE because the account balance is less than 500. Please recharge your tag to activate it.</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='message'>No information found for Tag ID: " . htmlspecialchars($tag_id) . "</td></tr>";
                    }

                    $stmt->close();
                } else {
                    echo "<tr><td colspan='5' class='message'>Please enter a Tag ID to view its status.</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
