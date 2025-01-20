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

.container {
    padding: 20px;
    max-width: 800px;
    margin: auto;
    width: 90%;
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    color: #4C57A7;
}

.statistics-container {
    text-align: center;
    margin-bottom: 30px;
}

.bar-chart-container {
    padding: 20px;
    background: #f0f4fa;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

.recommendations {
    background: #f0f4fa;
    padding: 20px;
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
        <li><a href="statistics.php" class="nav-link active">Statistics</a></li>
        <li><a href="report.php" class="nav-link">Report</a></li>
        <li><a href="sleep.php" class="nav-link">Sleep</a></li>
        <li><a href="alerts.php" class="nav-link">Alerts</a></li>
        <li><a href="profile.php" class="nav-link">Profile</a></li>
    </ul>
</div>

<!-- Content -->
<div class="container">
    <div class="statistics-container">
        <h2>Sleep Statistics</h2>
        <p><strong>Average Sleep Duration:</strong> 6 hours 30 minutes</p>
        <p><strong>Sleep Efficiency:</strong> 78%</p>

    </div>

    <div class="bar-chart-container">
        <h3>Sleep Trends Over Time</h3>
        <canvas id="sleepTrendsChart"></canvas>
    </div>

    <div class="recommendations">
        <h3>Recommendations</h3>
        <p>Try to improve your sleep duration to 7+ hours for better recovery.</p>
        <p>Consider adjusting your sleep environment for better rest during shifts.</p>
    </div>
</div>

<!-- Footer -->
<footer>
    <hr>
    <p>Created by: Kseniia, Maryem, Sena, Saffree, Angelina - Sleep Med</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('sleepTrendsChart').getContext('2d');
    const sleepTrendsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
            datasets: [{
                label: 'Sleep Duration (hours)',
                data: [4, 6, 8, 5, 7],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Hours'
                    }
                }
            }
        }
    });
</script>

<script>
    document.getElementById("logout-btn").addEventListener("click", function () {
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = "login.php";
        }
    });
</script>

</body>
</html>







