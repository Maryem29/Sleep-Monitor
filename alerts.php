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
    <script src="https://www.gstatic.com/firebasejs/9.x.x/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.x.x/firebase-firestore-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.x.x/firebase-auth-compat.js"></script>
    
    <!-- Your firebase config script -->
    <script src="firebase-config.js"></script>

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







        /* Alerts Specific Styles */
        .alerts-container {
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
        }

        .alert-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            color: #4C57A7;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .alert-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .alert-title {
            font-size: 1.2em;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-toggle {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .alert-toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: #4C57A7;
        }

        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }

        .alert-settings {
            display: none;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .alert-settings.active {
            display: block;
        }

        .time-picker {
            margin: 10px 0;
        }

        .repeat-options {
            margin: 10px 0;
        }

        .sound-toggle {
            margin: 10px 0;
        }

        .save-btn {
            background-color: #4C57A7;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        .save-btn:hover {
            background-color: #3a4580;
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

            .alerts-container {
                width: 95%;
                padding: 10px;
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

    <!-- Alerts Container -->
    <div class="alerts-container">
        <h2 style="color: white; margin-bottom: 20px;">Alert Settings</h2>
        
        <!-- Water Intake Alert -->
        <div class="alert-card">
            <div class="alert-header">
                <div class="alert-title">
                    💧 Water Intake Reminder
                </div>
                <label class="alert-toggle">
                    <input type="checkbox" class="alert-checkbox" data-target="water-settings">
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="alert-settings" id="water-settings">
                <div class="time-picker">
                    <label>First Reminder Time:</label>
                    <input type="time" name="water-time">
                </div>
                <div class="repeat-options">
                    <label>Repeat Every:</label>
                    <select name="water-repeat">
                        <option value="1">1 hour</option>
                        <option value="2">2 hours</option>
                        <option value="3">3 hours</option>
                        <option value="4">4 hours</option>
                    </select>
                </div>
                <div class="sound-toggle">
                    <label>
                        <input type="checkbox" name="water-sound"> Enable Sound
                    </label>
                </div>
                <button class="save-btn">Save Settings</button>
            </div>
        </div>

        <!-- Sleep Reminder Alert -->
        <div class="alert-card">
            <div class="alert-header">
                <div class="alert-title">
                    🌙 Sleep Time Reminder
                </div>
                <label class="alert-toggle">
                    <input type="checkbox" class="alert-checkbox" data-target="sleep-settings">
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="alert-settings" id="sleep-settings">
                <div class="time-picker">
                    <label>Bedtime Reminder:</label>
                    <input type="time" name="sleep-time">
                </div>
                <div class="sound-toggle">
                    <label>
                        <input type="checkbox" name="sleep-sound"> Enable Sound
                    </label>
                </div>
                <button class="save-btn">Save Settings</button>
            </div>
        </div>

        <!-- Nap Reminder Alert -->
        <div class="alert-card">
            <div class="alert-header">
                <div class="alert-title">
                    😴 Nap Reminder
                </div>
                <label class="alert-toggle">
                    <input type="checkbox" class="alert-checkbox" data-target="nap-settings">
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="alert-settings" id="nap-settings">
                <div class="time-picker">
                    <label>Nap Time:</label>
                    <input type="time" name="nap-time">
                </div>
                <div class="sound-toggle">
                    <label>
                        <input type="checkbox" name="nap-sound"> Enable Sound
                    </label>
                </div>
                <button class="save-btn">Save Settings</button>
            </div>
        </div>
    </div>



    <!-- Scripts -->
    <script>
        


        // Load existing alert settings when page loads
        window.onload = function () {
            // Set up the toggle buttons to toggle alert settings
            const toggles = document.querySelectorAll(".alert-toggle input");

            toggles.forEach(toggle => {
                toggle.addEventListener("change", function () {
                    const targetId = this.getAttribute('data-target');
                    const settingsPanel = document.getElementById(targetId);
                    settingsPanel.classList.toggle("active", this.checked);
                });

                // Initialize toggles based on existing alert state (this can be fetched from a database if required)
                const targetId = toggle.getAttribute('data-target');
                const settingsPanel = document.getElementById(targetId);
                settingsPanel.classList.toggle("active", toggle.checked);
            });
        };
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
