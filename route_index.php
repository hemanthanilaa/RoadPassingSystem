<!DOCTYPE html>
<html>
<head>
    <center>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fastag Route Selector</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #e0eafc, #cfdef3);
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 800px;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            display: flex;
            justify-content: space-between;
        }
        .form-section, .stages-section {
            width: 48%;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #4b0082; /* Indigo color */
        }
        input, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
            background-color: #ffffff;
        }
        input[type="text"] {
            background-color: #fafafa;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #4b0082; /* Indigo color */
        }
        button {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            cursor: pointer;
            background: linear-gradient(135deg, #4b0082, #240a62);
            color: #ffffff;
            border: none;
            border-radius: 6px;
            transition: background 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        button:hover {
            background: linear-gradient(135deg, #240a62, #4b0082);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }
        .stage-item {
            padding: 8px;
            border-radius: 6px;
            margin: 6px 0;
            font-size: 16px;
        }
        .toll {
            background-color: #28a745; /* Green color */
            color: white;
        }
        .no-toll {
            background-color: #f8f9fa; /* Light background */
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-section">
            <h2>Fastag Route Selector</h2>
            <form id="routeForm" method="POST" action="submit_route.php">
                <div class="form-group">
                    <label for="route_no">Route No:</label>
                    <select id="route_no" name="route_no" onchange="populateRouteName()">
                        <option value="">Select Route No</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="route_name">Route Name:</label>
                    <input type="text" id="route_name" name="route_name" readonly>
                </div>
                <div class="form-group">
                    <label for="stages">Stages:</label>
                    <select id="stages" name="stages">
                        <option value="">Select Stage</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit">Submit</button>
                </div>
            </form>
        </div>
        <div class="stages-section" id="stagesList">
            <!-- Stages will be dynamically added here -->
        </div>
    </div>

    <script>
        let tolls = {};

        document.addEventListener('DOMContentLoaded', function() {
            fetch('fetch_routes.php')
                .then(response => response.json())
                .then(data => {
                    let routeSelect = document.getElementById('route_no');
                    data.routes.forEach(route => {
                        let option = document.createElement('option');
                        option.value = route.Route_no;
                        option.text = route.Route_no;
                        routeSelect.add(option);
                    });
                    tolls = data.tolls;
                });
        });

        function populateRouteName() {
            let routeSelect = document.getElementById('route_no');
            let selectedRouteNo = routeSelect.value;
            if (selectedRouteNo) {
                fetch('fetch_routes.php')
                    .then(response => response.json())
                    .then(data => {
                        let route = data.routes.find(r => r.Route_no == selectedRouteNo);
                        document.getElementById('route_name').value = route ? route.Route_name : '';
                        populateStages(route, selectedRouteNo);
                    });
            } else {
                document.getElementById('route_name').value = '';
                document.getElementById('stages').innerHTML = '<option value="">Select Stage</option>';
                document.getElementById('stagesList').innerHTML = ''; // Clear stages list
            }
        }

        function populateStages(route, selectedRouteNo) {
            let stagesSelect = document.getElementById('stages');
            stagesSelect.innerHTML = '<option value="">Select Stage</option>'; 
            let stagesList = document.getElementById('stagesList');
            stagesList.innerHTML = ''; // Clear previous stages
            
            if (route) {
                let stages = [
                    route.S1, route.S2, route.S3, route.S4, route.S5,
                    route.S6, route.S7, route.S8, route.S9, route.S10
                ].filter(stage => stage); 

                let tollStages = tolls[selectedRouteNo] || [];
                
                stages.forEach(stage => {
                    let option = document.createElement('option');
                    option.value = stage;
                    option.text = stage;
                    stagesSelect.add(option);

                    let stageItem = document.createElement('div');
                    stageItem.textContent = stage;
                    stageItem.className = tollStages.includes(stage) ? 'stage-item toll' : 'stage-item no-toll';
                    stagesList.appendChild(stageItem);
                });
            }
        }
    </script>
</body>
    </center>
</html>
