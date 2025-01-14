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
    <title>Navigation Bar with Settings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        /* Header styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #ffffff;
            border-bottom: 2px solid #eaeaea;
        }

        /* Navigation bar styles */
        .nav-container {
            flex: 1;
            display: flex;
            justify-content: center;
        }

        .nav-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            width: 100%;
            max-width: 80%;
        }

        .nav-menu li {
            flex-grow: 1;
            text-align: center;
        }

        .nav-link {
            text-decoration: none;
            color: #4C57A7;
            font-size: 18px;
            padding: 10px 15px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .nav-link:hover {
            background-color: #D1D9F1;
            color: #2C3E99;
            border-radius: 5px;
        }

        .nav-link.active {
            color: #2C3E99;
            font-weight: bold;
            background-color: #D1D9F1;
            border-radius: 5px;
        }

        /* Logout button */
        .logout-settings-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logout-button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: white;
            color: #4C57A7;
            border: 1px solid #4C57A7;
            border-radius: 5px;
            cursor: pointer;
        }

        .logout-button:hover {
            background-color: #E2E8F0;
        }

        /* Settings button (three dots) */
        .settings-button {
            font-size: 24px;
            background: none;
            border: none;
            cursor: pointer;
            color: #4C57A7;
        }

        .settings-button:hover {
            color: #2C3E99;
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
        background-color: #4C57A7;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);
    }

    .settings-menu h2 {
        color: #ffffff;
        margin-top: 0;
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
        color: #D1D9F1;
        font-size: 16px;
    }

    .settings-menu a:hover {
        text-decoration: underline;
    }

    /* About Us Section (Right Section) */
    .about-us {
        flex: 2; /* Right section takes 70% */
        background-color: #626AB2;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);
    }

    .about-us h2 {
        margin-top: 0;
        color: #ffffff;
    }

    .about-us p {
        font-size: 16px;
        line-height: 1.6;
    }

    /* Close button */
    .close-settings {
        background: none;
        border: none;
        font-size: 18px;
        color: #ffffff;
        cursor: pointer;
        margin-bottom: 20px;
    }
    /* Active overlay display */
    .settings-overlay.active {
        display: flex; /* Flex layout is only applied when active */
    
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="nav-container">
            <nav>
                <ul class="nav-menu">
                    <li><a href="statistics.php" class="nav-link <?php echo ($current_page === 'statistics.php') ? 'active' : ''; ?>">Statistics</a></li>
                    <li><a href="report.php" class="nav-link <?php echo ($current_page === 'report.php') ? 'active' : ''; ?>">Report</a></li>
                    <li><a href="sleep.php" class="nav-link <?php echo ($current_page === 'sleep.php') ? 'active' : ''; ?>">Sleep</a></li>
                    <li><a href="alerts.php" class="nav-link <?php echo ($current_page === 'alerts.php') ? 'active' : ''; ?>">Alerts</a></li>
                    <li><a href="profile.php" class="nav-link <?php echo ($current_page === 'profile.php') ? 'active' : ''; ?>">Profile</a></li>
                </ul>
            </nav>
        </div>
        <div class="logout-settings-container">
            <button id="logout-btn" class="logout-button">Logout</button>
            <button class="settings-button" id="settings-btn">⋮</button>
        </div>
    </div>

    <!-- Settings Overlay -->
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

        // Logout confirmation
        document.getElementById("logout-btn").addEventListener("click", function () {
            const userConfirmed = confirm("Are you sure you want to log out?");
            if (userConfirmed) {
                window.location.href = "login.php";
            }
        });
    </script>
</body>
</html>
