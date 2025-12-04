<?php
$servername = "localhost";
$username = "toll";
$password = "toll123";
$dbname = "fastag";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $person_type = $_POST["person_type"];

 
    if (!preg_match("/^[0-9]{10}$/", $phone)) {
        echo '<script>
                alert("Invalid phone number. Please enter a 10-digit phone number.");
                window.history.back();
              </script>';
        exit();
    }


    $stmt = $conn->prepare("SELECT * FROM users WHERE (email = ? OR phone = ?) AND person_type = ?");
    $stmt->bind_param("sss", $email, $phone, $person_type);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<script>
                alert("A user with the same email or phone number already exists for this person type.");
                window.history.back();
              </script>';
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO users (username, password, email, phone, person_type) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $password, $email, $phone, $person_type);

    if ($stmt->execute()) {
        echo '<script>
                alert("New record created successfully");
                window.location.href = "main2.php";
              </script>';
    } else {
        echo '<script>
                alert("Error: ' . $stmt->error . '");
                window.history.back();
              </script>';
    }

    $stmt->close();
}

$conn->close();
?>
