<?php
session_start(); // Start the session

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'firebase.php';  // Ensure your Firebase PHP SDK is included

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

$userId = $_SESSION['user_id']; // Or another method to get the current user's ID

// Retrieve user data from Firebase
$user_data = get_user_data($userId);

// Set default values for placeholders
$defaultText = 'Not available';

// Use the data if available, or fall back to placeholders
$userName = !empty($user_data['username']) ? $user_data['username'] : $defaultText;
$userSurname = !empty($user_data['surname']) ? $user_data['surname'] : $defaultText;
$userAge = !empty($user_data['age']) ? $user_data['age'] : $defaultText;

// Fetch sleep data for today from Firebase
$sleepData = get_sleep_data($userId);

// Get today's date in 'YYYY-MM-DD' format
$todayDate = date("Y-m-d");
$todaySleepData = isset($sleepData[$todayDate]) ? $sleepData[$todayDate] : null;

// Function to format today's sleep data
function formatTodaySleepData($sleepData) {
    return !empty($sleepData) ? $sleepData['sleepDuration'] : 0; // Return the sleep duration for today
}

$todaySleepDuration = formatTodaySleepData($todaySleepData);

// Aggregate the sleep data by week
$weeklyData = aggregateSleepDataByWeek($sleepData);

// Function to aggregate sleep data by week
function aggregateSleepDataByWeek($sleepData) {
    $weeklyData = [];
    foreach ($sleepData as $date => $data) {
        $weekNumber = getWeekNumber(new DateTime($date)); // Assuming sleep data has timestamp as key
        if (!isset($weeklyData[$weekNumber])) {
            $weeklyData[$weekNumber] = [
                'sleepDuration' => 0,
                'overnightShifts' => 0,
                'sleepQuality' => 0,
                'count' => 0
            ];
        }
        $weeklyData[$weekNumber]['sleepDuration'] += $data['sleepDuration'];
        $weeklyData[$weekNumber]['overnightShifts'] += $data['overnightShifts'];
        $weeklyData[$weekNumber]['sleepQuality'] += $data['sleepQuality'];
        $weeklyData[$weekNumber]['count'] += 1;
    }

    // Calculate averages
    foreach ($weeklyData as $week => $data) {
        $weeklyData[$week]['sleepDuration'] /= $data['count'];
        $weeklyData[$week]['sleepQuality'] /= $data['count'];
    }

    return $weeklyData;
}

// Function to get week number from date
function getWeekNumber($date) {
    $startDate = new DateTime($date->format('Y-01-01'));
    $diff = $date->diff($startDate);
    $dayOfYear = $diff->days;
    return intdiv($dayOfYear, 7) + 1;
    
 
}


include 'firebase_sleep_data.php'; // Include the renamed function file

// Fetch sleep data for the user
$sleep_data = get_user_sleep_data($userId);

// Check if sleep data exists
if ($sleep_data === null) {
    // Handle the case where no data is available
} else {
    // Process the data as needed
}


// Check if sleep data exists
if ($sleep_data === null) {
    $sleep_data_message = "No sleep data available for today.";
    $total_sleep_time = 0;
    $awake_time = 24;
    $sleep_stages = ['Light' => 0, 'Deep' => 0, 'REM' => 0]; // Default empty values
} else {
    // Process the sleep data
    $processed_data = process_sleep_data($sleep_data);
    $total_sleep_time = $processed_data['total_sleep_time'];
    $awake_time = $processed_data['awake_time'];
    $sleep_stages = $processed_data['sleep_stages'];
}

// Prepare data for the pie chart (sleep stages + awake)
$chart_data = [
    'labels' => ['Light Sleep', 'Deep Sleep', 'REM Sleep', 'Awake'],
    'data' => [
        $sleep_stages['Light'],  // Light Sleep
        $sleep_stages['Deep'],   // Deep Sleep
        $sleep_stages['REM'],    // REM Sleep
        $awake_time              // Awake Time
    ]
];

// Render Pie Chart
echo "<div class='pie-chart'>";
echo "<canvas id='pieChart'></canvas>";
echo "</div>";


$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sleep Statistics</title>
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <style>
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

        .container {
            padding: 20px;
            max-width: 1000px;
            margin: auto;
            width: 90%;
            color: #4C57A7;
            border-radius: 10px;
            background: #E2E8F0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #4C57A7;
        }

        .patient-info p {
            font-size: 18px;
            margin: 5px 0;
        }

        .patient-info p strong {
            color: #4C57A7;
        }

        .pie-chart {
            width: 300px;
            height: 300px;
            margin: 30px auto;
        }

        .chart-container {
            margin-top: 30px;
            text-align: center;
            color: white;
        }

        /* Weekly Data Section */
        .weekly-report {
            background-color: #E2E8F0;
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
            font-size: 16px;
            color: #4C57A7;
        }

        .weekly-report table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .weekly-report th, .weekly-report td {
            padding: 10px;
            text-align: center;
            border: 1px solid #4C57A7;
        }

        .weekly-report th {
            background-color: #3745aa;
            color: white;
        }

        .weekly-report td {
            background-color: #f4f4f4;
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

<!-- Sleep Statistics Section -->
<div class="container">
    <h2>Sleep Statistics</h2>

    <!-- Patient Info Section -->
    <div class="patient-info">
        <p><strong>Healthcare Worker:</strong> <?= $userName . " " . $userSurname ?></p>
        <p><strong>Age:</strong> <?= $userAge ?></p>
        
        <!-- Today's Sleep Duration -->
        <p><strong>Today's Sleep Duration:</strong> <?= $total_sleep_time ? $total_sleep_time . " hours" : "No data available" ?></p>
    </div>

    <!-- Weekly Report Section -->
    <div class="weekly-report">
        <h3>Weekly Report</h3>
        <table>
            <tr>
                <th>Week #</th>
                <th>Sleep Duration (hrs)</th>
                <th>Sleep Quality</th>
                <th>Overnight Shifts</th>
            </tr>
            <?php if (!empty($weeklyData)): ?>
                <?php foreach ($weeklyData as $weekNumber => $data): ?>
                <tr>
                    <td><?= $weekNumber ?></td>
                    <td><?= $data['sleepDuration'] ?: '-' ?></td>
                    <td><?= $data['sleepQuality'] ?: '-' ?></td>
                    <td><?= $data['overnightShifts'] ?: '-' ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">No data available for this week.</td></tr>
            <?php endif; ?>
        </table>
    </div>

    <!-- Sleep Stages Pie Chart -->
    <div class="chart-container">
        <h3>Today's Sleep Stages</h3>
        <div id="pie-chart" class="pie-chart"></div>
    </div>
</div>


<script>
// Pass the processed data to JavaScript
var chartData = <?php echo json_encode($chart_data); ?>;

var ctx = document.getElementById('pieChart').getContext('2d');
var pieChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: chartData.labels,
        datasets: [{
            data: chartData.data,
            backgroundColor: ['#FFEB3B', '#2196F3', '#9C27B0', '#FF9800'], // Custom colors
        }]
    }
});
</script>

</body>
</html>
