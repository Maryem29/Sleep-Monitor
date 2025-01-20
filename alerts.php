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
            margin-top: 5px;
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

        /* Settings Button */
        .settings-button {
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            width: 50px;
            height: 50px;
            padding: 12px;
            border-radius: 50%;
            transition: background-color 0.3s ease;
            background-color: white;
        }

        .settings-button:hover {
            background-color: rgba(0, 0, 0, 0.1);
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
            flex-wrap: wrap;
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
            width: 100%;
        }

        footer hr {
            border: 0;
            border-top: 1px solid white;
            margin-bottom: 10px;
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

            .nav-link {
                font-size: 16px;
                padding: 8px 10px;
            }

            .alerts-container {
                width: 95%;
                padding: 10px;
            }
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

    <!-- Footer -->
    <footer>
        <hr>
        <p>Created by: Kseniia, Maryem, Sena, Saffree, Angelina - Sleep Med </p>
    </footer>

    <!-- Scripts -->
    <script>
        // Update the date and time dynamically (remains the same)
        function updateDateTime() {
            const date = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
            const formattedDateTime = date.toLocaleString('en-US', options);
            document.getElementById('currentDateTime').textContent = formattedDateTime;
        }

        setInterval(updateDateTime, 1000);
        updateDateTime();

        // Handle logout with Firebase
        document.getElementById("logout-btn").addEventListener("click", function () {
            if (confirm("Are you sure you want to log out?")) {
                firebase.auth().signOut().then(() => {
                    window.location.href = "login.php";
                }).catch((error) => {
                    console.error("Error signing out:", error);
                });
            }
        });

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
</body>
</html>
