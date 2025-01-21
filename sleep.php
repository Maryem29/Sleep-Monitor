<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'firebase.php';

function fetch_average_heartbeat($userId, $date) {
    $database = initialize_firebase();
    $heartbeatRef = $database->getReference("users/$userId/heartbeat_data");

    try {
        $data = $heartbeatRef->getChild($date)->getValue();

        if ($data && isset($data['night']) && is_array($data['night'])) {
            $nightData = $data['night'];
            $averageHeartbeat = array_sum($nightData) / count($nightData);
            return ['date' => $date, 'average_heartbeat' => $averageHeartbeat];
        } else {
            return ['date' => $date, 'average_heartbeat' => 0];
        }
    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['date'])) {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'User not logged in']);
        exit;
    }

    $userId = $_SESSION['user_id'];
    $date = $_GET['date'];
    $result = fetch_average_heartbeat($userId, $date);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

// Get the current page name
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Average Heartbeat Analysis</title>
    <script src="https://d3js.org/d3.v7.min.js"></script>
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





        h1 {
            text-align: center;
            margin-top: 30px;
            color: #4C57A7;
                }
        .container {
            width: 80%;
            max-width: 900px;
            margin: 0 auto;
            padding: 25px;
            background-color: #ffffff; /* Matches your website's background color */
            border-radius: 12px; /* Rounded corners to match your website's design */
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1); /* Subtle shadow to give depth */
        }

        .input-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .input-container label {
            font-size: 18px;
            color: #6c4dbf; /* Consistent text color */
            margin-right: 10px;
        }

        .input-container input[type="date"], .input-container button {
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin: 0 10px;
        }

        .input-container input[type="date"] {
            background-color: #f9f9f9;
        }

        .input-container button {
            background-color: #4C57A7; /* Matches the header button */
            color: white;
            border: none;
            cursor: pointer;
        }

        .input-container button:hover {
            background-color: #4C57A7; /* Darker hover effect */
        }

        #chart {
            display: flex;
            justify-content: center;
            margin-top: 40px;
        }

        #message {
            text-align: center;
            margin-top: 20px;
            font-size: 18px;
            color: #ff6b81; /* Error message color */
        }





           /* Footer Styles */
        .footer {
            font-size: 14px;
            text-align: center;
            margin-top: auto;
        }

        .footer hr {
            border: 0;
            border-top: 1px solid white;
            margin-bottom: 10px;
        }

        .footer p {
            text-align: center;
        }
        
        
                /* Media Queries */
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
        .container {
                padding: 20px;
        }

        .info-item {
            align-items: flex;
        }

        .info-item label {
            text-align: flex;
           
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
            <li><a href="delete-account.php">Delete Account</a></li>
            <li><a href="support.php">Support</a></li>
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
    











    <div class="container">
        <h1>Average Heartbeat Analysis</h1>
        <div class="input-container" id="input-container">
            <label for="date">Select a Date:</label>
            <input type="date" id="date" />
            <button id="analyze-btn">Analyze</button>
        </div>
        <div id="chart"></div>
        <div id="message"></div>
    </div>

    <script>
        // Create form elements dynamically with D3.js
        const inputContainer = d3.select("#input-container");
        inputContainer.select("label")
            .style("font-size", "18px")
            .style("color", "#4C57A7");

        d3.select("#date")
            .style("padding", "12px")
            .style("font-size", "16px")
            .style("border-radius", "8px")
            .style("border", "1px solid #ddd")
            .style("background-color", "#f9f9f9");

        d3.select("#analyze-btn")
            .style("padding", "12px 18px")
            .style("font-size", "16px")
            .style("border-radius", "8px")
            .style("border", "none")
            .style("background-color", "#4C57A7")
            .style("color", "white")
            .style("cursor", "pointer")
            .on("mouseover", function() { d3.select(this).style("background-color", "#4C57A7"); })
            .on("mouseout", function() { d3.select(this).style("background-color", "#4C57A7"); })
            .on("click", fetchAndRender);

        async function fetchAndRender() {
            const dateInput = d3.select('#date').node().value;
            if (!dateInput) {
                alert("Please select a date.");
                return;
            }

            try {
                const response = await fetch(`?date=${dateInput}`);
                const data = await response.json();

                // Clear previous chart and messages
                d3.select('#chart').html('');
                d3.select('#message').html('');

                if (data.error) {
                    d3.select('#message').text("Error: " + data.error).style("color", "#4C57A7");
                    return;
                }

                if (data.average_heartbeat === 0) {
                    d3.select('#message').text("No heartbeat data found for the selected date.").style("color", "#4C57A7");
                    return;
                }

                renderDonutChart(data.average_heartbeat);
            } catch (error) {
                console.error("Error fetching data:", error);
                d3.select('#message').text("An error occurred while fetching the data.").style("color", "#4C57A7");
            }
        }

        function renderDonutChart(averageHeartbeat) {
            const maxHeartbeat = 140;
            const chartData = [
                { label: "Heartbeat", value: averageHeartbeat },
                { label: "Remaining", value: maxHeartbeat - averageHeartbeat },
            ];

            const width = 300;
            const height = 300;
            const radius = Math.min(width, height) / 2;
            const innerRadius = radius / 2;

            const svg = d3.select("#chart")
                .append("svg")
                .attr("width", width)
                .attr("height", height)
                .append("g")
                .attr("transform", `translate(${width / 2}, ${height / 2})`);

            const color = d3.scaleOrdinal()
                .domain(chartData.map(d => d.label))
                .range(["#4C57A7", "#e3e0f9"]);

            const pie = d3.pie()
                .value(d => d.value);

            const arc = d3.arc()
                .innerRadius(innerRadius) // Creates the donut effect
                .outerRadius(radius);

            // Create the arc paths for the chart with transition effect for overflow
            const path = svg.selectAll("path")
                .data(pie(chartData))
                .join("path")
                .attr("d", arc)
                .attr("fill", d => color(d.data.label))
                .each(function(d) { this._current = d; })  // store the initial angle for transition

            // Animate the donut overflow effect
            path.transition()
                .duration(1000)
                .attrTween("d", arcTween);

            // Add label in the center of the donut chart
            svg.append("text")
                .attr("class", "chart-label")
                .text(`${averageHeartbeat.toFixed(1)} bpm / ${maxHeartbeat} bpm`)
                .attr("text-anchor", "middle")
                .attr("dy", "0.35em")
                .style("font-size", "22px")
                .style("font-weight", "bold");

            // Arc tween function for smooth transition
            function arcTween(a) {
                var i = d3.interpolate(this._current, a);
                this._current = i(0);
                return function(t) { return arc(i(t)); };
            }
        }
    </script>











  
       
     <!-- Footer -->
    <footer>
        <hr>
        <p>Created by: Kseniia, Maryem, Sena, Saffree, Angelina - Sleep Med </p>

                <p>&copy; 2025 Sleep Med. All rights reserved.</p>
    </footer>
    
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
