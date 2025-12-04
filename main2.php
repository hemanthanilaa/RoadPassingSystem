<?php
// Database connection details
$hostname = "localhost";
$username = "toll";
$password = "toll123";
$database = "fastag";

// Create connection
$conn = new mysqli($hostname, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to check login
function check_login($conn, $username, $password) {
    $stmt = $conn->prepare("SELECT username, person_type FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_username, $db_person_type);
        $stmt->fetch();
        if ($db_person_type === 'traveller') {
            return 1;
        }
        elseif($db_person_type === 'contractor'){
            return 2;
        }
        elseif($db_person_type === 'admin'){
            return 3;
        }
    }
    return false;
}

// Initialize message
$message = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if (check_login($conn, $username, $password)==1) {
        header("Location:traveller.php?username=" . $username);
        exit();
    } 
    elseif(check_login($conn, $username, $password)==2) {
        header("Location:new5.php?username=" . $username);
        exit();
    }
    elseif(check_login($conn, $username, $password)==3) {
        header("Location:admin.php?username=" . $username);
        exit();
    }
    else {
        $message = "Invalid username or password, or you are not authorized.";
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FASTag Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: url('fastag.png') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: rgba(74, 70, 187, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            max-width: 400px;
            width: 100%;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #fff;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #fff;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #3f29ba;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-group button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .form-group button:hover {
            background: #0056b3;
        }
        .toggle-link, .signup-buttons {
            text-align: center;
            margin-top: 10px;
        }
        .toggle-link a, .signup-buttons a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }
        .toggle-link a:hover, .signup-buttons a:hover {
            text-decoration: underline;
        }
        .signup-buttons {
            display: flex;
            justify-content: space-between;
        }
        .signup-buttons button {
            width: 48%;
            background: #28a745;
        }
        .signup-buttons button:hover {
            background: #218838;
        }
    </style>
</head>
<body>

<div class="container" id="login-container">
    <h2>Login</h2>
    <form action="" method="post">
        <div class="form-group">
            <label for="login-username">Username</label>
            <input type="text" id="login-username" name="username" required>
        </div>
        <div class="form-group">
            <label for="login-password">Password</label>
            <input type="password" id="login-password" name="password" required>
        </div>
        <div class="form-group">
            <button type="submit">Login</button>
        </div>
    </form>
    <p class="toggle-link">New user? <a href="#" onclick="showSignupOptions()">Sign up here</a></p>
</div>

<div class="container" id="signup-options-container" style="display:none;">
    <h2>Sign Up As</h2>
    <div class="signup-buttons">
        <button onclick="showSignUpForm('C')">Contractor</button>
        <button onclick="showSignUpForm('T')">Traveller</button>
    </div>
    <p class="toggle-link"><a href="#" onclick="showLogin()">Back to Login</a></p>
</div>

<div class="container" id="signup-container" style="display:none;">
    <h2>Sign Up</h2>
    <form action="signup.php" method="post">
        <div class="form-group">
            <label for="signup-username">Username</label>
            <input type="text" id="signup-username" name="username" required readonly>
        </div>
        <div class="form-group">
            <label for="signup-password">Password</label>
            <input type="password" id="signup-password" name="password" required>
        </div>
        <div class="form-group">
            <label for="signup-email">Email</label>
            <input type="email" id="signup-email" name="email" required>
        </div>
        <div class="form-group">
            <label for="signup-phone">Phone Number</label>
            <input type="text" id="signup-phone" name="phone" required>
        </div>
        <input type="hidden" id="person-type" name="person_type">
        <div class="form-group">
            <button type="submit">Sign Up</button>
        </div>
    </form>
    <p class="toggle-link"><a href="#" onclick="showSignupOptions()">Back to Signup Options</a></p>
</div>

<script>
    function showSignUpForm(type) {
        let prefix = type === 'C' ? 'C' : 'T';
        fetch(`generate_username.php?prefix=${prefix}`)
            .then(response => response.text())
            .then(data => {
                document.getElementById('signup-username').value = data;
                document.getElementById('person-type').value = type === 'C' ? 'contractor' : 'traveller';
                document.getElementById('signup-options-container').style.display = 'none';
                document.getElementById('signup-container').style.display = 'block';
            })
            .catch(error => {
                console.error('Error fetching username:', error);
                alert('Failed to generate username. Please try again.');
            });
    }

    function showSignupOptions() {
        document.getElementById('login-container').style.display = 'none';
        document.getElementById('signup-container').style.display = 'none';
        document.getElementById('signup-options-container').style.display = 'block';
    }

    function showLogin() {
        document.getElementById('login-container').style.display = 'block';
        document.getElementById('signup-container').style.display = 'none';
        document.getElementById('signup-options-container').style.display = 'none';
    }
</script>

</body>
</html>
