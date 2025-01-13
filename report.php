<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sleep Statistics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #626AB2;
            color: white;
        }

        header {
            display: flex;
            flex-direction:column;
            justify-content: space-between;
            align-items: flex-start;
            padding: 20px;
            background-color: #4C57A7;
        }

        header h1 {
            margin: 0;
            font-size: 24px;
        }

        header .datetime {
            font-size: 14px;
            margin-top: 10px;
        }

        .container {
            padding: 20px;
            max-width: 1000px;
            margin: auto;
            background: white;
            color: #4C57A7;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #4C57A7;
        }

        .patient-info {
            background-color: #E8F0FE;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .patient-info p {
            margin: 5px 0;
            font-size: 16px;
        }

        .recommendation {
            background-color: #E8F0FE;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            font-size: 16px;
        }

        .chart-container {
            margin-top: 30px;
            text-align: center;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #4C57A7;
            color: white;
            margin-top: 30px;
        }

        footer hr {
            border: 0;
            border-top: 1px solid white;
            margin-bottom: 10px;
        }
        
		
		/* Bookmark Navigation */
	.bookmark-nav {
	    position: fixed;
	    top: 0;
	    left: 82%; /* Center horizontally */
	    transform: translateX(-50%);
	    z-index: 1000;
	    display: flex;
	    flex-direction: column;
	    align-items: center;
	}

	.bookmark {
	    background-color: white;
	    padding: 10px 0px;
	    border-radius: 10px 10px 100px 100px;
	    cursor: pointer;
	    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
	    text-align: center;
	    width: 120px;
	    position: relative; /* Ensures text is positioned relative to the bookmark */
	}

	.bookmark-logo {
	    max-width: 100%;
	    height: auto;
	    margin-bottom: 20px; /* Add some space below the logo */
	    transform: translateY(-5px); /* Move the logo upward */
	}

	/* Center text inside the bookmark */
	.bookmark span {
	    position: absolute;
	    top: 50%;
	    left: 50%;
	    transform: translate(-50%, -50%);
	    font-size: 14px;
	    color: #4C57A7;
	    font-weight: bold;
	    pointer-events: none; /* Prevent interactions with the text */
	}

	/* Navigation Options */
	.nav-options {
	    background-color: white;
	    color: #4C57A7;
	    text-align: center;
	    width: 100%;
	    padding: 20px 0;
	    border-radius: 0 0 100px 100px;
	    display: none; /* Initially hidden */
	    flex-direction: column;
	    position: fixed;
	    top: 60%; /* Start at the top of the page */
	    left: 0%;
	    transform: translateX(-50%);
	}

	.nav-options ul {
	    list-style: none;
	    padding: 0;
	    margin: 0;
	}

	.nav-options li {
	    margin: 10px 0;
	}

	.nav-options a {
	    text-decoration: none;
	    color: #4C57A7;
	    font-size: 18px;
	    transition: color 0.2s;
	}

	.nav-options a:hover {
	    color: #e0f7fa;
	}

	/* Slide-down animation */
	.nav-options.active {
	    display: flex; /* Show when active */
	    animation: slide-down 0.5s ease-out forwards;
	}

	@keyframes slide-down {
	    from {
		transform: translateY(-100%);
		opacity: 0;
	    }
	    to {
		transform: translateY(0);
		opacity: 1;
	    }
	}

	/* Slide-up animation */
	.nav-options.inactive {
	    animation: slide-up 0.5s ease-out forwards;
	}

	@keyframes slide-up {
	    from {
		transform: translateY(0);
		opacity: 1;
	    }
	    to {
		transform: translateY(-100%);
		opacity: 0;
	    }
	}

	/* Ensure nav is hidden after slide-up */
	.nav-options:not(.active) {
	    display: none; /* Hide the nav when not active */
	}


        
    </style>
</head>
<body>
    <!-- Header with Date and Time -->
    <header>
        <h1>Sleep Monitor App</h1>
        <div class="datetime" id="datetime"></div>
    </header>




 <div class="bookmark-nav">
	    <div class="bookmark" onclick="toggleNav()">
		<img src="images/sleep.png" alt="Logo" class="bookmark-logo">
	    </div>
	    <div class="nav-options" id="nav-options">
		<ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="register.php">Register</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="report.php">Report</a></li>
                <li><a href="statistics.php">Statistics</a></li>
		</ul>
	    </div>
	</div>
	
	<script>
	    function toggleNav() {
		const navOptions = document.getElementById('nav-options');

		if (navOptions.classList.contains('active')) {
		    // Slide up
		    navOptions.classList.remove('active');
		    navOptions.classList.add('inactive');

		    // Wait for the animation to finish, then hide the element
		    setTimeout(() => {
		        navOptions.style.display = 'none';
		    }, 500); // Match the transition duration
		} else {
		    // Slide down
		    navOptions.style.display = 'flex'; // Ensure it's visible
		    navOptions.classList.remove('inactive');
		    navOptions.classList.add('active');
		}
	    }

	    // Attach event listener to the logo
	    document.getElementById('bookmark-logo').addEventListener('click', toggleNav);
	</script>


    

    <script>
        // Automatically hide the message after 3 seconds
        window.onload = function() {
            var message = document.getElementById('message');
            if (message) {
                message.style.visibility = 'visible';
                message.style.opacity = 1;
                setTimeout(function() {
                    message.style.opacity = 0;
                    message.style.visibility = 'hidden';
                }, 3000); // Hide after 3 seconds
            }
        };
    </script>

    <!-- Main Content -->
    <div class="container">
        <h2>Sleep Statistics</h2>

        <!-- Patient Info Section -->
        <div class="patient-info">
            <p><strong>Healthcare Worker:</strong> Sarah Smith</p>
            <p><strong>Age:</strong> 29</p>
            <p><strong>Last Sleep Session:</strong> 4 hours, 30 minutes</p>
            <p><strong>Last Session Date:</strong> January 11, 2025</p>
        </div>

        <!-- Recommendation Section -->
        <div class="recommendation">
            <p><strong>Recommendation:</strong> As a healthcare worker, it’s crucial to prioritize sleep during non-shift hours to ensure optimal performance. Consider taking short naps during breaks if possible and avoid caffeine close to your next shift. Your current sleep duration is insufficient, and adequate rest is key to preventing burnout and maintaining alertness during your shifts.</p>
        </div>

        <!-- Chart Section -->
        <div class="chart-container">
            <h3>Sleep Data Breakdown</h3>
            <canvas id="sleepChart" width="350" height="175"></canvas>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <hr>
        <p>Created by: Kseniia, Maryem, Sena, Saffree, Angelina - Sleep Med </p>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Display Current Date and Time
        function updateDateTime() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            document.getElementById('datetime').innerText = now.toLocaleDateString('en-US', options);
        }
        setInterval(updateDateTime, 1000);

        // Generate Random Sleep Data (for healthcare worker who didn't sleep well)
        function generateSleepData() {
            const deepSleep = Math.floor(Math.random() * 20) + 10; // 10-30%
            const remSleep = Math.floor(Math.random() * 15) + 5; // 5-20%
            const lightSleep = 100 - (deepSleep + remSleep); // Remainder for 100%
            return [deepSleep, remSleep, lightSleep];
        }

        // Chart.js for Sleep Data
        const sleepData = generateSleepData();
        const ctx = document.getElementById('sleepChart').getContext('2d');
        const sleepChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Deep Sleep', 'REM Sleep', 'Light Sleep'],
                datasets: [{
                    label: 'Sleep Stages',
                    data: sleepData,
                    backgroundColor: ['#4C57A7', '#626AB2', '#A3A8D7']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return `${tooltipItem.label}: ${tooltipItem.raw}%`;
                            }
                        }
                    }
                },
            }
        });

       
    </script>
</body>
</html>
