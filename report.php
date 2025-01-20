<?php
session_start();

// Include Firebase PHP configuration
require 'firebase.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$userId = $_SESSION['user_id'];

// Retrieve user data from Firebase
$user_data = get_user_data($userId);

// Set default values for placeholders
$defaultText = 'Not available';
$userName = !empty($user_data['username']) ? $user_data['username'] : $defaultText;
$userSurname = !empty($user_data['surname']) ? $user_data['surname'] : $defaultText;

// Example report data (replace this with your Firebase logic)
$totalSleepHours = 7; // Example value
$reportSummary = "You had $totalSleepHours hours of good sleep last night.";

// Get the current page name
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sleep Report</title>
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

.settings-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    display: none;
    justify-content: space-between;
    padding: 20px;
    z-index: 1000;
    color: white;
}

.settings-menu {
    flex: 1;
    max-width: 20%;
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

.container {
    padding: 20px;
    max-width: 800px;
    margin: auto;
    width: 90%;
    color: #4C57A7;
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

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
    function updateDateTime() {
        const date = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
        const formattedDateTime = date.toLocaleString('en-US', options);
        document.getElementById('currentDateTime').textContent = formattedDateTime;
    }

    setInterval(updateDateTime, 1000);
    updateDateTime();
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

<!-- Content -->
<div class="container">
    <h2>Sleep Report</h2>
    <p><strong>Name:</strong> <?= htmlspecialchars($userName) ?></p>
    <p><strong>Surname:</strong> <?= htmlspecialchars($userSurname) ?></p>
    <p><?= htmlspecialchars($reportSummary) ?></p>
</div>

<!-- Footer -->
<footer>
    <hr>
    <p>Created by: Kseniia, Maryem, Sena, Safreena, Angelina - Sleep Med</p>
</footer>

<script>
    document.getElementById("logout-btn").addEventListener("click", function () {
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = "login.php";
        }
    });
</script>

</body>
</html>

