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
	
	
	
	/* Go Back Button Styles */
.go-back-button {
    padding: 10px 20px;
    background-color: #4C57A7;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 20px;
}

.go-back-button:hover {
    background-color: #3B4A8A;
}

	
	
        /* Container */
        .container {
            margin: 100px auto;
            padding: 20px;
            max-width: 1200px;
            display: flex;
            flex-direction: column;
            gap: 40px;
        }

        /* Info Section */
        .info-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 40px;
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Add transition */
        }

        .info-section:hover {
            transform: scale(1.05); /* Slightly increase size on hover */
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2); /* Increase shadow on hover */
        }
        .combined-box:hover {
            transform: scale(1.05); /* Slightly increase size on hover */
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2); /* Increase shadow on hover */
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Add smooth transition */
        }

        .info-section.reverse {
            flex-direction: row-reverse;
        }

        /* Text Box */
        .text-box {
            flex: 1;
            color: #4C57A7;
            font-size: 18px;
        }

        .text-box h2 {
            font-size: 28px;
            margin-bottom: 15px;
            color: #4C57A7;
        }

        .text-box p, .text-box ul {
            line-height: 1.8;
        }

        /* Combined White Box */
        .combined-box {
            background-color: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Blue Box Inside the Combined White Box */
        .blue-box {
            background-color: #2E4A7D; /* Darker Blue */
            color: white;
            border-radius: 10px;
            padding: 80px;
            padding-bottom: px;
            margin: 40px 0px; /* Space around the blue box */

             /* Box width */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            font-size: 18px;
            line-height: 1.9;
            overflow: hidden; /* Ensure smooth expansion */
            word-wrap: break-word; /* Allow long words to break */

        }
        .blue-box h2 {
            font-size: 24px;
            margin-bottom: 15px;
            }

        .blue-box h3 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .blue-box ul {
            list-style: none;
            padding: 0;
        }

        .blue-box ul li {
            margin: 10px 0;
        }

        /* Connections Section */
        .connections-section {
            margin-top: 60px;
             /* Larger gap before Connections heading */
        }

        .connections-section h2 {
            margin-bottom: 20px;
        }

        .connection-graphic {
            display:flex;
            justify-content:center
            align-items:center
            text-align: center;
            margin: 0px auto;
        }

        .connection-graphic img {
            max-width: 100%;
            height: auto;
            display:block;
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
		flex-direction: column; 
		gap: 20px;
	    }
	    
	    .team-section {
		grid-template-columns: repeat(2, 1fr); 
	    }

	    .settings-menu, .about-us {
		max-width: 100%; 
		flex: none;
	    }

	    .bar-chart {
		height: 150px;
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
    // Open settings overlay
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


<!-- Go Back Button -->
<button onclick="goBack()" class="go-back-button">Go Back</button>

<script>
    // Function to go back to the previous page
    function goBack() {
        window.history.back();
    }
</script>






    <div class="container">
        <!-- Section 1 -->
        <div class="info-section">
            <div class="text-box">
                <h2>Sleep Med</h2>
                <p> A real-time monitoring app powered by a chest sensor for accurate health and sleep insights.</p>
            </div>
            <div class="image-box">
                <img src="images/pic2.jpg" alt="Doctors resting">
            </div>
        </div>

        <!-- Section 2 -->
        <div class="info-section reverse">
            <div class="text-box">
                <h2>Night Shift Workers</h2>
                 <p> Tailored specifically for night shift healthcare workers to monitor and improve sleep quality and health.</p>
            </div>
            <div class="image-box">
                <img src="images/pic3.jpg" alt="Key features">
            </div>
        </div>

        <!-- Section 3 -->
        <div class="info-section">
            <div class="text-box">
                <h2> Better Care for Patients</h2>
                <p>Enhancing healthcare providers' performance for improved patient outcomes</p>
            </div>
            <div class="image-box">
                <img src="images/pic4.jpg" alt="Getting started">
            </div>
        </div>

        <!-- Combined Section -->
        <div class="combined-box">
            <h2 style="color: #4C57A7;">Sleep Med</h2>
            <div class="features-section">
                <ul class="feature-list" style="list-style: none; padding: 0; margin: 20px 0;">
                    <li style="display: flex; align-items: center; margin-bottom: 15px;">
                        <div style="width: 10px; height: 10px; background-color: #4C57A7; border-radius: 50%; margin-right: 10px;"></div>
                        <span style="color: #4C57A7;">Real-Time Health Monitoring</span>
                    </li>
                    <li style="display: flex; align-items: center; margin-bottom: 15px;">
                        <div style="width: 10px; height: 10px; background-color: #4C57A7; border-radius: 50%; margin-right: 10px;"></div>
                        <span style="color: #4C57A7;">Weekly and Monthly Reports</span>
                    </li>
                    <li style="display: flex; align-items: center; margin-bottom: 15px;">
                        <div style="width: 10px; height: 10px; background-color: #4C57A7; border-radius: 50%; margin-right: 10px;"></div>
                        <span style="color: #4C57A7;">Personalized Sleep Insights</span>
                    </li>
                </ul>
            </div>

            <!-- Blue Box -->
            <div class="blue-box">
                Healthcare  providers  on  night  shifts  face  significant  challenges,  including disrupted circadian rhythms, high stress levels,
                and demanding workloads, leading to poor sleep quality, burnout, and compromised patient care.
                To address these issues, the app utilizes the **BITalino sensor**, which attaches to the healthcare worker's
                chest to capture real-time **ECG (Electrocardiogram)** data.
                this data offers insights into heart rate variability, stress levels, and sleep patterns.
                Through the app, users can monitor real-time metrics and access detailed weekly and monthly reports,
                enabling them to optimize recovery, improve sleep quality, and enhance their performance during night shifts,
                ultimately benefiting both healthcare providers and their patients.
            </div>

            <!-- Connections Section -->
            <div class="connections-section">
                <h2 style="color: #4C57A7; text-align: center;">Connections</h2>
                <p style="color: #4C57A7; text-align: center;">MedSleep is connected through sensors to doctors, and then the data can be accessed on phones</p>
                <div class="connection-graphic" style="display: flex; justify-content: center; align-items: center; margin: 20px auto;">
                    <img src="images/graphic.png" alt="Connections Graphic" style="max-width: 100%; height: auto; display: block;">
                </div>
            </div>
        </div>
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
     </script>
</body>
</html>
