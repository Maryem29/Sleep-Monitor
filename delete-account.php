<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle account deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $password = $_POST['password'];
    $user_id = $_SESSION['user_id'];

    // Replace with actual database connection
    $conn = new mysqli("localhost", "root", "", "your_database_name");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Verify the password
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    if (password_verify($password, $hashed_password)) {
        // Delete the user's account
        $delete_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $delete_stmt->bind_param("i", $user_id);
        $delete_stmt->execute();
        $delete_stmt->close();

        session_destroy();
        header("Location: goodbye.php"); // Redirect to a farewell or confirmation page
        exit();
    } else {
        $error_message = "Incorrect password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #616cbb, #748ac7);
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .header {
            width: 100%;
            max-width: 1200px;
            padding: 10px 20px;
            background-color: #4C57A7;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            color: white;
            border-radius: 10px;
            text-align: center;
        }

        .delete-button {
            padding: 10px 20px;
            background-color: #FF6B6B;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }

        .delete-button:hover {
            background-color: #FF3B3B;
        }

        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .popup-overlay.active {
            display: flex;
        }

        .popup {
            background: #4C57A7;
            border-radius: 10px;
            padding: 20px;
            width: 400px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .popup h2 {
            margin-top: 0;
        }

        .popup p {
            margin: 15px 0;
        }

        .popup input[type="password"] {
            padding: 10px;
            width: calc(100% - 20px);
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .popup .popup-buttons {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .popup .popup-buttons button {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .popup .popup-buttons .cancel {
            background-color: #6C757D;
            color: white;
        }

        .popup .popup-buttons .cancel:hover {
            background-color: #565E64;
        }

        .popup .popup-buttons .confirm {
            background-color: #FF6B6B;
            color: white;
        }

        .popup .popup-buttons .confirm:hover {
            background-color: #FF3B3B;
        }

        .error-message {
            color: #FF6B6B;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Delete Account</h1>
        <p>We're sorry to see you go. If you delete your account, all your data will be permanently removed.</p>
    </div>

    <button class="delete-button" id="delete-account-btn">Delete My Account</button>

    <div class="popup-overlay" id="popup-overlay">
        <div class="popup">
            <h2>Are you sure?</h2>
            <p>To confirm account deletion, please enter your password.</p>
            <?php if (!empty($error_message)): ?>
                <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
            <?php endif; ?>
            <form method="POST" action="">
                <input type="password" name="password" placeholder="Enter your password" required>
                <div class="popup-buttons">
                    <button type="button" class="cancel" id="cancel-btn">Cancel</button>
                    <button type="submit" class="confirm" name="confirm_delete">Delete Account</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const deleteBtn = document.getElementById('delete-account-btn');
        const popupOverlay = document.getElementById('popup-overlay');
        const cancelBtn = document.getElementById('cancel-btn');

        deleteBtn.addEventListener('click', () => {
            popupOverlay.classList.add('active');
        });

        cancelBtn.addEventListener('click', () => {
            popupOverlay.classList.remove('active');
        });
    </script>
</body>
</html>