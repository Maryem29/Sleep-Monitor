<?php
session_start();
require_once 'firebase.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Get user ID from session
$userId = $_SESSION['user_id'];

// Get today's date in Ymd format
$today = date('Ymd');

// Fetch today's sleep data
$sleepData = get_sleep_data_by_date($userId, $today);

if ($sleepData) {
    // Pass sleep data to JavaScript for rendering pie charts
    echo "<script>
        const sleepData = " . json_encode($sleepData) . ";
        console.log(sleepData); // Log the data for debugging
    </script>";
} else {
    echo "<script>console.log('No sleep data available for today.');</script>";
    $sleepData = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sleep Data Visualization</title>
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <style>
        .chart-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin-top: 20px;
        }
        .chart {
            width: 300px;
            height: 300px;
        }
        .chart text {
            font-size: 12px;
            fill: #fff;
        }
    </style>
</head>
<body>
    <h1>Today's Sleep Data</h1>
    <div class="chart-container">
        <div id="chartHeartbeat" class="chart"></div>
        <div id="chartHours" class="chart"></div>
        <div id="chartMovement" class="chart"></div>
        <div id="chartSleepQuality" class="chart"></div>
    </div>
    <script>
        // Check if sleepData is available
        if (typeof sleepData !== "undefined" && sleepData) {
            // Extract data for each metric
            const totalHeartbeat = sleepData.heartbeat || 0; // Replace with actual key from Firebase
            const totalHours = sleepData.hours_of_sleep || 0; // Replace with actual key from Firebase
            const totalMovement = sleepData.movement || 0; // Replace with actual key from Firebase
            const totalSleepQuality = sleepData.sleep_quality || 0; // Replace with actual key from Firebase

            // Data to render pie charts
            const metrics = [
                { id: 'chartHeartbeat', label: 'Heartbeat', value: totalHeartbeat },
                { id: 'chartHours', label: 'Hours of Sleep', value: totalHours },
                { id: 'chartMovement', label: 'Movement', value: totalMovement },
                { id: 'chartSleepQuality', label: 'Sleep Quality', value: totalSleepQuality }
            ];

            // Function to render a pie chart
            const renderPieChart = (id, label, value) => {
                const width = 300, height = 300, radius = Math.min(width, height) / 2;

                const svg = d3.select(`#${id}`)
                    .append('svg')
                    .attr('width', width)
                    .attr('height', height)
                    .append('g')
                    .attr('transform', `translate(${width / 2}, ${height / 2})`);

                const data = [value, 100 - value];
                const color = d3.scaleOrdinal(['#FF6384', '#36A2EB']);

                const pie = d3.pie();
                const arc = d3.arc()
                    .innerRadius(0)
                    .outerRadius(radius);

                const arcs = svg.selectAll('arc')
                    .data(pie(data))
                    .enter()
                    .append('g');

                arcs.append('path')
                    .attr('d', arc)
                    .attr('fill', (d, i) => color(i));

                // Add labels
                arcs.append('text')
                    .attr('transform', d => `translate(${arc.centroid(d)})`)
                    .attr('text-anchor', 'middle')
                    .text(d => `${d.data}%`);

                // Add chart title
                svg.append('text')
                    .attr('text-anchor', 'middle')
                    .attr('y', -radius - 10)
                    .text(label)
                    .style('font-size', '16px')
                    .style('font-weight', 'bold');
            };

            // Render charts
            metrics.forEach(metric => {
                renderPieChart(metric.id, metric.label, metric.value);
            });
        } else {
            document.body.innerHTML += '<p>No sleep data available for today.</p>';
        }
    </script>
</body>
</html>