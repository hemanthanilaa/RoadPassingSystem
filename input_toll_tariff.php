<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Input Toll Tariff</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
body {
    font-family: 'Roboto', sans-serif;
    background-color: #f0f0f0; /* Light background color */
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-image: url('your-background-image.jpg'); /* Replace with your background image URL */
    background-size: cover;
    background-position: center;
}

.container {
    background: #ffffff; /* Light background for the container */
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 400px;
    width: 100%;
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #4b0082; /* Dark indigo color for header */
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #333333; /* Dark gray color for labels */
}

.form-group input, .form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid #cccccc; /* Light gray border */
    border-radius: 5px;
    font-size: 16px;
    box-sizing: border-box; /* Ensure padding is included in the width */
}

.form-group button {
    width: 100%;
    padding: 12px;
    background: #4b0082; /* Dark indigo background for button */
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.form-group button:hover {
    background: #3a006e; /* Darker indigo on hover */
    transform: scale(1.05); /* Slightly enlarges the button on hover */
}

.form-group button:active {
    background: #2a004a; /* Even darker indigo for active state */
    transform: scale(0.95); /* Slightly reduces the button size on click */
}


    </style>
</head>
<body>

<div class="container">
    <h2>Input Toll Tariff</h2>
    <form action="submit_toll_tariff.php" method="post">
    <div class="form-group">
        <label for="toll_no">Toll No</label>
        <select id="toll_no" name="toll_no" required>
            <option value="" disabled selected>Select Toll No</option>
            <!-- Options will be populated dynamically -->
        </select>
    </div>
    <div class="form-group">
        <label for="vehicle_type">Vehicle Type</label>
        <select id="vehicle_type" name="vehicle_type" required>
            <option value="" disabled selected>Select Vehicle Type</option>
            <option value="Car">Car</option>
            <option value="LCV">LCV</option>
            <option value="Lorry">Lorry</option>
            <option value="Truck">Truck</option>
            <option value="Bus">Bus</option>
        </select>
    </div>
    <div class="form-group">
        <label for="vehicle_charge">Vehicle Charge</label>
        <input type="number" id="vehicle_charge" name="vehicle_charge" required>
    </div>
    <div class="form-group">
        <button type="submit">Submit</button>
    </div>
</form>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        fetch('get_toll_numbers.php')
            .then(response => response.json())
            .then(data => {
                const tollNoSelect = document.getElementById('toll_no');
                data.forEach(toll => {
                    const option = document.createElement('option');
                    option.value = toll.toll_no;
                    option.textContent = toll.toll_no;
                    tollNoSelect.appendChild(option);
                });
            });
    });
</script>

</body>
</html>
