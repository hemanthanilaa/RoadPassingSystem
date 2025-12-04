<?php
// Assuming the username is stored in a session variable or passed as a query parameter
session_start();
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = isset($_GET['username']) ? $_GET['username'] : '';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tagId = $_POST['tagId'];

    $servername = "localhost";
    $usernameDB = "toll";
    $passwordDB = "toll123";
    $dbname = "fastag";

    $conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT tbd.tag_id, bd.account_number, bd.bank_balance 
            FROM bank_details bd 
            JOIN tag_bank_details tbd ON bd.account_number = tbd.account_number 
            WHERE tbd.tag_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $tagId);
    $stmt->execute();
    $result = $stmt->get_result();

    $vehicles = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $vehicles[] = $row;
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recharge</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #eef2f7;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #4b0082;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            font-weight: bold;
        }
        input[type="number"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 150px;
            transition: border-color 0.3s ease;
        }
        input[type="number"]:focus {
            border-color: #4b0082;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: #4b0082;
            color: white;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #9400d3;
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
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .footer {
            background-color: #4b0082;
            padding: 10px 20px;
            color: white;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
        a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            color: #ffa500;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Recharge Tag</h1>
        <form id="rechargeForm" method="post" action="">
            <div>
                <label for="tagId">Tag ID:</label>
                <input type="number" id="tagId" name="tagId" required>
                <button type="submit">Submit</button>
            </div>
            
            <table id="rechargeTable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Tag ID</th>
                        <th>Account Number</th>
                        <th>Bank Balance</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($vehicles)) {
                        $rowCount = 0;
                        foreach ($vehicles as $vehicle) {
                            $rowCount++;
                            echo "<tr>";
                            echo "<td>$rowCount</td>";
                            echo "<td class='tag-id'>" . $vehicle["tag_id"] . "</td>";
                            echo "<td class='account-number'>" . $vehicle["account_number"] . "</td>";
                            echo "<td class='bank-balance'>" . $vehicle["bank_balance"] . "</td>";
                            echo "<td><input type='text' class='amount-input' placeholder='Enter Amount'></td>";
                            echo "<td><button type='button' onclick='pay(this)'>Pay</button></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No data available</td></tr>";
                    } ?>
                </tbody>
            </table>
        </form>
    </div>
    <div class="footer">
        <a href="traveller.php?username=<?php echo htmlspecialchars($username); ?>">Home</a>
    </div>
    <script>
        function pay(button) {
            const row = button.closest('tr');
            const amountInput = row.querySelector('.amount-input');
            const amount = parseFloat(amountInput.value);
            if (isNaN(amount) || amount <= 0) {
                alert("Please enter a valid amount");
                return;
            }

            const accountNumber = row.querySelector('.account-number').textContent;
            const tagId = row.querySelector('.tag-id').textContent;
            const balanceCell = row.querySelector('.bank-balance');
            const currentBalance = parseFloat(balanceCell.textContent);
            const newBalance = currentBalance + amount;

            // AJAX request to update the database
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_balance.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    if (xhr.responseText === 'success') {
                        balanceCell.textContent = newBalance.toFixed(2);
                        alert("Recharged successfully");
                        amountInput.value = '';
                    } else {
                        alert("Recharge failed: " + xhr.responseText);
                    }
                }
            };
            xhr.send('account_number=' + accountNumber + '&tag_id=' + tagId + '&amount=' + amount);

            // Redirect to traveller.php after successful recharge
            xhr.onload = function() {
                if (xhr.status === 200 && xhr.responseText === 'success') {
                    window.location.href = 'traveller.php?username=<?php echo htmlspecialchars($username); ?>';
                }
            };
        }
    </script>
</body>
</html>
