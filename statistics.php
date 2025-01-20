<?php
session_start();
require_once 'firebase.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$selectedWeek = isset($_GET['week']) ? $_GET['week'] : date('o-W');
$sleepData = get_sleep_data_by_week($userId, $selectedWeek);

$totalNapTime = 0;
$averageHeartRate = 0;
$nightDataCount = 0;
$chartData = [];

if ($sleepData) {
    foreach ($sleepData as $date => $data) {
        if (isset($data['night'])) {
            $nightRecords = $data['night'];
            $totalNaps = count($nightRecords);
            $totalNapTime += $totalNaps * (20 / 60);

            $heartRateSum = array_sum($nightRecords);
            $averageHeartRate += $heartRateSum / $totalNaps;
            $nightDataCount++;

            $chartData[] = [
                'date' => $date,
                'totalHours' => $totalNaps * (20 / 60)
            ];
        }
    }

    if ($nightDataCount > 0) {
        $averageHeartRate = round($averageHeartRate / $nightDataCount, 2);
    }
}
?>

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

        form {
            text-align: center;
            margin: 20px 0;
        }

        form input {
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

        svg {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 10px;
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
        <h1>Sleep Statistics</h1>
        <div class="info">
            <p>Average Nap Time: <?= round($totalNapTime / 7, 2) ?> hours/day</p>
            <p>Average Heart Rate: <?= $averageHeartRate ?> BPM</p>
        </div>
        <form method="GET">
            <label for="week">Select a Week:</label>
            <input type="week" id="week" name="week" value="<?= $selectedWeek ?>">
            <button type="submit">View</button>
        </form>
        <div class="chart-container">
            <svg id="chart" width="700" height="400"></svg>
        </div>
    </div>

    <script src="https://d3js.org/d3.v7.min.js"></script>
    <script>
    const data = <?= json_encode($chartData) ?>;

    const svg = d3.select("#chart");
    const width = +svg.attr("width");
    const height = +svg.attr("height");
    const margin = { top: 20, right: 30, bottom: 70, left: 50 }; // Adjusted margins for centering

    // Define the x-scale
    const x = d3.scaleBand()
        .domain(data.map(d => d.date))
        .range([margin.left, width - margin.right]) // Space bars evenly within the full width
        .padding(0.4); // Adjusted padding to space bars better

    // Define the y-scale
    const y = d3.scaleLinear()
        .domain([0, 12]) // Adjusted to scale from 1 to 12 hours
        .nice()
        .range([height - margin.bottom, margin.top]);

    // Add bars to the chart
    svg.append("g")
        .selectAll("rect")
        .data(data)
        .join("rect")
        .attr("x", d => x(d.date))
        .attr("y", d => y(d.totalHours))
        .attr("height", d => y(0) - y(d.totalHours))
        .attr("width", x.bandwidth())
        .attr("fill", "#4C57A7");

    // Add x-axis
    svg.append("g")
        .attr("transform", `translate(0,${height - margin.bottom})`)
        .call(d3.axisBottom(x).tickSizeOuter(0))
        .selectAll("text")
        .style("text-anchor", "middle") // Align labels properly
        .attr("transform", "translate(0,10)"); // Shift labels slightly down for better visibility

    // Add y-axis
    svg.append("g")
        .attr("transform", `translate(${margin.left},0)`)
        .call(d3.axisLeft(y).ticks(12));

    // Add x-axis label
    svg.append("text")
        .attr("x", width / 2)
        .attr("y", height - 20)
        .attr("text-anchor", "middle")
        .text("Date");

    // Add y-axis label
    svg.append("text")
        .attr("x", -height / 2)
        .attr("y", 15)
        .attr("transform", "rotate(-90)")
        .attr("text-anchor", "middle")
        .text("Total Nap Time (Hours)");

    // Adjust SVG container to center content
    svg.attr("style", `display: block; margin: 0 auto;`);
    </script>


</body>
</html>



