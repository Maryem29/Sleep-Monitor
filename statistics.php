<?php
session_start();
require_once 'firebase.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$selectedWeek = isset($_GET['week']) ? $_GET['week'] : date('o-W');
$sleepData = get_sleep_data_by_week($userId, $selectedWeek);

$totalNapTime = 0;
$averageHeartRate = 0;
$nightDataCount = 0;
$chartData = [];

if ($sleepData) {
    foreach ($sleepData as $date => $data) {
        if (isset($data['night'])) {
            $nightRecords = $data['night'];
            $totalNaps = count($nightRecords);
            $totalNapTime += $totalNaps * (20 / 60);

            $heartRateSum = array_sum($nightRecords);
            $averageHeartRate += $heartRateSum / $totalNaps;
            $nightDataCount++;

            $chartData[] = [
                'date' => $date,
                'totalHours' => $totalNaps * (20 / 60)
            ];
        }
    }

    if ($nightDataCount > 0) {
        $averageHeartRate = round($averageHeartRate / $nightDataCount, 2);
    }
}



// Get the current page name
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sleep Statistics</title>
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











        .container {
            width: 90%;
            max-width: 1000px;
            background: white;
            border-radius: 10px;
            padding: 30px;
            color: black;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .info {
            text-align: center;
            margin-bottom: 20px;
        }


        .info p {
            font-size: 1.5em;
            font-weight: bold;
            color: #4C57A7;
        }

        .container h1 {
            text-align: center;
            color: #4C57A7;
        }

        form {
            text-align: center;
            margin: 20px 0;
        }

        form input {
            padding: 10px;
            font-size: 16px;
        }

        form button {
            padding: 10px 20px;
            background-color: #4C57A7;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .chart-container {
            margin-top: 20px;
        }

        svg {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 10px;
            max-width: 100%;
            height: auto;
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
        <h1>Sleep Statistics</h1>
        <div class="info">
            <p>Average Nap Time: <?= round($totalNapTime / 7, 2) ?> hours/day</p>
            <p>Average Heart Rate: <?= $averageHeartRate ?> BPM</p>
        </div>
        <form method="GET">
            <label for="week">Select a Week:</label>
            <input type="week" id="week" name="week" value="<?= $selectedWeek ?>">
            <button type="submit">View</button>
        </form>
        <div class="chart-container">
            <svg id="chart" width="700" height="400"></svg>
        </div>
    </div>

    <script src="https://d3js.org/d3.v7.min.js"></script>
    <script>
    const data = <?= json_encode($chartData) ?>;

    const svg = d3.select("#chart");
    const width = +svg.attr("width");
    const height = +svg.attr("height");
    const margin = { top: 20, right: 30, bottom: 70, left: 50 }; // Adjusted margins for centering

    // Define the x-scale
    const x = d3.scaleBand()
        .domain(data.map(d => d.date))
        .range([margin.left, width - margin.right]) // Space bars evenly within the full width
        .padding(0.4); // Adjusted padding to space bars better

    // Define the y-scale
    const y = d3.scaleLinear()
        .domain([0, 12]) // Adjusted to scale from 1 to 12 hours
        .nice()
        .range([height - margin.bottom, margin.top]);

    // Add bars to the chart
    svg.append("g")
        .selectAll("rect")
        .data(data)
        .join("rect")
        .attr("x", d => x(d.date))
        .attr("y", d => y(d.totalHours))
        .attr("height", d => y(0) - y(d.totalHours))
        .attr("width", x.bandwidth())
        .attr("fill", "#4C57A7");

    // Add x-axis
    svg.append("g")
        .attr("transform", `translate(0,${height - margin.bottom})`)
        .call(d3.axisBottom(x).tickSizeOuter(0))
        .selectAll("text")
        .style("text-anchor", "middle") // Align labels properly
        .attr("transform", "translate(0,10)"); // Shift labels slightly down for better visibility

    // Add y-axis
    svg.append("g")
        .attr("transform", `translate(${margin.left},0)`)
        .call(d3.axisLeft(y).ticks(12));

    // Add x-axis label
    svg.append("text")
        .attr("x", width / 2)
        .attr("y", height - 20)
        .attr("text-anchor", "middle")
        .style("fill", "#4C57A7") // Set text color
        .style("font-size", "20px") // Set font size
        .text("Date");

    // Add y-axis label
    svg.append("text")
        .attr("x", -height / 2)
        .attr("y", 15)
        .attr("transform", "rotate(-90)")
        .attr("text-anchor", "middle")
        .style("fill", "#4C57A7") // Set text color
        .style("font-size", "20px") // Set font size
        .text("Total Nap Time (Hours)");

    // Adjust SVG container to center content
    svg.attr("style", `display: block; margin: 0 auto;`);
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