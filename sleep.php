<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation Bar</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .navbar {
            display: flex;
            justify-content: center;
            background-color: #4C57A7;
            padding: 15px 0;
        }

        .navbar a {
            color: white;
            font-size: 18px;
            text-decoration: none;
            padding: 10px 20px;
            margin: 0 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .navbar a:hover {
            background-color: #37497A;
        }

        .container {
            text-align: center;
            margin-top: 50px;
        }

        .container h1 {
            color: #4C57A7;
        }

        .logout-btn {
            margin-top: 30px;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4C57A7;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .logout-btn:hover {
            background-color: #37497A;
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <div class="navbar">
        <a href="profile.php">Profile</a>
        <a href="report.php">Report</a>
        <a href="statistics.php">Statistics</a>
    </div>

    <div class="container">
        <h1>Welcome to Your Dashboard</h1>
        <p>You are logged in!</p>

        <!-- Logout Button -->
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

</body>
</html>
