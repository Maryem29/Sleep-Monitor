<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
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
	    max-width: 30%; /* Optional: Restrict max width */
	    /*background: linear-gradient(to left, #748ac7, #4C57A7);*/
	    background: linear-gradient(to right, #616cbb, #748ac7);
	    padding: 20px;
	    border-radius: 10px;
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
	    font-size: 16px;
	    padding: 5px;
	    transition: color 0.3s, background-color 0.3s;
	    border-radius: 5px;
	}

	.settings-menu a:hover {
	    color: #2C3E99;
	    background-color: #D1D9F1;
	}

	/* About Us Section (Right Section) */
	.about-us {
	    flex: 2; /* Right section takes 70% */

            background: linear-gradient(to right, #616cbb, #748ac7);
	    padding: 20px;
	    border-radius: 10px;
	    box-shadow: 2px 2px 15px rgba(0, 0, 0, 0.5);
	    color: #ffffff;
	}

	.about-us h2 {
	    margin-top: 0;
	    color: #E2E8F0;
	    font-weight: bold;
	    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
	}

	.about-us p {
	    font-size: 16px;
	    line-height: 1.6;
	    color: #f1f4fa;
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






        /* Content Styles */
	.statistics-box, .chart-container {
	    width: 90%; /* Adjusts to the screen size */
	    max-width: 1200px;
	    margin: 20px 0;
	    padding: 20px;
	    border-radius: 10px;
	    background: #E2E8F0;
	    color: #4C57A7;
	}

	.bar-chart {
	    justify-content: space-around;
	    align-items: flex-end;
	    height: 200px;
	    display: flex;
	}
            


        .bar {
            width: 40px;
            background-color: #4C57A7;
            border-radius: 5px;
            text-align: center;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        .bar span {
            margin-top: 5px;
            font-size: 12px;
        }

        .chart-labels {
            display: flex;
            justify-content: space-around;
            margin-top: 10px;
        }

        .chart-labels span {
            font-size: 14px;
            font-weight: bold;
        }

        .recommendations {
            text-align: center;
            margin-top: 20px;
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
            <li><a href="#">App Information</a></li>
        </ul>
    </div>

    <!-- About Us Section -->
    <div class="about-us">
        <h2>About Us</h2>
        <p>We are dedicated to improving sleep quality through data-driven insights. Our mission is to provide accurate, user-friendly tools to help you sleep better.</p>
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

    <!-- Main Content -->
    <h1>Sleep Statistics</h1>
    <div class="statistics-box">
        <p><strong>Average Sleep Duration:</strong> <span id="avg-sleep">6 hours 30 minutes</span></p>
        <p><strong>Sleep Efficiency:</strong> <span id="sleep-efficiency">78%</span></p>
        <p><strong>Deep Sleep:</strong> <span id="deep-sleep">1 hour 45 minutes</span></p>
    </div>

    <div class="chart-container">
            <h2 style="text-align: center; color: #4C57A7;">Sleep Trends Over Time</h2>
            <div class="bar-chart">
                <div class="bar" style="height: 40%;"><span>4 hrs</span></div>
                <div class="bar" style="height: 60%;"><span>6 hrs</span></div>
                <div class="bar" style="height: 80%;"><span>8 hrs</span></div>
                <div class="bar" style="height: 50%;"><span>5 hrs</span></div>
                <div class="bar" style="height: 70%;"><span>7 hrs</span></div>
            </div>
            <div class="chart-labels">
                <span>Mon</span>
                <span>Tue</span>
                <span>Wed</span>
                <span>Thu</span>
                <span>Fri</span>
            </div>
        </div>

        <!-- Recommendations Section -->
        <div class="statistics-box">
            <h3>Recommendations</h3>
            <p>Try to improve your sleep duration to 7+ hours for better recovery.</p>
            <p>Consider adjusting your sleep environment for better rest during shifts.</p>
        </div>
        
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

        // Update recommendations dynamically
        document.getElementById("submit-data").addEventListener("click", function () {
            // Generate random sleep stats and update the DOM
            const avgSleepHours = Math.floor(Math.random() * 4) + 4;
            const avgSleepMinutes = Math.floor(Math.random() * 60);
            document.getElementById('avg-sleep').textContent = `${avgSleepHours} hours ${avgSleepMinutes} minutes`;
        });
        
        
        
        
	window.addEventListener('resize', () => {
	    const bars = document.querySelectorAll('.bar');
	    bars.forEach(bar => {
		bar.style.height = `${Math.random() * 80 + 20}%`;
	    });
	});

		
		
        
    </script>
</body>
</html>
