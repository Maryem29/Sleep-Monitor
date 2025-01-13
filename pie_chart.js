// Generate Random Sleep Data (for healthcare worker who didn't sleep well)
function generateSleepData() {
    const deepSleep = Math.floor(Math.random() * 20) + 10; // 10-30%
    const remSleep = Math.floor(Math.random() * 15) + 5; // 5-20%
    const lightSleep = 100 - (deepSleep + remSleep); // Remainder for 100%
    return [deepSleep, remSleep, lightSleep];
}

// Chart.js for Sleep Data
const sleepData = generateSleepData(); // Getting sleep data
const ctx = document.getElementById('sleepChart').getContext('2d'); // Getting the canvas context

const sleepChart = new Chart(ctx, { // Creating the chart
    type: 'pie', // The chart type (pie chart)
    data: {
        labels: ['Deep Sleep', 'REM Sleep', 'Light Sleep'], // Labels for the chart segments
        datasets: [{
            label: 'Sleep Stages', // Label for the dataset
            data: sleepData, // The data (from generateSleepData function)
            backgroundColor: ['#4C57A7', '#626AB2', '#A3A8D7'] // Colors for the pie chart segments
        }]
    },
    options: {
        responsive: true, // Makes the chart responsive to window size
        plugins: {
            legend: {
                position: 'bottom', // Positions the legend below the chart
            },
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) { // Customizes the tooltip to display percentage
                        return `${tooltipItem.label}: ${tooltipItem.raw}%`;
                    }
                }
            }
        }
    }
});
