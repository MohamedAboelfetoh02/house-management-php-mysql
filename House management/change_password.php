<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Initialize feedback variables
$success = $error = "";

// Handle password change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $username = $_SESSION['username'];
    $current_password = md5($conn->real_escape_string($_POST['current_password']));
    $new_password = md5($conn->real_escape_string($_POST['new_password']));
    $confirm_password = md5($conn->real_escape_string($_POST['confirm_password']));

    // Verify current password
    $query = "SELECT * FROM APPUSERS WHERE username='$username' AND password='$current_password'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        // Check if new passwords match
        if ($new_password === $confirm_password) {
            // Update password
            $update_query = "UPDATE APPUSERS SET password='$new_password' WHERE username='$username'";
            if ($conn->query($update_query)) {
                $success = "Password updated successfully.";
            } else {
                $error = "Failed to update password. Please try again.";
            }
        } else {
            $error = "New passwords do not match.";
        }
    } else {
        $error = "Current password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .change-password-box {
            width: 90%;
            max-width: 400px;
            background: white;
            color: #333;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #6a11cb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #2575fc;
        }
        .success {
            color: green;
            font-size: 14px;
        }
        .error {
            color: red;
            font-size: 14px;
        }
        @media (max-width: 480px) {
            .change-password-box {
                padding: 15px;
            }
            input[type="password"] {
                font-size: 12px;
            }
            button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="change-password-box">
        <h1>Change Password</h1>
        <form method="POST">
            <div>
                <input type="password" name="current_password" placeholder="Current Password" required>
            </div>
            <div>
                <input type="password" name="new_password" placeholder="New Password" required>
            </div>
            <div>
                <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
            </div>
            <button type="submit" name="change_password">Change Password</button>
        </form>
        <?php if ($success): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php elseif ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
