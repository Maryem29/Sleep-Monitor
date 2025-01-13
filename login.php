<?php
session_start(); // Start the session

// If there's any success message, display it
if (isset($_SESSION['success_message'])) {
    echo '<div class="floating-message success" id="message">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']); // Clear the message after it's displayed
}

include 'firebase.php'; // Include the Firebase functions

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
        // Retrieve all users from Firebase Realtime Database
        $users = get_data_from_firebase('users'); // 'users' is the Firebase path
        
        // Loop through users and check if the username exists
        $user_found = false;
        foreach ($users as $user_id => $user) {
            if ($user['email'] == $username) {
                $user_found = true;
                // Verify the password
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user_id; // Store user ID in session
                    $message = "Login successful.";
                    $message_class = "success";
                    
                    // Redirect to sleep.php after successful login
                    header("Location: sleep.php");
                    exit(); // Make sure to stop the script after the redirect
                } else {
                    $message = "Invalid credentials.";
                    $message_class = "error";
                }
            }
        }

        if (!$user_found) {
            $message = "User not found.";
            $message_class = "error";
        }
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

    <script>
        // Automatically hide the message after 3 seconds
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
