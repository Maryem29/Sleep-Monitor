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
            background: linear-gradient(to right, #4C57A7, #1E3A8A); /* Blue and purple gradient */
            color: #fff;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
        }

        .container {
            width: 80%;
            max-width: 900px;
            background: white;
            color: black;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            position: relative;
        }
        
        
        header {
            width: 100%;
            background-color: linear-gradient(to right, #4C57A7, #1E3A8A); /* Blue and purple gradient */
            flex-direction:column;
            text-align: left;
            justify-content: space-between;
            align-items: flex-start;
            padding: 20px;
            /*background-color: #4C57A7;*/
            color:white;
        }

        header h0 {
            margin: 20px;
            font-size: 24px;
            font-weight: bold;
        }

        header .datetime {
            font-size: 14px;
            margin-top: 10px;
            margin-left:20px;
            
        }
        

        h1 {
            text-align: center;
            color: #4C57A7;
            margin-bottom: 20px;
        }

        .date-time {
            font-size: 16px;
            font-weight: bold;
            color: purple; /* Purple font for date and time */
            position: absolute;
            top: 10px;
            right: 20px;
        }

        .statistics-box {
            background: #E2E8F0;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        .chart-container {
            margin: 20px 0;
        }

        .bar-chart {
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
            height: 200px;
            background: #E2E8F0;
            padding: 20px;
            border-radius: 10px;
            position: relative;
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

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            background: #4C57A7;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            margin-top: 20px;
            text-align: center;
        }

        .back-button:hover {
            background: #37497A;
        }

        .creator-credit {
            font-size: 14px;
            text-align: center;
            color: #4C57A7;
            margin-top: 20px;
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
        <h0>Sleep Monitor App</h0>
        <div class="datetime" id="datetime"></div>
    </header>

    <div class="container">
        <!-- Date and Time -->
        <div class="date-time" id="currentDateTime"></div>

        <h1>Sleep Statistics</h1>

        <!-- Sleep Overview Section -->
        <div class="statistics-box">
            <div><strong>Average Sleep Duration:</strong> 6 hours 30 minutes</div>
            <div><strong>Sleep Efficiency:</strong> 78%</div>
            <div><strong>Deep Sleep:</strong> 1 hour 45 minutes</div>
        </div>

        <!-- Sleep Trends Bar Graph -->
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

        <!-- Back Button -->
        <a href="dashboard.html" class="back-button">Back to Dashboard</a>

        <!-- Creator Credits -->
        <div class="creator-credit">
            Created by: Kseniia, Maryem, Saffree, Sena, Angelina
- Sleep Med
        </div>
    </div>
    
    
    
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

    <script>
        // Display Current Date and Time
        function updateDateTime() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            document.getElementById('datetime').innerText = now.toLocaleDateString('en-US', options);
        }
        setInterval(updateDateTime, 1000);
    </script>
</body>
</html>
