<?php
session_start(); // Start the session

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'firebase.php'; // Include your 

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}


// Get the user ID
$userId = $_SESSION['user_id'];


// $pythonPath = shell_exec("where python");
// $pythonPath = trim($pythonPath); // Remove extra spaces or newlines
// if (!$pythonPath) {
//     die("Error: Python is not installed or not added to the PATH.");
// }

$pythonScript = escapeshellcmd("python read_h5.py $userId");
$output = shell_exec($pythonScript . " 2>&1"); // Capture output and errors
if ($output) {
    echo "<pre>$output</pre>";
}


// Check if a date was provided
if (isset($_GET['date'])) {
    $selectedDate = $_GET['date'];

    // Fetch the data for the selected date
    $data = getDataForDate($userId, $selectedDate);

    if (!$data) {
        echo json_encode(["error" => "No data found for the selected date"]);
        exit();
    }

    // Process the data (e.g., heart rate)
    $totalHeartRate = 0;
    $dataCount = 0;

    foreach ($data as $entry) {
        if (isset($entry['heart_rate'])) {
            $totalHeartRate += $entry['heart_rate'];
            $dataCount++;
        }
    }

    if ($dataCount > 0) {
        $averageHeartRate = $totalHeartRate / $dataCount;
        echo json_encode(["averageHeartRate" => $averageHeartRate]);
    } else {
        echo json_encode(["error" => "No heart rate data available"]);
    }
} else {
    echo json_encode(["error" => "Please select a date."]);
}


$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sleep Statistics</title>
    <script src="https://d3js.org/d3.v6.min.js"></script>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #616cbb, #748ac7);
            color: #fff;

            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        /* Header Styles */
        .header {
            width: 100%;
            max-width: 1200px;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #4C57A7;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            color: white;
            border-radius: 10px;
        }

        .header img {
            max-width: 100px;
        }
        
        .date-time {
            font-size: 16px;
            font-weight: bold;
            color: white;
            margin-top: 5px; /* Added margin to move it down a bit */
        }

        .logout-settings-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logout-button {
            padding: 10px 20px;
            background-color: white;
            color: #4C57A7;
            border: 1px solid #4C57A7;
            border-radius: 5px;
            cursor: pointer;
        }

        .logout-button:hover {
            background-color: #E2E8F0;
        }


    /* Button Styling */
    .settings-button {
        background: none;
        border: none;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
        width: 50px; /* Increased width */
        height: 50px; /* Increased height */
        padding: 12px; /* Adjusted padding for better proportions */
        border-radius: 50%;
        transition: background-color 0.3s ease;
        background-color: white;

    }



    /* Hover Effect for the Button */
    .settings-button:hover {
        background-color: rgba(0, 0, 0, 0.1);
    }
    
    
        /* Fullscreen overlay */
    .settings-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        display: none;
        justify-content: space-between; /* Divides left and right sections */
        padding: 20px;
        z-index: 1000;
        color: white;
    }

 /* Settings Menu (Left Section) */
    .settings-menu {
        flex: 1; /* Left section takes 30% */
        max-width: 20%; /* Optional: Restrict max width */
        /*background: linear-gradient(to left, #748ac7, #4C57A7);*/
        background: linear-gradient(to right, #616cbb, #748ac7);
        padding: 30px;
        border-radius: 10px;
        font-size: 24px;
        box-shadow: 2px 2px 15px rgba(0, 0, 0, 0.5);
    }

    .settings-menu h2 {
        color: #D1D9F1;
        margin-top: 0;
        font-weight: bold;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    }

    .settings-menu ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .settings-menu li {
        margin-bottom: 15px;
    }

    .settings-menu a {
        text-decoration: none;
        color: #E2E8F0;
        font-size: 20px;
        padding: 5px;
        transition: color 0.3s, background-color 0.3s;
        border-radius: 5px;
    }

    .settings-menu a:hover {
        color: #2C3E99;
        background-color: #D1D9F1;
    }
    
    
    
    /* Team Section */
    .team-section {
        flex: 2;
        background: linear-gradient(to right, #616cbb, #748ac7);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 2px 2px 15px rgba(0, 0, 0, 0.5);
        color: #ffffff;
        overflow-y: auto;
        display: grid; /* Use grid to align team members */
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); /* Create responsive grid */
        gap: 20px; /* Space between the team members */
    }

        .team-section h1 {
            font-size: 52px;
            margin-bottom: 100px;
            color: white;
            font-family: 'Yatra One', cursive;
        }

    .team-member {
        transition: transform 0.3s ease-in-out;
        text-decoration: none;
        color: inherit;
        background: white;
        padding: 20px;
        border-radius: 10px;
        position: relative;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

        .team-member:hover {
            transform: scale(1.05);
        }

        .team-member img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
        }

        .team-member h3 {
            color: #4C57A7;
            font-size: 26px;
            margin-top: 20px;
        }

        .team-member p {
            color: #4C57A7;
            font-size: 14px;
        }
        
        
        

    /* Close Button */
    .close-settings {
        background: none;
        border: none;
        font-size: 18px;
        color: #E2E8F0;
        cursor: pointer;
        margin-bottom: 20px;
        border-radius: 5px;
        transition: color 0.3s, background-color 0.3s;
    }

    .close-settings:hover {

        color: #2C3E99;
        background-color: #D1D9F1;
    }





        /* Navigation Bar Styles */
        .nav-container {
            margin: 20px 0;
            padding: 10px 0;
        }
    
    
        .nav-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            gap: 15px;
        flex-wrap: wrap; /* Allows wrapping on smaller screens */
        justify-content: center;
        }

        .nav-link {
            text-decoration: none;
            color: #fff;
            font-size: 18px;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }
    
        .nav-link:hover, .nav-link.active {
            background-color: #D1D9F1;
            color: #2C3E99;
        }





       /* Footer Styles */
        .footer {
            font-size: 14px;
            text-align: center;
            margin-top: auto;
        }

        footer hr {
            border: 0;
            border-top: 1px solid white;
            margin-bottom: 10px;
        }
        
        
        
        @media (max-width: 768px) {
    .header {
        flex-direction: column;
        align-items: flex-start;
    }

    .header img {
        max-width: 80px;
    }

    .settings-overlay {
        flex-direction: column; /* Stack the settings and about sections */
        gap: 20px;
    }
    
    .team-section {
        grid-template-columns: repeat(2, 1fr); /* Two items per row on larger screens */
    }

    .settings-menu, .about-us {
        max-width: 100%; /* Use full width for smaller screens */
        flex: none;
    }

    .bar-chart {
        height: 150px; /* Adjust chart height */
    }

    .nav-link {
        font-size: 16px;
        padding: 8px 10px;
    }
}

        
        
        /* Active overlay display */
    .settings-overlay.active {
        display: flex; /* Flex layout is only applied when active */
    }
        
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <img src="images/sleep.png" alt="Sleep Med Logo">
        <div class="date-time" id="currentDateTime"></div>
        <div class="logout-settings-container">
            <button id="logout-btn" class="logout-button">Logout</button>
            <button id="settings-btn" class="settings-button">⋮</button>
        </div>
    </div>


    <script>
        // Update the date and time dynamically
        function updateDateTime() {
            const date = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
            const formattedDateTime = date.toLocaleString('en-US', options);
            document.getElementById('currentDateTime').textContent = formattedDateTime;
        }

        setInterval(updateDateTime, 1000);
        updateDateTime();
    </script>



<div class="settings-overlay" id="settings-overlay">
    <!-- Settings Menu -->
    <div class="settings-menu">
        <button class="close-settings" id="close-settings">Close ✕</button>
        <h2>Settings</h2>
        <ul>
            <li><a href="#">Switch Account</a></li>
            <li><a href="#">Delete Account</a></li>
            <li><a href="#">Language</a></li>
            <li><a href="#">Support</a></li>
            <li><a href="app-information.php">App Information</a></li>
        </ul>
    </div>
    
    
    
    
    <!-- Team Section -->
    <div class="team-section">
        <h1>Our Team</h1>

        <!-- Team Member 1 -->
        <a href="https://github.com/safrinfaizz" target="_blank" class="team-member">
            <div>
                <img src="images/safreena.jpg" alt="Safreena">
            </div>
            <h3>Safreena</h3>
            <p>Front-End Developer</p>
            <p>"As a health informatics student interested in building websites and working with data, I contributed to the Sleep Monitor project by developing the front-end. For me, front-end development is where creativity and technology meet to solve problems and inspire users."</p>
        </a>

        <!-- Team Member 2 -->
        <a href="https://github.com/SenaDok" target="_blank" class="team-member">
            <div>
                <img src="images/sena.jpg" alt="Sena">
            </div>
            <h3>Sena</h3>
            <p>Front-End Developer</p>
            <p>“A healthy body holds a healthy mind and soul, and that's what we should strive to have and share”</p>
        </a>

        <!-- Team Member 3 -->
        <a href="https://github.com/AngelinaNSS" target="_blank" class="team-member">
            <div>
                <img src="images/angelina.jpg" alt="Angelina">
            </div>
            <h3>Angelina</h3>
            <p>Front-End Developer</p>
            <p>"I’m a health informatics student with a passion for using tech to improve healthcare. With this Sleep Monitor project, I aim to help people track and improve their sleep, especially for those working late shifts, so they can feel better and perform their best."</p>
        </a>

        <!-- Team Member 4 -->
        <a href="https://github.com/kseniiavi" target="_blank" class="team-member">
            <div>
                <img src="images/kseniia.jpg" alt="Kseniia">
            </div>
            <h3>Kseniia</h3>
            <p>Back-End Developer</p>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Est quaerat tempora.</p>
        </a>

        <!-- Team Member 5 -->
        <a href="https://github.com/Maryem29" target="_blank" class="team-member">
            <div>
                <img src="images/maryem.jpg" alt="Maryem">
            </div>
            <h3>Maryem</h3>
            <p>Back-End Developer</p>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Est quaerat tempora.</p>
        </a>
    </div>
    
    

</div>

<script>
    const settingsBtn = document.getElementById("settings-btn");
    const settingsOverlay = document.getElementById("settings-overlay");
    const closeSettings = document.getElementById("close-settings");

    // Open settings overlay
    settingsBtn.addEventListener("click", () => {
        settingsOverlay.classList.add("active");
    });

    // Close settings overlay
    closeSettings.addEventListener("click", () => {
        settingsOverlay.classList.remove("active");
    });

    // Optional: Close overlay when clicking outside the settings panel
    settingsOverlay.addEventListener("click", (e) => {
        if (e.target === settingsOverlay) {
            settingsOverlay.classList.remove("active");
        }
    });
</script>



    <!-- Navigation -->
    <div class="nav-container">
        <ul class="nav-menu">
            <li><a href="statistics.php" class="nav-link <?= $current_page === 'statistics.php' ? 'active' : ''; ?>">Statistics</a></li>
            <li><a href="report.php" class="nav-link <?= $current_page === 'report.php' ? 'active' : ''; ?>">Report</a></li>
            <li><a href="sleep.php" class="nav-link <?= $current_page === 'sleep.php' ? 'active' : ''; ?>">Sleep</a></li>
            <li><a href="alerts.php" class="nav-link <?= $current_page === 'alerts.php' ? 'active' : ''; ?>">Alerts</a></li>
            <li><a href="profile.php" class="nav-link <?= $current_page === 'profile.php' ? 'active' : ''; ?>">Profile</a></li>
        </ul>
    </div>












<script>
function updateData() {
    const selectedDate = document.getElementById('datePicker').value;
    if (selectedDate) {
        fetchDataAndAnalyze(selectedDate);
    } else {
        alert("Please select a date.");
    }
}

async function fetchDataAndAnalyze(selectedDate) {
    const response = await fetch(`/get_data_for_date.php?date=${selectedDate}`);
    const data = await response.json();

    if (data.error) {
        alert(data.error);
        return;
    }

    const heartRateData = processData(data);
    renderPieChart("heartRateChart", "Average Heart Rate", heartRateData.averageHeartRate);
}




</script>





<!-- Date picker -->
    <input type="date" id="datePicker" onchange="updateData()">
    
    <div id="pieCharts">
        <div id="heartRateChart"></div>
    </div>

    <script>
        function updateData() {
            const selectedDate = document.getElementById('datePicker').value;
            fetchDataAndAnalyze(selectedDate);
        }

        async function fetchDataAndAnalyze(selectedDate) {
            // Fetch data for selected date (from Firebase or backend)
            const response = await fetch(`/get_data_for_date.php?date=${selectedDate}`);
            const data = await response.json();

                // Check if data is returned
            console.log(data); // Add this line to check if data is being returned correctly
                    // Process the data for heart rate, movement, etc.
            const heartRateData = processData(data);
            
            // Render pie charts
            renderPieChart("heartRateChart", "Average Heart Rate", heartRateData.averageHeartRate);
        }

        function processData(data) {
            let heartRateSum = 0;
            let sleepDuration = 0;
            let movement = 0;
            let lowHeartRateCount = 0;
            let highHeartRateCount = 0;

            data.forEach(entry => {
                const heartRate = entry.heart_rate;
                heartRateSum += heartRate;

                // Simple rules based on heart rate (you can adjust these based on your actual data)
                if (heartRate < 60) {
                    sleepDuration++;
                    lowHeartRateCount++;
                } else if (heartRate > 100) {
                    movement++;
                    highHeartRateCount++;
                }
            });

            // Calculate averages
            const averageHeartRate = heartRateSum / data.length;

            // Sleep quality analysis (basic)
            let sleepQuality = "Good";
            if (highHeartRateCount > lowHeartRateCount) {
                sleepQuality = "Poor";
            } else if (lowHeartRateCount < data.length / 2) {
                sleepQuality = "Moderate";
            }

            return {
                averageHeartRate,
                sleepDuration,
                movement,
                sleepQuality
            };
        }

        function renderPieChart(id, label, value) {
            const width = 200;
            const height = 200;
            const radius = Math.min(width, height) / 2;

            const color = d3.scaleOrdinal()
                .domain([label, "Rest"])
                .range(["#2ca02c", "#ff7f0e"]);

            const data = [
                { label: label, value: value },
                { label: "Rest", value: 100 - value }
            ];

            const svg = d3.select(`#${id}`).append("svg")
                .attr("width", width)
                .attr("height", height)
                .append("g")
                .attr("transform", `translate(${width / 2}, ${height / 2})`);

            const pie = d3.pie().value(d => d.value);
            const arc = d3.arc().innerRadius(0).outerRadius(radius);

            const arcs = svg.selectAll("arc")
                .data(pie(data))
                .enter().append("g")
                .attr("class", "arc");

            arcs.append("path")
                .attr("d", arc)
                .attr("fill", d => color(d.data.label));

            arcs.append("text")
                .attr("transform", d => `translate(${arc.centroid(d)})`)
                .attr("dy", ".35em")
                .text(d => `${d.data.label}: ${d.data.value}%`);
        }
    </script>












   <!-- Footer -->
    <footer>
        <hr>
        <p>Created by: Kseniia, Maryem, Sena, Saffree, Angelina - Sleep Med </p>
    </footer>

    <script>
        // Handle logout
        document.getElementById("logout-btn").addEventListener("click", function () {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = "login.php";
            }
        });

        
              
        
        
    </script>
</body>
</html>