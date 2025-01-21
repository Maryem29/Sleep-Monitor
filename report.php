<?php
session_start();
require_once 'firebase.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$sleepData = get_all_sleep_data($userId);

// Extract available months from sleep data
$availableMonths = [];
if ($sleepData) {
    foreach ($sleepData as $date => $data) {
        $month = date('Y-m', strtotime($date));
        if (!in_array($month, $availableMonths)) {
            $availableMonths[] = $month;
        }
    }
}

$selectedMonth = isset($_GET['month']) ? $_GET['month'] : (end($availableMonths) ?: date('Y-m'));
$filteredData = array_filter($sleepData, function ($date) use ($selectedMonth) {
    return strpos($date, $selectedMonth) === 0;
}, ARRAY_FILTER_USE_KEY);

$weeklyData = [0, 0, 0, 0];
$weekCounts = [0, 0, 0, 0];

if ($filteredData) {
    foreach ($filteredData as $date => $data) {
        if (isset($data['night'])) {
            $nightRecords = $data['night'];
            $averageHeartRate = array_sum($nightRecords) / count($nightRecords);

            // Determine sleep quality
            if ($averageHeartRate < 50) {
                $quality = 0; // Very bad sleep
            } elseif ($averageHeartRate < 60) {
                $quality = 100; // Good sleep
            } elseif ($averageHeartRate <= 75) {
                $quality = 70; // Normal sleep
            } else {
                $quality = 30; // Bad sleep
            }

            $weekNumber = ceil((int)date('j', strtotime($date)) / 7) - 1;

            if (!isset($weeklyData[$weekNumber])) {
                $weeklyData[$weekNumber] = 0;
            }
            if (!isset($weekCounts[$weekNumber])) {
                $weekCounts[$weekNumber] = 0;
            }

            $weeklyData[$weekNumber] += $quality;
            $weekCounts[$weekNumber]++;
        }
    }

    foreach ($weeklyData as $index => $total) {
        if ($weekCounts[$index] > 0) {
            $weeklyData[$index] = round($total / $weekCounts[$index], 2);
        }
    }
}

$averageSleepQuality = round(array_sum($weeklyData) / count(array_filter($weeklyData)), 2);

// Determine sleep quality message
if ($averageSleepQuality < 30) {
    $qualityMessage = "Your sleep quality was very poor this month.";
} elseif ($averageSleepQuality < 70) {
    $qualityMessage = "Your sleep quality was normal this month.";
} else {
    $qualityMessage = "Your sleep quality was good this month.";
}

$chartLabels = json_encode(["Week 1", "Week 2", "Week 3", "Week 4"]);
$chartValues = json_encode($weeklyData);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sleep Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #616cbb, #748ac7);
            color: white;
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
            border-radius: 10px;
        }

        .header img {
            max-width: 100px;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            background: white;
            border-radius: 10px;
            padding: 30px;
            color: black;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .info {
            text-align: center;
            margin-bottom: 20px;
        }

        .highlight {
            font-size: 1.5em;
            font-weight: bold;
            color: #4C57A7;
        }

        h1 {
            text-align: center;
            color: #4C57A7;
        }

        form {
            text-align: center;
            margin: 20px 0;
        }

        form select {
            padding: 10px;
            font-size: 16px;
        }

        form button {
            padding: 10px 20px;
            background-color: #4C57A7;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .chart-container {
            margin-top: 20px;
        }

        canvas {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 10px;
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="images/sleep.png" alt="Sleep Med Logo">
        <div class="date-time">
            <?= date('l, F j, Y \a\t g:i A') ?>
        </div>
    </div>

    <div class="container">
        <h1>Welcome to Your Sleep Report</h1>
        <div class="info">
            <p>Your average sleep quality this month was <span class="highlight"><?= $averageSleepQuality ?>%</span>.</p>
            <p class="highlight"><?= $qualityMessage ?></p>
        </div>
        <form method="GET">
            <label for="month">Select a Month:</label>
            <select id="month" name="month">
                <?php foreach ($availableMonths as $month): ?>
                    <option value="<?= $month ?>" <?= $month === $selectedMonth ? 'selected' : '' ?>>
                        <?= date('F Y', strtotime($month)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">View</button>
        </form>
        <div class="chart-container">
            <canvas id="chart"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('chart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= $chartLabels ?>,
                datasets: [{
                    label: 'Sleep Quality (%)',
                    data: <?= $chartValues ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    </script>
</body>
</html>



