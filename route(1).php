<?php
$servername = "localhost";
$username = "toll";
$password = "toll123";
$dbname = "fastag";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$options = '';
$route_name = '';
$stages = [];
$toll_stages = [];
$previous_destination = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['route'])) {
    $route_id = $_POST['route'];

    $sql1 = "SELECT Route_name, S1, S2, S3, S4, S5, S6, S7, S8, S9, S10 FROM route WHERE Routeno = ?";
    $stmt1 = $conn->prepare($sql1);
    if ($stmt1) {
        $stmt1->bind_param('i', $route_id);
        $stmt1->execute();
        $stmt1->bind_result($route_name, $s1, $s2, $s3, $s4, $s5, $s6, $s7, $s8, $s9, $s10);

        if ($stmt1->fetch()) {
            $stages = array_filter([$s1, $s2, $s3, $s4, $s5, $s6, $s7, $s8, $s9, $s10]);
            $previous_destination = end($stages);
        } else {
            echo "Error fetching stages: " . $stmt1->error;
        }

        $stmt1->close();
    } else {
        echo "Prepare statement error: " . $conn->error;
    }

    $sql2 = "SELECT toll_no FROM contractor WHERE Routeno = ?";
    $stmt2 = $conn->prepare($sql2);
    if ($stmt2) {
        $stmt2->bind_param('i', $route_id);
        $stmt2->execute();
        $stmt2->store_result();

        $stmt2->bind_result($toll_no);

        while ($stmt2->fetch()) {
            $sql3 = "SELECT stage FROM tolls WHERE toll_no = ?";
            $stmt3 = $conn->prepare($sql3);
            if ($stmt3) {
                $stmt3->bind_param('s', $toll_no);
                $stmt3->execute();
                $stmt3->bind_result($stage);

                if ($stmt3->fetch()) {
                    $toll_stages[] = $stage;
                } else {
                    echo "No stage found for toll number: " . $toll_no;
                }

                $stmt3->close();
            } else {
                echo "Prepare statement error: " . $conn->error;
            }
        }

        $stmt2->free_result();
        $stmt2->close();
    } else {
        echo "Prepare statement error: " . $conn->error;
    }
}

$sql = $previous_destination ? 
    "SELECT Routeno, Route_name FROM route WHERE S1 = ?" :
    "SELECT Routeno, Route_name FROM route";
$stmt = $conn->prepare($sql);
if ($stmt) {
    if ($previous_destination) {
        $stmt->bind_param('s', $previous_destination);
    }
    $stmt->execute();
    $stmt->bind_result($routeno, $route_name);
    
    while ($stmt->fetch()) {
        $options .= '<option value="' . $routeno . '">' . $route_name . '</option>';
    }

    $stmt->close();
} else {
    echo "Prepare statement error: " . $conn->error;
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>ROUTE</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4; /* Light background for better contrast */
            font-family: Arial, sans-serif;
        }
        .container {
            display: flex;
            align-items: center;
            position: relative;
            padding: 20px;
            border-radius: 8px;
            background-color: #ffffff; /* White background for the container */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Subtle shadow for 3D effect */
        }
        .circle-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0 20px;
            position: relative;
        }
        .circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #3498db;
            font-family: Arial, sans-serif;
            border: 1px solid #3498db;
            position: relative;
            z-index: 1;
        }
        .circle::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 100%;
            width: 80px;
            height: 2px;
            background-color: #000;
            transform: translateY(-1px);
            z-index: -1;
        }
        .circle-container:last-child .circle::before {
            content: none;
        }
        .tick {
            font-size: 24px;
            display: none;
        }
        .green {
            background-color: green;
            color: white;
        }
        .green .tick {
            display: block;
        }
        .green::before {
            background-color: green;
        }
        .label {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin-bottom: 7px;
            color: #333; /* Darker text for better readability */
        }
        .toll {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin-top: 7px;
            color: #e74c3c; /* Red color for toll indicator */
        }
        .destination-reached {
            font-family: Arial, sans-serif;
            font-size: 20px;
            color: green;
            margin-top: 20px;
        }
        .button-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
        .button-container button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #4b0082; /* Custom button color */
            color: #ffffff; /* White text on buttons */
            border: none;
            border-radius: 5px; /* Rounded corners for buttons */
            transition: background-color 0.3s ease; /* Smooth transition on hover */
        }
        .button-container button:hover {
            background-color: #6a0dad; /* Slightly lighter shade on hover */
        }
        form {
            margin-bottom: 20px;
        }
        select, button {
            font-size: 16px;
        }
        select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-right: 10px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #4b0082; /* Custom button color */
            color: #ffffff; /* White text on buttons */
            border: none;
            border-radius: 5px; /* Rounded corners for buttons */
            transition: background-color 0.3s ease; /* Smooth transition on hover */
        }
        button:hover {
            background-color: #6a0dad; /* Slightly lighter shade on hover */
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<form method="post" id="routeForm">
    <label for="route">Select Route:</label>
    <select id="route" name="route">
        <option value="">Select Route</option>
        <?php echo $options; ?>
    </select><br><br>
    <center><button type="submit">Submit</button></center>
</form>

<div class="container" id="stagesContainer">
    <?php
        if (!empty($stages)) {
            foreach ($stages as $stage) {
                $isTollStage = in_array($stage, $toll_stages);
                echo '<div class="circle-container">';
                echo '<div class="label">' . $stage . '</div>';
                echo '<div class="circle">' . ($isTollStage ? '<div class="toll">Toll</div>' : '<div class="tick">&#8594;</div>') . '</div>';
                echo '</div>';
            }
        }
    ?>
</div>

<div class="button-container" style="display: none;" id="buttonContainer">
    <button id="returnRoute">Return Route</button>
   
</div>
<br><br><br><br>
<button id="moveLocation">Move Location</button>

<script>
    let currentGreenIndex = 0;
    let isReturning = false;

    function handleStageCompletion() {
        const circles = document.querySelectorAll('.circle');
        if (!isReturning) {
            if (currentGreenIndex < circles.length) {
                circles[currentGreenIndex].classList.add('green');
                currentGreenIndex++;
                if (currentGreenIndex === circles.length) {
                    alert("Destination reached!");
                    showCompletionOptions();
                }
            }
        } else {
            if (currentGreenIndex < circles.length) {
                circles[circles.length - 1 - currentGreenIndex].classList.add('green');
                currentGreenIndex++;
                if (currentGreenIndex === circles.length) {
                    alert("Destination reached!");
                    showCompletionOptions();
                }
            }
        }
    }

    function showCompletionOptions() {
        document.getElementById('buttonContainer').style.display = 'flex';
    }

    function handleReturnRoute() {
        isReturning = true;
        currentGreenIndex = 0;
        const stagesContainer = document.getElementById('stagesContainer');
        
        const originalStages = <?php echo json_encode($stages); ?>;
        const tollStages = <?php echo json_encode($toll_stages); ?>;
        
        stagesContainer.innerHTML = '';
        originalStages.forEach((stage, index) => {
            const isTollStage = tollStages.includes(stage);
            const circleContainer = document.createElement('div');
            circleContainer.className = 'circle-container';

            const label = document.createElement('div');
            label.className = 'label';
            label.textContent = stage;
            circleContainer.appendChild(label);

            const circle = document.createElement('div');
            circle.className = 'circle';
            if (isTollStage) {
                const toll = document.createElement('div');
                toll.className = 'toll';
                toll.textContent = 'Toll';
                circle.appendChild(toll);
            } else {
                const tick = document.createElement('div');
                tick.className = 'tick';
                tick.textContent = '←';
                circle.appendChild(tick);
            }
            circleContainer.appendChild(circle);
            stagesContainer.appendChild(circleContainer);
        });
    }

    function moveLocation() {
        const circles = document.querySelectorAll('.circle');
        if (currentGreenIndex < circles.length) {
            const currentCircle = isReturning ? circles[circles.length - 1 - currentGreenIndex] : circles[currentGreenIndex];
            const stage = currentCircle.previousElementSibling.textContent;

            if (currentCircle.querySelector('.toll')) {
                $.get('fetch_vehicle_charge.php', { stage: stage }, function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        const vehicle_charge = data.vehicle_charge;
                        const tagId = prompt(`Toll reached. Vehicle charge: ${vehicle_charge}. Please enter your Tag ID:`);
                        if (tagId) {
                            $.post('verify_tag.php', { tag_id: tagId }, function(response) {
                                const tagData = JSON.parse(response);
                                if (tagData.valid) {
                                    $.get('transaction.php', { tag_id: tagId, stage: stage }, function(transResponse) {
                                        if (transResponse.trim() === "Transaction successful.") {
                                            alert(transResponse);
                                            handleStageCompletion();
                                        } else {
                                            alert(transResponse);
                                        }
                                    });
                                } else {
                                    alert('Tag ID is invalid. You cannot proceed.');
                                }
                            });
                        }
                    } else {
                        alert('Error fetching vehicle charge.');
                    }
                });
            } else {
                handleStageCompletion();
            }
        }
    }

    document.getElementById('returnRoute').onclick = function() {
        handleReturnRoute();
    };



    document.getElementById('moveLocation').onclick = function() {
        moveLocation();
    };
</script>
</body>
</html>
