<?php
session_start(); // Start the session

if (isset($_SESSION['success_message'])) {
    echo '<div class="floating-message success" id="message">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']); // Clear the message after it's displayed
}

include 'db.php'; // Include the database connection

$message = '';
$message_class = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate input
    if (empty($username) || empty($password)) {
        $message = "Please fill in all fields.";
        $message_class = "error";
    } else {
        // Query the database
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $message = "Login successful.";
                $message_class = "success";
            } else {
                $message = "Invalid credentials.";
                $message_class = "error";
            }
        } else {
            $message = "User not found.";
            $message_class = "error";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <!-- Display floating message if set -->
        <?php if ($message): ?>
            <div class="floating-message <?php echo $message_class; ?>" id="message">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="white-box">
            <img src="images/sleep med.png" alt="Logo" class="logo">
            <div class="blue-box">
                <form action="" method="post">
                    <div class="input-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Enter your username" required>
                    </div>
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="sign-in-btn">Sign In</button>
                    <p class="register-text">
                        Don't have an account? <a href="register.php">Register Now</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
    
	<div class="bookmark-nav">
	    <div class="bookmark" onclick="toggleNav()">
		<img src="images/sleep.png" alt="Logo" class="bookmark-logo">
	    </div>
	    <div class="nav-options" id="nav-options">
		<ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="register.php">Register</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="report.php">Report</a></li>
                <li><a href="statistics.php">Statistics</a></li>
		</ul>
	    </div>
	</div>
	
	<script>
	    function toggleNav() {
		const navOptions = document.getElementById('nav-options');

		if (navOptions.classList.contains('active')) {
		    // Slide up
		    navOptions.classList.remove('active');
		    navOptions.classList.add('inactive');

		    // Wait for the animation to finish, then hide the element
		    setTimeout(() => {
		        navOptions.style.display = 'none';
		    }, 500); // Match the transition duration
		} else {
		    // Slide down
		    navOptions.style.display = 'flex'; // Ensure it's visible
		    navOptions.classList.remove('inactive');
		    navOptions.classList.add('active');
		}
	    }

	    // Attach event listener to the logo
	    document.getElementById('bookmark-logo').addEventListener('click', toggleNav);
	</script>
	
	<script>
        //Automatically hide the message after 3 seconds
        window.onload = function() {
            var message = document.getElementById('message');
            if (message) {
                message.style.visibility = 'visible';
                message.style.opacity = 1;
                setTimeout(function() {
                    message.style.opacity = 0;
                    message.style.visibility = 'hidden';
                }, 3000); // Hide after 3 seconds
            }
        };
    </script>
</body>
</html>
