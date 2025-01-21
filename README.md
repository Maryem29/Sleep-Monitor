<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h1>Sleep Monitor Web Application</h1>
        <h2>Purpose</h2>
            <p>The Sleep Monitor Web Application is designed to help healthcare providers, particularly those working night shifts, monitor and improve their sleep quality. By analyzing sleep patterns, offering insights, and tracking statistics, this tool aims to promote better sleep hygiene and overall health for users facing irregular schedules.</p>
        <h2>Features</h2>
        <ul>
            <li>User registration and profile management.</li>
            <li>Sleep data input and visualization through graphical statistics.</li>
            <li>Firebase integration for real-time data synchronization.</li>
            <li>Informative sections like "About Us" and "App Information" for user guidance.</li>
        </ul>
    <h2>Technologies Used</h2>
        <div class="technologies">
            <ul>
                <li><strong>Frontend:</strong> HTML5, CSS3, JavaScript</li>
                <li><strong>Backend:</strong> PHP</li>
                <li><strong>Database:</strong> Firebase</li>
                <li><strong>Other Tools:</strong> Chart.js for visualizations</li>
            </ul>
        </div>
    <h2>File Structure</h2>
        <ul>
            <li><code>report.php</code>: Generates user sleep reports based on logged data.</li>
            <li><code>firebase.php</code>: Handles Firebase integration for data storage and retrieval.</li>
            <li><code>profile.php</code>: Allows users to view and update their profiles.</li>
            <li><code>register.php</code>: User registration functionality.</li>
            <li><code>app-information.php</code>: Details the app's purpose and functionality.</li>
            <li><code>about-us.html</code>: Contains information about the developers and mission.</li>
            <li><code>statistics.php</code>: Displays sleep statistics and analytics.</li>
            <li><code>sleep.png</code>: Visual asset used within the application.</li>
            <li><code>Sleep Quality Monitoring for Healthcare Providers on Night Shifts.pdf</code>: A reference document providing context and background information for the project.</li>
        </ul>
    <h2>Goals</h2>
        <p>Our primary objectives include:</p>
        <ul>
            <li>Providing a user-friendly platform for tracking and analyzing sleep quality.</li>
            <li>Leveraging modern technologies to ensure real-time and accurate data management.</li>
            <li>Educating healthcare workers on the importance of sleep hygiene.</li>
        </ul>
    <h2>How to Run the Project</h2>
        <ol>
            <li>Ensure you have a PHP server environment (e.g., XAMPP, WAMP) set up.</li>
            <li>Clone or download the project files to your server's root directory.</li>
            <li>Set up Firebase credentials in <code>firebase.php</code> for database connectivity.</li>
            <li>Access the application through your browser (e.g., <code>http://localhost/sleep-monitor</code>).</li>
        </ol>
    <h2>Credits</h2>
        <p>This project was created with the collective efforts of the development team to address a critical issue faced by healthcare professionals. The attached PDF, "Sleep Quality Monitoring for Healthcare Providers on Night Shifts," serves as the foundation for our research and objectives.</p>
</body>
</html>