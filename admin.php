<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Option</title>
    <style>
     
            body {
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    background-color: #f0f0f0;
    position: relative;
}

.container {
    text-align: center;
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h1 {
    color: #343a40;
    margin-bottom: 20px;
}

.option {
    display: block;
    margin: 10px 0;
    padding: 10px 20px;
    font-size: 16px;
    color: #ffffff;
    background-color: #4b0082; /* Indigo color */
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.option:hover {
    background-color: #3a006e; /* Darker indigo color */
    transform: scale(1.05); /* Slightly enlarges the button on hover */
}

.option:active {
    background-color: #2a004a; /* Even darker indigo for active state */
    transform: scale(0.95); /* Slightly reduces the button size on click */
}

.logout {
    position: absolute;
    top: 20px;
    right: 20px;
}

.logout a {
    color: #4b0082; /* Indigo color */
    text-decoration: none;
    font-size: 14px;
    transition: color 0.3s ease;
}

.logout a:hover {
    color: #3a006e; /* Darker indigo color */
    text-decoration: underline;
}

    </style>
</head>
<body>
    <div class="logout">
        <a href="main2.php">Logout</a>
    </div>
    <div class="container">
        <h1>Select an Option</h1>
        <a href="route_index.php" class="option">Toll Selector</a>
        <a href="input_toll_tariff.php" class="option">Vehicle Charge Selector</a>
    </div>
</body>
</html>
